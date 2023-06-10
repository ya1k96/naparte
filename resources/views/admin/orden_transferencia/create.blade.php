@extends('layouts.admin-master')

@section('title')
    Ordenes de Transferencia
@endsection

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Orden de Transferencia</h1>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Nueva orden de Transferencia</h4>
                        </div>
                        
                        <form id="form-orden-transferencia" action="{{ route('admin.orden-transferencia.store') }}" method="POST">
                          @csrf

                          <input type="hidden" id="cant_tr" name="cant_tr" value="0">

                          <div class="card-body">                          
                            <div class="row">                              
                              <div class="col-md-6">
                                <div class="row">
                                  <div class="col-md-12">     
                                    <div class="font-weight-bold">Nº de OT</div>
                                    <input type="search" id="id" name="id" class="form-control" value="{{$next_id}}" readonly disabled>
                                  </div>                                        
                                  <div class="col-md-12">
                                    <div class="font-weight-bold">Base Origen</div>       
                                    <select id="base_origen_id" name="base_origen_id" class="form-control select2" data-placeholder="Seleccione" onchange="changeBase()">
                                        <option label="Seleccione" value="">Seleccione</option>
                                        @foreach ($arr_base_origen as $base)
                                            <option value="{{$base->id}}">{{$base->nombre}}</option>
                                        @endforeach
                                    </select>
                                  </div>
                                                                    
                                  <div class="col-md-12 mt-2">
                                    <div class="font-weight-bold">Base Destino</div>       
                                    <select id="base_destino_id" name="base_destino_id" class="form-control select2" data-placeholder="Seleccione">
                                        <option label="Seleccione" value="">Seleccione</option>
                                        @foreach ($arr_base_destino as $base)
                                            <option value="{{$base->id}}">{{$base->nombre}}</option>
                                        @endforeach
                                    </select>                                  
                                  </div>                     
                                </div>
                              </div>                              

                              <div class="col-md-6">
                                <div class="row">             
                                                                                                            
                                  <div class="col-md-12 mt-2">
                                    <div class="font-weight-bold">Observaciones</div>                   
                                    <textarea name="observaciones" class="form-control" style="height:165px;" placeholder="Observaciones"></textarea>
                                  </div>
                                </div>
                              </div>                              
                            </div>

                            <div class="row mt-3">
                              <div class="col-md-12">
                                <div class="table-responsive">
                                  <table class="table table-striped">
                                    <thead>
                                      <tr>
                                        <th class="text-left">Pieza</th>
                                        <th class="text-right">Cantidad</th>                                                                     
                                        <th class="text-center">Acciones</th>
                                      </tr>
                                    </thead>
                                    <tbody id="tbl_tbody_piezas"></tbody>
                                  </table>                                  
                                </div>                              
                              </div>
                            </div>

                            <div class="row mt-3">                                                          
                              <div class="col-md-12">
                                <hr>
                              </div>
                            </div>

                            <div class="row mt-3">                              
                              <div class="col-md-12">
                                <div class="row">                                             
                                  <div class="col-md-12">
                                    <h6>
                                      <div class="font-weight-bold">
                                        <u>
                                          Solicitado Por:
                                        </u>
                                      </div>
                                    </h6>                                    
                                  </div>

                                  <div class="col-md-12 mt-2">
                                    <div class="font-weight-bold">Nombre y apellido</div>                   
                                    <input type="text" id="solicitado_nombre" name="solicitado_nombre" class="form-control" placeholder="Nombre y apellido">
                                  </div>                                                             

                                </div>
                              </div>                              
                              <!--<div class="col-md-12">
                                <div class="row">                                             
                                  <div class="col-md-12">
                                    <h6>
                                      <div class="font-weight-bold">
                                        <u>
                                          Entregado Por:
                                        </u>
                                      </div>
                                    </h6>                                    
                                  </div>

                                  <div class="col-md-12 mt-2">
                                    <div class="font-weight-bold">Nombre y apellido</div>                   
                                    <input type="text" id="entregado_nombre" name="entregado_nombre" class="form-control" placeholder="Nombre y apellido">
                                  </div>

                                </div>
                              </div>-->
                            </div>

                                          
                            <div class="row mt-3">
                              <div class="col-md-12">
                                <div class="float-left">                                  
                                  <a href="{{route('admin.orden-transferencia.index')}}"  class="btn btn-secondary">
                                      Volver 
                                  </a>                                                                  
                                </div>
                                <div class="float-right">
                                  <a href="javascript:void(0);"  class="btn btn-primary" onclick="generar()">
                                      Generar 
                                  </a>                                
                                </div>
                              </div>
                            </div>
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
    var base_origen = 0
    var tr_id = 0
    
    function changeBase() {      
      base_origen = parseInt($("#base_origen_id").val())
      
      $('#tbl_tbody_piezas').empty();

      tr_id = 0      
      addItem()
    }
  </script>

  <script>            
    function addItem() {       
      tr_id = tr_id + 1
      var arrPiezas = {!! json_encode($arr_piezas) !!};

      var td1 = document.createElement("td");      
      td1.className = "text-left" 
      var input1 = document.createElement("select")
      input1.className = 'form-control'
      input1.name = 'pieza_id-'+tr_id
      input1.id = 'pieza_id-'+tr_id
      input1.dataPlaceholder = "Filtrar por Pieza"
      input1.addEventListener("change", getPieza);   

      let option = document.createElement("option")  
      option.setAttribute("label", "Filtrar por Pieza");
      option.setAttribute("value", "0");
      let optionTexto = document.createTextNode("Filtrar por Pieza");
      option.appendChild(optionTexto);     
      input1.appendChild(option);   
      
      arrPiezas.forEach((element, index) => {     
        if(element.base_id == base_origen) {
          let option = document.createElement("option")  
          option.setAttribute("value", element.id);
          let optionTexto = document.createTextNode(element.nro_pieza + ' - ' + element.descripcion);
          option.appendChild(optionTexto);     

          input1.appendChild(option);   
        }
      });
      td1.appendChild(input1);   
      
      var td2 = document.createElement("td");
      td2.className = "text-rigth" 
      var input2 = document.createElement("input")
      input2.type = "number"
      input2.step = "0.01"
      input2.className = "form-control"
      input2.style = "text-align: right;"
      input2.name = "cantidad-"+tr_id
      input2.id = "cantidad-"+tr_id
      input2.value = 0
      input2.addEventListener("blur", validCant);
      td2.appendChild(input2);      

      var td5 = document.createElement("td");
      td5.className = "text-center" 
      var linkAdd5 = document.createElement("a")
      linkAdd5.className = "btn btn-primary btn-icon" 
      linkAdd5.innerHTML = `<i class="fas fa-plus"></i>`
      linkAdd5.title = "Agregar Pieza"      
      linkAdd5.addEventListener("click", addItem);      
      linkAdd5.id = 'btn_add-' + tr_id
      linkAdd5.href = 'javascript:void(0);'
      td5.appendChild(linkAdd5);

      var linkDelete5 = document.createElement("a")
      linkDelete5.className = "btn btn-danger btn-icon" 
      linkDelete5.innerHTML = `<i class="fas fa-trash"></i>`
      linkDelete5.title = "Quitar Pieza"            
      linkDelete5.addEventListener("click", deleteItem);          
      linkDelete5.id = 'btn_delete-' + tr_id  
      linkDelete5.href = 'javascript:void(0);'
      td5.appendChild(linkDelete5);      

      var tr = document.createElement("tr");
      tr.id = 'tbl_tbody_tr-' + tr_id
      tr.appendChild(td1)    
      tr.appendChild(td2)     
      tr.appendChild(td5)    
      
      var tbody = document.getElementById("tbl_tbody_piezas");
      tbody.appendChild(tr)                    

      displayButton()

      document.getElementById('cant_tr').value = tr_id      
    }

    function displayButton() {      
      let minId = getFirstTr()
      let maxId = getLastTr()      

      for (let index = minId; index <= maxId; index++) {        
        if(document.getElementById('tbl_tbody_tr-' + index)) {
          if(index == minId) {
            document.getElementById("btn_add-" + minId).style.display = "inline-block"
            document.getElementById("btn_delete-" + minId).style.display = "none"
          } else {
            document.getElementById("btn_add-" + minId).style.display = "none"
            document.getElementById("btn_delete-" + minId).style.display = "inline-block"          
          }
          
          if(index>1 && index<maxId) {
            document.getElementById("btn_add-" + index).style.display = "none"
            document.getElementById("btn_delete-" + index).style.display = "inline-block"
          } 
          
          if(index>1 && index==maxId) {
            document.getElementById("btn_add-" + maxId).style.display = "inline-block"
            document.getElementById("btn_delete-" + maxId).style.display = "inline-block"
          } 
        }
      }
    }

    function getLastTr() {      
      let arrRows = document.querySelectorAll('#tbl_tbody_piezas tr');
      
      let maxId = 0
      for(let i=0; i<arrRows.length; i++) {
        let row = arrRows[i];        
        
        if(row.style.display != 'none') {
          id = parseInt(row.id.split('-')[1])

          if(maxId<id) {
            maxId = id
          }
        }
      }  
      return parseInt(maxId)
    }

    function getFirstTr() {      
      let arrRows = document.querySelectorAll('#tbl_tbody_piezas tr');
      
      let mixId = 1000
      for(let i=0; i<arrRows.length; i++) {
        let row = arrRows[i];        
        
        if(row.style.display != 'none') {
          id = parseInt(row.id.split('-')[1])

          if(mixId>id) {
            mixId = id
          }
        }
      }  
      return parseInt(mixId)
    }    

    function getPieza(evt) {
      var id = evt.currentTarget.id.split('-')[1]
      var id_inventario = evt.currentTarget.value
      
      var arrPiezas = {!! json_encode($arr_piezas) !!};      
      arrPiezas.forEach((element, index) => {                  
        if(element.id == id_inventario) {
          document.getElementById('cantidad-'+id).value = 1
        }        
      })
      
      if(parseFloat(id_inventario) == 0) {
        document.getElementById('cantidad-'+id).value = 0
      }
    }

    function validCant(evt) {      
      var arrPiezas = {!! json_encode($arr_piezas) !!};

      id = evt.currentTarget.id.split('-')[1]      
            
      var cantidad = document.getElementById('cantidad-'+id).value;
      var pieza_id = document.getElementById('pieza_id-'+id).value;      

      if(parseInt(cantidad) > 0 || cantidad) {        
        arrPiezas.forEach((element, index) => {     
          if(element.id == pieza_id) {
            if(parseInt(cantidad) > parseFloat(element.stock)) {    
              iziToast.warning({
                  title: 'Validación',
                  message: 'La cantidad ingresada supera el stock disponible: [STOCK DISPONIBLE--> ' + element.stock + ']'
              });        
              document.getElementById('cantidad-'+id).value = element.stock
              return false              
            } 
          }
        });
      } else {      
        document.getElementById('cantidad-'+id).value = 1
      }      
    }

    function isOkDetalle() {
      valid = true

      let arrRows = document.querySelectorAll('#tbl_tbody_piezas tr');
            
      for(let i=0; i<arrRows.length; i++) {
        let row = arrRows[i];        
        
        if(row.style.display != 'none') {
          id = parseInt(row.id.split('-')[1])
          let pieza_id = document.getElementById('pieza_id-'+id).value
          let cantidad = document.getElementById('cantidad-'+id).value          

          if(parseFloat(pieza_id) == 0) {
            valid = false
          }
          
          if(parseFloat(cantidad) == 0) {
            valid = false
          }                              
        }
      }  
      return valid
    }

    function generar() {
      var base_origen_id = document.getElementById('base_origen_id').value      
      var base_destino_id = document.getElementById('base_destino_id').value            
      var solicitado_nombre = document.getElementById('solicitado_nombre').value  
      //var entregado_nombre = document.getElementById('entregado_nombre').value          
            
      if(!base_origen_id) {
        iziToast.warning({
            title: 'Validación',
            message: 'Falta cargar la Base Origen'
        });        
        return false
      }         

      if(!base_destino_id) {
        iziToast.warning({
            title: 'Validación',
            message: 'Falta cargar la Base Destino'
        });        
        return false
      }         
      
      if(!solicitado_nombre) {
        iziToast.warning({
            title: 'Validación',
            message: 'Falta cargar el nombre de quien solicita'
        });        
        return false
      }         

      /*if(!entregado_nombre) {
        iziToast.warning({
            title: 'Validación',
            message: 'Falta cargar el nombre de quien entrega'
        });        
        return false
      }*/
      
      valid = isOkDetalle()
      if(!valid) {
        iziToast.warning({
            title: 'Validación',
            message: 'Verifique el detalle de la orden de transferencia'
        });        
        return false        
      }

      if(confirm("¿Desea generar la orden de transferencia?") == true) {
        document.getElementById('form-orden-transferencia').submit();
      }                          
    }

    function deleteItem(evt) {
      tr_delete = evt.currentTarget.id.split('-')[1]
      //document.getElementById("tbl_tbody_tr-" + tr_delete).style.display = "none"
      document.getElementById("tbl_tbody_tr-" + tr_delete).remove()
      displayButton()
    }
  </script>
@endsection
