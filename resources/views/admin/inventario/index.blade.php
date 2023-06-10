@extends('layouts.admin-master')

@section('title')
    Inventario
@endsection

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Listado del inventario</h1>
        </div>
        <div class="section-body">
            <div class="section-title">
                <a href="{{ route('admin.inventario.create') }}">
                    <button type="button" class="btn btn-primary">Agregar nueva Pieza</button>
                </a>
                 <a href="{{ route('admin.inventario.exportar', request()->all()) }}">
                    <button type="button" class="btn btn-green">Exportar</button>
                </a>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="col-6">Listado de inventarios</h4>
                        </div>
                        <div class="card-body">
                            <form action="" method="GET">
                                <div class="input-group-btn d-flex {{-- flex-row --}}">
                                    <div class="mr-2">
                                        <select name="base_operacion" id="base_operacion" class="form-control select2" data-placeholder="Filtrar base de operación">
                                            <option label="Seleccione una base de operación" value="">Seleccione una Base de operación</option>
                                            @foreach ($bases_operaciones as $base)
                                                <option value="{{$base->id}}" {{ ($base->id == $buscar_base_operacion) ? 'selected' : ''}}>{{$base->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mr-auto">
                                        <select name="pieza" id="pieza" class="form-control select2" data-placeholder="Filtrar pieza">
                                            <option label="Seleccione una pieza" value="">Seleccione una Pieza</option>
                                            @foreach ($piezas as $pieza)
                                                <option value="{{$pieza->id}}" {{ ($pieza == $buscar_pieza) ? 'selected' : ''}}>{{$pieza->nro_pieza}} - {{$pieza->descripcion}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mr-auto">
                                        <div class="input-group-btn d-flex flex-row">
                                            <div class="font-weight-bold">Buscar hasta la fecha</div>
                                            <input type="date" name="fecha_hasta" class="form-control" value="{{ $buscar_fecha_hasta }}">
                                        </div>
                                    </div>
                                    <div class="float-right">
                                        <div class="input-group d-flex flex-row-reverse">
                                            <div class="input-group-btn d-flex flex-row">
                                                <input type="search" name="buscar" class="form-control" placeholder="Ingrese la ubicacion" value="{{ $buscar }}" autocomplete="off">
                                                <button class="btn btn-primary btn-icon">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                                <a href="{{ route('admin.inventario') }}" class="btn btn-lighty btn-icon">
                                                    <i class="fas fa-redo"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="clearfix mb-3"></div>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr class="text-center">
                                        @php
                                            $direction = request('direction') ?? 'asc';
                                            if($direction == 'desc'){
                                                $direction = 'asc';
                                            }else if($direction == 'asc'){
                                                $direction = 'desc';
                                            }
                                        @endphp
                                            <th>Pieza</th>
                                            <th>Número</th>
                                            <th>Pañol</th>
                                            <th>Stock</th>
                                            <th>Precio</th>
                                            <th>
                                                Ubicacion
                                                <a href="{{ request()->fullUrlWithQuery(['order' => 'ubicacion', 'direction' => $direction]) }}">
                                                @if ($direction == 'asc')
                                                    <i class="fas fa-sort-up"></i>
                                                @else
                                                    <i class="fas fa-sort-down"></i>
                                                @endif
                                                </a>
                                            </th>
                                            <th>Max de compra</th>
                                            <th>Min de compra</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody >
                                        @forelse ($inventarios as $inventario)
                                        {{-- @dd($inventario->movimientos); --}}
                                        <tr class="text-center">
                                            <td>{{ $inventario->piezas->descripcion }}</td>
                                            <td>{{ $inventario->piezas->nro_pieza }}</td>
                                            <td>{{ $inventario->base_operacion->nombre }}</td>
                                            <td>{{ $inventario->stock_total }}</td>
                                            <td>{{ $inventario->last_price }}</td>
                                            <td>{{ $inventario->ubicacion }}</td>
                                            <td>{{ $inventario->maximo_compra }}</td>
                                            <td>{{ $inventario->minimo_compra }}</td>
                                            <td>
                                                <form action="{{ route('admin.inventario.destroy', $inventario->id) }}" method="post">
                                                    @csrf
                                                    @method('delete')
                                                    <a href="{{ route('admin.inventario.show', $inventario->id) }}" class="btn btn-secondary" title="Ver">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.inventario.verMovimientos', $inventario->id) }}" class="btn btn-info" title="Ver movimientos">
                                                        Ver movimientos
                                                    </a>
                                                </form>
                                            </td>
                                        @empty
                                            <td colspan="6" class="text-center">No se encontraron resultados</td>
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
                                            <li class="page-item">{{ $inventarios->appends(request()->input())->links() }}</li>
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

{{-- @section('scripts')
    <script src="{{ asset('assets/js/inventario/index.js') }}"></script>
@endsection --}}
