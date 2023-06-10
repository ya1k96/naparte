@extends('layouts.admin-master')

@section('title')
Mantenimiento rutinario
@endsection


@section('content')

<!-- Container -->
<div class="section">
    <!-- Header -->
    <div class="section-header">
        <h1>Mantenimiento rutinario</h1>
    </div>

    <!-- Add button -->
    <!-- <div class="section-title">
        <a href="{{ route('admin.mantenimiento-rutinario.create') }}">
            <button type="button" class="btn btn-primary">Agregar mantenimiento rutinario</button>
        </a>
    </div>  -->

    <!-- Header of CARD -->
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <!-- Title select -->
                    <div class="card-header">
                        <h4>Seleccione una unidad</h4>
                    </div>
                    <!-- Select one unity -->
                    <div class="card-body">
                        <div class="form-group row mb-4">
                            <label for="unidad" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Unidad</label>
                            <div class="col-12 col-md-7">
                                <select name="unidad" id="unidad_id" class="form-control select2" data-placeholder="Seleccione una unidad">
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

        <!-- Tabla de resultados -->
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header">
                        <h4>Mantenimientos próximos</h4>
                    </div>
                    <!-- Body -->
                    <div class="card-body" >
                        <div class="table-responsive table-user-custom">
                            <table class="table align-items-center table-flush">
                                <input type="hidden" name="_token" value="{{ Session::token() }}">
                                <thead>
                                    <tr class="text-center">
                                        <th>Parte</th>
                                        <th>Actividad</th>
                                        <th>Último mant. realizado</th>
                                        <th>Frecuencia</th>
                                        <th>Próx. mantenimiento</th>
                                        <th>Próx. mant. modificado</th>
                                        <th>Estado</th>
                                        <th>OT</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody id="historial">
                                    {{-- <tr>
                                        <td>Motor/Sist. de admisión</td>
                                        <td>Cambio de aceite</td>
                                        <td>800000</td>
                                        <td>60000</td>
                                        <td>860000</td>
                                        <td> - </td>
                                        <td>Normal</td>
                                        <td> - </td>
                                        <td>
                                            <button type="button" class="btn btn-light" title="Adelantar/Posponer">
                                                <i class="fas fa-history"></i>
                                            </button>
                                        </td>
                                    </tr> --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('admin.mantenimiento_rutinario.partials.kilometros_modal')
@include('admin.mantenimiento_rutinario.partials.fecha_modal')
@include('admin.mantenimiento_rutinario.partials.combinado_modal')
@endsection

@section('scripts')
<script src="{{ asset('assets/js/mantenimiento_rutinario/index.js') }}"></script>
@endsection