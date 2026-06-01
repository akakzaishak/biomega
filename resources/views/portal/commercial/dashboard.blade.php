@php
/**
 * Commercial dashboard converted from legacy commercial_dashboard.php
 * Expects: firstname, lastname, success, error, pharmacies_orders, total_pharmacies, total_items, total_achat, total_pending
 */
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Service Commercial — Bio Mega Pharme</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<script>
  tailwind.config={darkMode:"class",theme:{extend:{colors:{
    "primary":"#005ea4","primary-container":"#0077ce","on-primary":"#ffffff",
    "tertiary":"#186a22","tertiary-container":"#1e8a2a","on-tertiary":"#ffffff",
    "secondary":"#4c616c","secondary-container":"#cfe6f2","on-secondary-container":"#526772",
    "surface":"#f8f9fa","surface-container-lowest":"#ffffff","surface-container-low":"#f3f4f5",
    "surface-container":"#edeeef","surface-container-high":"#e7e8e9",
    "on-surface":"#191c1d","on-surface-variant":"#404752","outline":"#707783",
    "outline-variant":"#c0c7d4","error":"#ba1a1a","error-container":"#ffdad6","on-error-container":"#93000a",
  },fontFamily:{"headline":["Manrope"],"body":["Inter"]}}}}
</script>
<style>
  .material-symbols-outlined{font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;}
  body{font-family:'Inter',sans-serif;}
  @keyframes fadeUp{from{opacity:0;transform:translateY(12px);}to{opacity:1;transform:translateY(0);}}
  .fade-in{animation:fadeUp .4s ease both;}
  .modal-bg{display:none;position:fixed;inset:0;z-index:100;background:rgba(0,0,0,.5);backdrop-filter:blur(4px);align-items:center;justify-content:center;padding:1rem;}
  .modal-bg.open{display:flex;}
  .pill{display:inline-flex;align-items:center;gap:3px;font-size:10px;font-weight:700;padding:2px 8px;border-radius:999px;text-transform:uppercase;letter-spacing:.04em;}
  .tab-btn{padding:.5rem 1.5rem;border-radius:.75rem;font-weight:600;font-size:.875rem;transition:all .2s;cursor:pointer;}
  .tab-btn.active{background:#005ea4;color:#fff;}
  .tab-btn:not(.active){color:#404752;}
  .tab-btn:not(.active):hover{background:#edeeef;}
  .pharm-body{display:none;} .pharm-body.open{display:block;}
  .chevron{transition:transform .2s;} .chevron.open{transform:rotate(180deg);} 
</style>
</head>
<body class="bg-surface text-on-surface min-h-screen">

<header class="bg-white/90 backdrop-blur-lg shadow-sm sticky top-0 z-50 flex items-center justify-between px-6 py-3">
  <div class="flex items-center gap-3">
    <div class="w-9 h-9 rounded-xl bg-primary flex items-center justify-center">
      <span class="material-symbols-outlined text-white text-xl" style="font-variation-settings:'FILL' 1;">storefront</span>
    </div>
    <div>
      <span class="text-lg font-extrabold tracking-tighter text-blue-900" style="font-family:Manrope,sans-serif;">Bio Mega Pharme</span>
      <span class="hidden sm:inline text-xs text-on-surface-variant ml-2">· Service Commercial</span>
    </div>
  </div>
  <div class="flex items-center gap-3">
    <div class="hidden sm:flex items-center gap-2 bg-surface-container px-3 py-1.5 rounded-full">
      <div class="w-7 h-7 rounded-full bg-gradient-to-br from-primary to-primary-container text-white flex items-center justify-center text-xs font-bold">
        {{ strtoupper(mb_substr($firstname ?? 'Commercial',0,1)) }}
      </div>
      <span class="text-sm font-semibold">{{ ($firstname ?? '') . ' ' . ($lastname ?? '') }}</span>
    </div>
    <a href="{{ url('/logout') }}" class="p-2 hover:bg-slate-50 rounded-full" title="Déconnexion">
      <span class="material-symbols-outlined text-slate-600">logout</span>
    </a>
  </div>
</header>

<div class="flex min-h-screen">

  <aside class="bg-slate-50 w-60 border-r border-slate-200 p-4 fixed left-0 top-[57px] h-screen hidden lg:flex flex-col gap-1">
    <div class="mb-4 px-2">
      <p class="font-bold text-blue-900 text-sm" style="font-family:Manrope,sans-serif;">Commercial Portal</p>
      <p class="text-xs text-on-surface-variant">{{ ($firstname ?? '') . ' ' . ($lastname ?? '') }}</p>
    </div>
    <a onclick="switchTab('orders')" class="flex items-center gap-3 px-3 py-2.5 rounded-lg cursor-pointer hover:translate-x-1 transition-transform bg-blue-50 text-blue-700 font-bold" id="side-orders">
      <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">receipt_long</span><span class="text-sm">Commandes</span>
    </a>
    <a onclick="switchTab('achats')" class="flex items-center gap-3 px-3 py-2.5 rounded-lg cursor-pointer hover:translate-x-1 transition-transform text-slate-600 hover:bg-slate-100" id="side-achats">
      <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">shopping_cart</span><span class="text-sm">Achats</span>
    </a>
    <a href="{{ url('/logout') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-red-500 hover:bg-red-50 mt-auto">
      <span class="material-symbols-outlined">logout</span><span class="text-sm font-bold">Déconnexion</span>
    </a>
  </aside>

  <main class="flex-1 lg:ml-60 p-4 lg:p-8 space-y-6">

    @if(!empty($success))
    <div class="fade-in flex items-center gap-3 bg-green-50 text-green-800 border border-green-200 px-5 py-3.5 rounded-2xl text-sm font-semibold">
      <span class="material-symbols-outlined text-green-600" style="font-variation-settings:'FILL' 1;">check_circle</span>
      {{ $success }}
    </div>
    @endif
    @if(!empty($error))
    <div class="fade-in flex items-center gap-3 bg-error-container text-on-error-container border border-error/20 px-5 py-3.5 rounded-2xl text-sm font-semibold">
      <span class="material-symbols-outlined">error</span>
      {{ $error }}
    </div>
    @endif

    <div class="fade-in grid grid-cols-2 sm:grid-cols-4 gap-3">
      <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
          <span class="material-symbols-outlined text-primary" style="font-variation-settings:'FILL' 1;">local_pharmacy</span>
        </div>
        <div>
          <p class="text-xs text-on-surface-variant">Pharmacies</p>
          <p class="text-2xl font-extrabold text-primary" style="font-family:Manrope,sans-serif;">{{ $total_pharmacies ?? 0 }}</p>
        </div>
      </div>
      <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
          <span class="material-symbols-outlined text-primary" style="font-variation-settings:'FILL' 1;">medication</span>
        </div>
        <div>
          <p class="text-xs text-on-surface-variant">Total articles</p>
          <p class="text-2xl font-extrabold text-on-surface" style="font-family:Manrope,sans-serif;">{{ $total_items ?? 0 }}</p>
        </div>
      </div>
      <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-secondary-container flex items-center justify-center flex-shrink-0">
          <span class="material-symbols-outlined text-on-secondary-container" style="font-variation-settings:'FILL' 1;">pending_actions</span>
        </div>
        <div>
          <p class="text-xs text-on-surface-variant">En attente</p>
          <p class="text-2xl font-extrabold text-on-surface" style="font-family:Manrope,sans-serif;">{{ $total_pending ?? 0 }}</p>
        </div>
      </div>
      <div class="bg-surface-container-lowest rounded-2xl border border-green-100 shadow-sm p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center flex-shrink-0">
          <span class="material-symbols-outlined text-green-700" style="font-variation-settings:'FILL' 1;">shopping_cart_checkout</span>
        </div>
        <div>
          <p class="text-xs text-on-surface-variant">Achetés</p>
          <p class="text-2xl font-extrabold text-green-700" style="font-family:Manrope,sans-serif;">{{ $total_achat ?? 0 }}</p>
        </div>
      </div>
    </div>

    <div class="fade-in flex gap-2 bg-surface-container-lowest rounded-2xl p-1.5 border border-outline-variant/15 w-fit shadow-sm">
      <button class="tab-btn active" id="tab-orders-btn" onclick="switchTab('orders')">
        <span class="flex items-center gap-2">
          <span class="material-symbols-outlined text-base" style="font-variation-settings:'FILL' 1;">receipt_long</span>
          Commandes
          @if(($total_pending ?? 0) > 0)
          <span class="bg-primary text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">{{ $total_pending }}</span>
          @endif
        </span>
      </button>
      <button class="tab-btn" id="tab-achats-btn" onclick="switchTab('achats')">
        <span class="flex items-center gap-2">
          <span class="material-symbols-outlined text-base" style="font-variation-settings:'FILL' 1;">shopping_cart</span>
          Achats
          @if(($total_achat ?? 0) > 0)
          <span class="bg-green-600 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">{{ $total_achat }}</span>
          @endif
        </span>
      </button>
    </div>

    <div id="tab-orders" class="fade-in space-y-4">

      @if(empty($pharmacies_orders))
      <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm flex flex-col items-center py-20 text-center">
        <span class="material-symbols-outlined text-6xl text-outline/30 mb-4" style="font-variation-settings:'FILL' 1;">receipt_long</span>
        <h3 class="font-extrabold text-xl text-on-surface mb-2" style="font-family:Manrope,sans-serif;">Aucune commande</h3>
        <p class="text-sm text-on-surface-variant">Aucune pharmacie n'a encore passé de commande.</p>
      </div>
      @else
      @foreach($pharmacies_orders as $pid => $po)
        @php
          $all_done = count(array_filter($po['items'], fn($i) => ($i['achat'] ?? 0) === 1)) === count($po['items']);
          $some_done = !$all_done && count(array_filter($po['items'], fn($i) => ($i['achat'] ?? 0) === 1)) > 0;
          $done_count = count(array_filter($po['items'], fn($i) => ($i['achat'] ?? 0) === 1));
        @endphp

      <div class="bg-surface-container-lowest rounded-2xl border {{ $all_done ? 'border-green-200' : 'border-outline-variant/15' }} shadow-sm overflow-hidden fade-in">
        <button type="button" onclick="toggleAccordion('pharm-{{ $pid }}')" class="w-full flex items-center justify-between px-5 py-4 hover:bg-surface-container-low transition-colors text-left">
          <div class="flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-primary to-primary-container text-white flex items-center justify-center font-extrabold text-lg flex-shrink-0">
              {{ strtoupper(mb_substr($po['pharm_first'] ?? '?', 0, 1)) }}
            </div>
            <div>
              <p class="font-extrabold text-on-surface text-sm" style="font-family:Manrope,sans-serif;">
                {{ ($po['pharm_first'] ?? '') . ' ' . ($po['pharm_last'] ?? '') }}
                <span class="text-outline font-normal ml-1">· NIF #{{ $po['pharm_nif'] ?? '' }}</span>
              </p>
              <div class="flex items-center gap-3 mt-0.5 flex-wrap">
                <span class="text-xs text-on-surface-variant flex items-center gap-1">
                  <span class="material-symbols-outlined text-xs">phone</span>
                  {{ $po['pharm_phone'] ?? '' }}
                </span>
                <span class="text-xs text-on-surface-variant flex items-center gap-1">
                  <span class="material-symbols-outlined text-xs">location_on</span>
                  {{ $po['pharm_location'] ?? '' }}
                </span>
              </div>
            </div>
          </div>
          <div class="flex items-center gap-3 flex-shrink-0 ml-4">
            @if($all_done)
            <span class="pill bg-green-100 text-green-700">✓ Acheté</span>
            @elseif($some_done)
            <span class="pill bg-secondary-container text-on-secondary-container">⏳ Partiel</span>
            @else
            <span class="pill bg-yellow-100 text-yellow-700">⚠ En attente</span>
            @endif
            <span class="text-xs font-bold text-on-surface-variant bg-surface-container px-2.5 py-1 rounded-full">
              {{ count($po['items']) }} article{{ count($po['items'])>1?'s':'' }}
            </span>
            @if(!$all_done)
            <button type="button" onclick="event.stopPropagation(); openAchatModal('{{ $pid }}', '{{ addslashes($po['pharm_first'].' '.$po['pharm_last']) }}', {!! json_encode($po['items']) !!})" class="flex items-center gap-1.5 bg-primary text-white px-4 py-2 rounded-xl text-xs font-bold hover:opacity-90 active:scale-95 transition-all">
              <span class="material-symbols-outlined text-sm" style="font-variation-settings:'FILL' 1;">shopping_cart</span>
              Achats
            </button>
            @else
            <div class="flex items-center gap-1.5 bg-green-100 text-green-700 px-4 py-2 rounded-xl text-xs font-bold">
              <span class="material-symbols-outlined text-sm" style="font-variation-settings:'FILL' 1;">check_circle</span>
              Tout acheté
            </div>
            @endif
            <span class="material-symbols-outlined text-outline chevron" id="chev-{{ $pid }}">expand_more</span>
          </div>
        </button>

        <div class="pharm-body" id="pharm-{{ $pid }}">
          <div class="border-t border-outline-variant/15 overflow-x-auto">
            <table class="w-full text-sm">
              <thead class="bg-surface-container-low">
                <tr>
                  <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">#</th>
                  <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Article</th>
                  <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Stock dispo</th>
                  <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Qté commandée</th>
                  <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Statut achat</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-outline-variant/10">
                @foreach($po['items'] as $idx => $item)
                <tr class="hover:bg-surface-container-low/50 transition-colors">
                  <td class="px-5 py-3 text-xs text-outline">{{ $idx+1 }}</td>
                  <td class="px-5 py-3">
                    <div class="flex items-center gap-2">
                      <span class="material-symbols-outlined text-primary text-base" style="font-variation-settings:'FILL' 1;">medication</span>
                      <span class="font-semibold text-on-surface">{{ $item['item_name'] }}</span>
                    </div>
                  </td>
                  <td class="px-5 py-3">
                    <span class="text-xs font-bold text-on-surface-variant bg-surface-container px-2.5 py-1 rounded-full">{{ $item['stock_contiti'] }}</span>
                  </td>
                  <td class="px-5 py-3">
                    <span class="text-sm font-extrabold text-primary">{{ $item['contiti'] }}</span>
                  </td>
                  <td class="px-5 py-3">
                    @if(($item['achat'] ?? 0) === 1)
                    <span class="pill bg-green-100 text-green-700"><span class="material-symbols-outlined text-xs" style="font-variation-settings:'FILL' 1;">check_circle</span> Acheté</span>
                    @else
                    <span class="pill bg-yellow-100 text-yellow-700">⏳ En attente</span>
                    @endif
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          <div class="px-5 py-3 border-t border-outline-variant/15 bg-surface-container-low flex items-center justify-between text-xs text-on-surface-variant">
            <span>{{ $done_count }}/{{ count($po['items']) }} article{{ count($po['items'])>1?'s':'' }} acheté{{ count($po['items'])>1?'s':'' }}</span>
            <div class="flex items-center gap-2">
              <div class="w-24 h-1.5 bg-outline-variant/30 rounded-full overflow-hidden">
                <div class="h-full bg-green-500 rounded-full transition-all" style="width:{{ count($po['items'])>0 ? round($done_count/count($po['items'])*100) : 0 }}%"></div>
              </div>
              <span>{{ count($po['items'])>0 ? round($done_count/count($po['items'])*100) : 0 }}%</span>
            </div>
          </div>
        </div>

      </div>
      @endforeach
      @endif
    </div>

    <div id="tab-achats" class="hidden fade-in space-y-4">
      @php
        $purchased = [];
        foreach($pharmacies_orders as $pid => $po) {
            $bought = array_filter($po['items'], fn($i) => ($i['achat'] ?? 0) === 1);
            if (!empty($bought)) $purchased[$pid] = array_merge($po, ['items'=>array_values($bought)]);
        }
      @endphp

      @if(empty($purchased))
      <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm flex flex-col items-center py-20 text-center">
        <span class="material-symbols-outlined text-6xl text-outline/30 mb-4" style="font-variation-settings:'FILL' 1;">shopping_cart</span>
        <h3 class="font-extrabold text-xl text-on-surface mb-2" style="font-family:Manrope,sans-serif;">Aucun achat confirmé</h3>
        <p class="text-sm text-on-surface-variant">Les articles confirmés apparaîtront ici.</p>
        <button onclick="switchTab('orders')" class="mt-4 inline-flex items-center gap-2 bg-primary text-white px-5 py-2.5 rounded-xl text-sm font-bold hover:opacity-90">
          <span class="material-symbols-outlined text-lg">receipt_long</span>Voir les commandes
        </button>
      </div>
      @else
      <div class="bg-green-50 border border-green-200 rounded-2xl px-5 py-4 flex items-center gap-4">
        <span class="material-symbols-outlined text-green-600 text-3xl" style="font-variation-settings:'FILL' 1;">shopping_cart_checkout</span>
        <div>
          <p class="font-extrabold text-green-800" style="font-family:Manrope,sans-serif;">{{ $total_achat }} article{{ $total_achat>1?'s':'' }} achetés</p>
          <p class="text-xs text-green-700">sur {{ count($purchased) }} pharmacie{{ count($purchased)>1?'s':'' }}</p>
        </div>
      </div>

      @foreach($purchased as $pid => $po)
      <div class="bg-surface-container-lowest rounded-2xl border border-green-200/60 shadow-sm overflow-hidden">
        <div class="flex items-center gap-4 px-5 py-4 border-b border-outline-variant/10">
          <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary to-primary-container text-white flex items-center justify-center font-bold flex-shrink-0">{{ strtoupper(mb_substr($po['pharm_first'] ?? '?',0,1)) }}</div>
          <div>
            <p class="font-bold text-on-surface text-sm">{{ ($po['pharm_first'] ?? '') . ' ' . ($po['pharm_last'] ?? '') }}</p>
            <p class="text-xs text-on-surface-variant">{{ $po['pharm_location'] ?? '' }}</p>
          </div>
          <span class="ml-auto pill bg-green-100 text-green-700"><span class="material-symbols-outlined text-xs" style="font-variation-settings:'FILL' 1;">check_circle</span> {{ count($po['items']) }} acheté{{ count($po['items'])>1?'s':'' }}</span>
        </div>
        <div class="divide-y divide-outline-variant/10">
          @foreach($po['items'] as $item)
          <div class="flex items-center justify-between px-5 py-3">
            <div class="flex items-center gap-3">
              <span class="material-symbols-outlined text-green-600 text-base" style="font-variation-settings:'FILL' 1;">medication</span>
              <span class="text-sm font-semibold text-on-surface">{{ $item['item_name'] }}</span>
            </div>
            <div class="flex items-center gap-3">
              <span class="text-sm font-extrabold text-primary">Qté: {{ $item['contiti'] }}</span>
              <span class="pill bg-green-100 text-green-700">✓ Acheté</span>
            </div>
          </div>
          @endforeach
        </div>
      </div>
      @endforeach
      @endif
    </div>

  </main>
</div>

<div id="achatModal" class="modal-bg">
  <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full max-h-[80vh] flex flex-col">
    <div class="flex items-center justify-between px-6 py-4 border-b border-outline-variant/10 flex-shrink-0">
      <div>
        <h2 class="font-extrabold text-xl text-on-surface" style="font-family:Manrope,sans-serif;">Confirmer les Achats</h2>
        <p class="text-xs text-on-surface-variant mt-0.5">Pharmacie : <strong id="achat-pharm-name"></strong></p>
      </div>
      <button onclick="closeAchatModal()" class="p-2 hover:bg-surface-container rounded-full transition-colors">
        <span class="material-symbols-outlined">close</span>
      </button>
    </div>

    <div class="overflow-y-auto flex-1 p-6 space-y-2" id="achatItemsList"></div>

    <div class="px-6 py-4 border-t border-outline-variant/10 flex-shrink-0">
      <form method="POST" action="{{ route('commercial.dashboard') }}" id="achatForm">
        @csrf
        <input type="hidden" name="action" value="achat"/>
        <input type="hidden" name="pharmacy_id" id="achatPharmacyId"/>
        <div id="achatHiddenIds"></div>
        <div class="flex gap-3">
          <button type="button" onclick="closeAchatModal()" class="flex-1 py-3 border border-outline-variant/40 rounded-xl font-semibold text-sm hover:bg-surface-container transition-colors">Annuler</button>
          <button type="button" onclick="submitAchat()" class="flex-1 flex items-center justify-center gap-2 bg-primary text-white py-3 rounded-xl font-bold text-sm hover:opacity-90 active:scale-95 transition-all"><span class="material-symbols-outlined text-lg" style="font-variation-settings:'FILL' 1;">shopping_cart_checkout</span> Confirmer l'achat</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function switchTab(tab) {
  document.getElementById('tab-orders').classList.toggle('hidden', tab !== 'orders');
  document.getElementById('tab-achats').classList.toggle('hidden', tab !== 'achats');
  document.getElementById('tab-orders-btn').classList.toggle('active', tab === 'orders');
  document.getElementById('tab-achats-btn').classList.toggle('active', tab === 'achats');
  ['orders','achats'].forEach(t => {
    const el = document.getElementById('side-' + t);
    if (!el) return;
    if (t === tab) {
      el.classList.add('bg-blue-50','text-blue-700','font-bold');
      el.classList.remove('text-slate-600','hover:bg-slate-100');
    } else {
      el.classList.remove('bg-blue-50','text-blue-700','font-bold');
      el.classList.add('text-slate-600','hover:bg-slate-100');
    }
  });
}
function toggleAccordion(id) {
  const body = document.getElementById(id);
  const chev = document.getElementById('chev-' + id.replace('pharm-',''));
  body.classList.toggle('open');
  chev.classList.toggle('open');
}
let currentItems = [];
function openAchatModal(pharmacyId, pharmName, items) {
  document.getElementById('achat-pharm-name').textContent = pharmName;
  document.getElementById('achatPharmacyId').value = pharmacyId;
  currentItems = items;

  const list = document.getElementById('achatItemsList');
  const pending = items.filter(i => i.achat == 0);

  if (pending.length === 0) {
    list.innerHTML = '<p class="text-sm text-center text-on-surface-variant py-6">Tous les articles ont déjà été achetés.</p>';
  } else {
    list.innerHTML = `
      <div class="flex items-center justify-between mb-3">
        <p class="text-xs font-bold text-on-surface-variant uppercase tracking-wider">Sélectionner les articles à acheter</p>
        <button type="button" onclick="selectAllAchat()" class="text-xs text-primary font-bold hover:underline">Tout sélectionner</button>
      </div>` +
      pending.map(item => `
        <label class="flex items-center gap-3 p-3 rounded-xl border border-outline-variant/20 cursor-pointer hover:border-primary/40 hover:bg-primary/5 transition-all has-[:checked]:border-primary has-[:checked]:bg-primary/8">
          <input type="checkbox" class="achat-checkbox accent-primary" value="${item.link_id}" checked/>
          <div class="flex-1">
            <p class="font-semibold text-sm text-on-surface">${item.item_name}</p>
            <p class="text-xs text-on-surface-variant">Qté commandée : <strong class="text-primary">${item.contiti}</strong></p>
          </div>
          <span class="text-xs bg-surface-container text-on-surface-variant px-2 py-1 rounded-full font-bold">Stock: ${item.stock_contiti}</span>
        </label>`).join('');
  }

  document.getElementById('achatModal').classList.add('open');
}
function selectAllAchat(){ document.querySelectorAll('.achat-checkbox').forEach(cb=>cb.checked=true); }
function submitAchat(){
  const checked = Array.from(document.querySelectorAll('.achat-checkbox:checked'));
  if (checked.length === 0) { alert('Veuillez sélectionner au moins un article.'); return; }
  const container = document.getElementById('achatHiddenIds');
  container.innerHTML = checked.map(cb => `<input type="hidden" name="link_ids[]" value="${cb.value}"/>`).join('');
  document.getElementById('achatForm').submit();
}
function closeAchatModal(){ document.getElementById('achatModal').classList.remove('open'); }
document.getElementById('achatModal').addEventListener('click', function(e){ if(e.target===this) closeAchatModal(); });
</script>
</body>
</html>
 