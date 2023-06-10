<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dia extends Model
{
    protected $table = "dias";

    protected $fillable = [
        'cantidad'
    ];
}
