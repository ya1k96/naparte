@extends('layouts.admin-master')

@section('title')
    Historial de kilómetros
@endsection

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Historial de Kms</h1>
        </div>
        <div class="section-body">
            <div class="section-title">
                <a href="{{ route('admin.historial') }}">
                    <button type="button" class="btn btn-primary">Ir al listado de historiales</button>
                </a>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="col-6">Historial de unidad</h4>
                        </div>
                        <form action="{{ route('admin.historial.store') }}" method="POST">
                            @csrf
                            <div class="card-body" id="card-body">
                                <table class="table table-responsive table-striped">
                                    <thead>
                                        <tr class="text-center">
                                            <th>Unidad</th>
                                            <th>Promedio mensual</th>
                                            <th>Última lectura</th>
                                            <th>Fecha última lectura</th>
                                            <th>Nueva lectura</th>
                                            <th>Fecha nueva lectura</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="historial">
                                        @foreach ($unidades as $unidad)                 
                                            <tr class="text-center">
                                                <input type="hidden" id="historial-{{$unidad->id}}-estado" class="estados" name="historial[{{$unidad->id}}][estado]" value="">
                                                <input type="hidden" name="historial[{{$unidad->id}}][num_interno]" value="{{ $unidad->num_interno }}">
                                                <td>{{ $unidad->num_interno }}</td>
                                                <td id="historial_tabla_promedio_{{ $unidad->id }}">{{ $unidad->promedio_ok }} km</td>
                                                <td id="historial_tabla_kilometraje_{{ $unidad->id }}">{{ $agrupados[$unidad->id][0]->kilometraje }} kms</td>
                                                <td>{{ $agrupados[$unidad->id][0]->created_at->format("d/m/Y") }}</td>
                                                <td ><input type="number" name="historial[{{$unidad->id}}][kilometraje]" id="kilometraje-{{ $unidad->id }}" class="form-control input-kms w-auto" data-id="{{ $unidad->id }}" autocomplete="off"></td>
                                                <td><input type="datetime-local" name="historial[{{$unidad->id}}][fecha]" id="fecha" class="form-control" value="{{ Carbon\Carbon::now()->format('Y-m-d')."T".Carbon\Carbon::now()->format('H:i') }}"></td>
                                                <td>
                                                    <a style="display: none;" href="#" id="historial-warning-{{ $unidad->id }}" class="btn btn-icon btn-warning" title="Supera el 30% del promedio"><i class="fas fa-exclamation-triangle"></i></a>
                                                    <a style="display: none;" href="#" id="historial-mark-{{ $unidad->id }}" class="btn btn-icon btn-light">Marcar correcto</a>
                                                    <a style="display: none;" href="#" id="historial-check-{{ $unidad->id }}" class="btn btn-icon btn-success"><i class="fas fa-check"></i></a>
                                                    <a style="display: none;" href="#" id="historial-danger-{{ $unidad->id }}" class="btn btn-icon btn-danger" title="El kilometraje no puede ser menor al último ingresado"><i class="fas fa-times"></i></a>
                                                    <button type="button" class="btn btn-primary button-editar-historial" id="editar-historial-{{ $unidad->id }}"
                                                        data-id="{{ $unidad->id }}"
                                                        data-route="{{ route('api.historiales', $unidad->id) }}">
                                                        <i class="fas fa-pen"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer text-right">
                                <button type="submit" id="submit" class="btn btn-primary">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @include('admin.historial_unidad.partials.editar_kilometraje')
@endsection

@section('scripts')
    <script>

    </script>
    <script src="{{ asset('assets/js/historiales/index.js') }}"></script>
@endsection
