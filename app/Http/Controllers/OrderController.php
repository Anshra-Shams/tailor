<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Member;
use App\Models\Order;
use App\Models\Service;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        return view('orders.index');
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

        $query = Order::where('customer_id', $request->customer_id)
            ->where('service_id', $request->service_id);

        if ($request->member_id) {
            $query->where('member_id', $request->member_id);
        } else {
            $query->whereNull('member_id');
        }

        $order = $query->latest()->first();

        if (!$order || !$order->measurements) {
            return response()->json(['measurements' => null]);
        }

        return response()->json([
            'measurements' => json_decode($order->measurements, true),
            'order_date'   => $order->order_date?->format('M d, Y'),
            'order_id'     => $order->id,
        ]);
    }
}
