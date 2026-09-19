<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountCategory;
use App\Models\Payment;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    // ── Page ──

    public function index()
    {
        $categories = AccountCategory::with('accounts')
            ->orderBy('name')
            ->get();

        $categoriesData = $categories->map(fn ($c) => [
            'id'          => $c->id,
            'name'        => $c->name,
            'description' => $c->description,
            'accounts'    => $c->accounts->map(fn ($a) => [
                'id'              => $a->id,
                'category_id'     => $a->account_category_id,
                'name'            => $a->name,
                'opening_balance' => (float) $a->opening_balance,
                'current_balance' => (float) $a->current_balance,
                'type'            => $a->type,
                'is_active'       => $a->is_active,
            ])->values()->all(),
        ])->values();

        return view('accounts.index', compact('categories', 'categoriesData'));
    }

    // ── Store Category ──

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:account_categories,name',
            'description' => 'nullable|string|max:1000',
        ], [
            'name.unique' => 'This category already exists. Each category can only be added once.',
        ]);

        $category = AccountCategory::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success'  => true,
                'message'  => "Category \"{$category->name}\" created successfully!",
                'category' => [
                    'id'          => $category->id,
                    'name'        => $category->name,
                    'description' => $category->description,
                    'accounts'    => [],
                ],
            ]);
        }

        return redirect()->route('accounts.index')
            ->with('success', "Category \"{$category->name}\" created successfully!");
    }

    // ── Store Account ──

    public function storeAccount(Request $request)
    {
        $validated = $request->validate([
            'account_category_id' => 'required|exists:account_categories,id',
            'name'                => 'required|string|max:255',
            'opening_balance'     => 'nullable|numeric|min:0',
            'type'                => 'required|in:debit,credit',
        ]);

        $openingBalance = (float) ($validated['opening_balance'] ?? 0);

        $account = Account::create([
            'account_category_id' => $validated['account_category_id'],
            'name'                => $validated['name'],
            'opening_balance'     => $openingBalance,
            'current_balance'     => $openingBalance,
            'type'                => $validated['type'],
            'is_active'           => true,
        ]);

        $account->load('category');

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Account \"{$account->name}\" created successfully!",
                'account' => [
                    'id'              => $account->id,
                    'category_id'     => $account->account_category_id,
                    'name'            => $account->name,
                    'opening_balance' => (float) $account->opening_balance,
                    'current_balance' => (float) $account->current_balance,
                    'type'            => $account->type,
                    'is_active'       => $account->is_active,
                ],
            ]);
        }

        return redirect()->route('accounts.index')
            ->with('success', "Account \"{$account->name}\" created successfully!");
    }

    // ── Toggle Active/Inactive ──

    public function toggleActive(Account $account, Request $request)
    {
        $account->update(['is_active' => !$account->is_active]);
        $status = $account->is_active ? 'activated' : 'deactivated';

        if ($request->wantsJson()) {
            return response()->json([
                'success'   => true,
                'message'   => "Account \"{$account->name}\" {$status}.",
                'is_active' => $account->is_active,
            ]);
        }

        return redirect()->route('accounts.index')
            ->with('success', "Account \"{$account->name}\" {$status}.");
    }

    // ── Ledger Page (Payments for this account) ──

    public function ledger(Account $account)
    {
        $account->load('category');

        $needle = strtolower(trim((string) ($account->category?->name ?? '')));
        if ($needle === '') {
            $needle = strtolower(trim((string) $account->name));
        }

        $rows = Payment::with('order.customer')
            ->orderByDesc('created_at')
            ->get()
            ->filter(function (Payment $payment) use ($account, $needle) {
                if ((int) $payment->account_id === (int) $account->id) {
                    return true;
                }

                if ($payment->account_id) {
                    return false;
                }

                $method = strtolower(str_replace('_', ' ', (string) $payment->method));

                return $needle !== '' && (str_contains($method, $needle) || str_contains($needle, $method));
            })
            ->map(function (Payment $payment) {
                $order    = $payment->order;
                $customer = $order?->customer;

                return [
                    'id'       => $payment->id,
                    'customer' => $customer?->name ?? 'Walk-in Customer',
                    'phone'    => $customer?->phone ?? '—',
                    'order_no' => $order?->id,
                    'amount'   => (float) $payment->amount,
                    'notes'    => $payment->notes,
                    'date'     => $payment->created_at?->format('d M Y'),
                    'time'     => $payment->created_at?->format('h:i A'),
                ];
            })
            ->values();

        return view('accounts.ledger', compact('account', 'rows'));
    }

    // ── Edit Category ──

    public function updateCategory(Request $request, AccountCategory $category)
    {
        $validated = $request->validate([
            'name'        => "required|string|max:255|unique:account_categories,name,{$category->id}",
            'description' => 'nullable|string|max:1000',
        ], [
            'name.unique' => 'This category name is already taken.',
        ]);

        $category->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success'  => true,
                'message'  => "Category updated successfully!",
                'category' => ['id' => $category->id, 'name' => $category->name, 'description' => $category->description],
            ]);
        }

        return redirect()->route('accounts.index')->with('success', 'Category updated!');
    }
}
