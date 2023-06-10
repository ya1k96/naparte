<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlanUnidad extends Model
{
  use SoftDeletes;
    protected $table = "plan_unidad";

    protected $fillable = [
        'plan_id',
        'unidad_id',
        'km_inicial',
        'fecha',
        'estimativo'
    ];

    public function plan() {
        return $this->belongsTo(
          Plan::class
        );
    }

    public function unidad() {
        return $this->belongsTo(
          Unidad::class
        );
    }
}
