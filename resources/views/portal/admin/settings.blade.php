<div class="fade-in flex flex-col gap-6">
    <div>
        <h1 class="font-headline text-3xl font-extrabold tracking-tight mb-1">Settings</h1>
        <p class="text-on-surface-variant mb-6">Portal configuration summary.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm p-6"><p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Pharmacies</p><p class="mt-3 text-3xl font-headline font-extrabold">{{ number_format((int) ($pharmaciesCount ?? 0)) }}</p></div>
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm p-6"><p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Employees</p><p class="mt-3 text-3xl font-headline font-extrabold">{{ number_format((int) ($employeesCount ?? 0)) }}</p></div>
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm p-6"><p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Orders</p><p class="mt-3 text-3xl font-headline font-extrabold">{{ number_format((int) ($ordersCount ?? 0)) }}</p></div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm p-6">
            <h2 class="font-headline text-lg font-bold mb-4">System Preferences</h2>
            <div class="space-y-3 text-sm text-on-surface-variant">
                <div class="rounded-xl border border-slate-200 px-4 py-3 flex items-center justify-between"><span>Notification digest</span><span class="font-bold text-tertiary">Enabled</span></div>
                <div class="rounded-xl border border-slate-200 px-4 py-3 flex items-center justify-between"><span>Auto assignment</span><span class="font-bold text-tertiary">Enabled</span></div>
                <div class="rounded-xl border border-slate-200 px-4 py-3 flex items-center justify-between"><span>Support escalation</span><span class="font-bold text-tertiary">Enabled</span></div>
                <div class="rounded-xl border border-slate-200 px-4 py-3 flex items-center justify-between"><span>Audit logging</span><span class="font-bold text-tertiary">Enabled</span></div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-primary to-primary-container text-white rounded-2xl shadow-lg p-6">
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-white/70">Settings note</p>
            <p class="mt-3 text-sm leading-relaxed text-white/90">This page mirrors the original PHP summary cards while keeping the Laravel shell intact.</p>
        </div>
    </div>
</div>