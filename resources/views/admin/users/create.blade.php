@extends('layouts.admin-master')

@section('title')
Agregar Usuario
@endsection

@section('content')
<section class="section">
  <div class="section-header">
    <h1>Agregar Usuario</h1>
  </div>
  <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="col-6">Agregar Usuario</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action=" {{ route('admin.users.store') }} ">
                            @csrf
                            {{-- 
                              Asigno como ADMIN
                              TODO: Cambiar a futuro para los distintos roles
                            --}}
                            <input class="form-control" type="hidden" id="role" name="role" value="1" >
                            <div class="form-group row mb-4">
                                <label for="name" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Nombre*</label>
                                <div class="col-sm-12 col-md-7">
                                    <div class="input-group">
                                        <input class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}" type="text" id="name" name="name" >
                                        <div class="invalid-feedback">
                                            {{ $errors->first('name') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                              <label for="email" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Email*</label>
                              <div class="col-sm-12 col-md-7">
                                    <div class="input-group">
                                        <input class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" type="text" id="email" name="email" >
                                        <div class="invalid-feedback">
                                        {{ $errors->first('email') }}
                                        </div>
                                    </div>
                              </div>
                            </div>
                            <div class="form-group row mb-4">
                              <label for="password" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Contraseña*</label>
                              <div class="col-sm-12 col-md-7">
                                    <div class="input-group">
                                        <input class="form-control {{ $errors->has('password') ? ' is-invalid' : '' }}" type="password" id="password" name="password" >
                                        <div class="invalid-feedback">
                                            {{ $errors->first('password') }}
                                        </div>
                                    </div>
                              </div>
                            </div>
                            <div class="form-group row mb-4">
                              <label for="password_confirmation" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Confirmar contraseña*</label>
                              <div class="col-sm-12 col-md-7">
                                  <div class="input-group">
                                        <input class="form-control {{ $errors->has('password_confirmation') ? ' is-invalid' : '' }}" type="password" id="password_confirmation" name="password_confirmation" >
                                        <div class="invalid-feedback">
                                        {{ $errors->first('password_confirmation') }}
                                        </div>
                                  </div>
                              </div>
                            </div>
                            <div class="form-group row mb-4">
                              <label for="observaciones" class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                              <div class="col-sm-12 col-md-7">
                                  <div class="input-group">
                                      <button type="submit" action="{{ route('admin.users.store') }}" class="btn btn-primary">Guardar</button>
                                      <a href="{{ route('admin.users') }}" class="btn btn-light mx-1">Cancelar</a>
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