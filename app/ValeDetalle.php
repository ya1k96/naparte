<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ValeDetalle extends Model
{
    protected $table = "vale_detalles";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'vale_id',
        'tarea_id',
        'pieza_id',
        'cantidad',
    ];

    public function vale() {
        return $this->belongsTo(Tarea::class, 'vale_id');
    }

    public function tarea() {
        return $this->belongsTo(Tarea::class, 'tarea_id');
    }

    public function pieza() {
        return $this->belongsTo(Pieza::class,  'pieza_id');
    }

}