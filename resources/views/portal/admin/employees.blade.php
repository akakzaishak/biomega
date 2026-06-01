@php
    $allEmployees = $allEmployees ?? $employees ?? [];
    $roleCounts = [];
    foreach ($allEmployees as $e) { $t = $e['source_table'] ?? ($e['source'] ?? 'unknown'); $roleCounts[$t] = ($roleCounts[$t] ?? 0) + 1; }
    $roleLabels = [
        'commercialservice' => 'Commercial Service', 'deliverymanager' => 'Delivery Manager',
        'deliveryperson' => 'Delivery Person', 'stockemployee' => 'Stock Employee'
    ];
    $roleColors = [
        'commercialservice' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700'],
        'deliverymanager'   => ['bg' => 'bg-purple-100', 'text' => 'text-purple-700'],
        'deliveryperson'    => ['bg' => 'bg-orange-100', 'text' => 'text-orange-600'],
        'stockemployee'     => ['bg' => 'bg-teal-100', 'text' => 'text-teal-700'],
    ];
@endphp

<div class="fade-in">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
        <div>
            <h1 class="font-headline text-3xl font-extrabold tracking-tight">Employees</h1>
            <p class="text-on-surface-variant font-medium mt-1">Manage all staff across every department — <span class="font-bold text-primary">{{ number_format(count($allEmployees)) }} total</span></p>
        </div>
        <button onclick="openModal()" class="flex items-center gap-2 bg-primary text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-md hover:bg-primary/90 active:scale-95 transition-all">
            <span class="material-symbols-outlined text-lg">person_add</span>
            Add Employee
        </button>
    </div>

    <div class="grid grid-cols-5 gap-4 mb-6">
        <div class="bg-surface-container-lowest rounded-xl p-6 border border-outline-variant/15">
            <p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Total</p>
            <p class="mt-3 text-3xl font-headline font-extrabold">{{ number_format(count($allEmployees)) }}</p>
        </div>
        @foreach(['commercialservice'=>'Commercial','deliverymanager'=>'Managers','deliveryperson'=>'Drivers','stockemployee'=>'Stock'] as $k=>$label)
            <div class="bg-surface-container-lowest rounded-xl p-6 border border-outline-variant/15">
                <p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">{{ $label }}</p>
                <p class="mt-3 text-3xl font-headline font-extrabold">{{ number_format($roleCounts[$k] ?? 0) }}</p>
            </div>
        @endforeach
    </div>

    <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
            <div class="flex flex-wrap gap-2" id="filterTabs">
                <button class="filter-btn active-filter px-4 py-1.5 rounded-full text-xs font-bold border" data-filter="all" onclick="filterTable('all')">All ({{ number_format(count($allEmployees)) }})</button>
                @foreach($roleLabels as $key=>$label)
                    <button class="filter-btn px-4 py-1.5 rounded-full text-xs font-bold border" data-filter="{{ $key }}" onclick="filterTable('{{ $key }}')">{{ $label }} ({{ $roleCounts[$key] ?? 0 }})</button>
                @endforeach
            </div>
        </div>

        @if(empty($allEmployees))
            <div class="flex flex-col items-center justify-center py-20 text-on-surface-variant">
                <span class="material-symbols-outlined text-5xl mb-3 opacity-30">group_off</span>
                <p class="font-bold text-lg">No employees yet</p>
                <p class="text-sm mt-1">Click "Add Employee" to get started.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm" id="employeeTable">
                    <thead class="bg-surface-container-low border-b border-outline-variant/20">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Employee</th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Role</th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Phone</th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">ID</th>
                            <th class="px-6 py-4 text-right text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/10" id="employeeRows">
                        @foreach($allEmployees as $emp)
                            @php
                                $tbl = $emp['source_table'] ?? ($emp['source'] ?? 'unknown');
                                $colors = $roleColors[$tbl] ?? ['bg'=>'bg-slate-100','text'=>'text-slate-600'];
                                $initials = strtoupper(substr(($emp['FirstName'] ?? ($emp['employee_name'] ?? '')),0,1) . substr(($emp['LastName'] ?? ''),0,1));
                            @endphp
                            <tr class="hover:bg-surface-container-low transition-colors employee-row" data-role="{{ $tbl }}">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-primary/10 flex items-center justify-center font-bold text-primary text-sm flex-shrink-0">{{ $initials }}</div>
                                        <div>
                                            <p class="font-semibold text-on-surface">{{ trim(($emp['FirstName'] ?? '') . ' ' . ($emp['LastName'] ?? '')) ?: ($emp['employee_name'] ?? 'Unnamed') }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4"><span class="text-[11px] font-black px-2.5 py-1 rounded-full uppercase tracking-wide {{ $colors['bg'] . ' ' . $colors['text'] }}">{{ $roleLabels[$tbl] ?? $tbl }}</span></td>
                                <td class="px-6 py-4 text-on-surface-variant font-medium">{{ $emp['PhoneNumber'] ?? $emp['employee_phone'] ?? '—' }}</td>
                                <td class="px-6 py-4 text-on-surface-variant text-xs font-mono">#{{ $emp['ID'] ?? $emp['employee_id'] ?? '—' }}</td>
                                <td class="px-6 py-4 text-right">
                                    <form method="POST" action="{{ route('admin.employees') }}" onsubmit="return confirm('Delete this employee? This cannot be undone.');" class="inline">
                                        @csrf
                                        <input type="hidden" name="action" value="delete_employee" />
                                        <input type="hidden" name="del_table" value="{{ $tbl }}" />
                                        <input type="hidden" name="del_phone" value="{{ $emp['PhoneNumber'] ?? $emp['employee_phone'] ?? '' }}" />
                                        <button type="submit" class="p-2 rounded-lg text-error hover:bg-error-container transition-colors" title="Delete">
                                            <span class="material-symbols-outlined text-base">delete</span>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>

<!-- Add Employee Modal -->
<div id="modal-overlay" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40" onclick="closeModalOutside(event)">
    <div id="modal-box" class="bg-surface-container-lowest w-full max-w-lg mx-4 rounded-2xl shadow-2xl border border-outline-variant/15 p-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="font-headline font-extrabold text-xl text-on-surface">Add New Employee</h2>
                <p class="text-xs text-on-surface-variant mt-0.5">Fill in the details below to register a new staff member.</p>
            </div>
            <button type="button" onclick="closeModal()" class="p-2 hover:bg-surface-container rounded-lg transition-colors text-on-surface-variant">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.employees') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="action" value="add_employee" />
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1.5">First Name</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">person</span>
                        <input type="text" name="firstname" required placeholder="Ahmed" value="{{ old('firstname') }}" class="w-full pl-10 pr-3 py-3 bg-surface-container-high border-none rounded-lg text-sm focus:ring-2 focus:ring-primary/20" />
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1.5">Last Name</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">person</span>
                        <input type="text" name="lastname" required placeholder="Benali" value="{{ old('lastname') }}" class="w-full pl-10 pr-3 py-3 bg-surface-container-high border-none rounded-lg text-sm focus:ring-2 focus:ring-primary/20" />
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1.5">Phone Number</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">phone</span>
                    <input type="tel" name="phone" required placeholder="0551234567" value="{{ old('phone') }}" class="w-full pl-10 pr-3 py-3 bg-surface-container-high border-none rounded-lg text-sm focus:ring-2 focus:ring-primary/20" />
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1.5">Role / Department</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">badge</span>
                    <select name="role" required class="w-full pl-10 pr-3 py-3 bg-surface-container-high border-none rounded-lg text-sm focus:ring-2 focus:ring-primary/20 appearance-none">
                        <option value="" disabled selected>Select a role...</option>
                        <option value="commercialservice" @selected(old('role') === 'commercialservice')>Commercial Service</option>
                        <option value="deliverymanager" @selected(old('role') === 'deliverymanager')>Delivery Manager</option>
                        <option value="deliveryperson" @selected(old('role') === 'deliveryperson')>Delivery Person</option>
                        <option value="stockemployee" @selected(old('role') === 'stockemployee')>Stock Employee</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1.5">Password</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">lock</span>
                    <input type="password" name="password" required id="modalPassword" placeholder="••••••••" class="w-full pl-10 pr-10 py-3 bg-surface-container-high border-none rounded-lg text-sm focus:ring-2 focus:ring-primary/20" />
                    <button type="button" onclick="toggleModalPassword()" class="absolute right-3 top-1/2 -translate-y-1/2 text-outline hover:text-primary"><span id="modalEyeIcon" class="material-symbols-outlined text-lg">visibility</span></button>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1.5">Confirm Password</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">lock</span>
                    <input type="password" name="confirm" required id="modalConfirm" placeholder="••••••••" class="w-full pl-10 pr-10 py-3 bg-surface-container-high border-none rounded-lg text-sm focus:ring-2 focus:ring-primary/20" />
                    <button type="button" onclick="toggleModalConfirm()" class="absolute right-3 top-1/2 -translate-y-1/2 text-outline hover:text-primary"><span id="modalConfirmEyeIcon" class="material-symbols-outlined text-lg">visibility</span></button>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal()" class="flex-1 py-3 rounded-xl border border-outline-variant text-on-surface-variant font-bold text-sm">Cancel</button>
                <button type="submit" class="flex-1 py-3 rounded-xl bg-gradient-to-r from-primary to-primary-container text-white font-bold text-sm">Add Employee</button>
            </div>
        </form>
    </div>

<style>
    .filter-btn { background: white; color: #404752; border-color: #c0c7d4; }
    .filter-btn:hover { background: #f3f4f5; }
    .active-filter { background: #005ea4 !important; color: white !important; border-color: #005ea4 !important; }
</style>

<script>
    function openModal(){ document.getElementById('modal-overlay').classList.remove('hidden'); }
    function closeModal(){ document.getElementById('modal-overlay').classList.add('hidden'); }
    function closeModalOutside(e){ if (e.target === document.getElementById('modal-overlay')) closeModal(); }
    function toggleModalPassword(){ const input=document.getElementById('modalPassword'); const icon=document.getElementById('modalEyeIcon'); input.type = input.type==='password' ? 'text' : 'password'; icon.textContent = input.type==='password' ? 'visibility' : 'visibility_off'; }
    document.addEventListener('DOMContentLoaded', function(){
        // auto-open modal if there are validation errors
        @if($errors->any()) openModal(); @endif
    });
    function filterTable(role){ document.querySelectorAll('.filter-btn').forEach(btn=>btn.classList.toggle('active-filter', btn.dataset.filter===role)); document.querySelectorAll('.employee-row').forEach(row=>row.style.display=(role==='all'||row.dataset.role===role)?'':'none'); }
    function toggleModalConfirm(){ const input=document.getElementById('modalConfirm'); const icon=document.getElementById('modalConfirmEyeIcon'); input.type = input.type==='password' ? 'text' : 'password'; icon.textContent = input.type==='password' ? 'visibility' : 'visibility_off'; }
</script> 