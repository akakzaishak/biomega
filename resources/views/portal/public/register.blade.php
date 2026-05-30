<div class="relative z-10 w-full max-w-3xl mx-auto bg-white rounded-3xl p-8 shadow-[0_24px_60px_-20px_rgba(0,94,164,0.2)] border border-slate-100">
    <p class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-bold uppercase tracking-wider mb-5">
        <span class="material-symbols-outlined text-sm">app_registration</span>
        Pharmacy Registration
    </p>
    <h2 class="text-3xl font-extrabold headline mb-2">Register Pharmacy</h2>
    <p class="text-sm text-slate-600 mb-6">Create a pharmacy account to join the portal.</p>

    @if(session('error') ?? $error ?? false)
        <div class="mb-4 text-sm text-red-700">{{ session('error') ?? $error }}</div>
    @endif
    @if(session('success') ?? $success ?? false)
        <div class="mb-4 text-sm text-green-700">{{ session('success') ?? $success }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-4 text-sm text-red-700">
            @foreach ($errors->all() as $message)
                <div>{{ $message }}</div>
            @endforeach
        </div>
    @endif

    <form method="post" action="{{ route('register.pharmacy') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @csrf
        <div>
            <label class="text-xs font-bold uppercase tracking-widest text-slate-500">NIF</label>
            <input name="nif" value="{{ old('nif') }}" class="mt-2 w-full border border-slate-200 rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-200 focus:border-blue-400 outline-none" />
        </div>
        <div>
            <label class="text-xs font-bold uppercase tracking-widest text-slate-500">Wilaya</label>
            <select name="wilaya" class="mt-2 w-full border border-slate-200 rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-200 focus:border-blue-400 outline-none">
                @foreach($wilayas as $w)
                    <option value="{{ $w }}">{{ $w }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-xs font-bold uppercase tracking-widest text-slate-500">First name</label>
            <input name="firstname" value="{{ old('firstname') }}" class="mt-2 w-full border border-slate-200 rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-200 focus:border-blue-400 outline-none" />
        </div>
        <div>
            <label class="text-xs font-bold uppercase tracking-widest text-slate-500">Last name</label>
            <input name="lastname" value="{{ old('lastname') }}" class="mt-2 w-full border border-slate-200 rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-200 focus:border-blue-400 outline-none" />
        </div>
        <div>
            <label class="text-xs font-bold uppercase tracking-widest text-slate-500">Phone</label>
            <input name="phone" value="{{ old('phone') }}" class="mt-2 w-full border border-slate-200 rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-200 focus:border-blue-400 outline-none" />
        </div>
        <div>
            <label class="text-xs font-bold uppercase tracking-widest text-slate-500">Location</label>
            <input name="location" value="{{ old('location') }}" class="mt-2 w-full border border-slate-200 rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-200 focus:border-blue-400 outline-none" />
        </div>
        <div>
            <label class="text-xs font-bold uppercase tracking-widest text-slate-500">Work time</label>
            <input name="worktime" value="{{ old('worktime') }}" class="mt-2 w-full border border-slate-200 rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-200 focus:border-blue-400 outline-none" />
        </div>
        <div>
            <label class="text-xs font-bold uppercase tracking-widest text-slate-500">Password</label>
            <input name="password" type="password" class="mt-2 w-full border border-slate-200 rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-200 focus:border-blue-400 outline-none" />
        </div>
        <div>
            <label class="text-xs font-bold uppercase tracking-widest text-slate-500">Confirm</label>
            <input name="confirm" type="password" class="mt-2 w-full border border-slate-200 rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-200 focus:border-blue-400 outline-none" />
        </div>
        <div class="md:col-span-2 flex justify-end gap-3 mt-2 pt-2">
            <a href="{{ route('admin.pharmacies') }}" class="px-5 py-3 border border-slate-200 rounded-xl font-semibold text-slate-700">Cancel</a>
            <button type="submit" class="bg-primary text-white px-5 py-3 rounded-xl font-bold shadow-sm">Register</button>
        </div>
    </form>
</div>
