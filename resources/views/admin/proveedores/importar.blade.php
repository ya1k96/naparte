@extends('layouts.admin-master')

@section('title')
    Importar Proveedores
@endsection

@section('content')
    @if (empty($mensajes))
    <div class="col-12 col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                <h4 class="">Importar Proveedores</h4>
                <div class="card-header-form">
                    <a href="{{route('admin.proveedor.descargarEjemplo')}}" class="btn btn-dark" escape="false"><i class="fa fa-download"></i> Plantilla Ejemplo</a>
                </div>
            </div>

            <form action="{{ route('admin.proveedor.importarStore') }}" method="post" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_token" value="{{ Session::token() }}">
                <div class="card-body">
                    <div class="input-group mb-3">
                        <div class="col-12">
                            <div class="custom-file">
                                <input class="custom-file-input" type="file" name="archivo" accept=".csv" lang="es" id="inputGroupFile01" required>
                                <label class="custom-file-label" for="inputGroupFile01">Seleccionar Archivo</label>
                            </div>
                            <small>> Para el archivo .csv el separador debe ser " ; "</small>
                        </div>
                    </div>
                </div>

                <div class="card-footer text-right">
                    <a href="{{ route('admin.proveedor') }}">
                        <button type="button" class="btn btn-secondary mr-1">Cancelar</button>
                    </a>
                    <button type="submit" class="btn btn-primary">Importar</button>
                </div>
            </form> 
        </div>
    </div>
    @endif



    <!-- RESULTADOS -->
    @if (!empty($mensajes))
    <div class="col-12 col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                <div class="col-xs-12 col-sm-12 col-md-8 col-lg-10">
                    <h4 class="">Resultado de la importación</h2>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-2 ocultar_imprimir">
                    <a href="#" class="btn btn-light btn-block" escape="false" onClick="window.print();"><i class="fa fa-download"></i> Imprimir resultado</a>
                </div>
            </div>


            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr class="active">
                                <th>N° de Pieza</th>
                                <th colspan="1">Errores</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($mensajes as $k => $mensaje)
                            <tr>
                                <td>{{$k}}</td>
                                <td>{{$mensaje}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection

@section('scripts')
    <script>
        document.querySelector('.custom-file-input').addEventListener('change', function (e) {
            var name = document.getElementById("inputGroupFile01").files[0].name;
            var nextSibling = e.target.nextElementSibling
            nextSibling.innerText = name
        })
    </script>
@endsection