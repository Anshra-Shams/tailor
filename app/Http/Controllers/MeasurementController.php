<?php

namespace App\Http\Controllers;

use App\Models\Measurement;
use Illuminate\Http\Request;

class MeasurementController extends Controller
{
    public function index(Request $request)
    {
        $query = Measurement::with(['customer', 'member', 'service']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('customer', fn ($cq) => $cq->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('member', fn ($mq) => $mq->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('service', fn ($sq) => $sq->where('name', 'like', "%{$search}%"));
            });
        }

        $measurements = $query->latest()->paginate(15)->withQueryString();

        return view('measurements.index', compact('measurements'));
    }

    public function create()
    {
        return view('measurements.create');
    }

    public function store(Request $request)
    {
        $measurements = $request->measurements;
        if (is_string($measurements)) {
            $decoded = json_decode($measurements, true);
            $request->merge(['measurements' => is_array($decoded) ? $decoded : []]);
        }

        $validated = $request->validate([
            'customer_id'  => 'required|exists:customers,id',
            'member_ids'   => 'required|array|min:1',
            'member_ids.*' => 'nullable|exists:members,id',
            'service_id'   => 'required|exists:services,id',
            'notes'        => 'nullable|string|max:1000',
            'measurements' => 'required|array',
        ]);

        foreach ($validated['member_ids'] as $memberId) {
            Measurement::create([
                'customer_id' => $validated['customer_id'],
                'member_id'   => $memberId ?: null,
                'service_id'  => $validated['service_id'],
                'data'        => $validated['measurements'],
                'notes'       => $validated['notes'] ?? null,
            ]);
        }

        return redirect()->route('measurements.index')->with('success', 'Measurements saved successfully!');
    }

    public function show(Measurement $measurement)
    {
        $measurement->load(['customer', 'member', 'service']);

        return view('measurements.show', compact('measurement'));
    }

    public function edit(Measurement $measurement)
    {
        $measurement->load(['customer', 'member', 'service']);

        return view('measurements.edit', compact('measurement'));
    }

    public function update(Request $request, Measurement $measurement)
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
            'customer_id'  => 'required|exists:customers,id',
            'member_id'    => 'nullable|exists:members,id',
            'service_id'   => 'required|exists:services,id',
            'notes'        => 'nullable|string|max:1000',
            'measurements' => 'required|array',
        ]);

        $measurement->update([
            'customer_id' => $validated['customer_id'],
            'member_id'   => $validated['member_id'] ?? null,
            'service_id'  => $validated['service_id'],
            'data'        => $validated['measurements'],
            'notes'       => $validated['notes'] ?? null,
        ]);

        return redirect()->route('measurements.index')->with('success', 'Measurement updated successfully!');
    }

    public function destroy(Measurement $measurement)
    {
        $measurement->delete();
        return redirect()->route('measurements.index')->with('success', 'Measurement deleted.');
    }
}
