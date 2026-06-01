<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AsinedOrder extends Model
{
    protected $table = 'asined_order';
    protected $primaryKey = 'ID';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;
    protected $fillable = ['order_id','pharmacy_id','deliveryperson_id'];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'Tracking');
    }

    public function pharmacy()
    {
        return $this->belongsTo(Pharmacy::class, 'pharmacy_id', 'NIF');
    }

    public function deliveryPerson()
    {
        return $this->belongsTo(DeliveryPerson::class, 'deliveryperson_id', 'PhoneNumber');
    }
}
 