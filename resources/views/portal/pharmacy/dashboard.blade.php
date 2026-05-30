@php
    $pharmacyName = $pharmacyName ?? trim((string) session('pharmacy_name', 'Pharmacy'));
    $orders = $orders ?? [];
    $products = $products ?? [];
  $medicineSuggestions = $medicineSuggestions ?? [];
    $totalOrders = $totalOrders ?? count($orders);
    $pendingOrders = $pendingOrders ?? count(array_filter($orders, fn($o) => in_array($o['status'] ?? '', ['PENDING_COMMERCIAL_REVIEW'])));
    $activeOrders = $activeOrders ?? count(array_filter($orders, fn($o) => in_array($o['status'] ?? '', ['COMMERCIALLY_CONFIRMED','READY_FOR_DISPATCH','ASSIGNED_TO_DELIVERY','PICKED_UP','IN_TRANSIT'])));
    $completedOrders = $completedOrders ?? count(array_filter($orders, fn($o) => ($o['status'] ?? '') === 'COMPLETED'));
    $cancelledOrders = $cancelledOrders ?? count(array_filter($orders, fn($o) => in_array($o['status'] ?? '', ['CANCELLED','CANCELLED_AFTER_RETURN'])));
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>TronSport Medicamon | Pharmacy Portal</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<script>
  tailwind.config = {
    darkMode: "class",
    theme: { extend: {
      colors: {
        "primary-fixed-dim": "#a2c9ff",
        tertiary: "#186a22",
        secondary: "#4c616c",
        "on-primary": "#ffffff",
        background: "#f8f9fa",
        "inverse-surface": "#2e3132",
        "surface-tint": "#0060a8",
        "inverse-on-surface": "#f0f1f2",
        "on-error": "#ffffff",
        "secondary-container": "#cfe6f2",
        "on-primary-container": "#fdfcff",
        "on-secondary-container": "#526772",
        "surface-container-lowest": "#ffffff",
        "on-primary-fixed": "#001c38",
        "tertiary-container": "#358438",
        "surface-container-high": "#e7e8e9",
        "on-tertiary": "#ffffff",
        "primary-container": "#0077ce",
        "surface-bright": "#f8f9fa",
        "surface-container-highest": "#e1e3e4",
        "on-background": "#191c1d",
        "secondary-fixed": "#cfe6f2",
        "inverse-primary": "#a2c9ff",
        "surface-dim": "#d9dadb",
        "surface-variant": "#e1e3e4",
        "on-secondary": "#ffffff",
        error: "#ba1a1a",
        "outline-variant": "#c0c7d4",
        surface: "#f8f9fa",
        "on-surface": "#191c1d",
        "error-container": "#ffdad6",
        "primary-fixed": "#d3e4ff",
        "surface-container": "#edeeef",
        "on-error-container": "#93000a",
        "on-secondary-fixed": "#071e27",
        primary: "#005ea4",
        outline: "#707783",
        "surface-container-low": "#f3f4f5",
        "on-surface-variant": "#404752"
      },
      fontFamily: { headline: ["Manrope"], body: ["Inter"], label: ["Inter"] },
      borderRadius: { DEFAULT: "0.125rem", lg: "0.25rem", xl: "0.5rem", full: "0.75rem" },
    }},
  }
