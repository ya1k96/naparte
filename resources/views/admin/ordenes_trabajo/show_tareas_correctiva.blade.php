@extends('layouts.admin-master')

@section('title')
    Ordenes de trabajo
@endsection

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Ordenes de trabajo</h1>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Seleccionar tareas Preventivas realizadas para la unidad {{$orden->unidad->num_interno}} en la OT Correctiva N° {{$orden->numeracion}}</h4>
                        </div>
                        <form action="{{ route('admin.ordenes-trabajo.actualizarTareasPreventivas') }}" method="post">
                            @csrf
                            <input type="hidden" name="orden_trabajo_id" value="{{$orden->id}}">
                            <div class="card-body">
                                <div class="clearfix mb-3"></div>
                                <div class="table-responsive table-invoice">
                                    <table class="table table-striped">
                                        <tbody>
                                            @if(!empty($tareas_arr))
                                            <tr class="text-center">
                                                <th>Seleccionar</th>
                                                <th>Tarea</th>
                                                <th>Especialidad</th>
                                                <th>N° OT Preventiva</th>
                                            </tr>
                                                @foreach($tareas_arr as $tarea)
                                                    <tr class="text-center">
                                                        <td>
                                                            <div class="form-check">
                                                                <input class="form-check-input" name="tareas_seleccionadas[]" type="checkbox" value="{{$tarea['tarea']->id}}" class="check_tarea" {{$tarea['ot_abierta']? 'disabled' : ''}}>
                                                            </div>
                                                        </td>
                                                        <td>{{$tarea['tarea']->descripcion}}</td>
                                                        <td>{{$tarea['tarea']->especialidad->nombre}}</td>
                                                        <td>{{$tarea['ot_abierta'] ? $tarea['ot_abierta'] . ' ' . $tarea['base_operacion'] : '-'}}</td>
                                                    </tr>
                                                @endforeach
                                            @else
                                            <tr>
                                                <td colspan="3">Esta Unidad no tiene tareas.</td>
                                            </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer text-right">
                                <a href="{{ route('admin.ordenes-trabajo') }}">
                                    <button type="button" class="btn btn-secondary mr-1">Cancelar</button>
                                </a>
                                <button type="submit" id="submit" class="btn btn-primary">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection