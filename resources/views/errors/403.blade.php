<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 - Access Denied | {{ config('app.name', 'Prowave') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen flex items-center justify-center p-6">
    <div class="max-w-md w-full text-center">
        <!-- Shield Icon with gradient aura -->
        <div class="relative inline-flex items-center justify-center mb-6">
            <div class="absolute inset-0 bg-indigo-500/20 blur-2xl rounded-full"></div>
            <div class="relative w-24 h-24 rounded-3xl bg-slate-800/80 border border-slate-700/60 shadow-2xl flex items-center justify-center">
                <svg class="w-12 h-12 text-indigo-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0-10.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.75c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.75h-.152c-3.196 0-6.1-1.249-8.25-3.286zm0 13.036h.008v.008H12v-.008z" />
                </svg>
            </div>
        </div>

        <!-- Error Badge -->
        <div class="inline-block px-3 py-1 bg-red-500/10 border border-red-500/30 text-red-400 text-xs font-semibold uppercase tracking-wider rounded-full mb-3">
            Error 403 · Access Denied
        </div>

        <h1 class="text-3xl font-extrabold text-white tracking-tight mb-2">
            Access Restricted
        </h1>
        <p class="text-slate-400 text-sm leading-relaxed mb-8">
            Aap ke paas is page ko dekhne ya operate karne ki permission nahi hai. Agar aapko lagta hai ke yeh ghalti hai, toh apne administrator se rabta karein.
        </p>

        <div class="flex items-center justify-center gap-3">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 text-white font-semibold text-sm shadow-lg shadow-indigo-500/25 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                </svg>
                Dashboard par wapis jayein
            </a>
            <button onclick="window.history.back()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-sm border border-slate-700 transition-all">
                Peeche jayein
            </button>
        </div>
    </div>
</body>
</html>
