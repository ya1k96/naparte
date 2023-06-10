<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HistorialOrdenesTrabajo extends Model
{
    protected $table = 'historial_ordenes_trabajo';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'base_operacion_id',
        'status',
        'fecha',
    ];

    public function orden_trabajo() {
        return $this->belongsTo(
            OrdenesTrabajo::class
        );
    }
    public function user() {
        return $this->belongsTo(
            User::class
        );
    }
}
