<div class="fade-in space-y-8">
    <div>
        <p class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-bold uppercase tracking-wider mb-4">
            <span class="material-symbols-outlined text-sm">storefront</span>
            Commercial Operations
        </p>
        <h1 class="font-headline text-3xl font-extrabold tracking-tight">Commercial Dashboard</h1>
        <p class="text-on-surface-variant mt-1">Overview for commercial operations in the Biomega portal.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant/15 shadow-sm"><p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Pharmacies</p><p class="mt-3 text-3xl font-headline font-extrabold">{{ number_format($pharmaciesCount ?? 0) }}</p></div>
        <div class="bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant/15 shadow-sm"><p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Pending</p><p class="mt-3 text-3xl font-headline font-extrabold text-primary">{{ number_format($pendingCount ?? 0) }}</p></div>
        <div class="bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant/15 shadow-sm"><p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Urgent</p><p class="mt-3 text-3xl font-headline font-extrabold text-amber-600">{{ number_format($urgentCount ?? 0) }}</p></div>
    </div>

    <div class="bg-surface-container-lowest rounded-3xl border border-outline-variant/15 shadow-sm p-6">
        <div class="flex items-center justify-between mb-5 gap-4">
            <div>
                <h2 class="font-headline text-lg font-bold">Recent Orders</h2>
                <p class="text-sm text-on-surface-variant">Latest requests flowing through the commercial desk.</p>
            </div>
            <a class="text-sm font-semibold text-primary" href="{{ route('admin.orders') }}">Open admin board</a>
        </div>
        <div class="overflow-hidden rounded-2xl border border-slate-200">
            <div class="divide-y divide-slate-200 bg-white">
                @forelse ($orders ?? [] as $order)
                    <div class="flex items-center justify-between gap-4 px-4 py-3">
                        <div>
                            <p class="font-semibold">{{ $order['Tracking'] ?? 'Unknown tracking' }}</p>
                            <p class="text-xs text-on-surface-variant">{{ !empty($order['pharmacy_first']) ? $order['pharmacy_first'].' '.$order['pharmacy_last'] : 'Unassigned' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-on-surface">DZD {{ number_format((float) ($order['otalAmount'] ?? 0), 0, '.', ',') }}</p>
                            <p class="text-xs text-on-surface-variant">{{ $order['Date'] ?? '' }}</p>
                        </div>
                    </div>
                @empty
                    <div class="px-4 py-8 text-sm text-on-surface-variant">No recent orders found.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
