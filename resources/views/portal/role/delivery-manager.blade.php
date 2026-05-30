<div class="fade-in">
    <div>
        <p class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-bold uppercase tracking-wider mb-4">
            <span class="material-symbols-outlined text-sm">local_shipping</span>
            Delivery Manager
        </p>
        <h1 class="font-headline text-3xl font-extrabold tracking-tight">Delivery Manager</h1>
        <p class="text-on-surface-variant mt-1">Coordinate drivers, assignments, and urgent handoffs.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-outline-variant/15 p-6 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Drivers</p>
            <p class="mt-3 text-3xl font-headline font-extrabold">{{ number_format($driversCount ?? 0) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-outline-variant/15 p-6 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Active</p>
            <p class="mt-3 text-3xl font-headline font-extrabold text-primary">{{ number_format($activeCount ?? 0) }}</p>
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
                <h2 class="font-headline text-lg font-bold">Assignments</h2>
                <span class="text-xs font-semibold text-on-surface-variant">{{ number_format(count($assignments ?? [])) }} rows</span>
            </div>
            <div class="space-y-3">
                @forelse(array_slice($assignments ?? [], 0, 8) as $assignment)
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 rounded-xl border border-slate-200 px-4 py-3">
                        <div>
                            <p class="font-semibold">{{ $assignment['order_id'] ?? 'Unknown order' }}</p>
                            <p class="text-xs text-on-surface-variant">{{ !empty($assignment['pharmacy_first']) ? $assignment['pharmacy_first'].' '.$assignment['pharmacy_last'] : 'Unassigned pharmacy' }}</p>
                        </div>
                        <div class="flex items-center gap-2 text-xs font-bold">
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-slate-100 text-slate-700">Driver {{ $assignment['deliveryperson_id'] ?? 'N/A' }}</span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full {{ ((int) ($assignment['IsUrgen'] ?? 0) === 1) ? 'bg-amber-50 text-amber-700' : 'bg-green-50 text-green-700' }}">{{ ((int) ($assignment['IsUrgen'] ?? 0) === 1) ? 'Urgent' : 'Normal' }}</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-on-surface-variant py-8">No assignments available.</div>
                @endforelse
            </div>
        </div>
        <div class="space-y-6">
            <div class="bg-white rounded-2xl border border-outline-variant/15 p-6 shadow-sm">
                <h2 class="font-headline text-lg font-bold mb-4">Driver Roster</h2>
                <div class="space-y-3">
                    @forelse(array_slice($driverRows ?? [], 0, 5) as $driver)
                        <div class="rounded-xl border border-slate-200 px-4 py-3 flex items-center justify-between">
                            <div>
                                <p class="font-semibold text-sm">{{ trim(($driver['FirstName'] ?? '') . ' ' . ($driver['LastName'] ?? '')) }}</p>
                                <p class="text-xs text-on-surface-variant">{{ $driver['PhoneNumber'] ?? '' }}</p>
                            </div>
                            <span class="material-symbols-outlined text-primary">local_shipping</span>
                        </div>
                    @empty
                        <div class="text-sm text-on-surface-variant">No driver records found.</div>
                    @endforelse
                </div>
            </div>
            <div class="bg-gradient-to-br from-primary to-primary-container text-white rounded-2xl shadow-lg p-6">
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-white/70">Manager note</p>
                <p class="mt-3 text-sm leading-relaxed text-white/90">Use this board to keep route dispatch and pickup timing aligned with the rest of the portal.</p>
            </div>
        </div>
    </div>
</div>
