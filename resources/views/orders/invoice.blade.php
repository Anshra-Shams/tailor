<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }} - Meco</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f1f5f9;
            color: #0f172a;
        }

        .meco-blue {
            color: #0f4c81;
        }

        .bg-meco-blue {
            background-color: #0f4c81;
        }

        .border-meco-blue {
            border-color: #0f4c81;
        }

        .v-label-new {
            font-size: 8px;
            font-weight: 600;
            color: #1e3a8a;
            line-height: 1.1;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2px 1px;
            word-break: break-word;
            height: 100%;
        }

        .meas-table th,
        .meas-table td {
            border-right: 1px solid #cbd5e1;
            border-bottom: 1px solid #cbd5e1;
            padding: 3px 1px;
            text-align: center;
        }

        .meas-table th:last-child,
        .meas-table td:last-child {
            border-right: none;
        }

        .meas-part-r {
            border-right: 3px solid #0f4c81 !important;
        }

        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .no-print {
                display: none !important;
            }

            html, body {
                background: #fff !important;
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
                height: 100% !important;
                overflow: hidden !important;
            }

            .invoice-card {
                box-shadow: none !important;
                border: 1px solid #cbd5e1 !important;
                border-radius: 12px !important;
                padding: 12px !important;
                margin: 0 auto !important;
                max-width: 100% !important;
                width: 100% !important;
                box-sizing: border-box !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            .invoice-card > * + * {
                margin-top: 6px !important;
            }

            .grid-cols-12 {
                display: grid !important;
                grid-template-columns: repeat(12, minmax(0, 1fr)) !important;
            }

            .col-span-3 {
                grid-column: span 3 / span 3 !important;
            }

            .col-span-6 {
                grid-column: span 6 / span 6 !important;
            }

            .grid-cols-2 {
                display: grid !important;
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            }

            .grid-cols-4 {
                display: grid !important;
                grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
            }

            @page {
                size: landscape;
                margin: 4mm;
            }
        }
    </style>
