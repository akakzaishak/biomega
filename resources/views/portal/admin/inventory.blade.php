@php
    $inventory = $inventory ?? $items ?? [];
    $line_items = $line_items ?? array_sum(array_map(fn($row) => (int)($row['qty'] ?? 0), $inventory));
    $top_item = $top_item ?? ($inventory[0]['Name'] ?? '—');
@endphp

<div class="fade-in flex flex-col gap-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="font-headline text-3xl font-extrabold tracking-tight text-on-surface">Inventory</h1>
            <p class="text-on-surface-variant font-medium mt-1">Use ordered line items as a live demand signal for restocking.</p>
        </div>
        <a href="{{ route('admin.reports') }}" class="inline-flex items-center gap-2 bg-primary text-white px-4 py-2.5 rounded-xl font-bold text-sm shadow-sm hover:opacity-90 transition-all"><span class="material-symbols-outlined text-lg">analytics</span>View Reports</a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="fade-in bg-surface-container-lowest p-6 rounded-xl border border-outline-variant/15 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Line Items</p>
            <p class="mt-3 text-3xl font-headline font-extrabold text-on-surface">{{ number_format($line_items) }}</p>
        </div>
        <div class="fade-in bg-surface-container-lowest p-6 rounded-xl border border-outline-variant/15 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Top Requested</p>
            <p class="mt-3 text-xl font-headline font-extrabold text-primary">{{ $top_item }}</p>
        </div>
        <div class="fade-in bg-surface-container-lowest p-6 rounded-xl border border-outline-variant/15 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Reorder Status</p>
            <p class="mt-3 text-xl font-headline font-extrabold text-tertiary">Monitor demand</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm p-6">
            <div class="flex items-center justify-between mb-5">
                <h2 class="font-headline text-lg font-bold">Top Requested Medicines</h2>
                <span class="text-xs font-semibold text-on-surface-variant">{{ count($inventory) }} items</span>
            </div>
            <div class="space-y-3">
                @forelse ($inventory as $item)
                    <div class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3 hover:bg-slate-50">
                        <div>
                            <p class="font-semibold text-on-surface">{{ $item['Name'] ?? '—' }}</p>
                            <p class="text-xs text-on-surface-variant">Requested quantity signal</p>
                        </div>
                        <span class="inline-flex items-center rounded-full bg-primary/10 text-primary px-3 py-1 text-xs font-bold">{{ number_format((int)($item['qty'] ?? 0)) }}</span>
                    </div>
                @empty
                    <div class="text-center text-on-surface-variant py-8">No line items recorded yet.</div>
                @endforelse
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm p-6">
                <h2 class="font-headline text-lg font-bold mb-4">Restock Rules</h2>
                <div class="space-y-3 text-sm text-on-surface-variant">
                    <div class="rounded-xl border border-slate-200 px-4 py-3">Flag items with repeated urgent orders.</div>
                    <div class="rounded-xl border border-slate-200 px-4 py-3">Review stock gaps by order frequency.</div>
                    <div class="rounded-xl border border-slate-200 px-4 py-3">Link to a true stock table on the next pass.</div>
                </div>
            </div>
            <div class="bg-gradient-to-br from-primary to-primary-container text-white rounded-2xl shadow-lg p-6">
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-white/70">Inventory note</p>
                <p class="mt-3 text-sm leading-relaxed text-white/90">This page keeps the same UI language while surfacing demand data from the current schema.</p>
            </div>
        </div>
    </div>
</div>