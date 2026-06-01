@php
    $managerName = $managerName ?? trim((string) session('firstname', '') . ' ' . (string) session('lastname', ''));
  $embedded = request()->boolean('embed');
    $deliveryPersons = $deliveryPersons ?? [];
    $routeHistory = $routeHistory ?? [];
    $assignedOrders = $assignedOrders ?? [];
    $stats = $stats ?? ['totalDP' => count($deliveryPersons), 'onlineDP' => 0, 'offlineDP' => count($deliveryPersons), 'forcedCount' => 0];
    $dpColors = $dpColors ?? [];
    $forcedMsg = $forcedMsg ?? null;
    $totalDP = $stats['totalDP'] ?? count($deliveryPersons);
    $onlineDP = $stats['onlineDP'] ?? 0;
    $offlineDP = $stats['offlineDP'] ?? max(0, $totalDP - $onlineDP);
    $forcedCount = $stats['forcedCount'] ?? 0;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Live Tracking | Delivery Manager</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
  tailwind.config = {
    darkMode:"class",
    theme:{extend:{
      colors:{
        "primary-fixed-dim":"#a2c9ff","tertiary":"#186a22","secondary":"#4c616c",
        "on-primary":"#ffffff","background":"#f8f9fa","on-primary-fixed-variant":"#004881",
        "inverse-surface":"#2e3132","surface-tint":"#0060a8","inverse-on-surface":"#f0f1f2",
        "on-error":"#ffffff","secondary-container":"#cfe6f2","on-primary-container":"#fdfcff",
        "on-secondary-container":"#526772","surface-container-lowest":"#ffffff",
        "on-primary-fixed":"#001c38","tertiary-container":"#358438",
        "surface-container-high":"#e7e8e9","on-tertiary":"#ffffff",
        "primary-container":"#0077ce","surface-bright":"#f8f9fa",
        "surface-container-highest":"#e1e3e4","on-background":"#191c1d",
        "secondary-fixed":"#cfe6f2","inverse-primary":"#a2c9ff","surface-dim":"#d9dadb",
        "surface-variant":"#e1e3e4","on-secondary":"#ffffff","error":"#ba1a1a",
        "outline-variant":"#c0c7d4","surface":"#f8f9fa","on-surface":"#191c1d",
        "error-container":"#ffdad6","primary-fixed":"#d3e4ff","surface-container":"#edeeef",
        "on-error-container":"#93000a","on-secondary-fixed":"#071e27","primary":"#005ea4",
        "outline":"#707783","surface-container-low":"#f3f4f5","on-surface-variant":"#404752"
      },
      fontFamily:{"headline":["Manrope"],"body":["Inter"],"label":["Inter"]},
      borderRadius:{"DEFAULT":"0.125rem","lg":"0.25rem","xl":"0.5rem","full":"0.75rem"},
    }},
  }
