@extends('layouts.admin-master')

@section('title')
    Historial de kilómetros
@endsection

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Historial de Kms</h1>
        </div>
        <div class="section-body">
            <div class="section-title">
                <a href="{{ route('admin.historial.create') }}">
                    <button type="button" class="btn btn-primary">Cargar nuevo historial</button>
                </a>
            </div>
            <form action="" method="GET">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Seleccione unidad</h4>
                            </div>
                            <div class="card-body">
                                <div class="form-group row mb-4">
                                    <label for="unidad" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Unidad</label>
                                    <div class="col-sm-12 col-md-7">
                                        <select name="unidad_id" id="unidad_id" class="form-control select2" data-placeholder="Seleccione una unidad">
                                            <option label="Selecciona una unidad" value="">Selecciona una unidad</option>
                                            @foreach ($unidades as $unidad)
                                                <option value="{{ $unidad->id }}" {{ $unidad_id == $unidad->id ? 'selected' : "" }}>{{ $unidad->num_interno }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="col-6">Historial de unidad</h4>
                                <div class="input-group d-flex flex-row-reverse">
                                    <div class="form-inline">
                                        <div class="form-group mx-3">
                                            <label class="mr-1">Desde: </label>
                                            <input type="date" class="form-control" name="desde" value="{{ $desde }}">
                                        </div>
                                        <div class="form-group mx-3">
                                            <label class="mr-1">Hasta: </label>
                                            <input type="date" class="form-control" name="hasta" value="{{ $hasta }}">
                                        </div>
                                        <button class="btn btn-primary btn-icon">
                                            <i class="fas fa-search"></i>
                                        </button>
                                        <a href="{{ route('admin.historial') }}" class="btn btn-lighty btn-icon"><i class="fas fa-redo"></i></a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body" id="card-body">
                              
                                <ul class="nav nav-pills" id="myTab3" role="tablist">
                                  <li class="nav-item">
                                    <a class="nav-link active show" id="tab1-tab" data-toggle="tab" href="#tab1" role="tab" aria-controls="tab1" aria-selected="true">Listado del Historial</a>
                                  </li>
                                  <li class="nav-item">
                                    <a class="nav-link" id="tab2-tab" data-toggle="tab" href="#tab2" role="tab" aria-controls="tab2" aria-selected="false">Gráfico del Historial</a>
                                  </li>
                                  <li class="nav-item">
                                    <a class="nav-link" id="tab3-tab" data-toggle="tab" href="#tab3" role="tab" aria-controls="tab3" aria-selected="false">Gráfico del Promedio</a>
                                  </li>
                                </ul>
                                <div class="tab-content" id="myTabContent2">
                                  <div class="tab-pane fade active show" id="tab1" role="tabpanel" aria-labelledby="tab1-tab">
                                    <div class="mt-3 font-weight-bold">
                                      <u>Promedio:</u> <b>{{$promedio}}</b>
                                    </div>
                                    <table class="table table-striped table-sm">
                                      <thead>
                                          <tr class="text-center">
                                              <th>Kilometraje</th>
                                              <th>Fecha</th>
                                              <th>Observación</th>
                                          </tr>
                                      </thead>
                                      <tbody id="historial">
                                          {{-- Mostrar historial --}}
                                          @foreach ($historiales as $key => $historial)
                                            @if($historial->id == $id_km_inicial) 
                                              <tr class="text-center table-secondary">
                                                <td>{{ $historial->kilometraje }}</td>
                                                <td>{{ $historial->created_at->format('d/m/Y') }}</td>
                                                <td>                                                  
                                                  <span class="badge badge-info">
                                                    KM Iniciales
                                                  </span>                                                  
                                                </td>
                                              </tr>
                                            @else
                                              <tr class="text-center">
                                                <td>{{ $historial->kilometraje }}</td>
                                                <td>{{ $historial->created_at->format('d/m/Y') }}</td>
                                                <td></td>
                                              </tr>
                                            @endif
                                          @endforeach
                                      </tbody>
                                    </table>

                                    <div class="float-right mt-3">
                                      {{$historiales->appends($_GET)->links('pagination::bootstrap-4')}}
                                    </div>
                                  </div>
                                  <div class="tab-pane fade" id="tab2" role="tabpanel" aria-labelledby="tab2-tab">
                                    <div class="mt-3 font-weight-bold">
                                      <u>Promedio:</u> <b>{{$promedio}}</b>
                                    </div>
                                    <div class="card card-statistic-2">
                                        <div class="card-header">
                                            <h4>Control de Lecturas - Historial de Lecturas</h4>
                                        </div>
                                        <div class="card-body">
                                            <div>
                                                <canvas id="myChart"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                  </div>
                                  <div class="tab-pane fade" id="tab3" role="tabpanel" aria-labelledby="tab3-tab">
                                    <div class="mt-3 font-weight-bold">
                                      <u>Promedio:</u> <b>{{$promedio}}</b>
                                    </div>
                                    <div class="card card-statistic-2">
                                        <div class="card-header">
                                            <h4>Control de Lecturas - Promedios de Uso Mensual</h4>
                                        </div>
                                        <div class="card-body">
                                            <div>
                                                <canvas id="myChart2"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                  </div>
                                </div>                            
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection

@section('scripts')
    <script>      
      $('#unidad_id').on('change', function (e) {
          let id = e.target.value                 
          let param = document.location.search.split('&')
          let search = '?'
          var paramAlt = []

          if(param[0] == '') {
            search = search + 'unidad_id=' + id
          } else {            
            param.forEach(element => {
              if(element.includes('unidad_id')) {
                search = search + 'unidad_id=' + id
              } else {
                if(!element.includes('page')) {
                  paramAlt.push(element)
                }
              }
            });            
          }

          paramAlt.forEach(element => {
            search = search + '&' + element        
          });

          document.location.href = search
      })
    </script>


    <script>
      function formatearFecha(fecha) {
          var today = new Date(fecha);
          var dd = today.getDate();

          var mm = today.getMonth()+1; 
          var yyyy = today.getFullYear();
          if(dd<10) 
          {
              dd='0'+dd;
          } 

          if(mm<10) 
          {
              mm='0'+mm;
          } 
          return today = dd+'/'+mm+'/'+yyyy;
      }      
    </script>

    <!-- Gráficos -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


    <?php if($historiales_grafico): ?>
    <script>
        
        var myChart = Chart.getChart('myChart'); 
        if(myChart){
            myChart.destroy();
        }
        var kilometrajes = {!! json_encode($historiales_grafico->toArray()) !!};
        let data_chart_nueva = []
        let label_chart_nueva = []
        let init_eje_y = 0
        $.each(kilometrajes, function (i, item) {                        
          data_chart_nueva.push(item.kilometraje);
          fecha = formatearFecha(item.created_at)
          label_chart_nueva.push(fecha)

          if(init_eje_y==0) {
            init_eje_y = item.kilometraje
          }                          
        });
        labels = label_chart_nueva;

        data = {
            labels: labels,
            datasets: [{
              label: 'Uso Mensual',
              backgroundColor: 'rgb(105, 195, 174)',
              borderColor: 'rgb(105, 195, 174)',
              data: data_chart_nueva,
            }]
        };

        config = {
            type: 'line',
            data: data,
            options: {
              scales: {
                  y: {
                      type: 'linear',
                      min: init_eje_y 
                  }
              }              
            }
        };

        myChart = new Chart(
            document.getElementById('myChart'),
            config
        );
    </script>

    <script>
        var myChart2 = Chart.getChart('myChart2'); 
        if(myChart2){
            myChart2.destroy();
        }
        var kilometrajes_mes = {!! json_encode($calcular_promedios) !!};
        
        let data_chart_nueva2 = []
        let label_chart_nueva2 = []        
        $.each(kilometrajes_mes, function (i, item) {
            $.each(item, function(j, mes) {
                //let promedio = mes.total / mes.cantidad;
                let km_acumulado = mes.max - mes.min;
                data_chart_nueva2.push(km_acumulado);
                label_chart_nueva2.push(j+'/'+i);
            })
        });
        labels2 = label_chart_nueva2;
        
        var primedio_estimativo = {!! json_encode($calcular_promedios_estimativo) !!}

        data2 = {
            labels: labels2,
            datasets: [{
              type: 'line',
              label: 'Uso Estimativo',
              backgroundColor: 'rgb(255, 0, 0)',
              borderColor: 'rgb(255, 0, 0)',
              pointStyle: 'circle',
              pointRadius: 10,
              pointHoverRadius: 15,     
              fill: false,
              showLine: false,
              data: primedio_estimativo,
            },{
              type: 'bar',
              label: 'Uso Mensual',
              backgroundColor: 'rgb(105, 195, 174)',
              borderColor: 'rgb(105, 195, 174)',
              data: data_chart_nueva2,
            }]
        };

        config2 = {
            data: data2,
            options: {}
        };

        myChart2 = new Chart(
            document.getElementById('myChart2'),
            config2
        );
    </script>
    <?php endif; ?>
@endsection

