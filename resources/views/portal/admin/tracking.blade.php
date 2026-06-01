@php
    $deliveryPersons = $deliveryPersons ?? $drivers ?? [];
    $routeHistory = $routeHistory ?? [];
    $assignedOrders = $assignedOrders ?? [];
    $stats = $stats ?? ['totalDP' => count($deliveryPersons), 'onlineDP' => 0, 'offlineDP' => count($deliveryPersons), 'forcedCount' => 0];
    $dpColors = $dpColors ?? [];
    $trackingUrl = $trackingUrl ?? route('admin.tracking');
    $forcedMsg = $forcedMsg ?? $forced ?? request('forced');
    $totalDP = $stats['totalDP'] ?? count($deliveryPersons);
    $onlineDP = $stats['onlineDP'] ?? 0;
    $offlineDP = $stats['offlineDP'] ?? max(0, $totalDP - $onlineDP);
    $forcedCount = $stats['forcedCount'] ?? 0;
@endphp

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<style>
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
    body { font-family: 'Inter', sans-serif; }
    .tracking-shell { position: relative; isolation: isolate; }
    .tracking-shell::before,
    .tracking-shell::after {
        content: '';
        position: absolute;
        border-radius: 9999px;
        filter: blur(40px);
        pointer-events: none;
        z-index: -1;
    }
    .tracking-shell::before {
        width: 18rem;
        height: 18rem;
        top: -4rem;
        right: -4rem;
        background: rgba(0, 94, 164, 0.12);
    }
    .tracking-shell::after {
        width: 14rem;
        height: 14rem;
        left: -3rem;
        bottom: 8rem;
        background: rgba(24, 106, 34, 0.08);
    }
    .fade-in { animation: fadeIn 0.4s ease both; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
    .pulse-dot { animation: pulseDot 2s ease-in-out infinite; }
    @keyframes pulseDot { 0%, 100% { transform: scale(1); opacity: 1; } 50% { transform: scale(1.6); opacity: .6; } }
    .beacon-ring { animation: beacon 1.5s ease-out infinite; }
    @keyframes beacon { 0% { transform: scale(1); opacity: .8; } 100% { transform: scale(2.8); opacity: 0; } }
    .dp-card { cursor: pointer; transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease, background-color .18s ease; }
    .dp-card:hover { transform: translateY(-1px); background: #f7faff !important; box-shadow: 0 10px 24px rgba(0, 0, 0, .04); }
    .dp-card.selected { border-color: #005ea4 !important; background: linear-gradient(180deg, #eef6ff 0%, #f7fbff 100%) !important; box-shadow: 0 14px 34px rgba(0, 94, 164, .12); }
    #map { width: 100%; height: clamp(500px, calc(100vh - 240px), 820px); min-height: 500px; border-radius: 1.5rem; z-index: 1; }
    .leaflet-popup-content-wrapper { border-radius: 12px !important; box-shadow: 0 4px 24px rgba(0, 0, 0, .15) !important; padding: 0 !important; overflow: hidden; }
    .leaflet-popup-content { margin: 0 !important; }
    .force-btn-pulse { animation: forceBtn 2s ease-in-out infinite; }
    @keyframes forceBtn { 0%, 100% { box-shadow: 0 0 0 0 rgba(186,26,26,.4); } 70% { box-shadow: 0 0 0 8px rgba(186,26,26,0); } }
    .tracking-stat { background: linear-gradient(180deg, rgba(255,255,255,.96), rgba(248,250,252,.96)); box-shadow: 0 10px 30px rgba(15, 23, 42, .05); }
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

<div class="tracking-shell space-y-6">
    @if (session('success'))
        <div class="rounded-2xl border border-green-200 bg-green-50 text-green-800 px-4 py-3 text-sm font-medium">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="rounded-2xl border border-red-200 bg-red-50 text-red-800 px-4 py-3 text-sm font-medium">{{ session('error') }}</div>
    @endif

    <div class="fade-in overflow-hidden rounded-[1.75rem] border border-outline-variant/15 bg-gradient-to-br from-white via-white to-sky-50 shadow-[0_18px_50px_rgba(15,23,42,.06)] px-6 py-5 md:px-7 md:py-6">
        <div class="flex items-start justify-between flex-wrap gap-4">
            <div class="max-w-2xl">
                <div class="inline-flex items-center gap-2 rounded-full border border-primary/15 bg-primary/5 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.18em] text-primary">
                    <span class="material-symbols-outlined text-[14px]" style="font-variation-settings:'FILL' 1;">radar</span>
                    Fleet visibility
                </div>
                <h1 class="mt-3 font-headline text-3xl md:text-4xl font-extrabold tracking-tight text-on-surface">Live Tracking</h1>
                <p class="mt-2 max-w-2xl text-sm md:text-base text-on-surface-variant font-medium leading-6">Monitor every delivery person in real time, inspect route history, and trigger a GPS request when someone goes offline.</p>
            </div>
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                <div class="tracking-stat min-w-[118px] rounded-2xl border border-outline-variant/15 px-4 py-3">
                    <div class="flex items-center gap-2 text-[11px] font-bold uppercase tracking-wide text-on-surface-variant">
                        <span class="w-2.5 h-2.5 rounded-full bg-tertiary pulse-dot"></span>
                        Online
                    </div>
                    <div class="mt-2 text-2xl font-extrabold tracking-tight text-on-surface">{{ number_format((int) ($stats['onlineDP'] ?? 0)) }}</div>
                </div>
                <div class="tracking-stat min-w-[118px] rounded-2xl border border-outline-variant/15 px-4 py-3">
                    <div class="flex items-center gap-2 text-[11px] font-bold uppercase tracking-wide text-on-surface-variant">
                        <span class="w-2.5 h-2.5 rounded-full bg-slate-400"></span>
                        Offline
                    </div>
                    <div class="mt-2 text-2xl font-extrabold tracking-tight text-on-surface">{{ number_format((int) ($stats['offlineDP'] ?? 0)) }}</div>
                </div>
                <div class="tracking-stat min-w-[118px] rounded-2xl border border-error/20 px-4 py-3 col-span-2 sm:col-span-1">
                    <div class="flex items-center gap-2 text-[11px] font-bold uppercase tracking-wide text-error">
                        <span class="material-symbols-outlined text-[15px]" style="font-variation-settings:'FILL' 1;">gps_fixed</span>
                        Forced
                    </div>
                    <div class="mt-2 text-2xl font-extrabold tracking-tight text-error">{{ number_format((int) ($stats['forcedCount'] ?? 0)) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-[388px_1fr] gap-6 fade-in">
        <div class="overflow-hidden rounded-[1.75rem] border border-outline-variant/15 bg-white shadow-[0_18px_50px_rgba(15,23,42,.06)] flex flex-col">
            <div class="border-b border-outline-variant/10 bg-gradient-to-b from-slate-50 to-white p-5">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="font-headline font-bold text-lg text-on-surface">Delivery Personnel</h2>
                        <p class="text-xs text-on-surface-variant mt-1">Tap a card to center the map or trigger GPS for offline drivers.</p>
                    </div>
                    <div class="rounded-full bg-primary/5 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.16em] text-primary">
                        {{ number_format((int) ($stats['totalDP'] ?? count($deliveryPersons))) }} total
                    </div>
                </div>
            </div>

            @if (empty($deliveryPersons))
                <div class="flex flex-col items-center py-16 text-on-surface-variant">
                    <span class="material-symbols-outlined text-5xl opacity-20 mb-3">local_shipping</span>
                    <p class="font-semibold">No delivery personnel yet</p>
                </div>
            @else
                <div class="flex-1 overflow-y-auto divide-y divide-outline-variant/10 max-h-[calc(100vh-300px)] px-2 py-2">
                    @foreach ($deliveryPersons as $dp)
                        @php
                            $phone = (string) ($dp['PhoneNumber'] ?? '');
                            $hasLoc = !empty($dp['Latitude']) && !empty($dp['Longitude']);
                            $isOnline = $hasLoc && !empty($dp['UpdatedAt']) && strtotime((string) $dp['UpdatedAt']) > time() - 600;
                            $isForced = !empty($dp['GpsForced']);
                            $color = $dpColors[$phone] ?? '#005ea4';
                            $orders = $assignedOrders[$phone] ?? [];
                            $urgentCnt = count(array_filter($orders, fn ($order) => (int) ($order['IsUrgen'] ?? 0) === 1));
                        @endphp
                        <div class="dp-card p-4 border border-transparent rounded-2xl mx-2 my-2 {{ $isForced && !$isOnline ? 'bg-red-50/60' : 'bg-white' }}" data-phone="{{ $phone }}" data-lat="{{ $hasLoc ? $dp['Latitude'] : '' }}" data-lng="{{ $hasLoc ? $dp['Longitude'] : '' }}" data-name="{{ $dp['FirstName'].' '.$dp['LastName'] }}" onclick="focusDriver(this)">
                            <div class="flex items-start gap-3">
                                <div class="relative flex-shrink-0">
                                    <div class="w-11 h-11 rounded-2xl flex items-center justify-center text-white font-bold text-sm shadow-sm" style="background:{{ $color }}">{{ strtoupper(substr((string) ($dp['FirstName'] ?? ''), 0, 1) . substr((string) ($dp['LastName'] ?? ''), 0, 1)) }}</div>
                                    @if ($isForced && !$isOnline)
                                        <div class="absolute inset-0 rounded-full border-2 border-error beacon-ring"></div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0 space-y-1">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="min-w-0">
                                            <p class="font-bold text-sm text-on-surface truncate">{{ trim((string) ($dp['FirstName'] ?? '') . ' ' . (string) ($dp['LastName'] ?? '')) }}</p>
                                            <p class="text-xs text-on-surface-variant truncate">{{ $phone }}</p>
                                        </div>
                                        @if ($urgentCnt > 0)
                                            <span class="text-[9px] font-black bg-error text-white px-1.5 py-0.5 rounded-full uppercase whitespace-nowrap">{{ $urgentCnt }} urgent</span>
                                        @endif
                                    </div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="flex items-center gap-1 text-[10px] font-bold flex-shrink-0 rounded-full px-2 py-1 {{ $isOnline ? 'bg-tertiary/10 text-tertiary' : 'bg-slate-100 text-slate-500' }}">
                                            <span class="w-2 h-2 rounded-full {{ $isOnline ? 'bg-tertiary pulse-dot' : 'bg-slate-300' }}"></span>
                                            {{ $isOnline ? 'Online' : 'Offline' }}
                                        </span>
                                        @if ($isForced && !$isOnline)
                                            <span class="text-[9px] font-black bg-error/10 text-error px-2 py-1 rounded-full uppercase flex items-center gap-0.5"><span class="material-symbols-outlined text-[10px]">gps_fixed</span>GPS requested</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @if ($hasLoc)
                                <div class="mt-3 flex items-center gap-1.5 text-[10px] text-on-surface-variant bg-surface-container-low rounded-xl px-3 py-2">
                                    <span class="material-symbols-outlined text-[13px]" style="font-variation-settings:'FILL' 1;">location_on</span>
                                    <span>{{ number_format((float) $dp['Latitude'], 5) }}, {{ number_format((float) $dp['Longitude'], 5) }}</span>
                                    <span class="ml-auto font-semibold">{{ !empty($dp['UpdatedAt']) ? date('H:i', strtotime((string) $dp['UpdatedAt'])) : '—' }}</span>
                                </div>
                            @else
                                <div class="mt-3 flex items-center gap-1.5 text-[10px] text-slate-400 bg-slate-50 rounded-xl px-3 py-2">
                                    <span class="material-symbols-outlined text-[13px]">location_off</span>
                                    <span>No location data yet</span>
                                    @if ($isForced)
                                        <span class="ml-auto text-error font-bold">Waiting for response…</span>
                                    @endif
                                </div>
                            @endif

                            @if (!empty($orders))
                                <div class="mt-3 space-y-1">
                                    @foreach (array_slice($orders, 0, 2) as $order)
                                        <div class="flex items-center justify-between text-[10px] font-medium bg-surface-container-low rounded-xl px-3 py-2">
                                            <span class="flex items-center gap-1">@if(!empty($order['IsUrgen']))<span class="material-symbols-outlined text-error text-[11px]">priority_high</span>@endif #{{ $order['order_id'] }}</span>
                                            <span class="text-on-surface-variant truncate max-w-[110px]">{{ trim((string) ($order['ph_first'] ?? '') . ' ' . (string) ($order['ph_last'] ?? '')) }}</span>
                                        </div>
                                    @endforeach
                                    @if (count($orders) > 2)
                                        <p class="text-[10px] text-primary font-bold text-right">+{{ count($orders) - 2 }} more</p>
                                    @endif
                                </div>
                            @endif

                            @if (!$isOnline)
                                <form method="POST" action="{{ route('admin.tracking') }}" onsubmit="return confirmForce('{{ trim((string) ($dp['FirstName'] ?? '') . ' ' . (string) ($dp['LastName'] ?? '')) }}')" onclick="event.stopPropagation()">
                                    @csrf
                                    <input type="hidden" name="force_gps_phone" value="{{ $phone }}" />
                                    <button type="submit" class="mt-4 w-full flex items-center justify-center gap-2 px-3 py-2.5 rounded-2xl text-xs font-black uppercase tracking-wide transition-all active:scale-95 {{ $isForced ? 'bg-orange-100 text-orange-600 border border-orange-300 hover:bg-orange-200' : 'bg-error text-white hover:bg-red-700 force-btn-pulse shadow-[0_8px_24px_rgba(186,26,26,.22)]' }}">
                                        <span class="material-symbols-outlined text-sm" style="font-variation-settings:'FILL' 1;">gps_fixed</span>
                                        {{ $isForced ? 'Re-send GPS Request' : 'Force GPS Activation' }}
                                    </button>
                                </form>
                            @else
                                <div class="mt-4 flex items-center gap-2 justify-center text-[10px] font-bold text-tertiary bg-tertiary/5 rounded-2xl py-2.5">
                                    <span class="material-symbols-outlined text-sm" style="font-variation-settings:'FILL' 1;">gps_fixed</span>
                                    GPS Active — tracking live
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="p-4 border-t border-outline-variant/10 bg-slate-50/80">
                <div class="flex items-center gap-4 text-[10px] font-semibold text-on-surface-variant flex-wrap">
                    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-tertiary"></span>Online</span>
                    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-slate-300"></span>Offline</span>
                    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-error"></span>GPS Forced</span>
                    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-primary"></span>Route</span>
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-[1.75rem] border border-outline-variant/15 bg-white shadow-[0_18px_50px_rgba(15,23,42,.06)] flex flex-col">
            <div class="border-b border-outline-variant/10 bg-gradient-to-r from-slate-50 to-white p-4 md:p-5 flex items-center gap-3 flex-wrap">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-primary/10 text-primary flex items-center justify-center">
                        <span class="material-symbols-outlined text-lg">map</span>
                    </div>
                    <div>
                        <span class="font-headline font-bold text-sm md:text-base text-on-surface block">Live Map</span>
                        <span class="text-xs text-on-surface-variant">Pan, zoom, and tap a driver for details</span>
                    </div>
                </div>
                <div class="flex gap-2 ml-auto flex-wrap">
                    <button onclick="showAllDrivers()" class="text-xs font-bold bg-surface-container border border-outline-variant/20 px-3 py-1.5 rounded-lg hover:bg-surface-container-high transition-colors flex items-center gap-1" type="button">
                        <span class="material-symbols-outlined text-[14px]">group</span>Show All
                    </button>
                    <button onclick="toggleRoutes()" id="route-toggle-btn" class="text-xs font-bold bg-primary/10 text-primary border border-primary/20 px-3 py-1.5 rounded-lg hover:bg-primary/20 transition-colors flex items-center gap-1" type="button">
                        <span class="material-symbols-outlined text-[14px]">route</span>Routes ON
                    </button>
                    <button onclick="toggleSatellite()" class="text-xs font-bold bg-surface-container border border-outline-variant/20 px-3 py-1.5 rounded-lg hover:bg-surface-container-high transition-colors flex items-center gap-1" type="button">
                        <span class="material-symbols-outlined text-[14px]">map</span>Street
                    </button>
                </div>
            </div>
            <div id="map" class="flex-1"></div>
        </div>
    </div>
</div>

<script>
const DELIVERY_PERSONS = @json($deliveryPersons);
const ROUTE_HISTORY = @json($routeHistory);
const ASSIGNED_ORDERS = @json($assignedOrders);
const DP_COLORS = @json($dpColors);
const TRACKING_FORCE_URL = @json($trackingUrl);
const CSRF_TOKEN = @json(csrf_token());

const DEFAULT_CENTER = [36.1898, 5.4135];
let map, tileNormal, tileSatellite;
let markers = {}, routeLines = {};
let showRoutes = true, isSatellite = false;

function initMap() {
  map = L.map('map', { zoomControl: true }).setView(DEFAULT_CENTER, 12);
  const imageryUrl = 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}';
  const streetUrl = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
  const cartoUrl = 'https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}.png';
  tileNormal = L.tileLayer(imageryUrl, { attribution: '© Esri', maxZoom: 19 });
  let tileErrorCount = 0;

  const debugOverlay = L.DomUtil.create('div', 'map-tile-debug');
  Object.assign(debugOverlay.style, {
    position: 'absolute', right: '12px', top: '12px', zIndex: 9999,
    background: 'rgba(255,255,255,0.85)', padding: '6px 10px', borderRadius: '8px',
    fontSize: '12px', color: '#111', boxShadow: '0 4px 18px rgba(0,0,0,.12)', display: 'none'
  });
  document.getElementById('map').appendChild(debugOverlay);

  tileNormal.on('tileerror', function (err) {
    tileErrorCount++;
    console.warn('Tile error', err);
    debugOverlay.style.display = 'block';
    debugOverlay.innerText = `Tile errors: ${tileErrorCount}`;
    if (tileErrorCount === 1) {
      try { map.removeLayer(tileNormal); } catch (e) {}
      tileNormal = L.tileLayer(streetUrl, { attribution: '© OpenStreetMap contributors', maxZoom: 19, subdomains: ['a', 'b', 'c'] }).addTo(map);
      console.info('Switched to OpenStreetMap tiles');
    } else if (tileErrorCount > 3) {
      try { map.removeLayer(tileNormal); } catch (e) {}
      tileNormal = L.tileLayer(cartoUrl, { attribution: '© Carto, © OpenStreetMap', maxZoom: 19 }).addTo(map);
      console.info('Switched to Carto tiles');
    }
  });

  tileNormal.addTo(map);
  tileSatellite = L.tileLayer(streetUrl, { attribution: '© OpenStreetMap contributors', maxZoom: 19, subdomains: ['a', 'b', 'c'] });
  renderAll();
  requestAnimationFrame(() => map.invalidateSize());
  setTimeout(() => map.invalidateSize(), 250);
}

function makeDriverIcon(color, initials, isOnline, isForced) {
  const ring = isForced && !isOnline ? `<div style="position:absolute;inset:-4px;border-radius:50%;border:2px solid #ba1a1a;animation:beacon 1.5s ease-out infinite;"></div>` : '';
  const dot = isOnline ? `<div style="position:absolute;bottom:-5px;left:50%;transform:translateX(-50%);width:9px;height:9px;border-radius:50%;background:${color};opacity:.5;animation:pulse-dot 2s ease-in-out infinite;"></div>` : '';
  return L.divIcon({
    className: '',
    html: `<div style="position:relative;width:40px;height:40px;">${ring}<div style="width:40px;height:40px;border-radius:50%;background:${color};border:3px solid white;box-shadow:0 2px 10px rgba(0,0,0,.3);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:13px;font-family:Inter,sans-serif;position:relative;">${initials}${dot}</div></div>`,
    iconSize: [40, 40], iconAnchor: [20, 20], popupAnchor: [0, -24],
  });
}

function renderAll() {
  Object.values(markers).forEach(m => map.removeLayer(m));
  Object.values(routeLines).forEach(l => map.removeLayer(l));
  markers = {};
  routeLines = {};
  const bounds = [];

  DELIVERY_PERSONS.forEach(dp => {
    const phone = String(dp.PhoneNumber || '');
    const color = DP_COLORS[phone] || '#005ea4';
    const initials = String(dp.FirstName || '').charAt(0) + String(dp.LastName || '').charAt(0);
    const hasLoc = !!(dp.Latitude && dp.Longitude);
    const isOnline = hasLoc && dp.UpdatedAt && ((Date.now() / 1000) - (new Date(dp.UpdatedAt).getTime() / 1000)) < 600;
    const isForced = !!dp.GpsForced;
    const orders = ASSIGNED_ORDERS[phone] || [];

    const history = ROUTE_HISTORY[phone];
    if (history && history.length > 1 && showRoutes) {
      routeLines[phone] = L.polyline(history.map(p => [p.lat, p.lng]), { color, weight: 3, opacity: .7, dashArray: '6 4' }).addTo(map);
    }

    if (!hasLoc) return;
    const lat = parseFloat(dp.Latitude), lng = parseFloat(dp.Longitude);
    bounds.push([lat, lng]);

    const icon = makeDriverIcon(color, initials.toUpperCase(), isOnline, isForced);
    const marker = L.marker([lat, lng], { icon }).addTo(map);

    const lastSeen = dp.UpdatedAt ? new Date(dp.UpdatedAt).toLocaleTimeString('fr-DZ', { hour: '2-digit', minute: '2-digit' }) : '—';
    const ordersHtml = orders.map(o => `<div style="display:flex;justify-content:space-between;font-size:11px;padding:2px 0;border-bottom:1px solid #f0f0f0"><span>${o.IsUrgen ? '🔴' : '📦'} #${o.order_id}</span><span style="color:#666">${o.ph_first || ''} ${o.ph_last || ''}</span></div>`).join('') || '<p style="font-size:11px;color:#999">No active orders</p>';

    const safeName = `${String(dp.FirstName || '')} ${String(dp.LastName || '')}`.trim().replace(/'/g, "\\'");
    const forceBtnHtml = !isOnline ? `<form method="POST" action="${TRACKING_FORCE_URL}" onsubmit="return confirmForce('${safeName}')"><input type="hidden" name="_token" value="${CSRF_TOKEN}"/><input type="hidden" name="force_gps_phone" value="${phone}"/><button type="submit" style="margin-top:10px;width:100%;background:${isForced ? '#fff7ed' : '#ba1a1a'};color:${isForced ? '#c2410c' : 'white'};border:${isForced ? '1px solid #fdba74' : 'none'};border-radius:10px;padding:8px;font-size:12px;font-weight:800;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:6px;letter-spacing:.03em;text-transform:uppercase;">📡 ${isForced ? 'Re-send GPS Request' : 'Force GPS Activation'}</button></form>` : '';

    const popup = `<div style="min-width:230px;font-family:Inter,sans-serif;"><div style="background:${color};padding:12px 14px;color:white;"><p style="font-weight:800;font-size:14px;margin:0">${dp.FirstName || ''} ${dp.LastName || ''}</p><p style="font-size:11px;margin:2px 0 0;opacity:.85">${phone}</p></div><div style="padding:12px 14px;"><div style="display:flex;align-items:center;gap:6px;margin-bottom:8px;"><span style="width:8px;height:8px;border-radius:50%;background:${isOnline ? '#186a22' : '#aaa'};display:inline-block"></span><span style="font-size:11px;font-weight:700;color:${isOnline ? '#186a22' : '#999'}">${isOnline ? 'Online' : 'Offline'}</span><span style="font-size:10px;color:#999;margin-left:auto">Last: ${lastSeen}</span></div><p style="font-size:10px;font-weight:700;color:#666;margin:0 0 4px;text-transform:uppercase">Active Orders (${orders.length})</p>${ordersHtml}<p style="margin-top:6px;font-size:10px;color:#aaa">📍 ${lat.toFixed(5)}, ${lng.toFixed(5)}</p>${forceBtnHtml}</div></div>`;

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
  document.querySelectorAll('.dp-card').forEach(c => c.classList.remove('selected'));
  el.classList.add('selected');
  if (!Number.isNaN(lat) && !Number.isNaN(lng)) {
    map.flyTo([lat, lng], 15, { duration: 1.2 });
    setTimeout(() => markers[phone] && markers[phone].openPopup(), 1300);
  }
}

function highlightCard(phone) {
  document.querySelectorAll('.dp-card').forEach(c => {
    if (c.dataset.phone === phone) {
      c.classList.add('selected');
      c.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    } else {
      c.classList.remove('selected');
    }
  });
}

function showAllDrivers() {
  document.querySelectorAll('.dp-card').forEach(c => c.classList.remove('selected'));
  const bounds = Object.values(markers).map(m => m.getLatLng());
  if (bounds.length) map.fitBounds(L.latLngBounds(bounds).pad(0.2));
}

function toggleRoutes() {
  showRoutes = !showRoutes;
  const btn = document.getElementById('route-toggle-btn');
  btn.innerHTML = `<span class="material-symbols-outlined text-[14px]">route</span>Routes ${showRoutes ? 'ON' : 'OFF'}`;
  btn.className = showRoutes ? 'text-xs font-bold bg-primary/10 text-primary border border-primary/20 px-3 py-1.5 rounded-lg hover:bg-primary/20 transition-colors flex items-center gap-1' : 'text-xs font-bold bg-surface-container border border-outline-variant/20 px-3 py-1.5 rounded-lg hover:bg-surface-container-high transition-colors flex items-center gap-1';
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
  setTimeout(() => map.invalidateSize(), 50);
}

function confirmForce(name) {
  return confirm(`Send GPS activation request to ${name}?\n\nThey will see a mandatory popup on their screen.`);
}

setInterval(() => location.reload(), 30000);
window.addEventListener('resize', () => {
  if (map) {
    clearTimeout(window.__trackingMapResizeTimer);
    window.__trackingMapResizeTimer = setTimeout(() => map.invalidateSize(), 120);
  }
});
document.addEventListener('DOMContentLoaded', initMap);
</script>


 