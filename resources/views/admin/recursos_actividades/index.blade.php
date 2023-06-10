@extends('layouts.admin-master')

@section('title')
    Asociación de recursos
@endsection

@section('content')
    <div class="section">
        <div class="section-header">
            <h1>Asociación de recursos</h1>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Seleccione una unidad</h4>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('admin.recurso-actividad.replicar-recursos-unidad') }}">
                                @csrf
                                <div class="form-group row mb-4">
                                    <label for="unidad" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Unidad</label>
                                    <div class="col-12 col-md-7">
                                        <select name="unidad" id="unidad_id_recurso_actividad" class="form-control select2" data-placeholder="Seleccione una unidad">
                                            <option value="" label="Seleccione una unidad">Seleccione una unidad</option>
                                            @foreach ($unidades as $unidad)
                                            <option value="{{ $unidad->id }}" {{ (isset($unidad_id) && $unidad_id == $unidad->id) ? 'selected' : '' }}>{{ $unidad->num_interno }}</option>                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row boton-replicar" style="display: none;">
                                    <div class="col-12 col-md-7 offset-md-3">
                                        <button type="button" name="action" class="btn btn-sm btn-primary" id="button-replicar">Replicar de otra unidad</button>
                                    </div>
                                </div>
                                <div class="div-asociar" style="display: none;">
                                    <div class="form-group row">
                                        <div class="col-12 col-md-4 offset-md-3">
                                            <select name="unidad_copiar" class="form-control select2 unidades_plan" data-placeholder="Seleccione una unidad">
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-12 col-md-4 offset-md-3">
                                            <button type="button" name="cancel" class="btn btn-secondary cancelar-replicar">Cancelar</button>
                                            <button type="submit" name="action" class="btn btn-primary">Copiar</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Listado</h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped">
                                <thead>
                                    <tr class="text-center">
                                        <th>Parte</th>
                                        <th>Actividad</th>
                                        <th>Recursos</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody id="historial">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/mantenimiento_rutinario/index.js') }}"></script>

    <script>
        if ($('#unidad_id_recurso_actividad').val() != '') {
            $("#unidad_id_recurso_actividad").select2().trigger("change");
        }
    </script>
@endsection
