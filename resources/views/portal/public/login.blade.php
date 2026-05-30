<div class="relative z-10 w-full max-w-md bg-white rounded-3xl p-8 shadow-[0_24px_60px_-20px_rgba(0,94,164,0.2)] border border-slate-100">
    <p class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-bold uppercase tracking-wider mb-5">
        <span class="material-symbols-outlined text-sm">lock</span>
        Secure Login
    </p>
    <h2 class="text-3xl font-extrabold headline mb-2">Sign in</h2>
    <p class="text-sm text-slate-600 mb-6">Enter your phone number and password to access the portal.</p>

    @if(session('success') ?? $success ?? false)
        <div class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') ?? $success }}</div>
    @endif
    @if(session('error') ?? $error ?? false)
        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ session('error') ?? $error }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 space-y-1">
            @foreach ($errors->all() as $message)
                <div>{{ $message }}</div>
            @endforeach
        </div>
    @endif

    <form method="post" action="{{ route('login') }}" class="space-y-4">
        @csrf
        <div>
            <label class="text-xs font-bold uppercase tracking-widest text-slate-500">Phone</label>
            <input name="phone" value="{{ old('phone') }}" class="mt-2 w-full border border-slate-200 rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-200 focus:border-blue-400 outline-none" placeholder="e.g. 0612345678" />
        </div>
        <div>
            <label class="text-xs font-bold uppercase tracking-widest text-slate-500">Password</label>
            <input name="password" type="password" class="mt-2 w-full border border-slate-200 rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-200 focus:border-blue-400 outline-none" />
        </div>
        <div class="flex items-center justify-between gap-4 pt-2">
            <a href="{{ route('register.pharmacy') }}" class="text-sm font-semibold text-primary">Register pharmacy</a>
            <button type="submit" class="bg-primary text-white px-5 py-3 rounded-xl font-bold shadow-sm">Login</button>
        </div>
    </form>
</div>
