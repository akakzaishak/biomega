@php
    $total_pharmacies = count($pharmacies ?? []);
    $order_data = $order_data ?? [];
    $search = $query ?? '';
@endphp

<style>
  .pharm-row:hover { background: #f3f4f5; }
  .status-pill {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 10px;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 999px;
    text-transform: uppercase;
    letter-spacing: .04em;
  }
</style>

<div class="fade-in">
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
      <h1 class="text-3xl font-extrabold tracking-tight text-on-surface">Pharmacies</h1>
      <p class="text-on-surface-variant text-sm mt-1"><span class="font-bold text-primary">{{ $total_pharmacies }}</span> pharmacie(s) enregistrée(s) — triées par NIF</p>
    </div>
    <a href="{{ route('register.pharmacy') }}" class="inline-flex items-center gap-2 bg-primary text-white px-5 py-2.5 rounded-xl font-bold text-sm hover:opacity-90 active:scale-95 transition-all shadow-sm">
      <span class="material-symbols-outlined text-lg">add_circle</span>Ajouter une Pharmacie
    </a>
  </div>

  @if(session('success'))
    <div class="fade-in flex items-center gap-3 bg-green-50 text-green-800 border border-green-200 px-4 py-3 rounded-xl text-sm font-semibold mt-4">
      <span class="material-symbols-outlined text-green-600">check_circle</span>
      {{ session('success') }}
    </div>
  @endif

  @if(session('error'))
    <div class="fade-in flex items-center gap-3 bg-error-container text-on-error-container border border-error/20 px-4 py-3 rounded-xl text-sm font-semibold mt-4">
      <span class="material-symbols-outlined">error</span>
      {{ session('error') }}
      @if(session('pending_delete_nif'))
        <form method="POST" action="{{ route('admin.pharmacies') }}" class="ml-auto flex-shrink-0">
          @csrf
          <input type="hidden" name="delete_nif" value="{{ session('pending_delete_nif') }}" />
          <input type="hidden" name="force_delete" value="1" />
          <input type="hidden" name="q" value="{{ $search }}" />
          <button type="submit" class="bg-error text-white px-4 py-1.5 rounded-lg text-xs font-bold hover:opacity-90">Forcer la suppression</button>
        </form>
      @endif
    </div>
  @endif

  <div class="fade-in mt-4">
    <form method="GET" action="{{ route('admin.pharmacies') }}" class="flex gap-2 flex-wrap">
      <div class="relative flex-1 min-w-[220px] max-w-md">
        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">search</span>
        <input type="text" name="q" value="{{ $search }}" placeholder="NIF, nom, téléphone, wilaya..." class="w-full pl-10 pr-4 py-2.5 bg-surface-container-lowest border border-outline-variant/30 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:outline-none" />
      </div>
      <button class="bg-primary text-white px-5 py-2.5 rounded-xl font-semibold text-sm">Chercher</button>
      @if($search !== '')
        <a href="{{ route('admin.pharmacies') }}" class="px-4 py-2.5 border border-outline-variant/30 rounded-xl text-sm font-medium text-on-surface-variant hover:bg-surface-container transition-colors">Effacer</a>
      @endif
    </form>
  </div>

  @php
    $total_orders_all   = array_sum(array_map(fn($d) => (int) ($d['total'] ?? 0), $order_data));
    $total_delivered    = array_sum(array_map(fn($d) => (int) ($d['delivered'] ?? 0), $order_data));
    $total_pending      = array_sum(array_map(fn($d) => (int) ($d['pending'] ?? 0), $order_data));
    $total_urgent       = array_sum(array_map(fn($d) => (int) ($d['urgent'] ?? 0), $order_data));
  @endphp

  <div class="fade-in grid grid-cols-2 sm:grid-cols-4 gap-3 mt-4">
    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/15 px-4 py-3 flex items-center gap-3">
      <div class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
        <span class="material-symbols-outlined text-primary text-lg">local_pharmacy</span>
      </div>
      <div>
        <p class="text-xs text-on-surface-variant">Pharmacies</p>
        <p class="text-lg font-extrabold text-on-surface">{{ $total_pharmacies }}</p>
      </div>
    </div>
    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/15 px-4 py-3 flex items-center gap-3">
      <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
        <span class="material-symbols-outlined text-primary text-lg">package_2</span>
      </div>
      <div>
        <p class="text-xs text-on-surface-variant">Total commandes</p>
        <p class="text-lg font-extrabold text-on-surface">{{ $total_orders_all }}</p>
      </div>
    </div>
    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/15 px-4 py-3 flex items-center gap-3">
      <div class="w-9 h-9 rounded-lg bg-green-50 flex items-center justify-center flex-shrink-0">
        <span class="material-symbols-outlined text-green-600 text-lg">check_circle</span>
      </div>
      <div>
        <p class="text-xs text-on-surface-variant">Livrées</p>
        <p class="text-lg font-extrabold text-green-700">{{ $total_delivered }}</p>
      </div>
    </div>
    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/15 px-4 py-3 flex items-center gap-3">
      <div class="w-9 h-9 rounded-lg bg-error-container flex items-center justify-center flex-shrink-0">
        <span class="material-symbols-outlined text-error text-lg">priority_high</span>
      </div>
      <div>
        <p class="text-xs text-on-surface-variant">Urgentes</p>
        <p class="text-lg font-extrabold text-error">{{ $total_urgent }}</p>
      </div>
    </div>
  </div>

  <div class="fade-in bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm overflow-hidden mt-4">
    @if(empty($pharmacies))
      <div class="flex flex-col items-center justify-center py-20 text-center px-6">
        <span class="material-symbols-outlined text-6xl text-outline/30 mb-4">local_pharmacy</span>
        <h3 class="font-bold text-lg text-on-surface mb-2">Aucune pharmacie trouvée</h3>
        <p class="text-on-surface-variant text-sm mb-6">{{ $search ? 'Aucun résultat pour "'.e($search).'".' : 'Aucune pharmacie enregistrée.' }}</p>
        <a href="{{ route('register.pharmacy') }}" class="inline-flex items-center gap-2 bg-primary text-white px-5 py-2.5 rounded-xl font-bold text-sm hover:opacity-90">
          <span class="material-symbols-outlined text-lg">add_circle</span>Enregistrer la première pharmacie
        </a>
      </div>
    @else
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-surface-container-low border-b border-outline-variant/20">
            <tr>
              <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-on-surface-variant/70 w-16">NIF</th>
              <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Responsable</th>
              <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Téléphone</th>
              <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Localisation</th>
              <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Horaire</th>
              <th class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Commandes liées</th>
              <th class="px-5 py-3.5 text-center text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-outline-variant/10">
            @foreach($pharmacies as $i => $p)
              @php
                $nif = (int) ($p['NIF'] ?? 0);
                $data = $order_data[$nif] ?? ['total'=>0,'orders'=>[],'delivered'=>0,'pending'=>0,'urgent'=>0];
              @endphp
              <tr class="pharm-row transition-colors" style="animation:fadeIn .3s ease {{ $i * 0.04 }}s both;">
                <td class="px-5 py-4">
                  <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-primary/10 text-primary font-extrabold text-xs">#{{ $nif }}</span>
                </td>
                <td class="px-5 py-4">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-primary to-primary-container text-white flex items-center justify-center text-sm font-bold flex-shrink-0">{{ strtoupper(mb_substr($p['FirstName'] ?? '', 0, 1)) }}</div>
                    <div>
                      <p class="font-semibold text-on-surface">{{ ($p['FirstName'] ?? '') . ' ' . ($p['LastName'] ?? '') }}</p>
                      <p class="text-xs text-on-surface-variant">Pharmacien(ne)</p>
                    </div>
                  </div>
                </td>
                <td class="px-5 py-4">
                  <a href="tel:{{ $p['PhoneNumber'] ?? '' }}" class="flex items-center gap-1.5 text-primary font-medium hover:underline"><span class="material-symbols-outlined text-sm">phone</span>{{ $p['PhoneNumber'] ?? '' }}</a>
                </td>
                <td class="px-5 py-4 max-w-[180px]"><div class="flex items-start gap-1.5"><span class="material-symbols-outlined text-sm text-outline mt-0.5">location_on</span><span class="text-on-surface-variant text-xs leading-relaxed">{{ $p['Location'] ?? '' }}</span></div></td>
                <td class="px-5 py-4"><div class="flex items-center gap-1.5 text-on-surface-variant text-xs"><span class="material-symbols-outlined text-sm">schedule</span>{{ $p['WorkTime'] ?? '' }}</div></td>
                <td class="px-5 py-4">
                  @if(($data['total'] ?? 0) === 0)
                    <span class="status-pill bg-surface-container text-on-surface-variant"><span class="material-symbols-outlined text-xs">remove</span> Aucune</span>
                  @else
                    <button onclick='openOrders({{ $nif }}, @js($data['orders']))' class="inline-flex items-center gap-2 bg-primary/10 hover:bg-primary/20 text-primary px-3 py-1.5 rounded-full text-xs font-bold transition-colors mb-1.5"> <span class="material-symbols-outlined text-sm">package_2</span> {{ $data['total'] }} commande{{ $data['total']>1 ? 's' : '' }}</button>
                    <div class="flex flex-wrap gap-1.5">
                      @if(($data['delivered'] ?? 0) > 0)
                        <span class="status-pill bg-green-100 text-green-700">✓ {{ $data['delivered'] }} livrée{{ $data['delivered']>1 ? 's' : '' }}</span>
                      @endif
                      @if(($data['pending'] ?? 0) > 0)
                        <span class="status-pill bg-secondary-container text-on-secondary-container">⏳ {{ $data['pending'] }} en attente</span>
                      @endif
                      @if(($data['urgent'] ?? 0) > 0)
                        <span class="status-pill bg-error-container text-error">🚨 {{ $data['urgent'] }} urgente{{ $data['urgent']>1 ? 's' : '' }}</span>
                      @endif
                    </div>
                  @endif
                </td>
                <td class="px-5 py-4 text-center">
                  <div class="flex items-center justify-center gap-1">
                    <button onclick='confirmDelete({{ $nif }}, @js(($p['FirstName'] ?? '')." ".($p['LastName'] ?? '')), {{ $data['total'] ?? 0 }})' class="p-2 rounded-lg hover:bg-error-container text-error transition-colors" title="Supprimer"><span class="material-symbols-outlined text-lg">delete</span></button>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div class="px-5 py-3 border-t border-outline-variant/20 bg-surface-container-low flex items-center justify-between text-xs text-on-surface-variant">
        <span>{{ $total_pharmacies }} résultat{{ $total_pharmacies>1 ? 's' : '' }}</span>
        <span>Trié par NIF croissant · Données via <code>asined_order</code></span>
      </div>
    @endif
  </div>

</div>

<!-- Delete Modal -->
<div id="deleteModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/50 backdrop-blur-sm p-4">
  <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-8">
    <div class="flex flex-col items-center text-center mb-6">
      <div class="w-16 h-16 rounded-full bg-error-container flex items-center justify-center mb-4">
        <span class="material-symbols-outlined text-error text-3xl">delete_forever</span>
      </div>
      <h2 class="font-extrabold text-xl text-on-surface mb-1">Supprimer la pharmacie ?</h2>
      <p class="text-on-surface-variant text-sm" id="modalPharmName"></p>
    </div>
    <div id="modalOrderWarning" class="hidden mb-5 flex items-center gap-3 bg-error-container text-on-error-container px-4 py-3 rounded-xl text-sm font-semibold">
      <span class="material-symbols-outlined text-lg flex-shrink-0">warning</span>
      <span id="modalOrdersText"></span>
    </div>
    <p class="text-xs text-on-surface-variant text-center mb-6">Cette action est <strong>irréversible</strong>. Les assignations liées seront aussi supprimées.</p>
    <form method="POST" id="deleteForm" action="{{ route('admin.pharmacies') }}">
      @csrf
      <input type="hidden" name="delete_nif" id="deleteNifInput" value="" />
      <input type="hidden" name="q" value="{{ $search }}" />
      <div class="flex gap-3">
        <button type="button" onclick="closeDeleteModal()" class="flex-1 px-5 py-3 border border-outline-variant/40 rounded-xl font-semibold text-sm text-on-surface hover:bg-surface-container transition-colors">Annuler</button>
        <button type="submit" class="flex-1 flex items-center justify-center gap-2 px-5 py-3 bg-error text-white rounded-xl font-bold text-sm hover:opacity-90 active:scale-95 transition-all"><span class="material-symbols-outlined text-lg">delete</span>Supprimer</button>
      </div>
    </form>
  </div>
</div>

<!-- Orders Modal -->
<div id="ordersModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/50 backdrop-blur-sm p-4">
  <div class="bg-white rounded-2xl shadow-2xl max-w-xl w-full max-h-[80vh] flex flex-col">
    <div class="flex items-center justify-between px-6 py-4 border-b border-outline-variant/20 flex-shrink-0">
      <div>
        <h2 class="font-extrabold text-lg text-on-surface">Commandes — Pharmacie <span id="ordersNifLabel" class="text-primary"></span></h2>
        <p class="text-xs text-on-surface-variant mt-0.5" id="ordersTotalLabel"></p>
      </div>
      <button onclick="closeOrdersModal()" class="p-2 hover:bg-surface-container rounded-full transition-colors"><span class="material-symbols-outlined">close</span></button>
    </div>
    <div class="overflow-y-auto flex-1 p-6" id="ordersListContainer"></div>
    <div class="px-6 py-4 border-t border-outline-variant/20 flex-shrink-0"><a href="{{ route('admin.orders') }}" class="text-xs text-primary font-semibold hover:underline flex items-center gap-1"><span class="material-symbols-outlined text-sm">open_in_new</span>Voir toutes les commandes</a></div>
  </div>
</div>

@push('scripts')
<script>
function confirmDelete(nif, name, orderCount) {
  document.getElementById('deleteNifInput').value = nif;
  document.getElementById('modalPharmName').textContent = name + '  (NIF #' + nif + ')';
  const warn = document.getElementById('modalOrderWarning');
  if (orderCount > 0) {
    warn.classList.remove('hidden');
    warn.classList.add('flex');
    document.getElementById('modalOrdersText').textContent = 'Cette pharmacie a ' + orderCount + ' commande(s) assignée(s). Elles seront désassignées.';
  } else {
    warn.classList.add('hidden');
    warn.classList.remove('flex');
  }
  document.getElementById('deleteModal').classList.remove('hidden');
}
function closeDeleteModal() { document.getElementById('deleteModal').classList.add('hidden'); }

function openOrders(nif, orders) {
  document.getElementById('ordersNifLabel').textContent = '#' + nif;
  document.getElementById('ordersTotalLabel').textContent = orders.length + ' commande(s) liée(s)';
  const container = document.getElementById('ordersListContainer');
  if (!orders || orders.length === 0) {
    container.innerHTML = '<p class="text-sm text-on-surface-variant text-center py-8">Aucune commande.</p>';
  } else {
    const statusLabel = (s) => {
      if (s === null || s === undefined) return '<span class="status-pill bg-surface-container text-on-surface-variant">Inconnu</span>';
      return parseInt(s) === 1 ? '<span class="status-pill bg-green-100 text-green-700">✓ Livré</span>' : '<span class="status-pill bg-secondary-container text-on-secondary-container">⏳ En attente</span>';
    };
    container.innerHTML = orders.map(o => `\
      <div class="flex items-center justify-between py-3 border-b border-outline-variant/10 last:border-0">\
        <div class="flex items-center gap-3">\
          <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0 ${parseInt(o.IsUrgen)===1 ? 'bg-error-container' : 'bg-primary/10'}">\
            <span class="material-symbols-outlined text-base ${parseInt(o.IsUrgen)===1 ? 'text-error' : 'text-primary'}">${parseInt(o.IsUrgen)===1 ? 'priority_high' : 'package_2'}</span>\
          </div>\
          <div>\
            <p class="text-sm font-bold text-on-surface">${o.Tracking || o.order_id}</p>\
            <p class="text-xs text-on-surface-variant">${o.Date || '—'} · Livreur: ${o.deliveryperson_id || '—'}</p>\
          </div>\
        </div>\
        <div class="text-right flex flex-col items-end gap-1">\
          ${statusLabel(o.Status)}\
          ${parseInt(o.IsUrgen)===1 ? '<span class="status-pill bg-error-container text-error">🚨 Urgent</span>' : ''}\
        </div>\
      </div>\
    `).join('');
  }
  document.getElementById('ordersModal').classList.remove('hidden');
}
function closeOrdersModal() { document.getElementById('ordersModal').classList.add('hidden'); }

// hide modals on backdrop click
document.getElementById('deleteModal').addEventListener('click', function(e){ if(e.target===this) closeDeleteModal(); });
document.getElementById('ordersModal').addEventListener('click', function(e){ if(e.target===this) closeOrdersModal(); });
</script>
@endpush
