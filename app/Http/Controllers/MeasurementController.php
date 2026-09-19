<?php

namespace App\Http\Controllers;

use App\Models\Customer;
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
            'service_id'   => 'nullable|exists:services,id',
            'notes'        => 'nullable|string|max:1000',
            'measurements' => 'required|array',
        ]);

        $cleanedData = $this->cleanMeasurementsData($validated['measurements']);

        foreach ($validated['member_ids'] as $memberId) {
            Measurement::create([
                'customer_id' => $validated['customer_id'],
                'member_id'   => $memberId ?: null,
                'service_id'  => $validated['service_id'] ?? null,
                'data'        => $cleanedData,
                'notes'       => $validated['notes'] ?? null,
            ]);
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Measurements saved successfully!']);
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
            'service_id'   => 'nullable|exists:services,id',
            'notes'        => 'nullable|string|max:1000',
            'measurements' => 'required|array',
        ]);

        $cleanedData = $this->cleanMeasurementsData($validated['measurements']);

        $measurement->update([
            'customer_id' => $validated['customer_id'],
            'member_id'   => $validated['member_id'] ?? null,
            'service_id'  => $validated['service_id'] ?? null,
            'data'        => $cleanedData,
            'notes'       => $validated['notes'] ?? null,
        ]);

        return redirect()->route('measurements.index')->with('success', 'Measurement updated successfully!');
    }

    private function cleanMeasurementsData(array $measurements): array
    {
        $cleaned = [];
        foreach ($measurements as $key => $val) {
            if ($key === '__style' || $key === '__custom' || $key === '__custom_fields') {
                $cleaned[$key] = $val;
            } elseif (is_array($val)) {
                $filtered = array_values(array_filter(array_map('trim', array_map('strval', $val)), fn ($v) => $v !== ''));
                if (!empty($filtered)) {
                    $cleaned[$key] = $filtered;
                }
            } elseif (is_string($val) || is_numeric($val)) {
                $parts = array_values(array_filter(array_map('trim', explode(',', (string)$val)), fn ($v) => $v !== ''));
                if (!empty($parts)) {
                    $cleaned[$key] = $parts;
                }
            }
        }
        return $cleaned;
    }

    public function destroy(Measurement $measurement)
    {
        $measurement->delete();
        return redirect()->route('measurements.index')->with('success', 'Measurement deleted.');
    }

    public function apiCustomerMeasurements(Request $request, Customer $customer)
    {
        $memberId = $request->query('member_id');
        if ($memberId === '__self__' || $memberId === '') {
            $memberId = null;
        }

        $measurement = Measurement::where('customer_id', $customer->id)
            ->when($memberId, fn ($q) => $q->where('member_id', $memberId), fn ($q) => $q->whereNull('member_id'))
            ->latest()
            ->first();

        return response()->json([
            'measurements'   => $measurement?->data ?? [],
            'notes'          => $measurement?->notes ?? '',
            'saved_date'     => $measurement?->created_at?->format('M d, Y'),
            'measurement_id' => $measurement?->id,
        ]);
    }
}
