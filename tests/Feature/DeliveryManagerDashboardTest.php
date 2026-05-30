<?php

namespace Tests\Feature;

use App\Models\DeliveryManager;
use App\Models\DeliveryPerson;
use App\Models\Order;
use App\Models\Pharmacy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DeliveryManagerDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_delivery_manager_can_assign_order_and_update_amount(): void
    {
        $manager = DeliveryManager::create([
            'FirstName' => 'Manager',
            'LastName' => 'User',
            'PhoneNumber' => '0777000000',
            'Password' => Hash::make('pass1234'),
            'Role' => 'deliverymanager',
        ]);

        Pharmacy::create([
            'NIF' => 'PH-100',
            'FirstName' => 'Pharmacy',
            'LastName' => 'One',
            'PhoneNumber' => '0711000000',
            'WorkTime' => '08:00 - 18:00',
            'Password' => Hash::make('pharmacy123'),
            'Location' => 'Algiers',
            'Role' => 'pharmacy',
        ]);

        $driver = DeliveryPerson::create([
            'FirstName' => 'Driver',
            'LastName' => 'One',
            'PhoneNumber' => '0799000000',
            'Password' => Hash::make('pass1234'),
            'Role' => 'deliveryperson',
        ]);

        Order::create([
            'Tracking' => 'ORD-DM-1',
            'QRCode' => 'QR-ORD-DM-1',
            'Date' => now()->toDateString(),
            'otalAmount' => 100,
            'PackageNumber' => 1,
            'Status' => 0,
            'IsUrgen' => 0,
        ]);

        $response = $this->withSession([
            'table' => 'deliverymanager',
            'user_id' => $manager->ID,
            'phone' => $manager->PhoneNumber,
            'firstname' => $manager->FirstName,
            'lastname' => $manager->LastName,
        ])->post(route('delivery-manager.dashboard'), [
            'action' => 'assign',
            'order_id' => 'ORD-DM-1',
            'pharmacy_id' => 'PH-100',
            'dp_phone' => $driver->PhoneNumber,
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('asined_order', [
            'order_id' => 'ORD-DM-1',
            'pharmacy_id' => 'PH-100',
            'deliveryperson_id' => $driver->PhoneNumber,
        ]);

        $response = $this->withSession([
            'table' => 'deliverymanager',
            'user_id' => $manager->ID,
            'phone' => $manager->PhoneNumber,
            'firstname' => $manager->FirstName,
            'lastname' => $manager->LastName,
        ])->post(route('delivery-manager.dashboard'), [
            'action' => 'update_amount',
            'order_id' => 'ORD-DM-1',
            'amount' => 250,
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('order', [
            'Tracking' => 'ORD-DM-1',
            'otalAmount' => 250,
        ]);
    }
}
