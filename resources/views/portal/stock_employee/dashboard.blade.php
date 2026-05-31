@php
		$firstname = session('firstname', 'Stock');
		$lastname = session('lastname', '');
		$success = session('success', '');
		$error = session('error', '');
		$pending_orders = $pendingOrders ?? [];
		$pharmacies = $pharmacies ?? [];
		$total_pending = count($pending_orders);
		$assigned_count = count(array_filter($pending_orders, fn ($o) => !empty($o['pharmacy_id']) || ($o['pharmacy_id'] ?? '') === '0'));
		$urgent_count = count(array_filter($pending_orders, fn ($o) => (int) ($o['IsUrgen'] ?? 0) === 1));
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Stock Employee — Bio Mega Pharme</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<script>
	tailwind.config={darkMode:"class",theme:{extend:{colors:{
		"primary":"#005ea4","primary-container":"#0077ce","primary-fixed":"#d3e4ff",
		"on-primary":"#ffffff","tertiary":"#186a22","tertiary-container":"#1e8a2a",
		"on-tertiary":"#ffffff","secondary":"#4c616c","secondary-container":"#cfe6f2",
		"on-secondary-container":"#526772","surface":"#f8f9fa",
		"surface-container-lowest":"#ffffff","surface-container-low":"#f3f4f5",
		"surface-container":"#edeeef","surface-container-high":"#e7e8e9",
		"on-surface":"#191c1d","on-surface-variant":"#404752","outline":"#707783",
		"outline-variant":"#c0c7d4","error":"#ba1a1a","error-container":"#ffdad6",
		"on-error-container":"#93000a",
	},fontFamily:{"headline":["Manrope"],"body":["Inter"]}}}}
