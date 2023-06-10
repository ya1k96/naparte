@extends('layouts.admin-master')

@section('title')
Editar Modelo
@endsection

@section('content')
<section class="section">
  <div class="section-header">
    <h1>Editar Modelo</h1>
  </div>
  <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Editar modelo</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action=" {{ route('admin.modelos.update', $modelo->id) }} ">
                            <div class="form-group row mb-4">
                                @csrf
                                @method('PUT')
                                <label for="" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Nombre*</label>
                                <div class="col-sm-12 col-md-7">
                                    <div class="input-group">
                                        <input type="hidden" name="id" value="{{$modelo->id}}">
                                        <input class="form-control {{ $errors->has('nombre') ? ' is-invalid' : '' }}" type="text" id="nombre" name="nombre" value="{{$modelo->nombre}}">
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
                                                <option value="{{$marca->id}}" {{($marca->id == $modelo->marca->id)? 'selected' : ''}}>{{$marca->nombre}}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback">
                                            {{ $errors->first('marca_id') }}
                                        </div>
                                        <div class="input-group-append">
                                            <button type="submit" action="{{ route('admin.modelos.store') }}" class="btn btn-primary">Guardar</button>
                                        </div>
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
@endsection