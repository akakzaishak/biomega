<div class="fade-in flex flex-col gap-6">
    <div>
        <h1 class="font-headline text-3xl font-extrabold tracking-tight mb-1">Payments</h1>
        <p class="text-on-surface-variant mb-6">Current payment summary.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm p-6"><p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Payments</p><p class="mt-3 text-3xl font-headline font-extrabold">{{ number_format((int) count($payments)) }}</p></div>
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm p-6"><p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Online</p><p class="mt-3 text-3xl font-headline font-extrabold text-primary">{{ number_format((int) count(array_filter($payments, fn ($payment) => stripos((string) ($payment['method'] ?? ''), 'online') !== false))) }}</p></div>
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm p-6"><p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Collected</p><p class="mt-3 text-3xl font-headline font-extrabold text-tertiary">DZD {{ number_format((float) array_sum(array_map(fn ($payment) => (float) ($payment['amount'] ?? 0), $payments)), 0, '.', ',') }}</p></div>
    </div>

    @if (! $paymentTable)
        <div class="bg-surface-container-lowest rounded-2xl border border-dashed border-outline-variant/40 p-8 text-center text-on-surface-variant">The payment table is not present in the connected database yet.</div>
    @else
        <div class="bg-surface-container-lowest rounded-3xl border border-outline-variant/15 shadow-sm p-6 overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="text-xs uppercase tracking-wider text-on-surface-variant"><tr class="border-b border-slate-200"><th class="text-left py-3 pr-4">ID</th><th class="text-left py-3 pr-4">Order</th><th class="text-left py-3 pr-4">Amount</th><th class="text-left py-3 pr-4">Method</th><th class="text-left py-3 pr-4">Status</th></tr></thead>
                <tbody>
                    @forelse ($payments as $payment)
                        <tr class="border-b border-slate-100 hover:bg-slate-50">
                            <td class="py-4 pr-4 font-semibold">{{ $payment['payment_id'] ?? '—' }}</td>
                            <td class="py-4 pr-4 text-on-surface-variant">{{ $payment['order_id'] ?? '—' }}</td>
                            <td class="py-4 pr-4 text-on-surface-variant">DZD {{ number_format((float) ($payment['amount'] ?? 0), 0, '.', ',') }}</td>
                            <td class="py-4 pr-4 text-on-surface-variant">{{ $payment['method'] ?? '—' }}</td>
                            <td class="py-4 pr-4 text-on-surface-variant">{{ $payment['status'] ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-8 text-center text-on-surface-variant">No payments found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
</div>