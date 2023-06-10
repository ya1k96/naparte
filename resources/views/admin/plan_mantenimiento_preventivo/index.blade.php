@extends('layouts.admin-master')

@section('title')
    Plan de mantenimiento preventivo
@endsection

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Plan de mantenimiento preventivo</h1>
        </div>
        <div class="section-body">
            <div class="section-title">
                <a href="{{ route('admin.plan-mantenimiento-preventivo.create') }}">
                    <button type="button" class="btn btn-primary">Agregar nuevo</button>
                </a>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="col-6">Listado de planes de mantenimiento preventivo</h4>
                        </div>
                        <div class="card-body">
                            <form action="" method="GET">
                                <div class="float-left">
                                    <select name="estado" id="estado" class="form-control select2" data-placeholder="Seleccionar estado">
                                        <option label="Seleccionar estado" value="">Seleccionar estado</option>
                                        <option value="activo" {{request()->get('estado') == 'activo' ? 'selected' : ''}}>Activo</option>
                                        <option value="anulado" {{request()->get('estado') == 'anulado' ? 'selected' : ''}}>Anulado</option>
                                    </select>
                                </div>
                                <div class="float-right">
                                    <div class="input-group d-flex flex-row-reverse">
                                        <div class="input-group-btn d-flex flex-row">
                                            <input type="search" name="buscar" class="form-control" placeholder="Buscar" value="{{ $buscar }}" autocomplete="off">
                                            <button class="btn btn-primary btn-icon">
                                                <i class="fas fa-search"></i>
                                            </button>
                                            <a href="{{ route('admin.plan-mantenimiento-preventivo') }}" class="btn btn-lighty btn-icon">
                                                <i class="fas fa-redo"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="clearfix mb-3"></div>
                            <table class="table table-striped">
                                <thead>
                                    <tr class="text-center">
                                        <th scope=col>Id</th>
                                        <th scope=col>Nombre</th>
                                        <th scope=col>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($planes as $plan)
                                    <tr class="text-center">
                                        <td>{{ $plan->id }}</td>
                                        <td>{{ $plan->nombre }}</td>
                                        <td>
                                            @if (!$plan->deleted_at)
                                                <form action="{{ route('admin.plan-mantenimiento-preventivo.destroy', $plan->id) }}" method="post">
                                                    <a href="{{ route('admin.plan-mantenimiento-preventivo.show', $plan->id) }}" class="btn btn-light" title="Ver">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.plan-mantenimiento-preventivo.edit', $plan->id) }}" class="btn btn-primary" title="Editar">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <a href="{{ route('admin.plan-mantenimiento-preventivo.replicate', $plan->id) }}" class="btn btn-info" title="Duplicar"
                                                        onclick="return confirm('Se duplicará el plan {{ $plan->nombre }}. ¿Continuar?')">
                                                        <i class="fa fa-copy"></i>
                                                    </a>
                                                    @csrf
                                                    @method('delete')
                                                    <button type="submit" class="btn btn-danger" title="Anular" onclick="return confirm('Se anulará el plan {{ $plan->nombre }}. ¿Continuar?')">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <button type="button" class="btn btn-sm btn-dark">Anulado</button>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer">
                            <div class="buttons">
                                <nav aria-label="Page navigation example">
                                    <ul class="pagination">
                                        <li class="page-item">{{ $planes->links() }}</li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
               </div>
            </div>
        </div>
    </section>
@endsection
