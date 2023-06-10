<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventario extends Model
{
    use SoftDeletes;

    protected $table = "inventarios";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'bases_operacion_id',
        'pieza_id',
        'compra_unica',
        'stock',
        'precio',
        'ubicacion',
        'maximo_compra',
        'minimo_compra'
    ];

    protected $appends = [
        'stock_total',
        'cantidad_en_pedidos',
        'last_price'
      ];

    public function base_operacion() {
        return $this->belongsTo(
            BaseOperacion::class,
            'bases_operacion_id'
        );
    }

    public function piezas() {
        return $this->belongsTo(
          Pieza::class,
          'pieza_id'
        );
    }

    public function movimientos() {
        return $this->hasMany(
            Movimiento::class,
            'inventario_id'
        );
    }

    public function getCantidadEnPedidosAttribute() {
        $cantidad = 0;
        if (!empty($this->piezas->orden_compra)){
            foreach($this->piezas->orden_compra as $oc) {
                if ($oc->orden_compra != null) {
                    if ($oc->orden_compra->base_recibe_id == $this->bases_operacion_id) {
                        //FIXME: CAMBIAR! cuando se cambie la forma en la que se guardan los estados en OC, validar el estado de la OC pendiente.
                        if ($oc->orden_compra->estado == 'parcial' || $oc->orden_compra->estado == 'aprobada') {
                            $cantidad += $oc->cantidad;
                        }
                    }
                }
            }
        }
        if (!empty($this->piezas->orden_transferencia)){
            foreach($this->piezas->orden_transferencia as $ot) {
                if ($ot->orden_transferencia != null) {

                    if ($ot->orden_transferencia->base_destino_id == $this->bases_operacion_id) {
                        //FIXME: CAMBIAR! cuando se cambie la forma en la que se guardan los estados en ot, validar el estado de la ot pendiente.
                        if ($ot->orden_transferencia->estado == 'parcial' || $ot->orden_transferencia->estado == 'aprobada') {
                            $cantidad += $ot->cantidad;
                        }
                    }
                }
            }
        }
        return $cantidad;
    }

    public function getStockTotalAttribute() {
        $total = $this->stock;
        if($this->movimientos) {
            //Tiene movimientos, tengo que calcular el total entre negativos y positivos.
            foreach($this->movimientos as $movimiento) {
                switch ($movimiento) {
                    case $movimiento->orden_trabajo_id != null && $movimiento->vale_id != null; //Movimiento negativo
                        $total -= $movimiento->cantidad;
                        break;
                    case $movimiento->orden_transferencia_id != null; //Movimiento negativo
                        if($movimiento->balance == '-')
                            $total -= $movimiento->cantidad;
                        else
                            $total += $movimiento->cantidad;

                        break;
                    case $movimiento->orden_compra_id != null; //Movimiento positivo
                        $total += $movimiento->cantidad;
                        break;
                    default:
                        $total += $movimiento->cantidad; //Cuando se crea directamente desde el ABM de inventarios o se devuelve desde un vale.
                        break;
                }
            }
        }
        return $total;
    }

    public function getLastPriceAttribute() {
        $ultimo_precio = $this->precio;
        if ($this->movimientos && !empty($this->movimientos)) {
            // if ($this->id == 40) dd($this->movimientos);
            foreach ($this->movimientos as $movimiento) {
                if ($movimiento->orden_compra_id != null) {
                    $ultimo_precio = $movimiento->precio_unitario;
                }
                if ($movimiento->orden_compra_id == null && $movimiento->orden_transferencia_id != null) {
                    $ultimo_precio = $movimiento->precio_unitario;
                }
            }
        }
        return $ultimo_precio;
    }
}
