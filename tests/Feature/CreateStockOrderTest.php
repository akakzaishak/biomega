<?php

namespace Tests\Feature;

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

        $response->assertRedirect(route('stock.dashboard'));

        $this->assertDatabaseHas('orderitem', [
            'Name' => 'MedA',
            'contiti' => 5,
        ]);

        $this->assertDatabaseHas('order', [
            'PackageNumber' => 1,
            'otalAmount' => 100,
        ]);
    }
}
