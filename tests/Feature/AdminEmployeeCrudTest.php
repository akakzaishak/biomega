<?php

namespace Tests\Feature;

use App\Models\CommercialService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminEmployeeCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_delete_employee()
    {
        $employee = CommercialService::create([
            'FirstName' => 'Delete',
            'LastName' => 'Me',
            'PhoneNumber' => '0777777777',
            'Password' => 'x',
            'Role' => 'commercialservice',
        ]);

        $response = $this->withSession(['table' => 'admin'])->post(route('admin.employees'), [
            'action' => 'delete_employee',
            'del_table' => 'commercialservice',
            'del_id' => $employee->ID,
            'del_phone' => $employee->PhoneNumber,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseMissing('commercialservice', [
            'ID' => $employee->ID,
        ]);
    }
}