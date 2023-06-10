@extends('layouts.admin-master')

@section('title')
    Ordenes de transferencia
@endsection

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Orden de transferencia</h1>
    </div>
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Recibir Orden de Transferencia</h4>
                    </div>
                    <form id="form-orden-transferencia" action="{{ route('admin.orden-transferencia.storeRecibidas') }}" method="POST">
                        @csrf
                        <div class="card-body">         
                            <div class="row">
                                <div class="col-4">
                                    <div class="form-group mr-2">
                                        <label for="base_operacion_id" class="mr-2">Pañol</label>
                                        <select name="base_operacion_id" id="base-operacion-id" class="form-control select2">
                                            <option value="">Seleccione una opción</option>
                                            @foreach($bases_operaciones as $base)
                                                <option value="{{$base->id}}">{{$base->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group mr-2">
                                        <label for="orden_transferencia_id" class="mr-2">Ordenes de Transferencia Pendientes</label>
                                        <select name="orden_transferencia_id" id="orden-transferencia-id" class="form-control select2">
                                            <option value="">Seleccione una opción</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-1">
                                    <div class="form-group">
                                        <label for=""></label>
                                        <button type="button" class="btn btn-primary" id="buscar-oc">Buscar</button>
                                    </div>
                                </div>
                            </div>                
                            
                            <hr>

                            <div class="clearfix mb-3"></div>   
                            <div class="row">
                                <div class="table-responsive">
                                    <table class="table table-striped" id="tabla-elementos">
                                            {{-- Acá se cargan los elementos --}}
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="float-right" id="button-guardar">
                                                                    
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
        let _this = this;

        $('#base-operacion-id').on('change', function() {
            let id = $('#base-operacion-id').val();
            
            _this.limpiar();
            $('#orden-transferencia-id').empty();

            if (id != '') {
                $.ajax({
                    url: `${HOST}/api/orden-transferencia/getOrdenTransferenciaPorBase/${id}`,
                    type: 'GET',
                    cache: false,
                    success: function (resp) {
                        if(resp.estado) {
                            resp.respuesta.forEach(function (oc) {
                                var opcion = new Option(
                                    `N° ${oc.id}`,
                                    oc.id,
                                    false,
                                    false
                                );
                                $('#orden-transferencia-id').append(opcion)
                            });
                        } else {
                            iziToast.error({
                                title: 'Error: ',
                                message: resp.mensaje,
                                position: 'topRight',
                                timeout: false,
                            });
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log(errorThrown)
                    }
                });
            }
        });

        $('#buscar-oc').on('click', function(e) {
            if ($('#base-operacion-id').val() != '' && $('#orden-transferencia-id').val() != '') {
                let id = $('#orden-transferencia-id').val();
    
                _this.limpiar();

                $.ajax({
                    url: `${HOST}/api/orden-transferencia/getOrdenTransferencia/${id}`,
                    type: 'GET',
                    cache: false,
                    success: function (resp) {
                        console.log(resp);
                        if(resp.estado) {

                            // Crear tabla 
                            let lista_td = ''; 
                            resp.respuesta.detalle.forEach(function (det) {
                                if (det.ingreso != null) {
                                    det.cantidad = det.cantidad - det.ingreso;
                                }

                                lista_td += 
                                `
                                <tr>
                                    <td>${det.pieza.nro_pieza} 
                                        <input type="hidden" name="detalle_ot[${det.id}][detalle_id]" value="${det.id}">
                                        <input type="hidden" name="detalle_ot[${det.id}][nro_pieza]" value="${det.pieza.nro_pieza}">
                                        <input type="hidden" name="detalle_ot[${det.id}][pieza_id]" value="${det.pieza.id}">
                                    </td>
                                    <td>${det.pieza.descripcion}</td>
                                    <td id="cantidad-pieza-${det.id}">${det.cantidad}</td>
                                    <td><input class="form-control ingreso-pieza" name="detalle_ot[${det.id}][ingreso]" data-id="${det.id}" type="number" min="1" id="input-ingreso-${det.id}" value="${det.cantidad}" disabled></td>
                                    <td id="faltante-pieza-${det.id}"></td>                                    
                                    <td class="p-0 text-center">
                                        <div class="custom-checkbox custom-control">
                                            <input class="form-check-input oc-checkbox" data-id="${det.id}" name="detalle_ot[${det.id}][checkbox]" type="checkbox" value="0">
                                        </div>
                                    </td>
                                </tr>
                                `;
                            });
                            var html = 
                            `
                            <thead>
                                <tr>
                                    <th>N° Pieza</th>
                                    <th>Descripción</th>
                                    <th>Cantidad</th>
                                    <th>ingreso</th>
                                    <th>Faltante</th>
                                    <th class="p-0 text-center">
                                        Confirmar
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                ${lista_td}
                            </tbody>
                            `;
                            $('#tabla-elementos').html(html);

                            // Crear botón
                            $('#button-guardar').html(
                                '<button class="btn btn-primary" id="guardar-btn">Guardar</button>'
                            );    
                        } else {
                            iziToast.error({
                                title: 'Error: ',
                                message: resp.mensaje,
                                position: 'topRight',
                                timeout: false,
                            });
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log(errorThrown)
                    }
                });
            } else {
                iziToast.error({
                    title: 'Error: ',
                    message: 'Debe seleccionar una Base de Operación y una Orden de Transferencia',
                    position: 'topRight',
                    timeout: false,
                });
            }
        });

        $('#orden-transferencia-id').on('change', function () {
            _this.limpiar();
        });

        $(document).on('change', '.ingreso-pieza', function () {
            let id = $(this).data('id');
            let cantidad = parseFloat($(`#cantidad-pieza-${id}`).html());
            let ingreso = parseFloat($(this).val());

            if (ingreso > cantidad) {
                $(this).val('');

                iziToast.info({
                    title: 'Info: ',
                    message: 'El ingreso no puede ser mayor a la cantidad',
                    position: 'topRight',
                    timeout: false,
                });
            } else {
                $(`#faltante-pieza-${id}`).html(cantidad - ingreso);
            }
        });

        $(document).on('click', '.oc-checkbox', function () {
            let id = $(this).data('id');

            if($(this).is(":checked")){
                $(this).val(1);
                $(`#input-ingreso-${id}`).prop('disabled', false).prop('required', true);
            }
            else if($(this).is(":not(:checked)")){
                $(this).val(0);
                $(`#faltante-pieza-${id}`).html('');
                $(`#input-ingreso-${id}`).prop('disabled', true).prop('required', false);
            }
        });

        $(document).on('click', '#guardar-btn', function (e) {
            let checkbox = $('.oc-checkbox').filter(":checked").length;
            if (checkbox == 0) {
                e.preventDefault();
                iziToast.info({
                    title: 'Info: ',
                    message: 'Debes al menos confirmar una pieza',
                    position: 'topRight',
                    timeout: false,
                });
            }            
        });

        function limpiar() {
            $('#button-guardar').empty();
            $('#tabla-elementos').empty();
        }

    </script>
@endsection