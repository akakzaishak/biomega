<div class="fade-in flex flex-col gap-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="font-headline text-3xl font-extrabold tracking-tight mb-1">Reports</h1>
            <p class="text-on-surface-variant">Order volume and revenue summary.</p>
        </div>
        <a href="{{ route('admin.orders') }}" class="inline-flex items-center gap-2 bg-primary text-white px-4 py-2.5 rounded-xl font-bold text-sm shadow-sm">
            <span class="material-symbols-outlined text-lg">package_2</span>Open Orders
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant/15 shadow-sm"><p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Orders</p><p class="mt-3 text-3xl font-headline font-extrabold">{{ number_format((int) ($reportTotals['total'] ?? 0)) }}</p></div>
        <div class="bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant/15 shadow-sm"><p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Revenue</p><p class="mt-3 text-3xl font-headline font-extrabold text-primary">DZD {{ number_format((float) ($reportTotals['revenue'] ?? 0), 0, '.', ',') }}</p></div>
        <div class="bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant/15 shadow-sm"><p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Window</p><p class="mt-3 text-xl font-headline font-extrabold text-tertiary">Last 7 days</p></div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm p-6">
            <div class="flex items-center justify-between mb-5">
                <h2 class="font-headline text-lg font-bold">Daily Breakdown</h2>
                <span class="text-xs font-semibold text-on-surface-variant">Latest entries first</span>
            </div>
            <div class="space-y-3">
                @forelse ($reportRows as $row)
                    <div class="rounded-xl border border-slate-200 px-4 py-3 flex items-center justify-between">
                        <div><p class="font-semibold text-on-surface">{{ $row['Date'] ?? '—' }}</p><p class="text-xs text-on-surface-variant">{{ number_format((int) ($row['total'] ?? 0)) }} orders</p></div>
                        <span class="text-sm font-bold text-primary">DZD {{ number_format((float) ($row['revenue'] ?? 0), 0, '.', ',') }}</span>
                    </div>
                @empty
                    <div class="text-center text-on-surface-variant py-8">No report data available yet.</div>
                @endforelse
            </div>
        </div>
        <div class="space-y-6">
            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm p-6">
                <h2 class="font-headline text-lg font-bold mb-4">Exports</h2>
                <div class="space-y-3 text-sm text-on-surface-variant">
                    <div class="rounded-xl border border-slate-200 px-4 py-3">Order summary CSV</div>
                    <div class="rounded-xl border border-slate-200 px-4 py-3">Payment reconciliation</div>
                    <div class="rounded-xl border border-slate-200 px-4 py-3">Operational snapshot PDF</div>
                </div>
            </div>
            <div class="bg-gradient-to-br from-primary to-primary-container text-white rounded-2xl shadow-lg p-6">
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-white/70">Reporting note</p>
                <p class="mt-3 text-sm leading-relaxed text-white/90">This gives you a stable reporting landing page now, and you can swap the simple summaries for charts later.</p>
            </div>
        </div>
    </div>
</div>