<?php

namespace Tests\Feature;

use App\Models\AsinedOrder;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminOrderDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_delete_order()
    {
        Order::create([
            'Tracking' => 'ORD-DEL-1',
            'QRCode' => 'QR',
            'Date' => now()->toDateString(),
            'otalAmount' => 100,
            'PackageNumber' => 1,
            'Status' => 0,
            'IsUrgen' => 0,
        ]);

        AsinedOrder::create([
            'order_id' => 'ORD-DEL-1',
            'pharmacy_id' => 'PH-1',
            'deliveryperson_id' => null,
        ]);

        Payment::create([
            'order_id' => 'ORD-DEL-1',
            'amount' => 100,
            'method' => 'cash',
            'status' => 'paid',
        ]);

        $response = $this->withSession(['table' => 'admin'])->post(route('admin.orders'), [
            'action' => 'delete_order',
            'tracking' => 'ORD-DEL-1',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseMissing('order', ['Tracking' => 'ORD-DEL-1']);
        $this->assertDatabaseMissing('asined_order', ['order_id' => 'ORD-DEL-1']);
        $this->assertDatabaseMissing('payment', ['order_id' => 'ORD-DEL-1']);
    }
}