<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Salary;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();
        $preset = $request->query('preset', '');

        // Determine date range from preset or custom inputs
        if ($preset === 'today') {
            $fromDate = $today->toDateString();
            $toDate = $today->toDateString();
        } elseif ($preset === 'yesterday') {
            $yesterday = $today->copy()->subDay()->toDateString();
            $fromDate = $yesterday;
            $toDate = $yesterday;
        } elseif ($preset === 'this_week') {
            $fromDate = $today->copy()->startOfWeek()->toDateString();
            $toDate = $today->toDateString();
        } elseif ($preset === 'this_month') {
            $fromDate = $today->copy()->startOfMonth()->toDateString();
            $toDate = $today->toDateString();
        } elseif ($preset === 'last_month') {
            $fromDate = $today->copy()->subMonth()->startOfMonth()->toDateString();
            $toDate = $today->copy()->subMonth()->endOfMonth()->toDateString();
        } elseif ($preset === 'this_year') {
            $fromDate = $today->copy()->startOfYear()->toDateString();
            $toDate = $today->toDateString();
        } elseif ($preset === 'all') {
            $fromDate = '2020-01-01';
            $toDate = $today->toDateString();
        } else {
            $fromDate = $request->query('from_date', $today->copy()->startOfMonth()->toDateString());
            $toDate = $request->query('to_date', $today->toDateString());
        }

        // Ensure fromDate is not after toDate
        if ($fromDate > $toDate) {
            $temp = $fromDate;
            $fromDate = $toDate;
            $toDate = $temp;
        }

        // ── 1. Orders Query & Metrics ──
        $ordersQuery = Order::query()
            ->where(function ($q) use ($fromDate, $toDate) {
                $q->whereBetween('order_date', [$fromDate, $toDate])
                  ->orWhere(function ($sub) use ($fromDate, $toDate) {
                      $sub->whereNull('order_date')
                          ->whereDate('created_at', '>=', $fromDate)
                          ->whereDate('created_at', '<=', $toDate);
                  });
            });

        $totalOrdersCount = (int) (clone $ordersQuery)->count();
        $totalOrderAmount = (float) (clone $ordersQuery)->sum(DB::raw('COALESCE(price * quantity, 0)'));
        
        // Orders paid & remaining balances for orders in this period
        $periodOrdersPaid = (float) (clone $ordersQuery)->sum('paid_amount');
        $periodOrdersDue = (float) (clone $ordersQuery)
            ->whereIn('payment_status', ['unpaid', 'partial'])
            ->sum(DB::raw('COALESCE(price * quantity - paid_amount, 0)'));

        $ordersStatusCounts = [
            'pending'     => (clone $ordersQuery)->where('status', 'pending')->count(),
            'in_progress' => (clone $ordersQuery)->where('status', 'in_progress')->count(),
            'completed'   => (clone $ordersQuery)->where('status', 'completed')->count(),
            'delivered'   => (clone $ordersQuery)->where('status', 'delivered')->count(),
            'cancelled'   => (clone $ordersQuery)->where('status', 'cancelled')->count(),
        ];

        // ── 2. Payment Receipts Query & Metrics ──
        $paymentsQuery = Payment::query()
            ->whereDate('created_at', '>=', $fromDate)
            ->whereDate('created_at', '<=', $toDate);

        $totalReceipts = (float) (clone $paymentsQuery)->sum('amount');
        $receiptsCount = (int) (clone $paymentsQuery)->count();

        $receiptsByMethod = (clone $paymentsQuery)
            ->select('method', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('method')
            ->get();

        // ── 3. Salaries Paid Query & Metrics ──
        $salariesQuery = Salary::query()
            ->where('status', 'paid')
            ->where(function ($q) use ($fromDate, $toDate) {
                $q->whereBetween('payment_date', [$fromDate, $toDate])
                  ->orWhere(function ($sub) use ($fromDate, $toDate) {
                      $sub->whereNull('payment_date')
                          ->whereDate('created_at', '>=', $fromDate)
                          ->whereDate('created_at', '<=', $toDate);
                  });
            });

        $totalSalariesPaid = (float) (clone $salariesQuery)->sum('net_salary');
        $salariesCount = (int) (clone $salariesQuery)->count();

        // ── 4. Expenses Query & Metrics ──
        $expensesQuery = Expense::query()
            ->whereDate('date', '>=', $fromDate)
            ->whereDate('date', '<=', $toDate);

        $totalExpenses = (float) (clone $expensesQuery)->sum('amount');
        $expensesCount = (int) (clone $expensesQuery)->count();

        $expensesByCategory = (clone $expensesQuery)
            ->select('category', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        // ── 5. Net Calculations ──
        $totalOutflow = $totalSalariesPaid + $totalExpenses;
        $netCashFlow = $totalReceipts - $totalOutflow; // Cash in vs Cash out
        $netProfit = $totalOrderAmount - $totalOutflow; // Total Sales vs Total Outflow

        // ── 6. Detail Lists for the Selected Range ──
        $ordersList = (clone $ordersQuery)
            ->with(['customer:id,name,phone', 'service:id,name'])
            ->latest('id')
            ->limit(50)
            ->get();

        $paymentsList = (clone $paymentsQuery)
            ->with(['order.customer:id,name', 'account:id,name'])
            ->latest('id')
            ->limit(50)
            ->get();

        $salariesList = (clone $salariesQuery)
            ->with(['employee.department', 'employee.designation'])
            ->latest('id')
            ->limit(50)
            ->get();

        $expensesList = (clone $expensesQuery)
            ->with(['expenseCategory', 'account'])
            ->latest('id')
            ->limit(50)
            ->get();

        return view('reports.index', compact(
            'fromDate',
            'toDate',
            'preset',
            'totalOrdersCount',
            'totalOrderAmount',
            'periodOrdersPaid',
            'periodOrdersDue',
            'ordersStatusCounts',
            'totalReceipts',
            'receiptsCount',
            'receiptsByMethod',
            'totalSalariesPaid',
            'salariesCount',
            'totalExpenses',
            'expensesCount',
            'expensesByCategory',
            'totalOutflow',
            'netCashFlow',
            'netProfit',
            'ordersList',
            'paymentsList',
            'salariesList',
            'expensesList'
        ));
    }
}
