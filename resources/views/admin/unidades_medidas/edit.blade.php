@extends('layouts.admin-master')

@section('title')
    Unidades de medida
@endsection

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Unidades de medida</h1>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Editar unidad de medida</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.unidades-de-medida.update', $unidad_medida->id) }}" method="POST">
                                @csrf
                                @method('put')
                                <div class="form-group row mb-4">
                                    <label for="nombre" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Unidad</label>
                                    <div class="col-sm-12 col-md-7">
                                        <div class="input-group">
                                            <input type="hidden" name="id" value="{{ $unidad_medida->id }}">
                                            <input type="text" name="nombre" class="form-control{{ $errors->has('nombre') ? ' is-invalid' : '' }}" value="{{ $unidad_medida->nombre }}" autocomplete="off">
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-primary" action="{{ route('admin.unidades-de-medida.update', $unidad_medida->id) }}">
                                                    Editar
                                                </button>
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
