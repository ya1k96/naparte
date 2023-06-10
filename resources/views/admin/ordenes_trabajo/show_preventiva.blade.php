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
                        <h4>Editar orden de trabajo Preventiva</h4>
                    </div>
                    <div class="card-body">
                        <input type="hidden" name="_token" value="{{ Session::token() }}">
                        <!-- UNIDAD / PERÍODO DE LA ORDEN / BASE / ESPECIALIDAD -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p for="unidad_id">
                                    Unidad:
                                    <span>{{$orden->unidad->num_interno}}</span>
                                    <input class="buscar_tareas" type="hidden" id="unidad_id" name="unidad_id" value="{{$orden->unidad_id}}">
                                </p>
                            </div>
                            <div class="col-md-6 d-md-flex justify-content-end">
                                <p for="fecha_periodo_orden">
                                    Período de la orden:
                                    <span id="fecha_periodo_orden">
                                        {{ \Carbon\Carbon::parse($orden->fecha_inicio_periodo)->format('d/m/Y').' - '.\Carbon\Carbon::parse($orden->fecha_fin_periodo)->format('d/m/Y') }}
                                    </span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <label for="base_operacion_id" class="form-label">Base:</label>
                                <span>{{$orden->base_operacion->nombre}}</span>
                            </div>
                            <div class="col-md-6 d-md-flex justify-content-end">
                                <p for="especialdiades">Especialidad:
                                    <span id="especialdiades">
                                        @foreach ($especialidades as $i=>$especialidad)
                                        @if ($i == 0)
                                        {{$especialidad->nombre}}
                                        @else
                                        / {{$especialidad->nombre}}
                                        @endif
                                        @endforeach
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <label for="kilometraje" class="form-label">Lectura </label>
                                <input type="number" name="kilometraje" readonly value="{{$orden->kilometraje}}" id="kilometraje" class="form-control {{ $errors->has('kilometraje') ? ' is-invalid' : '' }}">
                            </div>
                            <div class="col-md-3">
                                @if($url)
                                <label for="" class="form-label">Archivo adjunto</label>
                                <a href="{{ route('admin.ordenes-trabajo.mostrarPDF', $orden->id) }}" target="_blank" class="btn btn-secondary w-100 py-2">
                                    Descargar archivo guardado
                                </a>
                                @else
                                <label for="" class="form-label">Archivo adjunto</label>
                                <p>No hay archivos adjuntos.</p>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label for="observaciones" class="form-label">Observaciones</label>
                                <textarea name="observaciones" readonly class="form-control {{ $errors->has('observaciones') ? ' is-invalid' : '' }}" style="height: 75px;">
                                {{ $orden->observaciones ?? '' }}
                                </textarea>
                                <div class="invalid-feedback">
                                    {{ $errors->first('observaciones') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4>Tareas de la orden</h4>
                    </div>
                    <div class="card-body">
                        @foreach ($orden->tareas as $k => $tarea)
                        <div class="row mb-md-5">
                            <div class="col-12">
                                <h6>{{$tarea->descripcion}} // {{$tarea->componente->nombre}} {{($tarea->componente->showPadre)? ' // ' . $tarea->componente->showPadre->nombre : ''}}</h6>
                                <input type="hidden" value="{{ $tarea->id }}" class="form-control" name="tareas[{{$tarea->id}}][id]" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Fecha estimada</label>
                                <p>{{ \Carbon\Carbon::parse($tarea->pivot->fecha_estimada)->format('d/m/Y') }}</p>
                                <input type="hidden" name="tareas[{{$tarea->id}}][fecha_estimada]" value="{{$tarea->pivot->fecha_estimada}}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Fecha realización</label>
                                <input type="date" readonly value="{{ $tarea->pivot->fecha_realizacion }}" name="tareas[{{$tarea->id}}][fecha_realizacion]" class="form-control insertar-fecha">
                            </div>
                            @if(count($tarea->personal) > 0)
                            <div class="col-md-3">
                                <label for="tareas[{{$tarea->id}}][personal][]" class="form-label">Personal</label>
                                @foreach ($arrPersonal as $personal)
                                @if(count($tarea->showPersonal) > 0)
                                @if( in_array($personal->id,$tarea->showPersonal) )
                                <ul>
                                    <li>
                                        <span class="form-control-plaintext">
                                            {{$personal->nombre}} - {{$personal->especialidad->nombre}}
                                        </span>
                                    </li>
                                </ul>
                                @endif
                                @else
                                <p>No hay personal asignado.</p>
                                @endif
                                @endforeach
                            </div>
                            @else
                            <div class="col-md-3">
                                <label class="form-label">Personal</label>
                                <p>No hay personal asignado.</p>
                            </div>
                            @endif
                            <div class="col-md-4">
                                <label for="tareas[{{$tarea->id}}][comentario]" class="form-label">Comentario</label>
                                <textarea class="form-control" readonly name="tareas[{{$tarea->id}}][comentario]" style="height: 75px">
                                {{ $tarea->pivot->comentario ?? '' }}
                                </textarea>
                            </div>
                            <hr />
                        </div>
                        @endforeach
                    </div>
                    <div class="card-footer text-right">
                        <a href="{{ route('admin.ordenes-trabajo') }}">
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
<script src="{{ asset('assets/js/ordenes_trabajo/index.js') }}">
</script>
@endsection