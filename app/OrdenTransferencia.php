<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Movimiento;

class OrdenTransferencia extends Model
{
    use SoftDeletes;
    
    protected $table = "orden_transferencia";

    public static $_estado = [
        // 'abierta' => 'Abierta',
        'aprobada' => 'Aprobada',
        'parcial' => 'Parcial',
        'recibida' => 'Recibida',
        'cerrada' => 'Cerrada',
        'cancelada' => 'Cancelada',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fecha_emision',
        'base_origen_id',
        'base_destino_id',  
        'estado',
        'observacion',
        'solicitado_nombre',        
        'entregado_nombre',        
    ];

    public function base_origen() {
      return $this->belongsTo(BaseOperacion::class, 'base_origen_id');        
    }
    public function base_destino() {
      return $this->belongsTo(BaseOperacion::class, 'base_destino_id');        
    }    
    public function detalle(){
      return $this->hasMany(OrdenTransferenciaDetalle::class, 'orden_transferencia_id');
    }    
    public function accion()
    {
      return $this->hasMany(OrdenTransferenciaAccion::class, 'orden_transferencia_id');
    }

    /**
     * Función que buscar el último precio de compra de una pieza
     *
     * @param int $pieza_id
     * @param int $base_operacion_id
     * @return int
     */
    public static function getLastPriceOrdenCompra($pieza_id, $base_operacion_id)
    {
      $precio_pieza = 0;
      $movimiento = Movimiento::where(['pieza_id' => $pieza_id, 'base_operacion_id' => $base_operacion_id])->orderBy('id', 'desc')->first();
      if ($movimiento) {
        $precio_pieza = $movimiento->precio_unitario;
      }
      return $precio_pieza;
    }
}
