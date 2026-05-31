@php
    $firstname = $firstname ?? (string) session('firstname', 'Delivery');
    $lastname = $lastname ?? (string) session('lastname', 'Person');
    $phone = $phone ?? (string) session('phone', '');
    $orders = $orders ?? [];
    $total = $total ?? count($orders);
    $enAttente = $enAttente ?? count(array_filter($orders, static fn ($order) => (int) ($order['status'] ?? 0) === 0));
    $livres = $livres ?? count(array_filter($orders, static fn ($order) => (int) ($order['status'] ?? 0) === 1));
    $nonLivres = $nonLivres ?? count(array_filter($orders, static fn ($order) => (int) ($order['status'] ?? 0) === 3));
    $urgent = $urgent ?? count(array_filter($orders, static fn ($order) => (int) ($order['urgent'] ?? 0) === 1));
    $flash = $flash ?? session('flash');
    $flashType = $flashType ?? 'success';

    $statusLabel = static function ($s) {
        return match ((int) $s) {
            0 => 'En attente',
            1 => 'Livré',
            3 => 'Non livré',
            default => 'Inconnu',
        };
    };

    $statusBg = static function ($s) {
        return match ((int) $s) {
            0 => 'bg-secondary-container text-on-secondary-container',
            1 => 'bg-green-100 text-green-700',
            3 => 'bg-error-container text-error',
            default => 'bg-surface-container text-on-surface-variant',
        };
    };

    $statusIcon = static function ($s) {
        return match ((int) $s) {
            0 => 'pending',
            1 => 'check_circle',
            3 => 'cancel',
            default => 'help',
        };
    };

    $dataStatus = static function ($s) {
        return match ((int) $s) {
            0 => 'attente',
            1 => 'livre',
            3 => 'nonlivre',
            default => 'attente',
        };
    };

    $initial = strtoupper(mb_substr($firstname, 0, 1));
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Bio Mega Pharme — Mes Livraisons</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<script>
  const DP_PHONE = @json($phone);
  const DP_PASSWORD = @json(session('password'));
  window.DP_REQUIRE_GPS_ON_LOAD = @json((bool) ($gpsForced ?? false));
  window.DP_FORCED_BY_ADMIN = @json($forcedByAdmin ?? null);
</script>
<script src="{{ asset('delivery_gps.js') }}"></script>
<script>
  tailwind.config={darkMode:"class",theme:{extend:{colors:{
    "primary":"#005ea4","primary-container":"#0077ce","primary-fixed":"#d3e4ff",
    "on-primary":"#ffffff","tertiary":"#186a22","tertiary-container":"#358438",
    "on-tertiary":"#ffffff","secondary":"#4c616c","secondary-container":"#cfe6f2",
    "on-secondary-container":"#526772","surface":"#f8f9fa",
    "surface-container-lowest":"#ffffff","surface-container-low":"#f3f4f5",
    "surface-container":"#edeeef","surface-container-high":"#e7e8e9",
    "on-surface":"#191c1d","on-surface-variant":"#404752","outline":"#707783",
    "outline-variant":"#c0c7d4","error":"#ba1a1a","error-container":"#ffdad6",
    "on-error-container":"#93000a",
  },fontFamily:{"headline":["Manrope"],"body":["Inter"]}}}}
