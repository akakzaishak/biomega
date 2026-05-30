<div class="fade-in space-y-8">
    <div>
        <p class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-bold uppercase tracking-wider mb-4">
            <span class="material-symbols-outlined text-sm">local_pharmacy</span>
            Pharmacy Account
        </p>
        <h1 class="font-headline text-3xl font-extrabold tracking-tight">Pharmacy Hub</h1>
        <p class="text-on-surface-variant mt-1">{{ $pharmacy['Location'] ?? 'Your pharmacy dashboard is ready.' }}</p>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-5 gap-4">
        <div class="xl:col-span-2 bg-surface-container-lowest rounded-3xl border border-outline-variant/15 shadow-sm p-6">
            <h2 class="font-headline text-lg font-bold mb-4">Pharmacy Profile</h2>
            <div class="space-y-3 text-sm">
                <div class="flex items-center justify-between gap-4"><span class="text-on-surface-variant">Name</span><span class="font-semibold text-right">{{ trim(($pharmacy['FirstName'] ?? '') . ' ' . ($pharmacy['LastName'] ?? '')) }}</span></div>
                <div class="flex items-center justify-between gap-4"><span class="text-on-surface-variant">NIF</span><span class="font-semibold text-right">{{ $pharmacy['NIF'] ?? '' }}</span></div>
                <div class="flex items-center justify-between gap-4"><span class="text-on-surface-variant">Phone</span><span class="font-semibold text-right">{{ $pharmacy['PhoneNumber'] ?? '' }}</span></div>
                <div class="flex items-center justify-between gap-4"><span class="text-on-surface-variant">Location</span><span class="font-semibold text-right">{{ $pharmacy['Location'] ?? '' }}</span></div>
                <div class="flex items-center justify-between gap-4"><span class="text-on-surface-variant">Work time</span><span class="font-semibold text-right">{{ $pharmacy['WorkTime'] ?? '' }}</span></div>
            </div>
        </div>
        <div class="xl:col-span-3 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant/15 shadow-sm"><p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Orders</p><p class="mt-3 text-3xl font-headline font-extrabold">{{ number_format($ordersCount ?? 0) }}</p></div>
            <div class="bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant/15 shadow-sm"><p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Pending</p><p class="mt-3 text-3xl font-headline font-extrabold text-primary">{{ number_format($pendingCount ?? 0) }}</p></div>
            <div class="bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant/15 shadow-sm"><p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Urgent</p><p class="mt-3 text-3xl font-headline font-extrabold text-amber-600">{{ number_format($urgentCount ?? 0) }}</p></div>
            <div class="bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant/15 shadow-sm"><p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Completed</p><p class="mt-3 text-3xl font-headline font-extrabold text-tertiary">{{ number_format($completedCount ?? 0) }}</p></div>
        </div>
    </div>

    <div class="bg-surface-container-lowest rounded-3xl border border-outline-variant/15 shadow-sm p-6">
        <h2 class="font-headline text-lg font-bold mb-5">Orders</h2>
        <div class="overflow-hidden rounded-2xl border border-slate-200">
            <div class="divide-y divide-slate-200 bg-white">
                @forelse ($orders ?? [] as $order)
                    <div class="flex items-center justify-between gap-4 px-4 py-3">
                        <div>
                            <p class="font-semibold">{{ $order['Tracking'] ?? $order['order_id'] ?? 'Unknown order' }}</p>
                            <p class="text-xs text-on-surface-variant">Packages: {{ $order['PackageNumber'] ?? '0' }}</p>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ ((int) ($order['IsUrgen'] ?? 0) === 1) ? 'bg-amber-50 text-amber-700' : 'bg-green-50 text-green-700' }}">{{ ((int) ($order['IsUrgen'] ?? 0) === 1) ? 'Urgent' : 'Normal' }}</span>
                    </div>
                @empty
                    <div class="px-4 py-8 text-sm text-on-surface-variant">No orders have been assigned to this pharmacy.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
