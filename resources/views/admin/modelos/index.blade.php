@extends('layouts.admin-master')

@section('title')
Listado de Modelos de Unidades
@endsection

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Listado de Modelos de Unidades</h1>
    </div>
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="col-6">Agregar modelo</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action=" {{ route('admin.modelos.store') }} ">
                            @csrf
                            <div class="form-group row mb-4">
                                <label for="nombre" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Nombre*</label>
                                <div class="col-sm-12 col-md-7">
                                    <div class="input-group">
                                        <input class="form-control {{ $errors->has('nombre') ? ' is-invalid' : '' }}" type="text" id="nombre" name="nombre">
                                        <div class="invalid-feedback">
                                            {{ $errors->first('nombre') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label for="marca" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Marca*</label>
                                <div class="col-sm-12 col-md-7">
                                    <div class="input-group">
                                        <select name="marca_id" id="marca_id" class="form-control select2 {{ $errors->has('marca_id') ? ' is-invalid' : '' }}" data-placeholder="Seleccione una marca">
                                            <option label="Selecciona una marca" value="">Selecciona una marca</option>
                                            @foreach ($marcas as $marca)
                                                <option value="{{$marca->id}}">{{$marca->nombre}}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback">
                                            {{ $errors->first('marca_id') }}
                                        </div>
                                        <div class="input-group-append">
                                            <button type="submit" action="{{ route('admin.modelos.store') }}" class="btn btn-primary">Agregar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="col-6">Listado de modelos</h4>
                        <div class="input-group d-flex flex-row-reverse">
                            <form action="" method="GET">
                                <div class="input-group-btn d-flex flex-row">
                                    <input type="search" name="buscar" class="form-control" placeholder="Buscar" value="{{$buscar}}">
                                    <button class="btn btn-primary btn-icon"><i class="fas fa-search"></i></button>
                                    <a href="{{ route('admin.modelos') }}" class="btn btn-lighty btn-icon"><i class="fas fa-redo"></i></a>
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
                                        <th>Nombre</th>
                                        <th>Marca</th>
                                        <th>Acción</th>
                                    </tr>
                                    @foreach ($modelos as $modelo)
                                    <tr class="text-center">
                                        <td>{{$modelo->id}}</td>
                                        <td>{{$modelo->nombre}}</td>
                                        <td>{{$modelo->marca->nombre}}</td>
                                        <td>
                                            <form action=" {{ route('admin.modelos.destroy', $modelo->id) }} " method="POST">
                                                <a href="{{ route('admin.modelos.edit', $modelo->id) }}" class="btn btn-primary">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                @csrf
                                                @method('delete')
                                                <button class="btn btn-danger" onclick="return confirm('Se eliminará el modelo {{$modelo->nombre}}. ¿Continuar?')">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
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
