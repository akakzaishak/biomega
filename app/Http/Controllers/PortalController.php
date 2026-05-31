<?php

namespace App\Http\Controllers;

use App\Services\PortalService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use RuntimeException;

class PortalController extends Controller
{
    public function __construct(private PortalService $portal)
    {
    }

    private function requireRole(string $role)
    {
        if (session('table') !== $role) {
            return redirect()->route('login');
        }

        return null;
    }

    private function userName(string $fallback = 'User'): string
    {
        $first = session('firstname', $fallback);
        $last = session('lastname', '');
        return trim($first . ' ' . $last);
    }

    public function home()
    {
        return view('portal.public', [
            'page' => 'home',
        ]);
    }

    public function login(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->validate([
                'phone' => ['required', 'string'],
                'password' => ['required', 'string'],
            ]);

            $phone = trim((string) $data['phone']);
            $password = trim((string) $data['password']);

            $authenticated = $this->portal->authenticateByPhone($phone);

            if (!empty($authenticated)) {
                $user = $authenticated['user'];
                $stored = (string) ($user->Password ?? '');
                if ($password !== $stored && !Hash::check($password, $stored)) {
                    return back()->withInput()->with('error', 'Incorrect password.');
                }

                session([
                    'user_id' => $user->ID ?? $user->NIF ?? null,
                    'phone' => $phone,
                    'firstname' => $user->FirstName ?? 'User',
                    'lastname' => $user->LastName ?? '',
                    'role' => $user->Role ?? $authenticated['table'],
                    'table' => $authenticated['table'],
                ]);

                return redirect()->to($authenticated['redirect'])->with('success', 'Welcome back, '.$user->FirstName.'.');
            }

            return back()->withInput()->with('error', 'No account found with this phone number.');
        }

