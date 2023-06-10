@extends('layouts.admin-master')

@section('title')
Editar Marca
@endsection

@section('content')
<section class="section">
  <div class="section-header">
    <h1>Editar Marca</h1>
  </div>
  <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Editar marca</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action=" {{ route('admin.marcas.update', $marca->id) }} ">
                            <div class="form-group row mb-4">
                                @csrf
                                @method('PUT')
                                <label for="" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Nombre*</label>
                                <div class="col-sm-12 col-md-7">
                                    <div class="input-group">
                                        <input type="hidden" name="id" value="{{$marca->id}}}}">
                                        <input class="form-control {{ $errors->has('nombre') ? ' is-invalid' : '' }}" type="text" id="nombre" name="nombre" value="{{$marca->nombre}}">
                                        <div class="input-group-append">
                                            <button type="submit" action="{{ route('admin.marcas.update', $marca->id) }}" class="btn btn-primary">Guardar</button>
                                            <a href="{{ route('admin.marcas') }}" class="btn btn-light ml-1 edit-cancel-margin margin-left-5">Cancelar</a>
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
  </div>
</section>
@endsection