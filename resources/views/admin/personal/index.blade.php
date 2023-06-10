@extends('layouts.admin-master')

@section('title')
    Personal
@endsection

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Personal</h1>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Agregar personal</h4>
                        </div>
                        <div class="card-body">
                            @if($personal)           
                              <form action="{{ route('admin.personal.update', $personal->id) }}" method="post">
                              @csrf
                              @method('put')
                            @else
                              <form action="{{ route('admin.personal.store') }}" method="post">
                              @csrf
                            @endif
                                
                                <div class="row">
                                  <div class="form-group col-md-5">
                                      <label for="nombre" class="col-form-label col-12 col-md-12 col-lg-12">Nombre <code>*</code></label>
                                      <div class="col-sm-12 col-md-12">   
                                          @if($personal)                                    
                                            <input type="text" name="nombre" class="form-control {{ $errors->has('nombre') ? ' is-invalid' : '' }}" autocomplete="off" value="{{$personal->nombre}}">
                                          @else
                                            <input type="text" name="nombre" class="form-control {{ $errors->has('nombre') ? ' is-invalid' : '' }}" autocomplete="off">
                                          @endif

                                          <div class="invalid-feedback">
                                              {{ $errors->first('nombre') }}
                                          </div>                                          
                                      </div>
                                  </div>

                                  <div class="form-group col-md-5">
                                    <label for="especialidad_id" class="col-form-label col-12 col-md-12 col-lg-12">Especialidad <code>*</code></label>
                                    <div class="col-sm-12 col-md-12">
                                      <select name="especialidad_id" id="especialidad_id" class="form-control {{ $errors->has('especialidad_id') ? ' is-invalid' : '' }}">
                                          <option value="0">-- Seleccionar Especialidad</option>
                                          @foreach ($especialidades as $especialidad)
                                              @if($personal) 
                                                <option value="{{ $especialidad->id }}" {{ ($personal->especialidad_id == $especialidad->id) ? 'selected' : ''}}>{{ $especialidad->nombre }} </option>
                                              @else 
                                                <option value="{{ $especialidad->id }}">{{ $especialidad->nombre }} </option>                                                 
                                              @endif
                                          @endforeach
                                      </select>
                                      <div class="invalid-feedback">
                                          {{ $errors->first('especialidad_id') }}
                                      </div>
                                    </div>
                                  </div>                                  

                                  <div class="form-group col-md-2">                                    
                                    <button type="submit" class="btn btn-primary" style="margin-top:35px;">
                                      @if($personal)
                                        Guardar
                                      @else
                                        Agregar
                                      @endif
                                    </button>                                  
                                  </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            @if($arrPersonalActivo || $arrPersonalInactivo)
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="col-6">Listado del Personal</h4>
                            <div class="input-group d-flex flex-row-reverse">
                                <form action="" method="GET" id="form-id">                              
                                    <div class="input-group-btn d-flex flex-row">                                        
                                        <input type="search" name="buscar" class="form-control" placeholder="Buscar" value="{{ $buscar }}" autocomplete="off">
                                        <button id="search" class="btn btn-primary btn-icon"><i class="fas fa-search"></i></button>
                                        <a href="{{ route('admin.personal') }}" class="btn btn-lighty btn-icon"><i class="fas fa-redo"></i></a>
                                    </div>
                                </form>
                            </div>
                        </div>

                        
                        <div class="card-body">
                            <ul class="nav nav-pills flex-column flex-sm-row" id="pills-tab" role="tablist">
                              <li class="nav-item">
                                <a class="nav-link" id="pills-one-tab" data-toggle="pill" href="#pills-one" role="tab" aria-controls="pills-one" aria-selected="true">
                                  Activo @if($arrPersonalActivo->count() > 0) <b>({{ $arrPersonalActivo->count() }})</b> @endif
                                </a>
                              </li>
                              <li class="nav-item">
                                <a class="nav-link" id="pills-two-tab" data-toggle="pill" href="#pills-two" role="tab" aria-controls="pills-two" aria-selected="false">
                                  Inactivo @if($arrPersonalInactivo->count() > 0) <b>({{ $arrPersonalInactivo->count() }})</b> @endif
                                </a>
                              </li>                                      
                            </ul>    
                            <div class="tab-content" id="pills-tabContent">
                              <div class="tab-pane fade" id="pills-one" role="tabpanel" aria-labelledby="pills-one-tab">
                                <table class="table table-striped">
                                    <thead>
                                        <tr class="text-center">
                                            <th scope="col">Id</th>
                                            <th scope="col">Nombre</th>
                                            <th scope="col">Especialidad</th>
                                            <th scope="col">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($arrPersonalActivo as $per)                                    
                                          <tr class="text-center">
                                              <td>{{ $per->id }}</td>
                                              <td>{{ $per->nombre }}</td>
                                              <td>{{ $per->especialidad->nombre }}</td>

                                              <td>
                                                <form action="{{ route('admin.personal.destroy', $per->id) }}" method="post">
                                                    <a href="{{ route('admin.personal.edit', $per->id) }}" class="btn btn-primary">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    @csrf
                                                    @method('delete')
                                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Se eliminará el personal {{ $per->nombre }}. ¿Continuar?')">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
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
                                              <th scope="col">Id</th>
                                              <th scope="col">Nombre</th>
                                              <th scope="col">Especialidad</th>
                                              <th scope="col">Acciones</th>
                                          </tr>
                                      </thead>
                                      <tbody>
                                          @foreach ($arrPersonalInactivo as $per)                                    
                                            <tr class="text-center">
                                                <td>{{ $per->id }}</td>
                                                <td>{{ $per->nombre }}</td>
                                                <td>{{ $per->especialidad->nombre }}</td>
                                                
                                                <td>
                                                  <form action="{{ route('admin.personal.restore') }}" method="post">
                                                      <input type="hidden" value="{{ $per->id }}" name="id">
                                                      @csrf
                                                      @method('post')
                                                      <button type="submit" class="btn btn-primary" onclick="return confirm('Quiere recuperar el personal {{ $per->nombre }}. ¿Continuar?')">
                                                          <i class="fas fa-trash-restore"></i>
                                                      </button>
                                                  </form>
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
        let personalActivoLengh = "{{ $arrPersonalActivo->count() }}"
        let personalInactivoLengh = "{{ $arrPersonalInactivo->count() }}"

        document.addEventListener('DOMContentLoaded', function(){
            let activeTab = document.getElementById('pills-one-tab')
            let activeTable = document.getElementById('pills-one')
            let inactiveTab = document.getElementById('pills-two-tab')
            let inactiveTable = document.getElementById('pills-two')

            if( activeTab && activeTable && inactiveTab && inactiveTable ){
                if ( personalActivoLengh > 0 ) {
                    inactiveTab.classList.remove('active')
                    inactiveTable.classList.remove('show','active')
                    activeTab.classList.add('active')
                    activeTable.classList.add('show','active')
                } else if ( personalInactivoLengh > 0 ) {
                    activeTab.classList.remove('active')
                    activeTable.classList.remove('show','active')
                    inactiveTab.classList.add('active')
                    inactiveTable.classList.add('show','active')
                }
            }
        })
    </script>
@endsection