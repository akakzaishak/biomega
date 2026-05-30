<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PortalDemoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('admin')->updateOrInsert(
            ['PhoneNumber' => '0710000001'],
            [
                'FirstName' => 'Demo',
                'LastName' => 'Admin',
                'Password' => Hash::make('admin123'),
                'Role' => 'admin',
            ]
        );

        DB::table('pharmacy')->updateOrInsert(
            ['NIF' => '1001'],
            [
                'FirstName' => 'Demo',
                'LastName' => 'Pharmacy',
                'PhoneNumber' => '0710000002',
                'WorkTime' => '08:00 - 18:00',
                'Password' => Hash::make('pharmacy123'),
                'Location' => 'Algiers',
                'Role' => 'pharmacy',
            ]
        );

        DB::table('commercialservice')->updateOrInsert(
            ['PhoneNumber' => '0710000003'],
            [
                'FirstName' => 'Demo',
                'LastName' => 'Commercial',
                'Password' => Hash::make('commercial123'),
                'Role' => 'commercialservice',
            ]
        );

        DB::table('deliverymanager')->updateOrInsert(
            ['PhoneNumber' => '0710000004'],
            [
                'FirstName' => 'Demo',
                'LastName' => 'Manager',
                'Password' => Hash::make('manager123'),
                'Role' => 'deliverymanager',
            ]
        );

        DB::table('deliveryperson')->updateOrInsert(
            ['PhoneNumber' => '0710000005'],
            [
                'FirstName' => 'Demo',
                'LastName' => 'Driver',
                'Password' => Hash::make('driver123'),
                'Role' => 'deliveryperson',
            ]
        );

        DB::table('stockemployee')->updateOrInsert(
            ['PhoneNumber' => '0710000006'],
            [
                'FirstName' => 'Demo',
                'LastName' => 'Stock',
                'Password' => Hash::make('stock123'),
                'Role' => 'stockemployee',
            ]
        );

        DB::table('order')->updateOrInsert(
            ['Tracking' => 'BMP-DEMO-001'],
            [
                'QRCode' => 'QR-BMP-DEMO-001',
                'Date' => now()->toDateString(),
                'otalAmount' => 125.50,
                'ProofImage' => '',
                'PackageNumber' => 2,
                'Status' => 0,
                'QRimage' => '',
                'IsUrgen' => 1,
            ]
        );

        DB::table('asined_order')->updateOrInsert(
            ['order_id' => 'BMP-DEMO-001'],
            [
                'pharmacy_id' => '1001',
                'deliveryperson_id' => 1,
            ]
        );

        DB::table('orderitem')->updateOrInsert(
            ['Name' => 'Demo Medicine'],
            [
                'contiti' => 12,
            ]
        );

        DB::table('payment')->updateOrInsert(
            ['payment_id' => 1],
            [
                'order_id' => 'BMP-DEMO-001',
                'amount' => 125.50,
                'method' => 'cash',
                'status' => 'paid',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
} 
