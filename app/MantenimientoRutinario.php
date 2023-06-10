<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MantenimientoRutinario extends Model
{
    protected $table = "mantenimiento_rutinario";
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'unidad_id',
        'componente_id',
        'tarea_id',
        'ult_mantenimiento',
        'ult_mantenimiento_fecha',
        'frecuencia',
        'frecuencia_dias',
        'prox_mantenimiento',
        'prox_mantenimiento_fecha',
        'mantenimiento_modif',
        'mantenimiento_modif_fecha',
        'estado',
        'orden_trabajo_id',
        'created_at',
    ];

    public function tarea() {
        return $this->belongsTo(
          Tarea::class
        );
    }

    public function unidad() {
        return $this->belongsTo(
          Unidad::class
        );
    }

    public function orden_trabajo() {
      return $this->belongsTo(
        OrdenesTrabajo::class
      );
    }
}
