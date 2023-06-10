@extends('layouts.admin-master')

@section('title')
    Asociar / Desasociar recursos
@endsection

@section('content')
    <div class="section">
        <div class="section-header">
            <h1>Asociar / Desasociar recursos</h1>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>{{ 'Parte '.$componente->nombre.' / Actividad '.$tarea->descripcion }}</h4>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-12 col-md-1">
                                    <label for="recurso" class="col-form-label">Recurso</label>
                                </div>
                                <div class="col-12 col-md-2">
                                    <select name="recurso" id="recurso_id" class="form-control select2" data-placeholder="Filtrar por recurso">
                                        <option label="Seleccione un recurso" value="">Seleccione un recurso</option>
                                        @foreach ($recursos as $recurso)
                                            <option value="{{$recurso->id}}" data-actividad-id="{{ $tarea->id }}" data-unidad-id="{{ $unidadId }}" data-unidad-text="{{ $recurso->unidadMedida->nombre}}">{{$recurso->id_descripcion}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-md-1">
                                    <label for="recurso" class="col-form-label">Cantidad</label>
                                </div>
                                <div class="col-12 col-md-2">
                                    <input type="number" name="cant_recurso" id="cant_recurso" class="form-control">
                                </div>
                                <div class="col-12 col-md-2">
                                    <td style="text-align: center;vertical-align: middle;">
                                        <input type="checkbox" class="form-check-input" name="replicar_todas" id="replicar_todas" value="0">
                                    </td>
                                    <label for="recurso" class="form-check-label">Replicar a todas las unidades de este plan</label>
                                </div>
                                <div class="col-12 col-md-2">
                                    <a class="btn btn-success text-white" id="agregar-recurso-pieza" title="Agregar componente">
                                        <i class="fas fa-plus"></i> Agregar Recurso
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <form method="POST" action="{{ route('admin.tarea.save-recursos') }}">
                            @csrf
                                <input type="hidden" name="_token" value="{{ Session::token() }}">
                                <input type="hidden" class="form-control" value="{{ $tarea->id }}"  name="tarea_id">
                                <input type="hidden" class="form-control" value="{{ $unidadId }}"  name="unidad_id">
                                <input type="hidden" class="form-control" value="{{ $unidad[0]->vinculaciones[0]->plan->id }}"  name="plan_id" id="plan_id">
                                <input type="hidden" class="form-control" value="{{ $componente->id }}"  name="componente_id" id="componente_id">
                                
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Recurso</th>
                                            <th>Cantidad</th>
                                            <th>Unidad de Medida</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tabla_listado_recursos">
                                        <?php if($recursosActividades):foreach ($recursosActividades as $key => $recursoAct) :?>
                                            <tr class="index-{{ $key }} tr-padre">
                                                <?php if(!isset($recursoAct->pieza[0])) continue; ?>
                                                <td class="id_descripcion" data-nombre="{{ $recursoAct->pieza[0]->id_descripcion  }}">
                                                    {{ $recursoAct->pieza[0]->id_descripcion  }}
                                                </td>
                                                <td>
                                                    <input type="number" value="{{ $recursoAct->cantidad }}" required class="form-control cantidad" min="0" name="listado_piezas_recursos[{{ $recursoAct->pieza_id }}][cantidad]" readonly data-valor="{{ $recursoAct->cantidad }}">
                                                </td>
                                                <td>
                                                    <input type="text" value="{{ $recursoAct->pieza[0]['unidadMedida']->nombre }}" class="form-control" disabled name="listado_piezas_recursos[{{ $recursoAct->pieza_id }}][unidad]">
                                                </td>
                                                <td>
                                                    <span class="controls editar-recurso-span"><a class="btn btn-primary editar-recurso text-white" title="Editar recurso" data-index="{{ $key }}"><i class="fas fa-edit"></i></a></span>
                                                    <span class="controls cancelar-editar-recurso-span" hidden><a class="btn btn-warning cancelar-editar-recurso text-white" title="Cancelar editar recurso" data-index="{{ $key }}"><i class="fas fa-times"></i></a></span>
                                                    <span class="controls confirmar-editar-recurso-span" hidden><a class="btn btn-success confirmar-editar-recurso text-white" title="Confirmar editar recurso" data-index="{{ $key }}"><i class="fas fa-check"></i></a></span>
                                                    <span class="controls"><a class="btn btn-danger delete-row text-white" title="Eliminar recurso" data-index="{{ $key }}"><i class="fas fa-trash"></i></a></span>
                                                </td>
                                                <input type="hidden" class="form-control tarea_id" value="{{ $tarea->id }}"  name="listado_piezas_recursos[{{ $recursoAct->pieza_id }}][tarea_id]">
                                                <input type="hidden" class="form-control" value="{{ $unidadId }}" name="listado_piezas_recursos[{{ $recursoAct->pieza_id }}][unidad_id]">
                                                <input type="hidden" class="form-control pieza_id" value="{{ $recursoAct->pieza_id }}"  name="listado_piezas_recursos[{{ $recursoAct->pieza_id }}][pieza_id]">
                                            </tr>
                                        <?php endforeach;endif; ?>
                                    </tbody>
                                </table>

                                <div class="card-footer text-right">
                                    <a href="{{ route('admin.recurso-actividad', ['unidad' => $unidadId]) }}">
                                        <button type="button" class="btn btn-secondary mr-1">Cargar a otra actividad</button>
                                    </a>
                                    <!-- <button type="submit" name="action" value="guardar"
                                        class="btn btn-primary">Guardar</button> -->
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/mantenimiento_rutinario/index.js') }}"></script>
@endsection
