@extends('layouts.admin-master')

@section('title')
    Crear Vale
@endsection

@section('content')
    <div class="section">
        <div class="section-header">
            <h1>Crear Vale</h1>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>{{ 'Orden de trabajo '.$orden_trabajo->tipo_orden }}</h4>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-12 col-md-1">
                                    <label for="recurso" class="col-form-label">Recurso</label>
                                </div>
                                <div class="col-12 col-md-6">
                                    <select name="recurso" id="recurso_id" class="form-control select2" data-placeholder="Filtrar por recurso">
                                        <option label="Seleccione un recurso" value="">Seleccione un recurso</option>
                                        @foreach ($recursos as $recurso)
                                            <option value="{{$recurso->piezas->id}}" data-unidad-text="{{ $recurso->piezas->unidadMedida->nombre}}">{{$recurso->piezas->nro_pieza.' - '.$recurso->piezas->descripcion}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-md-2">
                                    <a class="btn btn-success text-white" id="agregar-recurso-pieza" title="Agregar componente">
                                        <i class="fas fa-plus"></i> Agregar Recurso
                                    </a>
                                </div>
                                <div class="col-12 col-md-3">
                                    Base de operación: <strong>{{$orden_trabajo->base_operacion->nombre}}</strong>
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
                            <form method="POST" action="{{ route('admin.vale.store') }}">
                            @csrf
                                <input type="hidden" name="ot_id" value="{{ $orden_trabajo->id }}">
                                <input type="hidden" name="base_operacion_id" id="base_operacion_id" value="{{ $orden_trabajo->base_operacion_id }}">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Recurso</th>
                                            <th>Cantidad</th>
                                            <th>Unidad de Medida</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tabla_listado_recursos">
                                    </tbody>
                                </table>

                                <div class="card-footer text-right">
                                    <a href="{{ route('admin.vale') }}">
                                        <button type="button" class="btn btn-secondary mr-1">Cancelar</button>
                                    </a>
                                    <button type="submit" name="action" value="store"
                                        class="btn btn-primary">Guardar</button>
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