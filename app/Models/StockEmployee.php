<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockEmployee extends Model
{
    protected $table = 'stockemployee';
    protected $primaryKey = 'ID';
    public $timestamps = false;
    protected $fillable = ['FirstName','LastName','PhoneNumber','Password','Role'];
}
