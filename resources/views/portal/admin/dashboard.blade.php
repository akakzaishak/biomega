@php
    $totalOrders = $totalOrders ?? $ordersCount ?? 0;
    $delivered = $delivered ?? 0;
    $pending = $pending ?? 0;
    $revFormatted = $revFormatted ?? (isset($ordersTotal) ? $ordersTotal : '0');
    $totalPharmacies = $totalPharmacies ?? $pharmaciesCount ?? 0;
    $totalEmployees = $totalEmployees ?? $employeesCount ?? 0;
    $deliveredPct = $deliveredPct ?? ($totalOrders>0 ? round(($delivered/$totalOrders)*100) : 0);
    $pendingPct = $pendingPct ?? (100 - ($deliveredPct ?? 0));
    $dpCount = $dpCount ?? 0;
    $unassigned = $unassigned ?? 0;
    $recentOrders = $recentOrders ?? ($recentOrdersList ?? []);
    $recentEmployees = $recentEmployees ?? ($recentEmployeesList ?? []);
    $roleLabels = [
        "commercialservice"=>"Commercial Service","deliverymanager"=>"Delivery Manager",
        "deliveryperson"=>"Delivery Person","stockemployee"=>"Stock Employee",
    ];
    $roleColors = [
        "commercialservice"=>"bg-blue-100 text-blue-700","deliverymanager"=>"bg-purple-100 text-purple-700",
        "deliveryperson"=>"bg-orange-100 text-orange-600","stockemployee"=>"bg-teal-100 text-teal-700",
    ];
@endphp

<div class="fade-in">
    <h1 class="font-headline text-3xl font-extrabold tracking-tight text-on-surface">Admin Dashboard</h1>
    <p class="text-on-surface-variant font-medium mt-1">Welcome back — live operations overview.</p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mt-6">
    <div class="fade-in fade-in-1 bg-surface-container-lowest p-6 rounded-xl border border-outline-variant/15 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
            <span class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Total Orders</span>
            <span class="material-symbols-outlined text-primary/30 text-xl">package_2</span>
        </div>
        <div class="mt-3 flex items-baseline gap-2">
            <span class="text-3xl font-headline font-extrabold text-on-surface">{{ number_format((int)$totalOrders) }}</span>
            @if($unassigned>0)
                <span class="text-[10px] font-bold text-error">{{ $unassigned }} unassigned</span>
            @endif
        </div>
        <div class="mt-2 flex gap-3 text-[11px] font-semibold">
            <span class="text-tertiary">✓ {{ number_format((int)($delivered)) }} delivered</span>
            <span class="text-orange-500">⏳ {{ number_format((int)($pending)) }} pending</span>
        </div>
    </div>

    <div class="fade-in fade-in-2 bg-surface-container-lowest p-6 rounded-xl border border-outline-variant/15 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
            <span class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Revenue</span>
            <span class="material-symbols-outlined text-primary/30 text-xl">payments</span>
        </div>
        <div class="mt-3">
            <span class="text-2xl font-headline font-extrabold text-on-surface">DZD {{ $revFormatted }}</span>
        </div>
        <div class="mt-2 text-[11px] font-semibold text-tertiary">↑ All orders combined</div>
    </div>

    <div class="fade-in fade-in-3 bg-surface-container-lowest p-6 rounded-xl border border-outline-variant/15 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
            <span class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Pharmacies</span>
            <span class="material-symbols-outlined text-primary/30 text-xl">local_pharmacy</span>
        </div>
        <div class="mt-3 flex items-baseline gap-2">
            <span class="text-3xl font-headline font-extrabold text-on-surface">{{ number_format((int)$totalPharmacies) }}</span>
            <span class="text-[10px] font-bold text-on-surface-variant">Registered</span>
        </div>
        <a href="{{ route('admin.pharmacies') }}" class="mt-2 block text-[11px] font-bold text-primary hover:underline">View all →</a>
    </div>

    <div class="fade-in fade-in-4 bg-surface-container-lowest p-6 rounded-xl border border-outline-variant/15 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
            <span class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Employees</span>
            <span class="material-symbols-outlined text-primary/30 text-xl">badge</span>
        </div>
        <div class="mt-3 flex items-baseline gap-2">
            <span class="text-3xl font-headline font-extrabold text-on-surface">{{ number_format((int)$totalEmployees) }}</span>
            <span class="text-[10px] font-bold text-tertiary">Active</span>
        </div>
        <a href="{{ route('admin.employees') }}" class="mt-2 block text-[11px] font-bold text-primary hover:underline">Manage →</a>
    </div>

    <div class="fade-in fade-in-5 bg-surface-container-lowest p-6 rounded-xl border border-outline-variant/15 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
            <span class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Delivery Rate</span>
            <span class="material-symbols-outlined text-primary/30 text-xl">local_shipping</span>
        </div>
        <div class="mt-3 h-2 bg-secondary-container rounded-full overflow-hidden">
            <div class="bg-primary h-full" style="width:{{ $deliveredPct }}%"></div>
        </div>
        <div class="mt-2 flex justify-between text-[11px] font-bold">
            <span class="text-tertiary">{{ $deliveredPct }}% Done</span>
            <span class="text-orange-500">{{ $pendingPct }}% Left</span>
        </div>
    </div>

