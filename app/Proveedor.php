<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proveedor extends Model
{
    use SoftDeletes;

    protected $table = 'proveedores';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nombre',
        'cuit'
    ];

    /*
    public function modelos() {
        return $this->hasMany(
            Modelo::class
        );
    }
    */

    public function ordenes_compra()
    {
        return $this->hasMany(OrdenCompra::class, 'proveedor_id');
    }

    /**
     * Función que verifica si ya existe un CUIT en la tabla de proveedores
     *
     * @param integer $cuit
     * @return boolean
     */
    public function existeCuit (int $cuit) : bool
    {
        $proveedor = Proveedor::where('cuit', $cuit)->first();

        if ($proveedor) return true;
        return false;
    }

    /**
        * Validar que el CUIT sea correcto
        *
        * @param int $cuit
        * @return void
        */
    public function validarCuit($cuit)
    {
        $digits = [];
        if (strlen($cuit) != 11) return false;
        for ($i = 0; $i < strlen($cuit); $i++) {
            if (!ctype_digit($cuit[$i])) return false;
            if ($i < 11) {
                $digits[] = $cuit[$i];
            }   
        }
        $acum = 0;
        foreach ([5, 4, 3, 2, 7, 6, 5, 4, 3, 2] as $i => $multiplicador) {
            $acum += $digits[$i] * $multiplicador;
        }
        $cmp = 11 - ($acum % 11);
        if ($cmp == 11) $cmp = 0;
        if ($cmp == 10) $cmp = 9;
        return($cuit[10] == $cmp);
    }
}
