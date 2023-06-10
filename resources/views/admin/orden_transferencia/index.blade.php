@extends('layouts.admin-master')

@section('title')
    Ordenes de Transferencia
@endsection

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Orden de Transferencia</h1>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Listado de ordenes de transferencia</h4>
                        </div>
                        <div class="card-body">                          
                            <form action="" method="GET">
                              <div class="float-left">
                                <div class="row mb-2">    
                                  <div class="float-left">
                                    <div class="font-weight-bold">Nº de OT</div>
                                    <input type="search" name="id" class="form-control" placeholder="Ingrese el N° de OC" value="{{ $buscar['id'] }}" autocomplete="off">
                                  </div>                                                                
                                  <div class="float-left ml-3">
                                    <div class="font-weight-bold">Fecha Emision Desde</div>
                                    <input type="date" name="fecha_emision_desde" class="form-control" value="{{ $buscar['fecha_emision_desde'] }}">
                                  </div>
                                  <div class="float-left">
                                    <div class="font-weight-bold">Fecha Emision Hasta</div>
                                    <input type="date" name="fecha_emision_hasta" class="form-control" value="{{ $buscar['fecha_emision_hasta'] }}">
                                  </div>                                       
                                
                                  <div class="float-left  ml-3">
                                      <div class="font-weight-bold">Base Origen</div>
                                      <select name="base_origen_id" class="form-control select2" data-placeholder="Seleccione">
                                          <option label="Seleccione" value="">Seleccione</option>
                                          @foreach ($arr_base_origen as $base)
                                              <option value="{{$base->id}}" {{ ($base->id == $buscar["base_origen_id"]) ? 'selected' : ''}}>{{$base->nombre}}</option>
                                          @endforeach
                                      </select>
                                  </div>
                                  <div class="float-left ml-1">
                                      <div class="font-weight-bold">Base Destino</div>                                      
                                      <select name="base_destino_id" class="form-control select2" data-placeholder="Seleccione">
                                          <option label="Seleccione" value="">Seleccione</option>
                                          @foreach ($arr_base_destino as $base)
                                              <option value="{{$base->id}}" {{ ($base->id == $buscar["base_destino_id"]) ? 'selected' : ''}}>{{$base->nombre}}</option>
                                          @endforeach
                                      </select>
                                  </div>  
                                  <div class="float-left ml-1">
                                      <div class="font-weight-bold">Estado</div>
                                      <select name="estado" class="form-control select2" data-placeholder="Seleccione">
                                          <option label="Seleccione" value="">Seleccione</option>
                                          <option value="pendiente_parcial">Pendiente y Parcial</option> {{-- Se tendrían que llamar originalmente Pendientes y Parcial --}}
                                          @foreach ($arr_estados as $key => $estado)
                                              <option value="{{$key}}" {{ ($key == $buscar["estado"]) ? 'selected' : ''}}>{{$estado}}</option>
                                          @endforeach
                                      </select>
                                  </div>                                                                                                   
                                </div>                                                                                        
                              </div>                                
                              <div class="float-right">
                                <div class="row mb-2"> 
                                  <div class="float-right">   
                                    <div class="input-group d-flex flex-row-reverse">
                                        <div class="input-group-btn d-flex flex-row">                                            
                                            <button class="btn btn-primary btn-icon">
                                                <i class="fas fa-search"></i>
                                            </button>
                                            <a href="{{ route('admin.orden-transferencia.index') }}" class="btn btn-lighty btn-icon">
                                                <i class="fas fa-redo"></i>
                                            </a>
                                        </div>
                                    </div>
                                  </div>
                                </div>
                                <div class="row mt-5"> 
                                  <div class="float-right">
                                      <a href="{{ route('admin.orden-transferencia.create') }}" class="btn btn-primary btn-icon">
                                          <i class="fas fa-plus"></i> Generar Orden
                                      </a>                                    
                                  </div>
                                </div>
                              </div>                                 
                            </form>
                            <div class="clearfix mb-3"></div>   
                            <div class="row">
                              <div class="table-responsive">
                                  <table class="table table-striped">
                                      <thead>
                                          <tr>
                                              <th class="text-center">N° Orden</th>
                                              <th class="text-center">Fecha Emision</th>
                                              <th class="text-center">Base Origen</th>
                                              <th class="text-center">Base Destino</th>                                                                                                                                          
                                              <th class="text-center">Estado</th>
                                              <th class="text-center">Acciones</th>
                                          </tr>
                                      </thead>                                      
                                      <tbody >                                              
                                        @if($ordenes->total()>0)
                                          @foreach ($ordenes as $orden)                                                                                    
                                            <tr>
                                              <td class="text-center font-weight-bold">
                                                {{$orden->id}}
                                              </td>
                                              <td class="text-center">
                                                {{\Carbon\Carbon::parse($orden->fecha_emision)->format('d/m/Y H:i:s')}}
                                              </td>                                              
                                              <td class="text-center">
                                                @if($orden->base_origen)
                                                  {{$orden->base_origen->nombre}}
                                                @endif
                                              </td>
                                              <td class="text-center">
                                                @if($orden->base_destino)
                                                  {{$orden->base_destino->nombre}}
                                                @endif
                                              </td>
                                              <td class="text-center">
                                                @switch($orden->estado)
                                                  @case('abierta')
                                                    <span class="badge badge-info">{{strtoupper($orden->estado)}}</span>
                                                    @break
                                                  @case('aprobada')
                                                    <span class="badge badge-primary">{{strtoupper($orden->estado)}}</span>
                                                    @break
                                                  @case('parcial')
                                                    <span class="badge badge-warning">{{strtoupper($orden->estado)}}</span>
                                                    @break                                                    
                                                  @case('recibida')
                                                    <span class="badge badge-success">{{strtoupper($orden->estado)}}</span>
                                                    @break
                                                  @case('cerrada')
                                                    <span class="badge badge-secondary">{{strtoupper($orden->estado)}}</span>
                                                    @break                                                    
                                                  @case('cancelada')
                                                    <span class="badge badge-danger">{{strtoupper($orden->estado)}}</span>
                                                    @break                                                    
                                                @endswitch
                                              </td>                                              
                                              <td class="text-center">                                                    
                                                <a href="{{ route('admin.orden-transferencia.show', $orden->id) }}" class="btn btn-secondary" title="Ver">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                {{-- cerrar --}}
                                                @if($orden->estado == 'parcial')
                                                <button type="button" id="btn-cerrar-ot-{{ $orden->id }}" title="Cerrar"
                                                    class="btn btn-dark cerrar-orden-transferencia"
                                                    data-id="{{ $orden->id }}">
                                                    <i class="fas fa-lock"></i>
                                                </button>
                                            @endif 
                                              </td>                                          
                                            </tr>
                                          @endforeach
                                        @else
                                          <tr>
                                            <td colspan="7" class="text-center">No se encontraron resultados</td> 
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
    
@endsection

@include('admin.orden_transferencia.partials.modal_cerrar_otrans')

@section('scripts')
  <script>
    if ($('.cerrar-orden-transferencia').length > 0) {
    $('.cerrar-orden-transferencia').off().on('click', function() {
        var id = $(this).data('id');
        $('#btn-cerrar-ot-'+id).fireModal({
            title: 'Cerrar Orden de Transferencia',
            body: $('#modal-cerrar-ot').clone(),
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

