<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryPerson extends Model
{
    protected $table = 'deliveryperson';
    protected $primaryKey = 'ID';
    public $timestamps = false;
    protected $fillable = ['FirstName','LastName','PhoneNumber','Password','Role'];

    public function assignedOrders()
    {
        return $this->hasMany(AsinedOrder::class, 'deliveryperson_id', 'ID');
    }
}
