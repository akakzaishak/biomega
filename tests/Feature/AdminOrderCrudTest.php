<?php

namespace Tests\Feature;

use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminOrderCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_search_orders()
    {
        Order::create([
            'Tracking' => 'ORD-SEARCH-1',
            'QRCode' => 'QR-1',
            'Date' => now()->toDateString(),
            'otalAmount' => 100,
            'PackageNumber' => 1,
            'Status' => 0,
            'IsUrgen' => 0,
        ]);

        Order::create([
            'Tracking' => 'ORD-SEARCH-2',
            'QRCode' => 'QR-2',
            'Date' => now()->toDateString(),
            'otalAmount' => 200,
            'PackageNumber' => 2,
            'Status' => 1,
            'IsUrgen' => 1,
        ]);

        $response = $this->withSession(['table' => 'admin'])->get(route('admin.orders', ['q' => 'SEARCH-2']));

        $response->assertOk();
        $this->assertSame(1, substr_count($response->getContent(), 'order-row '));
        $response->assertSee('ORD-SEARCH-2');
    }

    public function test_admin_can_create_order()
    {
        $response = $this->withSession(['table' => 'admin'])->post(route('admin.orders'), [
            'action' => 'create_order',
            'amount' => 125,
            'product_name' => ['MedA', 'MedB'],
            'quantity' => [2, 1],
            'package_number' => 2,
            'status' => 0,
            'date' => now()->toDateString(),
            'is_urgent' => 1,
        ]);

        $response->assertRedirect(route('admin.orders'));

        $this->assertDatabaseCount('order', 1);

        $order = Order::first();
        $this->assertNotNull($order);
        $this->assertNotEmpty($order->Tracking);
        $this->assertMatchesRegularExpression('/^BMP-[A-Z0-9]{8}-\d{8}$/', $order->Tracking);
        $this->assertDatabaseHas('orderitem', ['Name' => 'MedA', 'contiti' => 2]);
        $this->assertDatabaseHas('orderitem', ['Name' => 'MedB', 'contiti' => 1]);
    }

    public function test_admin_can_update_order_status()
    {
        Order::create([
            'Tracking' => 'ORD-TEST-2',
            'QRCode' => 'QR',
            'Date' => now()->toDateString(),
            'otalAmount' => 100,
            'PackageNumber' => 1,
            'Status' => 0,
            'IsUrgen' => 0,
        ]);

        $response = $this->withSession(['table' => 'admin'])->post(route('admin.orders'), [
            'action' => 'update_status',
            'tracking' => 'ORD-TEST-2',
            'status' => 1,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('order', [
            'Tracking' => 'ORD-TEST-2',
            'Status' => 1,
        ]);
    }
}