<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Componente extends Model
{
    protected $table = "componentes";

    protected $fillable = [
        'plan_id',
        'nombre',
        'componente_padre'
    ];

    public function subcomponentes() {
        return $this->hasMany(
          Componente::class, 'componente_padre'
        );
    }

    public function getShowPadreAttribute() {
      $padre = Componente::find($this->componente_padre);
      return $padre;
    }

    public function plan() {
        return $this->belongsTo(
          Plan::class
        );
    }

    public function tareas() {
        return $this->hasMany(
          Tarea::class
        );
    }
}
