<div class="fade-in">
    <div>
        <h1 class="font-headline text-3xl font-extrabold tracking-tight">My Route Board</h1>
        <p class="text-on-surface-variant mt-1">Open assignments, urgent stops, and completed deliveries.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
        <div class="bg-white rounded-xl border border-outline-variant/15 p-6 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Assigned</p>
            <p class="mt-3 text-3xl font-headline font-extrabold">{{ number_format($assignedCount ?? 0) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-outline-variant/15 p-6 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Urgent</p>
            <p class="mt-3 text-3xl font-headline font-extrabold text-amber-600">{{ number_format($urgentCount ?? 0) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-outline-variant/15 p-6 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Completed</p>
            <p class="mt-3 text-3xl font-headline font-extrabold text-tertiary">{{ number_format($completedCount ?? 0) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mt-6">
        <div class="xl:col-span-2 bg-white rounded-2xl border border-outline-variant/15 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-5">
                <h2 class="font-headline text-lg font-bold">Assigned Deliveries</h2>
                <span class="text-xs font-semibold text-on-surface-variant">{{ number_format(count($routes ?? [])) }} routes</span>
            </div>
            <div class="space-y-3">
                @foreach($routes ?? [] as $route)
                    <div class="rounded-xl border border-slate-200 px-4 py-3">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="font-semibold">{{ $route['Tracking'] ?? $route['order_id'] ?? '' }}</p>
                                <p class="text-xs text-on-surface-variant">{{ !empty($route['pharmacy_first']) ? $route['pharmacy_first'].' '.$route['pharmacy_last'] : 'Unknown pharmacy' }}</p>
                            </div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ ((int) ($route['IsUrgen'] ?? 0) === 1) ? 'bg-amber-50 text-amber-700' : 'bg-green-50 text-green-700' }}">{{ ((int) ($route['IsUrgen'] ?? 0) === 1) ? 'Urgent' : 'Normal' }}</span>
                        </div>
                        <p class="mt-3 text-xs text-on-surface-variant">{{ $route['pharmacy_location'] ?? 'No location provided' }}</p>
                    </div>
                @endforeach

                @if (empty($routes ?? []))
                    <div class="rounded-2xl border border-dashed border-outline-variant/40 bg-surface-container-low p-8 text-center text-on-surface-variant">No deliveries are assigned to this account yet.</div>
                @endif
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-2xl border border-outline-variant/15 p-6 shadow-sm">
                <h2 class="font-headline text-lg font-bold mb-4">Delivery Checklist</h2>
                <div class="space-y-3 text-sm text-on-surface-variant">
                    <div class="rounded-xl border border-slate-200 px-4 py-3">Confirm pickup details before leaving the hub.</div>
                    <div class="rounded-xl border border-slate-200 px-4 py-3">Update status after each handoff.</div>
                    <div class="rounded-xl border border-slate-200 px-4 py-3">Upload proof of delivery when available.</div>
                </div>
            </div>
            <div class="bg-gradient-to-br from-primary to-primary-container text-white rounded-2xl shadow-lg p-6">
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-white/70">Route note</p>
                <p class="mt-3 text-sm leading-relaxed text-white/90">This page keeps your assigned work visible without changing the portal’s visual language.</p>
            </div>
        </div>
    </div>
</div>
