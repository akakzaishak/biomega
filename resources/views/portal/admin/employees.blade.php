<div class="fade-in flex flex-col gap-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="font-headline text-3xl font-extrabold tracking-tight mb-1">Employees</h1>
            <p class="text-on-surface-variant">Cross-role employee list from the current database.</p>
        </div>
        <button type="button" onclick="const form = document.getElementById('employeeForm'); form.classList.toggle('hidden'); if (!form.classList.contains('hidden')) { form.scrollIntoView({behavior:'smooth', block:'start'}); }" class="inline-flex items-center gap-2 bg-primary text-white px-4 py-2.5 rounded-xl font-bold text-sm shadow-sm">
            <span class="material-symbols-outlined text-lg">person_add</span>Add employee
        </button>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm p-6"><p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Commercial</p><p class="mt-3 text-3xl font-headline font-extrabold">{{ number_format((int) count(array_filter($employees, fn ($employee) => ($employee['source'] ?? '') === 'Commercial')) ) }}</p></div>
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm p-6"><p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Managers</p><p class="mt-3 text-3xl font-headline font-extrabold">{{ number_format((int) count(array_filter($employees, fn ($employee) => ($employee['source'] ?? '') === 'Delivery Manager')) ) }}</p></div>
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm p-6"><p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Drivers</p><p class="mt-3 text-3xl font-headline font-extrabold">{{ number_format((int) count(array_filter($employees, fn ($employee) => ($employee['source'] ?? '') === 'Delivery Person')) ) }}</p></div>
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm p-6"><p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Stock</p><p class="mt-3 text-3xl font-headline font-extrabold">{{ number_format((int) count(array_filter($employees, fn ($employee) => ($employee['source'] ?? '') === 'Stock Employee')) ) }}</p></div>
    </div>

    <div class="bg-slate-50 rounded-[2rem] border border-slate-200 p-4 sm:p-6">
    <div id="employeeForm" class="{{ ($errors->any() || old('firstname') || old('lastname') || old('phone') || old('role') || old('confirm')) ? '' : 'hidden' }} relative z-10 mx-auto max-w-3xl overflow-hidden bg-white rounded-3xl p-8 shadow-lg border border-slate-200">
        <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-slate-300 via-slate-200 to-slate-300"></div>
        <div class="flex items-start justify-between gap-4 mb-6">
            <div class="max-w-2xl">
                <p class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-bold uppercase tracking-wider mb-4">
                    <span class="material-symbols-outlined text-sm">person_add</span>
                    Employee Creation
                </p>
                <h2 class="text-3xl font-extrabold headline mb-2">Add Employee</h2>
                <p class="text-sm text-slate-600 leading-6">Create a staff account directly inside the admin portal. The form stays compact, readable, and consistent with the pharmacy registration flow.</p>
            </div>
            <button type="button" onclick="document.getElementById('employeeForm').classList.add('hidden');" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 hover:text-slate-700 whitespace-nowrap">
                <span class="material-symbols-outlined text-base">close</span>
                Close
            </button>
        </div>

        <form method="post" action="{{ route('admin.employees') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf
            <input type="hidden" name="action" value="add_employee">

            <div>
                <label class="text-xs font-bold uppercase tracking-widest text-slate-500">First name</label>
                <input name="firstname" value="{{ old('firstname') }}" class="mt-2 w-full border border-slate-200 rounded-2xl bg-slate-50 px-4 py-3.5 text-slate-900 shadow-sm transition placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-slate-200 focus:border-slate-400 outline-none" placeholder="First name">
                @error('firstname')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="text-xs font-bold uppercase tracking-widest text-slate-500">Last name</label>
                <input name="lastname" value="{{ old('lastname') }}" class="mt-2 w-full border border-slate-200 rounded-2xl bg-slate-50 px-4 py-3.5 text-slate-900 shadow-sm transition placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-slate-200 focus:border-slate-400 outline-none" placeholder="Last name">
                @error('lastname')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="text-xs font-bold uppercase tracking-widest text-slate-500">Phone number</label>
                <input name="phone" value="{{ old('phone') }}" class="mt-2 w-full border border-slate-200 rounded-2xl bg-slate-50 px-4 py-3.5 text-slate-900 shadow-sm transition placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-slate-200 focus:border-slate-400 outline-none" placeholder="Phone number">
                @error('phone')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="text-xs font-bold uppercase tracking-widest text-slate-500">Role</label>
                <select name="role" class="mt-2 w-full border border-slate-200 rounded-2xl bg-slate-50 px-4 py-3.5 text-slate-900 shadow-sm transition focus:bg-white focus:ring-2 focus:ring-slate-200 focus:border-slate-400 outline-none">
                    <option value="">Select role</option>
                    <option value="commercialservice" @selected(old('role') === 'commercialservice')>Commercial Service</option>
                    <option value="deliverymanager" @selected(old('role') === 'deliverymanager')>Delivery Manager</option>
                    <option value="deliveryperson" @selected(old('role') === 'deliveryperson')>Delivery Person</option>
                    <option value="stockemployee" @selected(old('role') === 'stockemployee')>Stock Employee</option>
                </select>
                @error('role')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="text-xs font-bold uppercase tracking-widest text-slate-500">Password</label>
                <input name="password" type="password" class="mt-2 w-full border border-slate-200 rounded-2xl bg-slate-50 px-4 py-3.5 text-slate-900 shadow-sm transition placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-slate-200 focus:border-slate-400 outline-none" placeholder="Password">
                @error('password')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="text-xs font-bold uppercase tracking-widest text-slate-500">Confirm password</label>
                <input name="confirm" type="password" class="mt-2 w-full border border-slate-200 rounded-2xl bg-slate-50 px-4 py-3.5 text-slate-900 shadow-sm transition placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-slate-200 focus:border-slate-400 outline-none" placeholder="Confirm password">
                @error('confirm')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2 flex flex-col-reverse sm:flex-row sm:justify-end gap-3 mt-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('employeeForm').classList.add('hidden');" class="px-5 py-3 border border-slate-200 rounded-xl font-semibold text-slate-700 bg-white hover:bg-slate-50">Cancel</button>
                <button class="bg-primary text-white px-5 py-3 rounded-xl font-bold shadow-sm hover:brightness-95 transition">Save Employee</button>
            </div>
        </form>
    </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        @forelse ($employees as $employee)
            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm p-5 flex items-center justify-between gap-4">
                <div class="space-y-2 min-w-0">
                    <p class="block text-base font-bold leading-tight text-slate-900 break-words">
                        {{ $employee['employee_name'] ?: $employee['full_name'] ?: trim(($employee['FirstName'] ?? '') . ' ' . ($employee['LastName'] ?? '')) ?: 'Unnamed employee' }}
                    </p>
                    <p class="block text-sm leading-tight text-slate-800">
                        ID: {{ $employee['employee_id'] ?? $employee['ID'] ?? '—' }}
                    </p>
                    <p class="block text-sm leading-tight text-slate-800">
                        Phone: {{ $employee['employee_phone'] ?? $employee['PhoneNumber'] ?? '—' }}
                    </p>
                    <p class="block text-sm leading-tight text-slate-800">
                        Role: {{ $employee['employee_role'] ?? $employee['display_role'] ?? '—' }}
                    </p>
                </div>
                <form method="post" action="{{ route('admin.employees') }}" onsubmit="return confirm('Remove this employee?');">
                    @csrf
                    <input type="hidden" name="action" value="delete_employee">
                    <input type="hidden" name="del_table" value="{{ $employee['source_table'] ?? '' }}">
                    <input type="hidden" name="del_id" value="{{ $employee['employee_id'] ?? $employee['ID'] ?? '' }}">
                    <input type="hidden" name="del_phone" value="{{ $employee['employee_phone'] ?? $employee['PhoneNumber'] ?? '' }}">
                    <button type="submit" class="inline-flex items-center gap-2 bg-red-50 text-red-700 px-3 py-1 rounded-full text-xs font-bold">Delete</button>
                </form>
            </div>
        @empty
            <div class="bg-surface-container-lowest rounded-2xl border border-dashed border-outline-variant/40 p-8 text-center text-on-surface-variant">
                No employees found.
            </div>
        @endforelse
    </div>
</div>