@extends('layouts.admin-master')

@section('title')
Dashboard
@endsection

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Dashboard</h1>
        </div>

        <div class="section-body">
            @foreach($notificaciones as $notificacion)
                <div class="card" id="card-{{ $notificacion->id }}">
                    <div class="card-body">
                        <p class="card-text">{{ $notificacion->descripcion }}</p>
                        <a href="#" class="card-link continuar"
                            id="continuar-modal-{{ $notificacion->unidad_id }}"
                            data-token="{{ Session::token() }}"
                            data-notification_id="{{ $notificacion->id }}"
                            data-unidad_id="{{ $notificacion->unidad_id }}"
                            data-url="{{ route('admin.unidad-notificacion.extender') }}"
                        >Continuar deshabilitada</a>

                        <a href="#" class="card-link habilitar"
                            id="habilitar-modal-{{ $notificacion->unidad_id }}"
                            data-token="{{ Session::token() }}"
                            data-notification_id="{{ $notificacion->id }}"
                            data-unidad_id="{{ $notificacion->unidad_id }}"
                            data-url="{{ route('admin.unidad-notificacion.desactivar') }}"
                        >Habilitar</a>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
    @include('admin.dashboard.partials.habilitar_modal')
    @include('admin.dashboard.partials.continuar_modal')
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/dashboard.js') }}"></script>
@endsection
