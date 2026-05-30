<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Pharmacy;

class AdminPharmacyCrudTest extends TestCase
{
    use RefreshDatabase;

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

        $response = $this->withSession(['table' => 'admin'])->post(route('admin.pharmacies.delete', 'DELTEST'));

        $response->assertRedirect();

        $this->assertDatabaseMissing('pharmacy', ['NIF' => 'DELTEST']);
    }
}
