@extends('layouts.admin-master')

@section('title')
    Editar Empresa
@endsection

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Editar Empresa</h1>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Editar Empresa</h4>
                        </div>
                        <div class="card-body">
                            <form enctype="multipart/form-data" method="POST" action="{{ route('admin.empresas.update', $empresa->id) }}">
                                {{-- @dump($empresa->id) --}}
                                @method('PUT')
                                @csrf
                                <div class="row">
                                    <input type="hidden" name="id" value="{{ $empresa->id }}}}">
                                    <!-- cuit -->
                                    <div class="form-group col-md-5">
                                        <label for="cuit"
                                            class="col-form-label col-12 col-md-12 col-lg-12">CUIT*</label>
                                        <div class="col-sm-12 col-md-12">
                                            <input type="text" name="cuit"
                                                class="form-control {{ $errors->has('cuit') ? ' is-invalid' : '' }}"
                                                autocomplete="off" value="{{ $empresa->cuit }}">
                                            <div class="invalid-feedback">
                                                {{ $errors->first('cuit') }}
                                            </div>
                                        </div>
                                    </div>
                                    <!-- name -->
                                    <div class="form-group col-md-5">
                                        <label for="nombre"
                                            class="col-form-label col-12 col-md-12 col-lg-12">Nombre</label>
                                        <div class="col-sm-12 col-md-12">
                                            <input type="text" name="nombre"
                                                class="form-control {{ $errors->has('nombre') ? ' is-invalid' : '' }}"
                                                autocomplete="off" value="{{ $empresa->nombre }}">
                                            <div class="invalid-feedback">
                                                {{ $errors->first('nombre') }}
                                            </div>
                                        </div>
                                    </div>
                                    <!-- img -->
                                    <div class="form-group col-md-5">
                                        <label for="img" class="col-form-label col-12 col-md-12 col-lg-12">
                                            Logo de Empresa
                                        </label>
                                        <div class="col-sm-12 col-md-12">
                                            <input id="enterpriseImage" type="file" name="img"
                                                accept=".jpg, .jpeg, .png, .svg" lang="es"
                                                class=" custom-file-input {{ $errors->has('img') ? ' is-invalid' : '' }}">
                                            <label class=" custom-file-label ml-3" for="enterpriseImage">
                                                <b> Seleccionar Imagen. </b> Tipo .jpeg .jpg .png svg up to 1024KB.
                                                <p class="text mt-1 ml-2">
                                                    ATENCIÓN: Verifique que la imagen corresponde a una empresa.
                                                </p>
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('img') }}
                                                </div>
                                        </div>
                                    </div>
                                    <!-- buttons -->
                                    <div class="form-group col-md-5 ml-3">
                                        <button type="submit" class="btn btn-primary " style="margin-top:35px;"
                                            action="{{ route('admin.empresas.update', $empresa->id) }}">
                                            Guardar
                                        </button>
                                        <a href="{{ route('admin.empresas') }}" class="btn btn-light ml-2"
                                            style="margin-top:35px;">
                                            Cancelar
                                        </a>
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
        document.querySelector('.custom-file-input').addEventListener('change', function(e) {
            var name = document.getElementById("enterpriseImage").files[0].name;
            var nextSibling = e.target.nextElementSibling
            nextSibling.innerText = name
        })
    </script>
@endsection