</script>
<style>
.material-symbols-outlined{font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;}
body{font-family:'Inter',sans-serif;background:#f8f9fa;}
@keyframes fadeUp{from{opacity:0;transform:translateY(14px);}to{opacity:1;transform:translateY(0);}}
.fade-up{animation:fadeUp .4s cubic-bezier(.16,1,.3,1) both;}
.fade-up-2{animation:fadeUp .4s .08s cubic-bezier(.16,1,.3,1) both;}
.fade-up-3{animation:fadeUp .4s .16s cubic-bezier(.16,1,.3,1) both;}
.item-row:hover{background:#f3f4f5;}
.pill{display:inline-flex;align-items:center;gap:3px;font-size:10px;font-weight:700;padding:2px 8px;border-radius:999px;text-transform:uppercase;letter-spacing:.04em;}
.modal-backdrop{display:none;position:fixed;inset:0;z-index:100;background:rgba(0,0,0,.5);backdrop-filter:blur(4px);align-items:center;justify-content:center;padding:1rem;}
.modal-backdrop.open{display:flex;}
.tab-btn{padding:.5rem 1.25rem;border-radius:.75rem;font-weight:600;font-size:.875rem;transition:background .2s,color .2s;cursor:pointer;}
.tab-btn.active{background:#005ea4;color:#fff;}
.tab-btn:not(.active){color:#404752;}
.tab-btn:not(.active):hover{background:#edeeef;}
.item-input-row input{width:100%;padding:.6rem .75rem;background:#e7e8e9;border:none;border-radius:.5rem;font-size:.875rem;outline:none;}
.item-input-row input:focus{box-shadow:0 0 0 2px rgba(0,94,164,.2);}
</style>
</head>
<body class="text-on-surface min-h-screen">
<header class="bg-white/90 backdrop-blur-lg shadow-sm sticky top-0 z-50 flex items-center justify-between px-6 py-3">
	<div class="flex items-center gap-3">
		<div class="w-9 h-9 rounded-xl bg-primary flex items-center justify-center">
			<span class="material-symbols-outlined text-white text-xl" style="font-variation-settings:'FILL' 1;">inventory_2</span>
		</div>
		<div>
			<span class="text-lg font-extrabold tracking-tighter text-blue-900" style="font-family:Manrope,sans-serif;">Bio Mega Pharme</span>
			<span class="hidden sm:inline text-xs text-on-surface-variant ml-2 font-medium">· Stock Employee Portal</span>
		</div>
	</div>
	<div class="flex items-center gap-3">
		<div class="hidden sm:flex items-center gap-2 bg-surface-container px-3 py-1.5 rounded-full">
			<div class="w-7 h-7 rounded-full bg-gradient-to-br from-primary to-primary-container flex items-center justify-center text-white text-xs font-bold">
				{{ strtoupper(mb_substr($firstname, 0, 1)) }}
			</div>
			<span class="text-sm font-semibold text-on-surface">{{ $firstname.' '.$lastname }}</span>
		</div>
		<a href="{{ route('logout') }}" class="p-2 hover:bg-slate-50 rounded-full transition-colors" title="Déconnexion">
			<span class="material-symbols-outlined text-slate-600">logout</span>
		</a>
	</div>
</header>

<main class="max-w-7xl mx-auto px-4 py-8 space-y-8">
	@if (!empty($success))
	<div class="fade-up flex items-center gap-3 bg-green-50 text-green-800 border border-green-200 px-5 py-3.5 rounded-2xl text-sm font-semibold shadow-sm">
		<span class="material-symbols-outlined text-green-600 text-xl flex-shrink-0" style="font-variation-settings:'FILL' 1;">check_circle</span>
		<span>{!! $success !!}</span>
	</div>
	@endif
	@if (!empty($error))
	<div class="fade-up flex items-center gap-3 bg-error-container text-on-error-container border border-error/20 px-5 py-3.5 rounded-2xl text-sm font-semibold shadow-sm">
		<span class="material-symbols-outlined text-xl flex-shrink-0">error</span>
		<span>{{ $error }}</span>
	</div>
	@endif

	<div class="fade-up grid grid-cols-2 sm:grid-cols-3 gap-4">
		<div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm px-5 py-4 flex items-center gap-4">
			<div class="w-11 h-11 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
				<span class="material-symbols-outlined text-primary text-2xl" style="font-variation-settings:'FILL' 1;">pending_actions</span>
			</div>
			<div>
				<p class="text-xs font-semibold text-on-surface-variant">En attente</p>
				<p class="text-3xl font-extrabold text-primary" style="font-family:Manrope,sans-serif;">{{ $total_pending }}</p>
			</div>
		</div>
		<div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm px-5 py-4 flex items-center gap-4">
			<div class="w-11 h-11 rounded-xl bg-tertiary-container/20 flex items-center justify-center flex-shrink-0">
				<span class="material-symbols-outlined text-tertiary-container text-2xl" style="font-variation-settings:'FILL' 1;">local_pharmacy</span>
			</div>
			<div>
				<p class="text-xs font-semibold text-on-surface-variant">Assignées</p>
				<p class="text-3xl font-extrabold text-on-surface" style="font-family:Manrope,sans-serif;">{{ $assigned_count }}</p>
			</div>
		</div>
		<div class="col-span-2 sm:col-span-1 bg-surface-container-lowest rounded-2xl border border-error/20 shadow-sm px-5 py-4 flex items-center gap-4">
			<div class="w-11 h-11 rounded-xl bg-error-container flex items-center justify-center flex-shrink-0">
				<span class="material-symbols-outlined text-error text-2xl" style="font-variation-settings:'FILL' 1;">priority_high</span>
			</div>
			<div>
				<p class="text-xs font-semibold text-on-surface-variant">Urgentes</p>
				<p class="text-3xl font-extrabold text-error" style="font-family:Manrope,sans-serif;">{{ $urgent_count }}</p>
			</div>
		</div>
	</div>

	<div class="fade-up-2 flex gap-2 bg-surface-container-lowest rounded-2xl p-1.5 border border-outline-variant/15 w-fit shadow-sm">
		<button class="tab-btn active" id="tab-list-btn" onclick="switchTab('list')">
			<span class="flex items-center gap-2">
				<span class="material-symbols-outlined text-base" style="font-variation-settings:'FILL' 1;">list_alt</span>
				Commandes en attente
				@if ($total_pending > 0)
				<span class="bg-primary text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">{{ $total_pending }}</span>
				@endif
			</span>
		</button>
		<button class="tab-btn" id="tab-create-btn" onclick="switchTab('create')">
			<span class="flex items-center gap-2">
				<span class="material-symbols-outlined text-base">add_circle</span>
				Créer une commande
			</span>
		</button>
	</div>

	<div id="tab-list" class="fade-up-2 space-y-4">
		@if (empty($pending_orders))
		<div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm flex flex-col items-center justify-center py-20 text-center px-6">
			<span class="material-symbols-outlined text-6xl text-outline/30 mb-4" style="font-variation-settings:'FILL' 1;">package_2</span>
			<h3 class="font-extrabold text-xl text-on-surface mb-2" style="font-family:Manrope,sans-serif;">Aucune commande en attente</h3>
			<p class="text-on-surface-variant text-sm mb-6">Toutes les commandes sont traitées ou aucune n'a encore été créée.</p>
			<button onclick="switchTab('create')" class="inline-flex items-center gap-2 bg-primary text-white px-5 py-2.5 rounded-xl font-bold text-sm hover:opacity-90">
				<span class="material-symbols-outlined text-lg">add_circle</span>Créer la première commande
			</button>
		</div>
		@else
		@if ($urgent_count > 0)
		<div class="flex items-center gap-3 bg-error-container text-on-error-container border border-error/20 px-5 py-3 rounded-xl text-sm font-bold">
			<span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">priority_high</span>
			{{ $urgent_count }} commande{{ $urgent_count > 1 ? 's' : '' }} urgente{{ $urgent_count > 1 ? 's' : '' }} en attente — traitez-les en priorité !
		</div>
		@endif

		<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
			@foreach ($pending_orders as $i => $o)
			@php
					$is_urgent = (int) ($o['IsUrgen'] ?? 0) === 1;
					$has_pharmacy = isset($o['pharmacy_id']) && $o['pharmacy_id'] !== null && $o['pharmacy_id'] !== '';
			@endphp
			<div class="bg-surface-container-lowest rounded-2xl border {{ $is_urgent ? 'border-error/30 shadow-[0_0_0_1px_rgba(186,26,26,.15)]' : 'border-outline-variant/15' }} shadow-sm overflow-hidden" style="animation:fadeUp .35s ease {{ $i * 0.05 }}s both;">
				<div class="flex items-start justify-between px-5 pt-5 pb-3 border-b border-outline-variant/10">
					<div class="flex items-center gap-3">
						<div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 {{ $is_urgent ? 'bg-error-container' : 'bg-primary/10' }}">
							<span class="material-symbols-outlined {{ $is_urgent ? 'text-error' : 'text-primary' }} text-xl" style="font-variation-settings:'FILL' 1;">{{ $is_urgent ? 'priority_high' : 'package_2' }}</span>
						</div>
						<div>
							<p class="font-extrabold text-sm text-on-surface font-mono">{{ $o['Tracking'] ?? '' }}</p>
							<p class="text-xs text-on-surface-variant">{{ $o['Date'] ?? '' }} · {{ $o['PackageNumber'] ?? '' }} colis</p>
						</div>
					</div>
					<div class="flex flex-col items-end gap-1.5">
						<span class="pill {{ $is_urgent ? 'bg-error-container text-error' : 'bg-secondary-container text-on-secondary-container' }}">{{ $is_urgent ? '🚨 Urgent' : '⏳ En attente' }}</span>
					</div>
				</div>

				<div class="px-5 py-4 space-y-3">
					<div class="flex items-center justify-between"><span class="text-xs text-on-surface-variant font-medium">Montant total</span><span class="font-bold text-on-surface text-sm">{{ number_format((float) ($o['otalAmount'] ?? 0), 0, ',', ' ') }} DZD</span></div>
					<div class="flex items-center justify-between">
						<span class="text-xs text-on-surface-variant font-medium">Pharmacie</span>
						@if ($has_pharmacy)
								<span class="pill bg-green-100 text-green-700"><span class="material-symbols-outlined text-xs">local_pharmacy</span> {{ $o['p_first'] ?? '' }} {{ $o['p_last'] ?? '' }}</span>
						@else
								<span class="pill bg-yellow-100 text-yellow-700">⚠ Non assignée</span>
						@endif
					</div>
					@if ($has_pharmacy && !empty($o['p_loc']))
						<div class="flex items-start gap-1.5 text-xs text-on-surface-variant"><span class="material-symbols-outlined text-sm">location_on</span>{{ $o['p_loc'] }}</div>
					@endif
				</div>

				<div class="px-5 pb-5 flex gap-2 flex-wrap">
					<button onclick="openAssignModal('{{ addslashes($o['Tracking'] ?? '') }}')" class="flex items-center gap-1.5 {{ $has_pharmacy ? 'bg-surface-container text-on-surface-variant' : 'bg-primary text-white' }} px-4 py-2 rounded-xl text-xs font-bold flex-1 justify-center">{{ $has_pharmacy ? 'Changer la pharmacie' : 'Assigner une pharmacie' }}</button>
					<button onclick='openDetailsModal(@json($o))' class="flex items-center gap-1.5 bg-surface-container hover:bg-surface-container-high text-on-surface-variant px-4 py-2 rounded-xl text-xs font-bold">Détails</button>
				</div>
			</div>
			@endforeach
		</div>
		@endif
	</div>

	<div id="tab-create" class="fade-up-3 hidden mt-6">
		<div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm overflow-hidden">
			<div class="px-8 pt-7 pb-5 border-b border-outline-variant/10">
				<h2 class="font-extrabold text-2xl text-on-surface mb-1">Nouvelle Commande</h2>
				<p class="text-sm text-on-surface-variant">Remplissez tous les champs et ajoutez les articles de la commande.</p>
			</div>

			<form method="POST" action="{{ route('stock.dashboard') }}" class="px-8 py-7 space-y-8">
				@csrf
				<input type="hidden" name="action" value="create_order"/>

				<div>
					<p class="text-xs font-bold text-outline uppercase tracking-widest mb-4 flex items-center gap-2"><span class="material-symbols-outlined text-sm">info</span>Informations de la commande</p>
					<div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
						<div>
							<label class="block text-xs font-semibold text-on-surface-variant mb-1.5 ml-1">Montant total (DZD) <span class="text-error">*</span></label>
							<div class="relative"><span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">payments</span><input type="number" name="total_amount" required min="1" placeholder="ex: 15000" class="w-full pl-10 pr-4 py-3 bg-surface-container-high border-none rounded-xl text-sm"/></div>
						</div>
						<div>
							<label class="block text-xs font-semibold text-on-surface-variant mb-1.5 ml-1">Nombre de colis <span class="text-error">*</span></label>
							<div class="relative"><span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">inventory_2</span><input type="number" name="package_number" required min="1" value="1" class="w-full pl-10 pr-4 py-3 bg-surface-container-high border-none rounded-xl text-sm"/></div>
						</div>
					</div>
					<div class="mt-5">
						<label class="flex items-center gap-3 cursor-pointer group w-fit"><div class="relative"><input type="checkbox" name="is_urgent" id="urgentCheck" class="sr-only peer"/><div class="w-11 h-6 bg-surface-container-high rounded-full peer peer-checked:bg-error"></div><div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform peer-checked:translate-x-5"></div></div><div><span class="text-sm font-bold">🚨 Commande urgente</span><p class="text-xs text-on-surface-variant">Marquer comme prioritaire</p></div></label>
					</div>
				</div>

				<div class="h-px bg-outline-variant/20"></div>

				<div>
					<div class="flex items-center justify-between mb-4">
						<p class="text-xs font-bold text-outline uppercase tracking-widest flex items-center gap-2"><span class="material-symbols-outlined text-sm">medication</span>Articles de la commande</p>
						<button type="button" onclick="addItem()" class="flex items-center gap-1.5 text-primary text-xs font-bold hover:bg-primary/5 px-3 py-1.5 rounded-lg"><span class="material-symbols-outlined text-sm">add_circle</span>Ajouter un article</button>
					</div>
					<div id="itemsContainer" class="space-y-3">
						<div class="item-input-row flex gap-3 items-center p-3 bg-surface-container rounded-xl">
							<span class="material-symbols-outlined text-outline text-lg">medication</span>
							<input type="text" name="item_name[]" placeholder="Nom du médicament / article" required class="flex-1"/>
							<div class="relative w-28 flex-shrink-0"><input type="number" name="item_qty[]" placeholder="Qté" min="1" value="1" required class="w-full text-center"/></div>
							<button type="button" onclick="removeItem(this)" class="p-1.5 hover:bg-error-container hover:text-error rounded-lg text-on-surface-variant"><span class="material-symbols-outlined text-lg">remove_circle</span></button>
						</div>
					</div>
					<p class="text-xs text-on-surface-variant mt-2 ml-1">Ajoutez tous les médicaments inclus dans cette commande.</p>
				</div>

				<div class="h-px bg-outline-variant/20"></div>

				<div>
					<p class="text-xs font-bold text-outline uppercase tracking-widest mb-4 flex items-center gap-2"><span class="material-symbols-outlined text-sm">local_pharmacy</span>Assigner une pharmacie <span class="font-normal normal-case">(optionnel)</span></p>
					<div class="relative">
						<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">local_pharmacy</span>
						<select name="pharmacy_id" class="w-full pl-10 pr-4 py-3 bg-surface-container-high border-none rounded-xl text-sm appearance-none">
							<option value="">— Sélectionner une pharmacie (optionnel) —</option>
							@foreach ($pharmacies as $ph)
								<option value="{{ $ph['NIF'] ?? '' }}">#{{ $ph['NIF'] ?? '' }} · {{ $ph['FirstName'] ?? '' }} {{ $ph['LastName'] ?? '' }} · {{ $ph['Location'] ?? '' }}</option>
							@endforeach
						</select>
					</div>
					@if (empty($pharmacies))
						<p class="text-xs text-error mt-2 ml-1">⚠ Aucune pharmacie enregistrée. <a href="{{ route('register.pharmacy') }}" class="underline">En enregistrer une.</a></p>
					@endif
				</div>

				<div class="pt-2"><button type="submit" class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-primary to-primary-container text-white py-4 rounded-xl font-extrabold text-sm"> <span class="material-symbols-outlined text-xl">add_box</span>Créer la commande</button></div>
			</form>
		</div>

		<div id="assignModal" class="modal-backdrop">
			<div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-8">
				<div class="flex items-center justify-between mb-6">
					<div><h2 class="font-extrabold text-xl">Assigner une Pharmacie</h2><p class="text-xs text-on-surface-variant mt-0.5">Commande : <strong id="assignOrderId"></strong></p></div>
					<button onclick="closeAssignModal()" class="p-2 hover:bg-surface-container rounded-full"><span class="material-symbols-outlined">close</span></button>
				</div>
				<form method="POST" action="{{ route('stock.dashboard') }}" class="space-y-5">
					@csrf
					<input type="hidden" name="action" value="assign_pharmacy" />
					<input type="hidden" name="order_id" id="assignOrderIdInput" />
					<div class="space-y-2 max-h-72 overflow-y-auto pr-1" id="pharmacyRadioList">
						@foreach ($pharmacies as $ph)
							<label class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer">
								<input type="radio" name="pharmacy_id" value="{{ $ph['NIF'] ?? '' }}" class="accent-primary" />
								<div class="w-9 h-9 rounded-full bg-gradient-to-br from-primary to-primary-container text-white flex items-center justify-center text-sm font-bold">{{ strtoupper(substr($ph['FirstName'] ?? '', 0, 1)) }}</div>
								<div class="min-w-0"><p class="font-bold text-sm truncate">{{ $ph['FirstName'] ?? '' }} {{ $ph['LastName'] ?? '' }}</p><p class="text-xs text-on-surface-variant truncate">{{ $ph['Location'] ?? '' }}</p><p class="text-xs text-primary">{{ $ph['PhoneNumber'] ?? '' }}</p></div>
								<span class="ml-auto text-xs font-bold text-outline">#{{ $ph['NIF'] ?? '' }}</span>
							</label>
						@endforeach
						@if (empty($pharmacies))
							<p class="text-sm text-center text-on-surface-variant py-6">Aucune pharmacie disponible.<br/><a href="{{ route('register.pharmacy') }}" class="text-primary font-semibold underline">Enregistrer une pharmacie</a></p>
						@endif
					</div>
					<div class="flex gap-3 pt-2"><button type="button" onclick="closeAssignModal()" class="flex-1 px-5 py-3 border rounded-xl">Annuler</button><button type="submit" class="flex-1 bg-primary text-white px-5 py-3 rounded-xl">Confirmer</button></div>
				</form>
			</div>
		</div>

		<div id="detailsModal" class="modal-backdrop">
			<div class="bg-white rounded-2xl shadow-2xl max-w-md w-full">
				<div class="flex items-center justify-between px-7 pt-6 pb-4 border-b"><h2 class="font-extrabold text-xl">Détails de la commande</h2><div class="flex items-center gap-2"><button onclick="printOrderDetails()" class="px-3 py-2 bg-primary text-white rounded-xl">Imprimer</button><button onclick="closeDetailsModal()" class="p-2 hover:bg-surface-container rounded-full"><span class="material-symbols-outlined">close</span></button></div></div>
				<div class="px-7 py-5 space-y-4" id="detailsBody"></div>
			</div>
		</div>

		<div id="printArea" style="display:none;"></div>
	</div>
</main>

<style>
@media print {
	body > *:not(#printOverlay) { display: none !important; }
	#printOverlay { display: block !important; position: static !important; background: white !important; }
	#printOverlay * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
}
</style>
<div id="printOverlay" style="display:none; position:fixed; inset:0; background:white; z-index:9999; padding:40px; font-family:'Inter',sans-serif;"></div>

<script>
function switchTab(tab) {
	document.getElementById('tab-list').classList.toggle('hidden', tab !== 'list');
	document.getElementById('tab-create').classList.toggle('hidden', tab !== 'create');
	document.getElementById('tab-list-btn').classList.toggle('active', tab === 'list');
	document.getElementById('tab-create-btn').classList.toggle('active', tab === 'create');
}

function addItem() {
	const container = document.getElementById('itemsContainer');
	const row = document.createElement('div');
	row.className = 'item-input-row flex gap-3 items-center p-3 bg-surface-container rounded-xl';
	row.innerHTML = `
		<span class="material-symbols-outlined text-outline text-lg flex-shrink-0" style="font-variation-settings:'FILL' 1;">medication</span>
		<input type="text" name="item_name[]" placeholder="Nom du médicament / article" required class="flex-1"/>
		<div class="relative w-28 flex-shrink-0"><input type="number" name="item_qty[]" placeholder="Qté" min="1" value="1" required class="w-full text-center"/></div>
		<button type="button" onclick="removeItem(this)" class="p-1.5 hover:bg-error-container hover:text-error rounded-lg text-on-surface-variant"><span class="material-symbols-outlined text-lg">remove_circle</span></button>
	`;
	container.appendChild(row);
}
function removeItem(btn) {
	const rows = document.querySelectorAll('.item-input-row');
	if (rows.length > 1) btn.closest('.item-input-row').remove();
}

function openAssignModal(orderId) {
	document.getElementById('assignOrderId').textContent = orderId;
	document.getElementById('assignOrderIdInput').value = orderId;
	document.getElementById('assignModal').classList.add('open');
}
function closeAssignModal() {
	document.getElementById('assignModal').classList.remove('open');
}
document.getElementById('assignModal').addEventListener('click', function(e){ if (e.target === this) closeAssignModal(); });

function openDetailsModal(order) {
	const urgentBadge = parseInt(order.IsUrgen) === 1 ? '<span class="pill bg-error-container text-error">🚨 URGENTE</span>' : '';
	const pharmInfo = order.p_first ? `<span class="pill bg-green-100 text-green-700">${order.p_first} ${order.p_last}</span>` : '<span class="pill bg-yellow-100 text-yellow-700">⚠ Non assignée</span>';

	document.getElementById('detailsBody').innerHTML = `
		<div class="flex items-center justify-between"><span class="text-xs text-on-surface-variant font-medium">Tracking</span><span class="font-mono font-bold text-sm text-on-surface">${order.Tracking}</span></div>
		<div class="flex items-center justify-between"><span class="text-xs text-on-surface-variant font-medium">Date</span><span class="text-sm font-semibold">${order.Date}</span></div>
		<div class="flex items-center justify-between"><span class="text-xs text-on-surface-variant font-medium">Montant</span><span class="text-sm font-bold text-primary">${parseInt(order.otalAmount).toLocaleString('fr-DZ')} DZD</span></div>
		<div class="flex items-center justify-between"><span class="text-xs text-on-surface-variant font-medium">Colis</span><span class="text-sm font-semibold">${order.PackageNumber}</span></div>
		<div class="flex items-center justify-between"><span class="text-xs text-on-surface-variant font-medium">Statut</span><span class="pill bg-secondary-container text-on-secondary-container">⏳ En attente</span></div>
		<div class="flex items-center justify-between"><span class="text-xs text-on-surface-variant font-medium">Priorité</span>${urgentBadge || '<span class="pill bg-surface-container text-on-surface-variant">Normal</span>'}</div>
		<div class="flex items-center justify-between"><span class="text-xs text-on-surface-variant font-medium">Pharmacie</span>${pharmInfo}</div>
		${order.p_loc ? `<div class="flex items-start gap-2 pt-1"><span class="material-symbols-outlined text-sm text-outline mt-0.5">location_on</span><span class="text-xs text-on-surface-variant">${order.p_loc}</span></div>` : ''}
		<div class="pt-2"><button onclick="closeDetailsModal(); openAssignModal('${order.Tracking}');" class="w-full flex items-center justify-center gap-2 bg-primary text-white py-3 rounded-xl font-bold text-sm hover:opacity-90 active:scale-95 transition-all"><span class="material-symbols-outlined text-lg" style="font-variation-settings:'FILL' 1;">local_pharmacy</span>${order.p_first ? 'Changer la pharmacie' : 'Assigner une pharmacie'}</button></div>
	`;
	document.getElementById('detailsModal').classList.add('open');
}
function closeDetailsModal() { document.getElementById('detailsModal').classList.remove('open'); }
document.getElementById('detailsModal').addEventListener('click', function(e){ if (e.target === this) closeDetailsModal(); });
function printOrderDetails() { window.print(); }
if (new URLSearchParams(location.search).get('tab') === 'create') switchTab('create');
</script>
</body>
</html>
