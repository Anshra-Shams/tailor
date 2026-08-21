<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $query = Customer::withCount('members');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhereHas('members', function ($mq) use ($search) {
                      $mq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $customers = $query->latest()->paginate(10)->withQueryString();

        return view('customers.index', compact('customers'));
    }

    public function create(): View
    {
        return view('customers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
            'members' => 'nullable|array',
            'members.*.name' => 'required_with:members|string|max:255',
            'members.*.phone' => 'nullable|string|max:20',
            'members.*.gender' => 'nullable|in:male,female,other',
        ]);

        DB::transaction(function () use ($validated) {
            $customer = Customer::create([
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'gender' => $validated['gender'] ?? null,
                'address' => $validated['address'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            if (!empty($validated['members'])) {
                foreach ($validated['members'] as $member) {
                    if (!empty($member['name'])) {
                        $customer->members()->create([
                            'name' => $member['name'],
                            'phone' => $member['phone'] ?? null,
                            'gender' => $member['gender'] ?? 'male',
                        ]);
                    }
                }
            }
        });

        return redirect()->route('customers.index')
            ->with('success', 'Customer created successfully.');
    }

    public function show(Customer $customer): View
    {
        $customer->load(['members', 'orders']);
        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer): View
    {
        $customer->load('members');
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
            'members' => 'nullable|array',
            'members.*.id' => 'nullable|integer',
            'members.*.name' => 'required_with:members|string|max:255',
            'members.*.phone' => 'nullable|string|max:20',
            'members.*.gender' => 'nullable|in:male,female,other',
        ]);

        DB::transaction(function () use ($validated, $customer) {
            $customer->update([
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'gender' => $validated['gender'] ?? null,
                'address' => $validated['address'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            $existingMemberIds = [];
            if (!empty($validated['members'])) {
                foreach ($validated['members'] as $memberData) {
                    if (empty($memberData['name'])) continue;

                    if (!empty($memberData['id'])) {
                        $member = $customer->members()->find($memberData['id']);
                        if ($member) {
                            $member->update([
                                'name' => $memberData['name'],
                                'phone' => $memberData['phone'] ?? null,
                                'gender' => $memberData['gender'] ?? 'male',
                            ]);
                            $existingMemberIds[] = $member->id;
                        }
                    } else {
                        $newMember = $customer->members()->create([
                            'name' => $memberData['name'],
                            'phone' => $memberData['phone'] ?? null,
                            'gender' => $memberData['gender'] ?? 'male',
                        ]);
                        $existingMemberIds[] = $newMember->id;
                    }
                }
            }

            $customer->members()->whereNotIn('id', $existingMemberIds)->delete();
        });

        return redirect()->route('customers.index')
            ->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', 'Customer deleted successfully.');
    }

    public function getMembers(Customer $customer): \Illuminate\Http\JsonResponse
    {
        return response()->json($customer->members);
    }

    public function apiStore(Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'name'   => 'required|string|max:255',
            'phone'  => 'required|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'address'=> 'nullable|string|max:500',
        ]);

        $customer = Customer::create($validated);

        return response()->json([
            'id'      => $customer->id,
            'name'    => $customer->name,
            'gender'  => $customer->gender,
            'members'=> [],
        ]);
    }
}