        return view('portal.public', [
            'page' => 'login',
            'error' => session('error'),
            'success' => session('success'),
        ]);
    }

    public function logout(Request $request)
    {
        $request->session()->flush();
        return redirect()->route('home');
    }

    public function registerPharmacy(Request $request)
    {
        $wilayas = $this->portal->wilayas();

        if ($request->isMethod('post')) {
            $nif = trim((string) $request->input('nif', ''));
            $firstname = trim((string) $request->input('firstname', ''));
            $lastname = trim((string) $request->input('lastname', ''));
            $phone = trim((string) $request->input('phone', ''));
            $worktime = trim((string) $request->input('worktime', ''));
            $password = trim((string) $request->input('password', ''));
            $confirm = trim((string) $request->input('confirm', ''));
            $location = trim((string) $request->input('location', ''));
            $wilaya = trim((string) $request->input('wilaya', ''));

            if ($nif === '' || $firstname === '' || $lastname === '' || $phone === '' || $worktime === '' || $password === '' || $location === '' || $wilaya === '') {
                return back()->withInput()->with('error', 'Veuillez remplir tous les champs obligatoires.');
            }

            if (!ctype_digit($nif)) {
                return back()->withInput()->with('error', 'Le NIF doit contenir uniquement des chiffres.');
            }

            if (!preg_match('/^0[567]\d{8}$/', $phone)) {
                return back()->withInput()->with('error', 'Numéro de téléphone invalide (ex: 0555123456).');
            }

            if (strlen($password) < 6) {
                return back()->withInput()->with('error', 'Le mot de passe doit contenir au moins 6 caractères.');
            }

            if ($password !== $confirm) {
                return back()->withInput()->with('error', 'Les mots de passe ne correspondent pas.');
            }

            $data = [
                'nif' => $nif,
                'firstname' => $firstname,
                'lastname' => $lastname,
                'phone' => $phone,
                'worktime' => $worktime,
                'password' => $password,
                'location' => $wilaya . ' - ' . $location,
            ];

            try {
                $this->portal->createPharmacy($data);
            } catch (RuntimeException $exception) {
                return back()->withInput()->with('error', $exception->getMessage());
            }

            return redirect()->route('register.pharmacy')->with('success', 'Votre pharmacie a été enregistrée avec succès ! Vous pouvez maintenant vous connecter.');
        }

        return view('portal.public', [
            'page' => 'register',
            'wilayas' => $wilayas,
            'success' => session('success'),
            'error' => session('error'),
        ]);
    }

    

    public function adminDashboard()
    {
        if ($redirect = $this->requireRole('admin')) return $redirect;

        return view('portal.admin', array_merge(['userName' => $this->userName('Admin')], $this->portal->adminCommon(), [
            'page' => 'dashboard',
            'recentOrders' => $this->portal->recentOrders(),
            'recentEmployees' => $this->portal->recentEmployees(),
        ]));
    }

    public function adminOrders(Request $request)
    {
        if ($redirect = $this->requireRole('admin')) return $redirect;

        if ($request->isMethod('post') && ($request->input('action') === 'create_order' || $request->has('items') || $request->has('item_name'))) {
            $data = $request->validate([
                'amount' => ['nullable', 'numeric', 'min:0'],
                'package_number' => ['nullable', 'integer', 'min:1'],
                'product_name' => ['required', 'array', 'min:1'],
                'product_name.*' => ['required', 'string'],
                'quantity' => ['required', 'array', 'min:1'],
                'quantity.*' => ['required', 'integer', 'min:1'],
                'status' => ['nullable', 'integer', 'in:0,1'],
                'date' => ['nullable', 'date'],
                'pharmacy_id' => ['nullable', 'string'],
                'deliveryperson_id' => ['nullable', 'integer'],
                'is_urgent' => ['nullable'],
            ]);

            $data['amount'] = $data['amount'] ?? 0;
            $data['package_number'] = $data['package_number'] ?? 1;
            $data['status'] = isset($data['status']) ? (int) $data['status'] : 0;

            try {
                $tracking = $this->portal->createAdminOrder($data);
                return redirect()->route('admin.orders')->with('success', "Order $tracking created successfully.");
            } catch (\Throwable $exception) {
                return back()->withInput()->with('error', 'Could not create the order.');
            }
        }

        if ($request->isMethod('post') && $request->input('action') === 'assign') {
            $data = $request->validate([
                'order_id' => ['required', 'string'],
                'pharmacy_id' => ['nullable', 'string'],
                'dp_phone' => ['required', 'string'],
            ]);

            try {
                $assigned = $this->portal->assignDeliveryPersonToOrder($data['order_id'], $data['pharmacy_id'] ?? null, $data['dp_phone']);
                if ($assigned) {
                    return back()->with('success', "Order #{$data['order_id']} assigned successfully.");
                }
            } catch (\Throwable $exception) {
                return back()->with('error', 'Could not assign delivery person.');
            }

            return back()->with('error', 'Could not assign delivery person.');
        }

        if ($request->isMethod('post') && $request->input('action') === 'update_amount') {
            $data = $request->validate([
                'order_id' => ['required', 'string'],
                'amount' => ['required', 'integer', 'min:0'],
            ]);

            try {
                $updated = $this->portal->updateOrderAmount($data['order_id'], (int) $data['amount']);
                if ($updated) {
                    return back()->with('success', "Amount updated for order #{$data['order_id']}.");
                }
            } catch (\Throwable $exception) {
                return back()->with('error', 'Could not update amount.');
            }

            return back()->with('error', 'Order not found.');
        }

        if ($request->isMethod('post') && $request->input('action') === 'update_status') {
            $data = $request->validate([
                'tracking' => ['required', 'string'],
                'status' => ['required', 'integer', 'in:0,1'],
            ]);

            try {
                $updated = $this->portal->updateOrderStatus($data['tracking'], (int) $data['status']);
                if ($updated) {
                    return back()->with('success', 'Order status updated.');
                }
            } catch (\Throwable $exception) {
                return back()->with('error', 'Could not update order status.');
            }

            return back()->with('error', 'Order not found.');
        }

        if ($request->isMethod('post') && $request->input('action') === 'delete_order') {
            $data = $request->validate([
                'tracking' => ['required', 'string'],
            ]);

            try {
                $deleted = $this->portal->deleteOrder($data['tracking']);
                if ($deleted) {
                    return back()->with('success', 'Order deleted.');
                }
            } catch (\Throwable $exception) {
                return back()->with('error', 'Could not delete order.');
            }

            return back()->with('error', 'Order not found.');
        }

        $query = trim((string) $request->query('q', ''));
        $orders = $this->portal->adminOrderDashboardOrders();

        if ($query !== '') {
            $needle = mb_strtolower($query);
            $orders = array_values(array_filter($orders, static function (array $order) use ($needle) {
                $haystacks = [
                    (string) ($order['Tracking'] ?? ''),
                    (string) ($order['Date'] ?? ''),
                    (string) ($order['ph_first'] ?? ''),
                    (string) ($order['ph_last'] ?? ''),
                    (string) ($order['ph_loc'] ?? ''),
                    (string) ($order['dp_first'] ?? ''),
                    (string) ($order['dp_last'] ?? ''),
                    (string) ($order['assigned_pharmacy'] ?? ''),
                ];

                foreach ($haystacks as $haystack) {
                    if ($haystack !== '' && str_contains(mb_strtolower($haystack), $needle)) {
                        return true;
                    }
                }

                return false;
            }));
        }

        return view('portal.admin', array_merge(['userName' => $this->userName('Admin')], $this->portal->adminCommon(), [
            'page' => 'orders',
            'orders' => $orders,
            'deliveryPersons' => $this->portal->deliveryDrivers(),
            'query' => $query,
        ]));
    }

    public function completeOrder(string $tracking)
    {
        if ($redirect = $this->requireRole('admin')) return $redirect;

        try {
            $order = $this->portal->markOrderComplete($tracking);
            if ($order) {
                return back()->with('success', 'Order marked complete.');
            }
            return back()->with('error', 'Order not found.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Could not update order.');
        }
    }

    public function adminPharmacies(Request $request)
    {
        if ($redirect = $this->requireRole('admin')) return $redirect;

        if ($request->isMethod('post') && $request->filled('delete_nif')) {
            $nif = trim((string) $request->input('delete_nif', ''));

            if ($nif !== '') {
                $hasOrders = DB::table('asined_order')
                    ->where('pharmacy_id', $nif)
                    ->count() > 0;

                if ($hasOrders && !$request->boolean('force_delete')) {
                    return redirect()
                        ->route('admin.pharmacies', ['q' => (string) $request->input('q', '')])
                        ->with('error', 'Cette pharmacie possède des commandes assignées. Confirmez la suppression forcée.')
                        ->with('pending_delete_nif', $nif);
                }

                try {
                    $deleted = $this->portal->deletePharmacy($nif);

                    if ($deleted) {
                        $request->session()->forget('pending_delete_nif');
                        return redirect()
                            ->route('admin.pharmacies', ['q' => (string) $request->input('q', '')])
                            ->with('success', "Pharmacie #{$nif} supprimée avec succès.");
                    }

                    return redirect()
                        ->route('admin.pharmacies', ['q' => (string) $request->input('q', '')])
                        ->with('error', 'Pharmacy not found.');
                } catch (\Throwable $e) {
                    return redirect()
                        ->route('admin.pharmacies', ['q' => (string) $request->input('q', '')])
                        ->with('error', 'Erreur lors de la suppression.');
                }
            }
        }

        $data = $this->portal->adminPharmaciesData((string) $request->query('q', ''));

        return view('portal.admin', array_merge(['userName' => $this->userName('Admin')], $this->portal->adminCommon(), [
            'page' => 'pharmacies',
        ], $data));
    }

    public function deletePharmacy(string $nif)
    {
        if ($redirect = $this->requireRole('admin')) return $redirect;

        try {
            $deleted = $this->portal->deletePharmacy($nif);
            if ($deleted) {
                request()->session()->forget('pending_delete_nif');
                return redirect()->route('admin.pharmacies')->with('success', "Pharmacie #{$nif} supprimée avec succès.");
            }
            return redirect()->route('admin.pharmacies')->with('error', 'Pharmacy not found.');
        } catch (\Throwable $e) {
            return redirect()->route('admin.pharmacies')->with('error', 'Erreur lors de la suppression.');
        }
    }

    public function adminEmployees(Request $request)
    {
        if ($redirect = $this->requireRole('admin')) return $redirect;

        if ($request->isMethod('post') && $request->input('action') === 'add_employee') {
            $data = $request->validate([
                'firstname' => ['required', 'string'],
                'lastname' => ['required', 'string'],
                'phone' => ['required', 'string'],
                'role' => ['required', 'string'],
                'password' => ['required', 'string', 'min:6'],
                'confirm' => ['required', 'same:password'],
            ]);

            try {
                $this->portal->createEmployee($data['role'], $data);
                return redirect()->route('admin.employees')->with('success', 'Employee added successfully.');
            } catch (RuntimeException $exception) {
                return back()->withInput()->with('error', $exception->getMessage());
            }
        }

        if ($request->isMethod('post') && $request->input('action') === 'delete_employee') {
            $role = (string) $request->input('del_table', '');
            $id = $request->filled('del_id') ? (int) $request->input('del_id') : null;
            $phone = (string) $request->input('del_phone', '');

            if ($role !== '') {
                $deleted = $this->portal->deleteEmployee($role, $id, $phone);
                if ($deleted) {
                    return back()->with('success', 'Employee removed successfully.');
                }
            }

            return back()->with('error', 'Could not remove employee.');
        }

        return view('portal.admin', array_merge(['userName' => $this->userName('Admin')], $this->portal->adminCommon(), [
            'page' => 'employees',
            'employees' => $this->portal->employees(),
        ]));
    }

    public function adminTracking(Request $request)
    {
        if ($redirect = $this->requireRole('admin')) return $redirect;

        if ($request->isMethod('post') && $request->filled('force_gps_phone')) {
            $phone = trim((string) $request->input('force_gps_phone', ''));

            if ($phone !== '') {
                $adminName = trim((string) session('firstname', 'Admin') . ' ' . (string) session('lastname', ''));
                $this->portal->forceDeliveryGps($phone, $adminName);

                return redirect()->route('admin.tracking', ['forced' => $phone]);
            }
        }

        $tracking = $this->portal->trackingDashboardData();

        return view('portal.admin', array_merge(['userName' => $this->userName('Admin')], $this->portal->adminCommon(), [
            'page' => 'tracking',
            'routes' => $tracking['assignedOrders'],
            'drivers' => $tracking['deliveryPersons'],
            'deliveryPersons' => $tracking['deliveryPersons'],
            'routeHistory' => $tracking['routeHistory'],
            'assignedOrders' => $tracking['assignedOrders'],
            'stats' => $tracking['stats'],
            'dpColors' => $tracking['dpColors'],
            'forced' => $request->query('forced'),
        ]));
    }

    public function adminSettings()
    {
        if ($redirect = $this->requireRole('admin')) return $redirect;

        return view('portal.admin', array_merge(['userName' => $this->userName('Admin')], $this->portal->adminCommon(), [
            'page' => 'settings',
        ]));
    }

    public function adminInventory()
    {
        if ($redirect = $this->requireRole('admin')) return $redirect;

        return view('portal.admin', array_merge(['userName' => $this->userName('Admin')], $this->portal->adminCommon(), [
            'page' => 'inventory',
            'items' => $this->portal->inventoryItems(),
        ]));
    }

    public function adminReports()
    {
        if ($redirect = $this->requireRole('admin')) return $redirect;

        return view('portal.admin', array_merge(['userName' => $this->userName('Admin')], $this->portal->adminCommon(), [
            'page' => 'reports',
            ...$this->portal->reportData(),
        ]));
    }

    public function adminPayments()
    {
        if ($redirect = $this->requireRole('admin')) return $redirect;

        return view('portal.admin', array_merge(['userName' => $this->userName('Admin')], $this->portal->adminCommon(), [
            'page' => 'payments',
            ...$this->portal->paymentsData(),
        ]));
    }

    public function commercialDashboard()
    {
        if ($redirect = $this->requireRole('commercialservice')) return $redirect;

        return view('portal.role', array_merge([
            'page' => 'commercial',
            'userName' => $this->userName('Commercial'),
        ], $this->portal->commercialDashboard()));
    }

    public function deliveryManagerDashboard(Request $request)
    {
        if ($redirect = $this->requireRole('deliverymanager')) return $redirect;

        $managerName = trim((string) session('firstname', '') . ' ' . (string) session('lastname', ''));
        $success = '';
        $error = '';

        if ($request->isMethod('post') && $request->input('action') === 'assign') {
            $orderId = trim((string) $request->input('order_id', ''));
            $pharmacyId = trim((string) $request->input('pharmacy_id', ''));
            $dpPhone = trim((string) $request->input('dp_phone', ''));

            if ($orderId === '' || $dpPhone === '') {
                $error = 'Please select a delivery person.';
            } else {
                $wasAssigned = DB::table('asined_order')->where('order_id', $orderId)->exists();
                $assigned = $this->portal->assignDeliveryPersonToOrder($orderId, $pharmacyId !== '' ? $pharmacyId : null, $dpPhone);

                if ($assigned) {
                    $success = $wasAssigned
                        ? "Order <strong>#{$orderId}</strong> reassigned successfully."
                        : "Order <strong>#{$orderId}</strong> assigned successfully.";
                } else {
                    $error = 'Could not assign delivery person.';
                }
            }
        }

        if ($request->isMethod('post') && $request->input('action') === 'update_amount') {
            $orderId = trim((string) $request->input('order_id', ''));
            $amount = (int) $request->input('amount', 0);

            if ($orderId === '' || $amount < 0) {
                $error = 'Invalid amount.';
            } else {
                $updated = $this->portal->updateOrderAmount($orderId, $amount);

                if ($updated) {
                    $success = "Amount updated for order <strong>#{$orderId}</strong>.";
                } else {
                    $error = 'Failed to update amount.';
                }
            }
        }

        $orders = DB::table('order as o')
            ->leftJoin('asined_order as a', 'o.Tracking', '=', 'a.order_id')
            ->leftJoin('deliveryperson as d', 'a.deliveryperson_id', '=', 'd.PhoneNumber')
            ->leftJoin('pharmacy as p', 'a.pharmacy_id', '=', 'p.NIF')
            ->orderByDesc('o.Date')
            ->select([
                'o.*',
                'a.deliveryperson_id',
                DB::raw('a.pharmacy_id AS assigned_pharmacy'),
                DB::raw('d.FirstName AS dp_first'),
                DB::raw('d.LastName AS dp_last'),
                DB::raw('p.FirstName AS ph_first'),
                DB::raw('p.LastName AS ph_last'),
                DB::raw('p.Location AS ph_loc'),
            ])
            ->get()
            ->map(static fn ($row) => (array) $row)
            ->toArray();

        $deliveryPersons = DB::table('deliveryperson')
            ->orderBy('FirstName')
            ->get()
            ->map(static fn ($row) => (array) $row)
            ->toArray();

        $totalOrders = count($orders);
        $delivered = count(array_filter($orders, static fn (array $order) => (int) ($order['Status'] ?? 0) === 1));
        $notDelivered = count(array_filter($orders, static fn (array $order) => (int) ($order['Status'] ?? 0) === 0));
        $assigned = count(array_filter($orders, static fn (array $order) => !empty($order['deliveryperson_id'])));
        $unassigned = $totalOrders - $assigned;

        return view('portal.delivery_manager.dashboard', [
            'managerName' => $managerName,
            'success' => $success,
            'error' => $error,
            'orders' => $orders,
            'deliveryPersons' => $deliveryPersons,
            'totalOrders' => $totalOrders,
            'delivered' => $delivered,
            'notDelivered' => $notDelivered,
            'assigned' => $assigned,
            'unassigned' => $unassigned,
        ]);
    }

    public function deliveryPersonDashboard(Request $request)
    {
        if ($redirect = $this->requireRole('deliveryperson')) return $redirect;

        $firstname = (string) session('firstname', 'Delivery');
        $lastname = (string) session('lastname', 'Person');
        $phone = (string) session('phone', '');
        $gpsForced = false;
        $forcedByAdmin = null;

        if ($phone !== '' && Schema::hasTable('delivery_location')) {
            $location = DB::table('delivery_location')
                ->where('PhoneNumber', $phone)
                ->first(['GpsForced', 'ForcedByAdmin']);

            if ($location) {
                $gpsForced = (int) ($location->GpsForced ?? 0) === 1;
                $forcedByAdmin = $gpsForced ? (string) ($location->ForcedByAdmin ?? '') : null;
            }
        }

        if ($request->isMethod('post') && $request->filled('action')) {
            $tracking = trim((string) $request->input('tracking', ''));
            $action = (string) $request->input('action', '');
            $flash = '';
            $flashType = 'success';

            if ($tracking !== '') {
                if ($action === 'charge_transit') {
                    $flash = "Commande #{$tracking} chargée en transit.";
                } elseif ($action === 'livre') {
                    $proofPath = '';
                    $proofData = (string) $request->input('proof_image_data', '');

                    if ($proofData !== '') {
                        $data = preg_replace('/^data:image\/\w+;base64,/', '', $proofData);
                        $decoded = base64_decode((string) $data, true);

                        if ($decoded !== false) {
                            $uploadDir = public_path('uploads/proofs');

                            if (!is_dir($uploadDir)) {
                                mkdir($uploadDir, 0755, true);
                            }

                            $filename = 'proof_' . $tracking . '_' . time() . '.jpg';
                            file_put_contents($uploadDir . DIRECTORY_SEPARATOR . $filename, $decoded);
                            $proofPath = 'uploads/proofs/' . $filename;
                        }
                    }

                    DB::table('order')
                        ->where('Tracking', $tracking)
                        ->update([
                            'Status' => 1,
                            'ProofImage' => $proofPath,
                        ]);

                    $flash = "Commande #{$tracking} marquée comme LIVRÉE ✓";
                } elseif ($action === 'non_livre') {
                    DB::table('order')
                        ->where('Tracking', $tracking)
                        ->update(['Status' => 3]);

                    $flash = "Commande #{$tracking} marquée comme NON LIVRÉE.";
                    $flashType = 'error';
                }
            }

            return redirect()->route('delivery-person.dashboard', [
                'flash' => $flash,
                'type' => $flashType,
            ]);
        }

        $flash = (string) $request->query('flash', '');
        $flashType = (string) $request->query('type', 'success');

        $orders = DB::table('asined_order as ao')
            ->leftJoin('order as o', 'ao.order_id', '=', 'o.Tracking')
            ->leftJoin('pharmacy as p', 'ao.pharmacy_id', '=', 'p.NIF')
            ->where('ao.deliveryperson_id', $phone)
            ->orderByDesc('o.IsUrgen')
            ->orderByDesc('o.Status')
            ->orderByDesc('o.Date')
            ->select([
                DB::raw('ao.order_id AS tracking'),
                DB::raw('ao.pharmacy_id AS pharmacy_nif'),
                DB::raw('o.PackageNumber AS packages'),
                DB::raw('o.otalAmount AS amount'),
                DB::raw('o.Date AS order_date'),
                DB::raw('o.Status AS status'),
                DB::raw('o.IsUrgen AS urgent'),
                DB::raw('o.ProofImage AS proof'),
                DB::raw('p.FirstName AS pharm_first'),
                DB::raw('p.LastName AS pharm_last'),
                DB::raw('p.PhoneNumber AS pharm_phone'),
                DB::raw('p.Location AS pharm_location'),
            ])
            ->get()
            ->map(static fn ($row) => (array) $row)
            ->toArray();

        $total = count($orders);
        $enAttente = count(array_filter($orders, static fn ($order) => (int) ($order['status'] ?? 0) === 0));
        $livres = count(array_filter($orders, static fn ($order) => (int) ($order['status'] ?? 0) === 1));
        $nonLivres = count(array_filter($orders, static fn ($order) => (int) ($order['status'] ?? 0) === 3));
        $urgent = count(array_filter($orders, static fn ($order) => (int) ($order['urgent'] ?? 0) === 1));

        return view('portal.delivery_person.dashboard', [
            'firstname' => $firstname,
            'lastname' => $lastname,
            'phone' => $phone,
            'gpsForced' => $gpsForced,
            'forcedByAdmin' => $forcedByAdmin,
            'flash' => $flash,
            'flashType' => $flashType,
            'orders' => $orders,
            'total' => $total,
            'enAttente' => $enAttente,
            'livres' => $livres,
            'nonLivres' => $nonLivres,
            'urgent' => $urgent,
        ]);
    }

    public function pharmacyDashboard(Request $request)
    {
        if ($redirect = $this->requireRole('pharmacy')) return $redirect;

        $nif = (string) (session('user_id') ?: '');

        // Handle create order from pharmacy
        if ($request->isMethod('post') && $request->input('action') === 'create_order') {
            // support two incoming payload shapes:
            // 1) items[][medicine_name,quantity] (from new orders page)
            // 2) item_name[] and item_qty[] (legacy from portal view)

            if ($request->has('items')) {
                $data = $request->validate([
                    'items' => ['required', 'array', 'min:1'],
                    'items.*.medicine_name' => ['required', 'string'],
                    'items.*.quantity' => ['required', 'integer', 'min:1'],
                    'total_amount' => ['nullable', 'integer', 'min:0'],
                    'package_number' => ['nullable', 'integer', 'min:1'],
                    'is_urgent' => ['nullable'],
                ]);

                $itemNames = [];
                $itemQtys = [];
                foreach ($data['items'] as $it) {
                    $itemNames[] = $it['medicine_name'] ?? '';
                    $itemQtys[] = (int) ($it['quantity'] ?? 1);
                }

                $payload = [
                    'amount' => $data['total_amount'] ?? 0,
                    'package_number' => $data['package_number'] ?? 1,
                    'item_name' => $itemNames,
                    'item_qty' => $itemQtys,
                    'is_urgent' => !empty($data['is_urgent']) ? 1 : 0,
                    'pharmacy_id' => $nif,
                ];
            } else {
                $data = $request->validate([
                    'total_amount' => ['required', 'integer', 'min:0'],
                    'package_number' => ['required', 'integer', 'min:1'],
                    'item_name' => ['required', 'array', 'min:1'],
                    'item_name.*' => ['required', 'string'],
                    'item_qty' => ['required', 'array', 'min:1'],
                    'item_qty.*' => ['required', 'integer', 'min:1'],
                    'is_urgent' => ['nullable'],
                ]);

                $payload = [
                    'amount' => $data['total_amount'] ?? 0,
                    'package_number' => $data['package_number'] ?? 1,
                    'item_name' => $data['item_name'] ?? [],
                    'item_qty' => $data['item_qty'] ?? [],
                    'is_urgent' => !empty($data['is_urgent']) ? 1 : 0,
                    'pharmacy_id' => $nif,
                ];
            }

            try {
                $tracking = $this->portal->createStockOrder($payload);
                return redirect()->route('pharmacy.dashboard', ['section' => 'orders'])->with('success', "Order $tracking created successfully.");
            } catch (\Throwable $e) {
                return back()->withInput()->with('error', 'Could not create the order.');
            }
        }

        $data = $this->portal->pharmacyDashboard($nif);

        // Enrich orders with assigned delivery person and latest location (if available)
        $orders = $data['orders'] ?? [];
        foreach ($orders as &$o) {
            $assigned = DB::table('asined_order')->where('order_id', $o['order_id'])->first();
            $dpPhone = $assigned->deliveryperson_id ?? null;
            $o['deliveryperson_id'] = $dpPhone;
            if ($dpPhone && Schema::hasTable('delivery_location')) {
                $loc = DB::table('delivery_location')->where('PhoneNumber', $dpPhone)->first();
                $o['dp_lat'] = $loc->Latitude ?? null;
                $o['dp_lng'] = $loc->Longitude ?? null;
            } else {
                $o['dp_lat'] = null;
                $o['dp_lng'] = null;
            }

            if ($dpPhone) {
                $dp = DB::table('deliveryperson')->where('PhoneNumber', $dpPhone)->first();
                $o['dp_first'] = $dp->FirstName ?? null;
                $o['dp_last'] = $dp->LastName ?? null;
            } else {
                $o['dp_first'] = null;
                $o['dp_last'] = null;
            }
        }

        return view('portal.pharmacy.dashboard', array_merge([
            'page' => 'pharmacy',
            'userName' => $this->userName('Pharmacy'),
            'pharmacyName' => $this->userName('Pharmacy'),
            'medicineSuggestions' => $this->portal->medicineSuggestions(),
        ], $data, ['orders' => $orders]));
    }

    public function stockDashboard(Request $request)
    {
        if ($redirect = $this->requireRole('stockemployee')) return $redirect;

        if ($request->isMethod('post')) {
            if ($request->input('action') === 'assign_pharmacy') {
                $data = $request->validate([
                    'order_id' => ['required', 'string'],
                    'pharmacy_id' => ['required'],
                ]);

                try {
                    $assigned = $this->portal->assignPharmacyToOrder($data['order_id'], (string) $data['pharmacy_id']);
                    if ($assigned) {
                        return redirect()->route('stock.dashboard')->with('success', "Pharmacy assigned to order {$data['order_id']}.");
                    }
                } catch (\Throwable $exception) {
                    return back()->withInput()->with('error', 'Could not assign pharmacy to the order.');
                }

                return back()->withInput()->with('error', 'Could not assign pharmacy to the order.');
            }

            $data = $request->validate([
                'medicine_name' => ['nullable', 'string'],
                'quantity' => ['nullable', 'integer', 'min:1'],
                'amount' => ['nullable', 'integer', 'min:1'],
                'package_number' => ['required', 'integer', 'min:1'],
                'total_amount' => ['nullable', 'integer', 'min:1'],
                'item_name' => ['nullable', 'array', 'min:1'],
                'item_name.*' => ['nullable', 'string'],
                'item_qty' => ['nullable', 'array', 'min:1'],
                'item_qty.*' => ['nullable', 'integer', 'min:1'],
                'pharmacy_id' => ['nullable'],
                'is_urgent' => ['nullable'],
            ]);

            if (empty($data['medicine_name']) && empty($data['item_name'])) {
                return back()->withInput()->with('error', 'Please add at least one item to the order.');
            }

            try {
                $tracking = $this->portal->createStockOrder($data);
                return redirect()->route('stock.dashboard')->with('success', "Order $tracking created successfully.");
            } catch (\Throwable $exception) {
                return back()->withInput()->with('error', 'Could not create the order.');
            }
        }

        return view('portal.role', array_merge([
            'page' => 'stock',
            'userName' => $this->userName('Stock'),
        ], $this->portal->stockDashboard()));
    }
}
