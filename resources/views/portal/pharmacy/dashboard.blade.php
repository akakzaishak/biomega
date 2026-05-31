@php
/**
 * Converted legacy pharmacy_dashboard.php into a Blade view.
 * Controller must supply these variables:
 * $pharmacyName, $initialSection, $success, $error,
 * $totalOrders, $pendingOrders, $activeOrders, $completedOrders, $cancelledOrders,
 * $orders (array), $medicineSuggestions (array), $pharmacyNifSafe
 */
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

<header class="bg-white/80 backdrop-blur-lg shadow-sm sticky top-0 z-50 flex justify-between items-center px-6 py-3">
  <div class="flex items-center gap-8">
    <span class="text-xl font-extrabold tracking-tighter text-blue-800 font-headline">TronSport Medicamon</span>
    <span class="hidden md:block text-xs font-bold text-on-surface-variant bg-surface-container-low px-3 py-1 rounded-full border border-outline-variant/30">Pharmacy Portal</span>
  </div>
  <div class="flex items-center gap-3">
    <span class="hidden sm:block text-sm font-semibold text-on-surface-variant">{{ $pharmacyName ?? '' }}</span>
    <button class="p-2 hover:bg-slate-50 rounded-full transition-colors relative" onclick="toggleNotifPanel()">
      <span class="material-symbols-outlined text-slate-600">notifications</span>
    </button>
    <a href="{{ url('/logout') }}" class="p-2 hover:bg-slate-50 rounded-full transition-colors" title="Logout">
      <span class="material-symbols-outlined text-slate-600">logout</span>
    </a>
  </div>
</header>

<div class="flex min-h-screen">

  <aside class="bg-slate-50 h-screen w-64 border-r border-slate-200 flex flex-col gap-2 p-4 fixed left-0 top-[60px] hidden lg:flex">
    <div class="mb-4 px-2">
      <h3 class="font-headline font-bold text-blue-900">Pharmacy</h3>
      <p class="text-xs text-on-surface-variant">{{ $pharmacyName ?? '' }}</p>
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
      <a href="{{ url('/logout') }}" class="text-red-500 hover:bg-red-50 flex items-center gap-3 px-3 py-2.5 rounded-lg hover:translate-x-1 transition-transform mt-2">
        <span class="material-symbols-outlined">logout</span><span class="text-sm font-bold">Logout</span>
      </a>
    </nav>
  </aside>

  <main class="flex-1 lg:ml-64 p-4 lg:p-8 space-y-8 bg-surface">

    @if (!empty($success))
    <div class="fade-in flex items-center gap-3 bg-tertiary/10 text-tertiary border border-tertiary/20 px-5 py-3.5 rounded-xl font-semibold text-sm">
      <span class="material-symbols-outlined">check_circle</span>{!! $success !!}
    </div>
    @endif
    @if (!empty($error))
    <div class="fade-in flex items-center gap-3 bg-error/10 text-error border border-error/20 px-5 py-3.5 rounded-xl font-semibold text-sm">
      <span class="material-symbols-outlined">error</span>{{ $error }}</div>
    @endif

    <div id="section-overview" class="section-panel space-y-8">
      <div class="fade-in">
        <h1 class="font-headline text-3xl font-extrabold tracking-tight text-on-surface">Dashboard</h1>
        <p class="text-on-surface-variant font-medium mt-1">Welcome back, {{ $pharmacyName ?? '' }}. Here is your order summary.</p>
      </div>

      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 fade-in fade-in-1">
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm p-5 flex flex-col gap-3">
          <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center">
            <span class="material-symbols-outlined text-primary">package_2</span>
          </div>
          <div>
            <p class="text-2xl font-extrabold font-headline text-on-surface">{{ $totalOrders ?? 0 }}</p>
            <p class="text-xs text-on-surface-variant font-semibold mt-0.5">Total Orders</p>
          </div>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm p-5 flex flex-col gap-3">
          <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center">
            <span class="material-symbols-outlined text-amber-600">hourglass_top</span>
          </div>
          <div>
            <p class="text-2xl font-extrabold font-headline text-on-surface">{{ $pendingOrders ?? 0 }}</p>
            <p class="text-xs text-on-surface-variant font-semibold mt-0.5">Pending Review</p>
          </div>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm p-5 flex flex-col gap-3">
          <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center">
            <span class="material-symbols-outlined text-blue-600">local_shipping</span>
          </div>
          <div>
            <p class="text-2xl font-extrabold font-headline text-on-surface">{{ $activeOrders ?? 0 }}</p>
            <p class="text-xs text-on-surface-variant font-semibold mt-0.5">In Progress</p>
          </div>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm p-5 flex flex-col gap-3">
          <div class="w-10 h-10 rounded-xl bg-tertiary/10 flex items-center justify-center">
            <span class="material-symbols-outlined text-tertiary">check_circle</span>
          </div>
          <div>
            <p class="text-2xl font-extrabold font-headline text-on-surface">{{ $completedOrders ?? 0 }}</p>
            <p class="text-xs text-on-surface-variant font-semibold mt-0.5">Completed</p>
          </div>
        </div>
      </div>

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
              @foreach(array_slice($orders ?? [], 0, 5) as $order)
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
                <td class="px-4 py-3 text-on-surface-variant text-xs">{{ isset($order['created_at']) ? date('d M Y', strtotime($order['created_at'])) : '—' }}</td>
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
                  <button onclick="openTrackingModal(@json($order))" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-surface-container text-on-surface text-xs font-bold hover:bg-surface-container-high transition-colors">
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
