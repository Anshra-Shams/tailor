<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{
    public function index(Request $request)
    {
        $search = trim($request->input('search', ''));

        $query = ExpenseCategory::withCount('expenses')->latest('id');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $categories = $query->paginate(15)->withQueryString();
        $totalCategories = ExpenseCategory::count();

        return view('expense-categories.index', compact('categories', 'totalCategories', 'search'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:expense_categories,name',
            'description' => 'nullable|string|max:1000',
        ], [
            'name.unique' => 'This expense category already exists.',
        ]);

        ExpenseCategory::create($validated);

        return redirect()->route('expense-categories.index')->with('success', 'Expense Category created successfully!');
    }

    public function update(Request $request, ExpenseCategory $expenseCategory)
    {
        $validated = $request->validate([
            'name'        => "required|string|max:255|unique:expense_categories,name,{$expenseCategory->id}",
            'description' => 'nullable|string|max:1000',
        ], [
            'name.unique' => 'This category name is already taken.',
        ]);

        $oldName = $expenseCategory->name;
        $expenseCategory->update($validated);

        // Keep existing expenses updated if their string name matches old name
        if ($oldName !== $validated['name']) {
            \App\Models\Expense::where('expense_category_id', $expenseCategory->id)
                ->orWhere('category', $oldName)
                ->update(['category' => $validated['name']]);
        }

        return redirect()->route('expense-categories.index')->with('success', 'Expense Category updated successfully!');
    }

    public function destroy(ExpenseCategory $expenseCategory)
    {
        $expensesCount = $expenseCategory->expenses()->count();

        if ($expensesCount > 0) {
            return redirect()->route('expense-categories.index')
                ->with('error', "Cannot delete category \"{$expenseCategory->name}\" because it is used in {$expensesCount} expense voucher(s).");
        }

        $expenseCategory->delete();

        return redirect()->route('expense-categories.index')->with('success', 'Expense Category deleted successfully!');
    }
}
