<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UnidadNotificacion extends Model
{
    protected $table = "unidades_notificaciones";

    protected $fillable = [
        'descripcion',
        'fecha',
        'user_id',
        'unidad_id'
    ];

    public function unidad() {
        return $this->hasOne(
          Unidad::class
        );
    }

    public function user() {
        return $this->hasOne(
          User::class
        );
    }
}
