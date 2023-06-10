@extends('layouts.admin-master')

@section('title')
Consultar Vales
@endsection

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Consultar Vales</h1>
    </div>
    <div class="section-body">

        <div class="row">
            <div class="col-12">
                <!-- Init card -->
                <div class="card">
                    <!-- CARD HEADER -->
                    <div class="card-header">
                        <div class="row col-12">
                            <!-- TITLE -->
                            <div class="col-6">
                                <h4>Listado de Vales</h4>
                            </div>
                            <!-- PAGINATOR -->
                            <div class="col-6 d-flex justify-content-end">
                                <nav aria-label="Page navigation example">
                                    <ul class="pagination">
                                        <li class="page-item">
                                            <ul role="navigation" class="pagination">
                                                <li class="page-item">{{ $vales->links() }}</li>
                                            </ul>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>

                    <!-- CARD BODY -->
                    <div class="card-body">
                        <div class="table-responsive table-invoice">
                            <!-- Table -->
                            <table class="table table-striped">
                                <tbody>
                                    <!-- Titles table -->
                                    <tr class="text-center">
                                        <th>ID</th>
                                        <th>Fecha</th>
                                        <th>Folio OT</th>
                                        <th>Unidad</th>
                                        <th>Tipo</th>
                                        <th>Recursos</th>
                                    </tr>
                                    @forelse ($vales as $vale)
                                    @if (!empty($vale->ordenes_trabajo->unidad))
                                    <!-- Complete table -->
                                    <tr class="text-center">
                                        <td>{{$vale->id}}</td>
                                        <td>{{$vale->fecha}}</td>
                                        <td>{{$vale->ordenes_trabajo->numeracion}}
                                            {{$vale->ordenes_trabajo->base_operacion->nombre}}
                                        </td>
                                        <td>{{$vale->ordenes_trabajo->unidad->num_interno. ' // '.$vale->ordenes_trabajo->unidad->modelo->nombre}}
                                        </td>
                                        <td>{{$vale->ordenes_trabajo->tipo_orden}}</td>
                                        <!-- Edit params -->
                                        <td>
                                            <!-- See resources -->
                                            <a class="btn btn-sm btn-primary ver-recursos-btn" data-toggle="collapse" href="#" role="button" aria-expanded="false" data-id="{{$vale->id}}">
                                                Ver recursos
                                            </a>
                                            <!-- Delet Vale -->
                                            @if(!$vale->deleted_at)
                                            <a href="{{ route('admin.vale.generarPDF', $vale->id) }}" target="_blank" class="btn btn-sm btn-success" title="Imprimir">
                                                <i class="fas fa-print"></i>
                                            </a>
                                            @endif
                                            @if(!$vale->deleted_at)
                                            @if ($vale->cerrado == false)
                                            <a class="btn btn-sm btn-info" href="{{ route('admin.vale.editar', $vale->id) }}" aria-expanded="false" title='Editar'><i class="fa fa-edit"></i></a>
                                            <a class="btn btn-sm btn-info" href="{{ route('admin.vale.devolucion', $vale->id) }}" aria-expanded="false" title='Devolucion'><i class="fa fa-undo"></i></a>
                                            <a class="btn btn-sm btn-warning" href="{{ route('admin.vale.cerrar', $vale->id) }}" aria-expanded="false" title='Cerrar' onclick="return confirm('Se cerrará el vale con id {{ $vale->id }}. ¿Continuar?')"><i class="fas fa-times-circle"></i></a>
                                            @else
                                            <button type="button" class="btn btn-sm btn-secondary">Cerrado</button>
                                            @if ($vale->ordenes_trabajo->status == 'Abierta')
                                            <a href="{{route('admin.vale.reabrir', $vale->id)}}" class="btn btn-sm btn-success" title="Reabrir">Reabrir</a>
                                            @endif
                                            @endif
                                            @else
                                            <button type="button" class="btn btn-sm btn-dark">Anulado</button>
                                            @endif
                                        </td>
                                        @endif
                                        @empty
                                        <td colspan="5" class="text-center">No hay Vales.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row" id="listado_recursos">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive table-invoice">
                            <table class="table table-striped">
                                <thead>
                                    <tr class="text-center">
                                        <th>Material</th>
                                        <th>Parte</th>
                                        <th>Cantidad</th>
                                        <th>Unidad</th>
                                    </tr>
                                </thead>
                                <tbody id="tr-table-racursos" class="text-center">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
@endsection

@section('scripts')
<script src="{{ asset('assets/js/vale/index.js') }}"></script>
@endsection