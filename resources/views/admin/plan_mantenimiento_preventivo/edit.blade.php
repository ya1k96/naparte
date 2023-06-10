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
                            <h4>Editar plan</h4>
                        </div>
                        <form action="{{ route('admin.plan-mantenimiento-preventivo.update', $plan->id) }}" method="post">
                            @csrf
                            @method('put')
                            <div class="card-body">
                                <div class="form-group row mb-4">
                                    <label for="nombre" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Nombre del plan <code>*</code></label>
                                    <div class="col-sm-12 col-md-7">
                                        <input type="text" id="nombre_plan" class="form-control {{ $errors->has('nombre') ? ' is-invalid': '' }}" name="nombre" value="{{ $plan->nombre }}" onkeyup="fillField()" autocomplete="off">
                                        <div class="invalid-feedback">
                                            {{ $errors->first('nombre') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <h6 class=" text-md-right col-12 col-md-3 col-lg-3">Componentes</h6>
                                    <div class="col-sm-12 col-md-9">
                                        {{-- begin: Tree JS --}}
                                        <div class="main-ctn mb-1">
                                            <div class="left">
                                                <div class="form-inline">
                                                    <input id="p_name" class="form-control" value={{ $plan->nombre }} readonly/>
                                                </div>
                                                <div class="form-inline">
                                                    <ul class="tree">
                                                        @foreach ($componentes as $componente)
                                                    <li class="tree-node componente">
                                                        <input name="fieldName[]" class="form-control fieldName" value="{{ $componente->nombre }}" data-id="{{ $componente->id }}" />
                                                        <span class="controls">
                                                            <a class="btn btn-success mr-1" title="Agregar componente" href="#" data-func="add-sibling">
                                                                <i class="fas fa-plus"></i>
                                                            </a>
                                                            <a class="btn btn-info mr-1" href="#" title="Agregar sub-componente" data-func="add-child">
                                                                <i class="fas fa-indent"></i>
                                                            </a>
                                                            <a class="btn btn-primary mr-1 subcomponente-task" href="{{ route('admin.tarea.subcomponente', $componente->id) }}" title="Agregar tarea">
                                                                <i class="fas fa-tasks"></i>
                                                            </a>
                                                            <a class="btn btn-danger" href="#" data-func="delete">
                                                                <i class="fas fa-times"></i>
                                                            </a>
                                                        </span>
                                                    </li>
                                                @if(count($componente->subcomponentes))
                                                    @include('admin.plan_mantenimiento_preventivo.partials.subcomponentes',['subcomponentes' => $componente->subcomponentes, 'fieldName' => "fieldName[" . $componente->nombre . "][]"])
                                                @endif
                                            @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="hidden" id="template">
                                            <ul>
                                                <li class="tree-node">
                                                    <input placeholder="Componente" name="fieldName[][]" class="form-control fieldName" />
                                                    <span class="controls">
                                                        <a class="btn btn-success mr-1" title="Agregar componente" href="#" data-func="add-sibling"
                                                            ><i class="fas fa-plus"></i>
                                                        </a>
                                                        <a class="btn btn-info mr-1" href="#" title="Agregar sub-componente" data-func="add-child"
                                                            ><i class="fas fa-indent"></i>
                                                        </a>
                                                        <a class="btn btn-primary mr-1 subcomponente-task" href="{{ route('admin.tarea.subcomponente') }}" title="Agregar tarea"
                                                            ><i class="fas fa-tasks"></i>
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

        var plan_id = {{ $plan->id }}

        // Se quita el botón eliminar del primer elemento del árbol
        const tree = document.querySelector('.tree')
        const delete_button = tree.firstElementChild.childNodes

        if (delete_button.length == 5) {
            delete_button[3].lastElementChild.remove()
        } else {
            delete_button[2].lastElementChild.remove()
        }

        // Update Componente
        let fieldsName = document.querySelectorAll(".fieldName"),
            _token = $('input[name="_token"]').val();

        var url = HOST

        for (const iterator of fieldsName) {
            iterator.addEventListener('keyup', function (e) {
                let nombre = iterator.value,
                    id = parseInt(iterator.dataset.id);

                $.ajax({
                    url: url + "/admin/actualizar-componente",
                    data: {
                        id,
                        nombre
                    },
                    cache: false,
                    type: 'POST',
                    dataType: 'json',
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('X-CSRF-Token', _token);
                    },
                    success: function (resp) {
                        //
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log(errorThrown);
                    }
                });
            })
        }
    </script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="{{ asset('assets/js/tree-edit.js')}}"></script>
@endsection
