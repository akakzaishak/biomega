@extends('layouts.app')

@section('title', 'Biomega Pharm | Pharmacy Dashboard')

@section('content')
    @php
        $role = $user->role;
        $isAdmin = $role === \App\Models\User::ROLE_ADMIN;
        $isPharmacy = $role === \App\Models\User::ROLE_PHARMACY;
    @endphp

    <section class="space-y-6">
        @if (session('status'))
            <div class="rounded-2xl border border-emerald-300/20 bg-emerald-300/10 px-4 py-3 text-sm text-emerald-50">{{ session('status') }}</div>
        @endif

        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div class="max-w-3xl space-y-3">
                <p class="section-label">{{ str_replace('_', ' ', $role) }} workspace</p>
                <h1 class="section-title">Welcome, {{ $user->name }}.</h1>
                <p class="section-copy">Overview of your orders.</p>
            </div>
        </div>

        <div class="flex gap-4 overflow-x-auto pb-1">
            <article class="metric-card min-w-[12rem] flex-1">
                <p class="text-sm text-slate-400">Total orders</p>
                <p class="metric-value">{{ $overview['totalOrders'] ?? $pharmacyOverview['totalOrders'] ?? 0 }}</p>
                <p class="metric-label">All orders in your scope</p>
            </article>
        </div>

        <div class="module-card">
            <p class="section-label">Orders by status</p>
            <h2 class="mt-2 font-serif text-2xl text-slate-900">Current orders by status</h2>
            <div class="mt-4 grid gap-3 sm:grid-cols-2 md:grid-cols-3">
                @php
                    $statuses = ['pending_validation', 'preparing', 'ready_for_delivery', 'in_transit', 'delivered', 'cancelled'];
                @endphp
                @foreach ($statuses as $s)
                    <a href="{{ route('orders', ['status' => $s]) }}" class="block rounded-2xl border border-gray-200 bg-white p-4 hover:bg-slate-50">
                        <p class="text-xs uppercase tracking-[0.24em] text-slate-500">{{ $s }}</p>
                        <p class="mt-2 text-slate-900">{{ $ordersByStatus[$s] ?? 0 }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endsection
