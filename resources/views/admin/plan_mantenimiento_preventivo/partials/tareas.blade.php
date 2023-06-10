@if($subcomponente)
    @php
        $color = $arr_color[4];
        if(isset($arr_color[$index_color])) {
            $color = $arr_color[$index_color];
        }
    @endphp

    <div style="margin-left: {{$margin_left}}px;">
        <div>                                                                
            <input class="form-control fieldName mb-1" value="{{ $nombre }}" autocomplete="off" readonly style="width: 100% !important; background: {{$color}}; color: #fff;"/>                                                                                
        </div>
        @if(count($tareas))
            <div class="card-body p-0">
                <table class="table table-striped table-sm">
                    <thead>
                        <tr class="text-center">                        
                            <th scope="col" width="50%">Actividades</th>
                            <th scope="col" width="20%">Especialidad</th>
                            <th scope="col" width="20%">Frecuencia</th>
                            <th scope="col" width="10%">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">                                                        
                        @foreach ($tareas as $tarea)
                        <tr>
                            <td>
                                - {{ $tarea->descripcion }}                            
                            </td>
                            <td>                            
                                @if ($tarea->especialidad)
                                    {{ $tarea->especialidad->nombre }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if ($tarea->frecuencia == 'kms')
                                    {{ $tarea->kilometros }} {{ $tarea->frecuencia }}
                                @endif

                                @if ($tarea->frecuencia == 'dias')
                                    {{ $tarea->dias }} {{ $tarea->frecuencia }}
                                @endif

                                @if ($tarea->frecuencia == 'combinado')
                                    {{ $tarea->kilometros }} kms // {{ $tarea->dias }} dias
                                @endif
                            </td>
                            <td>
                                <form action="{{ route('admin.tareas.destroy', $tarea->id) }}" method="post">
                                    <a href="{{ route('admin.tareas.edit', $tarea->id)}}" class="btn btn-primary btn-sm">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    @csrf
                                    @method('delete')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Se eliminará la tarea {{ $tarea->descripcion }}. ¿Continuar?')">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>                            
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
        
        @if(count($subcomponente->subcomponentes))
            @foreach ($subcomponente->subcomponentes as $componente)                                            
                <div>
                    @include('admin.plan_mantenimiento_preventivo.partials.tareas',[
                        'subcomponente' => $componente,
                        'tareas' => $componente->tareas, 
                        'nombre' => $componente->nombre, 
                        'arr_color' => $arr_color,
                        'index_color' => $index_color + 1,
                        'margin_left' => $margin_left + 70,
                    ])
                </div>                                                                                                                       
            @endforeach    
        @endif
    </div>
@endif