<div class="fade-in space-y-8">
    <div>
        <p class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-bold uppercase tracking-wider mb-4">
            <span class="material-symbols-outlined text-sm">route</span>
            Delivery Coordination
        </p>
        <h1 class="font-headline text-3xl font-extrabold tracking-tight">Delivery Manager</h1>
        <p class="text-on-surface-variant mt-1">Dispatch and route coordination board.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant/15 shadow-sm"><p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Drivers</p><p class="mt-3 text-3xl font-headline font-extrabold">{{ number_format($driversCount ?? 0) }}</p></div>
        <div class="bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant/15 shadow-sm"><p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Active</p><p class="mt-3 text-3xl font-headline font-extrabold text-primary">{{ number_format($activeCount ?? 0) }}</p></div>
        <div class="bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant/15 shadow-sm"><p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Urgent</p><p class="mt-3 text-3xl font-headline font-extrabold text-amber-600">{{ number_format($urgentCount ?? 0) }}</p></div>
    </div>

    <div class="bg-surface-container-lowest rounded-3xl border border-outline-variant/15 shadow-sm p-6">
        <h2 class="font-headline text-lg font-bold mb-5">Assignments</h2>
        <div class="overflow-hidden rounded-2xl border border-slate-200">
            <div class="divide-y divide-slate-200 bg-white">
                @forelse ($assignments ?? [] as $assignment)
                    <div class="flex items-center justify-between gap-4 px-4 py-3">
                        <div>
                            <p class="font-semibold">{{ $assignment['order_id'] ?? 'Unknown order' }}</p>
                            <p class="text-xs text-on-surface-variant">{{ !empty($assignment['pharmacy_first']) ? $assignment['pharmacy_first'].' '.$assignment['pharmacy_last'] : 'Unassigned pharmacy' }}</p>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ ((int) ($assignment['IsUrgen'] ?? 0) === 1) ? 'bg-amber-50 text-amber-700' : 'bg-green-50 text-green-700' }}">{{ ((int) ($assignment['IsUrgen'] ?? 0) === 1) ? 'Urgent' : 'Normal' }}</span>
                    </div>
                @empty
                    <div class="px-4 py-8 text-sm text-on-surface-variant">No assignments are available.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
