<div class="fade-in space-y-8">
    <div>
        <p class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-bold uppercase tracking-wider mb-4">
            <span class="material-symbols-outlined text-sm">local_shipping</span>
            Delivery Person
        </p>
        <h1 class="font-headline text-3xl font-extrabold tracking-tight">My Route Board</h1>
        <p class="text-on-surface-variant mt-1">Assigned deliveries for the current account.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant/15 shadow-sm"><p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Assigned</p><p class="mt-3 text-3xl font-headline font-extrabold">{{ number_format($assignedCount ?? 0) }}</p></div>
        <div class="bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant/15 shadow-sm"><p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Urgent</p><p class="mt-3 text-3xl font-headline font-extrabold text-amber-600">{{ number_format($urgentCount ?? 0) }}</p></div>
        <div class="bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant/15 shadow-sm"><p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Completed</p><p class="mt-3 text-3xl font-headline font-extrabold text-tertiary">{{ number_format($completedCount ?? 0) }}</p></div>
    </div>

    <div class="space-y-3">
        @forelse ($routes ?? [] as $route)
            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm p-5 flex items-center justify-between gap-4">
                <div>
                    <p class="font-semibold">{{ $route['Tracking'] ?? $route['order_id'] ?? 'Unknown route' }}</p>
                    <p class="text-xs text-on-surface-variant">{{ !empty($route['pharmacy_first']) ? $route['pharmacy_first'].' '.$route['pharmacy_last'] : 'Unknown pharmacy' }}</p>
                    <p class="text-xs text-on-surface-variant mt-1">{{ $route['pharmacy_location'] ?? '' }}</p>
                </div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ ((int) ($route['IsUrgen'] ?? 0) === 1) ? 'bg-amber-50 text-amber-700' : 'bg-green-50 text-green-700' }}">{{ ((int) ($route['IsUrgen'] ?? 0) === 1) ? 'Urgent' : 'Normal' }}</span>
            </div>
        @empty
            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm p-6 text-sm text-on-surface-variant">No routes are assigned to this account yet.</div>
        @endforelse
    </div>
</div>
