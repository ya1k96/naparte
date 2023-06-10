<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrdenCompra extends Model
{
    use SoftDeletes;

    protected $table = "orden_compra";

    public static $_estado = [
        'abierta' => 'Abierta',
        'aprobada' => 'Aprobada',
        'parcial' => 'Parcial',
        'recibida' => 'Recibida',
        'cerrada' => 'Cerrada',
        'anulada' => 'Anulada',
    ];

    public static $_prioridad = [
        'baja' => 'Baja',
        'normal' => 'Normal',
        'alta' => 'Alta',
    ];

    public static $_acciones = [
        'cerrar' => 'Cerrar',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'proveedor_id',
        'base_emite_id',
        'base_recibe_id',
        'prioridad',
        'estado',
        'observacion',
        'nro_factura',
        'fecha_emision',
        'fecha_entrega',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }
    public function base_emite()
    {
        return $this->belongsTo(BaseOperacion::class, 'base_emite_id');
    }
    public function base_recibe()
    {
        return $this->belongsTo(BaseOperacion::class, 'base_recibe_id');
    }
    public function detalle()
    {
        return $this->hasMany(OrdenCompraDetalle::class, 'orden_compra_id');
    }
    public function accion()
    {
        return $this->hasMany(OrdenCompraAccion::class, 'orden_compra_id');
    }
}