</script>
<style>
  .material-symbols-outlined{font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;}
  .material-symbols-filled{font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24;}
  body{font-family:'Inter',sans-serif;}
  @keyframes fadeIn{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:translateY(0)}}
  .fade-in{animation:fadeIn 0.35s ease both;}
  .fade-in-1{animation-delay:.05s}.fade-in-2{animation-delay:.1s}.fade-in-3{animation-delay:.15s}.fade-in-4{animation-delay:.2s}
  .modal-overlay{display:none;position:fixed;inset:0;z-index:100;background:rgba(0,0,0,0.45);backdrop-filter:blur(4px);align-items:center;justify-content:center;}
  .modal-overlay.open{display:flex;}
  @keyframes slideUp{from{opacity:0;transform:translateY(28px) scale(0.97)}to{opacity:1;transform:translateY(0) scale(1)}}
  .modal-box{animation:slideUp 0.3s cubic-bezier(0.16,1,0.3,1) both;}
  @keyframes shake{0%,100%{transform:translateX(0)}20%{transform:translateX(-5px)}40%{transform:translateX(5px)}60%{transform:translateX(-3px)}80%{transform:translateX(3px)}}
  .shake{animation:shake 0.4s ease;}
  .filter-btn{background:white;color:#404752;border-color:#c0c7d4;}
  .filter-btn:hover{background:#f3f4f5;}
  .active-filter{background:#005ea4!important;color:white!important;border-color:#005ea4!important;}
  .nav-active{background:#eff6ff;color:#1d4ed8;font-weight:700;}
  .step-done .step-dot{background:#186a22;border-color:#186a22;}
  .step-active .step-dot{background:#005ea4;border-color:#005ea4;box-shadow:0 0 0 3px rgba(0,94,164,0.2);}
  .step-pending .step-dot{background:white;border-color:#c0c7d4;}
  .step-line{flex:1;height:2px;background:#e1e3e4;}
  .step-done .step-line{background:#186a22;}
</style>
</head>
<body class="bg-surface text-on-surface font-body">

{{-- ── HEADER ── --}}
<header class="bg-white/80 backdrop-blur-lg shadow-sm sticky top-0 z-50 flex justify-between items-center px-6 py-3">
  <div class="flex items-center gap-8">
    <span class="text-xl font-extrabold tracking-tighter text-blue-800 font-headline">TronSport Medicamon</span>
    <span class="hidden md:block text-xs font-bold text-on-surface-variant bg-surface-container-low px-3 py-1 rounded-full border border-outline-variant/30">Pharmacy Portal</span>
  </div>
  <div class="flex items-center gap-3">
    <span class="hidden sm:block text-sm font-semibold text-on-surface-variant">{{ $pharmacyName }}</span>
    <button class="p-2 hover:bg-slate-50 rounded-full transition-colors relative" onclick="toggleNotifPanel()">
      <span class="material-symbols-outlined text-slate-600">notifications</span>
      @if(isset($unreadNotifications) && $unreadNotifications > 0)
        <span class="absolute top-1 right-1 w-2 h-2 bg-error rounded-full"></span>
      @endif
    </button>
    <a href="{{ route('logout') }}" class="p-2 hover:bg-slate-50 rounded-full transition-colors" title="Logout">
      <span class="material-symbols-outlined text-slate-600">logout</span>
    </a>
  </div>
</header>

<div class="flex min-h-screen">

  {{-- ── SIDEBAR ── --}}
  <aside class="bg-slate-50 h-screen w-64 border-r border-slate-200 flex flex-col gap-2 p-4 fixed left-0 top-[60px] hidden lg:flex">
    <div class="mb-4 px-2">
      <h3 class="font-headline font-bold text-blue-900">Pharmacy</h3>
      <p class="text-xs text-on-surface-variant">{{ $pharmacyName }}</p>
    </div>
    <nav class="flex-1 flex flex-col gap-1">
      <a href="#overview-section" class="nav-active rounded-lg flex items-center gap-3 px-3 py-2.5 hover:translate-x-1 transition-transform" id="nav-overview" onclick="showSection('overview')">
        <span class="material-symbols-outlined">dashboard</span><span class="text-sm">Overview</span>
      </a>
      <a href="#" class="text-slate-600 hover:bg-slate-100 flex items-center gap-3 px-3 py-2.5 rounded-lg hover:translate-x-1 transition-transform" id="nav-create" onclick="showSection('create'); return false;">
        <span class="material-symbols-outlined">add_circle</span><span class="text-sm">New Order</span>
      </a>
      <a href="#" class="text-slate-600 hover:bg-slate-100 flex items-center gap-3 px-3 py-2.5 rounded-lg hover:translate-x-1 transition-transform" id="nav-orders" onclick="showSection('orders'); return false;">
        <span class="material-symbols-outlined">package_2</span><span class="text-sm">My Orders</span>
      </a>
      <a href="#" class="text-slate-600 hover:bg-slate-100 flex items-center gap-3 px-3 py-2.5 rounded-lg hover:translate-x-1 transition-transform" id="nav-tracking" onclick="showSection('tracking'); return false;">
        <span class="material-symbols-outlined">local_shipping</span><span class="text-sm">Track Delivery</span>
      </a>
      <a href="{{ route('logout') }}" class="text-red-500 hover:bg-red-50 flex items-center gap-3 px-3 py-2.5 rounded-lg hover:translate-x-1 transition-transform mt-2">
        <span class="material-symbols-outlined">logout</span><span class="text-sm font-bold">Logout</span>
      </a>
    </nav>
  </aside>

  {{-- ── MAIN ── --}}
  <main class="flex-1 lg:ml-64 p-4 lg:p-8 space-y-8 bg-surface">

    {{-- ── SUCCESS / ERROR MESSAGES ── --}}
    @if (!empty($success))
    <div class="fade-in flex items-center gap-3 bg-tertiary/10 text-tertiary border border-tertiary/20 px-5 py-3.5 rounded-xl font-semibold text-sm">
      <span class="material-symbols-outlined">check_circle</span>{!! $success !!}
    </div>
    @endif
    @if (!empty($error))
    <div class="fade-in flex items-center gap-3 bg-error/10 text-error border border-error/20 px-5 py-3.5 rounded-xl font-semibold text-sm">
      <span class="material-symbols-outlined">error</span>{!! $error !!}
    </div>
    @endif

    {{-- ════════════════════════════════════ --}}
    {{-- SECTION 1 — OVERVIEW               --}}
    {{-- ════════════════════════════════════ --}}
    <div id="section-overview" class="section-panel space-y-8">
      <div class="fade-in">
        <h1 class="font-headline text-3xl font-extrabold tracking-tight text-on-surface">Dashboard</h1>
        <p class="text-on-surface-variant font-medium mt-1">Welcome back, {{ $pharmacyName }}. Here is your order summary.</p>
      </div>

      {{-- STAT CARDS --}}
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 fade-in fade-in-1">
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm p-5 flex flex-col gap-3">
          <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center">
            <span class="material-symbols-outlined text-primary">package_2</span>
          </div>
          <div>
            <p class="text-2xl font-extrabold font-headline text-on-surface">{{ $totalOrders }}</p>
            <p class="text-xs text-on-surface-variant font-semibold mt-0.5">Total Orders</p>
          </div>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm p-5 flex flex-col gap-3">
          <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center">
            <span class="material-symbols-outlined text-amber-600">hourglass_top</span>
          </div>
          <div>
            <p class="text-2xl font-extrabold font-headline text-on-surface">{{ $pendingOrders }}</p>
            <p class="text-xs text-on-surface-variant font-semibold mt-0.5">Pending Review</p>
          </div>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm p-5 flex flex-col gap-3">
          <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center">
            <span class="material-symbols-outlined text-blue-600">local_shipping</span>
          </div>
          <div>
            <p class="text-2xl font-extrabold font-headline text-on-surface">{{ $activeOrders }}</p>
            <p class="text-xs text-on-surface-variant font-semibold mt-0.5">In Progress</p>
          </div>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm p-5 flex flex-col gap-3">
          <div class="w-10 h-10 rounded-xl bg-tertiary/10 flex items-center justify-center">
            <span class="material-symbols-outlined text-tertiary">check_circle</span>
          </div>
          <div>
            <p class="text-2xl font-extrabold font-headline text-on-surface">{{ $completedOrders }}</p>
            <p class="text-xs text-on-surface-variant font-semibold mt-0.5">Completed</p>
          </div>
        </div>
      </div>

      {{-- RECENT ORDERS TABLE --}}
      <div class="fade-in fade-in-2 bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm p-6">
        <div class="flex items-center justify-between mb-5">
          <h2 class="font-headline font-bold text-xl text-on-surface">Recent Orders</h2>
          <button onclick="showSection('orders')" class="text-xs font-bold text-primary hover:underline">View all</button>
        </div>
        @if(empty($orders))
          <div class="flex flex-col items-center py-12 text-on-surface-variant">
            <span class="material-symbols-outlined text-5xl mb-3 opacity-30">inbox</span>
            <p class="font-bold">No orders yet.</p>
            <button onclick="showSection('create')" class="mt-4 px-5 py-2 rounded-xl bg-primary text-white text-sm font-bold hover:bg-primary/90 transition-colors">Place your first order</button>
          </div>
        @else
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead>
              <tr class="border-b border-outline-variant/20">
                <th class="text-left px-4 py-3 text-xs font-bold text-on-surface-variant uppercase tracking-wider">Order ID</th>
                <th class="text-left px-4 py-3 text-xs font-bold text-on-surface-variant uppercase tracking-wider">Date</th>
                <th class="text-left px-4 py-3 text-xs font-bold text-on-surface-variant uppercase tracking-wider">Status</th>
                <th class="text-left px-4 py-3 text-xs font-bold text-on-surface-variant uppercase tracking-wider">Payment</th>
                <th class="text-left px-4 py-3 text-xs font-bold text-on-surface-variant uppercase tracking-wider">Total</th>
                <th class="text-right px-4 py-3"></th>
              </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/10">
              @foreach(array_slice($orders, 0, 5) as $order)
              @php
                $status = $order['status'] ?? 'UNKNOWN';
                $statusConfig = [
                  'PENDING_COMMERCIAL_REVIEW' => ['label'=>'Pending Review','color'=>'bg-amber-100 text-amber-700','icon'=>'hourglass_top'],
                  'COMMERCIALLY_CONFIRMED'    => ['label'=>'Confirmed','color'=>'bg-blue-100 text-blue-700','icon'=>'verified'],
                  'READY_FOR_DISPATCH'        => ['label'=>'Ready','color'=>'bg-purple-100 text-purple-700','icon'=>'inventory_2'],
                  'ASSIGNED_TO_DELIVERY'      => ['label'=>'Assigned','color'=>'bg-indigo-100 text-indigo-700','icon'=>'assignment_ind'],
                  'PICKED_UP'                 => ['label'=>'Picked Up','color'=>'bg-cyan-100 text-cyan-700','icon'=>'shopping_bag'],
                  'IN_TRANSIT'                => ['label'=>'In Transit','color'=>'bg-blue-100 text-blue-700','icon'=>'local_shipping'],
                  'DELIVERED'                 => ['label'=>'Delivered','color'=>'bg-green-100 text-green-700','icon'=>'done_all'],
                  'COMPLETED'                 => ['label'=>'Completed','color'=>'bg-tertiary/10 text-tertiary','icon'=>'check_circle'],
                  'RETURNED'                  => ['label'=>'Returned','color'=>'bg-orange-100 text-orange-700','icon'=>'assignment_return'],
                  'CANCELLED'                 => ['label'=>'Cancelled','color'=>'bg-error/10 text-error','icon'=>'cancel'],
                  'CANCELLED_AFTER_RETURN'    => ['label'=>'Cancelled','color'=>'bg-error/10 text-error','icon'=>'cancel'],
                ][$status] ?? ['label'=>$status,'color'=>'bg-surface-container text-on-surface-variant','icon'=>'help'];
                $payMethod = $order['payment_method'] ?? '';
                $payConfig = ['ONLINE'=>['label'=>'Online','color'=>'bg-blue-50 text-blue-600'],'CASH'=>['label'=>'Cash','color'=>'bg-amber-50 text-amber-600'],'PARTIAL'=>['label'=>'Partial','color'=>'bg-purple-50 text-purple-600']][$payMethod] ?? ['label'=>$payMethod,'color'=>'bg-surface-container text-on-surface-variant'];
              @endphp
              <tr class="hover:bg-surface-container-low transition-colors">
                <td class="px-4 py-3 font-bold text-primary">#{{ $order['order_id'] ?? '—' }}</td>
                <td class="px-4 py-3 text-on-surface-variant text-xs">{{ isset($order['created_at']) ? \Carbon\Carbon::parse($order['created_at'])->format('d M Y') : '—' }}</td>
                <td class="px-4 py-3">
                  <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold {{ $statusConfig['color'] }}">
                    <span class="material-symbols-outlined text-xs" style="font-size:14px">{{ $statusConfig['icon'] }}</span>
                    {{ $statusConfig['label'] }}
                  </span>
                </td>
                <td class="px-4 py-3">
                  <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $payConfig['color'] }}">{{ $payConfig['label'] }}</span>
                </td>
                <td class="px-4 py-3 font-semibold">{{ number_format($order['total_amount'] ?? 0, 2) }} DA</td>
                <td class="px-4 py-3 text-right">
                  <button onclick="openTrackingModal(@js($order))" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-surface-container text-on-surface text-xs font-bold hover:bg-surface-container-high transition-colors">
                    <span class="material-symbols-outlined text-sm" style="font-size:14px">visibility</span>Track
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

    {{-- ════════════════════════════════════ --}}
    {{-- SECTION 2 — CREATE ORDER           --}}
    {{-- ════════════════════════════════════ --}}
    <div id="section-create" class="section-panel hidden space-y-6">

    <div class="fade-in">
        <h1 class="font-headline text-3xl font-extrabold tracking-tight text-on-surface">
            New Order
        </h1>

        <p class="text-on-surface-variant font-medium mt-1">
            Add medicines, quantities and order details.
        </p>
    </div>

    <div class="fade-in fade-in-1 bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm p-6 lg:p-8">

          <form method="POST"
            action="{{ route('pharmacy.dashboard') }}"
            id="orderForm"
            class="space-y-8"
            onsubmit="return prepareOrderForm();">

            @csrf
            <input type="hidden" name="action" value="create_order">
            <input type="hidden" name="total_amount" id="hidden_total_amount" value="0">
            <input type="hidden" name="package_number" value="1">

            {{-- PRODUCTS --}}
            <div>

                <h3 class="font-headline font-bold text-base text-on-surface mb-4 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-full bg-primary text-white text-xs font-bold flex items-center justify-center">
                        1
                    </span>
                    Medicines
                </h3>

                <datalist id="medicine-suggestions">
                    @foreach($medicineSuggestions as $suggestion)
                        <option value="{{ $suggestion }}"></option>
                    @endforeach
                </datalist>

                <div id="product-list" class="space-y-3">

                    <div class="product-row grid grid-cols-1 md:grid-cols-[1fr_140px] gap-3">

                        <input
                            list="medicine-suggestions"
                            name="items[0][medicine_name]"
                            placeholder="Medicine name"
                            class="w-full rounded-xl border border-outline-variant bg-white px-4 py-3 text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none">

                        <input
                            type="number"
                            min="1"
                            value="1"
                            name="items[0][quantity]"
                            placeholder="Qty"
                            class="w-full rounded-xl border border-outline-variant bg-white px-4 py-3 text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none">

                    </div>

                </div>

                <button
                    type="button"
                    id="add-product-btn"
                    class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-outline-variant hover:bg-surface-container text-sm font-bold transition-colors">

                    <span class="material-symbols-outlined text-base">
                        add
                    </span>

                    Add Medicine

                </button>

            </div>

            {{-- NOTES --}}
            <div>

                <h3 class="font-headline font-bold text-base text-on-surface mb-4 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-full bg-primary text-white text-xs font-bold flex items-center justify-center">
                        2
                    </span>
                    Notes
                </h3>

                <textarea
                    rows="5"
                    name="historique"
                    placeholder="Order notes..."
                    class="w-full rounded-xl border border-outline-variant bg-white px-4 py-3 text-sm resize-none focus:border-primary focus:ring-1 focus:ring-primary outline-none">{{ old('historique') }}</textarea>

            </div>

            {{-- URGENCY --}}
            <div>

                <h3 class="font-headline font-bold text-base text-on-surface mb-4 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-full bg-primary text-white text-xs font-bold flex items-center justify-center">
                        3
                    </span>
                    Priority
                </h3>

                <label class="flex items-center gap-3 p-4 rounded-xl border border-outline-variant/20 bg-surface-container-low cursor-pointer">

                    <input
                        type="checkbox"
                        name="is_urgent"
                        value="1"
                        class="accent-primary w-4 h-4">

                    <div>
                        <p class="font-bold text-sm text-on-surface">
                            Urgent Order
                        </p>

                        <p class="text-xs text-on-surface-variant">
                            Mark this order as urgent.
                        </p>
                    </div>

                </label>

            </div>

            {{-- PAYMENT METHOD --}}
            <input
                type="hidden"
                name="payment_method"
                value="cash">

            {{-- ACTIONS --}}
            <div class="flex gap-3 pt-2">

                <button
                    type="button"
                    onclick="showSection('overview')"
                    class="flex-1 sm:flex-none px-6 py-3 rounded-xl border border-outline-variant text-on-surface-variant font-bold text-sm hover:bg-surface-container transition-colors">

                    Cancel

                </button>

                <button
                    type="submit"
                    class="flex-1 py-3 rounded-xl bg-gradient-to-r from-primary to-primary-container text-white font-bold text-sm shadow-md hover:opacity-90 active:scale-[0.98] transition-all flex items-center justify-center gap-2">

                    <span class="material-symbols-outlined">
                        send
                    </span>

                    Create Order

                </button>

            </div>

        </form>

    </div>

</div>

    {{-- ════════════════════════════════════ --}}
    {{-- SECTION 3 — MY ORDERS              --}}
    {{-- ════════════════════════════════════ --}}
    <div id="section-orders" class="section-panel hidden space-y-6">
      <div class="fade-in">
        <h1 class="font-headline text-3xl font-extrabold tracking-tight text-on-surface">My Orders</h1>
        <p class="text-on-surface-variant font-medium mt-1">All your orders and their current status.</p>
      </div>

      {{-- FILTERS --}}
      <div class="fade-in fade-in-1 flex flex-wrap gap-2">
        <button class="filter-btn active-filter px-4 py-2 rounded-full text-xs font-bold border transition-colors" data-filter="all" onclick="filterOrders('all')">All ({{ $totalOrders }})</button>
        <button class="filter-btn px-4 py-2 rounded-full text-xs font-bold border transition-colors" data-filter="pending" onclick="filterOrders('pending')">Pending ({{ $pendingOrders }})</button>
        <button class="filter-btn px-4 py-2 rounded-full text-xs font-bold border transition-colors" data-filter="active" onclick="filterOrders('active')">In Progress ({{ $activeOrders }})</button>
        <button class="filter-btn px-4 py-2 rounded-full text-xs font-bold border transition-colors" data-filter="completed" onclick="filterOrders('completed')">Completed ({{ $completedOrders }})</button>
        <button class="filter-btn px-4 py-2 rounded-full text-xs font-bold border transition-colors" data-filter="cancelled" onclick="filterOrders('cancelled')">Cancelled ({{ $cancelledOrders }})</button>
      </div>

      <div class="fade-in fade-in-2 bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm">
        @if(empty($orders))
          <div class="flex flex-col items-center py-16 text-on-surface-variant">
            <span class="material-symbols-outlined text-5xl mb-3 opacity-30">inbox</span>
            <p class="font-bold">No orders found.</p>
          </div>
        @else
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead>
              <tr class="border-b border-outline-variant/20">
                <th class="text-left px-5 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-wider">Order ID</th>
                <th class="text-left px-5 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-wider">Date</th>
                <th class="text-left px-5 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-wider">Products</th>
                <th class="text-left px-5 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-wider">Status</th>
                <th class="text-left px-5 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-wider">Payment</th>
                <th class="text-left px-5 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-wider">Total</th>
                <th class="text-right px-5 py-4"></th>
              </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/10">
              @foreach($orders as $order)
              @php
                $status = $order['status'] ?? 'UNKNOWN';
                $statusConfig = [
                  'PENDING_COMMERCIAL_REVIEW' => ['label'=>'Pending Review','color'=>'bg-amber-100 text-amber-700','icon'=>'hourglass_top','group'=>'pending'],
                  'COMMERCIALLY_CONFIRMED'    => ['label'=>'Confirmed','color'=>'bg-blue-100 text-blue-700','icon'=>'verified','group'=>'active'],
                  'READY_FOR_DISPATCH'        => ['label'=>'Ready','color'=>'bg-purple-100 text-purple-700','icon'=>'inventory_2','group'=>'active'],
                  'ASSIGNED_TO_DELIVERY'      => ['label'=>'Assigned','color'=>'bg-indigo-100 text-indigo-700','icon'=>'assignment_ind','group'=>'active'],
                  'PICKED_UP'                 => ['label'=>'Picked Up','color'=>'bg-cyan-100 text-cyan-700','icon'=>'shopping_bag','group'=>'active'],
                  'IN_TRANSIT'                => ['label'=>'In Transit','color'=>'bg-blue-100 text-blue-700','icon'=>'local_shipping','group'=>'active'],
                  'DELIVERED'                 => ['label'=>'Delivered','color'=>'bg-green-100 text-green-700','icon'=>'done_all','group'=>'active'],
                  'COMPLETED'                 => ['label'=>'Completed','color'=>'bg-tertiary/10 text-tertiary','icon'=>'check_circle','group'=>'completed'],
                  'RETURNED'                  => ['label'=>'Returned','color'=>'bg-orange-100 text-orange-700','icon'=>'assignment_return','group'=>'active'],
                  'CANCELLED'                 => ['label'=>'Cancelled','color'=>'bg-error/10 text-error','icon'=>'cancel','group'=>'cancelled'],
                  'CANCELLED_AFTER_RETURN'    => ['label'=>'Cancelled','color'=>'bg-error/10 text-error','icon'=>'cancel','group'=>'cancelled'],
                ][$status] ?? ['label'=>$status,'color'=>'bg-surface-container text-on-surface-variant','icon'=>'help','group'=>'all'];
                $payMethod = $order['payment_method'] ?? '';
                $payConfig = ['ONLINE'=>['label'=>'Online','color'=>'bg-blue-50 text-blue-600'],'CASH'=>['label'=>'Cash','color'=>'bg-amber-50 text-amber-600'],'PARTIAL'=>['label'=>'Partial','color'=>'bg-purple-50 text-purple-600']][$payMethod] ?? ['label'=>'—','color'=>'bg-surface-container text-on-surface-variant'];
                $itemCount = count($order['items'] ?? []);
              @endphp
              <tr class="order-row hover:bg-surface-container-low transition-colors" data-status="{{ $statusConfig['group'] }}">
                <td class="px-5 py-4 font-bold text-primary">#{{ $order['order_id'] ?? '—' }}</td>
                <td class="px-5 py-4 text-on-surface-variant text-xs">{{ isset($order['created_at']) ? \Carbon\Carbon::parse($order['created_at'])->format('d M Y') : '—' }}</td>
                <td class="px-5 py-4 text-on-surface-variant text-xs">{{ $itemCount }} item{{ $itemCount !== 1 ? 's' : '' }}</td>
                <td class="px-5 py-4">
                  <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold {{ $statusConfig['color'] }}">
                    <span class="material-symbols-outlined" style="font-size:14px">{{ $statusConfig['icon'] }}</span>
                    {{ $statusConfig['label'] }}
                  </span>
                </td>
                <td class="px-5 py-4">
                  <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $payConfig['color'] }}">{{ $payConfig['label'] }}</span>
                </td>
                <td class="px-5 py-4 font-semibold">{{ number_format($order['total_amount'] ?? 0, 2) }} DA</td>
                <td class="px-5 py-4 text-right">
                  <button onclick="openTrackingModal(@js($order))" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-primary text-white text-xs font-bold hover:bg-primary/90 active:scale-95 transition-all">
                    <span class="material-symbols-outlined" style="font-size:14px">timeline</span>Track
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

    {{-- ════════════════════════════════════ --}}
    {{-- SECTION 4 — TRACKING               --}}
    {{-- ════════════════════════════════════ --}}
    <div id="section-tracking" class="section-panel hidden space-y-6">
      <div class="fade-in">
        <h1 class="font-headline text-3xl font-extrabold tracking-tight text-on-surface">Track Delivery</h1>
        <p class="text-on-surface-variant font-medium mt-1">Follow your active orders in real time.</p>
      </div>

      @php $activeDeliveries = array_filter($orders ?? [], fn($o) => in_array($o['status'] ?? '', ['ASSIGNED_TO_DELIVERY','PICKED_UP','IN_TRANSIT'])); @endphp

      @if(empty($activeDeliveries))
        <div class="fade-in bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm flex flex-col items-center py-16 text-on-surface-variant">
          <span class="material-symbols-outlined text-5xl mb-3 opacity-30">local_shipping</span>
          <p class="font-bold">No active deliveries at the moment.</p>
          <p class="text-sm mt-1">Orders in transit will appear here.</p>
        </div>
      @else
        @foreach($activeDeliveries as $order)
        @php
          $status = $order['status'] ?? '';
          $stages = ['PENDING_COMMERCIAL_REVIEW','COMMERCIALLY_CONFIRMED','READY_FOR_DISPATCH','ASSIGNED_TO_DELIVERY','PICKED_UP','IN_TRANSIT','DELIVERED','COMPLETED'];
          $currentIndex = array_search($status, $stages);
        @endphp
        <div class="fade-in bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm p-6 space-y-5">
          <div class="flex items-center justify-between">
            <div>
              <span class="font-extrabold text-primary font-headline text-lg">#{{ $order['order_id'] ?? '—' }}</span>
              <span class="ml-3 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700">
                <span class="material-symbols-outlined" style="font-size:14px">local_shipping</span>In Delivery
              </span>
            </div>
            <span class="text-xs text-on-surface-variant">{{ isset($order['created_at']) ? \Carbon\Carbon::parse($order['created_at'])->format('d M Y') : '' }}</span>
          </div>

          {{-- PROGRESS STEPS --}}
          <div class="flex items-center gap-0">
            @foreach([
              ['icon'=>'storefront','label'=>'Order Placed','key'=>'PENDING_COMMERCIAL_REVIEW'],
              ['icon'=>'verified','label'=>'Confirmed','key'=>'COMMERCIALLY_CONFIRMED'],
              ['icon'=>'inventory_2','label'=>'Prepared','key'=>'READY_FOR_DISPATCH'],
              ['icon'=>'assignment_ind','label'=>'Assigned','key'=>'ASSIGNED_TO_DELIVERY'],
              ['icon'=>'shopping_bag','label'=>'Picked Up','key'=>'PICKED_UP'],
              ['icon'=>'local_shipping','label'=>'In Transit','key'=>'IN_TRANSIT'],
              ['icon'=>'check_circle','label'=>'Delivered','key'=>'DELIVERED'],
            ] as $stepIndex => $step)
            @php
              $stepPos = array_search($step['key'], $stages);
              $isDone = $currentIndex !== false && $stepPos < $currentIndex;
              $isActive = $currentIndex !== false && $stepPos === $currentIndex;
              $stateClass = $isDone ? 'step-done' : ($isActive ? 'step-active' : 'step-pending');
            @endphp
            <div class="flex flex-col items-center {{ $stepIndex > 0 ? 'flex-1' : '' }}">
              @if($stepIndex > 0)
              <div class="step-line w-full mb-2 {{ $isDone || $isActive ? 'bg-primary' : 'bg-outline-variant/30' }}" style="height:2px;margin-bottom:0;align-self:center;margin-top:14px;"></div>
              @endif
            </div>
            <div class="flex flex-col items-center min-w-[60px]">
              <div class="step-dot w-7 h-7 rounded-full border-2 flex items-center justify-center mb-1.5 transition-all
                {{ $isDone ? 'bg-tertiary border-tertiary' : ($isActive ? 'bg-primary border-primary shadow-md' : 'bg-white border-outline-variant') }}">
                <span class="material-symbols-outlined text-white {{ $isDone || $isActive ? '' : 'text-outline' }}" style="font-size:14px;{{ !$isDone && !$isActive ? 'color:#707783' : '' }}">{{ $step['icon'] }}</span>
              </div>
              <span class="text-[10px] font-semibold text-center leading-tight {{ $isActive ? 'text-primary font-bold' : ($isDone ? 'text-tertiary' : 'text-on-surface-variant') }}">{{ $step['label'] }}</span>
            </div>
            @endforeach
          </div>

          {{-- DELIVERY PERSON INFO --}}
          @if(!empty($order['delivery_person_name']))
          <div class="flex items-center gap-3 bg-surface-container-low rounded-xl p-4 border border-outline-variant/15">
            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary to-primary-container flex items-center justify-center text-white font-bold font-headline text-sm flex-shrink-0">
              {{ strtoupper(substr($order['delivery_person_name'] ?? 'D', 0, 1)) }}
            </div>
            <div>
              <p class="font-bold text-on-surface text-sm">{{ $order['delivery_person_name'] ?? '—' }}</p>
              <p class="text-xs text-on-surface-variant">Delivery Person</p>
            </div>
            @if(!empty($order['delivery_person_phone']))
            <a href="tel:{{ $order['delivery_person_phone'] }}" class="ml-auto p-2 rounded-full bg-primary/10 hover:bg-primary/20 transition-colors">
              <span class="material-symbols-outlined text-primary" style="font-size:18px">call</span>
            </a>
            @endif
          </div>
          @endif
        </div>
        @endforeach
      @endif
    </div>

  </main>
</div>

{{-- ════════════════════════════════════════ --}}
{{-- TRACKING MODAL                          --}}
{{-- ════════════════════════════════════════ --}}
<div id="trackingModal" class="modal-overlay" onclick="closeModalOutside(event,'trackingModal')">
  <div class="modal-box bg-surface-container-lowest w-full max-w-lg mx-4 rounded-2xl shadow-2xl border border-outline-variant/15 p-8 max-h-[90vh] overflow-y-auto">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h2 class="font-headline font-extrabold text-xl text-on-surface">Order Timeline</h2>
        <p class="text-xs text-on-surface-variant mt-0.5">Order <span id="modal-tracking-id" class="font-bold text-primary"></span></p>
      </div>
      <button onclick="closeModal('trackingModal')" class="p-2 hover:bg-surface-container rounded-lg transition-colors">
        <span class="material-symbols-outlined">close</span>
      </button>
    </div>

    {{-- STATUS TIMELINE --}}
    <div id="modal-timeline" class="space-y-3 mb-6"></div>

    {{-- PAYMENT INFO --}}
    <div id="modal-payment" class="bg-surface-container-low rounded-xl p-4 border border-outline-variant/15 space-y-2">
      <p class="text-xs font-bold text-on-surface-variant uppercase tracking-wider">Payment</p>
      <div class="flex items-center justify-between">
        <span id="modal-payment-method" class="text-sm font-bold text-on-surface"></span>
        <span id="modal-payment-status" class="px-2.5 py-1 rounded-full text-xs font-bold"></span>
      </div>
      <div id="modal-proof-section" class="hidden">
        <p class="text-xs text-on-surface-variant mb-1">Payment Proof</p>
        <a id="modal-proof-link" href="#" target="_blank" class="text-xs text-primary font-bold underline">View proof image</a>
      </div>
    </div>

    <button onclick="closeModal('trackingModal')" class="w-full mt-5 py-3 rounded-xl border border-outline-variant text-on-surface-variant font-bold text-sm hover:bg-surface-container transition-colors">
      Close
    </button>
  </div>
</div>

<script>
// ── SECTION NAVIGATION ──
function showSection(name) {
  document.querySelectorAll('.section-panel').forEach(p => p.classList.add('hidden'));
  document.getElementById('section-' + name)?.classList.remove('hidden');
  document.querySelectorAll('aside nav a').forEach(a => {
    a.classList.remove('nav-active','bg-blue-50','text-blue-700','font-bold');
    a.classList.add('text-slate-600','hover:bg-slate-100');
  });
  const active = document.getElementById('nav-' + name);
  if (active) {
    active.classList.add('nav-active');
    active.classList.remove('text-slate-600','hover:bg-slate-100');
  }
}

// ── ORDER FILTER ──
function filterOrders(f) {
  document.querySelectorAll('.filter-btn').forEach(b => b.classList.toggle('active-filter', b.dataset.filter === f));
  document.querySelectorAll('.order-row').forEach(row => {
    const s = row.dataset.status;
    let show = f === 'all' || s === f;
    row.style.display = show ? '' : 'none';
  });
}

// ── PRODUCT SELECTION ──
function toggleQtyInput(checkbox, pid) {
  const card = checkbox.closest('.product-card');
  const qtyWrapper = card.querySelector('.qty-wrapper');
  const checkIcon = card.querySelector('.product-check-icon span');
  const checkBox = card.querySelector('.product-check-icon');
  if (checkbox.checked) {
    qtyWrapper.classList.remove('hidden');
    checkIcon.classList.remove('hidden');
    checkBox.classList.add('border-primary','bg-primary/10');
  } else {
    qtyWrapper.classList.add('hidden');
    checkIcon.classList.add('hidden');
    checkBox.classList.remove('border-primary','bg-primary/10');
  }
  updateTotal();
}

function updateTotal() {
  let total = 0;
  document.querySelectorAll('.product-checkbox:checked').forEach(cb => {
    const card = cb.closest('.product-card');
    const qtyInput = card.querySelector('.qty-input');
    const price = parseFloat(qtyInput?.dataset.price || 0);
    const qty = parseInt(qtyInput?.value || 1);
    total += price * qty;
  });
  document.getElementById('order-total').textContent = total.toFixed(2) + ' DA';
}

function validateOrder() {
  const checked = document.querySelectorAll('.product-checkbox:checked');
  if (checked.length === 0) {
    document.getElementById('no-product-error').classList.remove('hidden');
    document.getElementById('products-grid').classList.add('shake');
    setTimeout(() => document.getElementById('products-grid').classList.remove('shake'), 400);
    return false;
  }
  document.getElementById('no-product-error').classList.add('hidden');
  return true;
}

function prepareOrderForm() {
  if (!validateOrder()) return false;
  // set hidden total
  const totalText = document.getElementById('order-total').textContent || '0';
  const total = parseFloat(totalText.replace(/[^0-9\.\-]/g, '')) || 0;
  document.getElementById('hidden_total_amount').value = Math.round(total);
  return true;
}

// ── TRACKING MODAL ──
function openTrackingModal(order) {
  document.getElementById('modal-tracking-id').textContent = '#' + (order.order_id || '—');

  // Timeline
  const timeline = document.getElementById('modal-timeline');
  const history = order.status_history || [];
  if (history.length === 0) {
    const statusLabels = {
      'PENDING_COMMERCIAL_REVIEW': 'Order submitted — awaiting commercial review',
      'COMMERCIALLY_CONFIRMED': 'Order confirmed by commercial team',
      'READY_FOR_DISPATCH': 'Order prepared and ready for delivery',
      'ASSIGNED_TO_DELIVERY': 'Assigned to delivery person',
      'PICKED_UP': 'Package picked up by delivery person',
      'IN_TRANSIT': 'Order in transit',
      'DELIVERED': 'Order delivered',
      'COMPLETED': 'Order completed',
      'RETURNED': 'Delivery returned',
      'CANCELLED': 'Order cancelled',
      'CANCELLED_AFTER_RETURN': 'Order cancelled after return',
    };
    const icons = {
      'PENDING_COMMERCIAL_REVIEW':'hourglass_top','COMMERCIALLY_CONFIRMED':'verified',
      'READY_FOR_DISPATCH':'inventory_2','ASSIGNED_TO_DELIVERY':'assignment_ind',
      'PICKED_UP':'shopping_bag','IN_TRANSIT':'local_shipping','DELIVERED':'done_all',
      'COMPLETED':'check_circle','RETURNED':'assignment_return',
      'CANCELLED':'cancel','CANCELLED_AFTER_RETURN':'cancel',
    };
    const s = order.status || '';
    timeline.innerHTML = `<div class="flex items-start gap-3 p-3 bg-surface-container-low rounded-xl">
      <span class="material-symbols-outlined text-primary mt-0.5" style="font-size:18px">${icons[s] || 'circle'}</span>
      <div><p class="font-bold text-sm text-on-surface">${statusLabels[s] || s}</p>
      <p class="text-xs text-on-surface-variant mt-0.5">Current status</p></div></div>`;
  } else {
    timeline.innerHTML = history.map((h, i) => `
      <div class="flex items-start gap-3 ${i === 0 ? 'p-3 bg-primary/5 border border-primary/20 rounded-xl' : 'p-3 rounded-xl hover:bg-surface-container-low'}">
        <div class="w-2 h-2 rounded-full mt-2 flex-shrink-0 ${i === 0 ? 'bg-primary' : 'bg-outline-variant'}"></div>
        <div class="flex-1">
          <p class="font-bold text-sm text-on-surface">${h.to_status || ''}</p>
          <p class="text-xs text-on-surface-variant mt-0.5">${h.note || ''}</p>
        </div>
        <span class="text-xs text-on-surface-variant flex-shrink-0">${h.created_at ? new Date(h.created_at).toLocaleDateString('en-GB', {day:'2-digit',month:'short',hour:'2-digit',minute:'2-digit'}) : ''}</span>
      </div>`).join('');
  }

  // Payment
  const methodLabels = {'ONLINE':'Online Payment','CASH':'Cash on Delivery','PARTIAL':'Partial Payment'};
  const statusColors = {'PAID':'bg-tertiary/10 text-tertiary','PARTIALLY_PAID':'bg-amber-100 text-amber-700','UNPAID':'bg-error/10 text-error'};
  document.getElementById('modal-payment-method').textContent = methodLabels[order.payment_method] || order.payment_method || '—';
  const ps = document.getElementById('modal-payment-status');
  const pStatus = order.payment_status || 'UNPAID';
  ps.textContent = pStatus.replace('_',' ');
  ps.className = 'px-2.5 py-1 rounded-full text-xs font-bold ' + (statusColors[pStatus] || 'bg-surface-container text-on-surface-variant');

  const proofSection = document.getElementById('modal-proof-section');
  if (order.proof_image_url) {
    proofSection.classList.remove('hidden');
    document.getElementById('modal-proof-link').href = order.proof_image_url;
  } else {
    proofSection.classList.add('hidden');
  }

  document.getElementById('trackingModal').classList.add('open');
}

function closeModal(id) { document.getElementById(id).classList.remove('open'); }
function closeModalOutside(e, id) { if (e.target === document.getElementById(id)) closeModal(id); }

function toggleNotifPanel() {
  // Extend with notification panel logic as needed
}

let productIndex = 1;

document.getElementById('add-product-btn')?.addEventListener('click', function () {

    const list = document.getElementById('product-list');

    const row = document.createElement('div');

    row.className =
        'product-row grid grid-cols-1 md:grid-cols-[1fr_140px_auto] gap-3';

    row.innerHTML = `
        <input
            list="medicine-suggestions"
            name="items[${productIndex}][medicine_name]"
            placeholder="Medicine name"
            class="w-full rounded-xl border border-outline-variant bg-white px-4 py-3 text-sm">

        <input
            type="number"
            min="1"
            value="1"
            name="items[${productIndex}][quantity]"
            placeholder="Qty"
            class="w-full rounded-xl border border-outline-variant bg-white px-4 py-3 text-sm">

        <button
            type="button"
            class="remove-product px-4 rounded-xl bg-red-50 text-red-600 font-bold">
            Remove
        </button>
    `;

    list.appendChild(row);

    productIndex++;
});

document.addEventListener('click', function(e) {

    if (e.target.classList.contains('remove-product')) {
        e.target.closest('.product-row').remove();
    }

});
</script>
</body>
</html>
