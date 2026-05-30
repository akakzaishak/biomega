<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PortalDemoSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $demoOrders = [
            'BMP-DEMO-001' => [
                'QRCode' => 'QR-BMP-DEMO-001',
                'Date' => $now->toDateString(),
                'otalAmount' => 125.50,
                'ProofImage' => '',
                'PackageNumber' => 2,
                'Status' => 0,
                'QRimage' => '',
                'IsUrgen' => 1,
            ],
        ];

        $users = [
            'admin' => [
                'table' => 'admin',
                'lookup' => ['PhoneNumber' => '0710000001'],
                'data' => ['FirstName' => 'Demo', 'LastName' => 'Admin', 'Password' => Hash::make('admin123')],
            ],
            'pharmacy' => [
                'table' => 'pharmacy',
                'lookup' => ['NIF' => '1001'],
                'data' => [
                    'FirstName' => 'Demo',
                    'LastName' => 'Pharmacy',
                    'PhoneNumber' => '0710000002',
                    'WorkTime' => '08:00 - 18:00',
                    'Password' => Hash::make('pharmacy123'),
                    'Location' => 'Algiers',
                    'Role' => 'pharmacy',
                ],
            ],
            'commercialservice' => [
                'table' => 'commercialservice',
                'lookup' => ['PhoneNumber' => '0710000003'],
                'data' => ['FirstName' => 'Demo', 'LastName' => 'Commercial', 'Password' => Hash::make('commercial123'), 'Role' => 'commercialservice'],
            ],
            'deliverymanager' => [
                'table' => 'deliverymanager',
                'lookup' => ['PhoneNumber' => '0710000004'],
                'data' => ['FirstName' => 'Demo', 'LastName' => 'Manager', 'Password' => Hash::make('manager123'), 'Role' => 'deliverymanager'],
            ],
            'deliveryperson' => [
                'table' => 'deliveryperson',
                'lookup' => ['PhoneNumber' => '0710000005'],
                'data' => ['FirstName' => 'Demo', 'LastName' => 'Driver', 'Password' => Hash::make('driver123'), 'Role' => 'deliveryperson'],
            ],
            'stockemployee' => [
                'table' => 'stockemployee',
                'lookup' => ['PhoneNumber' => '0710000006'],
                'data' => ['FirstName' => 'Demo', 'LastName' => 'Stock', 'Password' => Hash::make('stock123'), 'Role' => 'stockemployee'],
            ],
        ];

        foreach ($users as $user) {
            DB::table($user['table'])->updateOrInsert($user['lookup'], $user['data']);
        }

        foreach ($demoOrders as $tracking => $order) {
            DB::table('order')->updateOrInsert(['Tracking' => $tracking], $order);
        }

        DB::table('asined_order')->updateOrInsert(
            ['order_id' => 'BMP-DEMO-001'],
            ['pharmacy_id' => '1001', 'deliveryperson_id' => 1]
        );

        DB::table('orderitem')->updateOrInsert(
            ['Name' => 'Demo Medicine'],
            ['contiti' => 12]
        );

        DB::table('payment')->updateOrInsert(
            ['payment_id' => 1],
            [
                'order_id' => 'BMP-DEMO-001',
                'amount' => 125.50,
                'method' => 'cash',
                'status' => 'paid',
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );
    }
}