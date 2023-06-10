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
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Nuevo inventario</h4>
                        </div>
                        <form action="{{ route('admin.inventario.store') }}" method="post">
                            @csrf
                            <div class="card-body">
                                <input type="hidden" name="_token" value="{{ Session::token() }}">
                                <div class="form-group row">
                                    <div class="col-md-4">
                                        <label for="bases_operacion_id">Base de operación al cual corresponde el inventario</label>
                                    </div>
                                    <div class="col-md-4">
                                        <select name="bases_operacion_id" id="bases_operacion_id" class="form-control select2" data-placeholder="Filtrar base de operación">
                                            <option label="Seleccione una base de operación" value="">Seleccione una Base de operación</option>
                                            @foreach ($bases_operacion_id as $base)
                                                <option value="{{$base->id}}">{{$base->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 float-right">
                                        <a class="btn btn-success mr-1 add-sibling" title="Agregar componente" href="#">
                                            <i class="fas fa-plus"></i> Agregar Pieza
                                        </a>
                                    </div>
                                </div>
                                <div class="form-group row">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th>Pieza</th>
                                                    <th>Compra Única</th>
                                                    <th>Stock</th>
                                                    <th>Precio</th>
                                                    <th>Ubicación</th>
                                                    <th>Máximo de compra</th>
                                                    <th>Mínimo de compra</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody id="tabla_listado">
                                                <tr>
                                                    <td>
                                                        <select name="listado_piezas[0][pieza_id]" id="piezas-primer" class="form-control select2 listado-piezas" data-placeholder="Piezas" style="width:200px;" required></select>
                                                    </td>
                                                    <td style="text-align: center;vertical-align: middle;">
                                                        <input type="checkbox" class="{{-- form-control --}}" name="listado_piezas[0][compra_unica]" id="checkbox-listado-primer" value="0">
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control" name="listado_piezas[0][stock]" id="stock-primer" required>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control" name="listado_piezas[0][precio]" step="0,01" id="precio-primer" required>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" name="listado_piezas[0][ubicacion]" id="ubicacion-primer" required>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control" name="listado_piezas[0][maximo_compra]" id="max-listado-primer">
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control" name="listado_piezas[0][minimo_compra]" id="min-listado-primer">
                                                    </td>
                                                    <td>
                                                        
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                </div>
                            </div>
                            <div class="card-footer text-right">
                                <a href="{{ route('admin.inventario') }}">
                                    <button type="button" class="btn btn-secondary mr-1">Cancelar</button>
                                </a>
                                <button type="submit" class="btn btn-primary">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/inventario/index.js') }}"></script>
@endsection
