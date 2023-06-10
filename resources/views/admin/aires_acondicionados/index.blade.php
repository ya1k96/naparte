@extends('layouts.admin-master')

@section('title')
Listado de Marcas de Aire Acondicionado
@endsection

@section('content')
<section class="section">
  <div class="section-header">
    <h1>Listado de Marcas de Aire Acondicionado</h1>
  </div>
  <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="col-6">Agregar marca</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action=" {{ route('admin.aires_acondicionados.store') }} ">
                            <div class="form-group row mb-4">
                                @csrf
                                <label for="" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Nombre*</label>
                                <div class="col-sm-12 col-md-7">
                                    <div class="input-group">
                                        <input class="form-control {{ $errors->has('nombre') ? ' is-invalid' : '' }}" type="text" id="nombre" name="nombre">
                                        <div class="input-group-append">
                                            <button type="submit" action="{{ route('admin.aires_acondicionados.store') }}" class="btn btn-primary">Agregar</button>
                                        </div>
                                        <div class="invalid-feedback">
                                            {{ $errors->first('nombre') }}
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
                        <h4 class="col-6">Listado de Marcas</h4>
                        <div class="input-group d-flex flex-row-reverse">
                            <form action="" method="GET">
                                <div class="input-group-btn d-flex flex-row">
                                    <input type="search" name="buscar" class="form-control" placeholder="Buscar" value="{{$buscar}}">
                                    <button class="btn btn-primary btn-icon"><i class="fas fa-search"></i></button>
                                    <a href="{{ route('admin.aires_acondicionados') }}" class="btn btn-lighty btn-icon"><i class="fas fa-redo"></i></a>
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
                                        <th>Acción</th>
                                    </tr>
                                    @foreach ($aires_acondicionados as $aire_acond)
                                    <tr class="text-center">
                                        <td>{{$aire_acond->id}}</td>
                                        <td>{{$aire_acond->nombre}}</td>
                                        <td>
                                            <form action=" {{ route('admin.aires_acondicionados.destroy', $aire_acond->id) }} " method="POST">
                                                <a href="{{ route('admin.aires_acondicionados.edit', $aire_acond->id) }}" class="btn btn-primary"><i class="fa fa-edit"></i></a>
                                                @csrf
                                                @method('delete')
                                                <button class="btn btn-danger" onclick="return confirm('Se eliminará la marca {{$aire_acond->nombre}}. ¿Continuar?')"><i class="fa fa-trash"></i></button>
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
