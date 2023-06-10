@extends('layouts.admin-master')

@section('title')
    Editar Vale
@endsection

@section('content')
    <div class="section">
        <div class="section-header">
            <h1>Editar Vale</h1>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            {{-- <h4>{{ 'Orden de trabajo '.$orden_trabajo->tipo_orden }}</h4> --}}
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-12 col-md-1">
                                    <label for="recurso" class="col-form-label">Recurso</label>
                                </div>
                                <div class="col-12 col-md-4">
                                    <select name="recurso" id="recurso_id" class="form-control select2" data-placeholder="Filtrar por recurso">
                                        <option label="Seleccione un recurso" value="">Seleccione un recurso</option>
                                        @foreach ($recursos as $recurso)
                                            <option value="{{$recurso->id}}" data-unidad-text="{{ $recurso->unidadMedida->nombre}}">{{$recurso->nro_pieza.' - '.$recurso->descripcion}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @if ($tareas != null)
                                    <div class="col-12 col-md-3">
                                        <select name="tarea" id="tarea_id" class="form-control select2" data-placeholder="Filtrar por tarea">
                                            @foreach ($tareas as $tarea)
                                                <option value="{{$tarea->id}}" data-tarea-text="{{ $tarea->descripcion}}">{{$tarea->descripcion}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                                <div class="col-12 col-md-4">
                                    <a class="btn btn-success text-white" id="agregar-recurso-pieza" title="Agregar componente">
                                        <i class="fas fa-plus"></i> Agregar Recurso
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <form method="POST" action="{{ route('admin.vale.updateCerrar', $vale->id)}}">
                            @method('PATCH')
                            @csrf
                                <input type="hidden" name="base_operacion_id" id="base_operacion_id" value="{{ $vale->ordenes_trabajo->base_operacion_id }}">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th class="p-0 text-center">
                                                <div class="custom-checkbox custom-control">
                                                    <input class="form-check-input vale-checkbox-todos" name="vale-checkbox-todos" type="checkbox" value="0">
                                                    Recurso Utilizados
                                                </div>
                                            </th>
                                            <th>Recurso</th>
                                            @if ($vale->vale_detalle != null)
                                                <th>Actividad</th>
                                            @endif
                                            <th>Cantidad</th>
                                            <th>Unidad de Medida</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tabla_listado_recursos">
                                        @foreach ($vale->vale_detalle as $k => $detalle)
                                            @php
                                                if($detalle->cerrado) {
                                                    continue;
                                                }
                                            @endphp
                                            <tr class="index-{{ $k }}">
                                                <td class="p-0 text-center">
                                                    <div class="custom-checkbox custom-control">
                                                        <input class="form-check-input vale-checkbox" name="listado_piezas_recursos[{{ $k }}][checkbox]" type="checkbox" value="0">
                                                    </div>
                                                </td>
                                                <td>
                                                    {{ $detalle->pieza->nro_pieza.' - '.$detalle->pieza->descripcion.' (Stock actual: '.$detalle->pieza->getStockBase($vale->ordenes_trabajo->base_operacion_id).')' }}
                                                </td>
                                                @if ($detalle->tarea != null)
                                                    <td>
                                                        {{ $detalle->tarea->descripcion }}
                                                    </td>
                                                @endif
                                                <td>
                                                    <input type="number" value="{{ $detalle->cantidad }}" required class="form-control" min="0" name="listado_piezas_recursos[{{ $k }}][cantidad]">
                                                </td>
                                                <td>
                                                    <input type="text" value="{{ $detalle->pieza->unidadMedida->nombre }}" class="form-control" disabled name="listado_piezas_recursos[{{ $k }}][unidad]">
                                                </td>
                                                <td>
                                                </td>
                                                @if ($detalle->tarea != null)
                                                    <input type="hidden" class="form-control" value="{{ $detalle->tarea->id }}"  name="listado_piezas_recursos[{{ $k }}][tarea_id]">
                                                @endif
                                                <input type="hidden" class="form-control" value="{{ $detalle->pieza_id }}"  name="listado_piezas_recursos[{{ $k }}][pieza_id]">
                                                <input type="hidden" class="form-control" value="{{ $detalle->pieza->descripcion }}"  name="listado_piezas_recursos[{{ $k }}][pieza_descripcion]">
                                                <input type="hidden" class="form-control" value="{{ $detalle->pieza->unidadmedida->nombre }}"  name="listado_piezas_recursos[{{ $k }}][unidad_medida]">
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tbody id="tabla_listado_recursos_cerrados">
                                    @foreach ($vale->vale_detalle as $k => $detalle)
                                            @php
                                                if(!$detalle->cerrado) {
                                                    continue;
                                                }
                                            @endphp
                                            <tr>
                                                <td></td>
                                                <td>
                                                    {{ $detalle->pieza->nro_pieza.' - '.$detalle->pieza->descripcion }}
                                                </td>
                                                @if ($detalle->tarea != null)
                                                    <td>
                                                        {{ $detalle->tarea->descripcion }}
                                                    </td>
                                                @endif
                                                <td>{{$detalle->cantidad}}</td>
                                                <td>{{$detalle->pieza->unidadMedida->nombre}}</td>
                                                <td></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <div class="card-footer text-right">
                                    <a href="{{ route('admin.vale', $vale->id) }}">
                                        <button type="button" class="btn btn-secondary mr-1">Cancelar</button>
                                    </a>
                                    <button type="submit" name="action" value="store"
                                        class="btn btn-primary">Guardar Vale</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/vale/index.js') }}"></script>
@endsection