<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Pharmacy;
use Illuminate\Support\Facades\Hash;

class RegisterPharmacyTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_pharmacy_stores_record()
    {
        $payload = [
            'nif' => '123456789',
            'firstname' => 'Test',
            'lastname' => 'Pharm',
            'phone' => '0612345678',
            'worktime' => '9-17',
            'password' => 'secret123',
            'confirm' => 'secret123',
            'location' => 'Center',
            'wilaya' => '01 - Adrar',
        ];

        $response = $this->post(route('register.pharmacy'), $payload);

        $response->assertRedirect();

        $this->assertDatabaseHas('pharmacy', [
            'NIF' => '123456789',
            'PhoneNumber' => '0612345678',
        ]);

        $ph = Pharmacy::find('123456789');
        $this->assertTrue(Hash::check('secret123', $ph->Password));
    }

    public function test_register_pharmacy_stays_on_register_page_with_success_message()
    {
        $payload = [
            'nif' => '987654321',
            'firstname' => 'New',
            'lastname' => 'Pharm',
            'phone' => '0698765432',
            'worktime' => '8-16',
            'password' => 'secret123',
            'confirm' => 'secret123',
            'location' => 'West',
            'wilaya' => '02 - Chlef',
        ];

        $response = $this->post(route('register.pharmacy'), $payload);

        $response->assertRedirect(route('register.pharmacy'));
        $response->assertSessionHas('success');
    }
}
