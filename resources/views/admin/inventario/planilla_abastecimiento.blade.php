@extends('layouts.admin-master')

@section('title')
    Ver Movimientos
@endsection

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Planilla de abastecimiento</h1>
        </div>
        <div class="section-body">
            <div class="section-title">
                <a href="{{ route('admin.inventario') }}">
                    <button type="button" class="btn btn-primary">Volver a la lista de inventarios</button>
                </a>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Filtrar por Base de operación</h4>
                        </div>
                        <div class="card-body">
                            <form action="" method="GET">
                                <div class="form-group row">
                                    <div class="col-md-4">
                                        <select name="base_operacion" id="base_operacion" class="form-control select2" data-placeholder="Filtrar por base de operación">
                                            <option label="Seleccione una base de operación" value="">Seleccione una Base de operación</option>
                                            @foreach ($bases_operacion as $base)
                                                <option value="{{$base->id}}" {{request()->get('base_operacion') && request()->get('base_operacion') == $base->id? 'selected' : ''}}>{{$base->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 float-right">
                                        <button class="btn btn-primary mr-1 add-sibling" href="#">
                                            Buscar
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @if (request()->get('base_operacion'))
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Abastecimiento</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr class="text-center">
                                            <th>Número</th>
                                            <th>Pieza</th>
                                            <th>Unidad</th>
                                            <th>Cant. Requerida</th>
                                            <th>Cant. en pedidos</th>
                                            <th>Cant. a comprar</th>
                                        </tr>
                                    </thead>
                                    <tbody >
                                        @forelse ($inventarios as $inventario)
                                        <tr class="text-center">
                                            <td>{{ $inventario->piezas->nro_pieza }}</td>
                                            <td>{{ $inventario->piezas->descripcion }}</td>
                                            <td>{{ $inventario->piezas->unidadMedida->nombre }}</td>
                                            <td>{{ $inventario->maximo_compra - $inventario->stock_total }}</td>
                                            <td>{{ $inventario->cantidad_en_pedidos }}</td>
                                            <td>{{ $inventario->maximo_compra - $inventario->stock_total - $inventario->cantidad_en_pedidos }}</td>
                                        @empty
                                            <td colspan="6" class="text-center">No se encontraron resultados</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </section>
@endsection
