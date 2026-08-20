<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Member;
use App\Models\Order;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function create()
    {
        $customers = Customer::with('members')->get();

        return view('orders.create', compact('customers'));
    }

    public function store(Request $request)
    {
        if ($request->member_id === '' || $request->member_id === '__self__') {
            $request->merge(['member_id' => null]);
        }

        $measurements = $request->measurements;
        if (is_string($measurements)) {
            $decoded = json_decode($measurements, true);
            $request->merge(['measurements' => is_array($decoded) ? $decoded : []]);
        }

        $validated = $request->validate([
            'customer_id'   => 'required|exists:customers,id',
            'member_id'     => 'nullable|exists:members,id',
            'service_id'    => 'required|exists:services,id',
            'price'         => 'required|numeric|min:0',
            'due_date'      => 'nullable|date',
            'measurements'  => 'required|array',
            'notes'         => 'nullable|string|max:1000',
        ]);

        $order = Order::create([
            'customer_id'  => $validated['customer_id'],
            'member_id'    => $validated['member_id'] ?? null,
            'service_id'   => $validated['service_id'],
            'price'        => $validated['price'],
            'status'       => 'pending',
            'order_date'   => now()->toDateString(),
            'due_date'     => $validated['due_date'] ?? null,
            'measurements' => is_array($validated['measurements']) ? json_encode($validated['measurements']) : $validated['measurements'],
            'notes'        => $validated['notes'] ?? null,
        ]);

        return redirect()->route('orders.show', $order)->with('success', 'Order created successfully!');
    }

    public function show(Order $order)
    {
        $order->load(['customer', 'member', 'service']);
        $measurements = json_decode($order->measurements, true) ?? [];

        return view('orders.show', compact('order', 'measurements'));
    }

    public function index()
    {
        $orders = Order::with(['customer', 'member', 'service'])
            ->latest()
            ->paginate(15);

        return view('orders.index', compact('orders'));
    }

    public function destroy(Order $order)
    {
        $order->delete();

        return redirect()->route('orders.index')->with('success', 'Order deleted.');
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled',
        ]);

        $order->update([
            'status'         => $request->status,
            'completed_date' => $request->status === 'completed' ? now()->toDateString() : null,
        ]);

        return back()->with('success', 'Order status updated.');
    }

    // ── AJAX Endpoints ──

    public function apiCustomers()
    {
        return Customer::with('members')->get()->map(fn($c) => [
            'id'      => $c->id,
            'name'    => $c->name,
            'gender'  => $c->gender,
            'members' => $c->members->map(fn($m) => [
                'id'     => $m->id,
                'name'   => $m->name,
                'gender' => $m->gender,
            ]),
        ]);
    }

    public function apiServices()
    {
        return Service::where('is_active', true)->get()->map(fn($s) => [
            'id'    => $s->id,
            'name'  => $s->name,
            'price' => (float) $s->price,
            'days'  => $s->estimated_days,
        ]);
    }

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
