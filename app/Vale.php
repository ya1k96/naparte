<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vale extends Model
{
    use SoftDeletes;
    
    protected $table = "vales";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fecha', 
        'ordenes_trabajo_id',
        'cerrado'
    ];

    public function checkReabrir() {
        return $this->ordenes_trabajo->status != 'Anulada' || $this->ordenes_trabajo->status != 'Cerrada';
    }

    public function ordenes_trabajo() {
        return $this->belongsTo(
            OrdenesTrabajo::class
        );
    }

    public function vale_detalle() {
        return $this->hasMany(ValeDetalle::class,'vale_id');
    }

}
