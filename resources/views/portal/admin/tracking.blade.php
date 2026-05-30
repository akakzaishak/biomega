@php
    $deliveryPersons = $deliveryPersons ?? $drivers ?? [];
    $routeHistory = $routeHistory ?? [];
    $assignedOrders = $assignedOrders ?? [];
    $stats = $stats ?? ['totalDP' => count($deliveryPersons), 'onlineDP' => 0, 'offlineDP' => count($deliveryPersons), 'forcedCount' => 0];
    $dpColors = $dpColors ?? [];
    $forcedMsg = $forced ?? request('forced');
@endphp

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<style>
    .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }
    body {
        font-family: 'Inter', sans-serif;
    }
    .fade-in {
        animation: fadeIn 0.4s ease both;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(12px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .pulse-dot {
        animation: pulseDot 2s ease-in-out infinite;
    }
    @keyframes pulseDot {
        0%, 100% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.6); opacity: .6; }
    }
    .beacon-ring {
        animation: beacon 1.5s ease-out infinite;
    }
    @keyframes beacon {
        0% { transform: scale(1); opacity: .8; }
        100% { transform: scale(2.8); opacity: 0; }
    }
    .dp-card {
        cursor: pointer;
        transition: all .2s;
    }
    .dp-card:hover {
        background: #f0f4ff !important;
    }
    .dp-card.selected {
        border-color: #005ea4 !important;
        background: #eff6ff !important;
    }
    #map {
        height: calc(100vh - 230px);
        min-height: 420px;
        border-radius: 1rem;
        z-index: 1;
    }
    .leaflet-popup-content-wrapper {
        border-radius: 12px !important;
        box-shadow: 0 4px 24px rgba(0, 0, 0, .15) !important;
        padding: 0 !important;
        overflow: hidden;
    }
    .leaflet-popup-content {
        margin: 0 !important;
    }
    .force-btn-pulse {
        animation: forceBtn 2s ease-in-out infinite;
    }
    @keyframes forceBtn {
        0%, 100% { box-shadow: 0 0 0 0 rgba(186,26,26,.4); }
        70% { box-shadow: 0 0 0 8px rgba(186,26,26,0); }
    }
    #toast {
        transition: opacity .4s, transform .4s;
    }
</style>

@if ($forcedMsg)
    <div id="forced-toast" style="position:fixed;top:80px;right:24px;z-index:9999;background:#005ea4;color:white;padding:12px 20px;border-radius:14px;font-size:13px;font-weight:700;box-shadow:0 4px 20px rgba(0,0,0,.2);display:flex;align-items:center;gap:8px;">
        <span class="material-symbols-outlined text-lg" style="font-variation-settings:'FILL' 1;">gps_fixed</span>
        GPS request sent to {{ $forcedMsg }}
    </div>
    <script>
        setTimeout(() => {
            const toast = document.getElementById('forced-toast');
            if (toast) toast.remove();
        }, 4000);
    </script>
@endif

<header class="bg-white/80 backdrop-blur-lg shadow-sm shadow-blue-500/5 sticky top-0 z-50 flex justify-between items-center px-6 py-3 w-full">
    <div class="flex items-center gap-8">
        <span class="text-xl font-extrabold tracking-tighter text-blue-800 font-headline">TronSport Medicamon</span>
        <nav class="hidden md:flex items-center gap-6">
            <a class="text-slate-500 font-medium hover:text-blue-600 transition-colors" href="{{ route('admin.dashboard') }}">Dashboard</a>
            <a class="text-slate-500 font-medium hover:text-blue-600 transition-colors" href="{{ route('admin.orders') }}">Orders</a>
            <a class="text-blue-700 font-bold border-b-2 border-blue-600 px-1 py-1" href="{{ route('admin.tracking') }}">Tracking</a>
        </nav>
    </div>
    <div class="flex items-center gap-3">
        <div class="hidden sm:flex items-center gap-2 bg-tertiary/10 text-tertiary px-3 py-1.5 rounded-full text-xs font-bold">
            <span class="w-2 h-2 rounded-full bg-tertiary pulse-dot inline-block"></span>
            Live · <span id="last-refresh">{{ now()->format('H:i:s') }}</span>
        </div>
        @if (($stats['forcedCount'] ?? 0) > 0)
            <div class="flex items-center gap-2 bg-error/10 text-error px-3 py-1.5 rounded-full text-xs font-bold">
                <span class="material-symbols-outlined text-sm" style="font-variation-settings:'FILL' 1;">gps_fixed</span>
                {{ $stats['forcedCount'] }} GPS request{{ (int) $stats['forcedCount'] > 1 ? 's' : '' }} pending
            </div>
        @endif
        <button onclick="location.reload()" class="p-2 hover:bg-slate-50 rounded-full active:scale-95 transition-colors" type="button" title="Refresh">
            <span class="material-symbols-outlined text-slate-600">refresh</span>
        </button>
        <a href="{{ route('logout') }}" class="p-2 hover:bg-slate-50 rounded-full active:scale-95 transition-colors" title="Logout">
            <span class="material-symbols-outlined text-slate-600">logout</span>
        </a>
    </div>
