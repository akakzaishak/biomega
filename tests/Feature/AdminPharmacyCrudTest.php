<?php

namespace Tests\Feature;

use App\Models\AsinedOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Pharmacy;

class AdminPharmacyCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_search_pharmacies()
    {
        Pharmacy::create([
            'NIF' => 'SEARCH1',
            'FirstName' => 'Alpha',
            'LastName' => 'Pharma',
            'PhoneNumber' => '0711111111',
            'Password' => 'x',
            'WorkTime' => '9-17',
            'Location' => 'Algiers Center',
        ]);

        Pharmacy::create([
            'NIF' => 'SEARCH2',
            'FirstName' => 'Beta',
            'LastName' => 'Health',
            'PhoneNumber' => '0722222222',
            'Password' => 'x',
            'WorkTime' => '9-17',
            'Location' => 'Oran',
        ]);

        $response = $this->withSession(['table' => 'admin'])->get(route('admin.pharmacies', ['q' => 'Alpha']));

        $response->assertOk();
        $response->assertSee('Alpha Pharma');

        $html = $response->getContent();
        $this->assertSame(1, substr_count($html, 'class="pharm-row transition-colors"'));
    }

    public function test_admin_can_delete_pharmacy()
    {
        Pharmacy::create([
            'NIF' => 'DELTEST',
            'FirstName' => 'Delete',
            'LastName' => 'Me',
            'PhoneNumber' => '0999999999',
            'Password' => 'x',
            'WorkTime' => '9-17',
            'Location' => 'Test',
        ]);

        AsinedOrder::create([
            'order_id' => 'ORD-PH-1',
            'pharmacy_id' => 'DELTEST',
            'deliveryperson_id' => null,
        ]);

        $response = $this->withSession(['table' => 'admin'])->post(route('admin.pharmacies.delete', 'DELTEST'));

        $response->assertRedirect();

        $this->assertDatabaseMissing('pharmacy', ['NIF' => 'DELTEST']);
        $this->assertDatabaseMissing('asined_order', ['pharmacy_id' => 'DELTEST']);
    }
}
