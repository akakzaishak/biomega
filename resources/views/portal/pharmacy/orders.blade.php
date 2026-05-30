@extends('layouts.app')

@section('title', 'Biomega Pharm | Pharmacy Orders')

@section('content')
    @php
        $role = auth()->user()->role;
        $isPerson = $role === \App\Models\User::ROLE_PERSON;
        $isPharmacy = $role === \App\Models\User::ROLE_PHARMACY;
        $isAdmin = $role === \App\Models\User::ROLE_ADMIN;
        $canManage = in_array($role, [\App\Models\User::ROLE_ADMIN, \App\Models\User::ROLE_MANAGER_DELIVERY, \App\Models\User::ROLE_PHARMACY, \App\Models\User::ROLE_DELIVERY], true);
        $statusOptions = ['pending_validation', 'preparing', 'ready_for_delivery', 'in_transit', 'delivered', 'settled', 'cancelled'];
        $currentStatus = request()->string('status')->toString();
        $showOrderForm = $errors->any();
    @endphp

    <section class="space-y-8">
        @if (session('status'))
            <div class="rounded-2xl border border-emerald-300/20 bg-emerald-300/10 px-4 py-3 text-sm text-emerald-50">
                {{ session('status') }}
            </div>
        @endif

        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-3xl space-y-3">
                <p class="section-label">Orders workspace</p>
                <h1 class="section-title text-slate-900">Create, validate, assign, and settle orders.</h1>
                <p class="section-copy text-slate-600">This page is where each role interacts with the same order data through different controls.</p>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" data-order-toggle class="cursor-pointer rounded-full bg-cyan-300 px-5 py-3 text-sm font-semibold text-slate-950 {{ $showOrderForm ? 'hidden' : '' }}">
                    Create order
                </button>
                <span class="pill">{{ $orders->count() }} record(s)</span>
            </div>
        </div>

        @if ($isPerson || $isPharmacy)
            <div class="space-y-8" data-order-wrapper>

                <!-- CREATE ORDER FORM -->
                <form id="create-order" method="POST" action="{{ route('orders.store') }}"
                      class="fixed inset-0 z-50 {{ $showOrderForm ? 'flex' : 'hidden' }} items-center justify-center bg-black/30 p-4"
                      data-order-form>
                    <button type="button" class="absolute inset-0 cursor-default"></button>

                    <div class="relative z-10 max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded-[2rem] border border-gray-200 bg-white p-6 text-slate-900">
                        @csrf

                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="section-label">Pharmacy</p>
                                <h2 class="mt-2 font-serif text-2xl text-slate-900">Create an order</h2>
                                <p class="section-copy mt-2 text-slate-600">Fill in the order details below.</p>
                            </div>

                            <button type="button" data-order-close
                                    class="absolute right-4 top-4 grid h-9 w-9 place-items-center rounded-full border border-red-500/40 bg-red-500/10 text-red-200">
                                X
                            </button>
                        </div>

                        <div class="mt-5 space-y-4">

                            <datalist id="medicine-suggestions">
                                @foreach ($medicineSuggestions as $suggestion)
                                    <option value="{{ $suggestion }}"></option>
                                @endforeach
                            </datalist>

                            <div class="space-y-3">
                                <div>
                                    <p class="text-xs uppercase tracking-[0.24em] text-slate-400">Products</p>
                                    <p class="mt-1 text-sm text-slate-300">Add one product then more.</p>
                                </div>

                                <div class="space-y-3">
                                    <div data-product-list class="space-y-3">
                                        <div data-product-row class="grid gap-3 md:grid-cols-[1fr_9rem]">
                                            <input list="medicine-suggestions" name="items[0][medicine_name]"
                                                   class="rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm text-slate-900"
                                                   placeholder="Product name">

                                            <input type="number" min="1" name="items[0][quantity]" value="1"
                                                   class="rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm text-slate-900"
                                                   placeholder="Qty">
                                        </div>
                                    </div>

                                    <template data-product-template>
                                        <div data-product-row class="grid gap-3 md:grid-cols-[1fr_9rem]">
                                            <input list="medicine-suggestions" name="items[__INDEX__][medicine_name]"
                                                   class="rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm text-slate-900"
                                                   placeholder="Product name">

                                            <input type="number" min="1" name="items[__INDEX__][quantity]" value="1"
                                                   class="rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm text-slate-900"
                                                   placeholder="Qty">
                                        </div>
                                    </template>

                                    <div class="mt-2">
                                        <button type="button" data-product-add
                                                class="rounded-full border px-4 py-2 text-sm font-semibold">
                                            Add product
                                        </button>
                                    </div>

                                    @error('items')
                                        <p class="text-xs text-red-400 mt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <input type="hidden" name="payment_method" value="cash">

                            <textarea name="historique" rows="4"
                                      class="w-full rounded-3xl border border-gray-200 bg-white px-4 py-3 text-sm text-slate-900"
                                      placeholder="Order notes">{{ old('historique') }}</textarea>

                            <label class="flex items-center gap-2 text-sm text-slate-700">
                                <input type="checkbox" name="is_urgent" value="1" class="rounded">
                                Urgent
                            </label>

                            <button class="rounded-full bg-cyan-300 px-5 py-3 text-sm font-semibold text-slate-950">
                                Create order
                            </button>

                        </div>
                    </div>
                </form>

                <!-- ORDERS LIST -->
                <div class="module-card w-full">
                    <p class="section-label">Your orders</p>
                    <h2 class="mt-2 font-serif text-2xl text-slate-900">Recent submissions</h2>

                    <div class="mt-4 flex flex-wrap gap-2">
                        <a href="{{ route('orders', request()->except('status')) }}"
                           class="{{ $currentStatus === '' ? 'rounded-full bg-cyan-300 px-4 py-2 text-sm font-semibold text-slate-950' : 'rounded-full border px-4 py-2 text-sm font-semibold' }}">
                            All
                        </a>

                        @foreach ($statusOptions as $statusOption)
                            <a href="{{ route('orders', array_merge(request()->except('status'), ['status' => $statusOption])) }}"
                               class="{{ $currentStatus === $statusOption ? 'rounded-full bg-cyan-300 px-4 py-2 text-sm font-semibold text-slate-950' : 'rounded-full border px-4 py-2 text-sm font-semibold' }}">
                                {{ str_replace('_', ' ', $statusOption) }}
                            </a>
                        @endforeach
                    </div>

                    <div class="mt-6 space-y-4">

                        @foreach ($orders as $order)
                            <article class="rounded-3xl border border-gray-200 bg-white p-5">

                                <div class="flex items-center justify-between gap-6 flex-wrap">

                                    <!-- LEFT -->
                                    <div class="flex-1 min-w-[260px] space-y-2">
                                        <p class="section-label text-slate-500">Details</p>

                                        <p class="text-base font-semibold text-slate-900">
                                            {{ $order->tracking }}
                                        </p>

                                        <p class="text-sm text-slate-600">
                                            {{ $order->items->first()?->medicine_name ?? 'No items' }}
                                            · Qty {{ $order->items->first()?->quantity ?? 0 }}
                                        </p>

                                        <p class="text-sm text-slate-600">
                                            {{ $order->order_date ?: 'this day' }}
                                        </p>
                                    </div>

                                    <!-- CENTER -->
                                    <div class="min-w-[200px] rounded-2xl border border-gray-200 bg-gray-50 px-5 py-4 text-sm text-slate-600">
                                        <p class="text-xs uppercase tracking-[0.24em] text-slate-500">Status</p>
                                        <p class="mt-2 font-semibold text-slate-900">{{ $order->status }}</p>
                                    </div>

                                    <!-- RIGHT -->
                                    <div class="flex justify-end min-w-[220px]">
                                        @if ($order->status !== 'in_transit')
                                            <form method="POST" action="{{ route('orders.status', $order) }}">
                                                @csrf
                                                @method('PATCH')

                                                <input type="hidden" name="status" value="cancel_requested">

                                                <button type="submit"
                                                        class="rounded-full border border-red-200 bg-red-100 px-4 py-2 text-sm font-semibold text-red-700">
                                                    Request cancel
                                                </button>
                                            </form>
                                        @else
                                            <span class="rounded-full border border-gray-200 bg-gray-50 px-4 py-2 text-sm text-slate-500">
                                                Cancel locked in transit
                                            </span>
                                        @endif
                                    </div>

                                </div>
                            </article>
                        @endforeach

                    </div>
                </div>
            </div>
        @endif
    </section>
@endsection
