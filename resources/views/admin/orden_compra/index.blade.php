@extends('layouts.admin-master')

@section('title')
    Ordenes de compra
@endsection

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Orden de Compra</h1>
        </div>
        <div class="section-body">
            {{-- filters --}}
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        {{-- title --}}
                        <div class="card-header">
                            <h4 class="input-group d-flex">Listado de Ordenes de compras</h4>
                            <div class="input-group d-flex flex-row-reverse">
                                <a href="{{ route('admin.orden-compra.create') }}" class="btn btn-primary btn-icon" style="border-radius: 0.25rem">
                                    <i class="fas fa-plus"></i> Generar Orden
                                </a>
                            </div>
                        </div>
                        {{-- options --}}
                        <div class="card-body ml-3">
                            <form action="{{ route('admin.orden-compra.index') }}" method="GET">
                                <div class="float-left">
                                    <div class="row mb-2">
                                        <div class="float-left">
                                            <div class="font-weight-bold">Emision Desde</div>
                                            <input type="date" name="fecha_emision_desde" class="form-control"
                                                value="{{ $buscar['fecha_emision_desde'] }}">
                                        </div>

                                        <div class="float-left mx-1 mr-2">
                                            <div class="font-weight-bold">Emision Hasta</div>
                                            <input type="date" name="fecha_emision_hasta" class="form-control"
                                                value="{{ $buscar['fecha_emision_hasta'] }}">
                                        </div>

                                        <div class="float-left mx-1">
                                            <div class="font-weight-bold">Nº de OC</div>
                                            <input type="search" name="id" class="form-control"
                                                placeholder="Ingrese el N° de OC" value="{{ $buscar['id'] }}"
                                                autocomplete="off">
                                        </div>

                                        <div class="float-left mx-1">
                                            <div class="font-weight-bold">Estado</div>
                                            <select name="estado" class="form-control select2"
                                                data-placeholder="Seleccione">
                                                <option label="Seleccione" value="">Seleccione</option>
                                                <option value="pendiente_parcial">Pendiente y Parcial</option>
                                                @foreach ($arr_estados as $key => $estado)
                                                    <option value="{{ $key }}"
                                                        {{ $key == $buscar['estado'] ? 'selected' : '' }}>
                                                        {{ $estado }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="float-left mx-1">
                                            <div class="font-weight-bold">Prioridad</div>
                                            <select name="prioridad" class="form-control select2"
                                                data-placeholder="Seleccione">
                                                <option label="Seleccione" value="">Seleccione</option>
                                                @foreach ($arr_prioridades as $key => $prioridad)
                                                    <option value="{{ $key }}"
                                                        {{ $key == $buscar['prioridad'] ? 'selected' : '' }}>
                                                        {{ $prioridad }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="float-left mx-1" style="max-width: 300px">
                                            <div class="font-weight-bold">Nº Pieza</div>
                                            <select name="pieza_id" class="form-control select2"
                                                data-placeholder="Seleccione">
                                                <option label="Seleccione" value="">Seleccione</option>
                                                @foreach ($arr_piezas as $pieza)
                                                    <option value="{{ $pieza->id }}"
                                                        {{ $pieza->id == $buscar['pieza_id'] ? 'selected' : '' }}>
                                                        {{ $pieza->nro_pieza }} - {{$pieza->descripcion}}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row mb-2">
                                        <div class="float-left ">
                                            <div class="font-weight-bold">Entrega Desde</div>
                                            <input type="date" name="fecha_entrega_desde" class="form-control"
                                                value="{{ $buscar['fecha_entrega_desde'] }}">
                                        </div>

                                        <div class="float-left mx-1 mr-2">
                                            <div class="font-weight-bold">Entrega Hasta</div>
                                            <input type="date" name="fecha_entrega_hasta" class="form-control"
                                                value="{{ $buscar['fecha_entrega_hasta'] }}">
                                        </div>

                                        <div class="float-left mx-1 ">
                                            <div class="font-weight-bold">Empresa</div>
                                            <select name="empresa_id" class="form-control select2"
                                                data-placeholder="Seleccione">
                                                <option label="Seleccione" value="">Seleccione</option>
                                                @foreach ($arr_empresas as $empresa)
                                                    <option value="{{ $empresa->id }}"
                                                        {{ $empresa->id == $buscar['empresa_id'] ? 'selected' : '' }}>
                                                        {{ $empresa->nombre }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="float-left mx-1">
                                            <div class="font-weight-bold">Proveedor</div>
                                            <select name="proveedor_id" class="form-control select2"
                                                data-placeholder="Seleccione">
                                                <option label="Seleccione" value="">Seleccione</option>
                                                @foreach ($arr_proveedores as $proveedor)
                                                    <option value="{{ $proveedor->id }}"
                                                        {{ $proveedor->id == $buscar['proveedor_id'] ? 'selected' : '' }}>
                                                        {{ $proveedor->nombre }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="float-left mx-1">
                                            <div class="font-weight-bold">Base Emisora</div>
                                            <select multiple size=6 name="base_emite_id[]" class="form-control select2"
                                                data-placeholder="Seleccione">
                                                <option label="Seleccione" value="">Seleccione</option>
                                                @foreach ($arr_base_emite as $base)
                                                    <option value="{{ $base->id }}"
                                                        {{ $base->id == $buscar['base_emite_id'] ? 'selected' : '' }}>
                                                        {{ $base->nombre }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="float-left mx-1">
                                            <div class="font-weight-bold">Base Receptora</div>
                                            <select multiple size=6 name="base_recibe_id[]" class="form-control select2"
                                                data-placeholder="Seleccione">
                                                <option label="Seleccione" value="">Seleccione</option>
                                                @foreach ($arr_base_recibe as $base)
                                                    <option value="{{ $base->id }}"
                                                        {{ $base->id == $buscar['base_recibe_id'] ? 'selected' : '' }}>
                                                        {{ $base->nombre }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                {{-- button --}}
                                <div class="float-right mt-2">
                                    <div class="input-group d-flex ">
                                        <button class="btn btn-primary">
                                                {{-- <input type="search" name="buscar" class="form-control" placeholder="Ingrese el N° de OT" value="{{ $buscar }}" autocomplete="off"> --}}
                                                <i class="fas fa-search"></i> Buscar
                                            </button>
                                            <span class="d-inline-block" data-toggle="tooltip" data-placement="bottom" data-title="Limpiar Filtros" data-original-title="" title="">
                                            <a href="{{ route('admin.orden-compra.index') }}"
                                            class="btn btn-lighty btn-icon">
                                            <i class="fas fa-redo"></i>
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- table --}}
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            {{-- attributes --}}
                            <div class="row">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th class="text-center">N° Orden</th>
                                                <th class="text-center">Empresa</th>
                                                <th class="text-center">Base Emisora</th>
                                                <th class="text-center">Base Receptora</th>
                                                <th class="text-center">Proveedor</th>
                                                <th class="text-center">Fecha</th>
                                                <th class="text-center">Monto</th>
                                                <th class="text-center">Prioridad</th>
                                                <th class="text-center">Estado</th>
                                                <th class="text-center">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if ($ordenes)
                                                @foreach ($ordenes as $orden)
                                                    <tr>
                                                        <td class="text-center font-weight-bold">
                                                            {{ $orden->id }}
                                                        </td>
                                                        <td class="text-center">
                                                            @if ($orden->empresa)
                                                                {{ $orden->empresa->nombre }}
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            @if ($orden->base_emite)
                                                                {{ $orden->base_emite->nombre }}
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            @if ($orden->base_recibe)
                                                                {{ $orden->base_recibe->nombre }}
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            @if ($orden->proveedor)
                                                                {{ $orden->proveedor->nombre }}
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            {{ \Carbon\Carbon::parse($orden->fecha_emision)->format('d/m/Y') }}
                                                        </td>
                                                        <td class="text-center">
                                                            $ {{ $orden->detalle->sum('monto') }}
                                                        </td>
                                                        <td class="text-center">
                                                            @switch($orden->prioridad)
                                                                @case('baja')
                                                                    <div class="text-warning font-weight-bold">
                                                                        {{ strtoupper($orden->prioridad) }}
                                                                    </div>
                                                                @break

                                                                @case('normal')
                                                                    <div class="text-default font-weight-bold">
                                                                        {{ strtoupper($orden->prioridad) }}
                                                                    </div>
                                                                @break

                                                                @case('alta')
                                                                    <div class="text-danger font-weight-bold">
                                                                        {{ strtoupper($orden->prioridad) }}
                                                                    </div>
                                                                @break
                                                            @endswitch
                                                        </td>
                                                        <td class="text-center">
                                                            @switch($orden->estado)
                                                                @case('abierta')
                                                                    <span class="badge badge-info">{{ strtoupper($orden->estado) }}</span>
                                                                @break

                                                                @case('aprobada')
                                                                    <span class="badge badge-primary">{{ strtoupper($orden->estado) }}</span>
                                                                @break

                                                                @case('parcial')
                                                                    <span class="badge badge-warning">{{ strtoupper($orden->estado) }}</span>
                                                                @break

                                                                @case('recibida')
                                                                    <span class="badge badge-success">{{ strtoupper($orden->estado) }}</span>
                                                                @break

                                                                @case('cerrada')
                                                                    <span class="badge badge-secondary">{{ strtoupper($orden->estado) }}</span>
                                                                @break

                                                                @case('anulada')
                                                                    <span class="badge badge-danger">{{ strtoupper($orden->estado) }}</span>
                                                                @break
                                                            @endswitch
                                                        </td>
                                                        {{-- acciones --}}
                                                        <td class="text-center">
                                                            {{-- ver --}}
                                                            <a href="{{ route('admin.orden-compra.show', $orden->id) }}"
                                                                class="btn btn-secondary" title="Ver">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            {{-- imprimir --}}
                                                            <a href="{{ route('admin.orden-compra.generarPDF', $orden->id) }}" target="_blank"  class="btn btn-success" title="Imprimir">
                                                                <i class="fas fa-print"></i>
                                                            </a>
                                                            @if ((!$orden->deleted_at))
                                                                {{-- aprobar --}}
                                                                @if ($orden->estado == 'abierta')
                                                                    <button type="button" id="btn-aprobar-oc-{{ $orden->id }}" title="Aprobar orden"
                                                                        href="javascript:void(0);" class="btn btn-secondary aprobar-orden-compra"
                                                                        data-id="{{ $orden->id }}">
                                                                        <i class="fas fa-check"></i>
                                                                    </button>

                                                                    <a href="{{ route('admin.orden-compra.edit', $orden->id) }}" class="btn btn-primary" title="Editar">
                                                                        <i class="fa fa-edit"></i>
                                                                    </a>
                                                                @endif
                                                                {{-- cerrar --}}
                                                                @if($orden->estado == 'parcial')
                                                                    <button type="button" id="btn-cerrar-oc-{{ $orden->id }}" title="Cerrar"
                                                                        class="btn btn-dark cerrar-orden-compra" title="Marcar completa"
                                                                        data-id="{{ $orden->id }}">
                                                                        <i class="fas fa-lock"></i>
                                                                    </button>
                                                                @endif 
                                                                @if ($orden->estado == 'abierta' || $orden->estado == 'aprobada')
                                                                    <a href="#" class="btn btn-danger anular"
                                                                    title="Anular"
                                                                    id="anular-modal-{{ $orden->id }}"
                                                                    data-id="{{ $orden->id }}"
                                                                    data-token=" {{ Session::token() }}"
                                                                    data-route="{{ route('admin.orden-compra.anular', $orden->id) }}">
                                                                        <i class="fas fa-trash"></i>
                                                                    </a>
                                                                @endif
                                                            @else
                                                                <button type="button" class="btn btn-sm btn-dark">Anulada</button>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="7" class="text-center">No se encontraron resultados
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="buttons">
                                <div class="float-right">
                                    <nav aria-label="Page navigation example">
                                        <ul class="pagination">
                                            <li class="page-item">{{ $ordenes->appends(request()->query())->links() }}
                                            </li>
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

    @include('admin.orden_compra.partials.anular_modal')
    @include('admin.orden_compra.partials.modal_cerrar_oc')
    @include('admin.orden_compra.partials.modal_aprobar_oc')
@endsection

@section('scripts')
    <script>
        function changeStatus() {
            if(confirm("¿Desea cambiar el estado de la orden de compra?") == true) {
                document.getElementById('frm-change-status').submit();
            }
        }
    </script>
    <script>
        if ($('.cerrar-orden-compra').length > 0) {
        $('.cerrar-orden-compra')
        .off()
        .on('click', function() {
            var id = $(this).data('id');
            $('#btn-cerrar-oc-'+id).fireModal({
                title: 'Cerrar Orden de Compra',
                body: $('#modal-cerrar-oc').clone(),
                footerClass: 'bg-whitesmoke',
                autofocus: false,
                removeOnDismiss: true, 
                center: true,         
                created: function(modal, e, form) {
                    modal.find('input[name=id]').val($(this).data('id'));
                },
                onFormSubmit: function (modal, e, form) {
                },
                shown: function(modal, form) {
                    console.log("shown", modal, form)
                },
                buttons: [{
                    text: 'Guardar',
                    submit: true,
                    class: 'btn btn-primary btn-shadow',
                    handler: function(modal) {
                    }
                }]
            })
        });
        }
    </script>
    {{-- modal anular --}}
    <script>
        $(".anular").on('click', function (e) {
            let id = $(this).data('id');
            var anular = this;

            $(`#anular-modal-${id}`).fireModal({
                title: 'Anular Orden de Trabajo',
                body: $('#modal-anular-part').clone(),
                footerClass: 'bg-whitesmoke',
                autofocus: false,
                removeOnDismiss: true,
                created: function(modal, e, form) {
                    modal.find('form')[0].action = anular.dataset.route;
                },
                shown: function(modal, form) {
                    console.log("shown", modal, form)
                },
                buttons: [{
                    text: 'Continuar',
                    submit: true,
                    class: 'btn btn-primary btn-shadow',
                    handler: function(modal) {
                    }
                }]
            });
        })
    </script>
    {{-- modal-aprobar --}}
    <script>
        if ($('.aprobar-orden-compra').length > 0) {
        $('.aprobar-orden-compra')
        .off()
        .on('click', function() {
            var id = $(this).data('id');
            $('#btn-aprobar-oc-'+id).fireModal({
                title: 'Aprobar Orden de Compra',
                body: $('#modal-aprobar-oc').clone(),
                footerClass: 'bg-whitesmoke',
                autofocus: false,
                removeOnDismiss: true,
                center: true,
                created: function(modal, e, form) {
                    modal.find('input[name=id]').val($(this).data('id'));
                },
                onFormSubmit: function (modal, e, form) {
                },
                shown: function(modal, form) {
                    console.log("shown", modal, form)
                },
                buttons: [{
                    text: 'Guardar',
                    submit: true,
                    class: 'btn btn-primary btn-shadow',
                    handler: function(modal) {
                    }
                }]
            })
        });
        }
    </script>
@endsection