</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
    <div class="lg:col-span-2 bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6 shadow-sm fade-in">
        <div class="flex items-center justify-between mb-6">
            <h2 class="font-headline font-bold text-lg text-on-surface">Recent Orders</h2>
            <a href="{{ route('admin.orders') }}" class="text-xs font-bold text-primary hover:underline">View All</a>
        </div>
        @if(empty($recentOrders))
            <div class="flex flex-col items-center py-10 text-on-surface-variant">
                <span class="material-symbols-outlined text-4xl mb-2 opacity-30">inbox</span>
                <p class="font-semibold">No orders yet</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach($recentOrders as $ord)
                    @php
                        $isDel = (isset($ord['Status']) && $ord['Status']==1);
                        $isAsgn = !empty($ord['deliveryperson_id'] ?? $ord['deliveryperson_id'] ?? null);
                        $isUrg = (isset($ord['IsUrgen']) && $ord['IsUrgen']==1);
                    @endphp
                    <div class="flex items-center justify-between p-4 bg-surface-container-low rounded-xl hover:bg-surface-container transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="w-9 h-9 rounded-lg {{ $isUrg? 'bg-error-container':'bg-primary/10' }} flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-outlined {{ $isUrg? 'text-error':'text-primary' }} text-base">
                                    {{ $isDel ? 'check_circle' : ($isUrg ? 'priority_high' : 'package_2') }}
                                </span>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-on-surface">#{{ $ord['Tracking'] ?? '—' }} @if($isUrg)<span class="ml-1 text-[9px] font-black bg-error text-white px-1.5 py-0.5 rounded uppercase">URGENT</span>@endif</p>
                                <p class="text-xs text-on-surface-variant">{{ $isAsgn ? ($ord['dp_first'] ?? ($ord['dp_first'] ?? '')) . ' ' . ($ord['dp_last'] ?? '') : 'Not assigned' }} · DZD {{ number_format((float)($ord['otalAmount'] ?? 0)) }}</p>
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0">
                            @if($isDel)
                                <span class="text-[10px] font-black bg-tertiary/10 text-tertiary px-2 py-0.5 rounded-full uppercase">Delivered</span>
                            @else
                                <span class="text-[10px] font-black bg-orange-100 text-orange-600 px-2 py-0.5 rounded-full uppercase">Pending</span>
                            @endif
                            <p class="text-xs text-on-surface-variant mt-1">{{ $ord['Date'] ?? '' }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="space-y-4 fade-in">
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6 shadow-sm">
            <h2 class="font-headline font-bold text-lg text-on-surface mb-4">Quick Actions</h2>
            <div class="space-y-2">
                <a href="{{ route('admin.orders') }}?action=new_emergency" class="w-full flex items-center gap-3 px-4 py-3 bg-primary text-white rounded-xl font-bold text-sm hover:bg-primary/90 active:scale-95 transition-all"><span class="material-symbols-outlined text-lg">add_circle</span>New Emergency Order</a>
                <a href="{{ route('admin.employees') }}?action=add" class="w-full flex items-center gap-3 px-4 py-3 bg-surface-container-low text-on-surface rounded-xl font-bold text-sm border border-outline-variant/20"><span class="material-symbols-outlined text-lg text-primary">person_add</span>Add Employee</a>
                <a href="{{ route('admin.pharmacies') }}?action=register" class="w-full flex items-center gap-3 px-4 py-3 bg-surface-container-low text-on-surface rounded-xl font-bold text-sm border border-outline-variant/20"><span class="material-symbols-outlined text-lg text-primary">local_pharmacy</span>Register Pharmacy</a>
                <a href="{{ route('admin.reports') }}" class="w-full flex items-center gap-3 px-4 py-3 bg-surface-container-low text-on-surface rounded-xl font-bold text-sm border border-outline-variant/20"><span class="material-symbols-outlined text-lg text-primary">bar_chart</span>Generate Report</a>
            </div>
        </div>

        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6 shadow-sm">
            <h2 class="font-headline font-bold text-base text-on-surface mb-4">System Status</h2>
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-on-surface-variant">Database</span>
                    <span class="flex items-center gap-1.5 text-xs font-bold text-tertiary"><span class="w-2 h-2 rounded-full bg-tertiary"></span>Operational</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-on-surface-variant">Delivery Staff</span>
                    <span class="flex items-center gap-1.5 text-xs font-bold text-tertiary"><span class="w-2 h-2 rounded-full bg-tertiary"></span>{{ $dpCount }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-on-surface-variant">Pending Orders</span>
                    <span class="flex items-center gap-1.5 text-xs font-bold {{ $pending>0? 'text-orange-500':'text-tertiary' }}"><span class="w-2 h-2 rounded-full {{ $pending>0? 'bg-orange-400':'bg-tertiary' }}"></span>{{ $pending }} pending</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-on-surface-variant">Unassigned Orders</span>
                    <span class="flex items-center gap-1.5 text-xs font-bold {{ $unassigned>0? 'text-error':'text-tertiary' }}"><span class="w-2 h-2 rounded-full {{ $unassigned>0? 'bg-error':'bg-tertiary' }}"></span>{{ $unassigned }} unassigned</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-on-surface-variant">Last Refresh</span>
                    <span class="text-xs font-bold text-on-surface-variant">{{ now()->format('H:i:s') }}</span>
                </div>
            </div>
        </div>

    </div>

</div>

<div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6 shadow-sm fade-in mt-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="font-headline font-bold text-lg text-on-surface">Employees</h2>
        <a href="{{ route('admin.employees') }}" class="text-xs font-bold text-primary hover:underline">Manage All</a>
    </div>
    @if(empty($recentEmployees))
        <div class="flex flex-col items-center py-10 text-on-surface-variant">
            <span class="material-symbols-outlined text-4xl mb-2 opacity-30">group_off</span>
            <p class="font-semibold">No employees yet</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left border-b border-outline-variant/20">
                        <th class="pb-3 text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Name</th>
                        <th class="pb-3 text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Role</th>
                        <th class="pb-3 text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Phone</th>
                        <th class="pb-3 text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/10">
                    @foreach($recentEmployees as $emp)
                        @php
                            $src = $emp['source'] ?? ($emp['source_table'] ?? '');
                            $colors = $roleColors[$src] ?? 'bg-slate-100 text-slate-600';
                        @endphp
                        <tr class="hover:bg-surface-container-low transition-colors">
                            <td class="py-3.5 font-semibold text-on-surface">{{ ($emp['FirstName'] ?? '') . ' ' . ($emp['LastName'] ?? '') }}</td>
                            <td class="py-3.5"><span class="text-[10px] font-black px-2 py-0.5 rounded-full uppercase {{ $colors }}">{{ $roleLabels[$src] ?? $src }}</span></td>
                            <td class="py-3.5 text-on-surface-variant">{{ $emp['PhoneNumber'] ?? ($emp['employee_phone'] ?? '') }}</td>
                            <td class="py-3.5"><span class="text-[10px] font-black bg-tertiary/10 text-tertiary px-2 py-0.5 rounded-full uppercase">Active</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<!-- AI Assistant Button (kept from original design) -->
<button class="fixed bottom-8 right-8 w-14 h-14 bg-primary text-on-primary rounded-full shadow-2xl flex items-center justify-center hover:scale-110 active:scale-95 transition-all z-[60] group">
    <span class="material-symbols-outlined text-2xl" style="font-variation-settings:'FILL' 1;">smart_toy</span>
    <div class="absolute right-full mr-4 bg-inverse-surface text-inverse-on-surface px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity">How can I help you today?</div>
</button>

<!-- Mobile Bottom Nav -->
<nav class="md:hidden fixed bottom-0 left-0 w-full z-50 flex justify-around items-center px-4 pb-6 pt-3 bg-white/90 backdrop-blur-xl border-t border-slate-200">
    <a href="{{ route('admin.dashboard') }}" class="flex flex-col items-center bg-blue-100 text-blue-800 rounded-xl px-3 py-1.5">
        <span class="material-symbols-outlined">grid_view</span>
        <span class="text-[10px] font-semibold uppercase">Dashboard</span>
    </a>
    <a href="{{ route('admin.orders') }}" class="flex flex-col items-center text-slate-400">
        <span class="material-symbols-outlined">auto_stories</span>
        <span class="text-[10px] font-semibold uppercase">Orders</span>
    </a>
    <a href="{{ route('admin.employees') }}" class="flex flex-col items-center text-slate-400">
        <span class="material-symbols-outlined">badge</span>
        <span class="text-[10px] font-semibold uppercase">Employees</span>
    </a>
    <a href="{{ route('admin.settings') }}" class="flex flex-col items-center text-slate-400">
        <span class="material-symbols-outlined">settings</span>
        <span class="text-[10px] font-semibold uppercase">Settings</span>
    </a>
</nav>

 