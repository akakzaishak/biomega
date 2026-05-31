<div class="relative z-10 w-full max-w-3xl mx-auto bg-white rounded-3xl p-8 shadow-[0_24px_60px_-20px_rgba(0,94,164,0.2)] border border-slate-100">
    <button
        type="button"
        onclick="closeRegistration()"
        class="absolute top-4 right-4 w-10 h-10 inline-flex items-center justify-center rounded-full text-slate-500 hover:bg-slate-100 hover:text-slate-700 transition-colors"
        aria-label="Close and go back"
        title="Close"
    >
        <span class="material-symbols-outlined">close</span>
    </button>

    <p class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-bold uppercase tracking-wider mb-5">
        <span class="material-symbols-outlined text-sm">app_registration</span>
        Pharmacy Registration
    </p>
    <h2 class="text-3xl font-extrabold headline mb-2">Enregistrer votre Pharmacie</h2>
    <p class="text-sm text-slate-600 mb-6">Rejoignez le réseau Bio Mega Pharme et connectez-vous avec des milliers de patients à travers l'Algérie.</p>

    @if(session('error') ?? $error ?? false)
        <div class="mb-4 flex items-center gap-3 bg-error-container text-on-error-container text-sm font-semibold px-4 py-3 rounded-xl border border-error/20">
            <span class="material-symbols-outlined text-lg flex-shrink-0">error</span>
            {{ session('error') ?? $error }}
        </div>
    @endif
    @if(session('success') ?? $success ?? false)
        <div class="mb-4 flex items-start gap-3 bg-green-50 text-green-800 text-sm font-semibold px-4 py-4 rounded-xl border border-green-200">
            <span class="material-symbols-outlined text-lg flex-shrink-0 text-green-600" style="font-variation-settings:'FILL' 1;">check_circle</span>
            <div>
                {{ session('success') ?? $success }}
                <a href="{{ route('login') }}" class="block mt-2 underline text-primary font-bold">→ Aller à la page de connexion</a>
            </div>
        </div>
    @endif
    @if ($errors->any())
        <div class="mb-4 flex items-center gap-3 bg-error-container text-on-error-container text-sm font-semibold px-4 py-3 rounded-xl border border-error/20">
            <span class="material-symbols-outlined text-lg flex-shrink-0">error</span>
            <div class="space-y-1">
                @foreach ($errors->all() as $message)
                    <div>{{ $message }}</div>
                @endforeach
            </div>
        </div>
    @endif

    <form method="post" action="{{ route('register.pharmacy') }}" class="space-y-5">
        @csrf

        <div>
            <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">badge</span>Informations d'identité
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5 ml-1">NIF <span class="text-red-500">*</span></label>
                    <div class="relative group">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xl group-focus-within:text-primary transition-colors">tag</span>
                        <input type="text" name="nif" required placeholder="Numéro d'identification fiscale" value="{{ old('nif') }}" class="w-full pl-12 pr-4 py-3.5 bg-slate-100 border-none rounded-lg focus:ring-2 focus:ring-primary/20 transition-all text-slate-900 placeholder:text-slate-400 text-sm" />
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5 ml-1">Horaire d'ouverture <span class="text-red-500">*</span></label>
                    <div class="relative group">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xl group-focus-within:text-primary transition-colors">schedule</span>
                        <input type="time" name="worktime" required value="{{ old('worktime') }}" class="w-full pl-12 pr-4 py-3.5 bg-slate-100 border-none rounded-lg focus:ring-2 focus:ring-primary/20 transition-all text-slate-900 placeholder:text-slate-400 text-sm" />
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5 ml-1">Prénom du responsable <span class="text-red-500">*</span></label>
                    <div class="relative group">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xl group-focus-within:text-primary transition-colors">person</span>
                        <input type="text" name="firstname" required placeholder="Prénom" value="{{ old('firstname') }}" class="w-full pl-12 pr-4 py-3.5 bg-slate-100 border-none rounded-lg focus:ring-2 focus:ring-primary/20 transition-all text-slate-900 placeholder:text-slate-400 text-sm" />
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5 ml-1">Nom du responsable <span class="text-red-500">*</span></label>
                    <div class="relative group">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xl group-focus-within:text-primary transition-colors">person</span>
                        <input type="text" name="lastname" required placeholder="Nom" value="{{ old('lastname') }}" class="w-full pl-12 pr-4 py-3.5 bg-slate-100 border-none rounded-lg focus:ring-2 focus:ring-primary/20 transition-all text-slate-900 placeholder:text-slate-400 text-sm" />
                    </div>
                </div>
            </div>
        </div>

        <div class="h-px bg-slate-200"></div>

        <div>
            <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">location_on</span>Contact & Localisation
            </p>
            <div class="mb-4">
                <label class="block text-xs font-semibold text-slate-600 mb-1.5 ml-1">Numéro de téléphone <span class="text-red-500">*</span></label>
                <div class="relative group">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xl group-focus-within:text-primary transition-colors">phone</span>
                    <input type="tel" name="phone" required placeholder="0555123456" value="{{ old('phone') }}" class="w-full pl-12 pr-4 py-3.5 bg-slate-100 border-none rounded-lg focus:ring-2 focus:ring-primary/20 transition-all text-slate-900 placeholder:text-slate-400 text-sm" />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5 ml-1">Wilaya <span class="text-red-500">*</span></label>
                    <div class="relative group">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xl group-focus-within:text-primary transition-colors">map</span>
                        <select name="wilaya" required class="w-full pl-12 pr-4 py-3.5 bg-slate-100 border-none rounded-lg focus:ring-2 focus:ring-primary/20 transition-all text-slate-900 text-sm appearance-none">
                            <option value="">Sélectionner une wilaya</option>
                            @foreach ($wilayas as $wilaya)
                                <option value="{{ $wilaya }}" @selected(old('wilaya') === $wilaya)>{{ $wilaya }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5 ml-1">Adresse précise <span class="text-red-500">*</span></label>
                    <div class="relative group">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xl group-focus-within:text-primary transition-colors">home_pin</span>
                        <input type="text" name="location" required placeholder="Rue, quartier, cité..." value="{{ old('location') }}" class="w-full pl-12 pr-4 py-3.5 bg-slate-100 border-none rounded-lg focus:ring-2 focus:ring-primary/20 transition-all text-slate-900 placeholder:text-slate-400 text-sm" />
                    </div>
                </div>
            </div>
        </div>

        <div class="h-px bg-slate-200"></div>

        <div>
            <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">lock</span>Sécurité du compte
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5 ml-1">Mot de passe <span class="text-red-500">*</span></label>
                    <div class="relative group">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xl group-focus-within:text-primary transition-colors">lock</span>
                        <input type="password" id="pass1" name="password" required placeholder="Min. 6 caractères" class="w-full pl-12 pr-12 py-3.5 bg-slate-100 border-none rounded-lg focus:ring-2 focus:ring-primary/20 transition-all text-slate-900 placeholder:text-slate-400 text-sm" />
                        <button type="button" onclick="togglePwd('pass1','eye1')" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-primary transition-colors">
                            <span id="eye1" class="material-symbols-outlined text-xl">visibility</span>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5 ml-1">Confirmer le mot de passe <span class="text-red-500">*</span></label>
                    <div class="relative group">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xl group-focus-within:text-primary transition-colors">lock_reset</span>
                        <input type="password" id="pass2" name="confirm" required placeholder="Répéter le mot de passe" class="w-full pl-12 pr-12 py-3.5 bg-slate-100 border-none rounded-lg focus:ring-2 focus:ring-primary/20 transition-all text-slate-900 placeholder:text-slate-400 text-sm" />
                        <button type="button" onclick="togglePwd('pass2','eye2')" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-primary transition-colors">
                            <span id="eye2" class="material-symbols-outlined text-xl">visibility</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <p class="text-xs text-slate-500 text-center px-4">En vous inscrivant, vous acceptez d'être contacté par l'équipe Bio Mega Pharme pour vérification de votre dossier pharmacien.</p>

        <button type="submit" class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-primary to-primary-container text-white py-4 rounded-xl font-bold text-sm tracking-tight shadow-md hover:shadow-lg hover:opacity-90 active:scale-[0.98] transition-all">
            <span class="material-symbols-outlined text-lg" style="font-variation-settings:'FILL' 1;">how_to_reg</span>
            Enregistrer ma Pharmacie
        </button>
    </form>
</div>

<p class="text-center text-sm text-slate-600 mt-6">
    Vous avez déjà un compte ?
    <a href="{{ route('login') }}" class="text-primary font-semibold hover:underline">Se connecter</a>
</p>

<script>
    function closeRegistration() {
        if (window.history.length > 1) {
            window.history.back();
            return;
        }

        window.location.href = @js(route('login'));
    }

    function togglePwd(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);

        if (input.type === 'password') {
            input.type = 'text';
            icon.textContent = 'visibility_off';
        } else {
            input.type = 'password';
            icon.textContent = 'visibility';
        }
    }
</script>
