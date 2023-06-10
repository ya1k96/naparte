$('.ver-recursos-btn').on('click', function() {

    var id = $(this).data('id');

    $('#tr-table-racursos').html('');

    $.ajax({
        url: HOST + "/api/vale/recursosAsociados/"+id,
        type: 'GET',
        cache: false,
        success: function (resp) {
            console.log(resp);
            if(resp.estado) {

                var html = '';
                resp.respuesta.forEach(function (recurso) {
                    html += 
                    `<tr>
                        <td>${recurso.material}</td>
                        <td>${recurso.parte}</td>
                        <td>${recurso.cantidad}</td>
                        <td>${recurso.unidad}</td>
                    </tr>`;
                });
                $('#tr-table-racursos').html(html);

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
    
})

$('#agregar-recurso-pieza').on('click' , function () {
    let recursoId = $("#recurso_id").val();
    let recursoText = $("#recurso_id option:selected").text();
    let unidadText = $("#recurso_id option:selected").data("unidad-text");
    let tareaText = $("#tarea_id option:selected").data("tarea-text") ?? null;
    let tareaId = $("#tarea_id").val() ?? null;

    if(recursoId) {
        var base_operacion_id = $("#base_operacion_id").val();
        //Valido que exista inventario para la pieza.

        $.ajax({
            url: HOST + "/api/inventario/get-inventario-por-pieza",
            data: {
                recursoId,
                base_operacion_id
            },
            type: "GET",
            dataType: 'json',
            success: function (respuesta) {
                if(respuesta.respuesta.estado) {
                    console.log(respuesta);
                    recursoText = recursoText+" (Stock actual: "+respuesta.inventario.stock_total+")";
                    agregarRow(recursoId, recursoText, unidadText, tareaText, tareaId);
                    $("#recurso_id").val(null).trigger('change');
                } else {                    
                    iziToast.error({
                        timeout: 50000,
                        position: "topRight",
                        title: "Error :",
                        message:
                            "No se encontró inventario para la pieza "+recursoText+" en la base seleccionada.",
                    });
                }
            },
            error: function (jqXHR, textStatus, errorThrow) {
                console.log(`${textStatus}: ${errorThrow}`)
            }
        })

    } else {
        iziToast.error({
            timeout: 50000,
            position: "topRight",
            title: "Error :",
            message:
                "Primero seleccione un recurso.",
        });
    }
});

var index = 1000;
function agregarRow(recursoId, recursoText, unidadText, tareaText, tareaId) {
    var tabla_listado_recursos = $('#tabla_listado_recursos');

    var rows;
    rows += `<tr class="nuevos-inventario index-${index}">`;
    if (window.location.href.indexOf("editar") > -1) {
        //Si la URL contiene la palabra editar
        rows += `
        <td class="p-0 text-center">
            <div class="custom-checkbox custom-control">
                <input class="form-check-input vale-checkbox" name="listado_piezas_recursos[${index}][checkbox]" type="checkbox" value="1" checked>
            </div>
        </td>`;
    }
    rows += `<td><p>${recursoText}</p></td>`;
    if (tareaId != null) {
        rows += `<td><p>${tareaText}</p></td>`;
    }
    rows += `<td><input type="number" required class="form-control" min="0" name="listado_piezas_recursos[${index}][cantidad]"></td>`;
    rows += `<td><input type="text" disabled value="${unidadText}" class="form-control"></td>`;
    rows += `<td><span class="controls"><a class="btn btn-danger delete-row text-white" data-index="${index}"><i class="fas fa-times"></i></a></span></td>`;
    if (tareaId != null) {
        rows += `<input type="hidden" value="${tareaId}" min="0" name="listado_piezas_recursos[${index}][tarea_id]">`;
    }
    rows += `<input type="hidden" value="${recursoId}" min="0" name="listado_piezas_recursos[${index}][pieza_id]">`;
    rows += `<input type="hidden" value="${recursoText}" min="0" name="listado_piezas_recursos[${index}][pieza_descripcion]">`;
    rows += `<input type="hidden" value="${unidadText}" min="0" name="listado_piezas_recursos[${index}][unidad_medida]">`;

    tabla_listado_recursos.append(rows);
    
    index++;

    this.borrarRecursos();
}

function borrarRecursos() {
    $('.delete-row').on('click', function (e) {
        let index = $(this).data('index');
        $('.index-'+index).remove();
    })
}

$(".vale-checkbox-todos")
    .off("click")
    .on('click', function () {

        if($(this).prop('checked') === true){
            $('.vale-checkbox').filter(function () {
                return $(this).val(1).prop( "checked", true);
            })
        }
        else if($(this).prop('checked') === false){
            $('.vale-checkbox').filter(function () {
                return $(this).val(0).prop( "checked", false);
            })
        }

    });

$(document)
    .off("click")
    .on('click', ".vale-checkbox", function () {
        if ($(this).prop('checked') === true) {
                $(this).val(1).prop( "checked", true);
            }
        else if($(this).prop('checked') === false){
            $(this).val(0).prop( "checked", false);
        }
    });

/* 
    ************* DEVOLUCION VALES *************
*/

$('.devolucion-checkbox').on('click', function() {
    //let id = $(this).parent().parent().parent().children('.id-recurso').val();
    var index = $(this).data('index-id');
    if($(this).prop('checked') === true) {
        $('input[name="listado_piezas_recursos['+index+'][cantidad_devolucion]"]').prop('readonly', false);

    } else {
        $('input[name="listado_piezas_recursos['+index+'][cantidad_devolucion]"]').val(0);
        validarCantidad($('input[name="listado_piezas_recursos['+index+'][cantidad_devolucion]"]'));
        $('input[name="listado_piezas_recursos['+index+'][cantidad_devolucion]"]').prop('readonly', true);
    }
})

$('.cant-devolucion').on('keyup', function() {
    validarCantidad($(this));
})

function validarCantidad(input) {
    var index = input.data('index-id');
    var cantidad_usada = $('input[name="listado_piezas_recursos['+index+'][cantidad]"]').val();
    var cantidad_devolucion = input.val();

    console.log(cantidad_usada);
    console.log(cantidad_devolucion);
    console.log(cantidad_devolucion > cantidad_usada);

    if(parseInt(cantidad_devolucion) > parseInt(cantidad_usada)) {
        input.addClass('is-invalid');
        $('#guardar-vale').attr('disabled', true);
        iziToast.error({
            timeout: 50000,
            position: "topRight",
            title: "Error :",
            message:
                "La cantidad a devolver debe ser menor o igual a la guardada.",
        });
    } else {
        input.removeClass('is-invalid');
    }
    if($('.is-invalid').length) {
        $('#guardar-vale').attr('disabled', true);
    } else {
        $('#guardar-vale').attr('disabled', false);
    }
}

/* 
    ************* FIN DEVOLUCION VALES *************
*/
