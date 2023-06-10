@extends('layouts.admin-master')

@section('title')
Listado de Carrocerías
@endsection

@section('content')
<section class="section">
  <div class="section-header">
    <h1>Listado de Carrocerías</h1>
  </div>
  <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="col-6">Agregar carrocería</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action=" {{ route('admin.carrocerias.store') }} ">
                            <div class="form-group row mb-4">
                                @csrf
                                <label for="" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Nombre*</label>
                                <div class="col-sm-12 col-md-7">
                                    <div class="input-group">
                                        <input class="form-control {{ $errors->has('nombre') ? ' is-invalid' : '' }}" type="text" id="nombre" name="nombre">
                                        <div class="input-group-append">
                                            <button type="submit" action="{{ route('admin.carrocerias.store') }}" class="btn btn-primary">Agregar</button>
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
                        <h4 class="col-6">Listado de carrocerías</h4>
                        <div class="input-group d-flex flex-row-reverse">
                            <form action="" method="GET">
                                <div class="input-group-btn d-flex flex-row">
                                    <input type="search" name="buscar" class="form-control" placeholder="Buscar" value="{{$buscar}}">
                                    <button class="btn btn-primary btn-icon"><i class="fas fa-search"></i></button>
                                    <a href="{{ route('admin.carrocerias') }}" class="btn btn-lighty btn-icon"><i class="fas fa-redo"></i></a>
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
                                    @foreach ($carrocerias as $carroceria)
                                    <tr class="text-center">
                                        <td>{{$carroceria->id}}</td>
                                        <td>{{$carroceria->nombre}}</td>
                                        <td>
                                            <form action=" {{ route('admin.carrocerias.destroy', $carroceria->id) }} " method="POST">
                                                <a href="{{ route('admin.carrocerias.edit', $carroceria->id) }}" class="btn btn-primary"><i class="fa fa-edit"></i></a>
                                                @csrf
                                                @method('delete')
                                                <button class="btn btn-danger" onclick="return confirm('Se eliminará la carrocería {{$carroceria->nombre}}. ¿Continuar?')"><i class="fa fa-trash"></i></button>
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
