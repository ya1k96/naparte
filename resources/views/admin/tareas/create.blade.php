@extends('layouts.admin-master')

@section('title')
    Tarea
@endsection

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Tarea</h1>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Nueva tarea</h4>
                            <div class="card-header-action">
                                <form action="">
                                    <label for="sub-componente" class="col-form-label">Subcomponente </label>
                                    <input type="text" name="sub-componente" value="{{ $componente->nombre }}" class="form-control" readonly>
                                </form>
                            </div>
                        </div>
                        <form action="{{ route('admin.tareas.store') }}" method="post">
                            @csrf
                            <input type="hidden" name="componente" value="{{ $componente }}">
                            <div class="card-body">
                                <div class="form-group row mb-4">
                                    <label for="descripcion" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Tarea <code>*</code></label>
                                    <div class="col-sm-12 col-md-7">
                                        <input type="text"
                                            class="form-control {{ $errors->has('descripcion') ? ' is-invalid' : '' }}"
                                            name="descripcion" value="{{ old('descripcion') }}" autocomplete="off">
                                        <div class="invalid-feddback">
                                            {{ $errors->first('descripcion') }}
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row mb-4">
                                    <label for="tipo_frecuencia" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Frecuencia <code>*</code></label>
                                    <div class="input-group col-sm-12 col-md-7">
                                            <select name="tipo_frecuencia" id="select_frecuencia" class="form-control {{ $errors->has('tipo_frecuencia') ? ' is-invalid' : '' }}" data-placeholder="Seleccione una opción">
                                                <option label="Seleccione una opción" value="">Seleccione una opción</option>
                                                <option value="dias">Días</option>
                                                <option value="kms">Kilómetros</option>
                                                <option value="combinado">Combinado</option>
                                            </select>
                                            <select name="kms" id="input1" class="form-control {{ $errors->has('kms') ? ' is-invalid' : '' }}" data-placeholder="Seleccione los kilómetros">
                                                <option label="Seleccione los kilómetros" value="">Seleccione los kilómetros</option>
                                                @foreach($kms as $km)
                                                    <option value="{{ $km->cantidad }}" {{ (old('kms') == $km->cantidad) ? 'selected' : '' }}>{{ $km->cantidad . " km"}}</option>
                                                @endforeach
                                            </select>
                                            <select name="dias" id="input2" class="form-control {{ $errors->has('dias') ? ' is-invalid' : '' }}" data-placeholder="Seleccione la cantidad de días">
                                                <option label="Seleccione la cantidad de días" value="">Seleccione la cantidad de días</option>
                                                @foreach($dias as $dia)
                                                    <option value="{{ $dia->cantidad }}" {{ (old('dias') == $dia->cantidad) ? 'selected' : '' }}>{{ $dia->cantidad . " días" }}</option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback">
                                                {{ $errors->first('tipo_frecuencia') }} {{ $errors->first('kms') }} {{ $errors->first('dias') }}
                                            </div>
                                    </div>
                                </div>

                                <div class="form-group row mb-4">
                                    <label for="especialidad" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Especialidad <code>*</code></label>
                                    <div class="col-sm-12 col-md-7">
                                        <div class="input-group">
                                            <select name="especialidad" id="especialidad" class="form-control {{ $errors->has('especialidad') ? ' is-invalid' : '' }}" required>
                                                <option disabled selected>Seleccione una especialidad</option>
                                                @foreach ($especialidades as $especialidad)
                                                    <option value="{{ $especialidad->id }}" {{ (old('especialidad') == $especialidad->id) ? 'selected' : '' }}>{{ $especialidad->nombre }}</option>
                                                @endforeach
                                            </select>
                                            <div class="input-group-append">
                                                <button class="btn btn-primary trigger--fire-modal-5" type="button" id="especialidad-modal">Agregar nueva</button>
                                            </div>
                                        </div>
                                        <div class="invalid-feddback">
                                            {{ $errors->first('especialidad') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row mb-4">
                                    <label for="observaciones"
                                        class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Observaciones</label>
                                    <div class="col-sm-12 col-md-7">
                                        <textarea style="height: 100px" class="form-control" name="observaciones">{{ old('observaciones') }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-right">
                                <button type="submit" name="action" value="guardar_y_nuevo"
                                    class="btn btn-primary mr-1">Guardar y crear nuevo</button>
                                <a href="{{ route('admin.plan-mantenimiento-preventivo') }}">
                                    <button type="button" class="btn btn-secondary mr-1">Cancelar</button>
                                </a>
                                <button type="submit" name="action" value="guardar"
                                    class="btn btn-primary">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
    </section>
    @include('admin.tareas.modal.especialidadmodal')
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/page/bootstrap-modal.js') }}"></script>
    <script>

        $("#especialidad-modal").fireModal({
            title: 'Nueva especialidad',
            body: $("#modal-especialidad-part"),
            footerClass: 'bg-whitesmoke',
            autoFocus: false,
            onFormSubmit: function(modal, e, form) {
                let nombre = $('input[name="nombre"]').val(),
                    _token = $('input[name="_token"]').val();

                $.ajax({
                    url: HOST + "/admin/crear-especialidad",
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
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log(errorThrown);
                    }
                })
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

        // Se muestran un placeholder de acuerdo a la opción que selecciona
        let select_frecuencia = document.getElementById('select_frecuencia'),
            km = document.getElementById('input1');
            dias = document.getElementById('input2');

        document.addEventListener("DOMContentLoaded", function () {
           let select = select_frecuencia.value;

           // document.addEventListener("DOMContentLoaded", fn);
           if (select == "") {
               dias.style.display = 'none';
               km.style.display = 'none';
           }
        });

        select_frecuencia.addEventListener('change', function () {
            if (select_frecuencia.value == "dias") {
                dias.setAttribute('placeholder', 'Ej: 30 días');
                dias.style.display = 'block';
                km.style.display = 'none';
            }

            if (select_frecuencia.value == "kms") {
                dias.setAttribute('placeholder', 'Ej: 1000 kilómetros');
                dias.style.display = 'none';
                km.style.display = 'block';
            }

            if (select_frecuencia.value == "combinado") {
                dias.setAttribute('placeholder', 'Ingrese kilómetros');
                km.setAttribute('placeholder', 'Ingrese días');
                dias.style.display = 'block';
                km.style.display = 'block';
            } else if (select_frecuencia.value == "") {
                dias.style.display = 'none';
                km.style.display = 'none';
            }
        });
    </script>
@endsection
