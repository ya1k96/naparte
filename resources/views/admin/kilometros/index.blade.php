@extends('layouts.admin-master')

@section('title')
    Kilómetros
@endsection

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Kilómetros</h1>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Agregar kilómetro</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.kilometros.store') }}" method="post">
                                @csrf
                                <div class="form-group row mb-4">
                                    <label for="cantidad" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Cantidad <code>*</code></label>
                                    <div class="col-sm-12 col-md-7">
                                        <div class="input-group">
                                            <input type="number" name="cantidad" class="form-control {{ $errors->has('cantidad') ? ' is-invalid' : '' }}" placeholder="Ingrese la cantiad sin punto ni coma" autocomplete="off">
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-primary">
                                                    Agregar
                                                </button>
                                            </div>
                                            <div class="invalid-feedback">
                                                {{ $errors->first('cantidad') }}
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
                            <h4 class="col-6">Listado de kilómetros</h4>
                            <div class="input-group d-flex flex-row-reverse">
                                <form action="" method="GET">
                                    <div class="input-group-btn d-flex flex-row">
                                        <input type="search" name="buscar" class="form-control" placeholder="Buscar" value="{{ $buscar }}" autocomplete="off">
                                        <button class="btn btn-primary bt-icon">
                                            <i class="fas fa-search"></i>
                                        </button>
                                        <a href="{{ route('admin.kilometros') }}" class="btn btn-lighty btn-icon">
                                            <i class="fas fa-redo"></i>
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped">
                                <thead>
                                    <tr class="text-center">
                                        <th scope=col>Id</th>
                                        <th scope=col>Kilómetros</th>
                                        <th scope=col>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($kilometros as $kilometro)
                                        <tr class="text-center">
                                            <td>{{ $kilometro->id }}</td>
                                            <td>{{ number_format($kilometro->cantidad, '2', ',', '.') }}</td>
                                            <td>
                                                <form action="{{ route('admin.kilometros.destroy', $kilometro->id) }}" method="post">
                                                    <a href="{{ route('admin.kilometros.edit', $kilometro->id) }}" class="btn btn-primary">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    @csrf
                                                    @method('delete')
                                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Se eliminará el kilómetro {{ $kilometro->cantidad }}. ¿Continuar?')">
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
    </section>
@endsection
