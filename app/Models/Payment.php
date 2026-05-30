<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payment';
    protected $primaryKey = 'payment_id';
    public $timestamps = true;
    protected $fillable = ['order_id','amount','method','status'];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'Tracking');
    }
}