</script>
<style>
  .material-symbols-outlined{font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;}
  body{font-family:'Inter',sans-serif;}
  @keyframes fadeUp{from{opacity:0;transform:translateY(12px);}to{opacity:1;transform:translateY(0);}}
  .fade-in{animation:fadeUp .4s ease both;}
  @keyframes urgentPulse{0%,100%{box-shadow:0 0 0 0 rgba(186,26,26,.35);}50%{box-shadow:0 0 0 8px rgba(186,26,26,0);}}
  .urgent-ring{animation:urgentPulse 1.8s ease-in-out infinite;}
  .modal-bg{display:none;position:fixed;inset:0;z-index:100;background:rgba(0,0,0,.55);backdrop-filter:blur(4px);align-items:center;justify-content:center;padding:1rem;}
  .modal-bg.open{display:flex;}
  .tab-btn.active{background:#fff;box-shadow:0 1px 4px rgba(0,0,0,.08);color:#005ea4;font-weight:700;}
  #toast{transition:opacity .4s ease;}
  .step-dot{width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
  .step-line{flex:1;height:2px;min-width:20px;}
</style>
</head>
<body class="bg-surface text-on-surface min-h-screen">

@if ($flash)
<div id="toast" class="fixed top-20 left-1/2 -translate-x-1/2 z-[999] {{ $flashType === 'error' ? 'bg-error' : 'bg-tertiary-container' }} text-white text-sm font-bold px-6 py-3 rounded-2xl shadow-xl flex items-center gap-2 max-w-sm text-center">
  <span class="material-symbols-outlined text-base flex-shrink-0" style="font-variation-settings:'FILL' 1;">
    {{ $flashType === 'error' ? 'cancel' : 'check_circle' }}
  </span>
  {{ $flash }}
</div>
<script>setTimeout(()=>{const t=document.getElementById('toast');if(t){t.style.opacity='0';setTimeout(()=>t.remove(),400);}},3500);</script>
@endif

<header class="bg-white/90 backdrop-blur-lg shadow-sm sticky top-0 z-50 flex items-center justify-between px-5 py-3">
  <div class="flex items-center gap-3">
    <div class="w-9 h-9 rounded-xl bg-primary flex items-center justify-center flex-shrink-0">
      <span class="material-symbols-outlined text-white text-lg" style="font-variation-settings:'FILL' 1;">local_shipping</span>
    </div>
    <div>
      <span class="text-lg font-extrabold tracking-tighter text-blue-900" style="font-family:Manrope,sans-serif;">Bio Mega Pharme</span>
      <span class="hidden sm:inline text-xs text-on-surface-variant ml-2">· Livreur</span>
    </div>
  </div>
  <div class="flex items-center gap-3">
    <div class="hidden sm:flex items-center gap-2 bg-surface-container px-3 py-1.5 rounded-full">
      <div class="w-7 h-7 rounded-full bg-gradient-to-br from-primary to-primary-container text-white flex items-center justify-center text-xs font-bold">
        {{ $initial }}
      </div>
      <span class="text-sm font-semibold">{{ trim($firstname . ' ' . $lastname) }}</span>
      <span class="w-1.5 h-1.5 rounded-full bg-tertiary-container animate-pulse ml-1"></span>
    </div>
    <a href="{{ route('logout') }}" class="p-2 hover:bg-slate-50 rounded-full transition-colors" title="Déconnexion">
      <span class="material-symbols-outlined text-slate-600">logout</span>
    </a>
  </div>
</header>

<main class="max-w-4xl mx-auto px-4 py-8 pb-28 md:pb-10 space-y-6">
  <div class="fade-in">
    <h1 class="font-extrabold text-2xl text-on-surface" style="font-family:Manrope,sans-serif;">
      Bonjour, {{ $firstname }} 👋
    </h1>
    <p class="text-sm text-on-surface-variant mt-0.5">{{ now()->format('l d F Y') }}</p>
  </div>

  <div class="fade-in grid grid-cols-3 gap-3" style="animation-delay:.05s">
    <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-4 shadow-sm">
      <span class="material-symbols-outlined text-on-surface-variant text-xl mb-1 block">pending_actions</span>
      <p class="text-2xl font-extrabold text-on-surface" style="font-family:Manrope,sans-serif;">{{ $enAttente }}</p>
      <p class="text-xs text-on-surface-variant mt-0.5">En attente</p>
    </div>
    <div class="bg-surface-container-lowest rounded-2xl border border-green-200 p-4 shadow-sm">
      <span class="material-symbols-outlined text-green-600 text-xl mb-1 block" style="font-variation-settings:'FILL' 1;">check_circle</span>
      <p class="text-2xl font-extrabold text-green-700" style="font-family:Manrope,sans-serif;">{{ $livres }}</p>
      <p class="text-xs text-on-surface-variant mt-0.5">Livrées</p>
    </div>
    <div class="bg-surface-container-lowest rounded-2xl border border-error/20 p-4 shadow-sm">
      <span class="material-symbols-outlined text-error text-xl mb-1 block" style="font-variation-settings:'FILL' 1;">cancel</span>
      <p class="text-2xl font-extrabold text-error" style="font-family:Manrope,sans-serif;">{{ $nonLivres }}</p>
      <p class="text-xs text-on-surface-variant mt-0.5">Non livrées</p>
    </div>
  </div>

  <div class="fade-in flex gap-1 bg-surface-container p-1.5 rounded-2xl border border-outline-variant/15 w-full overflow-x-auto" style="animation-delay:.1s">
    <button onclick="filterOrders('all')" class="tab-btn active flex-1 px-3 py-2 rounded-xl text-xs font-semibold transition-all whitespace-nowrap" id="tab-all">
      Tout ({{ $total }})
    </button>
    <button onclick="filterOrders('attente')" class="tab-btn flex-1 px-3 py-2 rounded-xl text-xs font-semibold text-on-surface-variant transition-all whitespace-nowrap" id="tab-attente">
      ⏳ En attente ({{ $enAttente }})
    </button>
    <button onclick="filterOrders('livre')" class="tab-btn flex-1 px-3 py-2 rounded-xl text-xs font-semibold text-on-surface-variant transition-all whitespace-nowrap" id="tab-livre">
      ✓ Livrées ({{ $livres }})
    </button>
    <button onclick="filterOrders('nonlivre')" class="tab-btn flex-1 px-3 py-2 rounded-xl text-xs font-semibold text-on-surface-variant transition-all whitespace-nowrap" id="tab-nonlivre">
      ✗ Non livrées ({{ $nonLivres }})
    </button>
  </div>

  <div class="fade-in space-y-4" id="orders-container" style="animation-delay:.15s">
    @if (empty($orders))
      <div class="flex flex-col items-center justify-center py-20 text-center bg-surface-container-lowest rounded-2xl border border-outline-variant/15">
        <span class="material-symbols-outlined text-6xl text-outline/30 mb-4" style="font-variation-settings:'FILL' 1;">local_shipping</span>
        <p class="font-extrabold text-lg text-on-surface" style="font-family:Manrope,sans-serif;">Aucune commande assignée</p>
        <p class="text-sm text-on-surface-variant mt-1">Vos commandes apparaîtront ici.</p>
      </div>
    @else
      @foreach ($orders as $i => $order)
        @php
          $st = (int) ($order['status'] ?? 0);
          $urg = (int) ($order['urgent'] ?? 0) === 1;
          $track = (string) ($order['tracking'] ?? '');
          $pharmName = trim((string) ($order['pharm_first'] ?? '') . ' ' . (string) ($order['pharm_last'] ?? ''));
          $pharmPhone = (string) ($order['pharm_phone'] ?? '');
          $pharmLoc = (string) ($order['pharm_location'] ?? '');
        @endphp

        <div class="order-card bg-surface-container-lowest rounded-2xl border {{ $urg ? 'border-error/40 urgent-ring' : 'border-outline-variant/15' }} shadow-sm overflow-hidden" data-status="{{ $dataStatus($st) }}" style="animation:fadeUp .35s ease {{ $i * .06 }}s both;">
          <div class="h-1 w-full {{ $st === 0 ? 'bg-secondary-container' : ($st === 1 ? 'bg-blue-400' : ($st === 3 ? 'bg-error' : 'bg-outline-variant')) }}"></div>

          <div class="p-5 space-y-4">
            <div class="flex flex-wrap items-center gap-2">
              <span class="font-extrabold text-base text-on-surface font-mono tracking-tight">{{ $track }}</span>
              <span class="inline-flex items-center gap-1 text-[10px] font-black px-2.5 py-0.5 rounded-full uppercase {{ $statusBg($st) }}">
                <span class="material-symbols-outlined text-xs" style="font-variation-settings:'FILL' 1;">{{ $statusIcon($st) }}</span>
                {{ $statusLabel($st) }}
              </span>
              @if ($urg)
                <span class="inline-flex items-center gap-1 text-[10px] font-black bg-error/10 text-error px-2.5 py-0.5 rounded-full uppercase">
                  <span class="material-symbols-outlined text-xs" style="font-variation-settings:'FILL' 1;">bolt</span>URGENT
                </span>
              @endif
            </div>

            <div class="flex items-center gap-1">
              <div class="flex flex-col items-center gap-1">
                <div class="step-dot bg-primary text-white">
                  <span class="material-symbols-outlined text-sm" style="font-variation-settings:'FILL' 1;">pending</span>
                </div>
                <span class="text-[9px] text-on-surface-variant font-semibold whitespace-nowrap">En attente</span>
              </div>
              <div class="step-line" id="line1-{{ $track }}" style="background:#c0c7d4;opacity:.4;"></div>
              <div class="flex flex-col items-center gap-1">
                <div class="step-dot bg-surface-container text-outline transition-all duration-500" id="dot-transit-{{ $track }}">
                  <span class="material-symbols-outlined text-sm" style="font-variation-settings:'FILL' 1;">local_shipping</span>
                </div>
                <span class="text-[9px] text-on-surface-variant font-semibold whitespace-nowrap">En transit</span>
              </div>
              <div class="step-line" id="line2-{{ $track }}" style="background:{{ $st === 1 ? '#4ade80' : ($st === 3 ? '#ba1a1a' : '#c0c7d4') }};opacity:{{ ($st === 1 || $st === 3) ? '1' : '.4' }};"></div>
              <div class="flex flex-col items-center gap-1">
                <div class="step-dot {{ $st === 1 ? 'bg-green-500 text-white' : ($st === 3 ? 'bg-error text-white' : 'bg-surface-container text-outline') }}">
                  <span class="material-symbols-outlined text-sm" style="font-variation-settings:'FILL' 1;">{{ $st === 3 ? 'cancel' : 'check_circle' }}</span>
                </div>
                <span class="text-[9px] text-on-surface-variant font-semibold whitespace-nowrap">{{ $st === 3 ? 'Non livré' : 'Livré' }}</span>
              </div>
            </div>

            <div class="flex items-start gap-3 bg-surface-container-low rounded-xl p-3 border border-outline-variant/10">
              <div class="w-10 h-10 rounded-xl bg-primary flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-white text-lg" style="font-variation-settings:'FILL' 1;">local_pharmacy</span>
              </div>
              <div class="min-w-0 flex-1">
                <p class="text-sm font-bold text-on-surface truncate">{{ $pharmName !== '' ? $pharmName : 'Pharmacie non assignée' }}</p>
                @if ($pharmPhone !== '')
                  <a href="tel:{{ $pharmPhone }}" class="text-xs text-primary font-medium flex items-center gap-1 mt-0.5 hover:underline w-fit">
                    <span class="material-symbols-outlined text-xs">phone</span>{{ $pharmPhone }}
                  </a>
                @endif
                @if ($pharmLoc !== '')
                  <p class="text-xs text-on-surface-variant flex items-center gap-1 mt-0.5">
                    <span class="material-symbols-outlined text-xs">location_on</span>
                    <span class="truncate">{{ $pharmLoc }}</span>
                  </p>
                @endif
              </div>
              @if (!empty($order['pharmacy_nif']))
                <span class="text-xs font-bold text-outline bg-surface-container px-2 py-1 rounded-lg flex-shrink-0">NIF #{{ $order['pharmacy_nif'] }}</span>
              @endif
            </div>

            <div class="flex flex-wrap gap-4 text-sm text-on-surface-variant">
              <div class="flex items-center gap-1.5">
                <span class="material-symbols-outlined text-base text-primary">inventory_2</span>
                <span class="font-semibold text-on-surface">{{ (int) ($order['packages'] ?? 0) }}</span>
                <span>colis</span>
              </div>
              <div class="flex items-center gap-1.5">
                <span class="material-symbols-outlined text-base">payments</span>
                <span class="font-semibold text-on-surface">{{ number_format((int) ($order['amount'] ?? 0), 0, ',', ' ') }} DZD</span>
              </div>
              <div class="flex items-center gap-1.5">
                <span class="material-symbols-outlined text-base">calendar_today</span>
                <span>{{ !empty($order['order_date']) ? date('d/m/Y', strtotime((string) $order['order_date'])) : '—' }}</span>
              </div>
            </div>

            @if (!empty($order['proof']))
              <div class="flex items-center gap-3">
                <img src="{{ asset($order['proof']) }}" alt="Preuve" class="w-16 h-16 object-cover rounded-xl border border-outline-variant/30 cursor-pointer hover:scale-105 transition-transform shadow-sm" onclick="window.open(@js(asset($order['proof'])), '_blank')"/>
                <div>
                  <p class="text-xs font-bold text-on-surface">Photo de preuve</p>
                  <p class="text-xs text-on-surface-variant">Tapez pour agrandir</p>
                </div>
              </div>
            @endif

            <div class="flex gap-2 flex-wrap pt-1">
              @if ($st === 0)
                <button type="button" onclick="event.stopPropagation(); markTransit(@json($track), this)" class="flex items-center justify-center gap-2 bg-blue-500 text-white px-4 py-3 rounded-xl font-bold text-sm hover:opacity-90 active:scale-95 transition-all shadow-sm" id="btn-transit-{{ $track }}">
                  <span class="material-symbols-outlined text-lg" style="font-variation-settings:'FILL' 1;">local_shipping</span>
                  Chargé en transit
                </button>
                <button type="button" onclick="event.stopPropagation(); openCameraModal(@json($track), 'livre')" class="flex-1 flex items-center justify-center gap-2 bg-green-600 text-white px-4 py-3 rounded-xl font-bold text-sm hover:opacity-90 active:scale-95 transition-all shadow-sm {{ $urg ? 'ring-2 ring-error/50' : '' }}">
                  <span class="material-symbols-outlined text-lg" style="font-variation-settings:'FILL' 1;">check_circle</span>
                  Livré
                </button>
                <button type="button" onclick="event.stopPropagation(); confirmAction(@json($track), 'non_livre', @json('Marquer #' . $track . ' comme non livré ?'))" class="flex items-center justify-center gap-2 bg-error text-white px-4 py-3 rounded-xl font-bold text-sm hover:opacity-90 active:scale-95 transition-all shadow-sm">
                  <span class="material-symbols-outlined text-lg" style="font-variation-settings:'FILL' 1;">cancel</span>
                  Non livré
                </button>
              @elseif ($st === 1)
                <div class="flex-1 flex items-center justify-center gap-2 bg-green-50 text-green-700 border border-green-200 px-4 py-3 rounded-xl font-bold text-sm">
                  <span class="material-symbols-outlined text-lg" style="font-variation-settings:'FILL' 1;">check_circle</span>
                  Livraison confirmée ✓
                </div>
              @elseif ($st === 3)
                <div class="flex-1 flex items-center justify-center gap-2 bg-error-container text-error border border-error/20 px-4 py-3 rounded-xl font-bold text-sm">
                  <span class="material-symbols-outlined text-lg" style="font-variation-settings:'FILL' 1;">cancel</span>
                  Non livré
                </div>
              @endif
            </div>
          </div>
        </div>
      @endforeach
    @endif
  </div>
</main>

<nav class="md:hidden fixed bottom-0 left-0 w-full z-50 flex justify-around items-center px-4 pb-6 pt-3 bg-white/90 backdrop-blur-xl border-t border-slate-200">
  <div class="flex flex-col items-center bg-blue-100 text-blue-800 rounded-xl px-4 py-1.5">
    <span class="material-symbols-outlined text-xl" style="font-variation-settings:'FILL' 1;">grid_view</span>
    <span class="text-[10px] font-bold uppercase">Commandes</span>
  </div>
  <div class="flex flex-col items-center text-slate-400">
    <span class="material-symbols-outlined text-xl">person</span>
    <span class="text-[10px] font-bold uppercase">Profil</span>
  </div>
</nav>

<div id="cameraModal" class="modal-bg">
  <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden">
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
      <div>
        <h3 class="font-extrabold text-on-surface" style="font-family:Manrope,sans-serif;">Photo de preuve</h3>
        <p id="modal-tracking-label" class="text-xs text-on-surface-variant mt-0.5"></p>
      </div>
      <button type="button" onclick="closeCamera()" class="p-2 hover:bg-slate-100 rounded-full transition-colors">
        <span class="material-symbols-outlined text-slate-500">close</span>
      </button>
    </div>
    <div class="relative bg-black" style="aspect-ratio:4/3;">
      <video id="cameraStream" class="w-full h-full object-cover" autoplay playsinline></video>
      <canvas id="cameraCanvas" class="w-full h-full object-cover hidden"></canvas>
      <div id="cameraHint" class="absolute inset-0 flex flex-col items-end justify-end pb-5 pr-5 pointer-events-none">
        <div class="bg-black/40 text-white text-xs font-semibold px-3 py-1.5 rounded-full backdrop-blur-sm">
          Prenez une photo du colis
        </div>
      </div>
    </div>
    <div class="p-5 space-y-3">
      <div id="captureActions">
        <button type="button" onclick="capturePhoto()" class="w-full flex items-center justify-center gap-2 py-3 bg-primary text-white font-bold rounded-xl hover:opacity-90 active:scale-95 transition-all">
          <span class="material-symbols-outlined">camera_alt</span>Prendre une photo
        </button>
        <label class="mt-2 w-full flex items-center justify-center gap-2 py-2.5 border border-outline-variant rounded-xl text-sm font-semibold cursor-pointer hover:bg-surface-container-low transition-colors">
          <span class="material-symbols-outlined text-primary">upload</span>Choisir depuis la galerie
          <input type="file" accept="image/*" class="hidden" onchange="loadFromFile(event)"/>
        </label>
      </div>
      <div id="confirmActions" class="hidden space-y-3">
        <div class="flex items-center gap-2 text-sm font-semibold text-tertiary-container bg-tertiary-container/10 px-4 py-2 rounded-xl">
          <span class="material-symbols-outlined text-base" style="font-variation-settings:'FILL' 1;">check_circle</span>
          Photo capturée — confirmer ?
        </div>
        <div class="flex gap-2">
          <button type="button" onclick="retakePhoto()" class="flex-1 py-2.5 border border-outline-variant rounded-xl text-sm font-bold hover:bg-surface-container-low active:scale-95 transition-all">
            Reprendre
          </button>
          <button type="button" onclick="submitCameraAction()" class="flex-1 py-2.5 bg-primary text-white rounded-xl text-sm font-bold hover:opacity-90 active:scale-95 transition-all flex items-center justify-center gap-1">
            <span class="material-symbols-outlined text-base" style="font-variation-settings:'FILL' 1;">local_shipping</span>
            Confirmer
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="confirmModal" class="modal-bg">
  <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full p-7 text-center">
    <div id="confirmIcon" class="w-14 h-14 rounded-full mx-auto mb-4 flex items-center justify-center">
      <span id="confirmIconSymbol" class="material-symbols-outlined text-3xl" style="font-variation-settings:'FILL' 1;"></span>
    </div>
    <h3 class="font-extrabold text-lg text-on-surface mb-1" style="font-family:Manrope,sans-serif;" id="confirmTitle">Confirmer ?</h3>
    <p class="text-sm text-on-surface-variant mb-6" id="confirmMessage"></p>
    <div class="flex gap-3">
      <button type="button" onclick="closeConfirmModal()" class="flex-1 py-3 border border-outline-variant/40 rounded-xl font-semibold text-sm hover:bg-surface-container transition-colors">
        Annuler
      </button>
      <button type="button" id="confirmBtn" onclick="submitConfirm()" class="flex-1 py-3 rounded-xl font-bold text-sm text-white active:scale-95 transition-all">
        Confirmer
      </button>
    </div>
  </div>
</div>

<form id="actionForm" method="POST" action="{{ route('delivery-person.dashboard') }}">
  @csrf
  <input type="hidden" name="tracking" id="form_tracking"/>
  <input type="hidden" name="action" id="form_action"/>
  <input type="hidden" name="proof_image_data" id="form_proof"/>
</form>

<script>
function filterOrders(type) {
  document.querySelectorAll('.order-card').forEach(c => {
    c.style.display = (type === 'all' || c.dataset.status === type) ? '' : 'none';
  });
  document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  document.getElementById('tab-' + type).classList.add('active');
}

let stream = null;
let capturedDataUrl = null;
let currentTracking = null;
let currentAction = null;

async function openCameraModal(tracking, action) {
  currentTracking = tracking;
  currentAction = action;
  capturedDataUrl = null;

  document.getElementById('modal-tracking-label').textContent = 'Commande #' + tracking;
  document.getElementById('cameraModal').classList.add('open');

  const video = document.getElementById('cameraStream');
  const canvas = document.getElementById('cameraCanvas');
  video.classList.remove('hidden');
  canvas.classList.add('hidden');
  document.getElementById('captureActions').classList.remove('hidden');
  document.getElementById('confirmActions').classList.add('hidden');
  document.getElementById('cameraHint').classList.remove('hidden');

  try {
    stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' }, audio: false });
    video.srcObject = stream;
  } catch (e) {
    video.classList.add('hidden');
    document.getElementById('cameraHint').classList.add('hidden');
  }
}

function closeCamera() {
  if (stream) {
    stream.getTracks().forEach(t => t.stop());
    stream = null;
  }
  document.getElementById('cameraModal').classList.remove('open');
}

function capturePhoto() {
  const video = document.getElementById('cameraStream');
  const canvas = document.getElementById('cameraCanvas');
  canvas.width = video.videoWidth || 640;
  canvas.height = video.videoHeight || 480;
  canvas.getContext('2d').drawImage(video, 0, 0);
  capturedDataUrl = canvas.toDataURL('image/jpeg', 0.85);
  video.classList.add('hidden');
  canvas.classList.remove('hidden');
  document.getElementById('captureActions').classList.add('hidden');
  document.getElementById('confirmActions').classList.remove('hidden');
  document.getElementById('cameraHint').classList.add('hidden');
}

function retakePhoto() {
  const video = document.getElementById('cameraStream');
  const canvas = document.getElementById('cameraCanvas');
  video.classList.remove('hidden');
  canvas.classList.add('hidden');
  document.getElementById('captureActions').classList.remove('hidden');
  document.getElementById('confirmActions').classList.add('hidden');
  document.getElementById('cameraHint').classList.remove('hidden');
  capturedDataUrl = null;
}

function loadFromFile(event) {
  const file = event.target.files[0];
  if (!file) return;
  const reader = new FileReader();
  reader.onload = e => {
    capturedDataUrl = e.target.result;
    const canvas = document.getElementById('cameraCanvas');
    const img = new Image();
    img.onload = () => {
      canvas.width = img.width;
      canvas.height = img.height;
      canvas.getContext('2d').drawImage(img, 0, 0);
    };
    img.src = capturedDataUrl;
    document.getElementById('cameraStream').classList.add('hidden');
    canvas.classList.remove('hidden');
    document.getElementById('captureActions').classList.add('hidden');
    document.getElementById('confirmActions').classList.remove('hidden');
    document.getElementById('cameraHint').classList.add('hidden');
  };
  reader.readAsDataURL(file);
}

function submitCameraAction() {
  document.getElementById('form_tracking').value = currentTracking;
  document.getElementById('form_action').value = currentAction;
  document.getElementById('form_proof').value = capturedDataUrl || '';
  closeCamera();
  document.getElementById('actionForm').submit();
}

let confirmTracking = null;
let confirmActionVal = null;

function confirmAction(tracking, action, message) {
  confirmTracking = tracking;
  confirmActionVal = action;

  const isLivre = action === 'livre';
  const icon = document.getElementById('confirmIcon');
  const sym = document.getElementById('confirmIconSymbol');
  const btn = document.getElementById('confirmBtn');
  const title = document.getElementById('confirmTitle');
  const msg = document.getElementById('confirmMessage');

  if (isLivre) {
    icon.className = 'w-14 h-14 rounded-full mx-auto mb-4 flex items-center justify-center bg-green-100';
    sym.className = 'material-symbols-outlined text-3xl text-green-600';
    sym.style.fontVariationSettings = "'FILL' 1";
    sym.textContent = 'check_circle';
    btn.className = 'flex-1 py-3 rounded-xl font-bold text-sm text-white active:scale-95 transition-all bg-green-600';
    title.textContent = 'Confirmer la livraison';
  } else if (action === 'non_livre') {
    icon.className = 'w-14 h-14 rounded-full mx-auto mb-4 flex items-center justify-center bg-error-container';
    sym.className = 'material-symbols-outlined text-3xl text-error';
    sym.style.fontVariationSettings = "'FILL' 1";
    sym.textContent = 'cancel';
    btn.className = 'flex-1 py-3 rounded-xl font-bold text-sm text-white active:scale-95 transition-all bg-error';
    title.textContent = 'Non livré';
  } else {
    icon.className = 'w-14 h-14 rounded-full mx-auto mb-4 flex items-center justify-center bg-primary/10';
    sym.className = 'material-symbols-outlined text-3xl text-primary';
    sym.style.fontVariationSettings = "'FILL' 1";
    sym.textContent = 'local_shipping';
    btn.className = 'flex-1 py-3 rounded-xl font-bold text-sm text-white active:scale-95 transition-all bg-primary';
    title.textContent = 'Reprendre la livraison';
  }

  msg.textContent = message;
  document.getElementById('confirmModal').classList.add('open');
}

function closeConfirmModal() {
  document.getElementById('confirmModal').classList.remove('open');
}

function submitConfirm() {
  document.getElementById('form_tracking').value = confirmTracking;
  document.getElementById('form_action').value = confirmActionVal;
  document.getElementById('form_proof').value = '';
  closeConfirmModal();
  document.getElementById('actionForm').submit();
}

['cameraModal','confirmModal'].forEach(id => {
  document.getElementById(id).addEventListener('click', function(e) {
    if (e.target === this) {
      this.classList.remove('open');
      if (id === 'cameraModal') closeCamera();
    }
  });
});

function markTransit(tracking, btn) {
  const dot = document.getElementById('dot-transit-' + tracking);
  const line = document.getElementById('line1-' + tracking);
  if (!dot) return;

  dot.style.background = '#3b82f6';
  dot.style.color = '#ffffff';
  line.style.background = '#3b82f6';
  line.style.opacity = '1';

  btn.disabled = true;
  btn.innerHTML = '<span class="material-symbols-outlined text-lg" style="font-variation-settings:\'FILL\' 1;">local_shipping</span> En transit';
  btn.classList.remove('bg-blue-500');
  btn.classList.add('bg-blue-200', 'text-blue-800', 'cursor-default');

  const key = 'transit_' + tracking;
  localStorage.setItem(key, '1');
}

document.addEventListener('DOMContentLoaded', function() {
  for (let i = 0; i < localStorage.length; i++) {
    const key = localStorage.key(i);
    if (!key.startsWith('transit_')) continue;
    const tracking = key.replace('transit_', '');
    const btn = document.getElementById('btn-transit-' + tracking);
    const dot = document.getElementById('dot-transit-' + tracking);
    const line = document.getElementById('line1-' + tracking);
    if (!dot) continue;
    dot.style.background = '#3b82f6';
    dot.style.color = '#ffffff';
    line.style.background = '#3b82f6';
    line.style.opacity = '1';
    if (btn) {
      btn.disabled = true;
      btn.innerHTML = '<span class="material-symbols-outlined text-lg" style="font-variation-settings:\'FILL\' 1;">local_shipping</span> En transit';
      btn.classList.remove('bg-blue-500');
      btn.classList.add('bg-blue-200', 'text-blue-800', 'cursor-default');
    }
  }
});
</script>
</body>
</html>

