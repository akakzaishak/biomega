<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryManager extends Model
{
    protected $table = 'deliverymanager';
    protected $primaryKey = 'ID';
    public $timestamps = false;
    protected $fillable = ['FirstName','LastName','PhoneNumber','Password','Role'];
}
 