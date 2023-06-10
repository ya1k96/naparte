<?php

namespace App;

use App\OrdenCompra;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    use SoftDeletes;

    protected $table = 'empresas';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cuit',
        'nombre',
        'img',
    ];
    public function ordenes_compra()
    {
        return $this->hasMany(OrdenCompra::class, 'empresa_id');
    }

}
