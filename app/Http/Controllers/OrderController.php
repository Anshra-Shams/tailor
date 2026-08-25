<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Measurement;
use App\Models\Member;
use App\Models\Order;
use App\Models\Service;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['customer', 'member', 'service'])
            ->latest()
            ->paginate(15);

        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        return view('orders.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id'         => 'required|exists:customers,id',
            'member_id'           => 'nullable|exists:members,id',
            'due_date'            => 'required|date',
            'notes'               => 'nullable|string|max:1000',
            'paid_amount'         => 'nullable|numeric|min:0',
            'service_ids'         => 'required|array|min:1',
            'service_ids.*'       => 'required|exists:services,id',
            'prices'              => 'required|array',
            'prices.*'            => 'required|numeric|min:0',
            'quantities'          => 'required|array',
            'quantities.*'        => 'required|integer|min:1',
            'measurements_json'   => 'required|array',
            'measurements_json.*' => 'nullable|string',
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

            Order::create([
                'customer_id'  => $validated['customer_id'],
                'member_id'    => $validated['member_id'] ?: null,
                'service_id'   => $serviceId,
                'price'        => $validated['prices'][$i],
                'quantity'     => $validated['quantities'][$i] ?? 1,
                'paid_amount'  => $i === 0 ? ($validated['paid_amount'] ?? 0) : 0,
                'status'       => 'pending',
                'order_date'   => now()->toDateString(),
                'due_date'     => $validated['due_date'],
                'measurements' => json_encode($decoded),
                'notes'        => $validated['notes'] ?? null,
            ]);
        }

        return redirect()->route('orders.index')->with('success', "{$count} order(s) created successfully!");
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

        $query = Measurement::where('customer_id', $request->customer_id);

        if ($request->filled('member_id')) {
            $query->where('member_id', $request->member_id);
        } else {
            $query->whereNull('member_id');
        }

        $services = Service::whereIn('id', $query->select('service_id'))
            ->where('is_active', true)
            ->get()
            ->map(fn ($s) => [
                'id'                 => $s->id,
                'name'               => $s->name,
                'price'              => (float) $s->price,
                'days'               => $s->estimated_days,
                'measurement_fields' => $s->measurement_fields ?? [],
            ]);

        return response()->json($services);
    }

    // ── API: Search customers by name/phone ──

    public function apiSearchCustomers(Request $request)
    {
        $q = $request->input('q', '');

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $customers = Customer::with('members')
            ->where('name', 'like', "%{$q}%")
            ->orWhere('phone', 'like', "%{$q}%")
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
            ->where('service_id', $request->service_id)
            ->when(
                $request->member_id,
                fn ($q, $mid) => $q->where('member_id', $mid),
                fn ($q) => $q->whereNull('member_id')
            )
            ->latest()
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
