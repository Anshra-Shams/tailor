<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Customer;
use App\Models\Measurement;
use App\Models\Member;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['customer.members', 'member', 'service', 'cuttingEmployee', 'stitchingEmployee']);

        $q = trim($request->input('q', ''));
        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                if (ctype_digit($q)) {
                    $w->where('orders.id', (int) $q);
                }
                $w->orWhereHas('customer', function ($c) use ($q) {
                    $c->where('name', 'like', "%{$q}%")
                      ->orWhere('phone', 'like', "%{$q}%");
                });
                $w->orWhereHas('member', function ($m) use ($q) {
                    $m->where('name', 'like', "%{$q}%");
                });
                $w->orWhereHas('service', function ($s) use ($q) {
                    $s->where('name', 'like', "%{$q}%");
                });
                $w->orWhere('orders.status', 'like', "%{$q}%");
            });
        }

        $allOrders = $query->latest('id')->get();

        $groupedBatches = collect();
        $processedIds = [];

        foreach ($allOrders as $o) {
            if (in_array($o->id, $processedIds)) {
                continue;
            }

            $createdTime = $o->created_at;
            $siblings = Order::with(['customer.members', 'member', 'service', 'cuttingEmployee', 'stitchingEmployee'])
                ->where('customer_id', $o->customer_id)
                ->where('order_date', $o->order_date)
                ->whereBetween('created_at', [
                    $createdTime->copy()->subSeconds(30),
                    $createdTime->copy()->addSeconds(30)
                ])
                ->orderBy('id', 'asc')
                ->get();

            if ($siblings->isEmpty()) {
                $siblings = collect([$o]);
            }

            foreach ($siblings as $s) {
                $processedIds[] = $s->id;
            }

            $primaryOrder = $siblings->first();
            $primaryOrder->setAttribute('batch_items', $siblings);
            $groupedBatches->push($primaryOrder);
        }

        $page = (int) $request->input('page', 1);
        $perPage = 15;
        $paginatedItems = $groupedBatches->slice(($page - 1) * $perPage, $perPage)->values();

        $orders = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedItems,
            $groupedBatches->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('orders.index', compact('orders', 'q'));
    }

    public function create(Request $request)
    {
        $editOrder = null;
        if ($request->filled('edit')) {
            $editOrder = Order::with(['customer.members', 'member', 'service'])
                ->find($request->edit);
            if ($editOrder) {
                $createdTime = $editOrder->created_at;
                $siblingOrders = Order::with(['service', 'member'])
                    ->where('customer_id', $editOrder->customer_id)
                    ->where('order_date', $editOrder->order_date)
                    ->whereBetween('created_at', [
                        $createdTime->copy()->subSeconds(30),
                        $createdTime->copy()->addSeconds(30)
                    ])
                    ->orderBy('id', 'asc')
                    ->get();
                $editOrder->setAttribute('sibling_orders', $siblingOrders);
            }
        }

        $accounts = Account::with('category')
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(fn ($a) => [
                'id'       => $a->id,
                'name'     => $a->name,
                'category' => $a->category?->name ?? 'Other',
            ]);

        return view('orders.create', compact('editOrder', 'accounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id'         => 'required|exists:customers,id',
            'member_id'           => 'nullable|exists:members,id',
            'due_date'            => 'required|date',
            'notes'               => 'nullable|string|max:1000',
            'paid_amount'         => 'nullable|numeric|min:0',
            'account_id'          => ['nullable', Rule::requiredIf(fn () => (float) $request->input('paid_amount', 0) > 0), 'exists:accounts,id'],
            'service_ids'         => 'required|array|min:1',
            'service_ids.*'       => 'required|exists:services,id',
            'service_notes'       => 'nullable|array',
            'service_notes.*'     => 'nullable|string',
            'tiers'               => 'nullable|array',
            'prices'              => 'required|array',
            'prices.*'            => 'required|numeric|min:0',
            'quantities'          => 'required|array',
            'quantities.*'        => 'required|integer|min:1',
            'measurements_json'   => 'required|array',
            'measurements_json.*' => 'nullable|string',
        ], [
            'account_id.required' => 'Please select an account for the advance payment.',
        ]);

        $count = count($validated['service_ids']);
        if (count($validated['prices']) !== $count || count($validated['quantities']) !== $count || count($validated['measurements_json']) !== $count) {
            return back()->withInput()->withErrors(['general' => 'Service data mismatch. Please try again.']);
        }

            $firstOrder = null;
            foreach ($validated['service_ids'] as $i => $serviceId) {
                $decoded = json_decode($validated['measurements_json'][$i] ?? '{}', true);
                if (!is_array($decoded)) {
                    $decoded = [];
                }

                // If no inline measurements, pull the latest saved measurement for this customer/member/service
                if (empty($decoded)) {
                    $latest = \App\Models\Measurement::where('customer_id', $validated['customer_id'])
                        ->where(function ($q) use ($serviceId) {
                            $q->where('service_id', $serviceId)->orWhereNull('service_id');
                        })
                        ->when(!empty($validated['member_id']), fn ($q) => $q->where('member_id', $validated['member_id']), fn ($q) => $q->whereNull('member_id'))
                        ->latest()
                        ->first();
                    if ($latest) {
                        $decoded = $latest->data ?? [];
                    }
                }

                $advanceAmount = $i === 0 ? (float) ($validated['paid_amount'] ?? 0) : 0;

                $serviceNote = !empty($request->input('service_notes')[$i]) ? trim($request->input('service_notes')[$i]) : null;
                $noteParts = array_filter([$serviceNote, $validated['notes'] ?? null]);
                $orderNotes = !empty($noteParts) ? implode(' — ', $noteParts) : null;

                $order = Order::create([
                    'customer_id'  => $validated['customer_id'],
                    'member_id'    => !empty($validated['member_id']) ? $validated['member_id'] : null,
                    'service_id'   => $serviceId,
                    'price'        => $validated['prices'][$i],
                    'quantity'     => $validated['quantities'][$i] ?? 1,
                    'paid_amount'  => $advanceAmount,
                    'status'       => 'pending',
                    'order_date'   => now()->toDateString(),
                    'due_date'     => $validated['due_date'],
                    'measurements' => json_encode($decoded),
                    'notes'        => $orderNotes,
                ]);
                $order->refreshPaymentStatus();
                $order->save();

                if (!$firstOrder) {
                    $firstOrder = $order;
                }

                // If an advance was paid, record it and credit the selected account
                if ($advanceAmount > 0) {
                    $account = Account::find($validated['account_id']);

                    Payment::create([
                        'order_id'   => $order->id,
                        'account_id' => $account?->id,
                        'amount'     => $advanceAmount,
                        'method'     => $account ? $account->paymentMethod() : 'other',
                        'notes'      => 'Advance payment at order creation',
                    ]);

                    if ($account) {
                        $account->increment('current_balance', $advanceAmount);
                    }
                }
            }

            return redirect()->route('orders.invoice', $firstOrder ? $firstOrder->id : $order->id)->with('success', "Order created successfully!");
        }

        public function invoice(Order $order)
        {
            $order->load(['customer.members', 'member', 'service', 'payments']);

            // Fetch sibling orders created in the same batch/timeframe for this customer
            $createdTime = $order->created_at;
            $siblingOrders = Order::with(['service', 'member'])
                ->where('customer_id', $order->customer_id)
                ->where('order_date', $order->order_date)
                ->where('due_date', $order->due_date)
                ->whereBetween('created_at', [
                    $createdTime->copy()->subSeconds(30),
                    $createdTime->copy()->addSeconds(30)
                ])
                ->orderBy('id', 'asc')
                ->get();

            if ($siblingOrders->isEmpty()) {
                $siblingOrders = collect([$order]);
            }

            // Helper to map a raw measurements dictionary to Upper (1-14) and Lower (15-26) data arrays
            $buildMeasurements = function (array $measurementsArray) {
                $getVal = function ($keys) use ($measurementsArray) {
                    foreach ((array) $keys as $k) {
                        if (isset($measurementsArray[$k]) && trim((string)$measurementsArray[$k]) !== '') {
                            $val = trim((string)$measurementsArray[$k]);
                            if (str_contains($val, ',')) {
                                $parts = array_values(array_filter(array_map('trim', explode(',', $val)), fn($p) => $p !== ''));
                                return $parts[0] ?? '';
                            }
                            return $val;
                        }
                    }

                    // Check custom fields or dynamic keys matching pattern
                    foreach ($measurementsArray as $mKey => $mVal) {
                        if (str_starts_with($mKey, '__')) continue;
                        if (trim((string)$mVal) === '') continue;

                        foreach ((array)$keys as $k) {
                            if (strlen($k) >= 4 && str_contains(strtolower($mKey), strtolower($k))) {
                                $val = trim((string)$mVal);
                                if (str_contains($val, ',')) {
                                    $parts = array_values(array_filter(array_map('trim', explode(',', $val)), fn($p) => $p !== ''));
                                    return $parts[0] ?? '';
                                }
                                return $val;
                            }
                        }
                    }

                    return '';
                };

                $upperData = [
                    1  => $getVal(['point', 'Point']),
                    2  => $getVal(['gending', 'Gending']),
                    3  => $getVal(['kameez_length', 'length_shoulder_to_bottom', 'length', 'Length']),
                    4  => $getVal(['shoulder', 'Shoulder']),
                    5  => $getVal(['chest', 'Chest']),
                    6  => $getVal(['waist_upper', 'waist', 'Waist']),
                    7  => $getVal(['hip_upper', 'hip', 'Hip']),
                    8  => $getVal(['in_said', 'insaid', 'daman', 'Daman / Ghera', 'ghera']),
                    9  => $getVal(['flair', 'Flair']),
                    10 => $getVal(['choke', 'Choke', 'cross_back', 'Cross Back']),
                    11 => (function() use ($getVal) {
                        $f = $getVal(['sleeves', 'sleeves_full', 'Sleeves']);
                        $h = $getVal(['sleeves_half', 'Sleeves Half']);
                        $q = $getVal(['sleeves_qtr', 'Sleeves Qtr']);
                        $all = array_filter([$f, $h, $q]);
                        return !empty($all) ? implode(' / ', $all) : $getVal(['sleeves', 'sleeves_full', 'Sleeves', 'sleeves_half', 'Sleeves Half']);
                    })(),
                    12 => $getVal(['bicep', 'upper_arm', 'Upper Arm']),
                    13 => $getVal(['wrist', 'Wrist', 'cuff']),
                    14 => $getVal(['collar', 'neck', 'Neck']),
                ];

                $lowerData = [
                    15 => $getVal(['shalwar_length', 'trouser_length', 'length_lower', 'lower_length']),
                    16 => $getVal(['wrist_lower', 'waist_lower', 'wrist_bottom']),
                    17 => $getVal(['half_belt_elastic', 'belt_elastic', 'half_belt']),
                    18 => $getVal(['full_elastic', 'elastic', 'elastic_extra']),
                    19 => $getVal(['hip_lower', 'hip', 'Hip']),
                    20 => $getVal(['belt', 'Belt', 'back', 'Back']),
                    21 => $getVal(['asan', 'fly', 'Fly']),
                    22 => $getVal(['inseam', 'inside', 'Inside']),
                    23 => $getVal(['thigh', 'thai', 'Thai']),
                    24 => $getVal(['knee', 'Knee']),
                    25 => $getVal(['paincha', 'bottom', 'Bottom']),
                    26 => $getVal(['ankle', 'ankle_circumference', 'Ankle']),
                ];

                return [
                    'upper' => $upperData,
                    'lower' => $lowerData,
                ];
            };

            // Build individual measurements for each service/order
            $servicesMeasurements = [];
            foreach ($siblingOrders as $sOrder) {
                $raw = $sOrder->measurements;
                $decoded = is_string($raw) ? json_decode($raw, true) : (is_array($raw) ? $raw : []);
                $parsed = [];
                if (is_array($decoded)) {
                    foreach ($decoded as $k => $v) {
                        if ($v !== null && $v !== '') {
                            $parsed[$k] = is_array($v) ? reset($v) : (string) $v;
                        }
                    }
                }
                $mData = $buildMeasurements($parsed);
                $servicesMeasurements[] = [
                    'order'        => $sOrder,
                    'service_name' => $sOrder->service?->name ?? 'Service #' . $sOrder->id,
                    'upper'        => $mData['upper'],
                    'lower'        => $mData['lower'],
                ];
            }

            // Fallback upperData and lowerData
            $upperData = $servicesMeasurements[0]['upper'] ?? [];
            $lowerData = $servicesMeasurements[0]['lower'] ?? [];

            $grandTotal = $siblingOrders->sum(fn ($o) => (float) $o->price * (int) $o->quantity);
            $totalPaid = $siblingOrders->sum(fn ($o) => (float) $o->paid_amount);

            return view('orders.invoice', compact('order', 'siblingOrders', 'servicesMeasurements', 'upperData', 'lowerData', 'grandTotal', 'totalPaid'));
        }

        public function edit(Order $order)
    {
        $order->load(['customer.members', 'member', 'service']);

        // All active services; the measured subset is resolved dynamically per member via API
        $allServices = Service::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'price']);

        return view('orders.edit', compact('order', 'allServices'));
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'member_id'           => 'nullable|exists:members,id',
            'due_date'            => 'required|date',
            'notes'               => 'nullable|string|max:1000',
            'paid_amount'         => 'nullable|numeric|min:0',
            'service_ids'         => 'required|array|min:1',
            'service_ids.*'       => 'required|exists:services,id',
            'service_notes'       => 'nullable|array',
            'service_notes.*'     => 'nullable|string',
            'prices'              => 'required|array',
            'prices.*'            => 'required|numeric|min:0',
            'quantities'          => 'required|array',
            'quantities.*'        => 'required|integer|min:1',
            'measurements_json'   => 'required|array',
            'measurements_json.*' => 'nullable|string',
        ]);

        $createdTime = $order->created_at;
        $siblingOrders = Order::where('customer_id', $order->customer_id)
            ->where('order_date', $order->order_date)
            ->whereBetween('created_at', [
                $createdTime->copy()->subSeconds(30),
                $createdTime->copy()->addSeconds(30)
            ])
            ->orderBy('id', 'asc')
            ->get();

        if ($siblingOrders->isEmpty()) {
            $siblingOrders = collect([$order]);
        }

        $serviceCount = count($validated['service_ids']);

        for ($i = 0; $i < $serviceCount; $i++) {
            $serviceId = $validated['service_ids'][$i];
            $price = $validated['prices'][$i];
            $qty = $validated['quantities'][$i] ?? 1;
            $serviceNote = !empty($request->input('service_notes')[$i]) ? trim($request->input('service_notes')[$i]) : null;
            $noteParts = array_filter([$serviceNote, $validated['notes'] ?? null]);
            $orderNotes = !empty($noteParts) ? implode(' — ', $noteParts) : null;
            $advanceAmount = $i === 0 ? (float) ($validated['paid_amount'] ?? 0) : 0;

            $decoded = json_decode($validated['measurements_json'][$i] ?? '{}', true);
            if (!is_array($decoded)) {
                $decoded = [];
            }
            if (empty($decoded)) {
                $latest = \App\Models\Measurement::where('customer_id', $order->customer_id)
                    ->where(function ($q) use ($serviceId) {
                        $q->where('service_id', $serviceId)->orWhereNull('service_id');
                    })
                    ->when($validated['member_id'], fn ($q, $mid) => $q->where('member_id', $mid), fn ($q) => $q->whereNull('member_id'))
                    ->latest()
                    ->first();
                if ($latest) {
                    $decoded = $latest->data ?? [];
                }
            }

            if (isset($siblingOrders[$i])) {
                $sOrd = $siblingOrders[$i];
                $sOrd->update([
                    'member_id'    => $validated['member_id'] ?: null,
                    'service_id'   => $serviceId,
                    'price'        => $price,
                    'quantity'     => $qty,
                    'paid_amount'  => $advanceAmount,
                    'due_date'     => $validated['due_date'],
                    'measurements' => json_encode($decoded),
                    'notes'        => $orderNotes,
                ]);
                $sOrd->refreshPaymentStatus();
                $sOrd->save();
            } else {
                $newOrd = Order::create([
                    'customer_id'  => $order->customer_id,
                    'member_id'    => $validated['member_id'] ?: null,
                    'service_id'   => $serviceId,
                    'price'        => $price,
                    'quantity'     => $qty,
                    'paid_amount'  => 0,
                    'status'       => $order->status ?? 'pending',
                    'order_date'   => $order->order_date,
                    'due_date'     => $validated['due_date'],
                    'measurements' => json_encode($decoded),
                    'notes'        => $orderNotes,
                    'created_at'   => $order->created_at,
                ]);
                $newOrd->created_at = $order->created_at;
                $newOrd->save();
                $newOrd->refreshPaymentStatus();
                $newOrd->save();
            }
        }

        if ($siblingOrders->count() > $serviceCount) {
            for ($i = $serviceCount; $i < $siblingOrders->count(); $i++) {
                $siblingOrders[$i]->delete();
            }
        }

        return redirect()->route('orders.index')->with('success', "Order #" . str_pad($order->id, 4, '0', STR_PAD_LEFT) . " updated successfully!");
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,cutting,stitching,in_progress,completed,delivered,cancelled',
        ]);

        $order->status = $validated['status'];
        $order->save();

        return response()->json(['success' => true, 'status' => $validated['status']]);
    }

    public function destroy(Order $order)
    {
        $createdTime = $order->created_at;
        Order::where('customer_id', $order->customer_id)
            ->where('order_date', $order->order_date)
            ->where('due_date', $order->due_date)
            ->whereBetween('created_at', [
                $createdTime->copy()->subSeconds(30),
                $createdTime->copy()->addSeconds(30)
            ])
            ->delete();

        return redirect()->route('orders.index')->with('success', 'Order batch deleted successfully!');
    }

    // ── API: Customer ledger ──

    public function apiCustomerLedger(Customer $customer)
    {
        $total = (float) $customer->orders()->selectRaw('SUM(price * quantity) AS t')->value('t');

        return response()->json([
            'total_orders' => $customer->orders()->count(),
            'total_amount' => $total,
            'paid_amount'  => (float) $customer->orders()->sum('paid_amount'),
            'due_amount'   => max(0, $total - (float) $customer->orders()->sum('paid_amount')),
        ]);
    }

    // ── API: Services that have saved measurements for a member ──

    public function apiMemberServices(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'member_id'   => 'nullable|exists:members,id',
        ]);

        $services = Service::where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(fn ($s) => [
                'id'                 => $s->id,
                'name'               => $s->name,
                'price'              => (float) $s->price,
                'pricing_tiers'      => [
                    'basic'    => (isset($s->pricing_tiers['basic']) && $s->pricing_tiers['basic'] !== '') ? (float)$s->pricing_tiers['basic'] : round((float)$s->price * 0.75),
                    'standard' => (isset($s->pricing_tiers['standard']) && $s->pricing_tiers['standard'] !== '') ? (float)$s->pricing_tiers['standard'] : (float)$s->price,
                    'premium'  => (isset($s->pricing_tiers['premium']) && $s->pricing_tiers['premium'] !== '') ? (float)$s->pricing_tiers['premium'] : round((float)$s->price * 1.6),
                ],
                'days'               => $s->estimated_days,
                'measurement_fields' => $s->measurement_fields ?? [],
            ]);

        return response()->json($services);
    }

    // ── API: Search customers by name/phone ──

    public function apiSearchCustomers(Request $request)
    {
        $q = trim($request->input('q', ''));

        if ($q !== '' && strlen($q) < 2) {
            return response()->json([]);
        }

        $query = Customer::with('members');

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                  ->orWhere('phone', 'like', "%{$q}%");
            });
        }

        $customers = $query->latest()
            ->limit(20)
            ->get()
            ->map(fn($c) => [
                'id'      => $c->id,
                'name'    => $c->name,
                'phone'   => $c->phone,
                'gender'  => $c->gender,
                'address' => $c->address,
                'members' => $c->members->map(fn($m) => [
                    'id'       => $m->id,
                    'name'     => $m->name,
                    'gender'   => $m->gender,
                    'relation' => $m->relation,
                ]),
            ]);

        return response()->json($customers);
    }

    // ── API: Fetch a single customer (with members) by id ──

    public function apiCustomer(Customer $customer)
    {
        $c = $customer->load('members');

        return response()->json([
            'id'      => $c->id,
            'name'    => $c->name,
            'phone'   => $c->phone,
            'gender'  => $c->gender,
            'address' => $c->address,
            'members' => $c->members->map(fn($m) => [
                'id'       => $m->id,
                'name'     => $m->name,
                'gender'   => $m->gender,
                'relation' => $m->relation,
            ]),
        ]);
    }

    // ── API: Unified search for customers and members (Create Order) ──

    public function apiSearchAll(Request $request)
    {
        $q = trim((string) $request->input('q', ''));

        if ($q === '') {
            $customers = Customer::with('members')->orderBy('name')->limit(25)->get();

            return response()->json([
                'customers' => $customers->map(fn ($c) => $this->customerPayload($c))->values()->all(),
                'members'   => [],
            ]);
        }

        $customers = Customer::with('members')
            ->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")->orWhere('phone', 'like', "%{$q}%");
            })
            ->orderBy('name')
            ->limit(8)
            ->get();

        $customerIds = $customers->pluck('id');

        $members = Member::with('customer.members')
            ->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")->orWhere('relation', 'like', "%{$q}%");
            })
            ->whereNotIn('customer_id', $customerIds)
            ->orderBy('name')
            ->limit(8)
            ->get()
            ->map(fn ($m) => [
                'type'     => 'member',
                'id'       => $m->id,
                'name'     => $m->name,
                'relation' => $m->relation,
                'gender'   => $m->gender,
                'customer' => $m->customer ? $this->customerPayload($m->customer) : null,
            ]);

        return response()->json([
            'customers' => $customers->map(fn ($c) => $this->customerPayload($c))->values()->all(),
            'members'   => $members->values()->all(),
        ]);
    }

    private function customerPayload(Customer $c): array
    {
        return [
            'type'    => 'customer',
            'id'      => $c->id,
            'name'    => $c->name,
            'phone'   => $c->phone,
            'gender'  => $c->gender,
            'address' => $c->address,
            'members' => $c->members->map(fn ($m) => [
                'id'       => $m->id,
                'name'     => $m->name,
                'gender'   => $m->gender,
                'relation' => $m->relation,
            ])->all(),
        ];
    }

    // ── API: Quick create customer ──

    public function apiQuickCustomer(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'required|string|max:20',
            'gender'  => 'nullable|in:male,female,other',
            'address' => 'nullable|string|max:500',
        ]);

        $customer = Customer::create($validated);

        return response()->json([
            'id'      => $customer->id,
            'name'    => $customer->name,
            'phone'   => $customer->phone,
            'gender'  => $customer->gender,
            'address' => $customer->address,
            'members' => [],
        ]);
    }

    // ── API: Quick add member ──

    public function apiQuickMember(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'name'        => 'required|string|max:255',
            'gender'      => 'nullable|in:male,female,other',
            'relation'    => 'nullable|string|max:100',
        ]);

        $member = Member::create($validated);

        return response()->json([
            'id'       => $member->id,
            'name'     => $member->name,
            'gender'   => $member->gender,
            'relation' => $member->relation,
        ]);
    }

    // ── API: Services ──

    public function apiServices()
    {
        return Service::where('is_active', true)->get()->map(fn($s) => [
            'id'                 => $s->id,
            'name'               => $s->name,
            'price'              => (float) $s->price,
            'pricing_tiers'      => [
                'basic'    => (isset($s->pricing_tiers['basic']) && $s->pricing_tiers['basic'] !== '') ? (float)$s->pricing_tiers['basic'] : round((float)$s->price * 0.75),
                'standard' => (isset($s->pricing_tiers['standard']) && $s->pricing_tiers['standard'] !== '') ? (float)$s->pricing_tiers['standard'] : (float)$s->price,
                'premium'  => (isset($s->pricing_tiers['premium']) && $s->pricing_tiers['premium'] !== '') ? (float)$s->pricing_tiers['premium'] : round((float)$s->price * 1.6),
            ],
            'days'               => $s->estimated_days,
            'measurement_fields' => $s->measurement_fields ?? [],
        ]);
    }

    // ── API: Previous measurements ──

    public function apiPreviousMeasurements(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'service_id'  => 'required|exists:services,id',
            'member_id'   => 'nullable|exists:members,id',
        ]);

        $measurement = Measurement::where('customer_id', $request->customer_id)
            ->where(function ($q) use ($request) {
                $q->where('service_id', $request->service_id)->orWhereNull('service_id');
            })
            ->when(
                $request->member_id,
                fn ($q, $mid) => $q->where('member_id', $mid),
                fn ($q) => $q->whereNull('member_id')
            )
            ->orderByRaw('CASE WHEN service_id = ? THEN 0 ELSE 1 END', [$request->service_id])
            ->latest('id')
            ->first();

        $parseVals = function ($val) {
            if (is_array($val)) return array_map('trim', array_filter($val));
            if (is_string($val)) return array_map('trim', array_filter(explode(',', $val)));
            return [];
        };

        $allHistory = [];

        $allMeasurements = Measurement::where('customer_id', $request->customer_id)
            ->when(
                $request->member_id,
                fn ($q, $mid) => $q->where(fn ($w) => $w->where('member_id', $mid)->orWhereNull('member_id')),
                fn ($q) => $q->whereNull('member_id')
            )
            ->latest('id')
            ->get();

        foreach ($allMeasurements as $m) {
            if (is_array($m->data)) {
                foreach ($m->data as $k => $v) {
                    if ($k && !str_starts_with($k, '__')) {
                        foreach ($parseVals($v) as $singleV) {
                            $allHistory[$k][] = $singleV;
                        }
                    }
                }
            }
        }

        $allOrders = Order::where('customer_id', $request->customer_id)
            ->when(
                $request->member_id,
                fn ($q, $mid) => $q->where(fn ($w) => $w->where('member_id', $mid)->orWhereNull('member_id')),
                fn ($q) => $q->whereNull('member_id')
            )
            ->whereNotNull('measurements')
            ->latest('id')
            ->get();

        foreach ($allOrders as $o) {
            $data = is_array($o->measurements) ? $o->measurements : (is_string($o->measurements) ? json_decode($o->measurements, true) : null);
            if (is_array($data)) {
                foreach ($data as $k => $v) {
                    if ($k && !str_starts_with($k, '__')) {
                        foreach ($parseVals($v) as $singleV) {
                            $allHistory[$k][] = $singleV;
                        }
                    }
                }
            }
        }

        foreach ($allHistory as $k => $vals) {
            $allHistory[$k] = array_values(array_unique($vals));
        }

        return response()->json([
            'measurements'   => $measurement ? $measurement->data : null,
            'saved_date'     => $measurement ? $measurement->created_at?->format('M d, Y') : null,
            'measurement_id' => $measurement ? $measurement->id : null,
            'all_history'    => $allHistory,
        ]);
    }
}
