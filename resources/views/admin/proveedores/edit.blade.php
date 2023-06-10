@extends('layouts.admin-master')

@section('title')
Editar Proveedores
@endsection

@section('content')
<section class="section">
  <div class="section-header">
    <h1>Editar Proveedores</h1>
  </div>
  <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Editar proveedores</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" id="form-proveedor" action=" {{ route('admin.proveedor.update', $proveedor->id) }} ">
                            <div class="form-group row mb-4">
                                @csrf
                                @method('PUT')
                                <label for="nombre" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Nombre*</label>
                                <div class="col-sm-12 col-md-7">
                                    <div class="input-group">
                                        <input type="hidden" name="id" value="{{$proveedor->id}}" id="proveedor-id">
                                        <input class="form-control {{ $errors->has('nombre') ? ' is-invalid' : '' }}" type="text" id="nombre" name="nombre" value="{{$proveedor->nombre}}" required>
                                        <div class="invalid-feedback">
                                            {{ $errors->first('nombre') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label for="cuit" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">CUIT*</label>
                                <div class="col-sm-12 col-md-7">
                                    <div class="input-group">
                                        <input class="form-control {{ $errors->has('cuit') ? ' is-invalid' : '' }}" type="text" id="cuit" name="cuit" value="{{$proveedor->cuit}}" required>
                                        <div class="invalid-feedback">
                                            {{ $errors->first('cuit') }}
                                        </div>
                                    </div>
                                    <p><small>El CUIT debe tener 11 números sin guiones ni espacios. Debe validarlo antes de guardar.</small></p>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <div class="col-sm-12 col-md-7 offset-md-3 text-md-right">
                                    <a href="{{ route('admin.proveedor') }}" class="btn btn-light ml-1 edit-cancel-margin margin-left-5">Cancelar</a>
                                    <button type="submit" action="{{ route('admin.proveedor.store') }}" class="btn btn-primary submit-proveedor">Guardar</button>
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
    <script src="{{ asset('assets/js/proveedores/index.js') }}"></script>
@endsection