</header>

<div class="flex min-h-screen">
    <aside class="bg-slate-50 h-screen w-64 border-r border-slate-200 flex flex-col gap-2 p-4 fixed left-0 top-[60px] hidden lg:flex">
        <div class="mb-4 px-2">
            <h3 class="font-headline font-bold text-blue-900">Admin Portal</h3>
            <p class="text-xs text-on-surface-variant">{{ $userName ?? 'Admin' }} • Operational</p>
        </div>
        <nav class="flex-1 flex flex-col gap-1">
            <a href="{{ route('admin.dashboard') }}" class="text-slate-600 hover:bg-slate-100 flex items-center gap-3 px-3 py-2.5 rounded-lg hover:translate-x-1 transition-transform">
                <span class="material-symbols-outlined">dashboard</span><span class="text-sm">Dashboard</span>
            </a>
            <a href="{{ route('admin.pharmacies') }}" class="text-slate-600 hover:bg-slate-100 flex items-center gap-3 px-3 py-2.5 rounded-lg hover:translate-x-1 transition-transform">
                <span class="material-symbols-outlined">local_pharmacy</span><span class="text-sm">Pharmacies</span>
            </a>
            <a href="{{ route('admin.employees') }}" class="text-slate-600 hover:bg-slate-100 flex items-center gap-3 px-3 py-2.5 rounded-lg hover:translate-x-1 transition-transform">
                <span class="material-symbols-outlined">badge</span><span class="text-sm">Employees</span>
            </a>
            <a href="{{ route('admin.orders') }}" class="text-slate-600 hover:bg-slate-100 flex items-center gap-3 px-3 py-2.5 rounded-lg hover:translate-x-1 transition-transform">
                <span class="material-symbols-outlined">package_2</span><span class="text-sm">Orders</span>
            </a>
            <a href="{{ route('admin.payments') }}" class="text-slate-600 hover:bg-slate-100 flex items-center gap-3 px-3 py-2.5 rounded-lg hover:translate-x-1 transition-transform">
                <span class="material-symbols-outlined">payments</span><span class="text-sm">Payments</span>
            </a>
            <a href="{{ route('admin.tracking') }}" class="bg-blue-50 text-blue-700 rounded-lg font-bold flex items-center gap-3 px-3 py-2.5 hover:translate-x-1 transition-transform">
                <span class="material-symbols-outlined">local_shipping</span><span class="text-sm">Tracking</span>
            </a>
            <a href="{{ route('admin.settings') }}" class="text-slate-600 hover:bg-slate-100 flex items-center gap-3 px-3 py-2.5 rounded-lg hover:translate-x-1 transition-transform">
                <span class="material-symbols-outlined">settings</span><span class="text-sm">Settings</span>
            </a>
            <a href="{{ route('logout') }}" class="text-red-500 hover:bg-red-50 flex items-center gap-3 px-3 py-2.5 rounded-lg hover:translate-x-1 transition-transform mt-2">
                <span class="material-symbols-outlined">logout</span><span class="text-sm font-bold">Logout</span>
            </a>
        </nav>
    </aside>

    <main class="flex-1 lg:ml-64 p-4 lg:p-6 space-y-5 bg-surface">
        @if (session('success'))
            <div class="rounded-2xl border border-green-200 bg-green-50 text-green-800 px-4 py-3 text-sm font-medium">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="rounded-2xl border border-red-200 bg-red-50 text-red-800 px-4 py-3 text-sm font-medium">{{ session('error') }}</div>
        @endif

        <div class="fade-in flex items-center justify-between flex-wrap gap-3">
            <div>
                <h1 class="font-headline text-3xl font-extrabold tracking-tight text-on-surface">Live Tracking</h1>
                <p class="text-on-surface-variant font-medium mt-0.5">Real-time positions & route history. Force GPS if a driver is offline.</p>
            </div>
            <div class="flex gap-3 flex-wrap">
                <div class="bg-surface-container-lowest border border-outline-variant/15 rounded-xl px-4 py-2 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-tertiary pulse-dot"></span>
                    <span class="text-sm font-bold text-tertiary">{{ number_format((int) ($stats['onlineDP'] ?? 0)) }} Online</span>
                </div>
                <div class="bg-surface-container-lowest border border-outline-variant/15 rounded-xl px-4 py-2 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-slate-400"></span>
                    <span class="text-sm font-bold text-slate-500">{{ number_format((int) ($stats['offlineDP'] ?? 0)) }} Offline</span>
                </div>
                @if (($stats['forcedCount'] ?? 0) > 0)
                    <div class="bg-error/10 border border-error/20 rounded-xl px-4 py-2 flex items-center gap-2">
                        <span class="material-symbols-outlined text-error text-base" style="font-variation-settings:'FILL' 1;">gps_fixed</span>
                        <span class="text-sm font-bold text-error">{{ number_format((int) $stats['forcedCount']) }} Awaiting GPS</span>
                    </div>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-[360px_1fr] gap-5 fade-in">
            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm flex flex-col overflow-hidden">
                <div class="p-4 border-b border-outline-variant/10">
                    <h2 class="font-headline font-bold text-base text-on-surface">Delivery Personnel</h2>
                    <p class="text-xs text-on-surface-variant mt-0.5">Click card to focus map · Use Force GPS for offline drivers</p>
                </div>

                @if (empty($deliveryPersons))
                    <div class="flex flex-col items-center py-16 text-on-surface-variant">
                        <span class="material-symbols-outlined text-5xl opacity-20 mb-3">local_shipping</span>
                        <p class="font-semibold">No delivery personnel yet</p>
                        <a href="{{ route('admin.employees') }}" class="mt-3 text-xs text-primary font-bold hover:underline">Add one →</a>
                    </div>
                @else
                    <div class="flex-1 overflow-y-auto divide-y divide-outline-variant/10 max-h-[calc(100vh-320px)]">
                        @foreach ($deliveryPersons as $dp)
                            @php
                                $phone = $dp['PhoneNumber'];
                                $hasLoc = !empty($dp['Latitude']) && !empty($dp['Longitude']);
                                $isOnline = $hasLoc && !empty($dp['UpdatedAt']) && strtotime($dp['UpdatedAt']) > time() - 600;
                                $isForced = !empty($dp['GpsForced']);
                                $color = $dpColors[$phone] ?? '#005ea4';
                                $orders = $assignedOrders[$phone] ?? [];
                                $urgentCnt = count(array_filter($orders, fn ($order) => (int) ($order['IsUrgen'] ?? 0) === 1));
                            @endphp
                            <div class="dp-card p-4 border border-transparent rounded-xl mx-2 my-1 {{ $isForced && !$isOnline ? 'bg-red-50/60' : '' }}"
                                 data-phone="{{ $phone }}"
                                 data-lat="{{ $hasLoc ? $dp['Latitude'] : '' }}"
                                 data-lng="{{ $hasLoc ? $dp['Longitude'] : '' }}"
                                 data-name="{{ $dp['FirstName'].' '.$dp['LastName'] }}"
                                 onclick="focusDriver(this)">
                                <div class="flex items-center gap-3">
                                    <div class="relative flex-shrink-0">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm" style="background:{{ $color }}">
                                            {{ strtoupper(substr($dp['FirstName'], 0, 1) . substr($dp['LastName'], 0, 1)) }}
                                        </div>
                                        @if ($isForced && !$isOnline)
                                            <div class="absolute inset-0 rounded-full border-2 border-error beacon-ring"></div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <p class="font-bold text-sm text-on-surface truncate">{{ $dp['FirstName'].' '.$dp['LastName'] }}</p>
                                            @if ($urgentCnt > 0)
                                                <span class="text-[9px] font-black bg-error text-white px-1.5 py-0.5 rounded uppercase">{{ $urgentCnt }} URGENT</span>
                                            @endif
                                            @if ($isForced && !$isOnline)
                                                <span class="text-[9px] font-black bg-error/10 text-error px-1.5 py-0.5 rounded uppercase flex items-center gap-0.5"><span class="material-symbols-outlined text-[10px]">gps_fixed</span>GPS Requested</span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-on-surface-variant">{{ $phone }}</p>
                                    </div>
                                    <span class="flex items-center gap-1 text-[10px] font-bold flex-shrink-0 {{ $isOnline ? 'text-tertiary' : 'text-slate-400' }}">
                                        <span class="w-2 h-2 rounded-full {{ $isOnline ? 'bg-tertiary pulse-dot' : 'bg-slate-300' }}"></span>
                                        {{ $isOnline ? 'Online' : 'Offline' }}
                                    </span>
                                </div>

                                @if ($hasLoc)
                                    <div class="mt-2 flex items-center gap-1.5 text-[10px] text-on-surface-variant bg-surface-container-low rounded-lg px-2 py-1">
                                        <span class="material-symbols-outlined text-[13px]" style="font-variation-settings:'FILL' 1;">location_on</span>
                                        <span>{{ number_format((float) $dp['Latitude'], 5) }}, {{ number_format((float) $dp['Longitude'], 5) }}</span>
                                        <span class="ml-auto font-semibold">{{ !empty($dp['UpdatedAt']) ? date('H:i', strtotime($dp['UpdatedAt'])) : '—' }}</span>
                                    </div>
                                @else
                                    <div class="mt-2 flex items-center gap-1.5 text-[10px] text-slate-400 bg-slate-50 rounded-lg px-2 py-1">
                                        <span class="material-symbols-outlined text-[13px]">location_off</span>
                                        <span>No location data yet</span>
                                        @if ($isForced)
                                            <span class="ml-auto text-error font-bold">Waiting for response…</span>
                                        @endif
                                    </div>
                                @endif

                                @if (!empty($orders))
                                    <div class="mt-2 space-y-1">
                                        @foreach (array_slice($orders, 0, 2) as $order)
                                            <div class="flex items-center justify-between text-[10px] font-medium bg-surface-container-low rounded px-2 py-1">
                                                <span class="flex items-center gap-1">
                                                    @if (!empty($order['IsUrgen']))
                                                        <span class="material-symbols-outlined text-error text-[11px]">priority_high</span>
                                                    @endif
                                                    #{{ $order['order_id'] }}
                                                </span>
                                                <span class="text-on-surface-variant truncate max-w-[110px]">{{ trim(($order['ph_first'] ?? '') . ' ' . ($order['ph_last'] ?? '')) }}</span>
                                            </div>
                                        @endforeach
                                        @if (count($orders) > 2)
                                            <p class="text-[10px] text-primary font-bold text-right">+{{ count($orders) - 2 }} more</p>
                                        @endif
                                    </div>
                                @endif

                                @if (!$isOnline)
                                    <form method="POST" action="{{ route('admin.tracking') }}" onsubmit="return confirmForce('{{ $dp['FirstName'].' '.$dp['LastName'] }}')" onclick="event.stopPropagation()">
                                        @csrf
                                        <input type="hidden" name="force_gps_phone" value="{{ $phone }}" />
                                        <button type="submit"
                                            class="mt-3 w-full flex items-center justify-center gap-2 px-3 py-2 rounded-xl text-xs font-black uppercase tracking-wide transition-all active:scale-95 {{ $isForced ? 'bg-orange-100 text-orange-600 border border-orange-300 hover:bg-orange-200' : 'bg-error text-white hover:bg-red-700 force-btn-pulse' }}">
                                            <span class="material-symbols-outlined text-sm" style="font-variation-settings:'FILL' 1;">gps_fixed</span>
                                            {{ $isForced ? 'Re-send GPS Request' : 'Force GPS Activation' }}
                                        </button>
                                    </form>
                                @else
                                    <div class="mt-3 flex items-center gap-2 justify-center text-[10px] font-bold text-tertiary bg-tertiary/5 rounded-xl py-2">
                                        <span class="material-symbols-outlined text-sm" style="font-variation-settings:'FILL' 1;">gps_fixed</span>
                                        GPS Active — tracking live
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="p-3 border-t border-outline-variant/10 bg-surface-container-low">
                    <div class="flex items-center gap-4 text-[10px] font-semibold text-on-surface-variant flex-wrap">
                        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-tertiary"></span>Online</span>
                        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-slate-300"></span>Offline</span>
                        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-error"></span>GPS Forced</span>
                        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-primary"></span>Route</span>
                    </div>
                </div>
            </div>

            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm overflow-hidden flex flex-col">
                <div class="p-3 border-b border-outline-variant/10 flex items-center gap-3 flex-wrap">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-lg">map</span>
                        <span class="font-headline font-bold text-sm text-on-surface">Live Map</span>
                    </div>
                    <div class="flex gap-2 ml-auto flex-wrap">
                        <button onclick="showAllDrivers()" class="text-xs font-bold bg-surface-container border border-outline-variant/20 px-3 py-1.5 rounded-lg hover:bg-surface-container-high transition-colors flex items-center gap-1" type="button">
                            <span class="material-symbols-outlined text-[14px]">group</span>Show All
                        </button>
                        <button onclick="toggleRoutes()" id="route-toggle-btn" class="text-xs font-bold bg-primary/10 text-primary border border-primary/20 px-3 py-1.5 rounded-lg hover:bg-primary/20 transition-colors flex items-center gap-1" type="button">
                            <span class="material-symbols-outlined text-[14px]">route</span>Routes ON
                        </button>
                        <button onclick="toggleSatellite()" class="text-xs font-bold bg-surface-container border border-outline-variant/20 px-3 py-1.5 rounded-lg hover:bg-surface-container-high transition-colors flex items-center gap-1" type="button">
                            <span class="material-symbols-outlined text-[14px]">satellite</span>Satellite
                        </button>
                    </div>
                </div>
                <div id="map" class="flex-1 rounded-b-2xl"></div>
            </div>
        </div>
    </main>
