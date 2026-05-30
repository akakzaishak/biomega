<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Pharmacy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PharmacyOrderCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_pharmacy_can_create_order_and_persist_items(): void
    {
        Pharmacy::create([
            'NIF' => '1001',
            'FirstName' => 'Demo',
            'LastName' => 'Pharmacy',
            'PhoneNumber' => '0710000002',
            'WorkTime' => '08:00 - 18:00',
            'Password' => Hash::make('pharmacy123'),
            'Location' => 'Algiers',
            'Role' => 'pharmacy',
        ]);

        $response = $this->withSession([
            'table' => 'pharmacy',
            'user_id' => '1001',
            'phone' => '0710000002',
            'firstname' => 'Demo',
            'lastname' => 'Pharmacy',
        ])->post(route('pharmacy.dashboard'), [
            'action' => 'create_order',
            'items' => [
                ['medicine_name' => 'Paracetamol', 'quantity' => 2],
                ['medicine_name' => 'Ibuprofen', 'quantity' => 1],
            ],
            'total_amount' => 250,
            'package_number' => 3,
            'is_urgent' => 1,
        ]);

        $response->assertRedirect(route('pharmacy.dashboard', ['section' => 'orders']));

        $order = Order::first();
        $this->assertNotNull($order);
        $this->assertDatabaseHas('asined_order', [
            'order_id' => $order->Tracking,
            'pharmacy_id' => '1001',
        ]);
        $this->assertDatabaseHas('orderitem', [
            'order_id' => $order->Tracking,
            'Name' => 'Paracetamol',
            'contiti' => 2,
        ]);
        $this->assertDatabaseHas('orderitem', [
            'order_id' => $order->Tracking,
            'Name' => 'Ibuprofen',
            'contiti' => 1,
        ]);
    }
}
