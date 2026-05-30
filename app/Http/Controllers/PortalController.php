<?php

namespace App\Http\Controllers;

use App\Services\PortalService;
use Illuminate\Support\Facades\Hash;
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
                if (!Hash::check($password, $stored)) {
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
            $data = $request->validate([
                'nif' => ['required', 'regex:/^\d+$/'],
                'firstname' => ['required', 'string'],
                'lastname' => ['required', 'string'],
                'phone' => ['required', 'string'],
                'worktime' => ['required', 'string'],
                'password' => ['required', 'string', 'min:6'],
                'confirm' => ['required', 'same:password'],
                'location' => ['required', 'string'],
                'wilaya' => ['required', 'string'],
            ]);

            try {
                $this->portal->createPharmacy($data);
            } catch (RuntimeException $exception) {
                return back()->withInput()->with('error', $exception->getMessage());
            }

            return redirect()->route('register.pharmacy')->with('success', 'Pharmacy registered successfully.');
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

        if ($request->isMethod('post') && $request->input('action') === 'create_order') {
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

            // defaults for fields removed from the UI
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
        $orders = $this->portal->orders();

        if ($query !== '') {
            $orders = array_values(array_filter($orders, static function (array $order) use ($query) {
                $haystack = implode(' ', [
                    (string) ($order['Tracking'] ?? ''),
                    (string) ($order['QRCode'] ?? ''),
                    (string) ($order['pharmacy_first'] ?? ''),
                    (string) ($order['pharmacy_last'] ?? ''),
                    (string) ($order['pharmacy_location'] ?? ''),
                ]);

                return stripos($haystack, $query) !== false;
            }));
        }

        return view('portal.admin', array_merge(['userName' => $this->userName('Admin')], $this->portal->adminCommon(), [
            'page' => 'orders',
            'query' => $query,
            'orders' => $orders,
            'pharmacies' => $this->portal->pharmacies(),
            'products' => $this->portal->inventoryItems(),
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

    public function adminPharmacies()
    {
        if ($redirect = $this->requireRole('admin')) return $redirect;

        return view('portal.admin', array_merge(['userName' => $this->userName('Admin')], $this->portal->adminCommon(), [
            'page' => 'pharmacies',
            'pharmacies' => $this->portal->pharmacyOrderCounts(),
        ]));
    }

    public function deletePharmacy(string $nif)
    {
        if ($redirect = $this->requireRole('admin')) return $redirect;

        try {
            $deleted = $this->portal->deletePharmacy($nif);
            if ($deleted) {
                return back()->with('success', 'Pharmacy deleted.');
            }
            return back()->with('error', 'Pharmacy not found.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Could not delete pharmacy.');
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

    public function adminTracking()
    {
        if ($redirect = $this->requireRole('admin')) return $redirect;

        return view('portal.admin', array_merge(['userName' => $this->userName('Admin')], $this->portal->adminCommon(), [
            'page' => 'tracking',
            'routes' => $this->portal->trackingRoutes(),
            'drivers' => $this->portal->deliveryDrivers(),
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

    public function deliveryManagerDashboard()
    {
        if ($redirect = $this->requireRole('deliverymanager')) return $redirect;

        return view('portal.role', array_merge([
            'page' => 'delivery-manager',
            'userName' => $this->userName('Delivery Manager'),
        ], $this->portal->deliveryManagerDashboard()));
    }

    public function deliveryPersonDashboard()
    {
        if ($redirect = $this->requireRole('deliveryperson')) return $redirect;

        $identity = (string) (session('phone') ?: session('user_id') ?: '');
        return view('portal.role', array_merge([
            'page' => 'delivery-person',
            'userName' => $this->userName('Delivery'),
        ], $this->portal->deliveryPersonDashboard($identity)));
    }

    public function pharmacyDashboard()
    {
        if ($redirect = $this->requireRole('pharmacy')) return $redirect;

        $nif = (string) (session('user_id') ?: '');
        return view('portal.role', array_merge([
            'page' => 'pharmacy',
            'userName' => $this->userName('Pharmacy'),
        ], $this->portal->pharmacyDashboard($nif)));
    }

    public function stockDashboard(Request $request)
    {
        if ($redirect = $this->requireRole('stockemployee')) return $redirect;

        if ($request->isMethod('post')) {
            $data = $request->validate([
                'medicine_name' => ['required', 'string'],
                'quantity' => ['required', 'integer', 'min:1'],
                'amount' => ['required', 'integer', 'min:1'],
                'package_number' => ['required', 'integer', 'min:1'],
            ]);

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