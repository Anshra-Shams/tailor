<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(Request $request): View
    {
        $query = Service::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $services = $query->latest()->paginate(10)->withQueryString();

        return view('services.index', compact('services'));
    }

    public function create(): View
    {
        return view('services.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'price' => 'nullable|numeric|min:0',
            'price_basic' => 'nullable|numeric|min:0',
            'price_standard' => 'nullable|numeric|min:0',
            'price_premium' => 'nullable|numeric|min:0',
            'estimated_days' => 'nullable|integer|min:1',
            'measurement_fields' => 'nullable|array',
            'measurement_fields.*.label' => 'required|string|max:100',
            'measurement_fields.*.type' => 'nullable|string|in:upper,lower',
            'measurement_fields.*.required' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $basic = $request->filled('price_basic') ? (float)$request->price_basic : ($request->filled('price') ? (float)$request->price : 1000);
        $standard = $request->filled('price_standard') ? (float)$request->price_standard : ($request->filled('price') ? (float)$request->price : 1500);
        $premium = $request->filled('price_premium') ? (float)$request->price_premium : 2500;

        $validated['price'] = $request->filled('price') ? (float)$request->price : $standard;
        $validated['pricing_tiers'] = [
            'basic' => $basic,
            'standard' => $standard,
            'premium' => $premium,
        ];
        $validated['is_active'] = $request->boolean('is_active');
        $validated['measurement_fields'] = $this->buildFields($request->measurement_fields);

        Service::create($validated);

        return redirect()->route('services.index')
            ->with('success', 'Service created successfully.');
    }

    public function show(Service $service): View
    {
        $service->load('orders');
        return view('services.show', compact('service'));
    }

    public function edit(Service $service): View
    {
        return view('services.edit', compact('service'));
    }

    public function update(Request $request, Service $service): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'price' => 'nullable|numeric|min:0',
            'price_basic' => 'nullable|numeric|min:0',
            'price_standard' => 'nullable|numeric|min:0',
            'price_premium' => 'nullable|numeric|min:0',
            'estimated_days' => 'nullable|integer|min:1',
            'measurement_fields' => 'nullable|array',
            'measurement_fields.*.label' => 'required|string|max:100',
            'measurement_fields.*.type' => 'nullable|string|in:upper,lower',
            'measurement_fields.*.required' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $basic = $request->filled('price_basic') ? (float)$request->price_basic : ($request->filled('price') ? (float)$request->price : 1000);
        $standard = $request->filled('price_standard') ? (float)$request->price_standard : ($request->filled('price') ? (float)$request->price : 1500);
        $premium = $request->filled('price_premium') ? (float)$request->price_premium : 2500;

        $validated['price'] = $request->filled('price') ? (float)$request->price : $standard;
        $validated['pricing_tiers'] = [
            'basic' => $basic,
            'standard' => $standard,
            'premium' => $premium,
        ];
        $validated['is_active'] = $request->boolean('is_active');
        $validated['measurement_fields'] = $this->buildFields($request->measurement_fields);

        $service->update($validated);

        return redirect()->route('services.index')
            ->with('success', 'Service updated successfully.');
    }

    public function destroy(Service $service): RedirectResponse
    {
        $service->delete();

        return redirect()->route('services.index')
            ->with('success', 'Service deleted successfully.');
    }

    private function buildFields(?array $fields): array
    {
        if (!$fields) return [];

        $filtered = array_values(array_filter($fields, function ($f) {
            return !empty(trim($f['label'] ?? ''));
        }));

        $usedKeys = [];
        $result = [];

        foreach ($filtered as $f) {
            $key = $this->generateKey($f['label'], $usedKeys);
            $usedKeys[] = $key;

            $type = strtolower(trim($f['type'] ?? 'upper'));
            if (!in_array($type, ['upper', 'lower'])) {
                $type = 'upper';
            }

            $result[] = [
                'key'      => $key,
                'label'    => trim($f['label']),
                'type'     => $type,
                'required' => !empty($f['required']),
            ];
        }

        return $result;
    }

    private function generateKey(string $label, array $usedKeys): string
    {
        $base = preg_replace('/[^a-z0-9]+/i', '_', strtolower(trim($label)));
        $base = trim($base, '_');
        $key  = $base;

        $counter = 2;
        while (in_array($key, $usedKeys)) {
            $key = $base . '_' . $counter;
            $counter++;
        }

        return $key;
    }
}
