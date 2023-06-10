@extends('layouts.admin-master')

@section('title')
Editar Unidad
@endsection

@section('content')
<section class="section">
  <div class="section-header">
    <h1>Editar Unidad</h1>
  </div>
  <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="col-6">Editar unidad</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action=" {{ route('admin.unidades.update', $unidad->id) }} ">
                            @csrf
                            @method('PUT')
                            <input class="form-control" type="hidden" id="id" name="id" value="{{$unidad->id}}" >
                            <div class="form-group row mb-4">
                                <label for="num_interno" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">N° Interno*</label>
                                <div class="col-sm-12 col-md-7">
                                    <div class="input-group">
                                        <input class="form-control {{ $errors->has('num_interno') ? ' is-invalid' : '' }}" type="number" id="num_interno" name="num_interno" min="0" max="99999" value="{{$unidad->num_interno}}" >
                                        <div class="invalid-feedback">
                                            {{ $errors->first('num_interno') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="form-group row mb-4">
                                <label for="tipo_unidad" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Tipo Unidad*</label>
                                <div class="col-sm-12 col-md-7">
                                    <div class="input-group">
                                        <select name="tipo_unidad" id="tipo_unidad" class="form-control select2 {{ $errors->has('tipo_unidad') ? ' is-invalid' : '' }}" data-placeholder="Seleccione un tipo de unidad" >
                                            <option label="Selecciona un tipo de unidad" value="">Selecciona un tipo de unidad</option>
                                            @foreach ($tipo_unidades as $tipo_unidad)
                                                <option value="{{$tipo_unidad}}" {{($tipo_unidad == $unidad->tipo_unidad)? 'selected' : ''}}>{{$tipo_unidad}}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback">
                                            {{ $errors->first('tipo_unidad') }}
                                        </div>
                                    </div>
                                </div>
                            </div> --}}
                            <div class="form-group row mb-4">
                                <label for="marca" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Marca*</label>
                                <div class="col-sm-12 col-md-7">
                                    <div class="input-group">
                                        <select name="marca_id" id="marca_id" class="form-control select2 {{ $errors->has('marca_id') ? ' is-invalid' : '' }}" data-placeholder="Seleccione una marca" >
                                            <option label="Selecciona una marca" value="">Selecciona una marca</option>
                                            @foreach ($marcas as $marca)
                                                <option value="{{$marca->id}}" {{($marca->id == $unidad->modelo->marca_id)? 'selected' : ''}}>{{$marca->nombre}}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback">
                                            {{ $errors->first('marca_id') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label for="modelo" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Modelo*</label>
                                <div class="col-sm-12 col-md-7">
                                    <div class="input-group">
                                        <select name="modelo_id" id="modelo_id" class="form-control select2 {{ $errors->has('modelo_id') ? ' is-invalid' : '' }}" data-placeholder="Seleccione un modelo" >
                                            <option label="Selecciona un modelo" value="">Selecciona un modelo</option>
                                            @foreach ($modelos_por_marca as $modelo)
                                                <option value="{{$modelo->id}}" {{($modelo->id == $unidad->modelo->id)? 'selected' : ''}}>{{$modelo->nombre}}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback">
                                            {{ $errors->first('modelo_id') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label for="num_serie" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">N° Serie*</label>
                                <div class="col-sm-12 col-md-7">
                                    <div class="input-group">
                                        <input class="form-control {{ $errors->has('num_serie') ? ' is-invalid' : '' }}" type="text" id="num_serie" name="num_serie" value="{{$unidad->num_serie}}" >
                                        <div class="invalid-feedback">
                                            {{ $errors->first('num_serie') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label for="num_motor" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">N° Motor*</label>
                                <div class="col-sm-12 col-md-7">
                                    <div class="input-group">
                                        <input class="form-control {{ $errors->has('num_motor') ? ' is-invalid' : '' }}" type="text" id="num_motor" name="num_motor"  value="{{$unidad->num_motor}}">
                                        <div class="invalid-feedback">
                                            {{ $errors->first('num_motor') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label for="dominio" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Dominio*</label>
                                <div class="col-sm-12 col-md-7">
                                    <div class="input-group">
                                        <input class="form-control {{ $errors->has('dominio') ? ' is-invalid' : '' }}" type="text" id="dominio" name="dominio"  value="{{$unidad->dominio}}">
                                        <div class="invalid-feedback">
                                            {{ $errors->first('dominio') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label for="carroceria" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Carrocería*</label>
                                <div class="col-sm-12 col-md-7">
                                    <div class="input-group">
                                        <select name="carroceria_id" id="carroceria_id" class="form-control select2 {{ $errors->has('carroceria_id') ? ' is-invalid' : '' }}" data-placeholder="Seleccione una carrocería" >
                                            <option label="Selecciona una carrocería" value="">Selecciona una carrocería</option>
                                            @foreach ($carrocerias as $carroceria)
                                                <option value="{{$carroceria->id}}" {{($carroceria->id == $unidad->carroceria->id)? 'selected' : ''}}>{{$carroceria->nombre}}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback">
                                            {{ $errors->first('carroceria_id') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label for="cantidad_asientos" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Cantidad de asientos*</label>
                                <div class="col-sm-12 col-md-1">
                                    <div class="input-group">
                                        <input class="form-control {{ $errors->has('cantidad_asientos') ? ' is-invalid' : '' }}" type="number" id="cantidad_asientos" name="cantidad_asientos" min="0"  value="{{$unidad->cantidad_asientos}}">
                                        <div class="invalid-feedback">
                                            {{ $errors->first('cantidad_asientos') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label for="carroceria" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Marca de Aire Acond.*</label>
                                <div class="col-sm-12 col-md-7">
                                    <div class="input-group">
                                        <select name="aire_acondicionado_id" id="aire_acondicionado_id" class="form-control select2 {{ $errors->has('aire_acondicionado_id') ? ' is-invalid' : '' }}" data-placeholder="Seleccione una marca de aire acondicionado" >
                                            <option label="Selecciona una marca de aire acondicionado" value="">Selecciona una marca de aire acondicionado</option>
                                            @foreach ($aires_acondicionados as $aire_acondicionado)
                                                <option value="{{$aire_acondicionado->id}}" {{($aire_acondicionado->id == $unidad->aire_acondicionado->id)? 'selected' : ''}}>{{$aire_acondicionado->nombre}}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback">
                                            {{ $errors->first('aire_acondicionado_id') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label for="puesta_servicio" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Fecha Puesta en Servicio</label>
                                <div class="col-sm-12 col-md-7">
                                    <div class="input-group">
                                        <input class="form-control {{ $errors->has('puesta_servicio') ? ' is-invalid' : '' }}" type="date" id="puesta_servicio" name="puesta_servicio" value="{{$unidad->puesta_servicio}}">
                                        <div class="invalid-feedback">
                                            {{ $errors->first('puesta_servicio') }}
                                        </div>
                                    </div>
                                    <small class="d-flex flex-row-reverse">Al hacer click en el calendario se despliega el selector de fechas.</small>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label for="carroceria" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Tipo de Vehículo*</label>
                                <div class="col-sm-12 col-md-7">
                                    <div class="input-group">
                                        <select name="tipo_vehiculo_id" id="tipo_vehiculo_id" class="form-control select2 {{ $errors->has('tipo_vehiculo_id') ? ' is-invalid' : '' }}" data-placeholder="Selecciona tipo de vehiculo" >
                                            <option label="Selecciona tipo de vehiculo" value="">Selecciona tipo de vehiculo</option>
                                            @foreach ($tipos_vehiculos as $tipo_vehiculo)
                                                <option value="{{$tipo_vehiculo->id}}" {{($tipo_vehiculo->id == $unidad->tipo_vehiculo->id)? 'selected' : ''}}>{{$tipo_vehiculo->nombre}}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback">
                                            {{ $errors->first('tipo_vehiculo_id') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label for="motor" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Motor*</label>
                                <div class="div col-sm-12 col-md-7">
                                    <div class="form-check my-1">
                                        <input class="form-check-input" type="radio" name="motor" id="motor1" value="Delantero" {{($unidad->motor == 'Delantero')? 'checked' : ''}}>
                                        <label class="form-check-label" for="motor1">
                                          Delantero
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="motor" id="motor2" value="Trasero" {{($unidad->motor == 'Trasero')? 'checked' : ''}}>
                                        <label class="form-check-label" for="motor2">
                                          Trasero
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label for="carroceria" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Asignado a Base*</label>
                                <div class="col-sm-12 col-md-7">
                                    <div class="input-group">
                                        <select name="base_operacion_id" id="base_operacion_id" class="form-control select2 {{ $errors->has('base_operacion_id') ? ' is-invalid' : '' }}" data-placeholder="Selecciona base de operación" >
                                            <option label="Selecciona base de operación" value="">Selecciona base de operación</option>
                                            @foreach ($bases_operaciones as $base_operacion)
                                                <option value="{{$base_operacion->id}}" {{($base_operacion->id == $unidad->base_operacion->id)? 'selected' : ''}}>{{$base_operacion->nombre}}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback">
                                            {{ $errors->first('base_operacion_id') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label for="observaciones" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Observaciones</label>
                                <div class="col-sm-12 col-md-7">
                                    <div class="input-group">
                                        <textarea style="height: 100px" class="form-control {{ $errors->has('observaciones') ? ' is-invalid' : '' }}" id="observaciones" name="observaciones">{{$unidad->observaciones}}</textarea>
                                        <div class="invalid-feedback">
                                            {{ $errors->first('observaciones') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label for="" class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                                <div class="col-sm-12 col-md-7">
                                    <div class="input-group">
                                        <button type="submit" action="{{ route('admin.unidades.update', $unidad->id) }}" class="btn btn-primary">Guardar</button>
                                        <a href="{{ route('admin.unidades') }}" class="btn btn-light mx-1">Cancelar</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
  </div>
</section>
@endsection

@section('scripts')
    <script>
        let el = document.getElementById('num_interno');
        let updatetext = function() {
            el.value = ('00000' + el.value).slice(-5);
        }

        el.addEventListener("keyup", updatetext , false);
    </script>
@endsection
