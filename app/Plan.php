<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plan extends Model
{
  use SoftDeletes;
	protected $table = "planes";

	protected $fillable = [
		'nombre',
	];

    public function componentes() {
        return $this->hasMany(
          Componente::class
        );
    }

    public function unidades() {
        return $this->hasMany(
          Unidad::class
        );
    }

    public function vinculaciones() {
        return $this->hasMany(
          PlanUnidad::class
        );
    }
}
