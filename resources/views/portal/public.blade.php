<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    @php
        $title = match ($page ?? 'home') {
            'login' => 'Login - TronSport Medicamon',
            'register' => 'Register Pharmacy - Bio Mega Pharme',
            default => 'TronSport Medicamon - Medical Search Portal',
        };
    @endphp
    @include('layouts.portal-assets')
</head>
<body class="bg-surface text-on-background antialiased">
    @if ($page === 'home')
        @php
            $wilayas = [
                '01 - Adrar','02 - Chlef','03 - Laghouat','04 - Oum El Bouaghi','05 - Batna','06 - Béjaïa','07 - Biskra','08 - Béchar','09 - Blida','10 - Bouira',
                '11 - Tamanrasset','12 - Tébessa','13 - Tlemcen','14 - Tiaret','15 - Tizi Ouzou','16 - Alger','17 - Djelfa','18 - Jijel','19 - Sétif','20 - Saïda',
                '21 - Skikda','22 - Sidi Bel Abbès','23 - Annaba','24 - Guelma','25 - Constantine','26 - Médéa','27 - Mostaganem','28 - M\'Sila','29 - Mascara','30 - Ouargla',
                '31 - Oran','32 - El Bayadh','33 - Illizi','34 - Bordj Bou Arréridj','35 - Boumerdès','36 - El Tarf','37 - Tindouf','38 - Tissemsilt','39 - El Oued','40 - Khenchela',
                '41 - Souk Ahras','42 - Tipaza','43 - Mila','44 - Aïn Defla','45 - Naâma','46 - Aïn Témouchent','47 - Ghardaïa','48 - Relizane','49 - El M\'Ghair','50 - El Meniaa',
                '51 - Ouled Djellal','52 - Bordj Baji Mokhtar','53 - Béni Abbès','54 - Timimoun','55 - Touggourt','56 - Djanet','57 - In Salah','58 - In Guezzam',
            ];
        @endphp

        <header class="bg-white/80 backdrop-blur-lg shadow-sm shadow-blue-500/5 sticky top-0 z-50 border-b border-outline-variant/20">
            <div class="flex justify-between items-center px-6 py-3 w-full max-w-7xl mx-auto">
                <div class="flex items-center gap-8">
                    <span class="text-xl font-extrabold tracking-tighter text-blue-800 headline">Bio Mega Pharme</span>
                    <nav class="hidden md:flex items-center gap-6">
                        <a class="text-blue-700 font-bold border-b-2 border-blue-600 px-1 py-1" href="{{ route('home') }}">Dashboard</a>
                        <a class="text-slate-500 font-medium hover:text-blue-600 transition-colors" href="#featured-pharmacies">Orders</a>
                        <a class="text-slate-500 font-medium hover:text-blue-600 transition-colors" href="#featured-pharmacies">Inventory</a>
                        <a class="text-slate-500 font-medium hover:text-blue-600 transition-colors" href="#popular-medicines">Reports</a>
                    </nav>
                </div>
                <div class="flex items-center gap-4">
                    <div class="relative hidden sm:block">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-sm">search</span>
                        <input class="pl-10 pr-4 py-2 bg-surface-container-high rounded-xl border-none focus:ring-2 focus:ring-primary text-sm w-64" placeholder="Search medicines..." type="text" />
                    </div>
                    <button class="p-2 text-on-surface-variant hover:bg-slate-50 rounded-full transition-colors" type="button">
                        <span class="material-symbols-outlined">notifications</span>
                    </button>
                    <a href="{{ route('register.pharmacy') }}" class="flex items-center gap-2 bg-tertiary-container text-on-tertiary px-5 py-2 rounded-xl font-semibold text-sm hover:opacity-90 transition-all border border-tertiary/20">
                        <span class="material-symbols-outlined text-sm" style="font-variation-settings:'FILL' 1;">local_pharmacy</span>
                        I&#39;m a Pharmacy
                    </a>
                    <a href="{{ route('login') }}" class="flex items-center gap-2 bg-primary text-on-primary px-5 py-2 rounded-xl font-semibold text-sm hover:opacity-90 transition-all">
                        <span class="material-symbols-outlined text-sm">login</span>
                        Login
                    </a>
                </div>
            </div>
            <div class="bg-slate-100 h-px w-full"></div>
        </header>

        <main>
            <section class="relative min-h-[716px] flex items-center justify-center px-6 pt-20 pb-32 overflow-hidden bg-surface">
                <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
                    <div class="absolute -top-24 -right-24 w-96 h-96 bg-primary-fixed opacity-20 blur-3xl rounded-full"></div>
                    <div class="absolute top-1/2 -left-24 w-80 h-80 bg-tertiary-fixed opacity-10 blur-3xl rounded-full"></div>
                </div>
                <div class="relative z-10 max-w-5xl w-full text-center">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-secondary-container text-on-secondary-container text-xs font-bold tracking-wider uppercase mb-8">
                        <span class="material-symbols-outlined text-sm">verified</span>
                        Algeria's Leading Pharmaceutical Network - Bio Mega Pharme
                    </div>
                    <h1 class="text-5xl md:text-7xl font-extrabold tracking-tight text-on-background mb-8 leading-[1.1] headline">
                        Bienvenue sur<br />
                        <span class="text-primary">Bio Mega Pharme</span>
                    </h1>
                    <p class="text-lg md:text-xl text-on-surface-variant mb-12 max-w-2xl mx-auto leading-relaxed">
                        Votre réseau pharmaceutique de confiance en Algérie. Trouvez vos médicaments dans des pharmacies vérifiées à travers les 58 wilayas.
                    </p>
                    <div class="bg-surface-container-lowest p-2 rounded-2xl shadow-xl shadow-primary/5 flex flex-col md:flex-row gap-2 max-w-4xl mx-auto border border-outline-variant/20">
                        <div class="flex-1 flex items-center px-4 py-3 md:py-0 border-b md:border-b-0 md:border-r border-outline-variant/30">
                            <span class="material-symbols-outlined text-primary mr-3">search</span>
                            <input class="w-full border-none focus:ring-0 bg-transparent text-on-surface font-medium placeholder:text-on-surface-variant/60" placeholder="Enter medicine name..." type="text" />
                        </div>
                        <div class="flex-1 flex items-center px-4 py-3 md:py-0">
                            <span class="material-symbols-outlined text-primary mr-3">location_on</span>
                            <select class="w-full border-none focus:ring-0 bg-transparent text-on-surface font-medium appearance-none cursor-pointer">
                                <option value="">Select Wilaya (All 58)</option>
                                @foreach ($wilayas as $index => $wilaya)
                                    <option value="{{ $index + 1 }}">{{ $wilaya }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button class="bg-primary text-on-primary px-8 py-4 rounded-xl font-bold hover:scale-[0.98] transition-transform flex items-center justify-center gap-2" type="button">
                            Search Now
                        </button>
                    </div>
                </div>
            </section>

            <section id="featured-pharmacies" class="py-24 px-6 bg-surface-container-low">
                <div class="max-w-7xl mx-auto">
                    <div class="flex justify-between items-end mb-12">
                        <div>
                            <h2 class="text-3xl font-bold tracking-tight text-on-background mb-2 headline">Featured Pharmacies</h2>
                            <p class="text-on-surface-variant">Top rated partners in our medical logistics network.</p>
                        </div>
                        <a class="text-primary font-bold flex items-center gap-1 hover:underline" href="{{ route('register.pharmacy') }}">
                            View all 500+ pharmacies
                            <span class="material-symbols-outlined text-sm">arrow_forward</span>
                        </a>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="bg-surface-container-lowest rounded-2xl p-6 transition-all hover:shadow-lg hover:-translate-y-1">
                            <div class="w-16 h-16 bg-primary-fixed rounded-2xl flex items-center justify-center mb-6">
                                <span class="material-symbols-outlined text-primary text-3xl">local_pharmacy</span>
                            </div>
                            <h3 class="text-lg font-bold text-on-background mb-1 headline">Pharmacie Central d'Alger</h3>
                            <p class="text-sm text-on-surface-variant flex items-center gap-1 mb-4">
                                <span class="material-symbols-outlined text-xs">location_on</span>
                                Alger Centre, Alger
                            </p>
                            <div class="flex items-center gap-1 mb-6">
                                <span class="material-symbols-outlined text-orange-400 text-sm" style="font-variation-settings:'FILL' 1;">star</span>
                                <span class="text-sm font-bold">4.9</span>
                                <span class="text-xs text-on-surface-variant">(1.2k reviews)</span>
                            </div>
                            <button class="w-full py-3 bg-surface-container-high text-primary font-bold rounded-xl hover:bg-primary hover:text-on-primary transition-all flex items-center justify-center gap-2 group" type="button">
                                View Medicines
                                <span class="material-symbols-outlined text-sm opacity-0 group-hover:opacity-100 transition-opacity">chevron_right</span>
                            </button>
                        </div>
                        <div class="bg-surface-container-lowest rounded-2xl p-6 transition-all hover:shadow-lg hover:-translate-y-1">
                            <div class="w-16 h-16 bg-tertiary-fixed rounded-2xl flex items-center justify-center mb-6">
                                <span class="material-symbols-outlined text-tertiary text-3xl">medical_services</span>
                            </div>
                            <h3 class="text-lg font-bold text-on-background mb-1 headline">Ibn Sina Oran</h3>
                            <p class="text-sm text-on-surface-variant flex items-center gap-1 mb-4">
                                <span class="material-symbols-outlined text-xs">location_on</span>
                                Akid Lotfi, Oran
                            </p>
                            <div class="flex items-center gap-1 mb-6">
                                <span class="material-symbols-outlined text-orange-400 text-sm" style="font-variation-settings:'FILL' 1;">star</span>
                                <span class="text-sm font-bold">4.8</span>
                                <span class="text-xs text-on-surface-variant">(850 reviews)</span>
                            </div>
                            <button class="w-full py-3 bg-surface-container-high text-primary font-bold rounded-xl hover:bg-primary hover:text-on-primary transition-all flex items-center justify-center gap-2 group" type="button">View Medicines</button>
                        </div>
                        <div class="bg-surface-container-lowest rounded-2xl p-6 transition-all hover:shadow-lg hover:-translate-y-1">
                            <div class="w-16 h-16 bg-secondary-container rounded-2xl flex items-center justify-center mb-6">
                                <span class="material-symbols-outlined text-secondary text-3xl">vaccines</span>
                            </div>
                            <h3 class="text-lg font-bold text-on-background mb-1 headline">Pharmacie El Barka</h3>
                            <p class="text-sm text-on-surface-variant flex items-center gap-1 mb-4">
                                <span class="material-symbols-outlined text-xs">location_on</span>
                                Cité 500 Logts, Constantine
                            </p>
                            <div class="flex items-center gap-1 mb-6">
                                <span class="material-symbols-outlined text-orange-400 text-sm" style="font-variation-settings:'FILL' 1;">star</span>
                                <span class="text-sm font-bold">4.7</span>
                                <span class="text-xs text-on-surface-variant">(620 reviews)</span>
                            </div>
                            <button class="w-full py-3 bg-surface-container-high text-primary font-bold rounded-xl hover:bg-primary hover:text-on-primary transition-all flex items-center justify-center gap-2 group" type="button">View Medicines</button>
                        </div>
                        <div class="bg-surface-container-lowest rounded-2xl p-6 transition-all hover:shadow-lg hover:-translate-y-1">
                            <div class="w-16 h-16 bg-primary-fixed-dim rounded-2xl flex items-center justify-center mb-6">
                                <span class="material-symbols-outlined text-primary text-3xl">home_health</span>
                            </div>
                            <h3 class="text-lg font-bold text-on-background mb-1 headline">Médic-Express Blida</h3>
                            <p class="text-sm text-on-surface-variant flex items-center gap-1 mb-4">
                                <span class="material-symbols-outlined text-xs">location_on</span>
                                Ouled Yaïch, Blida
                            </p>
                            <div class="flex items-center gap-1 mb-6">
                                <span class="material-symbols-outlined text-orange-400 text-sm" style="font-variation-settings:'FILL' 1;">star</span>
                                <span class="text-sm font-bold">4.9</span>
                                <span class="text-xs text-on-surface-variant">(2.1k reviews)</span>
                            </div>
                            <button class="w-full py-3 bg-surface-container-high text-primary font-bold rounded-xl hover:bg-primary hover:text-on-primary transition-all flex items-center justify-center gap-2 group" type="button">View Medicines</button>
                        </div>
                    </div>
                </div>
            </section>

            <section id="popular-medicines" class="py-24 px-6 bg-surface">
                <div class="max-w-7xl mx-auto">
                    <div class="mb-12">
                        <h2 class="text-3xl font-bold tracking-tight text-on-background mb-2 headline">Frequently Searched Medicines</h2>
                        <p class="text-on-surface-variant">Essential treatments available across our partner network.</p>
                    </div>
                    <div class="flex gap-6 overflow-x-auto pb-8 hide-scrollbar">
                        <div class="min-w-[280px] bg-surface-container-lowest rounded-2xl overflow-hidden border border-outline-variant/10">
                            <div class="aspect-square bg-slate-100 flex items-center justify-center overflow-hidden">
                                <img class="w-full h-full object-cover" alt="pharmaceutical white pill boxes" src="https://lh3.googleusercontent.com/aida-public/AB6AXuC9_fFyrNtjxwA-T3JNgOYahU6pWS4F8JmaGSzEIvahc6ZifdxHxaFs311OEIGTAPEg6zT_fINLFAxvrnmSx0hBCVNtdv0o_fz855EPmR0j3vapk_Hf8bzL5wsRwD0OOgm5SD0y75fTZOsGLNr4Fx4v2XGdbe3QRrfG61NY9BtmvnXAvt723Dzc_WSScGws4JAGxw6nNtPimX8AKdVcPe0iEjLbzKWI4Lv9xxqe_YsJxNpnVCyew075rrBzIyKeRMpmPVbuBu6O-Vpu" />
                            </div>
                            <div class="p-6">
                                <span class="text-[10px] font-bold uppercase tracking-widest text-primary mb-2 block">Pain Relief</span>
                                <h4 class="text-lg font-bold text-on-background mb-4 headline">Paracetamol 500mg</h4>
                                <div class="flex items-center gap-2 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-tertiary">check_circle</span>
                                    Available in 42 pharmacies
                                </div>
                            </div>
                        </div>
                        <div class="min-w-[280px] bg-surface-container-lowest rounded-2xl overflow-hidden border border-outline-variant/10">
                            <div class="aspect-square bg-slate-100 flex items-center justify-center overflow-hidden">
                                <img class="w-full h-full object-cover" alt="blue and white medicine capsule" src="https://lh3.googleusercontent.com/aida-public/AB6AXuC4K1aLUmEjYzmR7oMaDZ4sRhGwkM2tPrj-mWYbcWzrQ6CYcA67Y6PhmGCW6_b3NfNlRorXcS4dQHqXkaZoS7rIGHFJ7kYScGCe2qcEbPlORDso2ywQZpn-5k8J52k6zlMbVCfdqX_JVnD4q5YoK0BRPqO4vlcJpkUjIIUHmYF_NzY3QI_In8qQE-I29YbaTOwpU7VysRs6F5lCy7bDJrty5tj3zfq2ZBEmrFYoULv33UXLZC60qhZeD3y4HSk_nTbF8-K6COGCnDzz" />
                            </div>
                            <div class="p-6">
                                <span class="text-[10px] font-bold uppercase tracking-widest text-tertiary mb-2 block">Antibiotics</span>
                                <h4 class="text-lg font-bold text-on-background mb-4 headline">Amoxicillin 1g</h4>
                                <div class="flex items-center gap-2 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-tertiary">check_circle</span>
                                    Available in 18 pharmacies
                                </div>
                            </div>
                        </div>
                        <div class="min-w-[280px] bg-surface-container-lowest rounded-2xl overflow-hidden border border-outline-variant/10">
                            <div class="aspect-square bg-slate-100 flex items-center justify-center overflow-hidden">
                                <img class="w-full h-full object-cover" alt="stethoscope on top of pharmaceutical boxes" src="https://lh3.googleusercontent.com/aida-public/AB6AXuD5O7bMoYBJmFDAvJUlwpR9K1PXGlrDaGy9AaXr5jBdaj38_7tfSZuHKPx94dwj491EEZgxW8mbTW1Gi_pQNmJD37_ubLEsELj6qgEMD8aINOE5iwun-OYBTT6dvPSSulme1DWqL-5uYiMyaKGmzCNnFZJxXzUPAVe2Iz2c_gjVsO5m9iSoG70_p0mW1AgChUwBwNC2nehNer-f1lPfWcVhvxQqVPpUENOrPh7rpNd8na79BlzIw6StXp6SpzNq2jgdG0ezUJe8iref" />
                            </div>
                            <div class="p-6">
                                <span class="text-[10px] font-bold uppercase tracking-widest text-orange-600 mb-2 block">Cardiovascular</span>
                                <h4 class="text-lg font-bold text-on-background mb-4 headline">Aspirin Protect</h4>
                                <div class="flex items-center gap-2 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-tertiary">check_circle</span>
                                    Available in 35 pharmacies
                                </div>
                            </div>
                        </div>
                        <div class="min-w-[280px] bg-surface-container-lowest rounded-2xl overflow-hidden border border-outline-variant/10">
                            <div class="aspect-square bg-slate-100 flex items-center justify-center overflow-hidden">
                                <img class="w-full h-full object-cover" alt="professional medical spray bottle" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCMmMlKpO3nqzDh8tXBSrNIfhhY1fMbDuwgFwvnaOOFxf2jDktFhCKhkSTF_szhVDaE5ZqC-8JRdoQS5j9yZZ9WCIDvn9RqRyYgFSIFxTgD9Wh2Ahn_-BGXh7X499QSNi_hE77GOVwElTLVl8RiT81OsUM0iO8Gw-EGCdj7YyGTA1Oh2XRzSWmAQZFEqsit9zBVW1kY-MhuhWJLDe7ZH1-iz1W_MaPAIIV7lXpgqHhkQddfHdJcCvEd89xetC3oevn9ZbVV5lcIiwTA" />
                            </div>
                            <div class="p-6">
                                <span class="text-[10px] font-bold uppercase tracking-widest text-primary mb-2 block">Respiratory</span>
                                <h4 class="text-lg font-bold text-on-background mb-4 headline">Ventolin Inhaler</h4>
                                <div class="flex items-center gap-2 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-tertiary">check_circle</span>
                                    Available in 12 pharmacies
                                </div>
                            </div>
                        </div>
                        <div class="min-w-[280px] bg-surface-container-lowest rounded-2xl overflow-hidden border border-outline-variant/10">
                            <div class="aspect-square bg-slate-100 flex items-center justify-center overflow-hidden">
                                <img class="w-full h-full object-cover" alt="modern thermometer and medicine bottle" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDDG9tBrgQ3968pJ1cxh1Xu6bnCxe7rQMcVur23Enoy7vbz6XLtlQN_BK_PTbvquARbY1pscuVP65_8dUCu8lek8Bsdpu3Yk7oGkBAfDxBiEnZsN1MoHz4Js7uYbO1GUI3pWCAq-erNXz5Ho_HZPKk9MUtwdqJE5J2Kmc5PQi5UcMrY8tV_ET9eQm4TtGgh3IJCVD-WV89jgTjccPzy_8kZMGJASiSobbPXrKNaHCaAVQPICv-IYDYLMRUKN3DLAwO_CFnW1JQE41Yn" />
                            </div>
                            <div class="p-6">
                                <span class="text-[10px] font-bold uppercase tracking-widest text-secondary mb-2 block">Vitamins</span>
                                <h4 class="text-lg font-bold text-on-background mb-4 headline">Vitamin C 1000mg</h4>
                                <div class="flex items-center gap-2 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-tertiary">check_circle</span>
                                    Available in 89 pharmacies
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="py-20 px-6">
                <div class="max-w-7xl mx-auto">
                    <div class="relative rounded-[2rem] overflow-hidden bg-primary p-12 md:p-20 text-on-primary">
                        <div class="absolute top-0 right-0 w-1/2 h-full opacity-10 pointer-events-none">
                            <svg class="w-full h-full" viewBox="0 0 400 400" xmlns="http://www.w3.org/2000/svg">
                                <path d="M47.7,-64.7C61.4,-56.3,71.8,-41.8,78.2,-25.9C84.6,-10,87.1,7.2,82.4,22.6C77.7,38,65.8,51.6,51.4,61.4C37.1,71.2,20.3,77.3,3.3,72.7C-13.7,68.2,-30.9,53.1,-46,39.8C-61.1,26.5,-74,15.1,-77.9,-0.2C-81.8,-15.5,-76.7,-34.7,-64.3,-45.5C-52,-56.3,-32.4,-58.6,-16,-66C0.4,-73.4,24,-85.9,47.7,-64.7Z" fill="currentColor" transform="translate(200 200)"></path>
                            </svg>
                        </div>
                        <div class="relative z-10 max-w-2xl">
                            <h2 class="text-4xl md:text-5xl font-extrabold tracking-tight mb-6 headline">Are you a Pharmacy? <br />Register Now</h2>
                            <p class="text-lg md:text-xl opacity-90 mb-10 leading-relaxed">
                                Join Algeria's fastest growing medical network. Increase your visibility, manage your stock efficiently, and help patients find medicine faster.
                            </p>
                            <div class="flex flex-col sm:flex-row gap-4">
                                <button onclick="window.location.href='{{ route('register.pharmacy') }}'" class="bg-white text-primary px-10 py-4 rounded-xl font-bold text-lg shadow-lg hover:shadow-xl transition-all" type="button">
                                    Get Started Today
                                </button>
                                <button class="bg-primary-container text-on-primary px-10 py-4 rounded-xl font-bold text-lg hover:bg-primary-container/80 transition-all border border-on-primary/20" type="button">
                                    Contact Support
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <footer class="bg-surface-container-highest py-16 px-6">
            <div class="max-w-7xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-16">
                    <div class="col-span-1 md:col-span-1">
                        <span class="text-2xl font-extrabold tracking-tighter text-blue-800 mb-6 block headline">Bio Mega Pharme</span>
                        <p class="text-on-surface-variant leading-relaxed">
                            The ultimate medical bridge connecting Algerian citizens with verified pharmaceutical services across all 58 wilayas.
                        </p>
                    </div>
                    <div>
                        <h4 class="font-bold text-on-background mb-6 uppercase text-xs tracking-widest">Platform</h4>
                        <ul class="space-y-4 text-on-surface-variant font-medium">
                            <li><a class="hover:text-primary" href="#">Search Medicine</a></li>
                            <li><a class="hover:text-primary" href="#">Find Pharmacy</a></li>
                            <li><a class="hover:text-primary" href="{{ route('login') }}">Pharmacy Login</a></li>
                            <li><a class="hover:text-primary" href="{{ route('register.pharmacy') }}">Register Pharmacy</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-bold text-on-background mb-6 uppercase text-xs tracking-widest">Wilayas</h4>
                        <ul class="space-y-4 text-on-surface-variant font-medium">
                            <li><a class="hover:text-primary" href="#">Alger</a></li>
                            <li><a class="hover:text-primary" href="#">Oran</a></li>
                            <li><a class="hover:text-primary" href="#">Constantine</a></li>
                            <li><a class="hover:text-primary" href="#">Sétif</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-bold text-on-background mb-6 uppercase text-xs tracking-widest">Support</h4>
                        <ul class="space-y-4 text-on-surface-variant font-medium">
                            <li><a class="hover:text-primary" href="#">Help Center</a></li>
                            <li><a class="hover:text-primary" href="#">Privacy Policy</a></li>
                            <li><a class="hover:text-primary" href="#">Terms of Service</a></li>
                            <li><a class="hover:text-primary" href="#">Contact Us</a></li>
                        </ul>
                    </div>
                </div>
                <div class="pt-8 border-t border-outline-variant/30 flex flex-col md:flex-row justify-between items-center gap-6">
                    <p class="text-sm text-on-surface-variant">© 2024 TronSport Medicamon. All rights reserved.</p>
                    <div class="flex gap-6">
                        <span class="material-symbols-outlined text-on-surface-variant cursor-pointer hover:text-primary">share</span>
                        <span class="material-symbols-outlined text-on-surface-variant cursor-pointer hover:text-primary">public</span>
                        <span class="material-symbols-outlined text-on-surface-variant cursor-pointer hover:text-primary">mail</span>
                    </div>
                </div>
            </div>
        </footer>
    @elseif ($page === 'login')
        <div class="min-h-screen flex items-center justify-center p-4 relative overflow-hidden">
            <div class="absolute inset-0 pointer-events-none">
                <div class="absolute -top-20 -right-16 h-72 w-72 rounded-full bg-blue-100/60 blur-3xl"></div>
                <div class="absolute bottom-0 left-0 h-80 w-80 rounded-full bg-cyan-100/70 blur-3xl"></div>
            </div>
            @include('portal.public.login')
        </div>
    @elseif ($page === 'register')
        <main class="py-12 px-4 relative overflow-hidden">
            <div class="absolute inset-0 pointer-events-none">
                <div class="absolute -top-24 right-10 h-72 w-72 rounded-full bg-blue-100/60 blur-3xl"></div>
                <div class="absolute bottom-0 left-10 h-72 w-72 rounded-full bg-cyan-100/70 blur-3xl"></div>
            </div>
            @include('portal.public.register')
        </main>
    @endif
</body>
</html>
 