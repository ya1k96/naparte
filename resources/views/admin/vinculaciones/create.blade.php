@extends('layouts.admin-master')

@section('title')
    Equipos
@endsection

@section('styles')
  <style>
    .table-mr-custom .td-parte {
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;      
      max-width: 5ch;
    }
    .table-mr-custom .td-actividad {
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;      
      max-width: 20ch;
    }    
  </style>
@endsection

@section('content')
    <section class="section" id="vinculacion-equipo">
        <div class="section-header">
            <h1>Equipos</h1>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Plan de Mantenimiento - Equipos</h4>
                        </div>
                        <form id="form-vinculacions-equipos" action="{{ route('admin.vinculaciones.store') }}" method="post">
                            @csrf
                            <div class="card-body">
                                <div class="form-group row mb-4">
                                    <label for="unidad" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Unidad <code>*</code></label>
                                    <div class="col-sm-12 col-md-7">
                                        <div class="input-group">
                                            <select name="unidad" id="unidad_id" class="form-control select2 {{ $errors->has('unidad') ? ' is-invalid' : '' }}" data-placeholder="Seleccione una unidad">
                                                <option label="Seleccione una unidad" value="">Seleccione una unidad</option>
                                                @foreach($unidades as $unidad)
                                                    <option value="{{ $unidad->id }}" {{ old('unidad') == $unidad->id ? 'selected' : '' }}>{{ $unidad->num_interno }}</option>
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
                                        @if (!empty(session('vinculacion')))
                                            <select name="plan" id="plan" class="form-control select2 {{ $errors->has('plan') ? ' is-invalid' : '' }}" data-placeholder="Seleccione un plan">
                                                <option label="Seleccione un plan" value="">Seleccione un plan</option>
                                                @foreach ($planes as $plan)
                                                    <option value="{{ $plan->id }}" {{ $plan->id == session('vinculacion')->plan->id ? 'selected' : '' }}>{{ $plan->nombre }}</option>
                                                @endforeach
                                            </select>
                                        @else
                                            <select name="plan" id="plan" class="form-control select2 {{ $errors->has('plan') ? ' is-invalid' : '' }}" data-placeholder="Seleccione un plan">
                                                <option label="Seleccione un plan" value="">Seleccione un plan</option>
                                                @foreach ($planes as $plan)
                                                    <option value="{{ $plan->id }}" {{ old('plan') == $plan->id ? 'selected' : '' }}>{{ $plan->nombre }}</option>
                                                @endforeach
                                            </select>
                                        @endif
                                        <div class="invalid-feedback">
                                            {{ $errors->first('plan') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row mb-4">
                                    <label for="km_inicial" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Km inicial <code>*</code></label>
                                    <div class="col-sm-12 col-md-7">
                                        <div class="input-group">
                                            <input type="number" id="km_inicial" name="km_inicial" class="form-control {{ $errors->has('km_inicial') ? ' is-invalid' : '' }}" value="{{ old('km_inicial') }}">
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
                                            <input type="date" id="fecha" name="fecha" class="form-control {{ $errors->has('fecha') ? ' is-invalid' : '' }}" value="{{ old('fecha') }}">
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
                                            <input type="number" id="estimativo" name="estimativo" class="form-control {{ $errors->has('estimativo') ? ' is-invalid' : '' }}" value="{{ old('estimativo') }}">
                                            <div class="invalid-feedback">
                                                {{ $errors->first('estimativo') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-right">
                                <a href="javascript:void(0);" class="btn btn-secondary mr-1 text-white" onclick="cancelar('{{ route('admin.vinculaciones') }}')">
                                  Cancelar
                                </a>
                                <a href="javascript:void(0);" name="action" value="guardar_y_nuevo" class="btn btn-primary mr-1 text-white" onclick="preguntar('guardar_y_nuevo')">
                                  Guardar y crear nuevo
                                </a>
                                <a href="javascript:void(0);" name="action" value="guardar" class="btn btn-primary text-white" onclick="preguntar('guardar')">
                                  Guardar
                                </a>
                            </div>
                        </form>                        
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('admin.vinculaciones.partials.mantenimiento-rutinario-inicial')
@endsection

@section('scripts')
    <script>
      var arrComponente = {!! json_encode($componentes) !!};
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="{{ asset('assets/js/vinculaciones/index.js') }}"></script>
@endsection
