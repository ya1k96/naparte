<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AireAcondicionado extends Model
{
    protected $table = 'aires_acondicionados';

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

}
