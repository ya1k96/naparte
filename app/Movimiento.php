<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Movimiento extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'pieza_id',
        'base_operacion_id',
        'inventario_id',
        'fecha',
        'cantidad',
        'balance',
        'precio_unitario',
        'ubicacion',
        'orden_compra_id',
        'orden_trabajo_id',
        'vale_id',
        'orden_transferencia_id',
        'user_id',
        'orden_compra_accion_id',
        'devolucion_detalle'
    ];

    /* TODO: Falta asociar con Ordenes de transferencia. */
    public function user() {
        return $this->belongsTo(
            User::class
        );
    }
    public function base_operacion() {
        return $this->belongsTo(
            BaseOperacion::class
        );
    }
    public function pieza() {
        return $this->belongsTo(
            Pieza::class
        );
    }
    public function orden_trabajo() {
        return $this->belongsTo(
            OrdenesTrabajo::class, 
            'orden_trabajo_id'
        );
    }
    public function vale() {
        return $this->belongsTo(
            Vale::class
        );
    }

    public function orden_compra() {
        return $this->belongsTo(OrdenCompra::class);
    }
    public function orden_compra_accion() {
        return $this->belongsTo(OrdenCompraAccion::class, 'orden_compra_accion_id');
    }
}
