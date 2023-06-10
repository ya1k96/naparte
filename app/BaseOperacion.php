<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BaseOperacion extends Model
{
    protected $table = 'bases_operaciones';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nombre',
    ];

    public function unidades() {
        return $this->hasMany(
            Unidad::class
        );
    }

    public function piezas() {
        return $this->belongsToMany(
          Pieza::class
        );
    }
}