</div>

<nav class="md:hidden fixed bottom-0 left-0 w-full z-50 flex justify-around items-center px-4 pb-6 pt-3 bg-white/90 backdrop-blur-xl border-t border-slate-200">
    <a href="{{ route('admin.dashboard') }}" class="flex flex-col items-center text-slate-400">
        <span class="material-symbols-outlined">grid_view</span>
        <span class="text-[10px] font-semibold uppercase">Dashboard</span>
    </a>
    <a href="{{ route('admin.orders') }}" class="flex flex-col items-center text-slate-400">
        <span class="material-symbols-outlined">auto_stories</span>
        <span class="text-[10px] font-semibold uppercase">Orders</span>
    </a>
    <a href="{{ route('admin.tracking') }}" class="flex flex-col items-center bg-blue-100 text-blue-800 rounded-xl px-3 py-1.5">
        <span class="material-symbols-outlined">local_shipping</span>
        <span class="text-[10px] font-semibold uppercase">Tracking</span>
    </a>
    <a href="{{ route('admin.settings') }}" class="flex flex-col items-center text-slate-400">
        <span class="material-symbols-outlined">settings</span>
        <span class="text-[10px] font-semibold uppercase">Settings</span>
    </a>
</nav>

@push('scripts')
<script>
const DELIVERY_PERSONS = @json($deliveryPersons);
const ROUTE_HISTORY = @json($routeHistory);
const ASSIGNED_ORDERS = @json($assignedOrders);
const DP_COLORS = @json($dpColors);

