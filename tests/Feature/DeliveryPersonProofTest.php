<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class DeliveryPersonProofTest extends TestCase
{
    use RefreshDatabase;

    public function test_delivery_person_can_submit_proof_and_order_is_marked_livre()
    {
        // Create deliveryperson record
        DB::table('deliveryperson')->insert([
            'FirstName' => 'DP',
            'LastName' => 'User',
            'PhoneNumber' => '0699000001',
            'Role' => 'deliveryperson',
            'Password' => bcrypt('secret'),
        ]);

        // Create a dummy pharmacy to satisfy foreign key (optional)
        DB::table('pharmacy')->insert([
            'NIF' => '9001',
            'FirstName' => 'Pharm',
            'LastName' => 'One',
            'PhoneNumber' => '0719000000',
            'WorkTime' => '08:00 - 18:00',
            'Password' => bcrypt('pharm'),
            'Location' => 'Test',
            'Role' => 'pharmacy',
        ]);

        // Create order and assign to deliveryperson
        DB::table('order')->insert([
            'Tracking' => 'T0001',
            'QRCode' => '',
            'Date' => date('Y-m-d'),
            'otalAmount' => 100,
            'ProofImage' => '',
            'PackageNumber' => 1,
            'Status' => 0,
            'QRimage' => '',
            'IsUrgen' => 0,
        ]);

        DB::table('asined_order')->insert([
            'order_id' => 'T0001',
            'pharmacy_id' => '9001',
            'deliveryperson_id' => '0699000001',
        ]);

        // Small transparent 1x1 jpeg base64 data URI
        $dataUri = 'data:image/jpeg;base64,' . base64_encode(hex2bin('ffd8ffe000104a46494600010101006000600000ffdb00430008060607060508'
            . '0707070909080a0c140d0c0c0b0b0c19120f13180f0c0c1d1b1f1f1f13181f2328272a2a2a1f2c2f2d2a2d2b2a2a'));

        $response = $this->withSession([
            'table' => 'deliveryperson',
            'phone' => '0699000001',
        ])->post(route('delivery-person.dashboard'), [
            'action' => 'livre',
            'tracking' => 'T0001',
            'proof_image_data' => $dataUri,
        ]);

        $response->assertStatus(302);

        $proof = DB::table('order')->where('Tracking', 'T0001')->value('ProofImage');
        $status = DB::table('order')->where('Tracking', 'T0001')->value('Status');

        $this->assertNotEmpty($proof, 'ProofImage should be stored in DB');
        $this->assertEquals(1, (int) $status, 'Order status should be 1 (livre)');
        $this->assertStringContainsString('uploads/proofs/proof_T0001_', $proof);
    }
}
 