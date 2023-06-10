@extends('layouts.admin-master')

@section('title')
    Ordenes de compra
@endsection

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Orden de compra</h1>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Nueva orden de compra</h4>
                        </div>
                        <form id="form-orden-compra" action="{{ route('admin.orden-compra.update', $orden->id) }}" method="post">
                          @method('PATCH')
                          @csrf

                          <input type="hidden" id="cant_tr" name="cant_tr" value="0">
                          <input type="hidden" id="elementos_borrados" name="elementos_borrados" value="">

                          <div class="card-body">
                            <div class="row">
                              <div class="col-md-3">
                                <div class="row">
                                  <div class="col-md-12">
                                    <div class="font-weight-bold">Nº de OC</div>
                                    <input type="search" id="id" name="id" class="form-control" value="{{$orden->id}}" readonly disabled>
                                  </div>
                                  <div class="col-md-12 mt-2">
                                    <div class="font-weight-bold">Fecha Emision</div>
                                    <input type="date" id="fecha_emision" name="fecha_emision" class="form-control" value="{{$orden->fecha_emision ?? ''}}"> {{-- ->format('Y-m-d') --}}
                                  </div>
                                  <div class="col-md-12 mt-2">
                                    <div class="font-weight-bold">Fecha Entrega</div>
                                    <input type="date" id="fecha_entrega" name="fecha_entrega" class="form-control" value="{{$orden->fecha_entrega ?? ''}}">
                                  </div>
                                  {{-- enterprise --}}
                                  <div class="col-md-12 mt-2">
                                    <div class="font-weight-bold">Empresa</div>
                                    <select id="empresa_id" name="empresa_id" class="form-control select2" data-placeholder="Seleccione">
                                        <option label="Seleccione" value="">Seleccione</option>
                                        @foreach ($arr_empresas as $empresa)
                                            <option value="{{$empresa->id}}" {{($orden->empresa_id == $empresa->id) ? 'selected' : ''}}>{{$empresa->nombre}}</option>
                                        @endforeach
                                    </select>
                                  </div>
                                </div>
                              </div>

                              <div class="col-md-4">
                                <div class="row">
                                  <div class="col-md-12">
                                    <div class="font-weight-bold">Base Emisora</div>
                                    <select id="base_emite_id" name="base_emite_id" class="form-control select2" data-placeholder="Seleccione">
                                        <option label="Seleccione" value="">Seleccione</option>
                                        @foreach ($arr_base_emite as $base)
                                            <option value="{{$base->id}}" {{($orden->base_emite_id == $base->id) ? 'selected' : ''}}>{{$base->nombre}}</option>
                                        @endforeach
                                    </select>
                                  </div>

                                  <div class="col-md-12 mt-2">
                                      <div class="font-weight-bold">Proveedor</div>
                                      <select id="proveedor_id" name="proveedor_id" class="form-control select2" data-placeholder="Seleccione">
                                          <option label="Seleccione" value="">Seleccione</option>
                                          @foreach ($arr_proveedores as $proveedor)
                                              <option value="{{$proveedor->id}}" {{($orden->proveedor_id == $proveedor->id) ? 'selected' : ''}}>{{$proveedor->nombre}}</option>
                                          @endforeach
                                      </select>
                                  </div>

                                  <div class="col-md-12 mt-2">
                                    <div class="font-weight-bold">Base Receptora</div>
                                    <select id="base_recibe_id" name="base_recibe_id" class="form-control select2" data-placeholder="Seleccione" onchange="changeBase()">
                                        <option label="Seleccione" value="">Seleccione</option>
                                        @foreach ($arr_base_recibe as $base)
                                            <option value="{{$base->id}}" {{($orden->base_recibe_id == $base->id) ? 'selected' : ''}}>{{$base->nombre}}</option>
                                        @endforeach
                                    </select>
                                  </div>
                                </div>
                              </div>

                              <div class="col-md-5">
                                <div class="row">
                                  <div class="col-md-12">
                                    <div class="font-weight-bold">Prioridad</div>
                                    <select id="prioridad" name="prioridad" class="form-control select2" data-placeholder="Seleccione">
                                        <option label="Seleccione" value="">Seleccione</option>
                                        @foreach ($arr_prioridades as $key => $prioridad)
                                            <option value="{{$key}}" {{($orden->prioridad == $key) ? 'selected' : ''}}>{{$prioridad}}</option>
                                        @endforeach
                                    </select>
                                  </div>
                                  <div class="col-md-12 mt-2">
                                    <div class="font-weight-bold">Observaciones</div>
                                    <textarea name="observaciones" class="form-control" style="height:115px;" placeholder="Observaciones">{{$orden->observaciones ?? ''}}</textarea>
                                  </div>
                                  <div class="col-md-12 mt-2 float-right">
                                    <a href="javascript:void(0);" class="btn btn-primary btn-icon" id="add-event" title="Agregar Pieza" style="display: inline-block;">Agregar Item <i class="fas fa-plus"></i></a>
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
                                        <th class="text-right">Costo Unitario</th>
                                        <th class="text-right">Monto</th>
                                        <th class="text-center">Acciones</th>
                                      </tr>
                                    </thead>
                                    <tbody id="tbl_tbody_piezas_0">
                                          @foreach($orden->detalle as $detalle)
                                            <tr class="count-tr" id="tbl_tbody_tr-{{$detalle->id}}">
                                              <td>
                                                <input type="hidden" name="elementos[{{$detalle->id}}][pieza_id]" id="pieza_id-{{$detalle->id}}" value="{{$detalle->piezas_id}}">
                                                <input type="text" class="form-control" value="{{$detalle->pieza->nro_pieza.'-'.$detalle->pieza->descripcion}}" readonly>
                                              </td>
                                              <td>
                                                <input type="number" step="0.01" class="form-control event-blur" style="text-align: right;" name="elementos[{{$detalle->id}}][cantidad]" id="cantidad-{{$detalle->id}}" value="{{$detalle->cantidad}}">
                                              </td>
                                              <td>
                                                <input type="number" step="0.01" class="form-control event-blur" style="text-align: right;" name="elementos[{{$detalle->id}}][costo]" id="costo-{{$detalle->id}}" value="{{$detalle->costo}}">
                                              </td>
                                              <td>
                                                <input type="number" step="0.01" class="form-control" style="text-align: right;" name="elementos[{{$detalle->id}}][monto]" id="monto-{{$detalle->id}}" readonly="" disabled="" value="{{$detalle->cantidad * $detalle->costo}}">
                                              </td>
                                              <td class="text-center">
                                                <a class="btn btn-danger btn-icon delete-event" title="Quitar Pieza" id="btn_delete-{{$detalle->id}}" href="javascript:void(0);"><i class="fas fa-trash"></i></a>
                                              </td>
                                            </tr>
                                          @endforeach
                                    </tbody>
                                    <tbody id="tbl_tbody_piezas"></tbody>
                                  </table>
                                  <table class="table table-striped" id="tbl_footer_piezas" style="display: none;">
                                    <thead>
                                      <tr>
                                        <th class="text-right w-75">TOTAL</th>
                                        <td class="text-right w-25 font-wegith-bold" style="font-size: 18px;">
                                          <b id="tbl_footer_piezas_total"></b>
                                        </td>
                                      </tr>
                                    </thead>
                                  </table>
                                </div>
                              </div>
                            </div>

                            <div class="row mt-3">
                              <div class="col-md-12">
                                <div class="float-left">
                                  <a href="{{route('admin.orden-compra.index')}}"  class="btn btn-secondary">
                                      Volver
                                  </a>
                                </div>
                                <div class="float-right">
                                  <a href="javascript:void(0);"  class="btn btn-primary" onclick="generar()">
                                      Guardar
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

    var base_receptora = parseInt($("#base_recibe_id").val()) ?? 0
    var base_receptora_original = parseInt($("#base_recibe_id").val()) ?? 0
    var tr_id = getLastTrForeach() ?? 0;

    calcTotal();

    function changeBase() {
      base_receptora = parseInt($("#base_recibe_id").val())

      if (base_receptora != base_receptora_original) {
        let tr_delete = $('#tbl_tbody_piezas_0 tr').filter(function() {
          tr_element = $(this).attr('id').split('-')[1];
          let elementos_borrados = $('#elementos_borrados').val();
          $('#elementos_borrados').val(elementos_borrados == '' ? tr_element : `${elementos_borrados},${tr_element}`);
        });
        displayButton()
        calcTotal()
      }

      $('#tbl_tbody_piezas').empty();
      $('#tbl_tbody_piezas_0').empty();

      /* if (base_receptora != base_receptora_original) {
        $('#elementos_borrados').val('')
        traerListadoOriginal(); // En caso de ser necesario traerlos devuelta
      } */

      tr_id = getLastTrForeach() ?? 0
      addItem()
    }

    function addItem() {

      if (base_receptora == 0) {
        return iziToast.warning({
            title: 'Validación',
            message: 'Por favor, seleccione una base receptora'
        });
      }

      tr_id = tr_id + 1
      var arrPiezas = {!! json_encode($arr_piezas) !!};

      var td1 = document.createElement("td");
      td1.className = "text-left"
      var input1 = document.createElement("select")
      input1.className = 'form-control'
      input1.name = `elementos[${tr_id}][pieza_id]`
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
        if(element.base_id == base_receptora) {
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
      input2.name = `elementos[${tr_id}][cantidad]`
      input2.id = "cantidad-"+tr_id
      input2.value = 0
      input2.addEventListener("blur", calcMonto);
      td2.appendChild(input2);

      var td3 = document.createElement("td");
      td3.className = "text-rigth"
      var input3 = document.createElement("input")
      input3.type = "number"
      input3.step = "0.01"
      input3.className = "form-control"
      input3.style = "text-align: right;"
      input3.name = `elementos[${tr_id}][costo]`
      input3.id = "costo-"+tr_id
      input3.value = 0
      input3.addEventListener("blur", calcMonto);
      td3.appendChild(input3);

      var td4 = document.createElement("td");
      td4.className = "text-rigth"
      var input4 = document.createElement("input")
      input4.type = "number"
      input4.step = "0.01"
      input4.className = "form-control"
      input4.style = "text-align: right;"
      input4.name = `elementos[${tr_id}][monto]`
      input4.id = "monto-"+tr_id
      input4.value = 0
      input4.readOnly = "true"
      input4.disabled = "true"
      td4.appendChild(input4);

      var td5 = document.createElement("td");
      td5.className = "text-center"
      var linkDelete5 = document.createElement("a")
      linkDelete5.className = "btn btn-danger btn-icon"
      linkDelete5.innerHTML = `<i class="fas fa-trash"></i>`
      linkDelete5.title = "Quitar Pieza"
      linkDelete5.addEventListener("click", deleteItem);
      linkDelete5.id = 'btn_delete-' + tr_id
      linkDelete5.href = 'javascript:void(0);'
      td5.appendChild(linkDelete5);

      var tr = document.createElement("tr");
      tr.className = 'count-tr'
      tr.id = 'tbl_tbody_tr-' + tr_id
      tr.appendChild(td1)
      tr.appendChild(td2)
      tr.appendChild(td3)
      tr.appendChild(td4)
      tr.appendChild(td5)

      var tbody = document.getElementById("tbl_tbody_piezas");
      tbody.appendChild(tr)

      displayButton()

      document.getElementById('cant_tr').value = tr_id;

      calcTotal()
    }

    function displayButton() {
      let minId = getFirstTr()
      let maxId = getLastTr()

      for (let index = minId; index <= maxId; index++) {
        if(document.getElementById('tbl_tbody_tr-' + index)) {
          if(index == minId) {
            document.getElementById("btn_delete-" + minId).style.display = "none"
          } else {
            document.getElementById("btn_delete-" + minId).style.display = "inline-block"
          }

          if(index>1 && index<maxId) {
            document.getElementById("btn_delete-" + index).style.display = "inline-block"
          }

          if(index>1 && index==maxId) {
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

    function getLastTrForeach() {
      let arrRows = document.querySelectorAll('#tbl_tbody_piezas_0 tr');

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
          document.getElementById('costo-'+id).value = parseFloat(element.precio).toFixed(2)
          document.getElementById('monto-'+id).value = parseFloat(element.precio).toFixed(2)
        }
      })

      if(parseFloat(id_inventario) == 0) {
        document.getElementById('cantidad-'+id).value = 0
        document.getElementById('costo-'+id).value = 0
        document.getElementById('monto-'+id).value = 0
      }

      calcTotal()
    }

    function calcMonto(evt) {
      var id = evt.currentTarget.id.split('-')[1]
      var cant = document.getElementById('cantidad-'+id).value
      var costo = document.getElementById('costo-'+id).value

      var supera = superaMaximoCompra(id, cant)

      if(supera.valid) {
        iziToast.warning({
            title: 'Validación',
            message: 'El máximo de compra es de ' + supera.maximo_compra
        });

        document.getElementById('cantidad-'+id).value = supera.maximo_compra.toFixed(2)
        var cant = document.getElementById('cantidad-'+id).value
      }

      var monto = 0
      if(cant && costo) {
        monto = parseFloat(cant) * parseFloat(costo)
      }

      document.getElementById('monto-'+id).value = monto.toFixed(2)

      calcTotal()
    }

    function calcTotal() {
      let arrRows = document.querySelectorAll('#tbl_tbody_piezas tr');
      let arrRows0 = document.querySelectorAll('#tbl_tbody_piezas_0 tr');

      var total = 0
      for(let i=0; i<arrRows.length; i++) {
        let row = arrRows[i];

        if(row.style.display != 'none') {
          id = parseInt(row.id.split('-')[1])

          let cantidad = document.getElementById('cantidad-' + id).value
          let costo = document.getElementById('costo-' + id).value

          total = total + (parseFloat(cantidad) * parseFloat(costo))
        }
      }

      for(let i=0; i<arrRows0.length; i++) {
        let row = arrRows0[i];

        if(row.style.display != 'none') {
          id = parseInt(row.id.split('-')[1])

          let cantidad = document.getElementById('cantidad-' + id).value
          let costo = document.getElementById('costo-' + id).value

          total = total + (parseFloat(cantidad) * parseFloat(costo))
        }
      }

      document.getElementById('tbl_footer_piezas').style.display = 'inline-table'
      document.getElementById('tbl_footer_piezas_total').innerText = '$ ' + total.toFixed(2)
    }

    function superaMaximoCompra (id, cant) {
      var valid = false
      var es_null = false
      var maximo_compra = 0
      var id_inventario = document.getElementById('pieza_id-'+id).value

      var arrPiezas = {!! json_encode($arr_piezas) !!};
      arrPiezas.forEach((element, index) => {
        if(element.id == id_inventario) {
          if (element.maximo_compra == null) {
            es_null = true;
          } else if (element.maximo_compra < parseFloat(cant)) {
            valid = true
            maximo_compra = element.maximo_compra
          }
        }
      })
      return {
        es_null: es_null,
        valid: valid,
        maximo_compra: maximo_compra
      }
    }

    function isOkDetalle() {
      valid = true

      let arrRows = document.querySelectorAll('#tbl_tbody_piezas tr');
      let arrRows0 = document.querySelectorAll('#tbl_tbody_piezas_0 tr');

      for(let i=0; i<arrRows.length; i++) {
        let row = arrRows[i];

        if(row.style.display != 'none') {
          id = parseInt(row.id.split('-')[1])
          let pieza_id = document.getElementById('pieza_id-'+id).value
          let cantidad = document.getElementById('cantidad-'+id).value
          let costo = document.getElementById('costo-'+id).value

          if(parseFloat(pieza_id) == 0) {
            valid = false
          }

          if(parseFloat(cantidad) == 0) {
            valid = false
          }

          if(parseFloat(costo) == 0) {
            valid = false
          }

          console.log(pieza_id, cantidad, costo)
        }
      }

      for(let i=0; i<arrRows0.length; i++) {
        let row = arrRows0[i];

        if(row.style.display != 'none') {
          let id = parseInt(row.id.split('-')[1])

          let pieza_id = document.getElementById('pieza_id-'+id).value
          let cantidad = document.getElementById('cantidad-'+id).value
          let costo = document.getElementById('costo-'+id).value

          if(parseFloat(pieza_id) == 0) {
            valid = false
          }

          if(parseFloat(cantidad) == 0) {
            valid = false
          }

          if(parseFloat(costo) == 0) {
            valid = false
          }

          console.log(pieza_id, cantidad, costo)
        }
      }
      return valid
    }

    function generar() {
      var fecha_emision = document.getElementById('fecha_emision').value
      var fecha_entrega = document.getElementById('fecha_entrega').value
      var base_emite_id = document.getElementById('base_emite_id').value
      var proveedor_id = document.getElementById('proveedor_id').value
      var base_recibe_id = document.getElementById('base_recibe_id').value
      var prioridad = document.getElementById('prioridad').value

      if(!fecha_emision) {
        iziToast.warning({
            title: 'Validación',
            message: 'Falta cargar la Fecha de Emision'
        });
        return false
      }

      if(!fecha_entrega) {
        iziToast.warning({
            title: 'Validación',
            message: 'Falta cargar la Fecha de Entrega'
        });
        return false
      }

      if(!base_emite_id) {
        iziToast.warning({
            title: 'Validación',
            message: 'Falta cargar la Base Emisora'
        });
        return false
      }

      if(!proveedor_id) {
        iziToast.warning({
            title: 'Validación',
            message: 'Falta cargar el Proveedor'
        });
        return false
      }

      if(!base_recibe_id) {
        iziToast.warning({
            title: 'Validación',
            message: 'Falta cargar la Base Receptora'
        });
        return false
      }

      if(!prioridad) {
        iziToast.warning({
            title: 'Validación',
            message: 'Falta cargar la Prioridad'
        });
        return false
      }

      valid = isOkDetalle()
      if(!valid) {
        iziToast.warning({
            title: 'Validación',
            message: 'Verifique el detalle de la orden de compra'
        });
        return false
      }

      if(confirm("¿Desea generar la orden de compra?") == true) {
        document.getElementById('form-orden-compra').submit();
      }
    }

    function deleteItem(evt) {
      if ($('#tbl_tbody_piezas tr').length + $('#tbl_tbody_piezas_0 tr').length == 1) {
        iziToast.warning({
            title: 'Validación',
            message: 'No puede guardar la Orden de Compra sin piezas.'
        });
        return evt.preventDefault();
      }

      tr_delete = evt.currentTarget.id.split('-')[1]
      //document.getElementById("tbl_tbody_tr-" + tr_delete).style.display = "none"
      document.getElementById("tbl_tbody_tr-" + tr_delete).remove()
      displayButton()

      calcTotal()
    }

    $('.delete-event').on('click', function(evt) {
      if ($('#tbl_tbody_piezas tr').length + $('#tbl_tbody_piezas_0 tr').length == 1) {
        iziToast.warning({
            title: 'Validación',
            message: 'No puede guardar la Orden de Compra sin piezas.'
        });
        return evt.preventDefault();
      }

      let tr_delete = $(this).attr('id').split('-')[1]
      document.getElementById("tbl_tbody_tr-" + tr_delete).remove()
      let elementos_borrados = $('#elementos_borrados').val();
      $('#elementos_borrados').val(elementos_borrados == '' ? tr_delete : `${elementos_borrados},${tr_delete}`);
      displayButton()

      calcTotal()
    });

    $('#add-event').on('click', function() {
      addItem();
    });

    $('.event-blur').blur(function(e) {calcMonto(e)});

    /* public function traerListadoOriginal() {

    } */

    </script>
@endsection
