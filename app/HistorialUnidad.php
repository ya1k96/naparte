<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HistorialUnidad extends Model
{
    protected $table = "historiales_unidades";

    protected $fillable = [
        'unidad_id',
        'kilometraje',
        'orden_trabajo_id',
        'created_at'
    ];

    public function unidad() {
        return $this->belongsTo(Unidad::class);
    }

    public function orden_trabajo() {
        return $this->belongsTo(OrdenesTrabajo::class);
    }

    public static function getValidacionMeses()
    {
        return date('Y-m-01', strtotime(date('Y-m-d H:i:s').' -3months'));
    }
}
