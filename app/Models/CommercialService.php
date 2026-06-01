<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommercialService extends Model
{
    protected $table = 'commercialservice';
    protected $primaryKey = 'ID';
    public $timestamps = false;
    protected $fillable = ['FirstName','LastName','PhoneNumber','Password','Role'];
}
 