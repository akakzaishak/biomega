<div class="fade-in">
    <h1 class="font-headline text-3xl font-extrabold tracking-tight text-on-surface">Settings</h1>
    <p class="text-on-surface-variant font-medium mt-1">Review operational preferences, access boundaries, and portal state.</p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div class="fade-in bg-surface-container-lowest p-6 rounded-xl border border-outline-variant/15 shadow-sm">
        <p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Pharmacies</p>
        <p class="mt-3 text-3xl font-headline font-extrabold text-on-surface">{{ number_format((int) ($pharmaciesCount ?? 0)) }}</p>
    </div>
    <div class="fade-in bg-surface-container-lowest p-6 rounded-xl border border-outline-variant/15 shadow-sm">
        <p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Employees</p>
        <p class="mt-3 text-3xl font-headline font-extrabold text-primary">{{ number_format((int) ($employeesCount ?? 0)) }}</p>
    </div>
    <div class="fade-in bg-surface-container-lowest p-6 rounded-xl border border-outline-variant/15 shadow-sm">
        <p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Orders</p>
        <p class="mt-3 text-3xl font-headline font-extrabold text-tertiary">{{ number_format((int) ($ordersCount ?? 0)) }}</p>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    <div class="xl:col-span-2 bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm p-6">
        <h2 class="font-headline text-lg font-bold mb-5">Operational Preferences</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @php
                $preferences = [
                    ['label' => 'Notification digest', 'value' => 'Enabled', 'icon' => 'notifications_active'],
                    ['label' => 'Auto assignment', 'value' => 'Enabled', 'icon' => 'route'],
                    ['label' => 'Support escalation', 'value' => 'Enabled', 'icon' => 'support_agent'],
                    ['label' => 'Audit logging', 'value' => 'Enabled', 'icon' => 'verified_user'],
                ];
            @endphp
            @foreach ($preferences as $pref)
                <div class="rounded-xl border border-slate-200 p-4 flex items-center justify-between">
                    <div>
                        <p class="font-semibold text-on-surface">{{ $pref['label'] }}</p>
                        <p class="text-xs text-on-surface-variant mt-1">{{ $pref['value'] }}</p>
                    </div>
                    <span class="material-symbols-outlined text-primary">{{ $pref['icon'] }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <div class="space-y-6">
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm p-6">
            <h2 class="font-headline text-lg font-bold mb-4">Admin Profile</h2>
            <div class="space-y-3 text-sm text-on-surface-variant">
                <div class="flex items-center justify-between">
                    <span>Name</span>
                    <span class="font-semibold text-on-surface">{{ Auth::user()->firstname ?? 'Admin' }} {{ Auth::user()->lastname ?? '' }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span>Role</span>
                    <span class="font-semibold text-on-surface">Administrator</span>
                </div>
                <div class="flex items-center justify-between">
                    <span>Access</span>
                    <span class="font-semibold text-primary">Full portal</span>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-primary to-primary-container text-white rounded-2xl shadow-lg p-6">
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-white/70">Continue later</p>
            <p class="mt-3 text-sm leading-relaxed text-white/90">This page is ready for real settings actions when you resume the next implementation pass.</p>
        </div>
    </div>
</div>