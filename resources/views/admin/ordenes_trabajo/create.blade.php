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
                            <h4>Nueva orden de trabajo Correctiva</h4>
                        </div>
                        <form action="{{ route('admin.ordenes-trabajo.store') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                            <input type="hidden" name="_token" value="{{ Session::token() }}">
                                <div class="form-group row mb-4">
                                    <label for="user_id" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Generó <code>*</code></label>
                                    <div class="col-sm-12 col-md-7">
                                        <input type="text" value="{{ $user->name }}" class="form-control {{ $errors->has('user_name') ? ' is-invalid': '' }}" name="user_name" autocomplete="off" required readonly>
                                        <input type="hidden" value="{{ $user->id }}" class="form-control {{ $errors->has('user_id') ? ' is-invalid': '' }}" name="user_id" autocomplete="off" required readonly>
                                        <div class="invalid-feedback">
                                            {{ $errors->first('user_id') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row mb-4">
                                    <label for="unidad_id" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Unidad <code>*</code></label>
                                    <div class="col-sm-12 col-md-7">
                                        <div class="input-group">
                                            <select name="unidad_id" id="unidad_id" class="form-control select2 {{ $errors->has('unidad_id') ? ' is-invalid' : '' }}" data-placeholder="Selecciona una unidad" required>
                                                <option label="Selecciona una unidad" value="">Selecciona una unidad</option>
                                                @foreach ($unidades as $unidad)
                                                    <option value="{{$unidad->id}}" {{ (old('unidad_id') == $unidad->id) ? 'selected' : '' }}>{{$unidad->num_interno}}</option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback">
                                                {{ $errors->first('unidad_id') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row mb-4">
                                    <label for="tarea_a_realizar" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Tarea a realizar <code>*</code></label>
                                    <div class="col-sm-12 col-md-7">
                                        <input type="text" value="{{ (old('tarea_a_realizar')) ?? '' }}" class="form-control {{ $errors->has('tarea_a_realizar') ? ' is-invalid': '' }}" name="tarea_a_realizar" autocomplete="off" required>
                                        <div class="invalid-feedback">
                                            {{ $errors->first('tarea_a_realizar') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row mb-4">
                                    <label for="personal" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Personal</label>                                    
                                    <div class="col-sm-12 col-md-7">

                                        <select name="personal[]" id="personal" class="form-control select2 {{ $errors->has('personal') ? ' is-invalid' : '' }}" data-placeholder="Selecciona un personal" multiple>
                                            <option label="Selecciona un personal" value="">Selecciona un personal</option>
                                            @foreach ($arrPersonal as $personal)
                                                <option value="{{$personal->id}}">{{$personal->nombre}} - {{$personal->especialidad->nombre}}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback">
                                            {{ $errors->first('personal') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row mb-4">
                                    <label for="tipo_orden" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Tipo de Orden <code>*</code></label>
                                    <div class="col-sm-12 col-md-7">
                                        <div class="input-group">
                                            <select name="tipo_orden" id="tipo_orden" class="form-control select2 {{ $errors->has('tipo_orden') ? ' is-invalid' : '' }}" data-placeholder="Selecciona un tipo de orden" required>
                                                <option label="Selecciona un tipo de orden" value="">Selecciona un tipo de orden</option>
                                                <option value="Correctiva" selected>Correctiva</option>
                                            </select>
                                            <div class="invalid-feedback">
                                                {{ $errors->first('tipo_orden') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row mb-4">
                                    <label for="base_operacion_id" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Nombre de la base <code>*</code></label>
                                    <div class="col-sm-12 col-md-7">
                                        <div class="input-group">
                                            <select name="base_operacion_id" id="base_operacion_id" class="form-control select2 {{ $errors->has('base_operacion_id') ? ' is-invalid' : '' }}" data-placeholder="Selecciona una base de operación" required>
                                                <option label="Selecciona una base de operación" value="">Selecciona una base de operación</option>
                                                @foreach ($bases_operaciones as $base)
                                                    <option value="{{$base->id}}" {{ (old('base_operacion_id') == $base->id) ? 'selected' : '' }}>{{$base->nombre}}</option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback">
                                                {{ $errors->first('base_operacion_id') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row mb-4">
                                    <label for="fecha_inicio_periodo" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Fecha Inicio Período</label>
                                    <div class="col-sm-12 col-md-7">
                                    <input type="date" value="{{ $fecha_hoy }}" name="fecha_inicio_periodo" class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="form-group row mb-4">
                                    <label for="fecha_fin_periodo" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Fecha Fin Período</label>
                                    <div class="col-sm-12 col-md-7">
                                    <input type="date" value="{{ $fecha_fin }}" name="fecha_fin_periodo" class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="form-group row mb-4">
                                    <label for="fecha_hora_recepcion" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Fecha y Hora de recepción</label>
                                    <div class="col-sm-12 col-md-7">
                                    <input type="datetime-local" value="{{ (old('fecha_hora_recepcion')) ?? '' }}" name="fecha_hora_recepcion" class="form-control {{ $errors->has('fecha_hora_recepcion') ? ' is-invalid' : '' }}">
                                        <div class="invalid-feedback">
                                            {{ $errors->first('fecha_hora_recepcion') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row mb-4">
                                    <label for="fecha_hora_devolucion" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Fecha y Hora de devolución</label>
                                    <div class="col-sm-12 col-md-7">
                                    <input type="datetime-local" value="{{ (old('fecha_hora_devolucion')) ?? '' }}" name="fecha_hora_devolucion" class="form-control {{ $errors->has('fecha_hora_devolucion') ? ' is-invalid' : '' }}">
                                        <div class="invalid-feedback">
                                            {{ $errors->first('fecha_hora_devolucion') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row mb-4">
                                    <label for="fecha_hora_inicio" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Fecha y hora inicio trabajo</label>
                                    <div class="col-sm-12 col-md-7">
                                    <input type="datetime-local" id="fecha-inicio" value="{{ (old('fecha_hora_inicio')) ?? '' }}" name="fecha_hora_inicio" class="form-control {{ $errors->has('fecha_hora_inicio') ? ' is-invalid' : '' }}">
                                        <div class="invalid-feedback">
                                            {{ $errors->first('fecha_hora_inicio') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row mb-4">
                                    <label for="fecha_hora_fin" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Fecha y hora fin trabajo</label>
                                    <div class="col-sm-12 col-md-7">
                                    <input type="datetime-local" value="{{ (old('fecha_hora_fin')) ?? '' }}" name="fecha_hora_fin" class="form-control {{ $errors->has('fecha_hora_fin') ? ' is-invalid' : '' }}">
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
                                    <label for="revisado_por" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Revisado por</label>
                                    <div class="col-sm-12 col-md-7">
                                    <textarea name="revisado_por" class="form-control {{ $errors->has('revisado_por') ? ' is-invalid' : '' }}" style="height: 75px">{{ (old('revisado_por')) ?? '' }}</textarea>
                                        <div class="invalid-feedback">
                                            {{ $errors->first('revisado_por') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row mb-4">
                                    <label for="observaciones" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Observaciones</label>
                                    <div class="col-sm-12 col-md-7">
                                    <textarea name="observaciones" class="form-control {{ $errors->has('observaciones') ? ' is-invalid' : '' }}" style="height: 75px">{{ (old('observaciones')) ?? '' }}</textarea>
                                        <div class="invalid-feedback">
                                            {{ $errors->first('observaciones') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row mb-4">
                                    <label for="url" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Adjuntar OT</label>
                                    <div class="col-sm-12 col-md-7">
                                    <input type="file" name="url" class="form-control">
                                        <div class="invalid-feedback">
                                            {{ $errors->first('url') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-right">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" value="true" name="imprimir" checked id="imprimir">
                                    <label class="form-check-label" for="imprimir">
                                      Imprimir OT
                                    </label>
                                </div>
                                <a href="{{ route('admin.ordenes-trabajo') }}">
                                    <button type="button" class="btn btn-secondary mr-1">Cancelar</button>
                                </a>
                                <button type="submit" id="submit" class="btn btn-primary">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/ordenes_trabajo/index.js') }}"></script>
@endsection