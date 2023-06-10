@extends('layouts.admin-master')

@section('title')
    Ordenes de compra
@endsection

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Orden de compra</h1>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Orden de compra</h4>
                        </div>
                        
                        <div class="card-body mb-3">                          
                          <div class="row">                                
                            <div class="col-md-4">       
                              <div class="table-responsive">
                                <table class="table">
                                  <tbody>
                                      <tr>
                                        <th class="text-left table-active">ID</th>
                                        <td class="text-left">
                                          <b>{{$orden->id}}</b>
                                        </td>
                                      </tr>
                                      <tr>
                                        <th class="text-left table-active">Fecha Emisión</th>
                                        <td class="text-left">
                                          {{\Carbon\Carbon::parse($orden->fecha_emision)->format('d/m/Y')}}
                                        </td>
                                      </tr>                   
                                      <tr>
                                        <th class="text-left table-active">Fecha Entrega</th>
                                        <td class="text-left">
                                          {{\Carbon\Carbon::parse($orden->fecha_entrega)->format('d/m/Y')}}
                                        </td>
                                      </tr>
                                      <tr>
                                        <th class="text-left table-active">Empresa</th>
                                        <td class="text-left">
                                          <b>{{$orden->empresa->nombre}}</b>
                                        </td>
                                      </tr>
                                  </tbody>
                                </table>
                              </div>      
                            </div>
                            <div class="col-md-4">       
                              <div class="table-responsive">
                                <table class="table">
                                  <tbody>
                                      <tr>
                                        <th class="text-left table-active">Base Emisora</th>
                                        <td class="text-left">
                                          {{$orden->base_emite->nombre}}
                                        </td>
                                      </tr>
                                      <tr>
                                        <th class="text-left table-active">Proveedor</th>
                                        <td class="text-left">
                                          {{$orden->proveedor->nombre}}
                                        </td>
                                      </tr>                   
                                      <tr>
                                        <th class="text-left table-active">Base Receptora</th>
                                        <td class="text-left">
                                          {{$orden->base_recibe->nombre}}
                                        </td>
                                      </tr>                                                                                    
                                  </tbody>
                                </table>
                              </div>      
                            </div>
                            <div class="col-md-4">       
                              <div class="table-responsive">
                                <table class="table">
                                  <tbody>
                                      <tr>
                                        <th class="text-left table-active">Estado</th>
                                          <td class="text-left">
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
                                              @case('anulada')
                                                <span class="badge badge-danger">{{strtoupper($orden->estado)}}</span>
                                                @break                                                    
                                            @endswitch
                                          </td>
                                      </tr>
                                      <tr>
                                        <th class="text-left table-active">Prioridad</th>
                                        <td class="text-left">
                                          @switch($orden->prioridad)
                                            @case('baja')
                                              <div class="text-warning font-weight-bold">
                                                {{strtoupper($orden->prioridad)}}
                                              </div>
                                              @break
                                            @case('normal')
                                              <div class="text-default font-weight-bold">
                                                {{strtoupper($orden->prioridad)}}
                                              </div>
                                              @break     
                                            @case('alta')
                                              <div class="text-danger font-weight-bold">
                                                {{strtoupper($orden->prioridad)}}
                                              </div>
                                              @break
                                          @endswitch                                          
                                        </td>
                                      </tr>                   
                                      <tr>
                                        <th class="text-left table-active">Observaciones</th>
                                        <td class="text-left">
                                          {{$orden->observacion}}
                                        </td>
                                      </tr>                                                                                    
                                  </tbody>
                                </table>
                              </div>      
                            </div>                                
                          </div>

                          <div class="row mt-3">
                            <div class="col-md-12">
                              <div class="table-responsive">
                                <table class="table table-striped">
                                  <thead>
                                    <tr>
                                      <th class="text-center">ID</th>
                                      <th class="text-center">Nro de Pieza</th>
                                      <th class="text-left">Descripción</th>
                                      <th class="text-right">Cantidad</th>
                                      <th class="text-right">Costo Unitario</th>
                                      <th class="text-right">Monto</th>                                                                            
                                    </tr>
                                  </thead>
                                  <tbody>
                                    @foreach($orden->detalle()->get() as $value)
                                      <tr>
                                        <td class="text-center">
                                          <b>{{$value->id}}</b>
                                        </td>
                                        <td class="text-center">
                                          {{$value->pieza->nro_pieza}}
                                        </td>
                                        <td class="text-left">
                                          {{$value->pieza->descripcion}}
                                        </td>                                        
                                        <td class="text-right">
                                          {{$value->cantidad}}
                                        </td>
                                        <td class="text-right">
                                          $ {{$value->costo}}
                                        </td>
                                        <td class="text-right">
                                          $ {{$value->cantidad * $value->costo}}
                                        </td>
                                      </tr>
                                    @endforeach

                                      <tr>
                                        <th colspan="5" class="text-right table-active">Total</th>
                                        <td class="text-right font-weight-bold">$ {{$orden->detalle->sum('monto')}}</td>
                                      </tr>
                                  </tbody>
                                </table>
                              </div>                              
                            </div>
                          </div>                                                      
                        </div>                      
                    </div>
                </div>
            </div>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card pb-5">
                        <div class="card-header">
                            <h4>Seguimientos Orden de Compra:</h4>
                        </div>

                        <div class="card-body">
                          <div class="row">
                            <div class="col-md-12">
                                @if(count($orden->accion) > 0)
                                  <ul style="list-style: none;">
                                    @foreach ($orden->accion as $value)
                                      @switch($value->tipo)
                                        @case('parcial')
                                          <li>
                                            <a style="position:relative; left:-2px; display:inline-block; width:15px" class="accordion-toggle collapsed 2" data-toggle="collapse" href="#multiCollapseIngresos-{{$value->id}}" role="button" aria-expanded="false" aria-controls="multiCollapseIngresos">
                                              <i class=" fas fa-caret-right mr-1 "></i>
                                            </a>
                                            {{ strtoupper($value->tipo) }} // {{ $value->user->name }} // {{$value->created_at}}
                                            <div class="collapse multi-collapse" id="multiCollapseIngresos-{{$value->id}}">
                                            <ul>
                                                @foreach ($value->movimiento as $movimientos)
                                                <li>
                                                  {{'Pieza: ' . $movimientos->pieza->nro_pieza . ' - ' . $movimientos->pieza->descripcion }} &nbsp
                                                  {{'Cantidad: ' . $movimientos->cantidad }} &nbsp
                                                </li>
                                                @endforeach
                                              </ul>
                                            </div>
                                          </li>
                                          @break
                                        @case('recibida')
                                          <li>
                                            <a style="position:relative; left:-2px; display:inline-block; width:15px" class="accordion-toggle collapsed 2" data-toggle="collapse" href="#multiCollapseIngresos-{{$value->id}}" role="button" aria-expanded="false" aria-controls="multiCollapseIngresos">
                                              <i class=" fas fa-caret-right mr-1 "></i>
                                            </a>
                                            {{ strtoupper($value->tipo) }} // {{ $value->user->name }} // {{$value->created_at}} &nbsp <i class="fas fa-long-arrow-alt-right"></i> &nbsp {{'Ingreso Completo'}}
                                            <div class="collapse multi-collapse" id="multiCollapseIngresos-{{$value->id}}">
                                              <ul>
                                                @foreach ($value->movimiento as $movimientos)
                                                <li>
                                                  {{'Pieza: ' . $movimientos->pieza->nro_pieza . ' - ' . $movimientos->pieza->descripcion }} &nbsp
                                                  {{'Cantidad: ' . $movimientos->cantidad }} &nbsp
                                                </li>
                                                @endforeach
                                              </ul>
                                            </div>
                                          </li>
                                          @break
                                        @default
                                          <li style="list-style: inside">
                                            <span style="padding-left: 2px;">
                                              {{ strtoupper($value->tipo) }} // {{ $value->user->name }} // {{$value->created_at}} &nbsp <i class="fas fa-long-arrow-alt-right"></i> &nbsp @if($value->observacion) {{ $value->observacion }} @else Sin comentarios. @endif
                                            </span>
                                          </li>
                                      @endswitch
                                    @endforeach
                                  </ul>
                                @else
                                  ¡No existen movimientos!
                                @endif
                              </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-footer text-left">
                            <a href="{{ route('admin.orden-compra.index') }}">
                                <button type="button" class="btn btn-secondary mr-1">Volver</button>
                            </a>
                        </div>          
                    </div>
                </div>
            </div>
        </div>
    </section>
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
    document.addEventListener('DOMContentLoaded',()=>{
      let toggles = [...document.querySelectorAll('.accordion-toggle')]
      toggles.map((toggle)=>{
        toggle.addEventListener('click', (event)=> {
          if (event.target.localName === 'i') {
            console.log('i');
            event.target.classList.toggle('fa-caret-down')
            event.target.classList.toggle('fa-caret-right')
          }else{
            console.log('a');
            event.target.firstElementChild.classList.toggle('fa-caret-down')
            event.target.firstElementChild.classList.toggle('fa-caret-right')
          }
        })
      })
    })
  </script>
@endsection