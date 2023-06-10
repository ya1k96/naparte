<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrdenTransferenciaDetalle extends Model
{
    use SoftDeletes;
    
    protected $table = "orden_transferencia_detalle";
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'orden_transferencia_id',  
        'piezas_id',
        'cantidad',  
        'ingreso'       
    ];

    public function orden_transferencia() {
      return $this->belongsTo(OrdenTransferencia::class, 'orden_transferencia_id');        
    }
    public function pieza() {
      return $this->belongsTo(Pieza::class, 'piezas_id');        
    }    
}
