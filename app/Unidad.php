<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unidad extends Model
{
    use SoftDeletes;

    protected $table = 'unidades';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        // 'tipo_unidad',
        'num_interno',
        'modelo_id',
        'num_serie',
        'num_motor',
        'dominio',
        'carroceria_id',
        'cantidad_asientos',
        'aire_acondicionado_id',
        'puesta_servicio',
        'tipo_vehiculo_id',
        'motor',
        'base_operacion_id',
        'desactivado',
        'observaciones',
    ];

    public function modelo() {
        return $this->belongsTo(
            Modelo::class
        );
    }

    public function carroceria() {
        return $this->belongsTo(
            Carroceria::class
        );
    }

    public function aire_acondicionado() {
        return $this->belongsTo(
            AireAcondicionado::class
        );
    }

    public function tipo_vehiculo() {
        return $this->belongsTo(
            TiposVehiculo::class
        );
    }

    public function base_operacion() {
        return $this->belongsTo(
            BaseOperacion::class
        );
    }

    public function planes() {
        return $this->hasMany(
          Plan::class
        );
    }

    public function ordenes_trabajo() {
        return $this->hasMany(
            OrdenesTrabajo::class
        );
    }

    public function vinculaciones() {
        return $this->hasMany(
          PlanUnidad::class
        );
    }

    public function historiales() {
        return $this->hasMany(
          HistorialUnidad::class
        );
    }

    public function mantenimientos() {
        return $this->hasMany(
          MantenimientoRutinario::class
        );
    }
}
