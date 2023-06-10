<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Kilometro extends Model
{
    protected $table = "kilometros";

    protected $fillable = [
        'cantidad'
    ];
}
