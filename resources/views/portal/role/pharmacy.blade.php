<div class="fade-in space-y-8">
    <div>
        <p class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-bold uppercase tracking-wider mb-4">
            <span class="material-symbols-outlined text-sm">local_pharmacy</span>
            Pharmacy Account
        </p>
        <h1 class="font-headline text-3xl font-extrabold tracking-tight">Pharmacy Hub</h1>
        <p class="text-on-surface-variant mt-1">{{ $pharmacy['Location'] ?? 'Your pharmacy dashboard is ready.' }}</p>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-5 gap-4">
        <div class="xl:col-span-2 bg-surface-container-lowest rounded-3xl border border-outline-variant/15 shadow-sm p-6">
            <h2 class="font-headline text-lg font-bold mb-4">Pharmacy Profile</h2>
            <div class="space-y-3 text-sm">
                <div class="flex items-center justify-between gap-4"><span class="text-on-surface-variant">Name</span><span class="font-semibold text-right">{{ trim(($pharmacy['FirstName'] ?? '') . ' ' . ($pharmacy['LastName'] ?? '')) }}</span></div>
                <div class="flex items-center justify-between gap-4"><span class="text-on-surface-variant">NIF</span><span class="font-semibold text-right">{{ $pharmacy['NIF'] ?? '' }}</span></div>
                <div class="flex items-center justify-between gap-4"><span class="text-on-surface-variant">Phone</span><span class="font-semibold text-right">{{ $pharmacy['PhoneNumber'] ?? '' }}</span></div>
                <div class="flex items-center justify-between gap-4"><span class="text-on-surface-variant">Location</span><span class="font-semibold text-right">{{ $pharmacy['Location'] ?? '' }}</span></div>
                <div class="flex items-center justify-between gap-4"><span class="text-on-surface-variant">Work time</span><span class="font-semibold text-right">{{ $pharmacy['WorkTime'] ?? '' }}</span></div>
            </div>
        </div>
        <div class="xl:col-span-3 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant/15 shadow-sm"><p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Orders</p><p class="mt-3 text-3xl font-headline font-extrabold">{{ number_format($ordersCount ?? 0) }}</p></div>
            <div class="bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant/15 shadow-sm"><p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Pending</p><p class="mt-3 text-3xl font-headline font-extrabold text-primary">{{ number_format($pendingCount ?? 0) }}</p></div>
            <div class="bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant/15 shadow-sm"><p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Urgent</p><p class="mt-3 text-3xl font-headline font-extrabold text-amber-600">{{ number_format($urgentCount ?? 0) }}</p></div>
            <div class="bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant/15 shadow-sm"><p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Completed</p><p class="mt-3 text-3xl font-headline font-extrabold text-tertiary">{{ number_format($completedCount ?? 0) }}</p></div>
        </div>
    </div>

    <div class="bg-surface-container-lowest rounded-3xl border border-outline-variant/15 shadow-sm p-6">
        <div class="flex items-center justify-between mb-5">
            <h2 class="font-headline text-lg font-bold">Orders</h2>
            <div class="flex gap-2">
                <button onclick="showTab('list')" class="px-3 py-2 rounded-md bg-surface-container">List</button>
                <button onclick="showTab('create')" class="px-3 py-2 rounded-md bg-primary text-white">Create Order</button>
                <button onclick="showTab('track')" class="px-3 py-2 rounded-md bg-surface-container">Track Orders</button>
            </div>
        </div>

        <div id="tab-list">
            <div class="overflow-hidden rounded-2xl border border-slate-200">
                <div class="divide-y divide-slate-200 bg-white">
                    @forelse ($ordersEnriched ?? [] as $order)
                        <div class="flex items-center justify-between gap-4 px-4 py-3">
                            <div>
                                <p class="font-semibold">{{ $order['Tracking'] ?? $order['order_id'] ?? 'Unknown order' }}</p>
                                <p class="text-xs text-on-surface-variant">Packages: {{ $order['PackageNumber'] ?? '0' }} · Status: {{ $order['Status'] ?? '—' }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                @if (!empty($order['deliveryperson_id']))
                                    <button onclick="openTrackModal(@js($order))" class="px-3 py-1 rounded-md bg-primary text-white text-xs">Track DP</button>
                                @else
                                    <span class="text-xs text-on-surface-variant">No delivery person assigned</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="px-4 py-8 text-sm text-on-surface-variant">No orders found for this pharmacy.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div id="tab-create" style="display:none;" class="mt-6">
            <form method="POST" action="{{ route('pharmacy.dashboard') }}" class="space-y-6">
                @csrf
                <input type="hidden" name="action" value="create_order" />
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-semibold">Total amount (DZD)</label>
                        <input name="total_amount" type="number" min="0" required class="w-full mt-2 px-3 py-2 rounded-md bg-surface-container" />
                    </div>
                    <div>
                        <label class="text-sm font-semibold">Packages</label>
                        <input name="package_number" type="number" min="1" value="1" required class="w-full mt-2 px-3 py-2 rounded-md bg-surface-container" />
                    </div>
                </div>

                <div>
                    <label class="text-sm font-semibold">Items</label>
                    <div id="items" class="space-y-2 mt-2">
                        <div class="flex gap-2">
                            <input name="item_name[]" placeholder="Item name" required class="flex-1 px-3 py-2 rounded-md bg-surface-container" />
                            <input name="item_qty[]" type="number" min="1" value="1" required class="w-24 px-3 py-2 rounded-md bg-surface-container" />
                        </div>
                    </div>
                    <div class="mt-2"><button type="button" onclick="addItem()" class="px-3 py-2 rounded-md bg-primary text-white">Add item</button></div>
                </div>

                <div>
                    <label class="inline-flex items-center gap-2"><input type="checkbox" name="is_urgent" class="form-checkbox" /><span class="text-sm">Mark as urgent</span></label>
                </div>

                <div><button type="submit" class="px-4 py-3 rounded-md bg-gradient-to-r from-primary to-primary-container text-white font-bold">Create Order</button></div>
            </form>
        </div>

        <div id="tab-track" style="display:none;" class="mt-6">
            <div class="mb-3 text-sm text-on-surface-variant">Click "Track DP" on any order to view the delivery person's live location on the map.</div>
            <div id="map" style="height:400px;border-radius:12px;overflow:hidden;" class="bg-surface-container"></div>
        </div>
    </div>
</div>

<!-- Track modal and scripts -->
<div id="trackModal" style="display:none;position:fixed;inset:0;z-index:60;align-items:center;justify-content:center;background:rgba(0,0,0,.6);">
    <div style="width:90%;max-width:900px;height:600px;background:white;border-radius:12px;overflow:hidden;position:relative;">
        <button onclick="closeTrackModal()" style="position:absolute;right:12px;top:12px;z-index:50;background:#fff;border-radius:8px;padding:8px;">Close</button>
        <div id="trackMap" style="width:100%;height:100%;"></div>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const orders = @json($ordersEnriched ?? []);
    let map, trackMap, trackMarker;

    function showTab(tab) {
        document.getElementById('tab-list').style.display = tab === 'list' ? '' : 'none';
        document.getElementById('tab-create').style.display = tab === 'create' ? '' : 'none';
        document.getElementById('tab-track').style.display = tab === 'track' ? '' : 'none';
        if (tab === 'track') initMap();
    }

    function addItem() {
        const container = document.getElementById('items');
        const row = document.createElement('div');
        row.className = 'flex gap-2';
        row.innerHTML = `<input name="item_name[]" placeholder="Item name" required class="flex-1 px-3 py-2 rounded-md bg-surface-container" />` +
                        `<input name="item_qty[]" type="number" min="1" value="1" required class="w-24 px-3 py-2 rounded-md bg-surface-container" />` +
                        `<button type="button" onclick="this.parentNode.remove()" class="px-2">Remove</button>`;
        container.appendChild(row);
    }

    function initMap() {
        if (map) return;
        map = L.map('map').setView([28.0339, 1.6596], 6);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);
    }

    function openTrackModal(order) {
        document.getElementById('trackModal').style.display = 'flex';
        if (!trackMap) {
            trackMap = L.map('trackMap').setView([28.0339,1.6596], 6);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(trackMap);
        }
        const lat = parseFloat(order.dp_lat);
        const lng = parseFloat(order.dp_lng);
        if (!isNaN(lat) && !isNaN(lng)) {
            trackMap.setView([lat,lng], 15);
            if (trackMarker) trackMarker.setLatLng([lat,lng]); else trackMarker = L.marker([lat,lng]).addTo(trackMap);
        } else {
            trackMap.setView([28.0339,1.6596], 6);
            if (trackMarker) { trackMap.removeLayer(trackMarker); trackMarker = null; }
            alert('No GPS location available for this delivery person.');
        }
    }

    function closeTrackModal() { document.getElementById('trackModal').style.display = 'none'; }
</script>