</head>
<body class="min-h-screen py-6 px-4">

    {{-- Screen Only Actions Header --}}
    <div class="no-print max-w-5xl mx-auto mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
        <div class="flex items-center gap-3">
            <a href="{{ route('orders.index') }}"
               class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                Back to Orders
            </a>
            <h2 class="text-sm font-bold text-slate-800">
                Invoice #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
            </h2>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('orders.create') }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-sky-700 bg-sky-50 hover:bg-sky-100 border border-sky-200 rounded-xl transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                New Order
            </a>
            <button type="button" onclick="window.print()"
                    class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold text-white bg-[#0f4c81] hover:bg-blue-900 rounded-xl shadow-md transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231a1.125 1.125 0 01-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.656"/></svg>
                Print Invoice
            </button>
        </div>
    </div>

    {{-- Standalone Professional Invoice Card Container --}}
    <div class="invoice-card max-w-5xl mx-auto bg-white p-6 rounded-3xl shadow-xl border border-sky-100 relative overflow-hidden space-y-4">
        
        {{-- Decorative Top-Left Light Blue Accent Wave --}}
        <div class="absolute -top-12 -left-12 w-48 h-48 bg-gradient-to-br from-sky-400/20 via-sky-300/10 to-transparent rounded-full pointer-events-none blur-xl"></div>

        {{-- 1. TOP HEADER SECTION --}}
        <div class="grid grid-cols-12 gap-4 items-center relative z-10 pb-2">
            
            {{-- Logo --}}
            <div class="col-span-3 pr-2">
                <h1 class="text-4xl font-extrabold text-[#0f4c81] tracking-tight">Meco</h1>
                <div class="h-1 w-16 bg-[#0f4c81] rounded-full mt-1"></div>
            </div>

            {{-- Address & Contacts --}}
            <div class="col-span-6 space-y-1 text-xs text-slate-600 font-medium border-l border-sky-200 pl-6">
                <div class="flex items-start gap-2">
                    <svg class="w-4 h-4 text-[#0f4c81] shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                    <span>SHOP # 5 &amp; 6 RED CRESCENT HOSPITAL MARKET LATIFABAD # 6, HYDERABAD</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#0f4c81] shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-2.826-1.47-5.114-3.758-6.584-6.584l1.293-.97c.362-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/></svg>
                    <span>Tel: 0223-860680 - 0223862252</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#0f4c81] shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m-15.432-6.7a8.959 8.959 0 00-.284 2.253c0 .778.099 1.533.284 2.253"/></svg>
                    <span>Visit Us: www.Meco.Com.Pk</span>
                </div>
            </div>

            {{-- Invoice Number Card --}}
            <div class="col-span-3 border-l border-sky-200 pl-4">
                <div class="bg-sky-50 border border-sky-200 rounded-2xl p-3 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-[#0f4c81] text-white flex items-center justify-center shrink-0 shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                    </div>
                    <div>
                        <div class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">INVOICE</div>
                        <div class="text-xl font-black text-[#0f4c81] leading-tight">#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</div>
                        <div class="text-[11px] font-semibold text-slate-500">Date: {{ ($order->order_date ?? $order->created_at)?->format('d-m-Y') }}</div>
                    </div>
                </div>
            </div>

        </div>

        {{-- 2. CUSTOMER INFO & BOOKING INFO GRID --}}
        <div class="grid grid-cols-2 gap-4">
            
            {{-- Customer Information Box --}}
            <div class="border border-sky-200 bg-white rounded-2xl overflow-hidden shadow-2xs flex flex-col justify-between">
                <div>
                    <div class="bg-[#0f4c81] text-white px-3 py-2 flex items-center gap-2 font-bold text-xs uppercase tracking-wide">
                        <svg class="w-4 h-4 text-sky-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                        <span>Customer Information</span>
                    </div>
                    <div class="p-3 text-xs space-y-2">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-1.5">
                            <span class="text-slate-500 font-semibold w-36">Customer Category</span>
                            <span class="text-slate-400 font-bold mr-2">:</span>
                            <span class="font-extrabold text-[#0f4c81] uppercase flex-1">{{ $order->customer?->gender ? ucfirst($order->customer->gender) : 'Regular' }}</span>
                        </div>
                        <div class="flex items-center justify-between border-b border-slate-100 pb-1.5">
                            <span class="text-slate-500 font-semibold w-36">Customer ID</span>
                            <span class="text-slate-400 font-bold mr-2">:</span>
                            <span class="font-bold text-slate-800 flex-1">{{ str_pad($order->customer_id, 4, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="flex items-center justify-between border-b border-slate-100 pb-1.5">
                            <span class="text-slate-500 font-semibold w-36">Name</span>
                            <span class="text-slate-400 font-bold mr-2">:</span>
                            <span class="font-extrabold text-slate-900 text-sm flex-1">
                                {{ $order->member ? $order->member->name : $order->customer?->name }}
                                @if($order->member && $order->customer)
                                    <span class="text-xs font-normal text-slate-500 inline">({{ $order->customer->name }})</span>
                                @endif
                            </span>
                        </div>
                        <div class="flex items-start justify-between border-b border-slate-100 pb-1.5">
                            <span class="text-slate-500 font-semibold w-36">Address</span>
                            <span class="text-slate-400 font-bold mr-2">:</span>
                            <span class="font-medium text-slate-700 leading-snug flex-1">{{ $order->customer?->address ?? '—' }}</span>
                        </div>
                        <div class="flex items-center justify-between border-b border-slate-100 pb-1.5">
                            <span class="text-slate-500 font-semibold w-36">Contact</span>
                            <span class="text-slate-400 font-bold mr-2">:</span>
                            <span class="font-bold text-slate-800 flex-1">{{ $order->customer?->phone ?? '—' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 font-semibold w-36">E-Mail</span>
                            <span class="text-slate-400 font-bold mr-2">:</span>
                            <span class="font-medium text-slate-500 flex-1">—</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Booking Information Box --}}
            <div class="border border-sky-200 bg-white rounded-2xl overflow-hidden shadow-2xs flex flex-col justify-between">
                <div class="flex-1 flex flex-col justify-between">
                    <div>
                        <div class="bg-sky-50 border-b border-sky-100 text-[#0f4c81] px-3 py-2 flex items-center gap-2 font-extrabold text-xs uppercase tracking-wide">
                            <svg class="w-4 h-4 text-[#0f4c81]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                            <span>Booking Information</span>
                        </div>
                        <table class="w-full text-xs border-collapse text-left">
                            <thead>
                                <tr class="bg-sky-50/50 text-[#0f4c81] border-b border-sky-100 font-bold text-center">
                                    <th class="w-10 py-2 border-r border-sky-100">#</th>
                                    <th class="py-2 px-3 text-left border-r border-sky-100">Description</th>
                                    <th class="w-24 py-2 border-r border-sky-100">Rate</th>
                                    <th class="w-28 py-2">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @php $rowCount = 0; @endphp
                                @foreach($siblingOrders as $item)
                                    @php $rowCount++; @endphp
                                    <tr>
                                        <td class="text-center font-bold text-slate-700 py-2.5 border-r border-slate-100">{{ $rowCount }}</td>
                                        <td class="px-3 py-2.5 font-bold text-slate-800 border-r border-slate-100">
                                            {{ $item->service?->name ?? 'Stitching Service' }}
                                            @php
                                                $cleanNote = trim(preg_replace('/(Standard|Premium|Basic|Urgent)?\s*Stitching/i', '', $item->notes ?? ''));
                                                $cleanNote = trim(preg_replace('/^[—\-\s]+|[—\-\s]+$/u', '', $cleanNote));
                                            @endphp
                                            @if(!empty($cleanNote))
                                                <span class="text-[10px] font-normal text-slate-500 block mt-0.5">{{ $cleanNote }}</span>
                                            @endif
                                        </td>
                                        <td class="text-center font-bold text-slate-700 py-2.5 border-r border-slate-100">Rs. {{ number_format($item->price, 0) }}</td>
                                        <td class="text-center font-extrabold text-slate-900 py-2.5">Rs. {{ number_format($item->price * $item->quantity, 0) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Total Bar --}}
                    <div class="p-3 bg-sky-50/60 border-t border-sky-100 flex items-center justify-between">
                        <span class="font-extrabold text-sm text-[#0f4c81]">Total</span>
                        <div class="bg-[#0f4c81] text-white px-5 py-1.5 rounded-xl font-extrabold text-base shadow-xs">
                            Rs. {{ number_format($grandTotal, 0) }}
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- 3. DATES & TAILOR METADATA STRIP --}}
        <div class="border border-sky-200 bg-sky-50/40 rounded-2xl p-3 grid grid-cols-4 divide-x divide-sky-200 text-xs shadow-2xs">
            <div class="px-3 flex flex-col justify-center">
                <span class="text-sky-700 font-bold text-[11px]">Booking Date</span>
                <span class="font-extrabold text-slate-800 text-sm mt-0.5">{{ ($order->order_date ?? $order->created_at)?->format('d-m-Y') }}</span>
            </div>
            <div class="px-3 flex flex-col justify-center">
                <span class="text-sky-700 font-bold text-[11px]">Delivery Dt.</span>
                <span class="font-extrabold text-blue-900 text-sm mt-0.5">{{ $order->due_date?->format('d-m-Y') }}</span>
            </div>
            <div class="px-3 flex flex-col justify-center">
                <span class="text-sky-700 font-bold text-[11px]">Cutting By</span>
                <span class="font-semibold text-slate-400 mt-0.5">—</span>
            </div>
            <div class="px-3 flex flex-col justify-center">
                <span class="text-sky-700 font-bold text-[11px]">Stitching By</span>
                <span class="font-semibold text-slate-400 mt-0.5">—</span>
            </div>
        </div>

        {{-- 4. BODY MEASUREMENT CARD --}}
        <div class="border border-sky-200 bg-white rounded-2xl overflow-hidden shadow-2xs">
            
            {{-- Title Banner --}}
            <div class="bg-[#0f4c81] text-white px-3 py-2 flex items-center gap-2 font-bold text-xs uppercase tracking-wider">
                <svg class="w-4 h-4 text-sky-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0 3.75v4.5m0 3.75h16.5m-16.5 0v-4.5m0-3.75v-4.5m0-3.75h16.5m-16.5 0h16.5"/></svg>
                <span>Body Measurement</span>
            </div>

            {{-- Measurement Table --}}
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-center text-[10px] leading-tight font-sans table-fixed meas-table">
                    <thead>
                        
                        {{-- Upper / Lower Split Header --}}
                        <tr class="bg-sky-50 font-extrabold text-xs text-[#0f4c81]">
                            <th rowspan="2" class="w-24 md:w-28 text-left px-2 bg-sky-50 text-[#0f4c81] font-extrabold text-[10px] border-r border-b border-sky-200">Service</th>
                            <th colspan="14" class="meas-part-r py-1.5 bg-sky-50">Upper</th>
                            <th colspan="12" class="py-1.5 bg-sky-50">Lower</th>
                        </tr>

                        {{-- Horizontal Labels Row --}}
                        <tr class="h-10 bg-white">
                            {{-- Upper (1 to 14) --}}
                            <th><span class="v-label-new">Point</span></th>
                            <th><span class="v-label-new">Grounding</span></th>
                            <th><span class="v-label-new">Length Shoulder to Bottom</span></th>
                            <th><span class="v-label-new">Shoulder</span></th>
                            <th><span class="v-label-new">Chest</span></th>
                            <th><span class="v-label-new">Waist</span></th>
                            <th><span class="v-label-new">Hip</span></th>
                            <th><span class="v-label-new">In and</span></th>
                            <th><span class="v-label-new">Flair</span></th>
                            <th><span class="v-label-new">Choke</span></th>

                            <th><span class="v-label-new">Sleeve</span></th>
                            <th><span class="v-label-new">Upper Arm Circumference</span></th>
                            <th><span class="v-label-new">Wrist</span></th>
                            
                            {{-- Dark Partition after Neck (14) --}}
                            <th class="meas-part-r"><span class="v-label-new">Neck</span></th>

                            {{-- Lower (15 to 26) --}}
                            <th><span class="v-label-new">Length</span></th>
                            <th><span class="v-label-new">Wrist</span></th>
                            <th><span class="v-label-new">Half Belt Elastic</span></th>
                            <th><span class="v-label-new">Full Elastic</span></th>
                            <th><span class="v-label-new">Hip</span></th>
                            <th><span class="v-label-new">Belt</span></th>
                            <th><span class="v-label-new">Fly</span></th>
                            <th><span class="v-label-new">Inside</span></th>
                            <th><span class="v-label-new">Thigh</span></th>
                            <th><span class="v-label-new">Knee</span></th>
                            <th><span class="v-label-new">Bottom</span></th>
                            <th><span class="v-label-new">Ankle Circumference</span></th>
                        </tr>

                        {{-- Column Numbers Row --}}
                        <tr class="bg-sky-50/60 font-bold text-[9px] text-slate-500">
                            <td class="bg-sky-50/60 font-bold text-[9px] text-slate-500 border-r border-sky-200">#</td>
                            <td>1</td>
                            <td>2</td>
                            <td>3</td>
                            <td>4</td>
                            <td>5</td>
                            <td>6</td>
                            <td>7</td>
                            <td>8</td>
                            <td>9</td>
                            <td>10</td>
                            <td>11</td>
                            <td>12</td>
                            <td>13</td>
                            <td class="meas-part-r">14</td>

                            <td>15</td>
                            <td>16</td>
                            <td>17</td>
                            <td>18</td>
                            <td>19</td>
                            <td>20</td>
                            <td>21</td>
                            <td>22</td>
                            <td>23</td>
                            <td>24</td>
                            <td>25</td>
                            <td>26</td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($servicesMeasurements as $sm)
                            {{-- Measurement Values Row (1 Compact Row per Service) --}}
                            <tr class="font-extrabold text-xs h-8 bg-white text-slate-900 border-b border-slate-100 hover:bg-sky-50/20">
                                <td class="text-left px-2 py-1 font-bold text-[#0f4c81] text-[10px] border-r border-sky-200 bg-sky-50/40 whitespace-nowrap">
                                    <div class="flex items-center gap-1.5 truncate">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#0f4c81] shrink-0"></span>
                                        <span class="truncate font-extrabold" title="{{ $sm['service_name'] }}">{{ $sm['service_name'] }}</span>
                                    </div>
                                </td>
                                {{-- Upper Values --}}
                                <td>{{ $sm['upper'][1] }}</td>
                                <td>{{ $sm['upper'][2] }}</td>
                                <td class="text-sm font-black text-[#0f4c81]">{{ $sm['upper'][3] }}</td>
                                <td>{{ $sm['upper'][4] }}</td>
                                <td>{{ $sm['upper'][5] }}</td>
                                <td>{{ $sm['upper'][6] }}</td>
                                <td>{{ $sm['upper'][7] }}</td>
                                <td>{{ $sm['upper'][8] }}</td>
                                <td>{{ $sm['upper'][9] }}</td>
                                <td>{{ $sm['upper'][10] }}</td>
                                <td>{{ is_array($sm['upper'][11] ?? null) ? implode(' / ', array_filter($sm['upper'][11])) : ($sm['upper'][11] ?? '') }}</td>
                                <td>{{ $sm['upper'][12] }}</td>
                                <td>{{ $sm['upper'][13] }}</td>
                                <td class="meas-part-r">{{ $sm['upper'][14] }}</td>

                                {{-- Lower Values --}}
                                <td class="text-sm font-black text-[#0f4c81]">{{ $sm['lower'][15] }}</td>
                                <td>{{ $sm['lower'][16] }}</td>
                                <td>{{ $sm['lower'][17] }}</td>
                                <td>{{ $sm['lower'][18] }}</td>
                                <td>{{ $sm['lower'][19] }}</td>
                                <td>{{ $sm['lower'][20] }}</td>
                                <td>{{ $sm['lower'][21] }}</td>
                                <td>{{ $sm['lower'][22] }}</td>
                                <td>{{ $sm['lower'][23] }}</td>
                                <td>{{ $sm['lower'][24] }}</td>
                                <td>{{ $sm['lower'][25] }}</td>
                                <td>{{ $sm['lower'][26] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>

        {{-- 5. NOTES CARD --}}
        <div class="border border-sky-200 bg-sky-50/40 rounded-2xl p-3.5 space-y-1.5">
            <div class="flex items-center gap-2 font-extrabold text-xs text-[#0f4c81]">
                <svg class="w-4 h-4 text-[#0f4c81]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                <span>Notes</span>
            </div>
            <ul class="text-xs text-slate-700 font-semibold space-y-1 pl-6 list-disc">
                <li>Due Electricity failure delivery can be delay</li>
                <li>After one month of Deliver date we wouldn't accept any claim</li>
            </ul>
        </div>

        {{-- 6. SOLID BLUE BOTTOM BANNER --}}
        <div class="bg-[#0f4c81] text-white p-3 rounded-2xl text-center text-xs font-bold tracking-wide shadow-sm">
            Powered by : Prowave Technologies &nbsp;|&nbsp; Contact : 0317-3836223
        </div>

        {{-- Decorative Bottom-Left Accent Wave --}}
        <div class="absolute -bottom-12 -left-12 w-48 h-48 bg-gradient-to-tr from-sky-500/20 via-sky-400/10 to-transparent rounded-full pointer-events-none blur-xl"></div>

    </div>

</body>
</html>
