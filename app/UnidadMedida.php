<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Pieza;

class UnidadMedida extends Model
{
    protected $table = "unidades_medidas";

    protected $fillable = [
        'nombre'
    ];

    public function piezas() {
        return $this->hasMany(
          Pieza::class
        );
    }
}
