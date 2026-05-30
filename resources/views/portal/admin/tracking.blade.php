<div class="fade-in flex flex-col gap-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="font-headline text-3xl font-extrabold tracking-tight mb-1">Tracking</h1>
            <p class="text-on-surface-variant">Route and assignment overview.</p>
        </div>
        <div class="text-sm text-on-surface-variant">Driver roster included below</div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm p-6"><p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Routes</p><p class="mt-3 text-3xl font-headline font-extrabold">{{ number_format((int) count($routes)) }}</p></div>
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm p-6"><p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Active</p><p class="mt-3 text-3xl font-headline font-extrabold text-primary">{{ number_format((int) count(array_filter($routes, fn ($route) => (int) ($route['Status'] ?? 0) === 0))) }}</p></div>
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm p-6"><p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Urgent</p><p class="mt-3 text-3xl font-headline font-extrabold text-amber-600">{{ number_format((int) count(array_filter($routes, fn ($route) => (int) ($route['IsUrgen'] ?? 0) === 1))) }}</p></div>
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm p-6"><p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Drivers</p><p class="mt-3 text-3xl font-headline font-extrabold">{{ number_format((int) count($drivers ?? [])) }}</p></div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm p-6">
            <div class="flex items-center justify-between mb-5">
                <h2 class="font-headline text-lg font-bold">Assignments</h2>
                <span class="text-xs font-semibold text-on-surface-variant">{{ count($routes) }} rows</span>
            </div>
            <div class="space-y-3">
                @forelse ($routes as $route)
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 rounded-xl border border-slate-200 px-4 py-3">
                        <div>
                            <p class="font-semibold">{{ $route['order_id'] }}</p>
                            <p class="text-xs text-on-surface-variant">{{ !empty($route['pharmacy_first']) ? $route['pharmacy_first'].' '.$route['pharmacy_last'] : 'Unassigned pharmacy' }}</p>
                        </div>
                        <div class="flex items-center gap-2 text-xs font-bold">
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-slate-100 text-slate-700">Driver {{ $route['deliveryperson_id'] ?: 'N/A' }}</span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full {{ ((int)($route['IsUrgen'] ?? 0) === 1) ? 'bg-amber-50 text-amber-700' : 'bg-green-50 text-green-700' }}">{{ ((int)($route['IsUrgen'] ?? 0) === 1) ? 'Urgent' : 'Normal' }}</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-on-surface-variant py-8">No assignments available.</div>
                @endforelse
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm p-6">
                <h2 class="font-headline text-lg font-bold mb-4">Driver Roster</h2>
                <div class="space-y-3">
                    @forelse (($drivers ?? []) as $driver)
                        <div class="rounded-xl border border-slate-200 px-4 py-3 flex items-center justify-between">
                            <div>
                                <p class="font-semibold text-sm">{{ $driver['FirstName'] ?? '—' }} {{ $driver['LastName'] ?? '' }}</p>
                                <p class="text-xs text-on-surface-variant">{{ $driver['PhoneNumber'] ?? '—' }}</p>
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