<div class="animate-card w-full max-w-[480px] bg-surface-container-lowest p-8 md:p-12 rounded-xl shadow-[0_24px_48px_-12px_rgba(0,94,164,0.08)] border border-outline-variant/10">
  <div class="flex flex-col items-center mb-10 text-center">
    <div class="w-16 h-16 bg-primary-container flex items-center justify-center rounded-xl mb-6 shadow-sm">
      <span class="material-symbols-outlined text-on-primary text-4xl" style="font-variation-settings:'FILL' 1;">medical_services</span>
    </div>
    <h1 class="headline-font text-3xl font-extrabold tracking-tighter text-primary mb-2">TronSport Medicamon</h1>
    <p class="text-on-surface-variant font-medium text-sm">Precision Medical Logistics Portal</p>
  </div>

  @if (!empty(session('success') ?? $success ?? null))
    <div id="successBox" class="mb-6 flex items-center gap-3 bg-tertiary-container/10 text-tertiary border border-tertiary/20 text-sm font-semibold px-4 py-3 rounded-lg">
      <span class="material-symbols-outlined text-lg flex-shrink-0" style="font-variation-settings:'FILL' 1;">check_circle</span>
      {{ session('success') ?? $success }}
    </div>
  @endif

  @if (!empty(session('error') ?? $error ?? null))
    <div id="errorBox" class="shake mb-6 flex items-center gap-3 bg-error-container text-on-error-container text-sm font-semibold px-4 py-3 rounded-lg border border-error/20">
      <span class="material-symbols-outlined text-lg flex-shrink-0">error</span>
      {{ session('error') ?? $error }}
    </div>
  @endif

  @if ($errors->any())
    <div class="mb-6 flex items-start gap-3 bg-error-container text-on-error-container text-sm font-semibold px-4 py-3 rounded-lg border border-error/20">
      <span class="material-symbols-outlined text-lg flex-shrink-0">error</span>
      <div class="space-y-1">
        @foreach ($errors->all() as $message)
          <div>{{ $message }}</div>
        @endforeach
      </div>
    </div>
  @endif

  <form method="POST" action="{{ route('login') }}" class="space-y-6">
    @csrf

    <div class="space-y-4">
      <div class="relative group">
        <label class="block text-xs font-semibold text-on-surface-variant uppercase tracking-wider mb-1.5 ml-1">Phone Number</label>
        <div class="relative">
          <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline text-xl group-focus-within:text-primary transition-colors">phone</span>
          <input
            type="tel"
            name="phone"
            required
            value="{{ old('phone') }}"
            placeholder="0xxxxxxxxx"
            class="w-full pl-12 pr-4 py-3.5 bg-surface-container-high border-none rounded-lg focus:ring-2 focus:ring-primary/20 transition-all text-on-surface placeholder:text-outline/60 text-sm"
          />
        </div>
      </div>

      <div class="relative group">
        <label class="block text-xs font-semibold text-on-surface-variant uppercase tracking-wider mb-1.5 ml-1">Password</label>
        <div class="relative">
          <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline text-xl group-focus-within:text-primary transition-colors">lock</span>
          <input
            id="passwordInput"
            type="password"
            name="password"
            required
            placeholder="••••••••"
            class="w-full pl-12 pr-12 py-3.5 bg-surface-container-high border-none rounded-lg focus:ring-2 focus:ring-primary/20 transition-all text-on-surface placeholder:text-outline/60 text-sm"
          />
          <button
            type="button"
            onclick="togglePassword()"
            class="absolute right-4 top-1/2 -translate-y-1/2 text-outline hover:text-primary transition-colors"
          >
            <span id="eyeIcon" class="material-symbols-outlined text-xl">visibility</span>
          </button>
        </div>
      </div>
    </div>

    <div class="pt-4">
      <button
        type="submit"
        class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-primary to-primary-container text-on-primary py-3.5 px-6 rounded-lg font-bold text-sm tracking-tight shadow-md hover:shadow-lg hover:opacity-90 active:scale-[0.98] transition-all"
      >
        <span class="material-symbols-outlined text-lg">login</span>
        Login
      </button>
    </div>
  </form>

  <div class="mt-10 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-outline-variant/20 pt-8">
    <a href="{{ route('register.pharmacy') }}" class="text-sm font-semibold text-primary hover:text-primary-container transition-colors flex items-center gap-1">
      <span class="material-symbols-outlined text-lg">person_add</span>
      Register as Pharmacy
    </a>
    <a href="#" class="text-sm font-medium text-on-surface-variant hover:text-primary transition-colors">Forgot Password?</a>
  </div>
</div>

<div class="fixed bottom-8 right-8 hidden xl:flex items-center gap-4 bg-surface-container-low/80 backdrop-blur-md p-4 rounded-xl border border-outline-variant/20 shadow-sm">
  <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
    <span class="material-symbols-outlined text-primary text-xl">support_agent</span>
  </div>
  <div>
    <p class="text-[10px] font-bold text-outline uppercase tracking-tight">Need assistance?</p>
    <p class="text-xs font-semibold text-on-surface">Contact Support Hub</p>
  </div>
</div>

<script>
  function togglePassword() {
    const input = document.getElementById('passwordInput');
    const icon  = document.getElementById('eyeIcon');
    if (input.type === 'password') {
      input.type = 'text';
      icon.textContent = 'visibility_off';
    } else {
      input.type = 'password';
      icon.textContent = 'visibility';
    }
  }
</script>
 