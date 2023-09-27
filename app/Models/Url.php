<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Url extends Model
{
    protected $connection = 'mongodb';
    protected $fillable = ['url'];

}
