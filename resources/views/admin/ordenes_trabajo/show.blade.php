@extends('layouts.admin-master')

@section('title')
    Ordenes de trabajo
@endsection

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Ordenes de trabajo</h1>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Ver orden de trabajo</h4>
                        </div>
                        <form action="{{ route('admin.ordenes-trabajo.update', $orden->id) }}" method="post" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                <div class="form-group row mb-4">
                                    <label for="user_id" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Usuario <code>*</code></label>
                                    <div class="col-sm-12 col-md-7">
                                        <input type="text" value="{{$orden->user->name}}" class="form-control" name="user_id" autocomplete="off" required readonly>
                                    </div>
                                </div>
                                <div class="form-group row mb-4">
                                    <label for="unidad_id" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Unidad <code>*</code></label>
                                    <div class="col-sm-12 col-md-7">
                                        <input type="text" value="{{$orden->unidad->num_interno}}" class="form-control" name="unidad_id" autocomplete="off" required readonly>
                                    </div>
                                </div>
                                @if ($orden->tipo_orden == 'Correctiva')
                                    <div class="form-group row mb-4">
                                        <label for="tarea_a_realizar" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Tarea a realizar <code>*</code></label>
                                        <div class="col-sm-12 col-md-7">
                                            <input type="text" value="{{$orden->tarea_a_realizar}}" class="form-control" name="tarea_a_realizar" autocomplete="off" required readonly>
                                        </div>
                                    </div>
                                @endif
                                @if ($orden->tipo_orden == 'Preventiva')
                                <div class="form-group row mb-4">
                                  <label for="especialidad" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Especialidad <code>*</code></label>                                    
                                  <div class="col-sm-12 col-md-7">
                                    @if(count($especialidades_todas) == count($orden->especialidad))
                                        <input type="text" value="Todas" class="form-control mb-1" autocomplete="off" readonly>                                        
                                    @else
                                        @foreach($orden->especialidad as $especialidad)                                                                                  
                                        <input type="text" value="{{$especialidad->nombre}}" class="form-control mb-1" autocomplete="off" readonly>                                        
                                        @endforeach
                                    @endif
                                  </div>                                    
                                </div>
                                @endif
                                <div class="form-group row mb-4">
                                  <label for="personal" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Personal <code>*</code></label>                                    
                                  <div class="col-sm-12 col-md-7">
                                    @foreach($orden->show_personal as $personal)                                                                                  
                                      <input type="text" value="{{$personal->nombre}} - {{$personal->especialidad->nombre}}" class="form-control mb-1" autocomplete="off" readonly>                                        
                                    @endforeach
                                  </div>                                    
                                </div>
                                <div class="form-group row mb-4">
                                    <label for="tipo_orden" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Tipo de Orden <code>*</code></label>
                                    <div class="col-sm-12 col-md-7">
                                        <input type="text" value="{{$orden->tipo_orden}}" class="form-control" name="tipo_orden" autocomplete="off" required readonly>
                                    </div>
                                </div>
                                <div class="form-group row mb-4">
                                    <label for="base_operacion_id" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Nombre de la base <code>*</code></label>
                                    <div class="col-sm-12 col-md-7">
                                        <input type="text" value="{{$orden->base_operacion->nombre}}" class="form-control" name="user_id" autocomplete="off" required readonly>
                                    </div>
                                </div>
                                <div class="form-group row mb-4">
                                    <label for="fecha_inicio_periodo" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Fecha Inicio Período</label>
                                    <div class="col-sm-12 col-md-7">
                                    <input type="date" value="{{$orden->fecha_inicio_periodo}}" name="fecha_inicio_periodo" class="form-control {{ $errors->has('fecha_inicio_periodo') ? ' is-invalid' : '' }}" readonly>
                                        <div class="invalid-feedback">
                                            {{ $errors->first('fecha_inicio_periodo') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row mb-4">
                                    <label for="fecha_fin_periodo" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Fecha Fin Período</label>
                                    <div class="col-sm-12 col-md-7">
                                    <input type="date" value="{{$orden->fecha_fin_periodo}}" name="fecha_fin_periodo" class="form-control {{ $errors->has('fecha_fin_periodo') ? ' is-invalid' : '' }}" readonly>
                                        <div class="invalid-feedback">
                                            {{ $errors->first('fecha_fin_periodo') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row mb-4">
                                    <label for="fecha_hora_inicio" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Hora inicio trabajo</label>
                                    <div class="col-sm-12 col-md-7">
                                    <input type="datetime-local" value="{{$orden->fecha_hora_inicio}}" name="fecha_hora_inicio" class="form-control {{ $errors->has('fecha_hora_inicio') ? ' is-invalid' : '' }}" readonly>
                                        <div class="invalid-feedback">
                                            {{ $errors->first('fecha_hora_inicio') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row mb-4">
                                    <label for="fecha_hora_fin" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Hora fin trabajo</label>
                                    <div class="col-sm-12 col-md-7">
                                    <input type="datetime-local" value="{{$orden->fecha_hora_fin}}" name="fecha_hora_fin" class="form-control {{ $errors->has('fecha_hora_fin') ? ' is-invalid' : '' }}" readonly>
                                        <div class="invalid-feedback">
                                            {{ $errors->first('fecha_hora_fin') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row mb-4">
                                    <label for="status" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Status <code>*</code></label>
                                    <div class="col-sm-12 col-md-7">
                                        <input type="text" name="status" class="form-control {{ $errors->has('status') ? ' is-invalid' : '' }}" value="Abierta" readonly>
                                        <div class="invalid-feedback">
                                            {{ $errors->first('status') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row mb-4">
                                    <label for="kilometraje" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Lectura </label>
                                    <div class="col-sm-12 col-md-7">
                                        <input type="number" name="kilometraje" value="{{$orden->kilometraje}}" id="kilometraje" class="form-control {{ $errors->has('kilometraje') ? ' is-invalid' : '' }}" readonly>
                                        <div class="invalid-feedback">
                                            {{ $errors->first('kilometraje') }}
                                        </div>
                                        <small class="kilometraje_anterior"></small>
                                    </div>
                                </div>
                                <div class="form-group row mb-4">
                                    <label for="revisado_por" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Revisado por</label>
                                    <div class="col-sm-12 col-md-7">
                                    <textarea name="revisado_por" class="form-control {{ $errors->has('revisado_por') ? ' is-invalid' : '' }}" style="height: 75px" readonly>{{$orden->revisado_por}}</textarea>
                                        <div class="invalid-feedback">
                                            {{ $errors->first('revisado_pnumeracor') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row mb-4">
                                    <label for="observaciones" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Observaciones</label>
                                    <div class="col-sm-12 col-md-7">
                                    <textarea name="observaciones" class="form-control {{ $errors->has('observaciones') ? ' is-invalid' : '' }}" style="height: 75px" readonly>{{$orden->observaciones}}</textarea>
                                        <div class="invalid-feedback">
                                            {{ $errors->first('observaciones') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row mb-4">
                                    <label for="comentario_mecanico" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Comentarios Mecánico</label>
                                    <div class="col-sm-12 col-md-7">
                                    <textarea name="comentario_mecanico" class="form-control {{ $errors->has('comentario_mecanico') ? ' is-invalid' : '' }}" style="height: 75px" readonly>{{$orden->comentario_mecanico}}</textarea>
                                        <div class="invalid-feedback">
                                            {{ $errors->first('comentario_mecanico') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row mb-4">
                                    <label for="url" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">OT Adjunta</label>
                                    <div class="col-sm-12 col-md-7">
                                        @if ($url)
                                        <a href="{{ route('admin.ordenes-trabajo.mostrarPDF', $orden->id) }}" class="btn btn-secondary">Ver OT</a>
                                        @else
                                        <p>No hay una OT cargada.</p>
                                        @endif
                                    </div>
                                </div>

                                @if ($orden->tipo_orden == 'Preventiva')
                                    <div class="card-header">
                                        <h4>Tareas</h4>
                                    </div>
                                    <div class="card-body">
                                        @foreach ($orden->tareas as $tarea)
                                            <div>
                                                <div class="form-group row mb-4">
                                                <label for="tareas[${tarea_id}][descripcion]" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Tarea <code>*</code></label>
                                                <div class="col-sm-12 col-md-7">
                                                    <input type="text" value="{{ $tarea->descripcion }}" class="form-control" name="tareas[${tarea_id}][descripcion]" autocomplete="off" required readonly>
                                                    <input type="hidden" value="{{ $tarea->id }}" class="form-control" name="tareas[${tarea_id}][id]" required>
                                                </div>
                                            </div>
                                            <div class="form-group row mb-4">
                                                <label for="tareas[${tarea_id}][especialidad_id]" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Especialidad <code>*</code></label>
                                                <div class="col-sm-12 col-md-7">
                                                    <input type="text" value="{{ $tarea->especialidad->nombre }}" class="form-control" name="tareas[${tarea_id}][especialidad_id]" autocomplete="off" required readonly>
                                                </div>
                                            </div>
                                            @if($tarea->observaciones)
                                            <div class="form-group row mb-4">
                                                <label for="tareas[${tarea_id}][observaciones]" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Procedimiento <code>*</code></label>
                                                <div class="col-sm-12 col-md-7">
                                                    <textarea class="form-control" name="tareas[${tarea_id}][observaciones]" style="height: 75px" readonly>{{ $tarea->observaciones ?? '-' }}</textarea>
                                                </div>
                                            </div>
                                            @endif
                                            <div class="form-group row mb-4">
                                                <label for="tareas[${tarea_id}][comentario]" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Comentario <code>*</code></label>
                                                <div class="col-sm-12 col-md-7">
                                                    <textarea class="form-control" name="tareas[${tarea_id}][comentario]" style="height: 75px" readonly>{{ $tarea->pivot->comentario ?? '' }}</textarea>
                                                </div>
                                                </div>
                                            </div>
                                            <div class="form-group row mb-4">
                                                <label for="tareas[${tarea_id}][personal][]" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Personal <code>*</code></label>                                    
                                                <div class="col-sm-12 col-md-7">
                                                    @foreach($tarea->personal as $personal_tarea)                                                                         
                                                    <input type="text" name="tareas[${tarea_id}][personal][$personal_tarea->id]" value="{{$personal_tarea->nombre}} - {{$personal_tarea->especialidad->nombre}}" class="form-control mb-1" autocomplete="off" readonly>                                        
                                                    @endforeach
                                                </div>                                    
                                            </div>
                                            <div class="form-group row mb-4">
                                                <label for="tareas[{{$tarea->id}}][fecha_realizacion]" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Fecha realización</label>
                                                <div class="col-sm-12 col-md-7">
                                                    <input type="date" value="{{ $tarea->pivot->fecha_realizacion }}" name="tareas[{{$tarea->id}}][fecha_realizacion]" class="form-control" readonly>
                                                </div>
                                            </div>
                                            <hr />
                                        @endforeach
                                    </div>
                                @endif

                            </div>
                            @if($orden->historiales)    
                                <div class="card-footer">
                                    <div class="card-text">
                                        Seguimiento de estado de la Orden de Trabajo
                                    </div>
                                    <ul>
                                            @foreach ($orden->historiales as $historial)
                                                    <li>{{$historial->status}} / {{Carbon\Carbon::parse($historial->fecha)->format('Y-m-d H:i')}} / {{$historial->user->name}}</li>
                                            @endforeach
                                    </ul>
                                </div>
                            @endif
                            <div class="card-footer text-right">
                                <a href="{{ route('admin.ordenes-trabajo') }}">
                                    <button type="button" class="btn btn-primary mr-1">Volver</button>
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
