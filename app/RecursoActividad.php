<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;
class RecursoActividad extends Pivot
{
    //Esta tabla vincula las piezas y las tareas
    protected $table = 'recursos_actividades';
    public $incrementing = true;
    public $timestamps = true;
    protected $fillable = [
        'unidad_id',
        'tarea_id',
        'pieza_id',
        'cantidad',
    ];

    public function unidad() {
        return $this->hasMany(Unidad::class, 'id' ,'unidad_id');
    }

    public function tarea() {
        return $this->hasMany(Tarea::class, 'id' ,'tarea_id');
    }

    public function pieza() {
        return $this->hasMany(Pieza::class, 'id' , 'pieza_id');
    }
    
}
