<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrdenCompraDetalle extends Model
{
    use SoftDeletes;
    
    protected $table = "orden_compra_detalle";
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'orden_compra_id',  
        'piezas_id',
        'cantidad',
        'costo',  
        'ingreso'      
    ];

    protected $appends = [
      'monto',
    ];

    public function orden_compra() {
      return $this->belongsTo(OrdenCompra::class, 'orden_compra_id');        
    }
    public function pieza() {
      return $this->belongsTo(Pieza::class, 'piezas_id');        
    }    

    public function getMontoAttribute() {
      return $this->cantidad * $this->costo;
    }
}
