@extends('layouts.admin-master')

@section('title')
    Historial de Mantenimientos
@endsection

@section('content')
    <div class="section">
        <div class="section-header">
            <h1>Historial de Mantenimientos</h1>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Seleccione una unidad</h4>
                        </div>
                        <div class="card-body">
                            <div class="form-group row mb-4">
                                <label for="unidad" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Unidad</label>
                                <div class="col-12 col-md-7">
                                    <select name="unidad" id="unidad_id_historial" class="form-control select2" data-placeholder="Seleccione una unidad">
                                        <option value="" label="Seleccione una unidad">Seleccione una unidad</option>
                                        @foreach ($unidades as $unidad)
                                            <option value="{{ $unidad->id }}">{{ $unidad->num_interno }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="text-primary">Historial de mantenimientos</h4>
                        </div>
                        <div class="card-body">
                            <div id="historial">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/mantenimiento_rutinario/index.js') }}"></script>
@endsection
