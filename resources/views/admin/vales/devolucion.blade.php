@extends('layouts.admin-master')

@section('title')
    Devolución Vale
@endsection

@section('content')
    <div class="section">
        <div class="section-header">
            <h1>Devolución de recursos de un Vale</h1>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Devolución de recursos</h4>
                        </div>
                        <div class="card-body">
                            <p>
                                Seleccione los checkbox de los recursos que desea generar devoluciones, luego ingrese la cantidad a devolver.
                                <br>
                                Si un recurso no fue utilizado, ingrese el total de la cantidad y se eliminará por completo del vale.
                            </p>
                            <form method="POST" action="{{ route('admin.vale.updateDevolucion', $vale->id)}}">
                            @method('PATCH')
                            @csrf
                                <input type="hidden" name="base_operacion_id" id="base_operacion_id" value="{{ $vale->ordenes_trabajo->base_operacion_id }}">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Recurso</th>
                                            @if ($vale->ordenes_trabajo->tipo_orden == 'Preventiva')
                                                <th>Actividad</th>
                                            @endif
                                            <th>Cantidad</th>
                                            <th>Cantidad a devolver</th>
                                            <th>Unidad de Medida</th>
                                            <th>Generar Devolución</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tabla_listado_recursos_devolucion">
                                    @foreach ($vale->vale_detalle as $k => $detalle)
                                            @php
                                                if(!$detalle->cerrado) {
                                                    continue;
                                                }
                                            @endphp
                                            <tr>
                                                <input type="hidden" class="id-recurso" name="listado_piezas_recursos[{{ $k }}][id]" value="{{$detalle->id}}">
                                                <input type="hidden" class="id-recurso" name="listado_piezas_recursos[{{ $k }}][pieza_id]" value="{{$detalle->pieza_id}}">
                                                <input type="hidden" class="id-recurso" name="listado_piezas_recursos[{{ $k }}][tarea_id]" value="{{$detalle->tarea_id}}">
                                                <td>
                                                    {{ $detalle->pieza->nro_pieza.' - '.$detalle->pieza->descripcion }}
                                                </td>
                                                @if ($detalle->tarea != null)
                                                    <td>
                                                        {{ $detalle->tarea->descripcion }}
                                                    </td>
                                                @endif
                                                <td>
                                                    <input type="number" value="{{ $detalle->cantidad }}" required readonly class="form-control" min="0" name="listado_piezas_recursos[{{ $k }}][cantidad]">
                                                </td>
                                                <td>
                                                    <input type="number" value="0" min="0" readonly max="{{ $detalle->cantidad }}" data-index-id="{{ $k }}" required class="form-control cant-devolucion" min="0" name="listado_piezas_recursos[{{ $k }}][cantidad_devolucion]">
                                                </td>
                                                <td>{{$detalle->pieza->unidadMedida->nombre}}</td>
                                                <td class="p-0 text-center">
                                                    <div class="custom-checkbox custom-control">
                                                        <input class="form-check-input devolucion-checkbox" data-index-id="{{ $k }}" name="listado_piezas_recursos[{{ $k }}][checkbox]" type="checkbox" value="0">
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <div class="card-footer text-right">
                                    <a href="{{ route('admin.vale') }}">
                                        <button type="button" class="btn btn-secondary mr-1">Cancelar</button>
                                    </a>
                                    <button type="submit" name="action" value="store" id="guardar-vale"
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