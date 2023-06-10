@extends('layouts.admin-master')

@section('title')
Listado de Proveedores
@endsection

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Listado de Proveedores</h1>
    </div>
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="col-6">Agregar proveedores</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" id="form-proveedor" action=" {{ route('admin.proveedor.store') }} ">
                            <input type="hidden" name="_token" value="{{ Session::token() }}">
                            <div class="form-group row mb-4">
                                @csrf
                                <label for="" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Nombre*</label>
                                <div class="col-sm-12 col-md-7">
                                    <div class="input-group">
                                        <input class="form-control {{ $errors->has('nombre') ? ' is-invalid' : '' }}" type="text" id="nombre" name="nombre" required>
                                        <div class="invalid-feedback">
                                            {{ $errors->first('nombre') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label for="" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">CUIT*</label>
                                <div class="col-sm-12 col-md-7">
                                    <div class="input-group">
                                        <input class="form-control {{ $errors->has('cuit') ? ' is-invalid' : '' }}" type="text" id="cuit" name="cuit" required>
                                        <div class="invalid-feedback">
                                            {{ $errors->first('cuit') }}
                                        </div>
                                    </div>
                                    <p><small>El CUIT debe tener 11 números sin guiones ni espacios.</small></p>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <div class="col-sm-12 col-md-7 offset-md-3 text-md-right">
                                    <button type="submit" action="{{ route('admin.proveedor.store') }}" class="btn btn-primary submit-proveedor">Agregar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @if($arrProveedorActivo || $arrProveedorInactivo)
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="col-6">Listado de proveedores</h4>
                        <div class="input-group d-flex flex-row-reverse">
                            <form href="{{ route('admin.proveedor') }}" method="GET">
                                <div class="input-group-btn d-flex flex-row ">
                                    <input type="search" name="buscar" class="form-control mr-2" placeholder="Buscar" value="{{$buscar}}">
                                    <button id="search" title="buscar" class="btn btn-primary btn-icon mr-3"> <i class="fas fa-search"></i> </button>
                                    <a href="{{ route('admin.proveedor') }}" title="limpiar" class="btn btn-lighty btn-icon"><i class="fas fa-redo"></i></a>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        {{-- active or inactive --}}
                        <ul class="nav nav-pills flex-column flex-sm-row px-5 pt-2" id="pills-tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link" id="pills-one-tab" data-toggle="pill" href="#pills-one" role="tab" aria-controls="pills-one" aria-selected="true">
                                    Activo @if($arrProveedorActivo->count() > 0) <b>({{$arrProveedorActivo->count()}})</b> @endif
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="pills-two-tab" data-toggle="pill" href="#pills-two" role="tab" aria-controls="pills-two" aria-selected="false">
                                    Inactivo @if($arrProveedorInactivo->count() > 0) <b>({{$arrProveedorInactivo->count()}})</b> @endif
                                </a>
                            </li>
                        </ul>
                        {{-- list of proveedores --}}
                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade" id="pills-one" role="tabpanel" aria-labelledby="pills-one-tab">
                                <table class="table table-striped">
                                    <thead>
                                        <tr class="text-center">
                                            <th>Id</th>
                                            <th>Cuit</th>
                                            <th>Nombre</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($arrProveedorActivo as $proveedor)
                                        <tr class="text-center">
                                            <td>{{$proveedor->id}}</td>
                                            <td>{{$proveedor->cuit}}</td>
                                            <td>{{$proveedor->nombre}}</td>
                                            <td>
                                                @if (!$proveedor->deleted_at)
                                                    <form action=" {{ route('admin.proveedor.destroy', $proveedor->id) }} " method="POST">
                                                        <a href="{{ route('admin.proveedor.edit', $proveedor->id) }}" class="btn btn-primary"><i class="fa fa-edit"></i></a>
                                                        @csrf
                                                        @method('delete')
                                                        <button class="btn btn-danger" onclick="return confirm('Se eliminará el proveedor {{$proveedor->nombre}}. ¿Continuar?')"><i class="fa fa-trash"></i></button>
                                                    </form>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-dark">Anulado</button>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="tab-pane fade" id="pills-two" role="tabpanel" aria-labelledby="pills-two-tab">
                                <table class="table table-striped">
                                    <thead>
                                        <tr class="text-center">
                                            <th>Id</th>
                                            <th>Cuit</th>
                                            <th>Nombre</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($arrProveedorInactivo as $proveedor)
                                        <tr class="text-center">
                                            <td>{{$proveedor->id}}</td>
                                            <td>{{$proveedor->cuit}}</td>
                                            <td>{{$proveedor->nombre}}</td>
                                            <td>
                                                @if (!$proveedor->deleted_at)
                                                    <form action=" {{ route('admin.proveedor.destroy', $proveedor->id) }} " method="POST">
                                                        <a href="{{ route('admin.proveedor.edit', $proveedor->id) }}" class="btn btn-primary"><i class="fa fa-edit"></i></a>
                                                        @csrf
                                                        @method('delete')
                                                        <button class="btn btn-danger" onclick="return confirm('Se eliminará el proveedor {{$proveedor->nombre}}. ¿Continuar?')"><i class="fa fa-trash"></i></button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('admin.proveedor.restore') }}" method="post">
                                                        <input type="hidden" value="{{ $proveedor->id }}" name="id">
                                                        @csrf
                                                        @method('post')
                                                        <button type="submit" class="btn btn-primary" onclick="return confirm('Quiere recuperar el Proveedor: {{ $proveedor->nombre }}. ¿Continuar?')">
                                                            <i class="fas fa-trash-restore"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</section>
@endsection

@section('scripts')
    // update class for active or inactive list table
    <script>
        let proveedoresActivoLengh = "{{ $arrProveedorActivo->count() }}"
        let proveedoresInactivoLengh = "{{ $arrProveedorInactivo->count() }}"

        document.addEventListener('DOMContentLoaded', function(){
            let activeTab = document.getElementById('pills-one-tab')
            let activeTable = document.getElementById('pills-one')
            let inactiveTab = document.getElementById('pills-two-tab')
            let inactiveTable = document.getElementById('pills-two')

            if( activeTab && activeTable && inactiveTab && inactiveTable ){
                if ( proveedoresActivoLengh > 0 ) {
                    inactiveTab.classList.remove('active')
                    inactiveTable.classList.remove('show','active')
                    activeTab.classList.add('active')
                    activeTable.classList.add('show','active')
                } else if ( proveedoresInactivoLengh > 0 ) {
                    activeTab.classList.remove('active')
                    activeTable.classList.remove('show','active')
                    inactiveTab.classList.add('active')
                    inactiveTable.classList.add('show','active')
                }
            }
        })
    </script>

    <script src="{{ asset('assets/js/proveedores/index.js') }}"></script>
@endsection