const DEFAULT_CENTER = [36.1898, 5.4135];
let map, tileNormal, tileSatellite;
let markers = {}, routeLines = {};
let showRoutes = true, isSatellite = false;

function initMap() {
  map = L.map('map', { zoomControl: true }).setView(DEFAULT_CENTER, 12);
  tileNormal = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors', maxZoom: 19
  }).addTo(map);
  tileSatellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
    attribution: '© Esri', maxZoom: 19
  });
  renderAll();
}

function makeDriverIcon(color, initials, isOnline, isForced) {
  const ring = isForced && !isOnline
    ? `<div style="position:absolute;inset:-4px;border-radius:50%;border:2px solid #ba1a1a;animation:beacon 1.5s ease-out infinite;"></div>` : '';
  const dot = isOnline
    ? `<div style="position:absolute;bottom:-5px;left:50%;transform:translateX(-50%);width:9px;height:9px;border-radius:50%;background:${color};opacity:.5;animation:pulseDot 2s ease-in-out infinite;"></div>` : '';
  return L.divIcon({
    className: '',
    html: `<div style="position:relative;width:40px;height:40px;">
      ${ring}
      <div style="width:40px;height:40px;border-radius:50%;background:${color};border:3px solid white;box-shadow:0 2px 10px rgba(0,0,0,.3);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:13px;font-family:Inter,sans-serif;position:relative;">
        ${initials}${dot}
      </div>
    </div>`,
    iconSize:[40,40], iconAnchor:[20,20], popupAnchor:[0,-24],
  });
}

