<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Especialidad extends Model
{
    protected $table = "especialidades";

    protected $fillable = ['nombre'];

    public function personal() {
        return $this->hasMany(
            Personal::class
        );
    } 
    
    public function orden_trabajo() {
      return $this->belongsToMany(
          OrdenesTrabajo::class,
          'ordenes_trabajo_especialidad',
      );
  }     

    public function tareas() {
        return $this->hasMany(
        Tarea::class
        );
    }
}
