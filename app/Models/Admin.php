<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $table = 'admin';
    protected $primaryKey = 'ID';
    public $timestamps = false;
    protected $fillable = ['FirstName','LastName','PhoneNumber','Password','Role'];
}
 