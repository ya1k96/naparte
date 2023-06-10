@extends('layouts.admin-master')

@section('title')
Listado de Próximos Mantenimientos
@endsection

@section('content')
<section class="section">
  <div class="section-header">
    <h1>Listado de Próximos Mantenimientos</h1>
  </div>
  <div class="section-body">
        <div class="section-title">
            <form action="{{ route('admin.ordenes-trabajo.create') }}" method="POST">
                @csrf
                <input type="hidden" name="dias_periodo" value="{{request()->query('dias') ? request()->query('dias') : 15}}">
                <a href="{{ route('admin.ordenes-trabajo.create') }}">
                    <button type="submit" class="btn btn-primary">Nueva orden de trabajo Correctiva</button>
                </a>
            </form>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="col-6">
                            <h4 class="col-6">Listado de Próximos Mantenimientos</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="" method="GET">
                            <div class="float-left">
                                <div class="form-inline">
                                    <div class="form-group mx-3">
                                        <label for="especialidad_id[]" class="mx-2">Filtrar por especialidad</label>
                                        <select name="especialidad_id[]" class="form-control form-control-lg select2 m-3 selector-especialidad" data-placeholder="Seleccionar una especialidad" multiple>
                                            <option value="">Seleccionar</option>
                                            @foreach ($especialidades as $especialidad)
                                                <option value="{{ $especialidad->id }}" {{(request()->query('especialidad_id') != null && in_array($especialidad->id, request()->query('especialidad_id'))) ? 'selected' : ''}}>{{ $especialidad->nombre }}</option>
                                            @endforeach
                                            <option label="Todas" value="todas" {{(request()->query('especialidad_id') != null && request()->query('especialidad_id')[0] == 'todas') ? 'selected' : ''}} {{request()->query('especialidad_id') == null ? 'selected' : ''}}>Todas</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="float-left">
                                <div class="form-inline">
                                    <div class="form-group mx-3">
                                        <label for="dias" class="mx-2">Filtrar período</label>
                                        <select name="dias" class="form-control select2 m-3" data-placeholder="Seleccionar un período">
                                            <option value="">Seleccionar</option>
                                            @foreach ($dias as $dia=>$nombre)
                                            <option value="{{ $dia }}" {{request()->query('dias') == $dia ? 'selected' : ''}} {{((request()->query('dias')) == null && ($dia == 15))? 'selected' : ''}}>{{ $dia . " días" }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="float-left">
                                <div class="form-inline">
                                    <div class="form-group mx-3">
                                        <div class="form-control-plaintext mx-3">Mostrando desde: {{\Carbon\Carbon::parse($fecha_hoy)->format('d/m/Y')}}, hasta: {{\Carbon\Carbon::parse($fecha_fin)->format('d/m/Y')}}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="float-right">
                                <div class="input-group d-flex flex-row-reverse">
                                    <div class="input-group-btn d-flex flex-row">
                                        <input type="search" name="buscar" class="form-control" placeholder="Buscar unidad" value="{{ $buscar }}" autocomplete="off">
                                        <button class="btn btn-primary btn-icon">
                                            <i class="fas fa-search"></i>
                                        </button>
                                        <a href="{{ route('admin.ordenes-trabajo.generadorOTS') }}" class="btn btn-lighty btn-icon">
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
                                    @forelse ($unidades_para_mantenimiento as $unidad)
                                    <tr class="text-center">
                                        <td>{{$unidad[0]->num_interno}}</td>
                                        {{-- <td>{{$unidad[0]->tipo_unidad}}</td> --}}
                                        <td>{{$unidad[0]->modelo->nombre}}</td>
                                        <td>{{$unidad[0]->num_serie}}</td>
                                        <td>{{$unidad[0]->tipo_vehiculo->nombre}}</td>
                                        <td>{{$unidad[0]->base_operacion->nombre}}</td>
                                        <td>
                                            <a class="btn btn-primary" data-toggle="collapse" href="#tareas_{{$unidad[0]->id}}" role="button" aria-expanded="false" aria-controls="tareas_{{$unidad[0]->id}}">
                                                Ver tareas
                                            </a>
                                        </td>
                                    @empty
                                        <td colspan="5" class="text-center">No hay OT pendientes de mantenimiento.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <form action="{{ route('admin.ordenes-trabajo.generarOT') }}" method="POST" id="submitOT">
            @csrf
            <input type="hidden" name="unidad_id" value="">
            <input type="hidden" name="fecha_fin" value="{{$fecha_fin}}">
            @foreach ($unidades_para_mantenimiento as $unidad_lista_tareas)
            <div class="collapse" id="tareas_{{$unidad_lista_tareas[0]->id}}">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="col-6">
                                    <h4 class="col-6">Tareas de la unidad {{$unidad_lista_tareas[0]->num_interno}} // {{$unidad_lista_tareas[0]->modelo->nombre}}</h4>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="clearfix mb-3"></div>
                                <div class="table-responsive table-invoice">
                                    <table class="table table-striped">
                                        <tbody>
                                            @if(!empty($unidad_lista_tareas['tareas'][0]))
                                            <tr class="text-center">
                                                <th>Seleccionar</th>
                                                <th>Tarea</th>
                                                <th>Especialidad</th>
                                                <th>Fecha Estimada</th>
                                            </tr>
                                            @foreach($unidad_lista_tareas['tareas'][0] as $tarea)
                                                <tr class="text-center">
                                                    <td>
                                                        <div class="form-check">
                                                            <input class="form-check-input" name="unidades[{{$unidad_lista_tareas[0]->id}}][tareas][{{$tarea['tarea']->id}}]" type="checkbox" value="" class="check_unidad_$unidad_lista_tareas[0]->id" checked>
                                                        </div>
                                                    </td>
                                                    <td>{{$tarea['tarea']->descripcion}}</td>
                                                    <td>{{$tarea['tarea']->especialidad->nombre}}</td>
                                                    <td>{{$tarea['fecha_estimada']}}</td>
                                                    <input type="hidden" name="unidades_tareas[{{$unidad_lista_tareas[0]->id}}][tareas][{{$tarea['tarea']->id}}][fecha_estimada]" value="{{$tarea['fecha_estimada']}}">
                                                </tr>
                                            @endforeach
                                            @else
                                            <tr>
                                                <td colspan="3">Todas las tareas de esta unidad ya tienen asignada una OT en este período.</td>
                                            </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @if(!empty($unidad_lista_tareas['tareas'][0]))
                            <div class="card-footer text-right">
                                <button type="button" class="btn btn-success generar" data-unidad-id="{{$unidad_lista_tareas[0]->id}}">Generar OT</button>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
            <button type="submit" class="d-none">Enviar</button>
        </form>
  </div>
</section>
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/ordenes_trabajo/generador_ot.js') }}"></script>
@endsection