<div class="fade-in flex flex-col gap-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="font-headline text-3xl font-extrabold tracking-tight">Pharmacies</h1>
            <p class="text-on-surface-variant mt-1">Registered pharmacies in the current database.</p>
        </div>
        <a href="{{ route('register.pharmacy') }}" class="inline-flex items-center gap-2 bg-primary text-white px-4 py-2.5 rounded-xl font-bold text-sm shadow-sm">
            <span class="material-symbols-outlined text-lg">add_circle</span>Add pharmacy
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant/15 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Pharmacies</p>
            <p class="mt-3 text-3xl font-headline font-extrabold">{{ number_format((int) count($pharmacies)) }}</p>
        </div>
        <div class="bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant/15 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Linked Orders</p>
            <p class="mt-3 text-3xl font-headline font-extrabold text-primary">{{ number_format((int) array_sum(array_map(fn ($pharmacy) => (int) ($pharmacy['total_orders'] ?? 0), $pharmacies))) }}</p>
        </div>
        <div class="bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant/15 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Order Coverage</p>
            <p class="mt-3 text-3xl font-headline font-extrabold text-tertiary">Live</p>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse ($pharmacies as $pharmacy)
            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm p-6">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="font-bold text-lg">{{ $pharmacy['FirstName'] ?? '—' }} {{ $pharmacy['LastName'] ?? '' }}</p>
                        <p class="text-xs text-on-surface-variant mt-1">NIF {{ $pharmacy['NIF'] ?? '—' }}</p>
                    </div>
                    <span class="material-symbols-outlined text-primary">local_pharmacy</span>
                </div>
                <div class="mt-4 space-y-2 text-sm text-on-surface-variant">
                    <p>{{ $pharmacy['PhoneNumber'] ?? '—' }}</p>
                    <p>{{ $pharmacy['Location'] ?? '—' }}</p>
                    <p>{{ $pharmacy['WorkTime'] ?? '' }}</p>
                    <p class="font-semibold text-on-surface">Orders: {{ number_format((int) ($pharmacy['total_orders'] ?? 0)) }}</p>
                </div>
                <div class="mt-4 flex items-center gap-2">
                    <form method="post" action="{{ route('admin.pharmacies.delete', $pharmacy['NIF'] ?? '') }}" onsubmit="return confirm('Delete this pharmacy?');">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 bg-red-50 text-red-700 px-3 py-1 rounded-full text-sm font-bold">Delete</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-surface-container-lowest rounded-2xl border border-dashed border-outline-variant/40 p-8 text-center text-on-surface-variant">
                No pharmacies found.
            </div>
        @endforelse
    </div>
</div>