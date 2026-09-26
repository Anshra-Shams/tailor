<?php

namespace App\Http\Controllers;

use App\Models\Designation;
use Illuminate\Http\Request;

class DesignationController extends Controller
{
    public function index(Request $request)
    {
        $search = trim($request->query('search', ''));

        $designations = Designation::withCount('employees')
            ->when($search !== '', function ($query) use ($search) {
                return $query->where('name', 'like', "%{$search}%")
                             ->orWhere('description', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('designations.index', compact('designations', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Designation::create($request->only('name', 'description'));

        return redirect()->route('designations.index')->with('success', 'Designation created successfully!');
    }

    public function update(Request $request, Designation $designation)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $designation->update($request->only('name', 'description'));

        return redirect()->route('designations.index')->with('success', 'Designation updated successfully!');
    }

    public function destroy(Designation $designation)
    {
        $designation->delete();
        return redirect()->route('designations.index')->with('success', 'Designation deleted successfully!');
    }
}
