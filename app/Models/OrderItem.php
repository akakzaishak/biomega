<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $table = 'orderitem';
    public $timestamps = false;
    protected $fillable = ['Name','contiti'];
}
