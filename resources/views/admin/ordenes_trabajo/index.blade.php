@extends('layouts.admin-master')

@section('title')
    Ordenes de trabajo
@endsection

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Listado de ordenes de trabajo</h1>
        </div>
        <div class="section-body">
            <!-- <div class="section-title">
                <a href="{{ route('admin.ordenes-trabajo.create') }}">
                    <button type="button" class="btn btn-primary">Nueva orden de trabajo Correctiva</button>
                </a>
                <a href="{{ route('admin.ordenes-trabajo.createPreventiva') }}">
                    <button type="button" class="btn btn-primary">Nueva orden de trabajo Preventiva</button>
                </a>
            </div> -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="col-6">Listado de ordenes de trabajo</h4>
                        </div>
                        <div class="card-body">
                            <form action="" method="GET">
                                <div class="float-left">
                                    <select name="tipo_orden" id="tipo_orden" class="form-control select2" data-placeholder="Filtrar por tipo de orden">
                                        <option label="Seleccione un tipo de orden" value="">Seleccione un tipo de orden</option>
                                        @foreach ($tipos_ordenes as $tipo_orden)
                                            <option value="{{$tipo_orden}}" {{ ($tipo_orden == $buscar_tipo_orden) ? 'selected' : ''}}>{{$tipo_orden}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="float-left mx-2">
                                    <select name="status_orden" id="status_orden" class="form-control select2" data-placeholder="Filtrar por status">
                                        <option label="Seleccione un status" value="">Seleccione un status</option>
                                        @foreach ($status_ordenes as $status_orden)
                                            <option value="{{$status_orden}}" {{ ($status_orden == $buscar_status_orden) ? 'selected' : ''}}>{{$status_orden}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="float-left mx-2">
                                    <select name="numero_unidad" id="numero_unidad" class="form-control select2" data-placeholder="Filtrar por unidad">
                                        <option label="Seleccione un status" value="">Seleccione un status</option>
                                        @foreach ($numero_unidades as $unidad)
                                            <option value="{{$unidad->num_interno}}" {{ ($unidad->num_interno == $buscar_numero_unidad) ? 'selected' : ''}}>{{$unidad->num_interno}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="float-left mx-2">
                                    <select name="base_operacion" id="base_operacion" class="form-control select2" data-placeholder="Filtrar por base de operación">
                                        <option label="Seleccione un status" value="">Seleccione un status</option>
                                        @foreach ($bases_operacion as $base)
                                            <option value="{{$base->id}}" {{ ($base->id == $buscar_base_operacion) ? 'selected' : ''}}>{{$base->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="float-right">
                                    <div class="input-group d-flex flex-row-reverse">
                                        <div class="input-group-btn d-flex flex-row">
                                            <input type="search" name="buscar" class="form-control" placeholder="Ingrese el N° de OT" value="{{ $buscar }}" autocomplete="off">
                                            <button class="btn btn-primary btn-icon">
                                                <i class="fas fa-search"></i>
                                            </button>
                                            <a href="{{ route('admin.ordenes-trabajo') }}" class="btn btn-lighty btn-icon">
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
                                            <th>N° Orden</th>
                                            <th>Base</th>
                                            <th>Tipo de Orden</th>
                                            <th>Unidad</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody >
                                        @forelse ($ordenes as $orden)
                                            @if($orden->unidad)
                                                <tr class="text-center">
                                                    <td>{{ $orden->numeracion }}</td>
                                                    <td>{{ $orden->base_operacion->nombre }}</td>
                                                    <td>{{ $orden->tipo_orden }}</td>
                                                    <td>{{ $orden->unidad->num_interno }}</td>
                                                    <td>{{ $orden->status }}</td>
                                                    <td>
                                                        <form action="{{ route('admin.ordenes-trabajo.destroy', $orden->id) }}" method="post">
                                                            @csrf
                                                            @method('delete')
                                                            <input type="hidden" name="id" value="{{ $orden->id }}">
                                                            <a href="{{ route('admin.ordenes-trabajo.show', $orden->id) }}" class="btn btn-secondary" title="Ver">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            @if ($orden->status == 'Cerrada')
                                                            <a href="#" class="btn btn-warning reabrir" 
                                                            title="Reabrir"
                                                            id="reabrir-modal-{{ $orden->id }}"
                                                            data-id="{{ $orden->id }}"
                                                            data-token=" {{ Session::token() }}"
                                                            data-route="{{ route('admin.ordenes-trabajo.reabrir', $orden->id) }}">
                                                                <i class="fas fa-envelope-open"></i>
                                                            </a>
                                                            @endif
                                                            @if ($orden->tipo_orden == 'Correctiva')
                                                            <a href="{{ route('admin.ordenes-trabajo.generarPDF', $orden->id) }}" target="_blank"  class="btn btn-success" title="Imprimir">
                                                                <i class="fas fa-print"></i>
                                                            </a>
                                                            @endif
                                                            @if ($orden->tipo_orden == 'Preventiva')
                                                            <a href="{{ route('admin.ordenes-trabajo.generarPDFPreventiva', $orden->id) }}" target="_blank" class="btn btn-success" title="Imprimir">
                                                                <i class="fas fa-print"></i>
                                                            </a>
                                                            @endif
                                                            @if ($orden->created_at->diffInDays($hoy) < 30)
                                                                @if ($orden->tipo_orden == 'Correctiva')
                                                                    <a href="{{ route('admin.ordenes-trabajo.edit', $orden->id) }}" class="btn btn-primary" title="Editar">
                                                                        <i class="fa fa-edit"></i>
                                                                    </a>
                                                                @elseif ($orden->tipo_orden == 'Preventiva')
                                                                    <a href="{{ route('admin.ordenes-trabajo.editPreventiva', $orden->id) }}" class="btn btn-primary" title="Editar">
                                                                        <i class="fa fa-edit"></i>
                                                                    </a>
                                                                @endif
                                                            @endif
                                                            @if ($orden->status == 'Abierta')
                                                                @if($orden->kilometraje != null)
                                                                {{-- <button type="submit" class="btn btn-danger" name="status" value="Cerrada" title="Cerrar" onclick="return confirm('Se cerrará la OT {{ $orden->id }}. ¿Continuar?')">
                                                                    <i class="fas fa-times-circle"></i>
                                                                </button> --}}
                                                                <input type="hidden" id="historial-{{$orden->id}}-estado" class="estados" name="historial[{{$orden->id}}][estado]" value="">
                                                                <a style="display: none;" href="#" id="historial-warning-{{ $orden->id }}" class="btn btn-icon btn-warning" title="Supera el 30% del promedio"><i class="fas fa-exclamation-triangle"></i></a>
                                                                <a style="display: none;" href="#" id="historial-mark-{{ $orden->id }}" class="btn btn-icon btn-light">Marcar correcto</a>
                                                                <a style="display: none;" href="#" id="historial-check-{{ $orden->id }}" class="btn btn-icon btn-success"><i class="fas fa-check"></i></a>
                                                                <a style="display: none;" href="#" id="historial-danger-{{ $orden->id }}" class="btn btn-icon btn-danger" title="El kilometraje no puede ser menor al último ingresado"><i class="fas fa-times"></i></a>
                                                                {{-- <button type="button" title="Cerrar" class="btn btn-danger button-cerrar-historial" id="cerrar-historial-{{ $orden->id }}"
                                                                    data-id="{{ $orden->id }}"
                                                                    data-route="{{ route('api.ordenes-trabajo.getOrdenHistoriales', $orden->id) }}">
                                                                    <i class="fas fa-times-circle"></i>
                                                                </button> --}}

                                                                <a href="#" class="btn btn-danger button-cerrar-historial"
                                                                title="Cerrar"
                                                                id="cerrar-historial-{{ $orden->id }}"
                                                                data-id="{{ $orden->id }}"
                                                                data-token=" {{ Session::token() }}"
                                                                data-route="{{ route('admin.ordenes-trabajo.destroy', $orden->id) }}"
                                                                data-api="{{ route('api.ordenes-trabajo.getOrdenHistoriales', $orden->id) }}"
                                                                data-tipo_orden="{{$orden->tipo_orden}}">
                                                                    <i class="fas fa-times-circle"></i>
                                                                </a>
                                                                @endif
                                                            <a href="#" class="btn btn-danger anular"
                                                            title="Anular"
                                                            id="anular-modal-{{ $orden->id }}"
                                                            data-id="{{ $orden->id }}"
                                                            data-token=" {{ Session::token() }}"
                                                            data-route="{{ route('admin.ordenes-trabajo.anular', $orden->id) }}">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                            @endif
                                                            @if ($orden->status == 'Abierta')
                                                                @if ($orden->tipo_orden == 'Correctiva')
                                                                    @if ($orden->vale == null)
                                                                        <a href="{{ route('admin.vale.create', ['id' => $orden->id]) }}" class="btn btn-info" title="Crear Vale">
                                                                            <i class="far fa-list-alt"></i>
                                                                        </a>
                                                                    @else
                                                                    <button type="button" class="btn btn-info info-vales" title="Vale Existente">
                                                                        <i class="far fa-list-alt"></i>
                                                                    </button>
                                                                    @endif
                                                                @endif
                                                            @endif
                                                        </form>
                                                    </td>
                                            @endif
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
                                            <li class="page-item">{{ $ordenes->appends(request()->query())->links() }}</li>
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
    @include('admin.ordenes_trabajo.partials.reabrir_modal')
    @include('admin.ordenes_trabajo.partials.anular_modal')
    @include('admin.ordenes_trabajo.partials.cerrar_ot')
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/ordenes_trabajo/index.js') }}"></script>
    <script>
        var url_imprimir = {!! json_encode(session('url_imprimir')) !!};
        if(url_imprimir != null) {
            window.open(
            HOST + url_imprimir, "_blank").focus();
        }
    </script>
@endsection
