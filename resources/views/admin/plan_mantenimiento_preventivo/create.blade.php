@extends('layouts.admin-master')

@section('title')
    Plan de mantenimiento preventivo
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">
@endsection

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Plan de Mantenimiento Preventivo</h1>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Nuevo plan</h4>
                        </div>
                        <form action="{{ route('admin.plan-mantenimiento-preventivo.store') }}" method="post">
                            @csrf
                            <div class="card-body">
                                <div class="form-group row mb-4">
                                    <label for="nombre" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Nombre del plan <code>*</code></label>
                                    <div class="col-sm-12 col-md-7">
                                        <input type="text" id="nombre_plan" class="form-control {{ $errors->has('nombre') ? ' is-invalid': '' }}" name="nombre" onkeyup="fillField()" autocomplete="off">
                                        <div class="invalid-feedback">
                                            {{ $errors->first('nombre') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <h6 class=" text-md-right col-12 col-md-3 col-lg-3">Componentes</h6>
                                    <div class="col-sm-12 col-md-9">
                                        {{-- begin: Tree JS --}}
                                        <div class="main-ctn">
                                            <div class="left">
                                                <div class="form-inline">
                                                    <input id="p_name" class="form-control" placeholder="Nombre del Plan" readonly/>
                                                </div>
                                                <div class="form-inline">
                                                    <ul id="tree" class="tree"></ul>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="hidden" id="template">
                                            <ul>
                                                <li class="tree-node">
                                                    <input placeholder="Componente" name="fieldName[][]" class="form-control fieldName" autocomplete="off"/>
                                                    <span class="controls">
                                                        <a class="btn btn-success mr-1" title="Agregar componente" href="#" data-func="add-sibling"
                                                            ><i class="fas fa-plus"></i>
                                                        </a>
                                                        <a class="btn btn-info mr-1" href="#" title="Agregar sub-componente" data-func="add-child"
                                                            ><i class="fas fa-indent"></i>
                                                        </a>
                                                        <a class="btn btn-danger" href="#" data-func="delete"
                                                            ><i class="fas fa-times"></i>
                                                        </a>
                                                    </span>
                                                </li>
                                            </ul>
                                        </div>
                                        {{-- end: Tree JS --}}
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-right">
                                <a href="{{ route('admin.plan-mantenimiento-preventivo') }}">
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
    <script>
        const fillField = () => {
            let write = document.getElementById('nombre_plan').value;
            document.getElementById('p_name').value = write;
        }

        // Se quita el botón eliminar del primer elemento del árbol
        // let tree = document.querySelector(".tree")
        // tree.remove()
    </script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.slim.min.js"></script>
    <script src="{{ asset('assets/js/tree.js')}}"></script>
@endsection
