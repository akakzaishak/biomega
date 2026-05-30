<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    @php($title = match ($page ?? 'home') {
        'login' => 'Login - TronSport Medicamon',
        'register' => 'Register Pharmacy - Bio Mega Pharme',
        default => 'TronSport Medicamon - Medical Search Portal',
    })
    @include('layouts.portal-assets')
</head>
<body class="bg-surface min-h-screen text-on-surface">
    @if ($page === 'home')
        <header class="bg-white/80 backdrop-blur-lg shadow-sm sticky top-0 z-50">
            <div class="max-w-7xl mx-auto flex justify-between items-center px-6 py-3">
                <div class="flex items-center gap-8">
                    <span class="text-xl font-extrabold tracking-tighter text-blue-800 headline">Bio Mega Pharme</span>
                    <nav class="hidden md:flex items-center gap-6">
                        <a class="text-blue-700 font-bold border-b-2 border-blue-600 px-1 py-1" href="{{ route('home') }}">Dashboard</a>
                        <a class="text-slate-500 font-medium hover:text-blue-600" href="{{ route('login') }}">Login</a>
                        <a class="text-slate-500 font-medium hover:text-blue-600" href="{{ route('register.pharmacy') }}">Register</a>
                    </nav>
                </div>
                <div class="flex items-center gap-4">
                    <a href="{{ route('login') }}" class="bg-primary text-white px-4 py-2 rounded-xl font-semibold text-sm">Login</a>
                </div>
            </div>
        </header>

        <main class="relative overflow-hidden">
            <div class="absolute inset-0 pointer-events-none">
                <div class="absolute -top-24 -right-24 h-80 w-80 rounded-full bg-blue-100/60 blur-3xl"></div>
                <div class="absolute top-40 -left-20 h-72 w-72 rounded-full bg-cyan-100/70 blur-3xl"></div>
            </div>
            <div class="relative max-w-7xl mx-auto px-6 py-16">
                <div class="grid lg:grid-cols-2 gap-10 items-center">
                    <div>
                        <p class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-bold uppercase tracking-wider mb-6">
                            <span class="material-symbols-outlined text-sm">verified</span> Medical Logistics Portal
                        </p>
                        <h1 class="text-5xl lg:text-7xl font-extrabold headline tracking-tight leading-tight text-slate-900">Bio Mega Pharme</h1>
                        <p class="mt-6 text-lg text-slate-600 max-w-xl">A single portal for pharmacies, stock, dispatch, and administration.</p>
                        <div class="mt-8 flex gap-3 flex-wrap">
                            <a href="{{ route('login') }}" class="bg-primary text-white px-5 py-3 rounded-xl font-bold">Enter portal</a>
                            <a href="{{ route('register.pharmacy') }}" class="bg-white border border-slate-200 px-5 py-3 rounded-xl font-bold text-slate-700">Register pharmacy</a>
                        </div>
                    </div>
                    <div class="bg-white rounded-3xl shadow-[0_24px_60px_-20px_rgba(0,94,164,0.2)] border border-slate-100 p-8">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="rounded-2xl bg-slate-50 p-5"><p class="text-xs font-bold uppercase tracking-wider text-slate-500">Pharmacies</p><p class="mt-3 text-3xl font-extrabold">58</p></div>
                            <div class="rounded-2xl bg-slate-50 p-5"><p class="text-xs font-bold uppercase tracking-wider text-slate-500">Coverage</p><p class="mt-3 text-3xl font-extrabold">58 wilayas</p></div>
                            <div class="rounded-2xl bg-slate-50 p-5"><p class="text-xs font-bold uppercase tracking-wider text-slate-500">Orders</p><p class="mt-3 text-3xl font-extrabold">Live</p></div>
                            <div class="rounded-2xl bg-slate-50 p-5"><p class="text-xs font-bold uppercase tracking-wider text-slate-500">Support</p><p class="mt-3 text-3xl font-extrabold">24/7</p></div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    @elseif ($page === 'login')
        <div class="min-h-screen flex items-center justify-center p-4 relative overflow-hidden">
            <div class="absolute inset-0 pointer-events-none">
                <div class="absolute -top-20 -right-16 h-72 w-72 rounded-full bg-blue-100/60 blur-3xl"></div>
                <div class="absolute bottom-0 left-0 h-80 w-80 rounded-full bg-cyan-100/70 blur-3xl"></div>
            </div>
            @include('portal.public.login')
        </div>
    @elseif ($page === 'register')
        <header class="bg-white/90 backdrop-blur-lg shadow-sm sticky top-0 z-50">
            <div class="max-w-7xl mx-auto flex items-center justify-between px-6 py-3">
                <div>
                    <span class="text-xl font-extrabold tracking-tighter text-blue-800 headline">Bio Mega Pharme</span>
                    <p class="text-xs text-slate-500">Register your pharmacy</p>
                </div>
                <a href="{{ route('home') }}" class="text-sm font-semibold text-primary">Home</a>
            </div>
        </header>

        <main class="py-12 px-4 relative overflow-hidden">
            <div class="absolute inset-0 pointer-events-none">
                <div class="absolute -top-24 right-10 h-72 w-72 rounded-full bg-blue-100/60 blur-3xl"></div>
                <div class="absolute bottom-0 left-10 h-72 w-72 rounded-full bg-cyan-100/70 blur-3xl"></div>
            </div>
            @include('portal.public.register')
        </main>
    @endif
</body>
</html>
