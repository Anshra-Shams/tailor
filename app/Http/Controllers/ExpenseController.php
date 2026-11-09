<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $search = trim($request->input('search', ''));
        $categoryId = $request->input('expense_category_id', '');
        $accountId = $request->input('account_id', '');
        $dateFrom = $request->input('date_from', '');
        $dateTo = $request->input('date_to', '');

        $query = Expense::with(['account.category', 'expenseCategory', 'creator'])->latest('date')->latest('id');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('voucher_no', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('paid_to', 'like', "%{$search}%")
                  ->orWhere('reference_no', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        if ($categoryId !== '') {
            $query->where(function ($q) use ($categoryId) {
                $q->where('expense_category_id', $categoryId);
                $cat = ExpenseCategory::find($categoryId);
                if ($cat) {
                    $q->orWhere('category', $cat->name);
                }
            });
        }

        if ($accountId !== '') {
            $query->where('account_id', $accountId);
        }

        if ($dateFrom !== '') {
            $query->whereDate('date', '>=', $dateFrom);
        }

        if ($dateTo !== '') {
            $query->whereDate('date', '<=', $dateTo);
        }

        $expenses = $query->paginate(15)->withQueryString();

        $todayTotal = (float) Expense::whereDate('date', today())->sum('amount');
        $monthTotal = (float) Expense::whereYear('date', now()->year)->whereMonth('date', now()->month)->sum('amount');
        $allTotal   = (float) Expense::sum('amount');

        $accounts = Account::with('category')->where('is_active', true)->orderBy('name')->get();
        $categories = ExpenseCategory::orderBy('name')->get();

        return view('expenses.index', compact(
            'expenses',
            'todayTotal',
            'monthTotal',
            'allTotal',
            'accounts',
            'categories'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'amount'              => 'required|numeric|min:0.01',
            'account_id'          => 'required|exists:accounts,id',
            'date'                => 'required|date',
            'paid_to'             => 'nullable|string|max:255',
            'reference_no'        => 'nullable|string|max:255',
            'notes'               => 'nullable|string|max:1000',
        ]);

        $account = Account::findOrFail($validated['account_id']);
        $category = ExpenseCategory::findOrFail($validated['expense_category_id']);
        $amount = round((float) $validated['amount'], 2);

        DB::transaction(function () use ($validated, $account, $category, $amount) {
            $voucherNo = Expense::generateVoucherNo();

            Expense::create([
                'voucher_no'          => $voucherNo,
                'account_id'          => $account->id,
                'expense_category_id' => $category->id,
                'category'            => $category->name,
                'amount'              => $amount,
                'date'                => $validated['date'],
                'paid_to'             => $validated['paid_to'] ?? null,
                'payment_method'      => $account->paymentMethod(),
                'reference_no'        => $validated['reference_no'] ?? null,
                'notes'               => $validated['notes'] ?? null,
                'created_by'          => Auth::id(),
            ]);

            // Deduct expense from the account current_balance
            $account->decrement('current_balance', $amount);
        });

        return redirect()->route('expenses.index')->with('success', 'Expense Voucher recorded successfully!');
    }

    public function destroy(Expense $expense)
    {
        DB::transaction(function () use ($expense) {
            if ($expense->account_id) {
                // Revert account balance
                $account = Account::find($expense->account_id);
                if ($account) {
                    $account->increment('current_balance', $expense->amount);
                }
            }

            $expense->delete();
        });

        return redirect()->route('expenses.index')->with('success', 'Expense Voucher deleted successfully!');
    }
}
