<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Order;

class AdminOrderActionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_mark_order_complete()
    {
        Order::create([
            'Tracking' => 'ORD-TEST-1',
            'QRCode' => 'QR',
            'Date' => now()->toDateString(),
            'otalAmount' => 100,
            'PackageNumber' => 1,
            'Status' => 0,
            'IsUrgen' => 0,
        ]);

        $response = $this->withSession(['table' => 'admin'])->post(route('admin.orders.complete', 'ORD-TEST-1'));

        $response->assertRedirect();

        $this->assertDatabaseHas('order', ['Tracking' => 'ORD-TEST-1', 'Status' => 1]);
    }
}
 