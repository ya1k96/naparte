var index = 1;

$('#bases_operacion_id').on("change", function (e) {
    limpiar();

    var base_id = e.target.value;
    var _token = $("input[name='_token']").val();
    var input = $('#piezas-primer');

    buscarPiezas(base_id, _token, input);
});

$('#checkbox-listado-primer').on('click', function (e) {
    let checkbox = $(this);

    if (checkbox.val() == 0) {
        checkbox.prop('checked', true).val(1);
        $('#max-listado-primer').val('').prop('readOnly', true);
        $('#min-listado-primer').val('').prop('readOnly', true);
    } else {
        checkbox.prop('checked', false).val(0);
        $('#max-listado-primer').prop('readOnly', false);
        $('#min-listado-primer').prop('readOnly', false);
    }
})

$('.add-sibling').on('click', function (e) {
    agregarRow();
})

function limpiar() {
    $('#piezas-primer').empty();
    let checkbox = $('#checkbox-listado-primer');
    if (checkbox.val() == 1) {
        checkbox.prop('checked', false).val(0);
        $('.max-'+index).prop('readOnly', false);
        $('.min-'+index).prop('readOnly', false);
    }
    $('#stock-primer').val('');
    $('#precio-primer').val('');
    $('#ubicacion-primer').val('');
    $('#max-listado-primer').val('');
    $('#min-listado-primer').val('');

    $('.nuevos-inventario').remove();
}

function buscarPiezas(base_id, _token, input) {
    $.ajax({
        url: HOST+'/api/bases_operaciones/get_pieza/'+base_id,
        type: "GET",
        cache: false,
        beforeSend: function (xhr) {
            xhr.setRequestHeader('X-CSRF-Token', _token)
        },
        success: function (data) {
            console.log(data);
            if (data.length > 0) {
                var opcion = new Option(
                    'Seleccione una pieza',
                    false,
                    false,
                    false
                );
                input.append(opcion)
                data.forEach(function (da) {
                    var opcion = new Option(
                        da.descripcion,
                        da.id,
                        false,
                        false
                    );
                    input.append(opcion);
                });
            }
        },
        error: function (jqXHR, textStatus, errorThrow) {
            console.log(`${textStatus}: ${errorThrow}`)
        }
    })
}

function agregarRow() {
    var tabla_listado = $('#tabla_listado');
    var base_id = $('#bases_operacion_id').val();
    var _token = $("input[name='_token']").val();

    if (base_id.length > 0) {
        var rows;
        rows += `<tr class="nuevos-inventario index-${index}">`;
        rows += `<td><select name="listado_piezas[${index}][pieza_id]" class="form-control select2 listado-piezas select-pieza-${index}" data-placeholder="Piezas" style="width:200px;" required></select></td>`;
        rows += `<td style="text-align: center;vertical-align: middle;"><input type="checkbox" class="checbox-listado" data-index="${index}" name="listado_piezas[${index}][compra_unica]" value="0"></td>`;
        rows += `<td><input type="number" class="form-control" name="listado_piezas[${index}][stock]" required></td>`;
        rows += `<td><input type="number" class="form-control" name="listado_piezas[${index}][precio]" step="0,01" required></td>`;
        rows += `<td><input type="text" class="form-control" name="listado_piezas[${index}][ubicacion]" required></td>`;
        rows += `<td><input type="number" class="form-control max-${index}" name="listado_piezas[${index}][maximo_compra]" id="max-listado-primer"></td>`;
        rows += `<td><input type="number" class="form-control min-${index}" name="listado_piezas[${index}][minimo_compra]" id="min-listado-primer"></td>`;
        rows += `<td><span class="controls"><a class="btn btn-danger delete-row" href="#" data-index="${index}"><i class="fas fa-times"></i></a></span></td>`;
    
        tabla_listado.append(rows);

        let input = $(".select-pieza-"+index);

        buscarPiezas(base_id, _token, input);
        
        $('.delete-row').on('click', function (e) {
            let index = $(this).data('index');
            $('.index-'+index).remove();
        })

        $('.checbox-listado').on('click', function (e) {
            let checkbox = $(this);
            let index = checkbox.data('index');
        
            if (checkbox.val() == 0) {
                checkbox.prop('checked', true).val(1);
                $('.max-'+index).val('').prop('readOnly', true);
                $('.min-'+index).val('').prop('readOnly', true);
            } else {
                checkbox.prop('checked', false).val(0);
                $('.max-'+index).prop('readOnly', false);
                $('.min-'+index).prop('readOnly', false);
            }
        })

        index++;
    } else {
        iziToast.error({
            timeout: 50000,
            position: "topRight",
            title: "Error :",
            message:
                "Primero seleccione una base de operaciones.",
        });
    }
}
