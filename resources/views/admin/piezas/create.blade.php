@extends('layouts.admin-master')

@section('title')
    Piezas de catálogo
@endsection

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Piezas de catálogo</h1>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Nueva pieza del catálogo</h4>
                        </div>
                        <form action="{{ route('admin.piezas-de-catalogo.store') }}" method="post">
                            @csrf
                            <div class="card-body">
                                <div class="form-group row mb-4">
                                    <label for="nro_pieza" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">N° de pieza <code>*</code></label>
                                    <div class="col-sm-12 col-md-7">
                                        <input type="text" class="form-control {{ $errors->has('nro_pieza') ? ' is-invalid' : '' }}" name="nro_pieza" autocomplete="off" value="{{ old('nro_pieza') }}">
                                        <div class="invalid-feedback">
                                            {{ $errors->first('nro_pieza') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row mb-4">
                                    <label for="descripcion" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Descripción <code>*</code></label>
                                    <div class="col-sm-12 col-md-7">
                                        <input type="text" class="form-control {{ $errors->has('descripcion') ? ' is-invalid' : '' }}" name="descripcion" autocomplete="off" value="{{ old('descripcion') }}">
                                        <div class="invalid-feedback">
                                            {{ $errors->first('descripcion') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row mb-4">
                                    <label for="unidad_medida" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Unidad de medida <code>*</code></label>
                                    <div class="col-sm-12 col-md-7">
                                        <select name="unidad_medida" id="unidad_medida_id" class="form-control {{ $errors->has('unidad_medida') ? ' is-invalid' : '' }}">
                                            <option value="" selected>Seleccione una unidad de medida</option>
                                            @foreach ($unidades_medidas as $unidad_medida)
                                                <option value="{{ $unidad_medida->id }}" {{ old('unidad_medida') == $unidad_medida->id ? 'selected' : "" }}>{{ $unidad_medida->nombre }}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback">
                                            {{ $errors->first('unidad_medida') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row mb-4">
                                    <label for="base_operacion" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Asignado a base <code>*</code></label>
                                    <div class="col-sm-12 col-md-7">
                                        <select name="base_operacion[]" id="bases_operaciones" class="form-control select2 select2-hidden-accessible {{ $errors->has('base_operacion') ? ' is-invalid' : '' }}" multiple tabindex="-1" aria-hidden="true">
                                            @foreach ($bases_operaciones as $base_operacion)
                                                <option value="{{ $base_operacion->id }}" {{ (old('base_operacion') == $base_operacion->id) ? 'selected' : "" }}>{{ $base_operacion->nombre }}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback">
                                            {{ $errors->first('base_operacion') }}
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="select_all" id="select_all" >
                                            <label class="form-check-label" for="select_all">
                                                Seleccionar todos
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <!-- <div class="form-group row mb-4">
                                    <label for="categorias" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Seleccione dos categorías <code>*</code></label>
                                    <div class="col-sm-12 col-md-7">
                                        <div class="input-group">
                                            <select name="categorias[]" id="categorias" class="form-control select2 select2-hidden-accessible {{ $errors->has('categorias') ? ' is-invalid' : '' }}" multiple tabindex="-1" aria-hidden="true">
                                                @foreach ($categorias as $categoria)
                                                    <option value="{{ $categoria->id }}" {{ old('categorias') == $categoria->id ? 'selected' : "" }}>{{ $categoria->nombre }}</option>
                                                @endforeach
                                            </select>
                                            <div class="input-group-append">
                                                <button class="btn btn-primary trigger--fire-modal-5" type="button" id="categoria-modal">Agregar nueva</button>
                                            </div>
                                            <div class="invalid-feedback">
                                                {{ $errors->first('categorias') }}
                                            </div>
                                        </div>
                                    </div>
                                </div> -->
                                <div class="form-group row mb-4">
                                    <label for="observaciones" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Observación</label>
                                    <div class="col-sm-12 col-md-7">
                                        <textarea class="form-control" name="observaciones">{{ old('observaciones') }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-right">
                                <a href="{{ route("admin.piezas-de-catalogo") }}">
                                    <button type="button" class="btn btn-secondary mr-1">Cancelar</button>
                                </a>
                                <button type="submit" class="btn btn-primary">Guardar</button>
                            </div>
                        </form>
                    </div>
               </div>
            </div>
        </div>
    </section>
    @include('admin.piezas.partials.categoriamodal')
@endsection

@section('scripts')
    <script>
        // Permite seleccionar sólo dos categorías
        let options = null;

        $('#categorias').change(function(event) {
            if ($(this).val().length > 2) {
                $(this).val(options);
            } else {
                options = $(this).val();
            }
        });

        // Selecciona todas las bases de operación
        $('#select_all').click(function() {
            var checkedValue = $('#select_all:checked').val();

            if (checkedValue == "on") {
                $('#bases_operaciones option').prop('selected', true);
            } else {
                $('#bases_operaciones option').prop('selected', false);
            }
        });

        var url = HOST

        // AJAX Categoria
        $("#categoria-modal").fireModal({
            title: 'Nueva categoría',
            body: $("#modal-categoria-part"),
            footerClass: 'bg-whitesmoke',
            autoFocus: false,
            removeOnDismiss: false,
            onFormSubmit: function(modal, e, form) {
                let nombre = $('input[name="categoria"]').val(),
                    _token = $('input[name="_token"]').val();

                $.ajax({
                    url: url + "/admin/crear-categoria",
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
                        $('input[name="categoria"]').val("");

                        let id = resp.id
                        let nombre = resp.nombre
                        let option = `<option value='${id}'>${nombre}</option>`

                        $("#categorias").append(option);

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
                }
            }]
        });
    </script>
@endsection
