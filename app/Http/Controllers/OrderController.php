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
        $query = Order::with(['customer', 'member', 'service']);

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

        $orders = $query->latest()->paginate(15)->withQueryString();

        return view('orders.index', compact('orders', 'q'));
    }

    public function create(Request $request)
    {
        $editOrder = null;
        if ($request->filled('edit')) {
            $editOrder = Order::with(['customer.members', 'member', 'service'])
                ->findOrFail($request->edit);
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
            $tierLabel = !empty($request->input('tiers')[$i]) ? ucfirst($request->input('tiers')[$i]) . ' Stitching' : null;
            $noteParts = array_filter([$serviceNote, $tierLabel, $validated['notes'] ?? null]);
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

        return redirect()->route('orders.index')->with('success', "{$count} order(s) created successfully!");
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

        $decoded = json_decode($validated['measurements_json'][0] ?? '{}', true);
        if (!is_array($decoded)) {
            $decoded = [];
        }

        if (empty($decoded)) {
            $latest = \App\Models\Measurement::where('customer_id', $order->customer_id)
                ->where('service_id', $validated['service_ids'][0])
                ->when($validated['member_id'], fn ($q) => $q->where('member_id', $validated['member_id']), fn ($q) => $q->whereNull('member_id'))
                ->latest()
                ->first();
            if ($latest) {
                $decoded = $latest->data ?? [];
            }
        }

        $serviceNote = !empty($request->input('service_notes')[0]) ? trim($request->input('service_notes')[0]) : null;
        $noteParts = array_filter([$serviceNote, $validated['notes'] ?? null]);
        $orderNotes = !empty($noteParts) ? implode(' — ', $noteParts) : null;

        $order->update([
            'member_id'    => $validated['member_id'] ?: null,
            'service_id'   => $validated['service_ids'][0],
            'price'        => $validated['prices'][0],
            'quantity'     => $validated['quantities'][0] ?? 1,
            'paid_amount'  => $validated['paid_amount'] ?? 0,
            'due_date'     => $validated['due_date'],
            'measurements' => json_encode($decoded),
            'notes'        => $orderNotes,
        ]);
        $order->refreshPaymentStatus();
        $order->save();

        return redirect()->route('orders.index')->with('success', "Order #" . str_pad($order->id, 4, '0', STR_PAD_LEFT) . " updated successfully!");
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed,delivered,cancelled',
        ]);

        $order->update(['status' => $validated['status']]);

        return response()->json(['success' => true, 'status' => $order->status]);
    }

    public function destroy(Order $order)
    {
        $order->delete();

        return redirect()->route('orders.index')->with('success', 'Order deleted successfully!');
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
                'pricing_tiers'      => $s->pricing_tiers ?? [
                    'basic' => (float) $s->price,
                    'standard' => (float) $s->price,
                    'premium' => (float) $s->price,
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
            'id'                => $s->id,
            'name'              => $s->name,
            'price'             => (float) $s->price,
            'days'              => $s->estimated_days,
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

        if (!$measurement || !$measurement->data) {
            return response()->json(['measurements' => null]);
        }

        return response()->json([
            'measurements'  => $measurement->data,
            'saved_date'    => $measurement->created_at?->format('M d, Y'),
            'measurement_id' => $measurement->id,
        ]);
    }
}
