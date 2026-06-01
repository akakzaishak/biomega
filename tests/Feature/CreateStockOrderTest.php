<?php

namespace Tests\Feature;

use App\Models\OrderItem;
use App\Models\Pharmacy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\StockEmployee;
use Illuminate\Support\Facades\Hash;

class CreateStockOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_stock_order_as_stockemployee()
    {
        $stock = StockEmployee::create([
            'FirstName' => 'Stock',
            'LastName' => 'User',
            'PhoneNumber' => '0698765432',
            'Password' => Hash::make('pass1234'),
            'Role' => 'stockemployee',
        ]);

        $payload = [
            'medicine_name' => 'MedA',
            'quantity' => 5,
            'amount' => 100,
            'package_number' => 1,
        ];

        $response = $this->withSession([
            'table' => 'stockemployee',
            'user_id' => $stock->ID,
            'phone' => $stock->PhoneNumber,
        ])->post(route('stock.dashboard'), $payload);

        $response->assertStatus(302);

        $this->assertDatabaseHas('orderitem', [
            'Name' => 'MedA',
            'contiti' => 5,
        ]);

        $this->assertDatabaseHas('order', [
            'PackageNumber' => 1,
            'otalAmount' => 100,
        ]);
    }

    public function test_create_stock_order_can_use_existing_orderitem_records(): void
    {
        $stock = StockEmployee::create([
            'FirstName' => 'Stock',
            'LastName' => 'User',
            'PhoneNumber' => '0698765433',
            'Password' => Hash::make('pass1234'),
            'Role' => 'stockemployee',
        ]);

        $item = OrderItem::create([
            'Name' => 'Existing Item',
            'contiti' => 7,
        ]);

        $pharmacy = Pharmacy::create([
            'NIF' => '9001',
            'FirstName' => 'Test',
            'LastName' => 'Pharmacy',
            'PhoneNumber' => '0719000000',
            'WorkTime' => '08:00 - 18:00',
            'Password' => Hash::make('pharmacy123'),
            'Location' => 'Algiers',
            'Role' => 'pharmacy',
        ]);

        $response = $this->withSession([
            'table' => 'stockemployee',
            'user_id' => $stock->ID,
            'phone' => $stock->PhoneNumber,
        ])->post(route('stock.dashboard'), [
            'items' => [
                [
                    'orderitem_id' => $item->ID,
                    'contiti' => 3,
                ],
            ],
            'amount' => 90,
            'package_number' => 2,
            'pharmacy_id' => $pharmacy->NIF,
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('order_item_link', [
            'orderitem_id' => $item->ID,
            'contiti' => 3,
        ]);
    }
}

 