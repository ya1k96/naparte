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
                            <h4>Editar kilómetro</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.kilometros.update', $kilometro->id) }}" method="POST">
                                @csrf
                                @method('put')
                                <div class="form-group row mb-4">
                                    <label for="cantidad" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Cantidad <code>*</code></label>
                                    <div class="col-sm-12 col-md-7">
                                        <div class="input-group">
                                            <input type="hidden" name="id" value="{{ $kilometro->id }}">
                                            <input type="text" name="cantidad" class="form-control {{ $errors->has('cantidad') ? ' is-invalid' : '' }}" value="{{ $kilometro->cantidad }}" autocomplete="off">
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-primary" action="{{ route('admin.kilometros.update', $kilometro->id) }}">
                                                    Editar
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
        </div>
    </section>
@endsection
