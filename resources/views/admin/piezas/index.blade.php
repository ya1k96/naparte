@extends('layouts.admin-master')

@section('title')
    Piezas de catálogo
@endsection

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Piezas de catálogo</h1>
        </div>
        <div class="section-body">
            <div class="section-title">
                <a href="{{ route('admin.piezas-de-catalogo.create') }}">
                    <button type="button" class="btn btn-primary">Agregar nuevo</button>
                </a>
                <a href="{{ route('admin.piezas.importar') }}">
                    <button type="button" class="btn btn-info">Importar Piezas</button>
                </a>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="col-6">Listado de piezas</h4>
                        </div>
                        <div class="card-body">
                            <form action="" method="GET">
                                <!-- <div class="float-left">
                                    <select name="categorias[]" id="categorias" class="form-control-lg select2 select2-hidden-accessible" data-placeholder="Seleccionar una categoria" multiple>
                                        <option value="0" disabled>Seleccione una categoría</option>
                                        @foreach ($categorias as $categoria)
                                            <option value="{{ $categoria->id }}" {{ ($categoria->id == $filtro_categoria['id']) ? 'selected' : '' }}>{{ $categoria->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div> -->
                                <div class="float-right">
                                    <div class="input-group d-flex flex-row-reverse">
                                        <div class="input-group-btn d-flex flex-row">
                                            <input type="search" name="buscar" class="form-control" placeholder="Buscar" value="{{ $buscar }}" autocomplete="off">
                                            <button class="btn btn-primary btn-icon"><i class="fas fa-search"></i></button>
                                            <a href="{{ route('admin.piezas-de-catalogo') }}" class="btn btn-lighty btn-icon"><i class="fas fa-redo"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="clearfix mb-3"></div>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr class="text-center">
                                            <th scope="col">N° de pieza</th>
                                            <th scope="col">Descripción</th>
                                            <th scope="col">Unidad de medida</th>
                                            <!-- <th scope="col">Categorías</th> -->
                                            <th scope="col">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($piezas as $pieza)
                                        <tr class="text-center">
                                            <td>{{ $pieza->nro_pieza }}</td>
                                            <td>{{ $pieza->descripcion }}</td>
                                            <td>{{ $pieza->unidadMedida->nombre }}</td>
                                            <!-- <td>
                                                @foreach ($pieza->categorias as $categoria)
                                                    <div class="badge badge-primary">{{ $categoria->nombre }}</div>
                                                @endforeach
                                            </td> -->
                                            <td>
                                                @if ($pieza->deleted_at != null)
                                                <a href="{{ route('admin.piezas-de-catalogo.restore', $pieza->id) }}" class="btn btn-success">
                                                    <i class="fas fa-check-circle"></i>
                                                </a>
                                                @else
                                                <form action="{{ route('admin.piezas-de-catalogo.destroy', $pieza->id) }}" method="post">
                                                    <a href="{{ route('admin.piezas-de-catalogo.edit', $pieza->id) }}" class="btn btn-primary">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    @csrf
                                                    @method('delete')
                                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Se desactivará la pieza {{ $pieza->nro_pieza }}. ¿Continuar?')">
                                                        <i class="fas fa-times-circle"></i>
                                                    </button>
                                                </form>
                                                @endif
                                            </td>
                                        @empty
                                            <td colspan="5" class="text-center">No se han encontrado coincidencias</td>
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
                                            <li class="page-item">{{ $piezas->links() }}</li>
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

@section('scripts')
    <script>
        // Permite seleccionar sólo dos categorías
        let options = null;

        $('#categorias').change(function(event) {
            if ($(this).val().length > 2) {
                $(this).val(options);
            } else {
                options = $(this).val();
            }
        });
    </script>
@endsection
