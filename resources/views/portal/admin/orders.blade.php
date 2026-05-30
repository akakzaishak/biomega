<div class="fade-in flex flex-col gap-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="font-headline text-3xl font-extrabold tracking-tight text-on-surface">Orders</h1>
            <p class="text-on-surface-variant mt-1">Track deliveries, status, and pharmacy assignment.</p>
        </div>
        <div class="flex items-center gap-3">
            <form method="get" action="{{ route('admin.orders') }}" class="relative flex items-center gap-2">
                <input id="orderSearchInput" name="q" value="{{ $query ?? '' }}" class="bg-surface-container-lowest border border-outline-variant/20 rounded-xl px-4 py-2 text-sm w-72 focus:ring-2 focus:ring-primary/20" placeholder="Search tracking, pharmacy, or location" autocomplete="off">
                <div id="orderSearchResults" class="hidden absolute left-0 top-full mt-2 z-30 w-72 bg-white border border-slate-200 rounded-2xl shadow-lg max-h-64 overflow-y-auto"></div>
                <button class="bg-primary text-white px-4 py-2 rounded-xl text-sm font-bold">Search</button>
            </form>
            <button type="button" onclick="const form = document.getElementById('orderForm'); form.classList.toggle('hidden'); if (!form.classList.contains('hidden')) { form.scrollIntoView({behavior:'smooth', block:'start'}); }" class="inline-flex items-center gap-2 bg-primary text-white px-4 py-2.5 rounded-xl font-bold text-sm shadow-sm">
                <span class="material-symbols-outlined text-lg">add</span>Add Order
            </button>
        </div>
    </div>

    <div id="orderForm" class="hidden bg-slate-50 rounded-[2rem] border border-slate-200 p-4 sm:p-6">
        <div class="bg-white rounded-3xl border border-slate-200 shadow-lg p-8 max-w-5xl mx-auto">
            <div class="flex items-start justify-between gap-4 mb-6">
                <div>
                    <p class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-bold uppercase tracking-wider mb-4">
                        <span class="material-symbols-outlined text-sm">inventory_2</span>
                        Order Creation
                    </p>
                    <h2 class="text-3xl font-extrabold headline mb-2">Add Order</h2>
                    <p class="text-sm text-slate-600">Create a new order from the admin panel.</p>
                </div>
                <button type="button" onclick="document.getElementById('orderForm').classList.add('hidden');" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 hover:text-slate-700 whitespace-nowrap">
                    <span class="material-symbols-outlined text-base">close</span>
                    Close
                </button>
            </div>

            <form method="post" action="{{ route('admin.orders') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="action" value="create_order">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="relative">
                        <label class="text-xs font-bold uppercase tracking-widest text-slate-500">Pharmacy</label>
                        <input type="hidden" name="pharmacy_id" id="orderPharmacyId" value="{{ old('pharmacy_id') }}">
                        <input id="orderPharmacyInput" name="pharmacy_name" type="text" value="{{ old('pharmacy_name') }}" class="mt-2 w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm" placeholder="Type a pharmacy name" autocomplete="off">
                        <div id="orderPharmacyResults" class="hidden absolute left-0 right-0 mt-2 z-20 bg-white border border-slate-200 rounded-2xl shadow-lg max-h-56 overflow-y-auto"></div>
                    </div>
                    <div>
                        <label class="text-xs font-bold uppercase tracking-widest text-slate-500">Date</label>
                        <input name="date" type="date" value="{{ old('date', now()->toDateString()) }}" class="mt-2 w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm">
                    </div>
                </div>

                <div class="mt-3">
                    <label class="inline-flex items-center gap-3 text-sm font-semibold text-slate-700">
                        <input type="checkbox" name="is_urgent" value="1" class="rounded border-slate-300" {{ old('is_urgent') ? 'checked' : '' }}>
                        Urgent order
                    </label>
                </div>

                <div class="space-y-4">
                    <div id="orderProductsContainer" class="space-y-2">
                        @php
                            $oldProducts = old('product_name', []);
                            $oldQtys = old('quantity', []);
                        @endphp

                        @if(count($oldProducts))
                            @foreach($oldProducts as $i => $p)
                                @if(trim($p) !== '')
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center product-row">
                                        <div class="relative md:col-span-2">
                                            <input name="product_name[]" value="{{ $p }}" class="product-input mt-2 w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm" placeholder="Type a product name" autocomplete="off">
                                            <div class="product-results hidden absolute left-0 right-0 mt-2 z-20 bg-white border border-slate-200 rounded-2xl shadow-lg max-h-56 overflow-y-auto"></div>
                                        </div>
                                        <div>
                                            <input name="quantity[]" type="number" min="1" value="{{ $oldQtys[$i] ?? 1 }}" class="mt-2 w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm">
                                        </div>
                                        <div>
                                            <button type="button" class="remove-product mt-2 px-3 py-2 bg-red-50 text-red-700 rounded-xl text-xs font-bold">Remove</button>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center product-row">
                                <div class="relative md:col-span-2">
                                    <input name="product_name[]" class="product-input mt-2 w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm" placeholder="Type a product name" autocomplete="off">
                                    <div class="product-results hidden absolute left-0 right-0 mt-2 z-20 bg-white border border-slate-200 rounded-2xl shadow-lg max-h-56 overflow-y-auto"></div>
                                </div>
                                <div>
                                    <input name="quantity[]" type="number" min="1" value="1" class="mt-2 w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm">
                                </div>
                                <div>
                                    <button type="button" class="remove-product mt-2 px-3 py-2 bg-red-50 text-red-700 rounded-xl text-xs font-bold">Remove</button>
                                </div>
                            </div>
                        @endif
                    </div>

                </div>
                <div class="md:col-span-2 xl:col-span-4 flex justify-end gap-3 mt-2 pt-2 border-t border-slate-100">
                    <button type="button" onclick="document.getElementById('orderForm').classList.add('hidden');" class="px-5 py-3 border border-slate-200 rounded-xl font-semibold text-slate-700 bg-white hover:bg-slate-50">Cancel</button>
                    <button class="bg-primary text-white px-5 py-3 rounded-xl font-bold shadow-sm">Create Order</button>
                </div>
            </form>
        </div>
    </div>

    <div class="bg-surface-container-lowest rounded-3xl border border-outline-variant/15 shadow-sm p-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant/15 shadow-sm"><p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Orders</p><p class="mt-3 text-3xl font-headline font-extrabold">{{ number_format((int) count($orders)) }}</p></div>
        <div class="bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant/15 shadow-sm"><p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Delivered</p><p class="mt-3 text-3xl font-headline font-extrabold text-emerald-600">{{ number_format((int) count(array_filter($orders, fn ($order) => (int) ($order['Status'] ?? 0) === 1))) }}</p></div>
        <div class="bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant/15 shadow-sm"><p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Pending</p><p class="mt-3 text-3xl font-headline font-extrabold text-amber-600">{{ number_format((int) count(array_filter($orders, fn ($order) => (int) ($order['Status'] ?? 0) === 0))) }}</p></div>
        <div class="bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant/15 shadow-sm"><p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant/70">Urgent</p><p class="mt-3 text-3xl font-headline font-extrabold text-red-600">{{ number_format((int) count(array_filter($orders, fn ($order) => (int) ($order['IsUrgen'] ?? 0) === 1))) }}</p></div>
    </div>

    <div class="bg-surface-container-lowest rounded-3xl border border-outline-variant/15 shadow-sm p-6 overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="text-xs uppercase tracking-wider text-on-surface-variant">
                <tr class="border-b border-slate-200">
                    <th class="text-left py-3 pr-4">Tracking</th>
                    <th class="text-left py-3 pr-4">Date</th>
                    <th class="text-left py-3 pr-4">Pharmacy</th>
                    <th class="text-left py-3 pr-4">Location</th>
                    <th class="text-left py-3 pr-4">Amount</th>
                    <th class="text-left py-3 pr-4">Status</th>
                    <th class="text-left py-3 pr-4">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    @php($isUrgent = ((int) ($order['IsUrgen'] ?? 0) === 1))
                    @php($statusLabel = ((int) ($order['Status'] ?? 0) === 1) ? ['Delivered', 'bg-green-50 text-green-700'] : ['Pending', 'bg-amber-50 text-amber-700'])
                    <tr class="border-b border-slate-100 hover:bg-slate-50">
                        <td class="py-4 pr-4 font-semibold">{{ $order['Tracking'] ?? '—' }}</td>
                        <td class="py-4 pr-4 text-on-surface-variant">{{ $order['Date'] ?? '—' }}</td>
                        <td class="py-4 pr-4 text-on-surface-variant">{{ trim(($order['pharmacy_first'] ?? '').' '.($order['pharmacy_last'] ?? '')) ?: 'Unassigned' }}</td>
                        <td class="py-4 pr-4 text-on-surface-variant">{{ $order['pharmacy_location'] ?? '—' }}</td>
                        <td class="py-4 pr-4 text-on-surface-variant">DZD {{ number_format((float) ($order['otalAmount'] ?? 0), 0, '.', ',') }}</td>
                        <td class="py-4 pr-4">
                            <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $statusLabel[1] }}">
                                {{ $statusLabel[0] }}{{ $isUrgent ? ' · Urgent' : '' }}
                            </div>
                        </td>
                        <td class="py-4 pr-4">
                            <div class="flex flex-col gap-2 min-w-[180px]">
                            <form method="post" action="{{ route('admin.orders') }}" class="flex items-center gap-2">
                                @csrf
                                <input type="hidden" name="action" value="update_status">
                                <input type="hidden" name="tracking" value="{{ $order['Tracking'] ?? '' }}">
                                <select name="status" class="bg-white border border-outline-variant/20 rounded-xl px-3 py-2 text-xs font-semibold w-full">
                                    <option value="0" @selected((int) ($order['Status'] ?? 0) === 0)>Pending</option>
                                    <option value="1" @selected((int) ($order['Status'] ?? 0) === 1)>Delivered</option>
                                </select>
                                <button type="submit" class="px-3 py-2 bg-slate-900 text-white rounded-full text-xs font-bold whitespace-nowrap">Update</button>
                            </form>
                            <form method="post" action="{{ route('admin.orders') }}" onsubmit="return confirm('Delete this order?');">
                                @csrf
                                <input type="hidden" name="action" value="delete_order">
                                <input type="hidden" name="tracking" value="{{ $order['Tracking'] ?? '' }}">
                                <button type="submit" class="w-full px-3 py-2 bg-red-50 text-red-700 rounded-xl text-xs font-bold border border-red-100">Delete</button>
                            </form>
                            @if(((int) ($order['Status'] ?? 0)) === 0)
                                <form method="post" action="{{ route('admin.orders.complete', $order['Tracking'] ?? '') }}">
                                    @csrf
                                    <button type="submit" class="w-full px-3 py-2 bg-green-50 text-green-700 rounded-xl text-xs font-bold border border-green-100">Mark Complete</button>
                                </form>
                            @else
                                <span class="inline-flex justify-center w-full px-3 py-2 bg-slate-100 text-slate-600 rounded-xl text-xs font-bold border border-slate-200">Completed</span>
                            @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-10 text-center text-on-surface-variant">No orders found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@push('scripts')
