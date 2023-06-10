<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TiposVehiculo extends Model
{
    protected $table = 'tipos_vehiculos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nombre',
    ];

    public function unidades() {
        return $this->hasMany(
            Unidad::class
        );
    }
}
