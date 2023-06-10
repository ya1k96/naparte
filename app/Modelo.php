<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Modelo extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nombre',
        'marca_id'
    ];

    public function marca() {
        return $this->belongsTo(
            Marca::class
        );
    }

    public function unidades() {
        return $this->hasMany(
            Unidad::class
        );
    }
}
