<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'order';
    protected $primaryKey = 'Tracking';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = ['QRCode','Tracking','Date','otalAmount','ProofImage','PackageNumber','Status','QRimage','IsUrgen'];

    public function asinedOrder()
    {
        return $this->hasOne(AsinedOrder::class, 'order_id', 'Tracking');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'order_id', 'Tracking');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'Tracking');
    }
}