</script>
<style>
  .material-symbols-outlined{font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;}
  body{font-family:'Inter',sans-serif;}
  @keyframes fadeIn{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}
  .fade-in{animation:fadeIn 0.4s ease both;}
  @keyframes pulse-dot{0%,100%{transform:scale(1);opacity:1}50%{transform:scale(1.6);opacity:.6}}
  .pulse-dot{animation:pulse-dot 2s ease-in-out infinite;}
  @keyframes beacon{0%{transform:scale(1);opacity:.8}100%{transform:scale(2.8);opacity:0}}
  .beacon-ring{animation:beacon 1.5s ease-out infinite;}
  #map{height:calc(100vh - 230px);min-height:420px;border-radius:1rem;z-index:1;}
  .leaflet-popup-content-wrapper{border-radius:12px!important;box-shadow:0 4px 24px rgba(0,0,0,.15)!important;padding:0!important;overflow:hidden;}
  .leaflet-popup-content{margin:0!important;}
  .dp-card.selected{border-color:#005ea4!important;background:#eff6ff!important;}
  .dp-card{cursor:pointer;transition:all .2s;}
  .dp-card:hover{background:#f0f4ff!important;}
  @keyframes forceBtn{0%,100%{box-shadow:0 0 0 0 rgba(186,26,26,.4)}70%{box-shadow:0 0 0 8px rgba(186,26,26,0)}}
  .force-btn-pulse{animation:forceBtn 2s ease-in-out infinite;}
</style>
@if ($embedded)
<style>
  .embedded-tracking header,
  .embedded-tracking aside,
  .embedded-tracking nav { display:none !important; }
  .embedded-tracking main { margin-left:0 !important; padding:0 !important; }
  .embedded-tracking .tracking-embed-shell { min-height:auto !important; }
</style>
@endif
</head>
<body class="bg-surface text-on-surface font-body {{ $embedded ? 'embedded-tracking' : '' }}">

@if ($forcedMsg)
<div id="forced-toast" style="position:fixed;top:80px;right:24px;z-index:9999;background:#005ea4;color:white;padding:12px 20px;border-radius:14px;font-size:13px;font-weight:700;box-shadow:0 4px 20px rgba(0,0,0,.2);display:flex;align-items:center;gap:8px;">
  <span class="material-symbols-outlined text-lg" style="font-variation-settings:'FILL' 1;">gps_fixed</span>
  GPS request sent to {{ $forcedMsg }}
</div>
<script>setTimeout(()=>{const t=document.getElementById('forced-toast');if(t)t.remove();},4000);</script>
@endif

<header class="bg-white/80 backdrop-blur-lg shadow-sm sticky top-0 z-50 flex justify-between items-center px-6 py-3 w-full">
  <div class="flex items-center gap-8">
    <span class="text-xl font-extrabold tracking-tighter text-blue-800 font-headline">TronSport Medicamon</span>
    <span class="hidden md:block text-xs font-bold text-on-surface-variant bg-surface-container-low px-3 py-1 rounded-full border border-outline-variant/30">Delivery Manager Portal</span>
  </div>
  <div class="flex items-center gap-3">
    <div class="hidden sm:flex items-center gap-2 bg-tertiary/10 text-tertiary px-3 py-1.5 rounded-full text-xs font-bold">
      <span class="w-2 h-2 rounded-full bg-tertiary pulse-dot inline-block"></span>
      Live · <span id="last-refresh">{{ now()->format('H:i:s') }}</span>
    </div>
    @if ($forcedCount > 0)
    <div class="flex items-center gap-2 bg-error/10 text-error px-3 py-1.5 rounded-full text-xs font-bold">
      <span class="material-symbols-outlined text-sm" style="font-variation-settings:'FILL' 1;">gps_fixed</span>
      {{ $forcedCount }} GPS request{{ $forcedCount > 1 ? 's' : '' }} pending
    </div>
    @endif
    <button onclick="location.reload()" class="p-2 hover:bg-slate-50 rounded-full active:scale-95 transition-colors" title="Refresh">
      <span class="material-symbols-outlined text-slate-600">refresh</span>
    </button>
    <a href="{{ route('logout') }}" class="p-2 hover:bg-slate-50 rounded-full active:scale-95 transition-colors" title="Logout">
      <span class="material-symbols-outlined text-slate-600">logout</span>
    </a>
  </div>
</header>

<div class="flex min-h-screen tracking-embed-shell">
  <aside class="bg-slate-50 h-screen w-64 border-r border-slate-200 flex flex-col gap-2 p-4 fixed left-0 top-[60px] hidden lg:flex">
    <div class="mb-4 px-2">
      <h3 class="font-headline font-bold text-blue-900">Delivery Manager</h3>
      <p class="text-xs text-on-surface-variant">{{ $managerName }}</p>
    </div>
    <nav class="flex-1 flex flex-col gap-1">
      <a href="{{ route('delivery-manager.dashboard') }}" class="text-slate-600 hover:bg-slate-100 flex items-center gap-3 px-3 py-2.5 rounded-lg hover:translate-x-1 transition-transform">
        <span class="material-symbols-outlined">dashboard</span><span class="text-sm">Dashboard</span>
      </a>
      <a href="{{ route('delivery-manager.dashboard') }}#orders-section" class="text-slate-600 hover:bg-slate-100 flex items-center gap-3 px-3 py-2.5 rounded-lg hover:translate-x-1 transition-transform">
        <span class="material-symbols-outlined">package_2</span><span class="text-sm">Orders</span>
      </a>
      <a href="{{ route('delivery-manager.dashboard') }}#dp-section" class="text-slate-600 hover:bg-slate-100 flex items-center gap-3 px-3 py-2.5 rounded-lg hover:translate-x-1 transition-transform">
        <span class="material-symbols-outlined">local_shipping</span><span class="text-sm">Delivery Persons</span>
      </a>
      <a href="{{ route('delivery-manager.tracking') }}" class="bg-blue-50 text-blue-700 rounded-lg font-bold flex items-center gap-3 px-3 py-2.5 hover:translate-x-1 transition-transform">
        <span class="material-symbols-outlined">location_on</span><span class="text-sm">Live Tracking</span>
      </a>
      <a href="{{ route('logout') }}" class="text-red-500 hover:bg-red-50 flex items-center gap-3 px-3 py-2.5 rounded-lg hover:translate-x-1 transition-transform mt-2">
        <span class="material-symbols-outlined">logout</span><span class="text-sm font-bold">Logout</span>
      </a>
    </nav>
  </aside>

  <main class="flex-1 lg:ml-64 p-4 lg:p-6 space-y-5 bg-surface">
    @php $trackingUrl = route('delivery-manager.tracking'); @endphp
    @include('portal.admin.tracking')
  </main>
</div>

<nav class="md:hidden fixed bottom-0 left-0 w-full z-50 flex justify-around items-center px-4 pb-6 pt-3 bg-white/90 backdrop-blur-xl border-t border-slate-200">
  <a href="{{ route('delivery-manager.dashboard') }}" class="flex flex-col items-center text-slate-400">
    <span class="material-symbols-outlined">grid_view</span>
    <span class="text-[10px] font-semibold uppercase">Dashboard</span>
  </a>
  <a href="{{ route('delivery-manager.dashboard') }}#orders-section" class="flex flex-col items-center text-slate-400">
    <span class="material-symbols-outlined">package_2</span>
    <span class="text-[10px] font-semibold uppercase">Orders</span>
  </a>
  <a href="{{ route('delivery-manager.tracking') }}" class="flex flex-col items-center bg-blue-100 text-blue-800 rounded-xl px-3 py-1.5">
    <span class="material-symbols-outlined">location_on</span>
    <span class="text-[10px] font-semibold uppercase">Tracking</span>
  </a>
  <a href="{{ route('logout') }}" class="flex flex-col items-center text-slate-400">
    <span class="material-symbols-outlined">logout</span>
    <span class="text-[10px] font-semibold uppercase">Logout</span>
  </a>
</nav>

</body>
</html>

 