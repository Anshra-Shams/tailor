@props(['currentStep' => 1])

@php
    $steps = [
        1 => [
            'number' => 1,
            'title' => 'Select Orders',
            'desc' => 'Step 1: Choose orders',
        ],
        2 => [
            'number' => 2,
            'title' => 'Assign Cutting',
            'desc' => 'Step 2: Cutting Tailors',
        ],
        3 => [
            'number' => 3,
            'title' => 'Assign Stitching',
            'desc' => 'Step 3: Stitching Tailors',
        ],
    ];
@endphp

<div class="flex items-start justify-center w-full mx-auto pt-2">
    @foreach ($steps as $stepNum => $step)
        @php
            $isCompleted = $stepNum < $currentStep;
            $isCurrent = $stepNum === $currentStep;
            $isUpcoming = $stepNum > $currentStep;
            $isLast = $stepNum === count($steps);
        @endphp

        <div class="flex-1 flex flex-col items-center text-center relative group">
            {{-- Text on TOP --}}
            <div class="mb-3 flex flex-col justify-end">
                <span class="text-sm sm:text-base font-bold tracking-tight whitespace-nowrap {{ $isCompleted ? 'text-emerald-700' : ($isCurrent ? 'text-indigo-700' : 'text-slate-600') }}">
                    {{ $step['title'] }}
                </span>
            </div>

            {{-- Circle & Connecting Line --}}
            <div class="flex items-center justify-center relative w-full">
                {{-- Connector Line (Except for last step) --}}
                @if (!$isLast)
                    @php
                        $lineCompleted = $currentStep > $stepNum;
                    @endphp
                    {{-- absolute line starting from the center of this step, extending 100% to the center of next step. Circles have opaque backgrounds and z-10 to cover the line underneath. --}}
                    <div class="absolute top-1/2 -translate-y-1/2 left-1/2 w-full h-1 rounded-full {{ $lineCompleted ? 'bg-emerald-500' : 'bg-slate-200' }} transition-colors duration-300">
                    </div>
                @endif

                {{-- Circle --}}
                <div class="relative z-10 flex items-center justify-center shrink-0 w-10 h-10 sm:w-12 sm:h-12 rounded-full {{ $isCompleted ? 'bg-emerald-600 text-white shadow-sm ring-4 ring-emerald-100/80' : ($isCurrent ? 'bg-indigo-600 text-white shadow-md ring-4 ring-indigo-100' : 'border-2 border-slate-300 bg-white text-slate-400') }} transition-all">
                    @if ($isCompleted)
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                        </svg>
                    @else
                        <span class="font-bold text-base sm:text-lg">{{ $stepNum }}</span>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>
