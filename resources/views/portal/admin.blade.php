<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        @php
            $title = match ($page ?? 'dashboard') {
                'tracking' => 'Live Tracking | TronSport Medicamon',
                default => 'TronSport Medicamon | Admin',
            };
        @endphp
        @include('layouts.portal-assets')
</head>
<body class="bg-surface text-on-surface font-body">
    @if ($page === 'tracking')
        @include('portal.admin.tracking')
    @else
    <header class="bg-white/80 backdrop-blur-lg shadow-sm shadow-blue-500/5 sticky top-0 z-50 flex justify-between items-center px-6 py-3 w-full">
        <div class="flex items-center gap-8">
            <span class="text-xl font-extrabold tracking-tighter text-blue-800 headline">TronSport Medicamon</span>
            <nav class="hidden md:flex items-center gap-6">
                <a class="{{ $page === 'dashboard' ? 'text-blue-700 font-bold border-b-2 border-blue-600 px-1 py-1' : 'text-slate-500 font-medium hover:text-blue-600' }}" href="{{ route('admin.dashboard') }}">Dashboard</a>
                <a class="{{ $page === 'orders' ? 'text-blue-700 font-bold border-b-2 border-blue-600 px-1 py-1' : 'text-slate-500 font-medium hover:text-blue-600' }}" href="{{ route('admin.orders') }}">Orders</a>
                <a class="text-slate-500 font-medium hover:text-blue-600" href="{{ route('admin.inventory') }}">Inventory</a>
                <a class="text-slate-500 font-medium hover:text-blue-600" href="{{ route('admin.reports') }}">Reports</a>
            </nav>
        </div>
        <div class="flex items-center gap-4">
            <div class="relative hidden sm:block">
                <input class="bg-surface-container-low border-none rounded-full px-4 py-2 text-sm w-64 focus:ring-2 focus:ring-primary/20" placeholder="Search tracking ID..." type="text" />
                <span class="material-symbols-outlined absolute right-3 top-2 text-on-surface-variant text-lg">search</span>
            </div>
            <button class="p-2 hover:bg-slate-50 transition-colors rounded-full active:scale-95 flex items-center gap-1 text-sm font-semibold text-slate-600" title="Notifications" type="button">
                <span class="material-symbols-outlined text-slate-600">notifications</span>
            </button>
            <a href="{{ route('logout') }}" class="p-2 hover:bg-slate-50 transition-colors rounded-full active:scale-95 flex items-center gap-1 text-sm font-semibold text-slate-600" title="Logout">
                <span class="material-symbols-outlined text-slate-600">logout</span>
            </a>
        </div>
    </header>

    <div class="flex min-h-screen">
        <aside class="bg-slate-50 h-screen w-64 border-r border-slate-200 flex flex-col gap-2 p-4 fixed left-0 top-[60px] hidden lg:flex">
            <div class="mb-4 px-2">
                <h3 class="font-headline font-bold text-blue-900">Admin Portal</h3>
                <p class="text-xs text-on-surface-variant">{{ $userName }} • Operational</p>
            </div>
            <nav class="flex-1 flex flex-col gap-1">
                <a class="{{ $page === 'dashboard' ? 'bg-blue-50 text-blue-700 rounded-lg font-bold' : 'text-slate-600 hover:bg-slate-100' }} flex items-center gap-3 px-3 py-2.5 rounded-lg transition-transform hover:translate-x-1" href="{{ route('admin.dashboard') }}"><span class="material-symbols-outlined">dashboard</span><span class="text-sm">Dashboard</span></a>
                <a class="{{ $page === 'pharmacies' ? 'bg-blue-50 text-blue-700 rounded-lg font-bold' : 'text-slate-600 hover:bg-slate-100' }} flex items-center gap-3 px-3 py-2.5 rounded-lg transition-transform hover:translate-x-1" href="{{ route('admin.pharmacies') }}"><span class="material-symbols-outlined">local_pharmacy</span><span class="text-sm">Pharmacies</span></a>
                <a class="{{ $page === 'employees' ? 'bg-blue-50 text-blue-700 rounded-lg font-bold' : 'text-slate-600 hover:bg-slate-100' }} flex items-center gap-3 px-3 py-2.5 rounded-lg transition-transform hover:translate-x-1" href="{{ route('admin.employees') }}"><span class="material-symbols-outlined">badge</span><span class="text-sm">Employees</span></a>
                <a class="{{ $page === 'orders' ? 'bg-blue-50 text-blue-700 rounded-lg font-bold' : 'text-slate-600 hover:bg-slate-100' }} flex items-center gap-3 px-3 py-2.5 rounded-lg transition-transform hover:translate-x-1" href="{{ route('admin.orders') }}"><span class="material-symbols-outlined">package_2</span><span class="text-sm">Orders</span></a>
                <a class="{{ $page === 'payments' ? 'bg-blue-50 text-blue-700 rounded-lg font-bold' : 'text-slate-600 hover:bg-slate-100' }} flex items-center gap-3 px-3 py-2.5 rounded-lg transition-transform hover:translate-x-1" href="{{ route('admin.payments') }}"><span class="material-symbols-outlined">payments</span><span class="text-sm">Payments</span></a>
                <a class="{{ $page === 'tracking' ? 'bg-blue-50 text-blue-700 rounded-lg font-bold' : 'text-slate-600 hover:bg-slate-100' }} flex items-center gap-3 px-3 py-2.5 rounded-lg transition-transform hover:translate-x-1" href="{{ route('admin.tracking') }}"><span class="material-symbols-outlined">local_shipping</span><span class="text-sm">Tracking</span></a>
                <a class="{{ $page === 'settings' ? 'bg-blue-50 text-blue-700 rounded-lg font-bold' : 'text-slate-600 hover:bg-slate-100' }} flex items-center gap-3 px-3 py-2.5 rounded-lg transition-transform hover:translate-x-1" href="{{ route('admin.settings') }}"><span class="material-symbols-outlined">settings</span><span class="text-sm">Settings</span></a>
                <a class="text-red-500 hover:bg-red-50 flex items-center gap-3 px-3 py-2.5 rounded-lg transition-transform hover:translate-x-1 mt-2" href="{{ route('logout') }}"><span class="material-symbols-outlined">logout</span><span class="text-sm font-bold">Logout</span></a>
            </nav>
            <div class="mt-auto pt-4 border-t border-slate-200">
                <a href="{{ route('admin.orders') }}?action=new_emergency" class="w-full bg-gradient-to-r from-primary to-primary-container text-white py-3 px-4 rounded-xl font-bold text-sm shadow-md flex items-center justify-center gap-2 active:scale-95 transition-transform">
                    <span class="material-symbols-outlined text-lg">add_circle</span>New Emergency Order
                </a>
            </div>
        </aside>

        <main class="flex-1 min-w-0 p-4 lg:p-8 lg:ml-64 space-y-8 bg-surface">
            @if (session('success'))
                <div class="rounded-2xl border border-green-200 bg-green-50 text-green-800 px-4 py-3 text-sm font-medium">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="rounded-2xl border border-red-200 bg-red-50 text-red-800 px-4 py-3 text-sm font-medium">{{ session('error') }}</div>
            @endif

            @if ($page === 'dashboard')
                @include('portal.admin.dashboard')
            @elseif ($page === 'orders')
                @include('portal.admin.orders')
            @elseif ($page === 'pharmacies')
                @include('portal.admin.pharmacies')
            @elseif ($page === 'employees')
                @include('portal.admin.employees')
            @elseif ($page === 'tracking')
                @include('portal.admin.tracking')
            @elseif ($page === 'settings')
                @include('portal.admin.settings')
            @elseif ($page === 'inventory')
                @include('portal.admin.inventory')
            @elseif ($page === 'reports')
                @include('portal.admin.reports')
            @elseif ($page === 'payments')
                @include('portal.admin.payments')
            @endif
        </main>
    </div>
    @stack('scripts')
    @endif
</body>
</html>
