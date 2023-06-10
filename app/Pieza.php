<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pieza extends Model
{
    use SoftDeletes;

    protected $table = "piezas";

    protected $fillable = [
        'nro_pieza',
        'descripcion',
        'unidad_medida_id',
        'base_operacion_id',
        'observacion',
    ];

	  protected $appends = ['id_descripcion'];

    public function getStockBase($base_operacion_id) {
      $inventario = $this->inventario()->where('bases_operacion_id', $base_operacion_id)->first();
      return $inventario? $inventario->stock_total : 0;
    }

    public function unidadMedida() {
        return $this->belongsTo(
          UnidadMedida::class
        );
    }

    public function baseOperacion() {
        return $this->belongsToMany(
          BaseOperacion::class,
          'base_operacion_pieza',
          'pieza_id',
          'base_operacion_id'
        );
    }

    /**
     * Valida que para una pieza y una base dada, no exista otro registro de inventario.
     *
     * @return bool
     */
    public function validarExisteInventario($base_operacion_id)
    {
      $existe = $this->inventario()->where('bases_operacion_id', $base_operacion_id)->first();
      if($existe) {
        return true;
      } else {
        return false;
      }
    }

    public function categorias() {
        return $this->belongsToMany(
          Categoria::class,
          'categoria_pieza',
          'pieza_id',
          'categoria_id'
        );
    }

    public function movimientos() {
      return $this->hasMany(
        Movimiento::class
      );
    }

    public function inventario() {
      return $this->hasMany(
          Inventario::class
      );
    }

    public function vale_detalle() {
      return $this->hasMany(
        ValeDetalle::class
      );
    }

    public function getIdDescripcionAttribute()
    {
      return $this->nro_pieza." - ".$this->descripcion ;

    }

    public function orden_compra() {
      return $this->hasMany(
        OrdenCompraDetalle::class, 'piezas_id'
      );
    }

    public function orden_transferencia() {
      return $this->hasMany(
        OrdenTransferenciaDetalle::class, 'piezas_id'
      );
    }
}
