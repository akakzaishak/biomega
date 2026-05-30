@php
    $firstname = $firstname ?? (string) session('firstname', 'Admin');
    $lastname = $lastname ?? (string) session('lastname', '');
    $orders = $orders ?? [];
    $deliveryPersons = $deliveryPersons ?? [];
    $totalOrders = count($orders);
    $delivered = count(array_filter($orders, fn ($o) => (int) ($o['Status'] ?? 0) === 1));
    $notDelivered = count(array_filter($orders, fn ($o) => (int) ($o['Status'] ?? 0) === 0));
    $assigned = count(array_filter($orders, fn ($o) => !empty($o['deliveryperson_id'])));
    $unassigned = $totalOrders - $assigned;
@endphp

<div class="fade-in flex flex-col gap-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="font-headline text-3xl font-extrabold tracking-tight text-on-surface">Orders</h1>
            <p class="text-on-surface-variant mt-1">Manage, assign and track all delivery orders.</p>
        </div>
        <div class="flex items-center gap-3">
            <form method="get" action="{{ route('admin.orders') }}" class="relative flex items-center gap-2">
                <input id="orderSearchInput" name="q" value="{{ $query ?? '' }}" class="bg-surface-container-lowest border border-outline-variant/20 rounded-xl px-4 py-2 text-sm w-72 focus:ring-2 focus:ring-primary/20" placeholder="Search tracking ID..." autocomplete="off">
                <div id="orderSearchResults" class="hidden absolute left-0 top-full mt-2 z-30 w-72 bg-white border border-slate-200 rounded-2xl shadow-lg max-h-64 overflow-y-auto"></div>
                <button class="bg-primary text-white px-4 py-2 rounded-xl text-sm font-bold">Search</button>
            </form>
        </div>
    </div>

    @if (!empty($success))
        <div class="fade-in flex items-center gap-3 bg-tertiary/10 text-tertiary border border-tertiary/20 px-5 py-3.5 rounded-xl font-semibold text-sm">
            <span class="material-symbols-outlined">check_circle</span>{!! $success !!}
        </div>
    @endif
    @if (!empty($error))
        <div class="fade-in shake flex items-center gap-3 bg-error-container text-on-error-container border border-error/20 px-5 py-3.5 rounded-xl font-semibold text-sm">
            <span class="material-symbols-outlined">error</span>{{ $error }}
        </div>
    @endif

    <div class="bg-surface-container-lowest rounded-3xl border border-outline-variant/15 shadow-sm p-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant/15 shadow-sm">
                <p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Orders</p>
                <p class="mt-3 text-3xl font-headline font-extrabold">{{ number_format($totalOrders) }}</p>
            </div>
            <div class="bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant/15 shadow-sm">
                <p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Delivered</p>
                <p class="mt-3 text-3xl font-headline font-extrabold text-emerald-600">{{ number_format($delivered) }}</p>
            </div>
            <div class="bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant/15 shadow-sm">
                <p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Pending</p>
                <p class="mt-3 text-3xl font-headline font-extrabold text-amber-600">{{ number_format($notDelivered) }}</p>
            </div>
            <div class="bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant/15 shadow-sm">
                <p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Urgent</p>
                <p class="mt-3 text-3xl font-headline font-extrabold text-red-600">{{ number_format((int) count(array_filter($orders, fn ($o) => (int) ($o['IsUrgen'] ?? 0) === 1))) }}</p>
            </div>
        </div>

        <div class="mt-6 overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="text-xs uppercase tracking-wider text-on-surface-variant">
                    <tr class="border-b border-slate-200">
                        <th class="text-left py-3 pr-4">Tracking</th>
                        <th class="text-left py-3 pr-4">Date</th>
                        <th class="text-left py-3 pr-4">Pharmacy</th>
                        <th class="text-left py-3 pr-4">Location</th>
                        <th class="text-left py-3 pr-4">Amount</th>
                        <th class="text-left py-3 pr-4">Status</th>
                        <th class="text-left py-3 pr-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        @php($isUrgent = ((int) ($order['IsUrgen'] ?? 0) === 1))
                        @php($statusLabel = ((int) ($order['Status'] ?? 0) === 1) ? ['Delivered', 'bg-green-50 text-green-700'] : ['Pending', 'bg-amber-50 text-amber-700'])
                        <tr class="border-b border-slate-100 hover:bg-slate-50">
                            <td class="py-4 pr-4 font-semibold">{{ $order['Tracking'] ?? '—' }}</td>
                            <td class="py-4 pr-4 text-on-surface-variant">{{ $order['Date'] ?? '—' }}</td>
                            <td class="py-4 pr-4 text-on-surface-variant">{{ trim(($order['ph_first'] ?? '').' '.($order['ph_last'] ?? '')) ?: 'Unassigned' }}</td>
                            <td class="py-4 pr-4 text-on-surface-variant">{{ $order['ph_loc'] ?? '—' }}</td>
                            <td class="py-4 pr-4 text-on-surface-variant">
                                <form method="post" action="{{ route('admin.orders') }}" class="flex items-center gap-2">
                                    @csrf
                                    <input type="hidden" name="action" value="update_amount">
                                    <input type="hidden" name="order_id" value="{{ $order['Tracking'] ?? '' }}">
                                    <input type="number" name="amount" value="{{ (int) ($order['otalAmount'] ?? 0) }}" min="0" class="w-24 bg-white border border-outline-variant/20 rounded-xl px-3 py-2 text-xs font-semibold">
                                    <button type="submit" class="px-3 py-2 bg-slate-900 text-white rounded-full text-xs font-bold whitespace-nowrap">Update</button>
                                </form>
                            </td>
                            <td class="py-4 pr-4">
                                <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $statusLabel[1] }}">
                                    {{ $statusLabel[0] }}{{ $isUrgent ? ' · Urgent' : '' }}
                                </div>
                            </td>
                            <td class="py-4 pr-4">
                                <button type="button" onclick="openAssignModal(@js($order['Tracking'] ?? ''), @js($order['assigned_pharmacy'] ?? ''), @js($order['deliveryperson_id'] ?? ''))" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-primary text-white text-xs font-bold shadow-sm">
                                    <span class="material-symbols-outlined text-sm">assignment_ind</span>
                                    {{ !empty($order['deliveryperson_id']) ? 'Reassign' : 'Assign' }}
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-on-surface-variant">No orders found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="assignModal" class="modal-overlay" onclick="closeModalOutside(event, 'assignModal')">
    <div class="modal-box bg-surface-container-lowest w-full max-w-md mx-4 rounded-2xl shadow-2xl border border-outline-variant/15 p-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="font-headline font-extrabold text-xl text-on-surface">Assign Order</h2>
                <p class="text-xs text-on-surface-variant mt-0.5">Order <span id="modal-order-id" class="font-bold text-primary"></span></p>
            </div>
            <button onclick="closeModal('assignModal')" class="p-2 hover:bg-surface-container rounded-lg transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.orders') }}" class="space-y-5">
            @csrf
            <input type="hidden" name="action" value="assign">
            <input type="hidden" name="order_id" id="modal-order-id-input">
            <input type="hidden" name="pharmacy_id" id="modal-pharmacy-id-input">
            <div>
                <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-2">Delivery Person</label>
                @if (empty($deliveryPersons))
                    <p class="text-sm text-error font-semibold">No delivery persons available.</p>
                @else
                <div class="space-y-2 max-h-64 overflow-y-auto pr-1" id="dp-radio-list">
                    @foreach ($deliveryPersons as $dp)
                    @php($ini = strtoupper(substr((string) ($dp['FirstName'] ?? ''), 0, 1) . substr((string) ($dp['LastName'] ?? ''), 0, 1)))
                    <label class="flex items-center gap-3 p-3 rounded-xl border border-outline-variant/20 hover:bg-surface-container-low cursor-pointer transition-colors has-[:checked]:border-primary has-[:checked]:bg-primary/5">
                        <input type="radio" name="dp_phone" value="{{ $dp['PhoneNumber'] ?? '' }}" class="accent-primary w-4 h-4"/>
                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-primary to-primary-container flex items-center justify-center text-white font-bold text-sm flex-shrink-0">{{ $ini }}</div>
                        <div>
                            <p class="font-bold text-on-surface text-sm">{{ trim(($dp['FirstName'] ?? '') . ' ' . ($dp['LastName'] ?? '')) }}</p>
                            <p class="text-xs text-on-surface-variant">{{ $dp['PhoneNumber'] ?? '' }}</p>
                        </div>
                    </label>
                    @endforeach
                </div>
                @endif
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal('assignModal')" class="flex-1 py-3 rounded-xl border border-outline-variant text-on-surface-variant font-bold text-sm hover:bg-surface-container transition-colors">Cancel</button>
                @if (!empty($deliveryPersons))
                <button type="submit" class="flex-1 py-3 rounded-xl bg-gradient-to-r from-primary to-primary-container text-white font-bold text-sm shadow-md hover:opacity-90 active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-lg">assignment_ind</span>Confirm
                </button>
                @endif
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openAssignModal(orderId, pharmacyId, currentDp) {
    document.getElementById('modal-order-id').textContent = '#' + orderId;
    document.getElementById('modal-order-id-input').value = orderId;
    document.getElementById('modal-pharmacy-id-input').value = pharmacyId;
    const radios = document.querySelectorAll('#dp-radio-list input[type=radio]');
    radios.forEach(r => r.checked = (r.value === currentDp));
    document.getElementById('assignModal').classList.add('open');
}
function closeModal(id) { document.getElementById(id).classList.remove('open'); }
function closeModalOutside(e, id) { if (e.target === document.getElementById(id)) closeModal(id); }
</script>
@endpush
