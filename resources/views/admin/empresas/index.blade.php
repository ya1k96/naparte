@extends('layouts.admin-master')

@section('title')
    Empresas
@endsection

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Listado de Empresas</h1>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="col-6">Agregar empresa</h4>
                        </div>
                        <div class="card-body">
                            <form class="form-group" method="POST" enctype="multipart/form-data"
                                action="{{ route('admin.empresas.store') }}">
                                @csrf
                                <div class="row">
                                    <!-- cuit -->
                                    <div class="form-group col-md-5">
                                        <label for="cuit"
                                            class="col-form-label col-12 col-md-12 col-lg-12">CUIT*</label>
                                        <div class="col-sm-12 col-md-12">
                                            <input type="text" name="cuit"
                                                class="form-control {{ $errors->has('cuit') ? ' is-invalid' : '' }}"
                                                autocomplete="off" value="">
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
                                                autocomplete="off" value="">
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
                                            <input id="enterpriseImage-edit" type="file" name="img"
                                                accept=".jpg, .jpeg, .png, .svg" lang="es"
                                                class=" custom-file-input {{ $errors->has('img') ? ' is-invalid' : '' }}">
                                            <label class=" custom-file-label ml-3" for="enterpriseImage-edit">
                                                <b> Seleccionar Imagen. </b> Tipo .jpeg .jpg .png svg up to 1024KB.
                                                <p class="text mt-1 ml-2">
                                                    ATENCIÓN: Verifique que la imagen corresponde a una empresa.
                                                </p>
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('img') }}
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                    <!-- submit -->
                                    <div class="form-group col-md-5 ml-3">
                                        <button type="submit" class="btn btn-primary " style="margin-top:35px;"
                                            action="{{ route('admin.empresas.store') }}">
                                            Agregar
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- enterprises list -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="col-6">Listado de empresas</h4>
                            <div class="input-group d-flex flex-row-reverse">
                                <form action="" method="GET">
                                    <div class="input-group-btn d-flex flex-row">
                                        <input type="search" name="buscar" class="form-control mr-2" placeholder="Buscar"
                                            value="{{ $buscar }}">
                                        <button title='Buscar' class="btn btn-primary btn-icon"><i
                                                class="fas fa-search"></i></button>
                                        <a title='Limpiar' href="{{ route('admin.empresas') }}"
                                            class="btn btn-lighty btn-icon ml-4"><i class="fas fa-redo"></i></a>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive table-invoice">
                                <table class="table table-striped">
                                    <tbody>
                                        <tr class="text-center">
                                            <th>Id</th>
                                            <th>CUIT</th>
                                            <th>Nombre</th>
                                            <th>Acción</th>
                                        </tr>
                                        @foreach ($empresas as $empresa)
                                            <tr class="text-center">
                                                <td>{{ $empresa->id }}</td>
                                                <td>{{ $empresa->cuit }}</td>
                                                <td>
                                                    @if ( !$empresa->img )
                                                        <img class="rounded-circle mr-1 h-50  size-img-enterprise"
                                                            src="{{ asset('assets/img/example-image-50.jpg') }}" /> {{ $empresa->nombre }}
                                                    @else
                                                        <img class="rounded-circle mr-1 h-50  size-img-enterprise"
                                                            src="{{ asset('storage/img/empresa_logos/'.$empresa->img) }}" /> {{ $empresa->nombre }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (!$empresa->deleted_at)
                                                        <form action=" {{ route('admin.empresas.destroy', $empresa->id) }} " method="POST">
                                                            <a href="{{ route('admin.empresas.edit', $empresa->id) }}" class="btn btn-primary"><i class="fa fa-edit"></i></a>
                                                            @method('delete')
                                                            @csrf
                                                            <button class="btn btn-danger"
                                                                data-toggle="tooltip" data-placement="bottom" title="Anular Empresa: '{{ $empresa->nombre }}'"
                                                                onclick="return confirm('Se Anulará la empresa {{$empresa->nombre}}. ¿Continuar?')">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <button class="btn btn-sm btn-dark" type="button" href="javascript:void(0);"
                                                            data-toggle="tooltip" data-placement="bottom" title=" La Empresa: '{{ $empresa->nombre }}'. Está Deshabilitada!">
                                                            Anulada
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
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
            var name = document.getElementById("enterpriseImage-edit").files[0].name;
            var nextSibling = e.target.nextElementSibling
            nextSibling.innerText = name
        })
    </script>
@endsection
