<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    @php($title = match ($page ?? '') {
        'commercial' => 'TronSport Medicamon | Commercial Dashboard',
        'delivery-manager' => 'TronSport Medicamon | Delivery Manager',
        'delivery-person' => 'TronSport Medicamon | Delivery Person',
        'pharmacy' => 'TronSport Medicamon | Pharmacy Dashboard',
        'stock' => 'TronSport Medicamon | Stock Dashboard',
        default => 'TronSport Medicamon | Portal',
    })
    @include('layouts.portal-assets')
</head>
<body class="bg-surface text-on-surface font-body">
    <header class="bg-white/80 backdrop-blur-lg shadow-sm sticky top-0 z-50 flex justify-between items-center px-6 py-3">
        <div class="flex items-center gap-8">
            <span class="text-xl font-extrabold tracking-tighter text-blue-800 headline">TronSport Medicamon</span>
            <nav class="hidden md:flex items-center gap-6">
                @if ($page === 'commercial')
                    <a class="text-blue-700 font-bold border-b-2 border-blue-600 px-1 py-1" href="{{ route('commercial.dashboard') }}">Dashboard</a>
                @elseif ($page === 'delivery-manager')
                    <a class="text-blue-700 font-bold border-b-2 border-blue-600 px-1 py-1" href="{{ route('delivery-manager.dashboard') }}">Dashboard</a>
                @elseif ($page === 'delivery-person')
                    <a class="text-blue-700 font-bold border-b-2 border-blue-600 px-1 py-1" href="{{ route('delivery-person.dashboard') }}">Dashboard</a>
                @elseif ($page === 'pharmacy')
                    <a class="text-blue-700 font-bold border-b-2 border-blue-600 px-1 py-1" href="{{ route('pharmacy.dashboard') }}">Dashboard</a>
                @elseif ($page === 'stock')
                    <a class="text-blue-700 font-bold border-b-2 border-blue-600 px-1 py-1" href="{{ route('stock.dashboard') }}">Dashboard</a>
                @endif
                <a class="text-slate-500 font-medium hover:text-blue-600" href="{{ route('logout') }}">Logout</a>
            </nav>
        </div>
        <a href="{{ route('logout') }}" class="p-2 hover:bg-slate-50 rounded-full text-slate-600"><span class="material-symbols-outlined">logout</span></a>
    </header>

    <main class="max-w-7xl mx-auto px-4 py-8 space-y-8">
        @if (!empty(session('success')))
            <div class="bg-green-50 text-green-800 border border-green-200 px-5 py-3 rounded-2xl text-sm font-semibold">{{ session('success') }}</div>
        @endif
        @if (!empty(session('error')))
            <div class="bg-error-container text-on-error-container border border-error/20 px-5 py-3 rounded-2xl text-sm font-semibold">{{ session('error') }}</div>
        @endif

        @if ($page === 'commercial')
            @include('portal.role.commercial')
        @elseif ($page === 'delivery-manager')
            @include('portal.role.delivery-manager')
        @elseif ($page === 'delivery-person')
            @include('portal.role.delivery-person')
        @elseif ($page === 'pharmacy')
            @include('portal.role.pharmacy')
        @elseif ($page === 'stock')
            @include('portal.role.stock')
        @endif
    </main>
</body>
</html>
 