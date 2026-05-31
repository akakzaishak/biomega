<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Admin;
use App\Models\Pharmacy;
use App\Models\CommercialService;
use App\Models\DeliveryManager;
use App\Models\DeliveryPerson;
use App\Models\StockEmployee;
use Illuminate\Support\Facades\Hash;
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
            'admin' => 'PhoneNumber',
            'pharmacy' => 'PhoneNumber',
            'commercialservice' => 'PhoneNumber',
            'deliverymanager' => 'PhoneNumber',
            'deliveryperson' => 'PhoneNumber',
            'stockemployee' => 'PhoneNumber',
        ];

        foreach ($map as $table => $phoneColumn) {
            $user = DB::table($table)->where($phoneColumn, $phone)->first();
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
            throw new RuntimeException('Une pharmacie avec ce NIF existe déjà.');
        }

        if (Pharmacy::where('PhoneNumber', $data['phone'])->exists()) {
            throw new RuntimeException('Ce numéro de téléphone est déjà enregistré.');
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

    public function adminOrderDashboardOrders(): array
    {
        return DB::table('order as o')
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
            ->map(fn ($row) => (array) $row)
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

    public function assignDeliveryPersonToOrder(string $orderId, ?string $pharmacyId, string $phone): bool
    {
        $existing = AsinedOrder::where('order_id', $orderId)->first();

        if ($existing) {
            $existing->deliveryperson_id = $phone;
            if ($pharmacyId !== null && $pharmacyId !== '') {
                $existing->pharmacy_id = $pharmacyId;
            }

            return (bool) $existing->save();
        }

        return (bool) AsinedOrder::create([
            'order_id' => $orderId,
            'pharmacy_id' => $pharmacyId,
            'deliveryperson_id' => $phone,
        ]);
    }

    public function updateOrderAmount(string $orderId, int $amount): bool
    {
        return (bool) DB::table('order')
            ->where('Tracking', $orderId)
            ->update(['otalAmount' => $amount]);
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

    public function forceDeliveryGps(string $phone, string $adminName): bool
    {
        $now = now();

        if (!Schema::hasTable('delivery_location')) {
            return false;
        }

        return (bool) DB::table('delivery_location')->updateOrInsert(
            ['PhoneNumber' => $phone],
            [
                'Latitude' => 0,
                'Longitude' => 0,
                'Status' => 0,
                'GpsForced' => 1,
                'ForcedAt' => $now,
                'ForcedByAdmin' => $adminName,
                'UpdatedAt' => $now,
            ]
        );
    }

    public function deliveryLocationHistory(): array
    {
        if (!Schema::hasTable('delivery_location_history')) {
            return [];
        }

        return DB::table('delivery_location_history')
            ->where('UpdatedAt', '>=', now()->subHours(8))
            ->orderBy('PhoneNumber')
            ->orderBy('UpdatedAt')
            ->get(['PhoneNumber', 'Latitude', 'Longitude', 'UpdatedAt'])
            ->map(function ($row) {
                $item = (array) $row;
                return [
                    'PhoneNumber' => $item['PhoneNumber'] ?? null,
                    'lat' => (float) ($item['Latitude'] ?? 0),
                    'lng' => (float) ($item['Longitude'] ?? 0),
                    'time' => $item['UpdatedAt'] ?? null,
                ];
            })
            ->groupBy('PhoneNumber')
            ->toArray();
    }

    public function assignedOrdersByDriver(): array
    {
        if (!Schema::hasTable('asined_order') || !Schema::hasTable('order')) {
            return [];
        }

        $orders = DB::table('asined_order as ao')
            ->join('order as o', 'ao.order_id', '=', 'o.Tracking')
            ->leftJoin('pharmacy as p', 'ao.pharmacy_id', '=', 'p.NIF')
            ->where('o.Status', 0)
            ->orderByDesc('o.IsUrgen')
            ->orderBy('ao.deliveryperson_id')
            ->select([
                'ao.deliveryperson_id',
                'ao.order_id',
                'o.otalAmount',
                'o.IsUrgen',
                'o.Status',
                DB::raw('"p"."FirstName" AS "ph_first"'),
                DB::raw('"p"."LastName" AS "ph_last"'),
                'p.Location',
            ])
            ->get()
            ->map(fn ($row) => (array) $row)
            ->toArray();

        $grouped = [];
        foreach ($orders as $order) {
            $grouped[$order['deliveryperson_id']][] = $order;
        }

        return $grouped;
    }

    public function trackingDashboardData(): array
    {
        if (Schema::hasTable('delivery_location')) {
            $deliveryPersons = DB::table('deliveryperson as d')
                ->leftJoin('delivery_location as l', 'd.PhoneNumber', '=', 'l.PhoneNumber')
                ->leftJoin('asined_order as ao', 'd.PhoneNumber', '=', 'ao.deliveryperson_id')
                ->leftJoin('order as o', function ($join) {
                    $join->on('ao.order_id', '=', 'o.Tracking')
                        ->where('o.Status', '=', 0);
                })
                ->groupBy(
                    'd.ID',
                    'd.FirstName',
                    'd.LastName',
                    'd.PhoneNumber',
                    'l.Latitude',
                    'l.Longitude',
                    'l.UpdatedAt',
                    'l.Status',
                    'l.GpsForced',
                    'l.ForcedAt',
                    'l.ForcedByAdmin'
                )
                ->orderByDesc('l.UpdatedAt')
                ->select([
                    'd.ID',
                    'd.FirstName',
                    'd.LastName',
                    'd.PhoneNumber',
                    'l.Latitude',
                    'l.Longitude',
                    'l.UpdatedAt',
                    DB::raw('"l"."Status" AS "OnlineStatus"'),
                    'l.GpsForced',
                    'l.ForcedAt',
                    'l.ForcedByAdmin',
                    DB::raw('COUNT(DISTINCT "ao"."ID") AS "ActiveOrders"'),
                ])
                ->get()
                ->map(fn ($row) => (array) $row)
                ->toArray();
        } else {
            // Fallback: return delivery persons without location columns
            $deliveryPersons = 
                \App\Models\DeliveryPerson::orderBy('ID')
                ->get(['ID', 'FirstName', 'LastName', 'PhoneNumber'])
                ->map(fn($d) => array_merge($d->toArray(), [
                    'Latitude' => null,
                    'Longitude' => null,
                    'UpdatedAt' => null,
                    'OnlineStatus' => 0,
                    'GpsForced' => 0,
                    'ForcedAt' => null,
                    'ForcedByAdmin' => null,
                    'ActiveOrders' => 0,
                ]))
                ->toArray();
        }

        $routeHistory = $this->deliveryLocationHistory();
        $assignedOrders = $this->assignedOrdersByDriver();

        $stats = [
            'totalDP' => count($deliveryPersons),
            'onlineDP' => count(array_filter($deliveryPersons, fn ($driver) =>
                !empty($driver['Latitude']) && !empty($driver['UpdatedAt']) && strtotime($driver['UpdatedAt']) > time() - 600
            )),
        ];

        $stats['offlineDP'] = $stats['totalDP'] - $stats['onlineDP'];
        $stats['forcedCount'] = count(array_filter($deliveryPersons, fn ($driver) => !empty($driver['GpsForced'])));

        $palette = ['#0060a8', '#186a22', '#b45309', '#7c3aed', '#be123c', '#0891b2', '#d97706', '#059669'];
        $colors = [];
        foreach ($deliveryPersons as $index => $driver) {
            $colors[$driver['PhoneNumber']] = $palette[$index % count($palette)];
        }

        return [
            'deliveryPersons' => $deliveryPersons,
            'routeHistory' => $routeHistory,
            'assignedOrders' => $assignedOrders,
            'stats' => $stats,
            'dpColors' => $colors,
        ];
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

    public function adminPharmaciesData(string $search = ''): array
    {
        $query = DB::table('pharmacy')
            ->select(['NIF', 'FirstName', 'LastName', 'PhoneNumber', 'WorkTime', 'Location'])
            ->orderBy('NIF', 'asc');

        $search = trim($search);
        if ($search !== '') {
            $like = '%' . $search . '%';
            $tokens = array_values(array_filter(preg_split('/\s+/', $search) ?: []));

            $query->where(function ($q) use ($like, $tokens) {
                $q->whereRaw('CAST(NIF AS TEXT) LIKE ?', [$like])
                    ->orWhere('FirstName', 'like', $like)
                    ->orWhere('LastName', 'like', $like)
                    ->orWhereRaw("LOWER(COALESCE(FirstName, '') || ' ' || COALESCE(LastName, '')) LIKE LOWER(?)", [$like])
                    ->orWhere('PhoneNumber', 'like', $like)
                    ->orWhere('Location', 'like', $like);

                foreach ($tokens as $token) {
                    $tokenLike = '%' . $token . '%';
                    $q->orWhereRaw('CAST(NIF AS TEXT) LIKE ?', [$tokenLike])
                        ->orWhere('FirstName', 'like', $tokenLike)
                        ->orWhere('LastName', 'like', $tokenLike)
                        ->orWhereRaw("LOWER(COALESCE(FirstName, '') || ' ' || COALESCE(LastName, '')) LIKE LOWER(?)", [$tokenLike])
                        ->orWhere('PhoneNumber', 'like', $tokenLike)
                        ->orWhere('Location', 'like', $tokenLike);
                }
            });
        }

        $pharmacies = $query->get()->map(fn ($row) => (array) $row)->toArray();

        $rawSuggestions = [];
        $allPharmacies = DB::table('pharmacy')
            ->select(['NIF', 'FirstName', 'LastName', 'PhoneNumber', 'Location'])
            ->orderBy('NIF', 'asc')
            ->get();

        foreach ($allPharmacies as $row) {
            $rawSuggestions[] = (string) ($row->NIF ?? '');
            $rawSuggestions[] = trim(((string) ($row->FirstName ?? '')) . ' ' . ((string) ($row->LastName ?? '')));
            $rawSuggestions[] = (string) ($row->FirstName ?? '');
            $rawSuggestions[] = (string) ($row->LastName ?? '');
            $rawSuggestions[] = (string) ($row->PhoneNumber ?? '');
            $rawSuggestions[] = (string) ($row->Location ?? '');
        }

        $searchSuggestions = array_values(array_unique(array_filter(array_map(
            static fn ($value) => trim((string) $value),
            $rawSuggestions
        ))));

        $orderData = [];
        foreach ($pharmacies as $pharmacy) {
            $nif = (string) ($pharmacy['NIF'] ?? '');

            $rows = DB::table('asined_order as ao')
                ->leftJoin('order as o', 'ao.order_id', '=', 'o.Tracking')
                ->where('ao.pharmacy_id', $nif)
                ->orderByDesc('o.Date')
                ->select([
                    'ao.order_id',
                    'ao.deliveryperson_id',
                    'o.Status',
                    'o.Date',
                    'o.IsUrgen',
                    'o.Tracking',
                ])
                ->get()
                ->map(fn ($row) => (array) $row)
                ->toArray();

            $orderIds = array_values(array_filter(array_map(
                static fn (array $order) => (string) ($order['order_id'] ?? $order['Tracking'] ?? ''),
                $rows
            )));

            $itemsByOrder = [];
            if (!empty($orderIds)) {
                foreach (DB::table('orderitem')
                    ->whereIn('order_id', $orderIds)
                    ->orderBy('Name')
                    ->get() as $itemRow) {
                    $item = (array) $itemRow;
                    $itemsByOrder[(string) ($item['order_id'] ?? '')][] = [
                        'Name' => (string) ($item['Name'] ?? ''),
                        'contiti' => (int) ($item['contiti'] ?? 0),
                    ];
                }
            }

            $rows = array_map(static function (array $order) use ($itemsByOrder) {
                $orderId = (string) ($order['order_id'] ?? $order['Tracking'] ?? '');
                $order['items'] = $itemsByOrder[$orderId] ?? [];

                return $order;
            }, $rows);

            $orderData[$nif] = [
                'total' => count($rows),
                'orders' => $rows,
                'delivered' => count(array_filter($rows, fn ($r) => (int) ($r['Status'] ?? -1) === 1)),
                'pending' => count(array_filter($rows, fn ($r) => (int) ($r['Status'] ?? -1) === 0)),
                'urgent' => count(array_filter($rows, fn ($r) => (int) ($r['IsUrgen'] ?? 0) === 1)),
            ];
        }

        return [
            'pharmacies' => $pharmacies,
            'order_data' => $orderData,
            'query' => $search,
            'search_suggestions' => $searchSuggestions,
        ];
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
                DB::beginTransaction();

                try {
                    if ($modelClass === DeliveryPerson::class) {
                        $employeePhone = (string) ($employee->PhoneNumber ?? $phone);
                        $employeeId = $employee->ID ?? $id;

                        DB::table('asined_order')
                            ->where('deliveryperson_id', $employeePhone)
                            ->orWhere('deliveryperson_id', $employeeId)
                            ->update(['deliveryperson_id' => null]);
                    }

                    $deleted = (bool) $employee->delete();
                    DB::commit();

                    return $deleted;
                } catch (\Throwable $exception) {
                    DB::rollBack();
                    throw $exception;
                }
            }
        }

        return false;
    }

    public function trackingRoutes(): array
    {
        return $this->trackingDashboardData()['assignedOrders'];
    }

    public function inventoryItems(): array
    {
        return OrderItem::selectRaw('Name, SUM(contiti) as qty')->groupBy('Name')->orderByDesc('qty')->limit(10)->get()->map(fn($r) => (array) $r)->toArray();
    }

    public function medicineSuggestions(): array
    {
        if (!Schema::hasTable('orderitem')) {
            return [];
        }

        // Return id, name and contiti to match legacy view expectations
        return OrderItem::query()
            ->select(['ID', 'Name', 'contiti'])
            ->orderBy('Name')
            ->limit(250)
            ->get()
            ->map(fn($r) => [
                'id' => $r->ID ?? null,
                'name' => $r->Name ?? null,
                'contiti' => (int) ($r->contiti ?? 0),
            ])
            ->toArray();
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
        $assignments = AsinedOrder::with(['order','pharmacy'])
            ->get()
            ->map(function ($a) {
                return [
                    'order_id' => $a->order_id,
                    'Date' => $a->order?->Date ?? null,
                    'IsUrgen' => $a->order?->IsUrgen ?? null,
                    'Status' => $a->order?->Status ?? null,
                    'pharmacy_first' => $a->pharmacy?->FirstName ?? null,
                    'pharmacy_last' => $a->pharmacy?->LastName ?? null,
                ];
            })
            ->sortByDesc(fn($r) => $r['Date'] ?? null)
            ->values()
            ->toArray();

        $drivers = DeliveryPerson::orderBy('ID')->get(['ID','FirstName','LastName','PhoneNumber'])->map(fn($d) => (array) $d)->toArray();

        return [
            'driversCount' => DeliveryPerson::count(),
            'activeCount' => Order::where('Status', 0)->count(),
            'urgentCount' => Order::where('IsUrgen', 1)->count(),
            'completedCount' => Order::where('Status', 1)->count(),
            'assignments' => $assignments,
            'driverRows' => $drivers,
        ];
    }

    public function deliveryPersonDashboard(string $identity): array
    {
        $routes = AsinedOrder::with(['order','pharmacy'])->orderByDesc('ID')->get()->map(fn($a) => [
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
                ->orderByDesc('ID')
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
        $pendingOrders = DB::table('order as o')
            ->leftJoin('asined_order as ao', 'ao.order_id', '=', 'o.Tracking')
            ->leftJoin('pharmacy as p', 'p.NIF', '=', 'ao.pharmacy_id')
            ->where('o.Status', 0)
            ->orderByDesc('o.Date')
            ->orderByDesc('o.IsUrgen')
            ->select([
                'o.*',
                'ao.pharmacy_id',
                'ao.deliveryperson_id',
                'ao.ID as ao_id',
                'p.FirstName as p_first',
                'p.LastName as p_last',
                'p.Location as p_loc',
            ])
            ->get()
            ->map(fn ($row) => (array) $row)
            ->toArray();

        return [
            'pendingOrders' => $pendingOrders,
            'pharmacies' => Pharmacy::orderBy('NIF', 'asc')
                ->get(['NIF', 'FirstName', 'LastName', 'Location', 'PhoneNumber'])
                ->map(fn ($pharmacy) => (array) $pharmacy)
                ->toArray(),
            'dbItems' => OrderItem::orderBy('Name', 'asc')
                ->get(['ID', 'Name', 'contiti'])
                ->map(fn ($item) => (array) $item)
                ->toArray(),
        ];
    }

    public function createStockOrder(array $data): string
    {
        $tracking = 'BMP-' . strtoupper(substr(md5(uniqid('', true)), 0, 8)) . '-' . now()->format('Ymd');
        DB::beginTransaction();

        try {

            $amount = (int) ($data['amount'] ?? $data['total_amount'] ?? 0);

            // Create the order record
            Order::create([
                'QRCode' => 'QR-' . $tracking,
                'Tracking' => $tracking,
                'Date' => now()->toDateString(),
                'otalAmount' => $amount,
                'ProofImage' => '',
                'PackageNumber' => $data['package_number'] ?? 1,
                'Status' => 0,
                'QRimage' => '',
                'IsUrgen' => !empty($data['is_urgent']) ? 1 : 0,
            ]);

            // If incoming payload uses legacy items[][] format (orderitem_id & contiti), persist to order_item_link
            if (!empty($data['items']) && is_array($data['items'])) {
                foreach ($data['items'] as $it) {
                    $orderitemId = (int) ($it['orderitem_id'] ?? 0);
                    $qty = (int) ($it['contiti'] ?? 0);
                    if ($orderitemId <= 0 || $qty <= 0) continue;

                    DB::table('order_item_link')->insert([
                        'orderitem_id' => $orderitemId,
                        'pharmacy_id' => $data['pharmacy_id'] ?? null,
                        'contiti' => $qty,
                    ]);
                }
            } else {
                // Fallback to current behavior: accept item_name/item_qty or medicine_name/quantity
                $items = [];

                if (!empty($data['medicine_name'])) {
                    $items[] = [
                        'name' => (string) $data['medicine_name'],
                        'quantity' => (int) ($data['quantity'] ?? 1),
                    ];
                }

                if (!empty($data['item_name']) && is_array($data['item_name'])) {
                    $quantities = $data['item_qty'] ?? [];
                    foreach ($data['item_name'] as $index => $name) {
                        $name = trim((string) $name);
                        if ($name === '') continue;
                        $items[] = [
                            'name' => $name,
                            'quantity' => max(1, (int) ($quantities[$index] ?? 1)),
                        ];
                    }
                }

                if ($items === []) {
                    throw new RuntimeException('At least one item is required.');
                }

                foreach ($items as $item) {
                    OrderItem::create([
                        'order_id' => $tracking,
                        'Name' => $item['name'],
                        'contiti' => $item['quantity'],
                    ]);
                }
            }

            if (!empty($data['pharmacy_id'])) {
                AsinedOrder::updateOrCreate(
                    ['order_id' => $tracking],
                    [
                        'pharmacy_id' => $data['pharmacy_id'],
                        'deliveryperson_id' => null,
                    ]
                );
            }

            DB::commit();
        } catch (\Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }

        return $tracking;
    }

    public function assignPharmacyToOrder(string $tracking, string $pharmacyId): bool
    {
        if ($tracking === '' || $pharmacyId === '') {
            return false;
        }

        AsinedOrder::updateOrCreate(
            ['order_id' => $tracking],
            [
                'pharmacy_id' => $pharmacyId,
                'deliveryperson_id' => null,
            ]
        );

        return true;
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
                        'order_id' => $tracking,
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

        DB::beginTransaction();

        try {
            DB::table('asined_order')->where('pharmacy_id', $nif)->delete();
            $deleted = (bool) $pharmacy->delete();
            DB::commit();

            return $deleted;
        } catch (\Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }
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
            OrderItem::where('order_id', $tracking)->delete();
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