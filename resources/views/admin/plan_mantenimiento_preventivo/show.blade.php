@extends('layouts.admin-master')

@section('title')
    Plan de mantenimiento preventivo
@endsection

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Ver plan</h1>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-block text-center">
                            <h2 class="text-dark">Plan de mantenimiento preventivo</h2>
                            <h3 class="text-primary">{{ $plan->nombre }}</h3>
                        </div>
                        <div class="card-body p-0 mx-3 mb-3">
                            <a href="{{ route('admin.plan-mantenimiento-preventivo') }}">
                                <button type="button" class="btn btn-primary">Volver a la lista de planes</button>
                            </a>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">       
                                    @php
                                        $arr_color = array(
                                            'darkgreen',
                                            'darkred',
                                            'darkmagenta',
                                            'darkgoldenrod',
                                            'darkgray',
                                        )
                                    @endphp

                                    @foreach ($componentes as $componente)                                                                                    
                                        @include('admin.plan_mantenimiento_preventivo.partials.tareas',[
                                            'subcomponente' => $componente,
                                            'tareas' => $componente->tareas, 
                                            'nombre' => $componente->nombre,                                             
                                            'arr_color' => $arr_color,
                                            'index_color' => 0,
                                            'margin_left' => 0,
                                        ])                                        
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
