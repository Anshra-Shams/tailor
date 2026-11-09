<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Ledger — {{ $employee->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body {
                background: white !important;
                color: black !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .no-print {
                display: none !important;
            }
            .page-break {
                page-break-before: always;
            }
        }
    </style>
</head>
<body class="bg-slate-100 font-sans text-slate-800 p-4 sm:p-8">

    <!-- Action Bar (Hidden on print) -->
    <div class="no-print max-w-4xl mx-auto mb-6 flex items-center justify-between bg-white p-4 rounded-2xl shadow-sm border border-slate-200">
        <a href="{{ route('employee-ledger.show', $employee) }}" 
           class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-slate-900">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Ledger
        </a>
        <div class="flex items-center gap-3">
            <button onclick="window.print()" 
                    class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-5 py-2 rounded-xl shadow-md transition text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print Statement
            </button>
        </div>
    </div>

    <!-- Printable Sheet -->
    <div class="max-w-4xl mx-auto bg-white p-8 sm:p-12 rounded-3xl shadow-lg border border-slate-200/80 print:shadow-none print:border-none print:p-0">
        
        <!-- Header -->
        <div class="border-b-2 border-slate-900 pb-6 mb-6 flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-black tracking-tight text-slate-950 uppercase">Tailor Shop POS</h1>
                <p class="text-xs uppercase font-semibold text-slate-500 tracking-wider mt-1">HR & Payroll Department &middot; Employee Statement</p>
                <p class="text-xs text-slate-400 mt-1">Generated: {{ now()->format('d M Y, h:i A') }}</p>
            </div>
            <div class="text-right">
                <span class="inline-block bg-slate-900 text-white text-xs font-bold uppercase tracking-widest px-3 py-1.5 rounded-md">
                    EMPLOYEE LEDGER
                </span>
                <p class="text-xs text-slate-500 font-semibold mt-2">
                    Period: 
                    @if ($fromDate || $toDate)
                        {{ $fromDate ? \Carbon\Carbon::parse($fromDate)->format('d M Y') : 'Start' }} — {{ $toDate ? \Carbon\Carbon::parse($toDate)->format('d M Y') : 'Today' }}
                    @else
                        All Time Records
                    @endif
                </p>
            </div>
        </div>

        <!-- Employee Info Grid -->
        <div class="bg-slate-50 rounded-2xl p-5 border border-slate-200/80 mb-6 grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
            <div>
                <span class="text-slate-400 font-medium block uppercase tracking-wider text-[10px]">Employee Name</span>
                <span class="font-bold text-slate-900 text-sm mt-0.5 block">{{ $employee->name }}</span>
                <span class="text-slate-500 text-[11px]">ID: #EMP-{{ str_pad($employee->id, 3, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div>
                <span class="text-slate-400 font-medium block uppercase tracking-wider text-[10px]">Department & Role</span>
                <span class="font-bold text-slate-900 mt-0.5 block">{{ $employee->department?->name ?? 'Tailoring' }}</span>
                <span class="text-slate-500 text-[11px]">{{ $employee->designation?->name ?? 'Staff' }}</span>
            </div>
            <div>
                <span class="text-slate-400 font-medium block uppercase tracking-wider text-[10px]">Phone & Contact</span>
                <span class="font-bold text-slate-900 mt-0.5 block">{{ $employee->phone ?: '—' }}</span>
                <span class="text-slate-500 text-[11px]">{{ $employee->email ?: 'No email' }}</span>
            </div>
            <div>
                <span class="text-slate-400 font-medium block uppercase tracking-wider text-[10px]">Salary Structure</span>
                <span class="font-bold text-slate-900 text-sm mt-0.5 block">Rs. {{ number_format((float) $employee->salary, 2) }}</span>
                <span class="text-slate-500 text-[11px] capitalize">{{ $employee->salary_type }} Basis</span>
            </div>
        </div>

        <!-- Key Financial Summary Box -->
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-8">
            <div class="border {{ $stats['due_balance'] < 0 ? 'border-rose-300 bg-rose-50/30' : ($stats['due_balance'] > 0 ? 'border-emerald-300 bg-emerald-50/30' : 'border-slate-200 bg-white') }} rounded-xl p-3.5">
                <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Due Balance</span>
                <span class="text-xl font-black {{ $stats['due_balance'] < 0 ? 'text-rose-700' : ($stats['due_balance'] > 0 ? 'text-emerald-700' : 'text-slate-900') }} mt-1 block">
                    {{ $stats['due_balance'] < 0 ? '- Rs. ' . number_format(abs($stats['due_balance']), 2) : 'Rs. ' . number_format($stats['due_balance'], 2) }}
                </span>
                <span class="text-[10px] font-semibold text-slate-500">
                    {{ $stats['due_balance'] < 0 ? 'Advance Given' : ($stats['due_balance'] > 0 ? 'Payable to Staff' : 'Settled') }}
                </span>
            </div>
            <div class="border border-slate-200 rounded-xl p-3.5 bg-white">
                <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Total Disbursed (Paid)</span>
                <span class="text-xl font-black text-slate-900 mt-1 block">Rs. {{ number_format($stats['total_disbursed'], 2) }}</span>
                <span class="text-[10px] text-slate-400">Cash / Bank paid</span>
            </div>
            <div class="border border-slate-200 rounded-xl p-3.5 bg-white">
                <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">{{ $employee->salary_type === 'project' ? 'Work Earned' : 'Basic Earned' }}</span>
                <span class="text-xl font-black text-slate-900 mt-1 block">Rs. {{ number_format($stats['total_work_earned'], 2) }}</span>
                <span class="text-[10px] text-slate-400">Assigned work gross</span>
            </div>
            <div class="border border-slate-200 rounded-xl p-3.5 bg-white">
                <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Bonuses / Deduct</span>
                <span class="text-sm font-black text-slate-800 mt-2 block">
                    <span class="text-indigo-700">+{{ number_format($stats['total_bonus'], 0) }}</span> / 
                    <span class="text-rose-700">-{{ number_format($stats['total_deductions'], 0) }}</span>
                </span>
            </div>
            <div class="border border-slate-200 rounded-xl p-3.5 bg-white">
                <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Assigned Orders</span>
                <span class="text-xl font-black text-purple-700 mt-1 block">{{ $stats['total_orders'] }} <span class="text-xs font-normal text-slate-500">({{ $stats['completed_orders'] }} done)</span></span>
                <span class="text-[10px] text-slate-400">Rs. {{ number_format($stats['assigned_work_amount'] ?? 0, 0) }} value</span>
            </div>
        </div>

        <!-- Section: Salary Payouts History -->
        <div class="mb-8">
            <h3 class="text-xs font-black uppercase tracking-wider text-slate-900 mb-2 border-b border-slate-200 pb-1 flex justify-between">
                <span>1. Salary Disbursements & Payouts (ادائیگیاں)</span>
                <span class="font-normal text-slate-500">{{ $salaries->count() }} records</span>
            </h3>

            @if ($salaries->isEmpty())
                <p class="text-xs text-slate-400 italic py-3">No salary payments recorded in this period.</p>
            @else
                <table class="w-full text-xs text-left">
                    <thead class="border-b border-slate-200 text-slate-500 uppercase tracking-wider font-semibold text-[10px]">
                        <tr>
                            <th class="py-2">Date</th>
                            <th class="py-2">Period / Month</th>
                            <th class="py-2 text-right">Basic</th>
                            <th class="py-2 text-right">Bonus (+)</th>
                            <th class="py-2 text-right">Deductions (-)</th>
                            <th class="py-2 text-right">Net Disbursed</th>
                            <th class="py-2 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($salaries as $sal)
                            <tr>
                                <td class="py-2 font-medium">{{ $sal->payment_date ? \Carbon\Carbon::parse($sal->payment_date)->format('d M Y') : '—' }}</td>
                                <td class="py-2 text-slate-600">
                                    {{ $sal->from_date && $sal->to_date ? \Carbon\Carbon::parse($sal->from_date)->format('d M') . ' - ' . \Carbon\Carbon::parse($sal->to_date)->format('d M Y') : $sal->salary_month }}
                                </td>
                                <td class="py-2 text-right">Rs. {{ number_format((float) $sal->basic_salary, 2) }}</td>
                                <td class="py-2 text-right text-indigo-700 font-semibold">{{ (float) $sal->bonus > 0 ? '+ ' . number_format((float) $sal->bonus, 2) : '—' }}</td>
                                <td class="py-2 text-right text-rose-700 font-semibold">{{ (float) $sal->deductions > 0 ? '- ' . number_format((float) $sal->deductions, 2) : '—' }}</td>
                                <td class="py-2 text-right font-bold text-slate-950">Rs. {{ number_format((float) $sal->net_salary, 2) }}</td>
                                <td class="py-2 text-center uppercase font-bold text-[9px]">{{ $sal->status }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="border-t-2 border-slate-900 font-bold">
                        <tr>
                            <td colspan="2" class="py-2 uppercase text-[10px]">Total</td>
                            <td class="py-2 text-right">Rs. {{ number_format($stats['total_basic'], 2) }}</td>
                            <td class="py-2 text-right text-indigo-700">Rs. {{ number_format($stats['total_bonus'], 2) }}</td>
                            <td class="py-2 text-right text-rose-700">Rs. {{ number_format($stats['total_deductions'], 2) }}</td>
                            <td class="py-2 text-right text-slate-950">Rs. {{ number_format($stats['total_disbursed'], 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            @endif
        </div>

        <!-- Section: Production & Assigned Orders -->
        @if ($orders->isNotEmpty())
            <div class="mb-8">
                <h3 class="text-xs font-black uppercase tracking-wider text-slate-900 mb-2 border-b border-slate-200 pb-1 flex justify-between">
                    <span>2. Production & Assigned Tailoring Orders (کام کا ریکارڈ)</span>
                    <span class="font-normal text-slate-500">{{ $orders->count() }} orders</span>
                </h3>

                <table class="w-full text-xs text-left">
                    <thead class="border-b border-slate-200 text-slate-500 uppercase tracking-wider font-semibold text-[10px]">
                        <tr>
                            <th class="py-2">Order #</th>
                            <th class="py-2">Role</th>
                            <th class="py-2">Customer & Service</th>
                            <th class="py-2 text-center">Qty</th>
                            <th class="py-2">Date</th>
                            <th class="py-2 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($orders as $ord)
                            <tr>
                                <td class="py-2 font-bold font-mono">#{{ str_pad($ord->id, 4, '0', STR_PAD_LEFT) }}</td>
                                <td class="py-2">
                                    @if ($ord->cutting_employee_id == $employee->id) Cutting Master @endif
                                    @if ($ord->stitching_employee_id == $employee->id) Stitching Master @endif
                                    @if ($ord->assigned_employee_id == $employee->id && $ord->cutting_employee_id != $employee->id && $ord->stitching_employee_id != $employee->id) Assigned @endif
                                </td>
                                <td class="py-2">
                                    <span class="font-semibold">{{ $ord->customer?->name ?? 'Walk-in' }}</span>
                                    <span class="text-slate-400 block text-[11px]">{{ $ord->service?->name ?? 'Order' }}</span>
                                </td>
                                <td class="py-2 text-center font-bold">{{ $ord->quantity }}</td>
                                <td class="py-2 text-slate-600">{{ $ord->order_date ? \Carbon\Carbon::parse($ord->order_date)->format('d M Y') : '—' }}</td>
                                <td class="py-2 text-center uppercase font-bold text-[9px]">{{ $ord->status }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <!-- Attendance Summary -->
        <div class="mb-10 bg-slate-50 p-4 rounded-xl border border-slate-200 text-xs">
            <h4 class="font-bold text-slate-900 uppercase text-[10px] tracking-wider mb-2">Attendance Summary for Period:</h4>
            <div class="flex flex-wrap items-center gap-6">
                <span>Present: <strong class="text-emerald-700">{{ $stats['present_days'] }} days</strong></span>
                <span>Absent: <strong class="text-rose-700">{{ $stats['absent_days'] }} days</strong></span>
                <span>Late: <strong class="text-amber-700">{{ $stats['late_days'] }} days</strong></span>
                <span>Leave: <strong class="text-blue-700">{{ $stats['leave_days'] }} days</strong></span>
                <span>Half Days: <strong class="text-indigo-700">{{ $stats['half_days'] }} days</strong></span>
            </div>
        </div>

        <!-- Signatures Section -->
        <div class="mt-16 pt-8 border-t border-slate-300 grid grid-cols-3 gap-6 text-center text-xs">
            <div>
                <div class="border-b border-slate-400 pb-1 mb-2"></div>
                <p class="font-bold text-slate-800">Prepared By</p>
                <p class="text-[10px] text-slate-400">Accountant / Cashier</p>
            </div>
            <div>
                <div class="border-b border-slate-400 pb-1 mb-2"></div>
                <p class="font-bold text-slate-800">Employee Signature</p>
                <p class="text-[10px] text-slate-400">ملازم کے دستخط</p>
            </div>
            <div>
                <div class="border-b border-slate-400 pb-1 mb-2"></div>
                <p class="font-bold text-slate-800">Authorized Signature</p>
                <p class="text-[10px] text-slate-400">Shop Owner / Manager</p>
            </div>
        </div>

    </div>

</body>
</html>
