@extends('layouts.admin-master')

@section('title')
Agregar Unidad
@endsection

@section('content')
<section class="section">
  <div class="section-header">
    <h1>Agregar Unidad</h1>
  </div>
  <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="col-6">Agregar unidad</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action=" {{ route('admin.unidades.store') }} ">
                            @csrf
                            <div class="form-group row mb-4">
                                <label for="num_interno" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">N° Interno <code>*</code></label>
                                <div class="col-sm-12 col-md-7">
                                    <div class="input-group">
                                        <input class="form-control {{ $errors->has('num_interno') ? ' is-invalid' : '' }}" type="number" id="num_interno" value="{{ old('num_interno') }}" name="num_interno">
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
                                                <option value="{{$tipo_unidad}}">{{$tipo_unidad}}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback">
                                            {{ $errors->first('tipo_unidad') }}
                                        </div>
                                    </div>
                                </div>
                            </div> --}}
                            <div class="form-group row mb-4">
                                <label for="marca" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Marca <code>*</code></label>
                                <div class="col-sm-12 col-md-7">
                                    <div class="input-group">
                                        <select name="marca_id" id="marca_id" class="form-control select2 {{ $errors->has('marca_id') ? ' is-invalid' : '' }}" data-placeholder="Seleccione una marca" >
                                            <option label="Selecciona una marca" value="">Selecciona una marca</option>
                                            @foreach ($marcas as $marca)
                                                <option value="{{$marca->id}}" {{ (old('marca_id') == $marca->id) ? 'selected' : '' }}>{{$marca->nombre}}</option>
                                            @endforeach
                                        </select>
                                        <div class="input-group-append">
                                            <button class="btn btn-primary trigger--fire-modal-5" type="button" id="marca-modal">Agregar nueva</button>
                                        </div>
                                        <div class="invalid-feedback">
                                            {{ $errors->first('marca_id') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label for="modelo" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Modelo <code>*</code></label>
                                <div class="col-sm-12 col-md-7">
                                    <div class="input-group">
                                        <select name="modelo_id" id="modelo_id" class="form-control select2 {{ $errors->has('modelo_id') ? ' is-invalid' : '' }}" data-placeholder="Selecciona un modelo" >
                                            <option label="Selecciona un modelo" value="">Selecciona un modelo</option>
                                            @foreach ($modelos as $modelo)
                                                <option value="{{$modelo->id}}" {{ (old('modelo_id') == $modelo->id) ? 'selected' : '' }}>{{$modelo->nombre}}</option>
                                            @endforeach
                                        </select>
                                        <div class="input-group-append">
                                            <button class="btn btn-primary trigger--fire-modal-5" type="button" id="modelo-modal">Agregar nuevo</button>
                                        </div>
                                        <div class="invalid-feedback">
                                            {{ $errors->first('modelo_id') }}
                                        </div>
                                    </div>
                                    <small>Selecciona una marca primero para ver sus modelos.</small>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label for="num_serie" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">N° Serie <code>*</code></label>
                                <div class="col-sm-12 col-md-7">
                                    <div class="input-group">
                                        <input class="form-control {{ $errors->has('num_serie') ? ' is-invalid' : '' }}" type="text" id="num_serie" name="num_serie" value="{{ old('num_serie') }}">
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
                                        <input class="form-control {{ $errors->has('num_motor') ? ' is-invalid' : '' }}" type="text" id="num_motor" name="num_motor" value="{{ old('num_motor') }}">
                                        <div class="invalid-feedback">
                                            {{ $errors->first('num_motor') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label for="dominio" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Dominio <code>*</code></label>
                                <div class="col-sm-12 col-md-7">
                                    <div class="input-group">
                                        <input class="form-control {{ $errors->has('dominio') ? ' is-invalid' : '' }}" type="text" id="dominio" name="dominio" value="{{ old('dominio') }}">
                                        <div class="invalid-feedback">
                                            {{ $errors->first('dominio') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label for="carroceria" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Carrocería <code>*</code></label>
                                <div class="col-sm-12 col-md-7">
                                    <div class="input-group">
                                        <select name="carroceria_id" id="carroceria_id" class="form-control select2 {{ $errors->has('carroceria_id') ? ' is-invalid' : '' }}" data-placeholder="Seleccione una carrocería" >
                                            <option label="Selecciona una carrocería" value="">Selecciona una carrocería</option>
                                            @foreach ($carrocerias as $carroceria)
                                                <option value="{{$carroceria->id}}" {{ (old('carroceria_id') == $carroceria->id) ? 'selected' : '' }}>{{$carroceria->nombre}}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback">
                                            {{ $errors->first('carroceria_id') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label for="cantidad_asientos" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Cantidad de asientos <code>*</code></label>
                                <div class="col-sm-12 col-md-1">
                                    <div class="input-group">
                                        <input class="form-control {{ $errors->has('cantidad_asientos') ? ' is-invalid' : '' }}" type="number" id="cantidad_asientos" name="cantidad_asientos" min="0" value="{{ old('cantidad_asientos') }}">
                                        <div class="invalid-feedback">
                                            {{ $errors->first('cantidad_asientos') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label for="carroceria" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Marca de Aire Acond. <code>*</code></label>
                                <div class="col-sm-12 col-md-7">
                                    <div class="input-group">
                                        <select name="aire_acondicionado_id" id="aire_acondicionado_id" class="form-control select2 {{ $errors->has('aire_acondicionado_id') ? ' is-invalid' : '' }}" data-placeholder="Seleccione una marca de aire acondicionado" >
                                            <option label="Selecciona una marca de aire acondicionado" value="">Selecciona una marca de aire acondicionado</option>
                                            @foreach ($aires_acondicionados as $aire_acondicionado)
                                                <option value="{{$aire_acondicionado->id}}" {{ (old('aire_acondicionado_id') == $aire_acondicionado->id) ? 'selected' : '' }}>{{$aire_acondicionado->nombre}}</option>
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
                                        <input class="form-control {{ $errors->has('puesta_servicio') ? ' is-invalid' : '' }}" type="date" id="puesta_servicio" name="puesta_servicio" value="{{ old('puesta_servicio')}}">
                                    </div>
                                    <small class="d-flex flex-row-reverse">Al hacer click en el calendario se despliega el selector de fechas.</small>
                                    <div class="invalid-feedback">
                                        {{ $errors->first('puesta_servicio') }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label for="carroceria" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Tipo de Vehículo <code>*</code></label>
                                <div class="col-sm-12 col-md-7">
                                    <div class="input-group">
                                        <select name="tipo_vehiculo_id" id="tipo_vehiculo_id" class="form-control select2 {{ $errors->has('tipo_vehiculo_id') ? ' is-invalid' : '' }}" data-placeholder="Selecciona tipo de vehiculo" >
                                            <option label="Selecciona tipo de vehiculo" value="">Selecciona tipo de vehiculo</option>
                                            @foreach ($tipos_vehiculos as $tipo_vehiculo)
                                                <option value="{{ $tipo_vehiculo->id }}" {{ (old('tipo_vehiculo_id') == $tipo_vehiculo->id) ? 'selected' : '' }}>{{ $tipo_vehiculo->nombre }}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback">
                                            {{ $errors->first('tipo_vehiculo_id') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label for="motor" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Motor <code>*</code></label>
                                <div class="div col-sm-12 col-md-7">
                                    <div class="form-check my-1">
                                        <input class="form-check-input" type="radio" name="motor" id="motor1" value="Delantero" checked>
                                        <label class="form-check-label" for="motor1">
                                          Delantero
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="motor" id="motor2" value="Trasero">
                                        <label class="form-check-label" for="motor2">
                                          Trasero
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label for="carroceria" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Asignado a Base <code>*</code></label>
                                <div class="col-sm-12 col-md-7">
                                    <div class="input-group">
                                        <select name="base_operacion_id" id="base_operacion_id" class="form-control select2 {{ $errors->has('base_operacion_id') ? ' is-invalid' : '' }}" data-placeholder="Selecciona base de operación" >
                                            <option label="Selecciona base de operación" value="">Selecciona base de operación</option>
                                            @foreach ($bases_operaciones as $base_operacion)
                                                <option value="{{ $base_operacion->id }}" {{ (old('base_operacion_id') == $base_operacion->id) ? 'selected' : '' }}>{{ $base_operacion->nombre }}</option>
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
                                        <textarea style="height: 100px" class="form-control {{ $errors->has('observaciones') ? ' is-invalid' : '' }}" id="observaciones" name="observaciones">{{ old('observaciones') }}</textarea>
                                        <div class="invalid-feedback">
                                            {{ $errors->first('observaciones') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label for="observaciones" class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                                <div class="col-sm-12 col-md-7">
                                    <div class="input-group">
                                        <button type="submit" action="{{ route('admin.unidades.store') }}" class="btn btn-primary">Agregar</button>
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
@include('admin.unidades.partials.marcamodal')
@include('admin.unidades.partials.modelomodal')
@endsection

@section('scripts')
<script>
    let el = document.getElementById('num_interno');
    let updatetext = function() {
        el.value = ('00000' + el.value).slice(-5);
    }

    el.addEventListener("keyup", updatetext , false);

    var url = HOST

    // AJAX Marca
    $("#marca-modal").fireModal({
        title: 'Nueva marca de unidad',
        body: $("#modal-marca-part"),
        footerClass: 'bg-whitesmoke',
        autoFocus: false,
        removeOnDismiss: false,
        onFormSubmit: function(modal, e, form) {
            let nombre = $('input[name="marca"]').val(),
                _token = $('input[name="_token"]').val();

            $.ajax({
                url: url + "/admin/crear-marca-unidad",
                data: {
                    nombre,
                },
                cache: false,
                type: 'POST',
                dataType: 'json',
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', _token);
                },
                success: function (resp) {
                    modal.removeClass("modal-progress")
                    modal.modal("hide");
                    $('input[name="marca"]').val("");

                    let id = resp.id;
                    let nombre = resp.nombre;
                    let option = `<option selected value='${id}'>${nombre}</option>`;

                    $("#marca_id").append(option);
                    $("#model_marca_id").append(option);
                    $("#marca_id").trigger('change');
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                }
            })

            e.preventDefault()
        },
        shown: function(modal, form) {
            console.log("shown", modal, form)
        },
        buttons: [{
            text: 'Agregar',
            submit: true,
            class: 'btn btn-primary btn-shadow',
            handler: function(modal) {
                console.log("handle", modal)
            }
        }]
    });

    // AJAX Modelo
    $("#modelo-modal").fireModal({
        title: 'Nuevo modelo de unidad',
        body: $("#modal-modelo-part"),
        footerClass: 'bg-whitesmoke',
        autoFocus: false,
        onFormSubmit: function(modal, e, form) {
            let modelo = $('input[name="modelo"]').val(),
                marca_id = $('#model_marca_id').val(),
                _token = $('input[name="_token"]').val();

            $.ajax({
                url: url + "/admin/crear-modelo-unidad",
                data: {
                    modelo,
                    marca_id
                },
                cache: false,
                type: 'POST',
                dataType: 'json',
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', _token);
                },
                success: function (resp) {
                    modal.removeClass("modal-progress")
                    modal.modal("hide");
                    $('input[name="modelo"]').val("");

                    let id = resp.id;
                    let nombre = resp.nombre;
                    let option = `<option selected value='${id}'>${nombre}</option>`;

                    $("#modelo_id").append(option);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(errorThrown);
                }
            })

            e.preventDefault()
        },
        shown: function(modal, form) {
            console.log(form)
        },
        buttons: [{
            text: 'Agregar',
            submit: true,
            class: 'btn btn-primary btn-shadow',
            handler: function(modal) {
            }
        }]
    });
</script>
@endsection
