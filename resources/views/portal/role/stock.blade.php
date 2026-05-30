<div class="fade-in space-y-8">
    <div>
        <p class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-bold uppercase tracking-wider mb-4">
            <span class="material-symbols-outlined text-sm">inventory_2</span>
            Stock Control
        </p>
        <h1 class="font-headline text-3xl font-extrabold tracking-tight">Stock Dashboard</h1>
        <p class="text-on-surface-variant mt-1">Create and review incoming orders.</p>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 bg-surface-container-lowest rounded-3xl border border-outline-variant/15 shadow-sm p-6">
            <h2 class="font-headline text-lg font-bold mb-5">Create Order</h2>
            <form method="POST" action="{{ route('stock.dashboard') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf
                <input type="hidden" name="action" value="create_order" />
                <div><label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Medicine</label><input name="medicine_name" value="{{ old('medicine_name') }}" class="w-full bg-surface-container-low border-none rounded-xl px-4 py-3" /></div>
                <div><label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Quantity</label><input type="number" name="quantity" value="{{ old('quantity', 1) }}" class="w-full bg-surface-container-low border-none rounded-xl px-4 py-3" /></div>
                <div><label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Amount</label><input type="number" name="amount" value="{{ old('amount', 0) }}" class="w-full bg-surface-container-low border-none rounded-xl px-4 py-3" /></div>
                <div><label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Package number</label><input type="number" name="package_number" value="{{ old('package_number', 1) }}" class="w-full bg-surface-container-low border-none rounded-xl px-4 py-3" /></div>
                <div><label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">Pharmacy NIF (optional)</label><select name="pharmacy_id" class="w-full bg-surface-container-low border-none rounded-xl px-4 py-3"><option value="">Select pharmacy</option>@foreach ($pharmacies ?? [] as $pharmacy)<option value="{{ $pharmacy['NIF'] ?? '' }}">{{ trim(($pharmacy['FirstName'] ?? '') . ' ' . ($pharmacy['LastName'] ?? '')) }}</option>@endforeach</select></div>
                <div class="flex items-end"><label class="inline-flex items-center gap-3 text-sm font-semibold"><input type="checkbox" name="is_urgent" class="rounded border-slate-300" {{ old('is_urgent') ? 'checked' : '' }}> Urgent order</label></div>
                <div class="md:col-span-2"><button class="bg-primary text-white px-5 py-3 rounded-xl font-bold">Create order</button></div>
            </form>
        </div>
        <div class="space-y-6">
            <div class="bg-surface-container-lowest rounded-3xl border border-outline-variant/15 shadow-sm p-6">
                <h2 class="font-headline text-lg font-bold mb-4">Pending Orders</h2>
                <div class="space-y-3">
                    @forelse ($pendingOrders ?? [] as $order)
                        <div class="rounded-xl border border-slate-200 px-4 py-3">
                            <div class="flex items-center justify-between gap-4"><p class="font-semibold">{{ $order['Tracking'] ?? 'Unknown tracking' }}</p><span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ ((int) ($order['IsUrgen'] ?? 0) === 1) ? 'bg-amber-50 text-amber-700' : 'bg-green-50 text-green-700' }}">{{ ((int) ($order['IsUrgen'] ?? 0) === 1) ? 'Urgent' : 'Normal' }}</span></div>
                            <p class="text-xs text-on-surface-variant mt-1">DZD {{ number_format((float) ($order['otalAmount'] ?? 0), 0, '.', ',') }}</p>
                        </div>
                    @empty
                        <div class="text-sm text-on-surface-variant">No pending orders.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
