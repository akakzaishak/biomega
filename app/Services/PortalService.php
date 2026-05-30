<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Models\Pharmacy;
use App\Models\CommercialService;
use App\Models\DeliveryManager;
use App\Models\DeliveryPerson;
use App\Models\StockEmployee;
use App\Models\Order;
use App\Models\AsinedOrder;
use App\Models\OrderItem;
use App\Models\Payment;
use RuntimeException;

class PortalService
{
    private function attributeValue(array $attributes, array $keys, mixed $default = null): mixed
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $attributes) && $attributes[$key] !== null && $attributes[$key] !== '') {
                return $attributes[$key];
            }

            $lowerKey = strtolower($key);
            foreach ($attributes as $attributeKey => $attributeValue) {
                if (strtolower((string) $attributeKey) === $lowerKey && $attributeValue !== null && $attributeValue !== '') {
                    return $attributeValue;
                }
            }
        }

        return $default;
    }

    private function rows(string $sql, array $bindings = []): array
    {
        return array_map(static fn ($row) => (array) $row, DB::select($sql, $bindings));
    }

    private function row(string $sql, array $bindings = []): array
    {
        return (array) (DB::selectOne($sql, $bindings) ?? []);
    }

    private function count(string $sql, array $bindings = []): int
    {
        $row = $this->row($sql, $bindings);

        return (int) ($row['cnt'] ?? 0);
    }

    public function wilayas(): array
    {
        return [
            '01 - Adrar', '02 - Chlef', '03 - Laghouat', '04 - Oum El Bouaghi', '05 - Batna',
            '06 - Béjaïa', '07 - Biskra', '08 - Béchar', '09 - Blida', '10 - Bouira',
            '11 - Tamanrasset', '12 - Tébessa', '13 - Tlemcen', '14 - Tiaret', '15 - Tizi Ouzou',
            '16 - Alger', '17 - Djelfa', '18 - Jijel', '19 - Sétif', '20 - Saïda',
            '21 - Skikda', '22 - Sidi Bel Abbès', '23 - Annaba', '24 - Guelma', '25 - Constantine',
            '26 - Médéa', '27 - Mostaganem', '28 - M\'Sila', '29 - Mascara', '30 - Ouargla',
            '31 - Oran', '32 - El Bayadh', '33 - Illizi', '34 - Bordj Bou Arréridj', '35 - Boumerdès',
            '36 - El Tarf', '37 - Tindouf', '38 - Tissemsilt', '39 - El Oued', '40 - Khenchela',
            '41 - Souk Ahras', '42 - Tipaza', '43 - Mila', '44 - Aïn Defla', '45 - Naâma',
            '46 - Aïn Témouchent', '47 - Ghardaïa', '48 - Relizane', '49 - El M\'Ghair', '50 - El Meniaa',
            '51 - Ouled Djellal', '52 - Bordj Baji Mokhtar', '53 - Béni Abbès', '54 - Timimoun',
            '55 - Touggourt', '56 - Djanet', '57 - In Salah', '58 - In Guezzam',
        ];
    }

    public function authenticateByPhone(string $phone): ?array
    {

        $map = [
            'admin' => Admin::class,
            'pharmacy' => Pharmacy::class,
            'commercialservice' => CommercialService::class,
            'deliverymanager' => DeliveryManager::class,
            'deliveryperson' => DeliveryPerson::class,
            'stockemployee' => StockEmployee::class,
        ];

        foreach ($map as $table => $modelClass) {
            $user = $modelClass::where('PhoneNumber', $phone)->first();
            if ($user) {
                return [
                    'table' => $table,
                    'user' => $user,
                    'redirect' => match ($table) {
                        'admin' => route('admin.dashboard'),
                        'pharmacy' => route('pharmacy.dashboard'),
                        'commercialservice' => route('commercial.dashboard'),
                        'deliverymanager' => route('delivery-manager.dashboard'),
                        'deliveryperson' => route('delivery-person.dashboard'),
                        'stockemployee' => route('stock.dashboard'),
                        default => route('home'),
                    },
                ];
            }
        }

        return null;
    }

    public function createPharmacy(array $data): void
    {
        if (Pharmacy::where('NIF', $data['nif'])->exists()) {
            throw new RuntimeException('This NIF already exists.');
        }

        if (Pharmacy::where('PhoneNumber', $data['phone'])->exists()) {
            throw new RuntimeException('This phone number already exists.');
        }

        Pharmacy::create([
            'NIF' => $data['nif'],
            'FirstName' => $data['firstname'],
            'LastName' => $data['lastname'],
            'PhoneNumber' => $data['phone'],
            'WorkTime' => $data['worktime'] ?? null,
            'Password' => Hash::make($data['password']),
            'Location' => $data['location'] ?? null,
        ]);
    }

    public function adminCommon(): array
    {
        $totalOrders = Order::count();
        $delivered = Order::where('Status', 1)->count();
        $pending = Order::where('Status', 0)->count();
        $pharmacies = Pharmacy::count();
        $employees = CommercialService::count() + DeliveryManager::count() + DeliveryPerson::count() + StockEmployee::count();
        $deliveryPersons = DeliveryPerson::count();
        $unassigned = DB::table('order as o')
            ->leftJoin('asined_order as a', 'o.Tracking', '=', 'a.order_id')
            ->whereNull('a.order_id')
            ->count();

        return [
            'ordersCount' => $totalOrders,
            'totalOrders' => $totalOrders,
            'pharmaciesCount' => $pharmacies,
            'totalPharmacies' => $pharmacies,
            'employeesCount' => $employees,
            'totalEmployees' => $employees,
            'urgentCount' => Order::where('IsUrgen', 1)->count(),
            'deliveredCount' => $delivered,
            'pendingCount' => $pending,
            'deliveredPct' => $totalOrders > 0 ? round(($delivered / $totalOrders) * 100) : 0,
            'pendingPct' => $totalOrders > 0 ? 100 - round(($delivered / $totalOrders) * 100) : 0,
            'dpCount' => $deliveryPersons,
            'unassignedCount' => $unassigned,
        ];
    }

    public function recentEmployees(): array
    {
        $employeeTables = [
            'commercialservice',
            'deliverymanager',
            'deliveryperson',
            'stockemployee',
        ];

        $recentEmployees = [];
        foreach ($employeeTables as $table) {
            $rows = DB::table($table)
                ->select(['FirstName', 'LastName', 'PhoneNumber', 'Role'])
                ->orderByDesc('ID')
                ->limit(2)
                ->get()
                ->map(function ($row) use ($table) {
                    $item = (array) $row;
                    $item['source'] = $table;
                    return $item;
                })
                ->toArray();

            $recentEmployees = array_merge($recentEmployees, $rows);
        }

        return array_slice($recentEmployees, 0, 5);
    }

    public function recentOrders(): array
    {
        return Order::orderBy('Date', 'desc')
            ->limit(6)
            ->get(['Tracking', 'Date', 'otalAmount', 'Status', 'IsUrgen'])
            ->map(fn ($order) => $order->toArray())
            ->toArray();
    }

    public function orders(): array
    {
        $orders = Order::with(['asinedOrder.pharmacy'])->orderBy('Date','desc')->get();

        return $orders->map(function ($o) {
            $arr = (array) $o->toArray();
            $arr['pharmacy_id'] = $o->asinedOrder?->pharmacy_id ?? null;
            $arr['pharmacy_first'] = $o->asinedOrder?->pharmacy?->FirstName ?? null;
            $arr['pharmacy_last'] = $o->asinedOrder?->pharmacy?->LastName ?? null;
            $arr['pharmacy_location'] = $o->asinedOrder?->pharmacy?->Location ?? null;
            return $arr;
        })->toArray();
    }

    public function pharmacies(): array
    {
        return Pharmacy::orderBy('NIF', 'asc')
            ->get()
            ->map(fn ($pharmacy) => $pharmacy->toArray())
            ->toArray();
    }

    public function employees(): array
    {
        $employees = [];

        $map = [
            [CommercialService::class, 'Commercial', 'commercialservice'],
            [DeliveryManager::class, 'Delivery Manager', 'deliverymanager'],
            [DeliveryPerson::class, 'Delivery Person', 'deliveryperson'],
            [StockEmployee::class, 'Stock Employee', 'stockemployee'],
        ];

        foreach ($map as [$model, $label, $tableName]) {
            foreach ($model::get(['ID','FirstName','LastName','PhoneNumber','Role']) as $row) {
                $arr = $row->getAttributes();
                $id = $this->attributeValue($arr, ['ID', 'id']);
                $firstName = trim((string) $this->attributeValue($arr, ['FirstName', 'firstname', 'first_name'], ''));
                $lastName = trim((string) $this->attributeValue($arr, ['LastName', 'lastname', 'last_name'], ''));
                $phoneNumber = (string) $this->attributeValue($arr, ['PhoneNumber', 'phonenumber', 'phone_number'], '');
                $role = (string) $this->attributeValue($arr, ['Role', 'role'], $label);

                $arr = (array) $arr;

                $arr['source'] = $label;
                $arr['source_table'] = $tableName;
                $arr['employee_id'] = $id;
                $arr['employee_first_name'] = $firstName;
                $arr['employee_last_name'] = $lastName;
                $arr['employee_name'] = trim($firstName . ' ' . $lastName);
                $arr['employee_phone'] = $phoneNumber;
                $arr['employee_role'] = $role;
                $arr['full_name'] = $arr['employee_name'];
                $arr['display_role'] = $label;
                $employees[] = $arr;
            }
        }

        return $employees;
    }

    public function deliveryDrivers(): array
    {
        return DeliveryPerson::orderBy('ID', 'asc')
            ->get(['ID', 'FirstName', 'LastName', 'PhoneNumber'])
            ->map(fn ($driver) => $driver->toArray())
            ->toArray();
    }

    public function pharmacyOrderCounts(): array
    {
        return DB::table('pharmacy as p')
            ->leftJoin('asined_order as a', 'a.pharmacy_id', '=', 'p.NIF')
            ->selectRaw('p.NIF, p.FirstName, p.LastName, p.PhoneNumber, p.WorkTime, p.Location, COUNT(a.order_id) as total_orders')
            ->groupBy('p.NIF', 'p.FirstName', 'p.LastName', 'p.PhoneNumber', 'p.WorkTime', 'p.Location')
            ->orderBy('p.NIF', 'asc')
            ->get()
            ->map(fn ($row) => (array) $row)
            ->toArray();
    }

    public function createEmployee(string $role, array $data): void
    {
        $tableMap = [
            'commercialservice' => CommercialService::class,
            'deliverymanager' => DeliveryManager::class,
            'deliveryperson' => DeliveryPerson::class,
            'stockemployee' => StockEmployee::class,
        ];

        if (!isset($tableMap[$role])) {
            throw new RuntimeException('Invalid role selected.');
        }

        $modelClass = $tableMap[$role];

        if ($modelClass::where('PhoneNumber', $data['phone'])->exists()) {
            throw new RuntimeException('This phone number already exists.');
        }

        $modelClass::create([
            'FirstName' => $data['firstname'],
            'LastName' => $data['lastname'],
            'PhoneNumber' => $data['phone'],
            'Password' => Hash::make($data['password']),
            'Role' => $role,
        ]);
    }

    public function deleteEmployee(string $role, ?int $id = null, string $phone = ''): bool
    {
        $tableMap = [
            'commercialservice' => CommercialService::class,
            'deliverymanager' => DeliveryManager::class,
            'deliveryperson' => DeliveryPerson::class,
            'stockemployee' => StockEmployee::class,
        ];

        if (!isset($tableMap[$role])) {
            $tableMap = array_values($tableMap);
        } else {
            $tableMap = [$tableMap[$role]];
        }

        foreach ($tableMap as $modelClass) {
            $employee = null;

            if ($id !== null) {
                $employee = $modelClass::where('ID', $id)->first();
            }

            if (!$employee && $phone !== '') {
                $employee = $modelClass::where('PhoneNumber', $phone)->first();
            }

            if ($employee) {
                return (bool) $employee->delete();
            }
        }

        return false;
    }

    public function trackingRoutes(): array
    {
        $assigned = AsinedOrder::with(['order','pharmacy'])->orderByDesc('id')->get();

        return $assigned->map(fn($a) => [
            'order_id' => $a->order_id,
            'pharmacy_id' => $a->pharmacy_id,
            'deliveryperson_id' => $a->deliveryperson_id,
            'Date' => $a->order?->Date ?? null,
            'IsUrgen' => $a->order?->IsUrgen ?? null,
            'Status' => $a->order?->Status ?? null,
            'pharmacy_first' => $a->pharmacy?->FirstName ?? null,
            'pharmacy_last' => $a->pharmacy?->LastName ?? null,
        ])->toArray();
    }

    public function inventoryItems(): array
    {
        return OrderItem::selectRaw('Name, SUM(contiti) as qty')->groupBy('Name')->orderByDesc('qty')->limit(10)->get()->map(fn($r) => (array) $r)->toArray();
    }

    public function reportData(): array
    {
        $rows = DB::table('order')
            ->selectRaw('Date, COUNT(*) as total, COALESCE(SUM(otalAmount), 0) as revenue')
            ->groupBy('Date')
            ->orderByDesc('Date')
            ->limit(7)
            ->get()
            ->map(fn($r) => (array) $r)
            ->toArray();

        $totals = (array) DB::table('order')->selectRaw('COUNT(*) as total, COALESCE(SUM(otalAmount), 0) as revenue')->first();

        return [
            'reportRows' => $rows,
            'reportTotals' => $totals,
        ];
    }

    public function paymentsData(): array
    {
        $paymentTable = Schema::hasTable('payment');

        return [
            'paymentTable' => $paymentTable,
            'payments' => $paymentTable ? Payment::orderByDesc('payment_id')->get()->map(fn($p) => (array) $p)->toArray() : [],
        ];
    }

    public function commercialDashboard(): array
    {
        return [
            'pharmaciesCount' => Pharmacy::count(),
            'pendingCount' => Order::where('Status', 0)->count(),
            'urgentCount' => Order::where('IsUrgen', 1)->count(),
            'orders' => Order::with('asinedOrder.pharmacy')->orderByDesc('Date')->limit(8)->get()->map(function ($o) {
                return [
                    'Tracking' => $o->Tracking,
                    'Date' => $o->Date,
                    'otalAmount' => $o->otalAmount,
                    'IsUrgen' => $o->IsUrgen,
                    'pharmacy_first' => $o->asinedOrder?->pharmacy?->FirstName ?? null,
                    'pharmacy_last' => $o->asinedOrder?->pharmacy?->LastName ?? null,
                ];
            })->toArray(),
        ];
    }

    public function deliveryManagerDashboard(): array
    {
        return [
            'driversCount' => DeliveryPerson::count(),
            'activeCount' => Order::where('Status', 0)->count(),
            'urgentCount' => Order::where('IsUrgen', 1)->count(),
            'assignments' => AsinedOrder::with(['order','pharmacy'])->get()->map(fn($a) => [
                'order_id' => $a->order_id,
                'Date' => $a->order?->Date ?? null,
                'IsUrgen' => $a->order?->IsUrgen ?? null,
                'Status' => $a->order?->Status ?? null,
                'pharmacy_first' => $a->pharmacy?->FirstName ?? null,
                'pharmacy_last' => $a->pharmacy?->LastName ?? null,
            ])->toArray(),
        ];
    }

    public function deliveryPersonDashboard(string $identity): array
    {
        $routes = AsinedOrder::with(['order','pharmacy'])->orderByDesc('id')->get()->map(fn($a) => [
            'order_id' => $a->order_id,
            'deliveryperson_id' => $a->deliveryperson_id,
            'Tracking' => $a->order?->Tracking ?? null,
            'Date' => $a->order?->Date ?? null,
            'IsUrgen' => $a->order?->IsUrgen ?? null,
            'Status' => $a->order?->Status ?? null,
            'pharmacy_first' => $a->pharmacy?->FirstName ?? null,
            'pharmacy_last' => $a->pharmacy?->LastName ?? null,
            'pharmacy_location' => $a->pharmacy?->Location ?? null,
        ])->toArray();

        if ($identity !== '') {
            $routes = array_values(array_filter($routes, static fn ($route) => (string) ($route['deliveryperson_id'] ?? '') === $identity));
        }

        return [
            'assignedCount' => count($routes),
            'urgentCount' => count(array_filter($routes, static fn ($route) => (int) ($route['IsUrgen'] ?? 0) === 1)),
            'completedCount' => count(array_filter($routes, static fn ($route) => (int) ($route['Status'] ?? 0) === 1)),
            'routes' => $routes,
        ];
    }

    public function pharmacyDashboard(string $nif): array
    {
        $pharmacy = $nif !== '' ? (Pharmacy::find($nif)?->toArray() ?? []) : [];
        $orders = [];

        if ($nif !== '') {
            $orders = AsinedOrder::with('order')
                ->where('pharmacy_id', $nif)
                ->orderByDesc('id')
                ->get()
                ->map(fn($a) => [
                    'order_id' => $a->order_id,
                    'Tracking' => $a->order?->Tracking ?? null,
                    'Date' => $a->order?->Date ?? null,
                    'Status' => $a->order?->Status ?? null,
                    'IsUrgen' => $a->order?->IsUrgen ?? null,
                    'PackageNumber' => $a->order?->PackageNumber ?? null,
                    'otalAmount' => $a->order?->otalAmount ?? null,
                ])->toArray();
        }

        return [
            'pharmacy' => $pharmacy,
            'ordersCount' => count($orders),
            'pendingCount' => count(array_filter($orders, static fn ($order) => (int) ($order['Status'] ?? 0) === 0)),
            'urgentCount' => count(array_filter($orders, static fn ($order) => (int) ($order['IsUrgen'] ?? 0) === 1)),
            'completedCount' => count(array_filter($orders, static fn ($order) => (int) ($order['Status'] ?? 0) === 1)),
            'orders' => $orders,
        ];
    }

    public function stockDashboard(): array
    {
        return [
            'pendingOrders' => DB::table('order')->where('Status', 0)->orderByDesc('Date')->limit(8)->get(['Tracking','Date','otalAmount','IsUrgen'])->map(fn($r) => (array) $r)->toArray(),
            'pharmacies' => Pharmacy::orderBy('NIF','asc')->get(['NIF','FirstName','LastName','Location'])->map(fn($p) => (array) $p)->toArray(),
        ];
    }

    public function createStockOrder(array $data): string
    {
        $tracking = 'BMP-' . strtoupper(substr(md5(uniqid('', true)), 0, 8)) . '-' . now()->format('Ymd');
        DB::beginTransaction();

        try {
            Order::create([
                'QRCode' => 'QR-' . $tracking,
                'Tracking' => $tracking,
                'Date' => now()->toDateString(),
                'otalAmount' => $data['amount'],
                'ProofImage' => '',
                'PackageNumber' => $data['package_number'],
                'Status' => 0,
                'QRimage' => '',
                'IsUrgen' => 0,
            ]);

            OrderItem::create([
                'Name' => $data['medicine_name'],
                'contiti' => $data['quantity'],
            ]);

            DB::commit();
        } catch (\Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }

        return $tracking;
    }

    public function createAdminOrder(array $data): string
    {
        $tracking = 'BMP-' . strtoupper(substr(md5(uniqid('', true)), 0, 8)) . '-' . now()->format('Ymd');

        DB::beginTransaction();

        try {
            Order::create([
                'QRCode' => 'QR-' . $tracking,
                'Tracking' => $tracking,
                'Date' => $data['date'] ?? now()->toDateString(),
                'otalAmount' => $data['amount'],
                'ProofImage' => '',
                'PackageNumber' => $data['package_number'],
                'Status' => (int) $data['status'],
                'QRimage' => '',
                'IsUrgen' => !empty($data['is_urgent']) ? 1 : 0,
            ]);

            if (!empty($data['pharmacy_id'])) {
                AsinedOrder::updateOrCreate(
                    ['order_id' => $tracking],
                    [
                        'pharmacy_id' => $data['pharmacy_id'],
                        'deliveryperson_id' => $data['deliveryperson_id'] ?? null,
                    ]
                );
            }

            // persist ordered items
            if (!empty($data['product_name']) && is_array($data['product_name'])) {
                $qtys = $data['quantity'] ?? [];
                foreach ($data['product_name'] as $idx => $pname) {
                    $q = (int) ($qtys[$idx] ?? 1);
                    if (trim((string) $pname) === '') continue;
                    OrderItem::create([
                        'Name' => $pname,
                        'contiti' => max(1, $q),
                    ]);
                }
            }

            DB::commit();
        } catch (\Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }

        return $tracking;
    }

    public function deletePharmacy(string $nif): bool
    {
        $pharmacy = Pharmacy::find($nif);
        if (!$pharmacy) return false;

        return (bool) $pharmacy->delete();
    }

    public function markOrderComplete(string $tracking): bool
    {
        $order = Order::where('Tracking', $tracking)->first();
        if (!$order) return false;

        $order->Status = 1;
        return (bool) $order->save();
    }

    public function updateOrderStatus(string $tracking, int $status): bool
    {
        $order = Order::where('Tracking', $tracking)->first();
        if (!$order) return false;

        $order->Status = $status;
        return (bool) $order->save();
    }

    public function deleteOrder(string $tracking): bool
    {
        $order = Order::where('Tracking', $tracking)->first();
        if (!$order) {
            return false;
        }

        DB::beginTransaction();

        try {
            AsinedOrder::where('order_id', $tracking)->delete();
            Payment::where('order_id', $tracking)->delete();
            $deleted = (bool) $order->delete();
            DB::commit();

            return $deleted;
        } catch (\Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }
    }
}