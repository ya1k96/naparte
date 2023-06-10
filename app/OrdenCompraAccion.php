<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrdenCompraAccion extends Model
{
    protected $table = "orden_compra_accion";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'orden_compra_id',
        'tipo',
        'observacion',
        'user_id'
    ];

    public function orden_compra() {
        return $this->belongsTo(OrdenCompra::class, 'orden_compra_id');        
      }
    public function user() {
        return $this->belongsTo(
            User::class
        );
    }

    public function movimiento(){
        return $this->hasMany(Movimiento::class, 'orden_compra_accion_id');
    }
}
