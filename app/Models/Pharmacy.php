<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pharmacy extends Model
{
    protected $table = 'pharmacy';
    protected $primaryKey = 'NIF';
    public $incrementing = false;
        protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = ['NIF','FirstName','LastName','PhoneNumber','WorkTime','Password','Location','Role'];

    public function asinedOrders()
    {
        return $this->hasMany(AsinedOrder::class, 'pharmacy_id', 'NIF');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'order_id', 'NIF');
    }
}
 