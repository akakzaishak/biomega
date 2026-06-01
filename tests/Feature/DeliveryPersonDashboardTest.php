<?php

namespace Tests\Feature;

use App\Models\AsinedOrder;
use App\Models\DeliveryPerson;
use App\Models\Order;
use App\Models\Pharmacy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DeliveryPersonDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_delivery_person_can_mark_order_delivered_with_proof(): void
    {
        $driver = DeliveryPerson::create([
            'FirstName' => 'Driver',
            'LastName' => 'One',
            'PhoneNumber' => '0799000000',
            'Password' => Hash::make('pass1234'),
            'Role' => 'deliveryperson',
        ]);

        $pharmacy = Pharmacy::create([
            'NIF' => 'PH-500',
            'FirstName' => 'Pharmacy',
            'LastName' => 'One',
            'PhoneNumber' => '0711000000',
            'WorkTime' => '08:00 - 18:00',
            'Password' => Hash::make('pharmacy123'),
            'Location' => 'Algiers',
            'Role' => 'pharmacy',
        ]);

        Order::create([
            'Tracking' => 'ORD-DP-1',
            'QRCode' => 'QR-ORD-DP-1',
            'Date' => now()->toDateString(),
            'otalAmount' => 150,
            'PackageNumber' => 2,
            'Status' => 0,
            'IsUrgen' => 0,
        ]);

        AsinedOrder::create([
            'order_id' => 'ORD-DP-1',
            'pharmacy_id' => $pharmacy->NIF,
            'deliveryperson_id' => $driver->PhoneNumber,
        ]);

        $response = $this->withSession([
            'table' => 'deliveryperson',
            'phone' => $driver->PhoneNumber,
            'firstname' => $driver->FirstName,
            'lastname' => $driver->LastName,
        ])->post(route('delivery-person.dashboard'), [
            'tracking' => 'ORD-DP-1',
            'action' => 'livre',
            'proof_image_data' => '',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('order', [
            'Tracking' => 'ORD-DP-1',
            'Status' => 1,
        ]);
    }
}
 