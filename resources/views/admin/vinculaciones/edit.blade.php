@extends('layouts.admin-master')

@section('title')
    Equipos
@endsection

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Equipos</h1>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Editar vinculación</h4>
                        </div>
                        <form action="{{ route('admin.vinculaciones.update', $vinculacion[0]->id) }}" method="post">
                            @csrf
                            @method('put')
                            <div class="card-body">
                                <div class="form-group row mb-4">
                                    <label for="unidad" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Unidad <code>*</code></label>
                                    <div class="col-sm-12 col-md-7">
                                        <div class="input-group">
                                            <select name="unidad" id="unidad_id" class="form-control select2 {{ $errors->has('unidad') ? ' is-invalid' : '' }}" data-placeholder="Seleccione una unidad">
                                                <option label="Seleccione una unidad" value="">Seleccione una unidad</option>
                                                @foreach($unidades as $unidad)
                                                {{-- {{ ($vinculacion[0]->unidades[0]->num_interno == $unidad->num_interno) ? 'selected' : '' }} --}}
                                                    <option value="{{ $unidad->id }}" {{ ($vinculacion[0]->unidad->num_interno == $unidad->num_interno) ? 'selected' : '' }}>{{ $unidad->num_interno }}</option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback">
                                                {{ $errors->first('unidad') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row mb-4">
                                    <label for="plan" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Plan a vincular <code>*</code></label>
                                    <div class="col-sm-12 col-md-7">
                                        <select name="plan" id="plan" class="form-control select2 {{ $errors->has('plan') ? ' is-invalid' : '' }}" data-placeholder="Seleccione un plan">
                                            <option label="Seleccione un plan" value="">Seleccione un plan</option>
                                            @foreach ($planes as $plan)
                                            {{-- {{ ($vinculacion[0]->planes[0]->nombre == $plan->nombre) ? 'selected' : '' }} --}}
                                                <option value="{{ $plan->id }}" {{ ($vinculacion[0]->plan->nombre == $plan->nombre) ? 'selected' : '' }}>{{ $plan->nombre }}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback">
                                            {{ $errors->first('plan') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row mb-4">
                                    <label for="km_inicial" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Km inicial <code>*</code></label>
                                    <div class="col-sm-12 col-md-7">
                                        <div class="input-group">
                                            <input type="number" id="km_inicial" name="km_inicial" class="form-control {{ $errors->has('km_inicial') ? ' is-invalid' : '' }}" value="{{ $vinculacion[0]->km_inicial }}">
                                            <div class="invalid-feedback">
                                                {{ $errors->first('km_inicial') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row mb-4">
                                    <label for="fecha" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Fecha <code>*</code></label>
                                    <div class="col-sm-12 col-md-7">
                                        <div class="input-group">
                                            <input type="date" id="fecha" name="fecha" class="form-control {{ $errors->has('fecha') ? ' is-invalid' : '' }}" value="{{ $vinculacion[0]->fecha }}">
                                            <div class="invalid-feedback">
                                                {{ $errors->first('fecha') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row mb-4">
                                    <label for="estimativo" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Estimativo mensual <code>*</code></label>
                                    <div class="col-sm-12 col-md-7">
                                        <div class="input-group">
                                            <input type="number" id="estimativo" name="estimativo" class="form-control {{ $errors->has('estimativo') ? ' is-invalid' : '' }}" value="{{ $vinculacion[0]->estimativo }}">
                                            <div class="invalid-feedback">
                                                {{ $errors->first('estimativo') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-right">
                                <a href="{{ route('admin.vinculaciones') }}">
                                    <button type="button" class="btn btn-secondary mr-1">Cancelar</button>
                                </a>
                                <button type="submit" name="action" value="guardar"
                                    class="btn btn-primary">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
