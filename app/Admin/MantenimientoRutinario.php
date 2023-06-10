<?php

namespace App\Admin;

use Illuminate\Database\Eloquent\Model;

class MantenimientoRutinario extends Model
{
    protected $table = "mantenimiento_rutinario";

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
    ];
}
