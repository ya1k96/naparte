<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrdenTransferenciaAccion extends Model
{
    protected $table = "orden_transferencia_accion";

    public static $_estado = [
        'cerrada' => 'Cerrada',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'orden_transferencia_id',
        'tipo',
        'observacion',
        'user_id'
    ];

    public function orden_transferencia() {
        return $this->belongsTo(OrdenTransferencia::class, 'orden_transferencia_id');        
      }
    public function user() {
        return $this->belongsTo(
            User::class
        );
    }
}
