@extends('layouts.admin-master')

@section('title')
    Equipos
@endsection

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Equipos</h1>
        </div>
        <div class="section-body">
            <div class="section-title">
                <a href="{{ route('admin.vinculaciones.create') }}">
                    <button type="button" class="btn btn-primary">Agregar nueva vinculación</button>
                </a>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="col-6">Listado de vinculaciones</h4>
                        </div>
                        <div class="card-body">
                            <form action="" method="GET">
                                <div class="float-left">
                                    <select name="plan_id" id="plan_id" class="form-control select2" data-placeholder="Seleccione un plan">
                                        <option label="Seleccione un plan" value="">Seleccione un plan</option>
                                        @foreach ($planes as $plan)
                                            <option value="{{ $plan->id }}">{{ $plan->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="float-right">
                                    <div class="input-group d-flex flex-row-reverse">
                                        <div class="input-group-btn d-flex flex-row">
                                            <input type="search" name="unidad" class="form-control" placeholder="Ingrese unidad a buscar" value="{{ $buscar }}" autocomplete="off">
                                            <button class="btn btn-primary btn-icon">
                                                <i class="fas fa-search"></i>
                                            </button>
                                            <a href="{{ route('admin.vinculaciones') }}" class="btn btn-lighty btn-icon">
                                                <i class="fas fa-redo"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="clearfix mb-3"></div>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr class="text-center">
                                            <th>Unidad</th>
                                            <th>Plan</th>
                                            <th>Km Iniciales</th>
                                            <th>Fecha</th>
                                            <th>Estimativo</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody >
                                        @forelse ($vinculaciones as $vinculacion)
                                        <tr class="text-center">
                                            <td>{{ $vinculacion->unidad->num_interno }}</td>
                                            <td>{{ $vinculacion->plan->nombre }}</td>
                                            <td>{{ $vinculacion->km_inicial . " km" }}</td>
                                            <td>{{ $vinculacion->fecha }}</td>
                                            <td>{{ $vinculacion->estimativo . " km" }}</td>
                                            <td>
                                                <a href="{{ route('admin.vinculaciones.edit', $vinculacion->id) }}" class="btn btn-primary">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <form action=" {{ route('admin.vinculaciones.destroy', $vinculacion->unidad_id) }} " method="POST">
                                                    @csrf
                                                    @method('delete')
                                                    <button class="btn btn-danger" onclick="return confirm('Se eliminará definitivamente la vinculacion de la unidad {{$vinculacion->unidad->num_interno}} al plan {{$vinculacion->plan->nombre}}. ¿Continuar?')"><i class="fa fa-trash"></i></button>
                                                </form>
                                            </td>
                                        @empty
                                            <td colspan="6" class="text-center">No se encontraron resultados</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="buttons">
                                <div class="float-right">
                                    <nav aria-label="Page navigation example">
                                        <ul class="pagination">
                                            <li class="page-item">{{ $vinculaciones->links() }}</li>
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
