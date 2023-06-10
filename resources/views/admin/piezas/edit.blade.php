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
                            <h4>Editar pieza del catálogo</h4>
                        </div>
                        <form action="{{ route('admin.piezas-de-catalogo.update', $pieza[0]->id) }}" method="post">
                            @csrf
                            @method('put')
                            <div class="card-body">
                                <div class="form-group row mb-4">
                                    <label for="nro_pieza" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">N° de pieza <code>*</code></label>
                                    <div class="col-sm-12 col-md-7">
                                        <input type="text" class="form-control {{ $errors->has('nro_pieza') ? ' is-invalid' : '' }}" name="nro_pieza" value="{{ $pieza[0]->nro_pieza }}" autocomplete="off">
                                        <div class="invalid-feedback">
                                            {{ $errors->first('nro_pieza') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row mb-4">
                                    <label for="descripcion" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Descripción <code>*</code></label>
                                    <div class="col-sm-12 col-md-7">
                                        <input type="text" class="form-control {{ $errors->has('descripcion') ? ' is-invalid' : '' }}" name="descripcion" value="{{ $pieza[0]->descripcion }}" autocomplete="off">
                                        <div class="invalid-feedback">
                                            {{ $errors->first('descripcion') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row mb-4">
                                    <label for="unidad_medida" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Unidad de medida <code>*</code></label>
                                    <div class="col-sm-12 col-md-7">
                                        <select name="unidad_medida" id="unidad_medida_id" class="form-control {{ $errors->has('unidad_medida') ? ' is-invalid' : '' }}">
                                            @foreach ($unidades_medidas as $unidad_medida)
                                                <option value="{{ $unidad_medida->id }}" {{ ($pieza[0]->unidadMedida->id == $unidad_medida->id) ? 'selected' : ''}}>{{ $unidad_medida->nombre }} </option>
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
                                        <select name="base_operacion[]" id="base_operacion_id" class="form-control select2 select2-hidden-accessible {{ $errors->has('base_operacion') ? ' is-invalid' : '' }}" multiple>
                                            @foreach ($bases_operaciones as $base_operacion)
                                                @php
                                                    $selected = "";
                                                @endphp
                                                @foreach ($pieza[0]->baseOperacion as $base)
                                                    @php
                                                        if ($base->id == $base_operacion->id) {
                                                            $selected = 'selected';
                                                        }
                                                    @endphp
                                                @endforeach
                                                <option value="{{ $base_operacion->id }}" {{ $selected }}>{{ $base_operacion->nombre }}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback">
                                            {{ $errors->first('base_operacion') }}
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="gridCheck">
                                            <label class="form-check-label" for="gridCheck">
                                                Seleccionar todos
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <!-- <div class="form-group row mb-4">
                                    <label for="categorias" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Seleccione dos categorías <code>*</code></label>
                                    <div class="col-sm-12 col-md-7">
                                        <select name="categorias[]" id="categorias" class="form-control select2 select2-hidden-accessible {{ $errors->has('categorias') ? ' is-invalid' : '' }}" multiple>
                                            @foreach ($categorias as $categoria)
                                                @php
                                                    $selected = "";
                                                @endphp
                                                @foreach ($pieza[0]->categorias as $old_categoria)
                                                    @php
                                                        if ($old_categoria->id == $categoria->id) {
                                                            $selected = 'selected';
                                                        }
                                                    @endphp
                                                @endforeach
                                                <option value="{{ $categoria->id }} " {{ $selected }}>{{ $categoria->nombre }}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback">
                                            {{ $errors->first('categorias') }}
                                        </div>
                                    </div>
                                </div> -->
                                <div class="form-group row mb-4">
                                    <label for="observacion" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Observación</label>
                                    <div class="col-sm-12 col-md-7">
                                        <textarea class="form-control" name="observacion">{{ $pieza[0]->observacion }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-right">
                                <a href="{{ route('admin.piezas-de-catalogo') }}">
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
    </script>
@endsection