<script>
(function () {
    const searchInput = document.getElementById('orderSearchInput');
    const searchResults = document.getElementById('orderSearchResults');
    const input = document.getElementById('orderPharmacyInput');
    const hidden = document.getElementById('orderPharmacyId');
    const results = document.getElementById('orderPharmacyResults');
    const productContainer = document.getElementById('orderProductsContainer');

    if (!input || !hidden || !results || !productContainer) return;

    const pharmacies = @json($pharmacies ?? []).map((pharmacy) => ({
        id: String(pharmacy.NIF ?? ''),
        label: (String(pharmacy.FirstName ?? '').trim() + ' ' + String(pharmacy.LastName ?? '').trim()).trim() || String(pharmacy.NIF ?? ''),
        location: String(pharmacy.Location ?? ''),
    }));

    const products = @json($products ?? []).map((product) => ({
        label: String(product.Name ?? '').trim(),
        qty: String(product.qty ?? ''),
    })).filter((product) => product.label !== '');

    const searchSuggestions = [
        ...@json($pharmacies ?? []).map((pharmacy) => ({
            type: 'pharmacy',
            label: (String(pharmacy.FirstName ?? '').trim() + ' ' + String(pharmacy.LastName ?? '').trim()).trim() || String(pharmacy.NIF ?? ''),
            value: (String(pharmacy.FirstName ?? '').trim() + ' ' + String(pharmacy.LastName ?? '').trim()).trim() || String(pharmacy.NIF ?? ''),
            meta: [String(pharmacy.NIF ?? ''), String(pharmacy.Location ?? '')].filter(Boolean).join(' · '),
        })),
        ...@json($orders ?? []).map((order) => ({
            type: 'tracking',
            value: String(order.Tracking ?? ''),
            label: String(order.Tracking ?? ''),
            meta: (String(order.pharmacy_first ?? '').trim() + ' ' + String(order.pharmacy_last ?? '').trim()).trim() || String(order.pharmacy_location ?? ''),
        }))
    ].filter((item) => item.value !== '' || item.label !== '');

    function normalize(value) {
        return (value || '').toString().trim().toLowerCase();
    }

    function createSearchSuggestionButton(item) {
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'w-full text-left px-4 py-3 hover:bg-slate-50 border-b border-slate-100 last:border-b-0';
        button.innerHTML = '<div class="font-semibold text-slate-900">' + item.label + '</div>' + (item.meta ? '<div class="text-xs text-slate-500 mt-1">' + item.meta + '</div>' : '');
        button.addEventListener('mousedown', (event) => {
            event.preventDefault();
            searchInput.value = item.value || item.label;
            searchResults.classList.add('hidden');
            searchInput.form.submit();
        });
        return button;
    }

    let searchButtons = [];
    let searchActiveIndex = -1;

    function highlightSearch(index) {
        searchButtons.forEach((button, buttonIndex) => {
            button.classList.toggle('bg-slate-100', buttonIndex === index);
            button.classList.toggle('text-slate-900', buttonIndex === index);
        });
    }

    function showSearchResults(query) {
        if (!searchInput || !searchResults) return;

        const term = normalize(query);
        const matches = term
            ? searchSuggestions.filter((item) => normalize(item.label).includes(term) || normalize(item.meta).includes(term) || normalize(item.value).includes(term))
            : searchSuggestions.slice(0, 8);

        searchResults.innerHTML = '';
        searchButtons = [];
        searchActiveIndex = -1;

        if (!matches.length) {
            searchResults.classList.add('hidden');
            return;
        }

        matches.slice(0, 8).forEach((item) => {
            const button = createSearchSuggestionButton(item);
            searchButtons.push(button);
            searchResults.appendChild(button);
        });

        searchResults.classList.remove('hidden');
        highlightSearch(searchActiveIndex);
    }

    if (searchInput && searchResults) {
        searchInput.addEventListener('input', () => showSearchResults(searchInput.value));
        searchInput.addEventListener('focus', () => showSearchResults(searchInput.value));
        searchInput.addEventListener('keydown', (event) => {
            if (searchResults.classList.contains('hidden') || !searchButtons.length) return;

            if (event.key === 'ArrowDown') {
                event.preventDefault();
                searchActiveIndex = (searchActiveIndex + 1) % searchButtons.length;
                highlightSearch(searchActiveIndex);
            }

            if (event.key === 'ArrowUp') {
                event.preventDefault();
                searchActiveIndex = searchActiveIndex <= 0 ? searchButtons.length - 1 : searchActiveIndex - 1;
                highlightSearch(searchActiveIndex);
            }

            if (event.key === 'Enter') {
                if (searchActiveIndex >= 0) {
                    event.preventDefault();
                    searchButtons[searchActiveIndex].dispatchEvent(new MouseEvent('mousedown', { bubbles: true, cancelable: true }));
                }
            }

            if (event.key === 'Escape') {
                searchResults.classList.add('hidden');
            }
        });

        searchInput.addEventListener('blur', () => {
            window.setTimeout(() => searchResults.classList.add('hidden'), 120);
        });

        document.addEventListener('click', (event) => {
            if (!searchResults.contains(event.target) && event.target !== searchInput) {
                searchResults.classList.add('hidden');
            }
        });

        if (searchInput.value) {
            showSearchResults(searchInput.value);
        }
    }

    function attachProductTypeahead(inputEl, resultsEl) {
        let activeIndex = -1;
        let currentButtons = [];

        function highlight(index) {
            currentButtons.forEach((button, buttonIndex) => {
                button.classList.toggle('bg-slate-100', buttonIndex === index);
                button.classList.toggle('text-slate-900', buttonIndex === index);
            });
        }

        function choose(index) {
            const button = currentButtons[index];
            if (button) button.dispatchEvent(new MouseEvent('mousedown', { bubbles: true, cancelable: true }));
        }

        function showResults(query) {
            const term = normalize(query);
            const matches = term
                ? products.filter((product) => normalize(product.label).includes(term))
                : products.slice(0, 8);

            resultsEl.innerHTML = '';
            currentButtons = [];
            activeIndex = -1;

            if (!matches.length) {
                resultsEl.classList.add('hidden');
                return;
            }

            matches.slice(0, 8).forEach((product) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'w-full text-left px-4 py-3 hover:bg-slate-50 border-b border-slate-100 last:border-b-0';
                button.innerHTML = '<div class="font-semibold text-slate-900">' + product.label + '</div>' + (product.qty ? '<div class="text-xs text-slate-500 mt-1">Requested ' + product.qty + '</div>' : '');
                button.addEventListener('mousedown', (event) => {
                    event.preventDefault();
                    inputEl.value = product.label;
                    resultsEl.classList.add('hidden');
                    inputEl.dispatchEvent(new Event('input', { bubbles: true }));
                });
                resultsEl.appendChild(button);
                currentButtons.push(button);
            });

            resultsEl.classList.remove('hidden');
            highlight(activeIndex);
        }

        inputEl.addEventListener('input', () => showResults(inputEl.value));
        inputEl.addEventListener('focus', () => showResults(inputEl.value));
        inputEl.addEventListener('keydown', (event) => {
            if (resultsEl.classList.contains('hidden') || !currentButtons.length) return;

            if (event.key === 'ArrowDown') {
                event.preventDefault();
                activeIndex = (activeIndex + 1) % currentButtons.length;
                highlight(activeIndex);
            }

            if (event.key === 'ArrowUp') {
                event.preventDefault();
                activeIndex = activeIndex <= 0 ? currentButtons.length - 1 : activeIndex - 1;
                highlight(activeIndex);
            }

            if (event.key === 'Enter') {
                if (activeIndex >= 0) {
                    event.preventDefault();
                    choose(activeIndex);
                }
            }

            if (event.key === 'Escape') {
                resultsEl.classList.add('hidden');
            }
        });
        inputEl.addEventListener('blur', () => { window.setTimeout(() => resultsEl.classList.add('hidden'), 120); });
    }

    function createAddButton() {
        const addBtn = document.createElement('button');
        addBtn.type = 'button';
        addBtn.className = 'add-product ml-auto mt-2 w-10 h-10 flex items-center justify-center bg-slate-900 text-white rounded-full text-xl font-bold';
        addBtn.textContent = '+';
        addBtn.setAttribute('aria-label', 'Add another product');
        return addBtn;
    }

    function addProductRow() {
        const newRow = makeProductRow('', 1);
        productContainer.appendChild(newRow);
        const newInput = newRow.querySelector('.product-input');
        if (newInput) newInput.focus();
        syncProductControls();
    }

    function removeProductRow(row) {
        if (!row) return;

        row.remove();

        if (!productContainer.querySelector('.product-row')) {
            productContainer.appendChild(makeProductRow('', 1));
        }

        syncProductControls();
    }

    function makeProductRow(name = '', qty = 1) {
        const row = document.createElement('div');
        row.className = 'grid grid-cols-1 md:grid-cols-3 gap-4 items-center product-row';

        const left = document.createElement('div');
        left.className = 'relative md:col-span-2';
        const input = document.createElement('input');
        input.name = 'product_name[]';
        input.className = 'product-input mt-2 w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm';
        input.placeholder = 'Type a product name';
        input.autocomplete = 'off';
        input.value = name;
        const resultsEl = document.createElement('div');
        resultsEl.className = 'product-results hidden absolute left-0 right-0 mt-2 z-20 bg-white border border-slate-200 rounded-2xl shadow-lg max-h-56 overflow-y-auto';
        left.appendChild(input);
        left.appendChild(resultsEl);

        const mid = document.createElement('div');
        const qtyInput = document.createElement('input');
        qtyInput.name = 'quantity[]';
        qtyInput.type = 'number';
        qtyInput.min = '1';
        qtyInput.value = String(qty);
        qtyInput.className = 'mt-2 w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm';
        mid.appendChild(qtyInput);

        const right = document.createElement('div');
        right.className = 'flex items-center gap-2';
        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'remove-product mt-2 px-3 py-2 bg-red-50 text-red-700 rounded-xl text-xs font-bold';
        removeBtn.textContent = 'Remove';
        removeBtn.setAttribute('aria-label', 'Remove this product row');
        const addBtn = createAddButton();
        right.appendChild(removeBtn);
        right.appendChild(addBtn);

        row.appendChild(left);
        row.appendChild(mid);
        row.appendChild(right);

        attachProductTypeahead(input, resultsEl);
        input.addEventListener('input', () => syncProductControls());

        return row;
    }

    function render(query) {
        const term = normalize(query);
        const matches = term
            ? pharmacies.filter((pharmacy) => normalize(pharmacy.label).includes(term) || normalize(pharmacy.location).includes(term))
            : pharmacies.slice(0, 8);

        results.innerHTML = '';
        pharmacyButtons = [];
        pharmacyActiveIndex = -1;

        if (!matches.length) {
            results.classList.add('hidden');
            return;
        }

        matches.slice(0, 8).forEach((pharmacy) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'w-full text-left px-4 py-3 hover:bg-slate-50 border-b border-slate-100 last:border-b-0';
            button.innerHTML = '<div class="font-semibold text-slate-900">' + pharmacy.label + '</div>' + (pharmacy.location ? '<div class="text-xs text-slate-500 mt-1">' + pharmacy.location + '</div>' : '');
            button.addEventListener('mousedown', (event) => {
                event.preventDefault();
                input.value = pharmacy.label;
                hidden.value = pharmacy.id;
                results.classList.add('hidden');
            });
            results.appendChild(button);
            pharmacyButtons.push(button);
        });

        results.classList.remove('hidden');
        highlightPharmacy(pharmacyActiveIndex);
    }

    input.addEventListener('input', () => {
        hidden.value = '';
        render(input.value);
    });

    input.addEventListener('focus', () => render(input.value));

    let pharmacyActiveIndex = -1;
    let pharmacyButtons = [];

    function highlightPharmacy(index) {
        pharmacyButtons.forEach((button, buttonIndex) => {
            button.classList.toggle('bg-slate-100', buttonIndex === index);
            button.classList.toggle('text-slate-900', buttonIndex === index);
        });
    }

    function choosePharmacy(index) {
        const button = pharmacyButtons[index];
        if (button) button.dispatchEvent(new MouseEvent('mousedown', { bubbles: true, cancelable: true }));
    }

    input.addEventListener('keydown', (event) => {
        if (results.classList.contains('hidden') || !pharmacyButtons.length) return;

        if (event.key === 'ArrowDown') {
            event.preventDefault();
            pharmacyActiveIndex = (pharmacyActiveIndex + 1) % pharmacyButtons.length;
            highlightPharmacy(pharmacyActiveIndex);
        }

        if (event.key === 'ArrowUp') {
            event.preventDefault();
            pharmacyActiveIndex = pharmacyActiveIndex <= 0 ? pharmacyButtons.length - 1 : pharmacyActiveIndex - 1;
            highlightPharmacy(pharmacyActiveIndex);
        }

        if (event.key === 'Enter') {
            if (pharmacyActiveIndex >= 0) {
                event.preventDefault();
                choosePharmacy(pharmacyActiveIndex);
            }
        }

        if (event.key === 'Escape') {
            results.classList.add('hidden');
        }
    });

    input.addEventListener('blur', () => {
        window.setTimeout(() => results.classList.add('hidden'), 120);
    });

    document.addEventListener('click', (event) => {
        if (!results.contains(event.target) && event.target !== input) {
            results.classList.add('hidden');
        }
    });

    if (input.value) {
        render(input.value);
    }

    function syncProductControls() {
        const rows = Array.from(productContainer.querySelectorAll('.product-row'));

        if (!rows.length) {
            productContainer.appendChild(makeProductRow('', 1));
            return syncProductControls();
        }

        rows.forEach((row, index) => {
            const inputEl = row.querySelector('.product-input');
            const actions = row.children[2] || row.appendChild(document.createElement('div'));
            actions.classList.add('flex', 'items-center', 'gap-2');

            let removeBtn = row.querySelector('.remove-product');
            if (!removeBtn) {
                removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'remove-product mt-2 px-3 py-2 bg-red-50 text-red-700 rounded-xl text-xs font-bold';
                removeBtn.textContent = 'Remove';
                removeBtn.setAttribute('aria-label', 'Remove this product row');
                actions.insertBefore(removeBtn, actions.firstChild || null);
            }

            let addBtn = row.querySelector('.add-product');
            if (!addBtn) {
                addBtn = createAddButton();
                actions.appendChild(addBtn);
            }

            const filled = Boolean(inputEl && inputEl.value && inputEl.value.toString().trim() !== '');
            removeBtn.style.display = rows.length > 1 || filled ? '' : 'none';
            addBtn.style.display = index === rows.length - 1 && filled ? '' : 'none';
        });
    }

    document.querySelectorAll('.product-row').forEach((row) => {
        const inputEl = row.querySelector('.product-input');
        const resultsEl = row.querySelector('.product-results');
        if (inputEl && resultsEl) attachProductTypeahead(inputEl, resultsEl);
        if (inputEl) inputEl.addEventListener('input', () => syncProductControls());
    });

    productContainer.addEventListener('click', (event) => {
        const removeBtn = event.target.closest('.remove-product');
        if (removeBtn) {
            const row = removeBtn.closest('.product-row');
            removeProductRow(row);
            return;
        }

        const addBtn = event.target.closest('.add-product');
        if (addBtn) {
            addProductRow();
        }
    });

    syncProductControls();
})();
</script>
@endpush
