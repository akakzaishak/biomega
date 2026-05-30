<?php

namespace Tests\Feature;

use App\Models\AsinedOrder;
use App\Models\DeliveryPerson;
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

    public function test_admin_can_delete_delivery_person_and_release_assignments()
    {
        $employee = DeliveryPerson::create([
            'FirstName' => 'Driver',
            'LastName' => 'One',
            'PhoneNumber' => '0888888888',
            'Password' => 'x',
            'Role' => 'deliveryperson',
        ]);

        AsinedOrder::create([
            'order_id' => 'ORD-DP-1',
            'pharmacy_id' => 'PH-1',
            'deliveryperson_id' => $employee->PhoneNumber,
        ]);

        $response = $this->withSession(['table' => 'admin'])->post(route('admin.employees'), [
            'action' => 'delete_employee',
            'del_table' => 'deliveryperson',
            'del_id' => $employee->ID,
            'del_phone' => $employee->PhoneNumber,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseMissing('deliveryperson', [
            'ID' => $employee->ID,
        ]);
        $this->assertDatabaseHas('asined_order', [
            'order_id' => 'ORD-DP-1',
            'pharmacy_id' => 'PH-1',
            'deliveryperson_id' => null,
        ]);
    }
}