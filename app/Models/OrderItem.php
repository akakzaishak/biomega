<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $table = 'orderitem';
    public $timestamps = false;
    protected $fillable = ['order_id','Name','contiti'];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'Tracking');
    }
}
