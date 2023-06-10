@extends('layouts.admin-master')

@section('title')
Ver Movimientos
@endsection

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Movimientos</h1>
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
                            <h4>Movimientos de inventario de {{$inventario->piezas->nro_pieza}} - {{$inventario->piezas->descripcion}} || {{ $inventario->base_operacion->nombre }}</h4>
                        </div>
                        <div class="card-body">
                            <h6 class="text-black mb-4"><strong>Stock actual: </strong>{{$inventario->stock_total}} {{$inventario->piezas->unidadMedida->nombre}}</h6>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr class="text-center">
                                            <th>Fecha</th>
                                            <th>Ingreso</th>
                                            <th>Egreso</th>
                                            <th>Precio</th>
                                            <th>Ubicación</th>
                                            <th>Orden de Compra</th>
                                            <th>Orden de Trabajo</th>
                                            <th>Vale</th>
                                            <th>Orden de Transferencia</th>
                                            <th>Devolucion</th>
                                            <th>Usuario</th>
                                        </tr>
                                    </thead>
                                    <tbody class="scroll-x">
                                        <!-- TODO: Esto tiene que filtrar por base, falta. -->
                                        @forelse ($inventario->movimientos as $movimiento)
                                            <tr class="text-center">
                                                <td>{{ \Carbon\Carbon::parse($movimiento->fecha)->format('d/m/Y H:i:s') }}</td>
                                                @if ($movimiento->orden_compra_id)
                                                    <td>{{ $movimiento->cantidad }}</td>
                                                    <td> - </td>
                                                @elseif ($movimiento->orden_trabajo_id && $movimiento->vale_id)
                                                    <td> - </td>
                                                    <td>{{ $movimiento->cantidad }}</td>
                                                @elseif ($movimiento->orden_transferencia_id)
                                                    @if($movimiento->balance == '+')                                                    
                                                        <td>{{ $movimiento->cantidad }}</td>
                                                        <td> - </td>
                                                    @else
                                                        <td> - </td>
                                                        <td>{{ $movimiento->cantidad }}</td>
                                                    @endif
                                                @else
                                                    <td>{{ $movimiento->cantidad }}</td>
                                                    <td> - </td>
                                                @endif
                                                <td>{{ $movimiento->precio_unitario }}</td>
                                                <td>{{ $movimiento->ubicacion }}</td>
                                                <td>{{ $movimiento->orden_compra_id ?? '-' }}</td>
                                                <td>{{ $movimiento->orden_trabajo->numeracion ?? '-' }}</td>
                                                <td>{{ $movimiento->vale_id ?? '-' }}</td>
                                                <td>{{ $movimiento->orden_transferencia_id ?? '-' }}</td>
                                                <td>{{ $movimiento->devolucion_detalle }}</td>
                                                <td>{{ $movimiento->user->name }}</td>
                                        @empty
                                    <tr>
                                        <td>No se encontraron movimientos</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="buttons">
                            <div class="float-right">
                                <nav aria-label="Page navigation example">
                                    <ul class="pagination">
                                        <li class="page-item">
                                            <ul role="navigation" class="pagination">
                                                <li aria-disabled="true" aria-label="« Anterior"
                                                    class="page-item disabled"><span aria-hidden="true"
                                                        class="page-link">‹</span></li>
                                                <li aria-current="page" class="page-item active"><span
                                                        class="page-link">1</span></li>
                                                <li class="page-item"><a
                                                        href="#"
                                                        class="page-link">2</a></li>
                                                <li class="page-item"><a
                                                        href="#"
                                                        rel="next" aria-label="Siguiente »" class="page-link">›</a></li>
                                            </ul>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection