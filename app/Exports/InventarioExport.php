<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Inventario;
use App\Pieza;
use App\BaseOperacion;

class InventarioExport implements FromQuery, WithMapping, WithHeadings
{
    use Exportable;

    public function __construct($base, $pieza, $fecha_hasta, $ubicacion)
    {
        $this->base = $base;
        $this->pieza = $pieza;
        $this->fecha_hasta = $fecha_hasta;
        $this->ubicacion = $ubicacion;
    }

    public function map($item): array
    {
        return [
            $item->id,
            $item->piezas->id_descripcion,
            $item->piezas->nro_pieza,
            $item->base_operacion->nombre,
            $item->compra_unica ?? '-',
            (string) $item->stock_total,
            (string) $item->precio,
            $item->ubicacion,
            $item->maximo_compra ?? '-',
            $item->minimo_compra ?? '-'
        ];
    }

    public function headings(): array {
        return [
            'id',
            'Pieza',
            'Numero',
            'Pañol',
            'Compra unica',
            'Stock',
            'Precio',
            'Ubicacion',
            'Maximo compra',
            'Minimo compra'
        ];
    }

    public function query()
    {
          $inventarios = Inventario::withTrashed()
        ->with(['piezas', 'base_operacion', 'movimientos']);

        $bases_operaciones = BaseOperacion::all();
        $piezas = Pieza::all();

        if (!empty($this->ubicacion)){
            //Filtra por ubicacion
            $inventarios = $inventarios->where('ubicacion', 'LIKE', '%'.$this->ubicacion.'%');
        }

        if (!empty($this->base)){
            $inventarios = $inventarios->whereHas('base_operacion', function ($q) {
                return $q->where('bases_operacion_id', $this->base);
                });
        }

        if (!empty($this->pieza)){
            $inventarios = $inventarios->whereHas('piezas', function ($q) {
                return $q->where('pieza_id', $this->pieza);
                });
        }

        if (!empty($this->fecha_hasta)){
            $inventarios = $inventarios->with(['movimientos' => function ($q) {
                return $q->whereDate('fecha', '<=', $this->fecha_hasta);
                }]);
        }

        $buscar = $this->ubicacion ?? "";
        $buscar_base_operacion = $this->base ?? "";
        $buscar_pieza = $this->pieza ?? "";
        $buscar_fecha_hasta = $this->fecha_hasta ?? "";

        if (request('order') && request('direction')) {
            $inventarios = $inventarios->orderBy(request('order'), request('direction'));
        }else {
            $inventarios = $inventarios->orderBy('ubicacion','asc');
        }

        return $inventarios;
    }

}
