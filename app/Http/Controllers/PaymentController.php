<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    // ── Base query for orders with outstanding balance ──

    private static function dueQuery()
    {
        return Order::query()
            ->whereIn('orders.payment_status', ['unpaid', 'partial'])
            ->with(['customer:id,name,phone', 'member:id,name,relation', 'service:id,name']);
    }

    public static function mapOrder(Order $o): array
    {
        return [
            'id'             => $o->id,
            'no'             => str_pad($o->id, 4, '0', STR_PAD_LEFT),
            'customer_id'    => $o->customer_id,
            'customer'       => $o->customer?->name ?? '—',
            'phone'          => $o->customer?->phone ?? '',
            'member'         => $o->member?->name,
            'relation'       => $o->member?->relation,
            'service'        => $o->service?->name ?? '—',
            'quantity'       => (int) $o->quantity,
            'unit_price'     => (float) $o->price,
            'total'          => $o->totalAmount(),
            'paid'           => (float) $o->paid_amount,
            'remaining'      => $o->remainingDue(),
            'payment_status' => $o->payment_status,
            'order_status'   => $o->status,
            'due_date'       => optional($o->due_date)->format('d M Y'),
        ];
    }

    // ── Page ──

    public function index(Request $request): Response
    {
        $orders = self::dueQuery()
            ->latest('orders.id')
            ->paginate(15)
            ->withQueryString();

        $stats = Order::whereIn('payment_status', ['unpaid', 'partial'])
            ->selectRaw('COUNT(*) AS cnt, COALESCE(SUM(price * quantity - paid_amount), 0) AS due')
            ->first();

        return response()
            ->view('payments.index', [
                'orders'          => $orders,
                'initialOrders'   => $orders->getCollection()->map(fn ($o) => self::mapOrder($o))->values(),
                'outstandingDue'  => (float) ($stats->due ?? 0),
                'dueCount'        => (int) ($stats->cnt ?? 0),
                'accounts'        => Account::with('category')->where('is_active', true)->orderBy('name')->get(),
            ])
            ->header('Cache-Control', 'no-store, max-age=0');
    }

    // ── API: search due orders ──

    public function apiSearch(Request $request): JsonResponse
    {
        $q = trim($request->input('q', ''));

        $query = self::dueQuery();

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                if (ctype_digit($q)) {
                    $w->orWhere('orders.id', (int) $q);
                }
                $w->orWhereHas('customer', function ($c) use ($q) {
                    $c->where('name', 'like', "%{$q}%")
                      ->orWhere('phone', 'like', "%{$q}%");
                });
                $w->orWhereHas('member', function ($m) use ($q) {
                    $m->where('name', 'like', "%{$q}%");
                });
            });
        }

        $results = $query->latest('orders.id')
            ->limit(50)
            ->get()
            ->map(fn ($o) => self::mapOrder($o))
            ->values();

        return response()->json($results);
    }

    // ── API: payment history of an order ──

    public function apiPayments(Order $order): JsonResponse
    {
        return response()->json(
            $order->payments()
                ->get()
                ->map(fn (Payment $p) => [
                    'id'     => $p->id,
                    'amount' => (float) $p->amount,
                    'method' => $p->method,
                    'notes'  => $p->notes,
                    'date'   => $p->created_at->format('d M Y, h:i A'),
                ])
        );
    }

    // ── API: recent payments across all orders ──

    public function apiRecentPayments(Request $request): JsonResponse
    {
        $limit  = min((int) $request->input('limit', 50), 200);
        $page   = max((int) $request->input('page', 1), 1);
        $offset = ($page - 1) * $limit;

        $payments = Payment::with(['order.customer:id,name,phone', 'order.member:id,name'])
            ->latest('payments.id')
            ->skip($offset)
            ->take($limit)
            ->get()
            ->map(fn (Payment $p) => [
                'id'        => $p->id,
                'order_id'  => $p->order_id,
                'order_no'  => str_pad($p->order_id, 4, '0', STR_PAD_LEFT),
                'customer'  => $p->order?->customer?->name ?? '—',
                'phone'     => $p->order?->customer?->phone ?? '',
                'member'    => $p->order?->member?->name,
                'amount'    => (float) $p->amount,
                'method'    => str_replace('_', ' ', $p->method),
                'notes'     => $p->notes,
                'type'      => str_contains((string) $p->notes, 'Advance') ? 'advance' : 'payment',
                'date'      => $p->created_at->format('d M Y'),
                'time'      => $p->created_at->format('h:i A'),
            ]);

        return response()->json($payments);
    }

    // ── Save payment ──

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id'   => ['required', 'exists:orders,id'],
            'amount'     => ['required', 'numeric', 'min:0.01'],
            'account_id' => ['required', 'exists:accounts,id'],
            'notes'      => ['nullable', 'string', 'max:500'],
        ]);

        $order = Order::with(['customer:id,name,phone', 'member:id,name,relation', 'service:id,name'])->findOrFail($validated['order_id']);

        $error = DB::transaction(function () use ($validated, $order) {
            $locked = Order::lockForUpdate()->findOrFail($validated['order_id']);

            $due = $locked->remainingDue();
            if ($due <= 0) {
                return 'This order is already fully paid.';
            }

            $amount = round((float) $validated['amount'], 2);
            if ($amount > $due + 0.001) {
                return 'Payment cannot exceed the remaining due of Rs. ' . number_format($due) . '.';
            }

            $account = Account::find($validated['account_id']);

            Payment::create([
                'order_id'   => $locked->id,
                'account_id' => $account?->id,
                'amount'     => $amount,
                'method'     => $account ? $account->paymentMethod() : 'other',
                'notes'      => $validated['notes'] ?? null,
            ]);

            if ($account) {
                $account->increment('current_balance', $amount);
            }

            $locked->paid_amount = (float) $locked->paid_amount + $amount;
            $locked->refreshPaymentStatus();
            $locked->save();

            return null;
        });

        $no = str_pad($validated['order_id'], 4, '0', STR_PAD_LEFT);

        // AJAX request -> JSON responses so the page can update without reload
        if ($request->wantsJson()) {
            if ($error) {
                return response()->json(['success' => false, 'message' => $error], 422);
            }

            return response()->json([
                'success' => true,
                'message' => "Payment of Rs. " . number_format((float) $validated['amount']) . " received for order #{$no}!",
                'order'   => self::mapOrder($order->fresh(['customer:id,name,phone', 'member:id,name,relation', 'service:id,name'])),
            ]);
        }

        if ($error) {
            return redirect()
                ->route('payments.index')
                ->withInput()
                ->withErrors(['amount' => $error]);
        }

        return redirect()
            ->route('payments.index')
            ->with('success', "Payment of Rs. " . number_format((float) $validated['amount']) . " received for order #{$no}. Ledger updated!");
    }
}
