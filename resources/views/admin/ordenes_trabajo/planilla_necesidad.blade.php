@extends('layouts.admin-master')

@section('title')
Planilla de Necesidad
@endsection

@section('content')
<section class="section">
  <div class="section-header">
    <h1>Planilla de Necesidad</h1>
  </div>
  <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="col-6">
                            <h4 class="col-6">Planilla de Necesidad</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="" method="GET">
                            @csrf
                            <div class="row">
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="base_operaciones" class="mx-2">Base de Operación</label>
                                        <select name="base_operaciones" class="form-control select2" data-placeholder="Seleccionar una base de Operación">
                                            <option value="">Seleccionar</option>
                                            @foreach ($bases as $base)
                                                <option value="{{ $base->id }}" {{(request()->query('base_operaciones') != null && request()->query('base_operaciones') == $base->id) ? 'selected' : ''}}>{{ $base->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="fecha" class="mx-2">Fecha</label>
                                        <input type="text" value="{{ $fecha_hoy }}" name="fecha" class="form-control" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-3">
                                    <div class="form-group">
                                        <label for="dias" class="mx-2">Cantidad de días a abastecer</label>
                                        <select name="dias" class="form-control select2 m-3" data-placeholder="Seleccionar un período">
                                            <option value="">Seleccionar</option>
                                            @foreach ($dias as $dia=>$nombre)
                                            <option value="{{ $dia }}" {{request()->query('dias') == $dia ? 'selected' : ''}} {{((request()->query('dias')) == null && ($dia == 15))? 'selected' : ''}}>{{ $dia . " días" }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="input-group d-flex flex-row-reverse">
                                    <div class="input-group-btn d-flex flex-row">
                                        <button class="btn btn-primary btn-icon">
                                            Calcular
                                        </button>
                                        <a href="{{ route('admin.ordenes-trabajo.planillaNecesidad') }}" class="btn btn-lighty btn-icon">
                                            <i class="fas fa-redo"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @if (!empty($base_operacion))
            @if (!empty($listado_piezas))
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive table-invoice">
                                <table class="table table-striped">
                                    <tbody>
                                        @if(!empty($listado_piezas))
                                        <tr class="text-center">
                                            <th>N° de Pieza</th>
                                            <th>Descripción</th>
                                            <th>Unidad</th>
                                            <th>Cant. Requerida</th>
                                        </tr>
                                        @foreach($listado_piezas as $pieza)
                                        {{-- {{dd($pieza)}} --}}
                                            <tr class="text-center">
                                                <td>{{$pieza['nro_pieza']}}</td>
                                                <td>{{$pieza['descripcion']}}</td>
                                                <td>{{$pieza['unidad']}}</td>
                                                <td>{{$pieza['cantidad']}}</td>
                                            </tr>
                                        @endforeach
                                        @else
                                        <tr>
                                            <td colspan="3">No se encontraron Piezas</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <span>No se encontraron registros</span>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        @endif

    </div>
</section>
@endsection