@extends('layouts.admin-master')

@section('title')
Listado de Unidades
@endsection

@section('content')
<section class="section">
  <div class="section-header">
    <h1>Listado de Unidades</h1>
  </div>
  <div class="section-body">
        <div class="section-title">
            <a href="{{ route('admin.unidades.create') }}">
                <button type="button" class="btn btn-primary">Agregar unidad</button>
            </a>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="col-6">
                            <h4 class="col-6">Listado de Unidades</h4>
                        </div>
                        {{-- <div class="col-6 input-group d-flex flex-row-reverse align-items-center">
                            <form action="" method="GET">
                                <div class="input-group-btn d-flex flex-row"> --}}
                                    {{-- <label for="tipo_unidad" class="col-form-label col-form-label-sm text-md-right col-12 col-md-3 col-lg-3">Tipo Unidad</label>
                                    <select name="tipo_unidad" id="tipo_unidad" class="form-control-sm mx-1" data-placeholder="Seleccionar un tipo de unidad" required>
                                        @foreach ($tipo_unidades as $tipo_unidad)
                                            <option value="{{$tipo_unidad}}" {{($tipo_unidad == $buscar_unidad)? 'selected' : ''}}>{{$tipo_unidad}}</option>
                                        @endforeach
                                    </select> --}}
                                    {{-- <input type="search" name="buscar" class="form-control" placeholder="Buscar" value="{{$buscar}}">
                                    <button class="btn btn-primary btn-icon"><i class="fas fa-search"></i></button>
                                    <a href="{{ route('admin.unidades') }}" class="btn btn-lighty btn-icon"><i class="fas fa-redo"></i></a> --}}
                                {{-- </div>
                            </form>
                        </div> --}}
                    </div>
                    <div class="card-body">
                        <form action="" method="GET">
                            <div class="float-left">
                                <div class="form-inline">
                                    <div class="form-group mx-3">
                                        <label>Estado de unidades</label>
                                        <select name="estado" class="form-control mx-3" >
                                            @foreach ($estados as $estado)
                                                <option value="{{ $estado }}" {{ ($estado == $filtro_estado) ? 'selected' : '' }}>{{ $estado }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="float-right">
                                <div class="input-group d-flex flex-row-reverse">
                                    <div class="input-group-btn d-flex flex-row">
                                        <input type="search" name="buscar" class="form-control" placeholder="Buscar por: N° interno" value="{{ $buscar }}" autocomplete="off">
                                        <button class="btn btn-primary btn-icon">
                                            <i class="fas fa-search"></i>
                                        </button>
                                        <a href="{{ route('admin.unidades') }}" class="btn btn-lighty btn-icon">
                                            <i class="fas fa-redo"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="clearfix mb-3"></div>
                        <div class="table-responsive table-invoice">
                            <table class="table table-striped">
                                <tbody>
                                    <tr class="text-center">
                                        <th>N° Interno</th>
                                        {{-- <th>Tipo de Unidad</th> --}}
                                        <th>Modelo</th>
                                        <th>Nro Serie</th>
                                        <th>Tipo de Vehículo</th>
                                        <th>Base asignada</th>
                                        <th>Acción</th>
                                    </tr>
                                    @forelse ($unidades as $unidad)
                                    <tr class="text-center">
                                        <td>{{$unidad->num_interno}}</td>
                                        {{-- <td>{{$unidad->tipo_unidad}}</td> --}}
                                        <td>{{$unidad->modelo->nombre}}</td>
                                        <td>{{$unidad->num_serie}}</td>
                                        <td>{{$unidad->tipo_vehiculo->nombre}}</td>
                                        <td>{{$unidad->base_operacion->nombre}}</td>
                                        <td>
                                            <div id="disabled-{{ $unidad->id }}" style="display: {{ ($unidad->deleted_at != null) ? 'initial' : 'none' }}">
                                                <button type="button" class="btn btn-success button-enabled" id="habilitar-modal-{{ $unidad->id }}"
                                                    data-id="{{ $unidad->id }}"
                                                    data-token=" {{ Session::token() }}"
                                                    data-route="{{ route('admin.unidades.restore', $unidad->id) }}">
                                                    <i class="fas fa-check-circle"></i>
                                                </button>
                                            </div>
                                            <div id="enabled-{{ $unidad->id }}" style="display: {{ ($unidad->deleted_at != null) ? 'none' : 'initial' }}">
                                                <a href="{{ route('admin.unidades.edit', $unidad->id) }}" class="btn btn-primary" title='Editar'>
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-warning button-disabled" id="deshabilitar-modal-{{ $unidad->id }}"
                                                    data-route="{{ route('admin.unidades.destroy', $unidad->id) }}"
                                                    data-id="{{ $unidad->id }}"
                                                    data-token=" {{ Session::token() }}"
                                                    title='Deshabilitar'>
                                                    <i class="fas fa-times-circle"></i>
                                                </button>
                                                <a href="{{ route('admin.unidades.forcedelete', $unidad->id) }}"
                                                    class="btn btn-danger"
                                                    onclick="return confirm('Se eliminará definitivamente la unidad {{ $unidad->num_interno }} del sistema. ¿Continuar?')"
                                                    title='Eliminar'>
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    @empty
                                        <td colspan="5" class="text-center">No se han encontrado coincidencias</td>
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
                                        <li class="page-item">{{ $unidades->links() }}</li>
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
@include('admin.unidades.partials.habilitar_modal')
@include('admin.unidades.partials.deshabilitar_modal')
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/unidades/index.js') }}"></script>
@endsection