function renderAll() {
  Object.values(markers).forEach(marker => map.removeLayer(marker));
  Object.values(routeLines).forEach(line => map.removeLayer(line));
  markers = {};
  routeLines = {};
  const bounds = [];

  DELIVERY_PERSONS.forEach(dp => {
    const phone = dp.PhoneNumber;
    const color = DP_COLORS[phone] || '#005ea4';
    const initials = ((dp.FirstName?.[0] || '') + (dp.LastName?.[0] || '')).toUpperCase();
    const hasLoc = dp.Latitude && dp.Longitude;
    const isOnline = hasLoc && dp.UpdatedAt && ((Date.now() / 1000) - (new Date(dp.UpdatedAt).getTime() / 1000)) < 600;
    const isForced = !!dp.GpsForced;
    const orders = ASSIGNED_ORDERS[phone] || [];

    const history = ROUTE_HISTORY[phone];
    if (history && history.length > 1 && showRoutes) {
      routeLines[phone] = L.polyline(history.map(point => [point.lat, point.lng]), {
        color, weight: 3, opacity: .7, dashArray: '6 4'
      }).addTo(map);
    }

    if (!hasLoc) return;
    const lat = parseFloat(dp.Latitude);
    const lng = parseFloat(dp.Longitude);
    bounds.push([lat, lng]);

    const marker = L.marker([lat, lng], { icon: makeDriverIcon(color, initials, isOnline, isForced) }).addTo(map);
    const lastSeen = dp.UpdatedAt ? new Date(dp.UpdatedAt).toLocaleTimeString('fr-DZ', { hour: '2-digit', minute: '2-digit' }) : '—';
    const ordersHtml = orders.map(order => `
      <div style="display:flex;justify-content:space-between;font-size:11px;padding:2px 0;border-bottom:1px solid #f0f0f0">
        <span>${order.IsUrgen ? '🔴' : '📦'} #${order.order_id}</span>
        <span style="color:#666">${order.ph_first || ''} ${order.ph_last || ''}</span>
      </div>`).join('') || '<p style="font-size:11px;color:#999">No active orders</p>';

    const forceBtnHtml = !isOnline
      ? `<form method="POST" action="{{ route('admin.tracking') }}" onsubmit="return confirmForce('${dp.FirstName} ${dp.LastName}')">
           <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
           <input type="hidden" name="force_gps_phone" value="${phone}"/>
           <button type="submit" style="margin-top:10px;width:100%;background:${isForced ? '#fff7ed' : '#ba1a1a'};color:${isForced ? '#c2410c' : 'white'};border:${isForced ? '1px solid #fdba74' : 'none'};border-radius:10px;padding:8px;font-size:12px;font-weight:800;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:6px;letter-spacing:.03em;text-transform:uppercase;">
             📡 ${isForced ? 'Re-send GPS Request' : 'Force GPS Activation'}
           </button>
         </form>` : '';

    const popup = `
      <div style="min-width:230px;font-family:Inter,sans-serif;">
        <div style="background:${color};padding:12px 14px;color:white;">
          <p style="font-weight:800;font-size:14px;margin:0">${dp.FirstName} ${dp.LastName}</p>
          <p style="font-size:11px;margin:2px 0 0;opacity:.85">${phone}</p>
        </div>
        <div style="padding:12px 14px;">
          <div style="display:flex;align-items:center;gap:6px;margin-bottom:8px;">
            <span style="width:8px;height:8px;border-radius:50%;background:${isOnline ? '#186a22' : '#aaa'};display:inline-block"></span>
            <span style="font-size:11px;font-weight:700;color:${isOnline ? '#186a22' : '#999'}">${isOnline ? 'Online' : 'Offline'}</span>
            <span style="font-size:10px;color:#999;margin-left:auto">Last: ${lastSeen}</span>
          </div>
          <p style="font-size:10px;font-weight:700;color:#666;margin:0 0 4px;text-transform:uppercase">Active Orders (${orders.length})</p>
          ${ordersHtml}
          <p style="margin-top:6px;font-size:10px;color:#aaa">📍 ${lat.toFixed(5)}, ${lng.toFixed(5)}</p>
          ${forceBtnHtml}
        </div>
      </div>`;

    marker.bindPopup(popup, { maxWidth: 280 });
    marker.on('click', () => highlightCard(phone));
    markers[phone] = marker;
  });

  if (bounds.length) map.fitBounds(L.latLngBounds(bounds).pad(0.2));
}

