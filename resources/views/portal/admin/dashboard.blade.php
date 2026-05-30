<div class="fade-in flex flex-col gap-8">
    <div class="flex items-start justify-between">
        <div>
            <h1 class="font-headline text-3xl font-extrabold tracking-tight text-on-surface">Admin Dashboard</h1>
            <p class="text-on-surface-variant mt-1">Orders, pharmacies, employees, and urgent work at a glance.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.orders') }}" class="inline-flex items-center gap-2 px-3 py-2 bg-primary text-white rounded-md text-sm font-semibold">New Order</a>
            <a href="{{ route('admin.employees') }}" class="inline-flex items-center gap-2 px-3 py-2 bg-surface-container-low border border-outline rounded-md text-sm font-semibold">New Employee</a>
            <button id="dashboardRefreshBtn" type="button" class="inline-flex items-center gap-2 px-3 py-2 bg-white border rounded-md text-sm">Refresh</button>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant/15 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Orders</p>
            <p class="mt-3 text-3xl font-headline font-extrabold">{{ number_format((int) ($ordersCount ?? 0)) }}</p>
        </div>
        <div class="bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant/15 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Pharmacies</p>
            <p class="mt-3 text-3xl font-headline font-extrabold">{{ number_format((int) ($pharmaciesCount ?? 0)) }}</p>
        </div>
        <div class="bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant/15 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Employees</p>
            <p class="mt-3 text-3xl font-headline font-extrabold">{{ number_format((int) ($employeesCount ?? 0)) }}</p>
        </div>
        <div class="bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant/15 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Urgent</p>
            <p class="mt-3 text-3xl font-headline font-extrabold">{{ number_format((int) ($urgentCount ?? 0)) }}</p>
        </div>
    </div>

    <div class="bg-surface-container-lowest rounded-3xl border border-outline-variant/15 shadow-sm p-6">
        <div class="flex items-center justify-between gap-4 mb-5">
            <h2 class="font-headline text-lg font-bold">Recent Orders</h2>
            <a href="{{ route('admin.orders') }}" class="text-sm font-semibold text-primary">View all</a>
        </div>

        <ul class="space-y-3">
            @forelse (($recentOrders ?? []) as $order)
                <li class="rounded-xl border border-slate-200 px-4 py-3 flex items-center justify-between gap-4 bg-white">
                    <div>
                        <p class="font-semibold">{{ $order['Tracking'] ?? '—' }}</p>
                        <p class="text-xs text-on-surface-variant">{{ $order['Date'] ?? '—' }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold">DZD {{ number_format((float) ($order['otalAmount'] ?? 0), 2) }}</p>
                    </div>
                </li>
            @empty
                <li class="py-8 text-center text-on-surface-variant">No recent orders available.</li>
            @endforelse
        </ul>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('click', function (e) {
    const btn = e.target.closest && e.target.closest('#dashboardRefreshBtn');
    if (!btn) return;
    e.preventDefault();
    location.reload();
});
</script>
@endpush
