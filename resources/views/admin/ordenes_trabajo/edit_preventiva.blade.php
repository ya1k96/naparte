@extends('layouts.admin-master')

@section('title')
Ordenes de trabajo
@endsection

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Órdenes de trabajo</h1>
    </div>
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <form action="{{ route('admin.ordenes-trabajo.updatePreventiva', $orden->id) }}" method="post" enctype="multipart/form-data">
                    @method('PATCH')
                    @csrf

                    <!--EDITAR ORDEN DE TRABAJO-->
                    <div class="card">
                        <div class="card-header">
                            <h4>Editar orden de trabajo Preventiva</h4>
                        </div>
                        <div class="card-body">
                            <input type="hidden" name="_token" value="{{ Session::token() }}">
                            <!-- UNIDAD / PERÍODO DE LA ORDEN / ESPECIALIDAD -->
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
                                <div class="col-md-12 d-md-flex justify-content-end">
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
                            <!-- BASE / LECTURA KILOMETRAJE -->
                            <div class="row mb-md-2">
                                <div class="col-md-6">
                                    <p for="base_operacion_id" class="form-label mb-0">
                                        Base:
                                    </p>
                                    <select name="base_operacion_id" id="base_operacion_id" data-placeholder="Selecciona una base de operación" class="form-control select2 {{ $errors->has('base_operacion_id') ? ' is-invalid' : '' }}" required>
                                        <option label="Selecciona una base de operación" value="">
                                            Selecciona una base de operación
                                        </option>
                                        @foreach ($bases_operaciones as $base)
                                        <option value="{{$base->id}}" {{ $orden->
                                            base_operacion_id == $base->id ? 'selected' : '' }}>{{$base->nombre}}
                                        </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">
                                        {{ $errors->first('base_operacion_id') }}
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="kilometraje" class="form-label">
                                        Lectura:
                                    </label>
                                    <input type="number" name="kilometraje" value="{{$orden->kilometraje}}" id="kilometraje" class="form-control {{ $errors->has('kilometraje') ? ' is-invalid' : '' }}">
                                    <div class="invalid-feedback">
                                        {{ $errors->first('kilometraje') }}
                                    </div>
                                    @if ($ultimo_historial)
                                    <small class="kilometraje_anterior">Kilometraje anterior: {{$ultimo_historial->kilometraje}} km</small>
                                    <input type="hidden" name="input_km_anterior" id="input_km_anterior" class="form-control" value="{{$ultimo_historial->kilometraje}}">
                                    @endif
                                    {{-- <small class="kilometraje_siguiente"></small>
                                    <input type="hidden" name="input_km_siguiente" id="input_km_siguiente" class="form-control"> --}}
                                    <div class="mt-1">
                                        <input type="hidden" id="historial-estado" class="estados" name="estado" value="">
                                        <a style="display: none;" href="#" id="historial-warning" class="btn btn-icon btn-warning" title="Supera el 30% del promedio"><i class="fas fa-exclamation-triangle"></i></a>
                                        <a style="display: none;" href="#" id="historial-mark" class="btn btn-icon btn-light">Marcar correcto</a>
                                        <a style="display: none;" href="#" id="historial-check" class="btn btn-icon btn-success"><i class="fas fa-check"></i></a>
                                        <a style="display: none;" href="#" id="historial-danger" class="btn btn-icon btn-danger" title="El kilometraje no puede ser menor al último ingresado ni mayor al siguiente."><i class="fas fa-times"></i></a>
                                    </div>
                                </div>
                            </div>
                            <!-- OBSERVACIONES / ADJUNTOS -->
                            <div class="row">
                                <div class="col-md-6 mb-2 mb-md-0">
                                    <label for="observaciones" class="form-label">
                                        Observaciones
                                    </label>
                                    <textarea name="observaciones" class="form-control {{ $errors->has('observaciones') ? ' is-invalid' : '' }}" style="height: 75px">
                                    {{ $orden->observaciones ?? '' }}
                                    </textarea>
                                    <div class="invalid-feedback">
                                        {{ $errors->first('observaciones') }}
                                    </div>
                                </div>
                                <div class="col-md-6 mt-md-4">
                                    <div class="row m-0 mb-2">
                                        @if($url)
                                        <label for="" class="col-md-3 pl-0">
                                            Archivo adjunto
                                        </label>
                                        <a href="{{ route('admin.ordenes-trabajo.mostrarPDF', $orden->id) }}" target="_blank" class="col-md-9 btn btn-secondary">
                                            Descargar archivo guardado
                                        </a>
                                    </div>
                                    <div class="row m-0">
                                        @endif
                                        <label for="url" class="col-md-3 pl-0">Adjuntar OT</label>
                                        <label class="col-md-9 btn btn-primary">
                                            Seleccionar archivo
                                            <input type="file" name="url" class="d-none">
                                        </label>
                                        <div class="col-12 d-flex justify-content-end pr-md-0">
                                            <div class="invalid-feedback">
                                                {{ $errors->first('url') }}
                                            </div>
                                            @if($url)
                                            <p class="text-muted">Se sobreescribirá el archivo anterior.</p>
                                            @else
                                            <p class="text-muted">No hay una OT cargada.</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--TAREAS DE LA ORDEN-->
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
                                    <p class="form-label mb-0">Fecha estimada</p>
                                    <span>{{ \Carbon\Carbon::parse($tarea->pivot->fecha_estimada)->format('d/m/Y') }}</span>
                                    <input type="hidden" name="tareas[{{$tarea->id}}][fecha_estimada]" value="{{$tarea->pivot->fecha_estimada}}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Fecha realización</label>
                                    <input type="date" value="{{ $tarea->pivot->fecha_realizacion }}" name="tareas[{{$tarea->id}}][fecha_realizacion]" class="form-control mb-3 {{($k == 0) ? "fecha-realizacion-1" : ''}} insertar-fecha">
                                    @if ($k == 0)
                                    <button id="replicar-fechas" type="button" class="btn btn-primary w-100">Replicar en todas las fechas</button>
                                    @endif
                                </div>
                                @if($tarea->personal)
                                <div class="col-md-3">
                                    <p for="tareas[{{$tarea->id}}][personal][]" class="form-label mb-0">Personal</p>
                                    <select name="tareas[{{$tarea->id}}][personal][]" class="form-control select2" data-placeholder="Selecciona un personal" multiple>
                                        @foreach ($arrPersonal as $personal)
                                        @if($tarea->showPersonal)
                                        @if( in_array($personal->id,$tarea->showPersonal) )
                                        <option value="{{$personal->id}}" selected>{{$personal->nombre}} - {{$personal->especialidad->nombre}}</option>
                                        @elseif($personal->especialidad->nombre == $tarea->especialidad->nombre)
                                        <option value="{{$personal->id}}">{{$personal->nombre}} - {{$personal->especialidad->nombre}}</option>
                                        @endif
                                        @elseif($personal->especialidad->nombre == $tarea->especialidad->nombre)
                                        <option value="{{$personal->id}}">{{$personal->nombre}} - {{$personal->especialidad->nombre}}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="tareas[{{$tarea->id}}][comentario]" class="form-label">Comentario</label>
                                    <textarea class="form-control" name="tareas[{{$tarea->id}}][comentario]" style="height: 75px">{{ $tarea->pivot->comentario ?? '' }}</textarea>
                                </div>
                                <hr />
                            </div>
                            @endif
                            @endforeach
                        </div>
                        <div class="card-footer text-right">
                            <a href="{{ route('admin.ordenes-trabajo') }}">
                                <button type="button" class="btn btn-secondary mr-1">Cancelar</button>
                            </a>
                            <button type="submit" id="submit" class="btn btn-primary">Guardar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script src="{{ asset('assets/js/ordenes_trabajo/index.js') }}"></script>
@endsection