function focusDriver(el) {
  const phone = el.dataset.phone;
  const lat = parseFloat(el.dataset.lat);
  const lng = parseFloat(el.dataset.lng);
  document.querySelectorAll('.dp-card').forEach(card => card.classList.remove('selected'));
  el.classList.add('selected');
  if (!Number.isNaN(lat) && !Number.isNaN(lng)) {
    map.flyTo([lat, lng], 15, { duration: 1.2 });
    setTimeout(() => markers[phone] && markers[phone].openPopup(), 1300);
  }
}

function highlightCard(phone) {
  document.querySelectorAll('.dp-card').forEach(card => {
    if (card.dataset.phone === phone) {
      card.classList.add('selected');
      card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    } else {
      card.classList.remove('selected');
    }
  });
}

function showAllDrivers() {
  document.querySelectorAll('.dp-card').forEach(card => card.classList.remove('selected'));
  const bounds = Object.values(markers).map(marker => marker.getLatLng());
  if (bounds.length) map.fitBounds(L.latLngBounds(bounds).pad(0.2));
}

function toggleRoutes() {
  showRoutes = !showRoutes;
  const btn = document.getElementById('route-toggle-btn');
  btn.innerHTML = `<span class="material-symbols-outlined text-[14px]">route</span>Routes ${showRoutes ? 'ON' : 'OFF'}`;
  btn.className = showRoutes
    ? 'text-xs font-bold bg-primary/10 text-primary border border-primary/20 px-3 py-1.5 rounded-lg hover:bg-primary/20 transition-colors flex items-center gap-1'
    : 'text-xs font-bold bg-surface-container border border-outline-variant/20 px-3 py-1.5 rounded-lg hover:bg-surface-container-high transition-colors flex items-center gap-1';
  renderAll();
}

function toggleSatellite() {
  isSatellite = !isSatellite;
  if (isSatellite) {
    map.removeLayer(tileNormal);
    tileSatellite.addTo(map);
  } else {
    map.removeLayer(tileSatellite);
    tileNormal.addTo(map);
  }
}

function confirmForce(name) {
  return confirm(`Send GPS activation request to ${name}?\n\nThey will see a mandatory popup on their screen that they cannot close until GPS is enabled.`);
}

setInterval(() => location.reload(), 30000);
document.addEventListener('DOMContentLoaded', initMap);
</script>
@endpush
