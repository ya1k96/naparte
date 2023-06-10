@extends('layouts.admin-master')

@section('title')
    Inventario
@endsection

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Inventario</h1>
        </div>
        <div class="section-body">
            <div class="section-title">
                <a href="{{ route('admin.inventario') }}">
                    <button type="button" class="btn btn-primary">Volver a la lista de inventarios</button>
                </a>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Inventario</h4>
                        </div>
                        <div class="card-body p-0">
                            <ul>
                                <li>Pieza: {{$inventario->piezas->descripcion}}</li>
                                <li>Número: {{$inventario->piezas->nro_pieza}}</li>
                                <li>Pañol: {{$inventario->base_operacion->nombre}}</li>
                                <li>Stock: {{$inventario->stock_total}}</li>
                                <li>Precio: {{$inventario->precio}}</li>
                                <li>Ubicación: {{$inventario->ubicacion}}</li>
                                @if (!empty($inventario->minimo_compra))
                                <li>Max de compra: {{$inventario->maximo_compra}}</li>
                                @endif
                                @if (!empty($inventario->minimo_compra))
                                <li>Min de compra: {{$inventario->minimo_compra}}</li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
