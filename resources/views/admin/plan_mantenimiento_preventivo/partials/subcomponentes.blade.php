<ul>
    @foreach($subcomponentes as $subcomponente)
        <div class="form-inline mb-1">
            <li class="tree-node">
                <input name="{{ $fieldName }}[{{ $subcomponente->nombre}}][]" class="form-control fieldName" value="{{ $subcomponente->nombre }}" data-id="{{ $subcomponente->id }}" autocomplete="off"/>
                <span class="controls">
                    <a class="btn btn-info mr-1" href="#" title="Agregar sub-componente" data-func="add-child"><i class="fas fa-indent"></i>
                    </a>
                    <a class="btn btn-primary mr-1" href="{{ route('admin.tarea.subcomponente', $subcomponente->id) }}" title="Agregar tarea"><i class="fas fa-tasks"></i>
                    </a>
                    <a class="btn btn-danger" href="#" data-func="delete"><i class="fas fa-times"></i>
                    </a>
                </span>
            </li>
        </div>
        @if(count($subcomponente->subcomponentes))
            @include('admin.plan_mantenimiento_preventivo.partials.subcomponentes',['subcomponentes' => $subcomponente->subcomponentes, 'fieldName' => $fieldName . "[" . $subcomponente->nombre . "][]"])
        @endif
    @endforeach
</ul>
