@php
    $firstname = $firstname ?? (string) session('firstname', 'Admin');
    $lastname = $lastname ?? (string) session('lastname', '');
    $orders = $orders ?? [];
    $deliveryPersons = $deliveryPersons ?? [];
    $query = $query ?? '';
    $totalOrders = count($orders);
    $delivered = count(array_filter($orders, fn ($o) => (int) ($o['Status'] ?? 0) === 1));
    $notDelivered = count(array_filter($orders, fn ($o) => (int) ($o['Status'] ?? 0) === 0));
    $assigned = count(array_filter($orders, fn ($o) => !empty($o['deliveryperson_id'])));
    $unassigned = $totalOrders - $assigned;
@endphp

<style>
  .material-symbols-outlined{font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;}
  @keyframes fadeIn{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:translateY(0)}}
  .fade-in{animation:fadeIn 0.35s ease both;}
  .fade-in-1{animation-delay:.05s}.fade-in-2{animation-delay:.1s}
  .fade-in-3{animation-delay:.15s}.fade-in-4{animation-delay:.2s}
  .modal-overlay{display:none;position:fixed;inset:0;z-index:100;background:rgba(0,0,0,0.45);backdrop-filter:blur(4px);align-items:center;justify-content:center;}
  .modal-overlay.open{display:flex;}
  @keyframes slideUp{from{opacity:0;transform:translateY(28px) scale(0.97)}to{opacity:1;transform:translateY(0) scale(1)}}
  .modal-box{animation:slideUp 0.3s cubic-bezier(0.16,1,0.3,1) both;}
  @keyframes shake{0%,100%{transform:translateX(0)}20%{transform:translateX(-5px)}40%{transform:translateX(5px)}60%{transform:translateX(-3px)}80%{transform:translateX(3px)}}
  .shake{animation:shake 0.4s ease;}
  .filter-btn{background:white;color:#404752;border-color:#c0c7d4;}
  .filter-btn:hover{background:#f3f4f5;}
  .active-filter{background:#005ea4!important;color:white!important;border-color:#005ea4!important;}
</style>

<div class="space-y-8">
  <div class="fade-in">
    <h1 class="font-headline text-3xl font-extrabold tracking-tight text-on-surface">Orders</h1>
    <p class="text-on-surface-variant font-medium mt-1">Manage, assign and track all delivery orders.</p>
  </div>

  <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="fade-in fade-in-1 bg-surface-container-lowest p-5 rounded-xl border border-outline-variant/15 hover:shadow-md transition-shadow">
      <span class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Total Orders</span>
      <div class="mt-2 text-3xl font-headline font-extrabold text-on-surface">{{ $totalOrders }}</div>
    </div>
    <div class="fade-in fade-in-2 bg-surface-container-lowest p-5 rounded-xl border border-outline-variant/15 hover:shadow-md transition-shadow">
      <span class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Delivered</span>
      <div class="mt-2 text-3xl font-headline font-extrabold text-tertiary">{{ $delivered }}</div>
    </div>
    <div class="fade-in fade-in-3 bg-surface-container-lowest p-5 rounded-xl border border-outline-variant/15 hover:shadow-md transition-shadow">
      <span class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Pending</span>
      <div class="mt-2 text-3xl font-headline font-extrabold text-orange-500">{{ $notDelivered }}</div>
    </div>
    <div class="fade-in fade-in-4 bg-surface-container-lowest p-5 rounded-xl border border-outline-variant/15 hover:shadow-md transition-shadow">
      <span class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Unassigned</span>
      <div class="mt-2 text-3xl font-headline font-extrabold text-error">{{ $unassigned }}</div>
    </div>
  </div>

  @if(!empty($success))
    <div class="fade-in flex items-center gap-3 bg-tertiary/10 text-tertiary border border-tertiary/20 px-5 py-3.5 rounded-xl font-semibold text-sm">
      <span class="material-symbols-outlined">check_circle</span>{!! $success !!}
    </div>
  @endif
  @if(!empty($error))
    <div class="fade-in shake flex items-center gap-3 bg-error-container text-on-error-container border border-error/20 px-5 py-3.5 rounded-xl font-semibold text-sm">
      <span class="material-symbols-outlined">error</span>{{ $error }}
    </div>
  @endif

  <div class="fade-in bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm overflow-hidden">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-6 border-b border-outline-variant/15">
      <div>
        <h2 class="font-headline font-bold text-xl text-on-surface">All Orders</h2>
        <p class="text-xs text-on-surface-variant mt-0.5">{{ $totalOrders }} total orders</p>
      </div>
      <div class="flex flex-wrap gap-2">
        <button type="button" onclick="filterOrders('all')" class="filter-btn active-filter px-3 py-1.5 rounded-full text-xs font-bold border transition-all" data-filter="all">All</button>
        <button type="button" onclick="filterOrders('unassigned')" class="filter-btn px-3 py-1.5 rounded-full text-xs font-bold border transition-all" data-filter="unassigned">Unassigned</button>
        <button type="button" onclick="filterOrders('assigned')" class="filter-btn px-3 py-1.5 rounded-full text-xs font-bold border transition-all" data-filter="assigned">Assigned</button>
        <button type="button" onclick="filterOrders('delivered')" class="filter-btn px-3 py-1.5 rounded-full text-xs font-bold border transition-all" data-filter="delivered">Delivered</button>
        <button type="button" onclick="filterOrders('pending')" class="filter-btn px-3 py-1.5 rounded-full text-xs font-bold border transition-all" data-filter="pending">Pending</button>
      </div>
    </div>

    @if(empty($orders))
      <div class="flex flex-col items-center py-20 text-on-surface-variant">
        <span class="material-symbols-outlined text-5xl mb-3 opacity-30">inbox</span>
        <p class="font-bold text-lg">No orders yet</p>
      </div>
    @else
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-surface-container-low">
            <tr>
              <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Tracking #</th>
              <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Date</th>
              <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Amount (DZD)</th>
              <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Packages</th>
              <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Status</th>
              <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Assigned To</th>
              <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Urgent</th>
              <th class="px-5 py-4 text-right text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-outline-variant/10">
            @foreach($orders as $ord)
              @php
                $isDel = (int) ($ord['Status'] ?? 0) === 1;
                $isAsgn = !empty($ord['deliveryperson_id']);
                $isUrg = (int) ($ord['IsUrgen'] ?? 0) === 1;
                $fc = ($isDel ? 'row-delivered ' : 'row-pending ') . ($isAsgn ? 'row-assigned' : 'row-unassigned');
              @endphp
              <tr class="hover:bg-surface-container-low/60 transition-colors order-row {{ $fc }}" data-status="{{ $isDel ? 'delivered' : 'pending' }}" data-assigned="{{ $isAsgn ? 'assigned' : 'unassigned' }}">
                <td class="px-5 py-4">
                  <span class="font-mono font-bold text-primary">#{{ $ord['Tracking'] }}</span>
                  @if($isUrg)
                    <span class="ml-1.5 text-[9px] font-black bg-error text-white px-1.5 py-0.5 rounded uppercase">URGENT</span>
                  @endif
                </td>
                <td class="px-5 py-4 text-on-surface-variant">{{ $ord['Date'] }}</td>
                <td class="px-5 py-4">
                  <form method="POST" action="{{ route('admin.orders') }}" class="flex items-center gap-1.5">
                    @csrf
                    <input type="hidden" name="action" value="update_amount" />
                    <input type="hidden" name="order_id" value="{{ $ord['Tracking'] }}" />
                    <input type="number" name="amount" value="{{ intval($ord['otalAmount']) }}" min="0" class="w-24 px-2 py-1.5 bg-surface-container-high border-none rounded-lg text-sm font-semibold focus:ring-2 focus:ring-primary/20" />
                    <button type="submit" title="Save" class="p-1.5 rounded-lg bg-primary/10 text-primary hover:bg-primary/20 transition-colors">
                      <span class="material-symbols-outlined text-base">save</span>
                    </button>
                  </form>
                </td>
                <td class="px-5 py-4 font-semibold">{{ intval($ord['PackageNumber']) }}</td>
                <td class="px-5 py-4">
                  @if($isDel)
                    <span class="inline-flex items-center gap-1 text-[11px] font-black bg-tertiary/10 text-tertiary px-2.5 py-1 rounded-full uppercase"><span class="w-1.5 h-1.5 rounded-full bg-tertiary"></span>Delivered</span>
                  @else
                    <span class="inline-flex items-center gap-1 text-[11px] font-black bg-orange-100 text-orange-600 px-2.5 py-1 rounded-full uppercase"><span class="w-1.5 h-1.5 rounded-full bg-orange-400"></span>Pending</span>
                  @endif
                </td>
                <td class="px-5 py-4">
                  @if($isAsgn)
                    <div class="flex items-center gap-2">
                      <div class="w-7 h-7 rounded-full bg-primary/10 flex items-center justify-center text-xs font-bold text-primary">
                        {{ strtoupper(substr((string) ($ord['dp_first'] ?? ''), 0, 1) . substr((string) ($ord['dp_last'] ?? ''), 0, 1)) }}
                      </div>
                      <span class="font-semibold text-on-surface text-xs">{{ trim((string) ($ord['dp_first'] ?? '') . ' ' . (string) ($ord['dp_last'] ?? '')) }}</span>
                    </div>
                  @else
                    <span class="text-xs text-on-surface-variant italic">Not assigned</span>
                  @endif
                </td>
                <td class="px-5 py-4">
                  @if($isUrg)
                    <span class="material-symbols-outlined text-error text-xl" style="font-variation-settings:'FILL' 1;">priority_high</span>
                  @else
                    <span class="text-outline text-xs">—</span>
                  @endif
                </td>
                <td class="px-5 py-4 text-right">
                  <button type="button" onclick="openAssignModal(@js($ord['Tracking']), @js($ord['assigned_pharmacy'] ?? ''), @js($ord['deliveryperson_id'] ?? ''))" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-primary text-white text-xs font-bold hover:bg-primary/90 active:scale-95 transition-all">
                    <span class="material-symbols-outlined text-sm">assignment_ind</span>
                    {{ $isAsgn ? 'Reassign' : 'Assign' }}
                  </button>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>
</div>

<div id="assignModal" class="modal-overlay" onclick="closeModalOutside(event,'assignModal')">
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
      <input type="hidden" name="action" value="assign" />
      <input type="hidden" name="order_id" id="modal-order-id-input" />
      <input type="hidden" name="pharmacy_id" id="modal-pharmacy-id-input" />
      <div>
        <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-2">Delivery Person</label>
        @if(empty($deliveryPersons))
          <p class="text-sm text-error font-semibold">No delivery persons available.</p>
        @else
          <div class="space-y-2 max-h-64 overflow-y-auto pr-1" id="dp-radio-list">
            @foreach($deliveryPersons as $dp)
              @php
                $ini = strtoupper(substr((string) ($dp['FirstName'] ?? ''), 0, 1) . substr((string) ($dp['LastName'] ?? ''), 0, 1));
              @endphp
              <label class="flex items-center gap-3 p-3 rounded-xl border border-outline-variant/20 hover:bg-surface-container-low cursor-pointer transition-colors has-[:checked]:border-primary has-[:checked]:bg-primary/5">
                <input type="radio" name="dp_phone" value="{{ $dp['PhoneNumber'] ?? '' }}" class="accent-primary w-4 h-4" />
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
        @if(!empty($deliveryPersons))
          <button type="submit" class="flex-1 py-3 rounded-xl bg-gradient-to-r from-primary to-primary-container text-white font-bold text-sm shadow-md hover:opacity-90 active:scale-[0.98] transition-all flex items-center justify-center gap-2">
            <span class="material-symbols-outlined text-lg">assignment_ind</span>Confirm
          </button>
        @endif
      </div>
    </form>
  </div>
</div>

<button class="fixed bottom-8 right-8 w-14 h-14 bg-primary text-on-primary rounded-full shadow-2xl flex items-center justify-center hover:scale-110 active:scale-95 transition-all z-[60] group">
  <span class="material-symbols-outlined text-2xl" style="font-variation-settings:'FILL' 1;">smart_toy</span>
  <div class="absolute right-full mr-4 bg-inverse-surface text-inverse-on-surface px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity">How can I help you today?</div>
</button>

<script>
  function openAssignModal(orderId, pharmacyId, currentDp) {
    document.getElementById('modal-order-id').textContent = '#'+orderId;
    document.getElementById('modal-order-id-input').value = orderId;
    document.getElementById('modal-pharmacy-id-input').value = pharmacyId;
    document.querySelectorAll('#dp-radio-list input[type=radio]').forEach(r => r.checked = (r.value===currentDp));
    document.getElementById('assignModal').classList.add('open');
  }
  function closeModal(id){document.getElementById(id).classList.remove('open');}
  function closeModalOutside(e,id){if(e.target===document.getElementById(id))closeModal(id);}
  function filterOrders(f){
    document.querySelectorAll('.filter-btn').forEach(b=>b.classList.toggle('active-filter',b.dataset.filter===f));
    document.querySelectorAll('.order-row').forEach(row=>{
      const s=row.dataset.status, a=row.dataset.assigned;
      let show=false;
      if(f==='all')show=true;
      if(f==='delivered')show=s==='delivered';
      if(f==='pending')show=s==='pending';
      if(f==='assigned')show=a==='assigned';
      if(f==='unassigned')show=a==='unassigned';
      row.style.display=show?'':'none';
    });
  }
</script>
 