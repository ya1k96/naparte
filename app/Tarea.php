<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tarea extends Model
{
    use SoftDeletes;

    protected $table = "tareas";

    protected $fillable = [
        'componente_id',
        'descripcion',
        'frecuencia',
        'dias',
        'kilometros',
        'especialidad_id',
        'observaciones'
    ];

    public function componente() {
        return $this->belongsTo(
          Componente::class
        );
    }

    public function mantenimientos() {
        return $this->hasMany(
          MantenimientoRutinario::class
        );
    }

    public function piezas() {
      return $this->belongsToMany(
        Pieza::class,
        RecursoActividad::class,
        'tarea_id',
        'pieza_id'
      )
      ->withTimestamps()
      ->withPivot('unidad_id', 'cantidad');
    }

    public function especialidad() {
      return $this->belongsTo(
        Especialidad::class
      );
    }

    public function ordenes_trabajo() {
      return $this->belongsToMany(OrdenesTrabajo::class)->withPivot('comentario', 'fecha_realizacion', 'fecha_estimada');
    }

    public function personal() {
      return $this->belongsToMany(Personal::class);
    }

    public function getShowPersonalAttribute() {
      return $this->personal->modelKeys();
    }

    public function vale_detalle() {
      return $this->hasMany(
          ValeDetalle::class
      );
  }
}
