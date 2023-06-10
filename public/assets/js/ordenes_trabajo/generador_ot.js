$('.generar').on('click', function(e) {

    let id = $(this).data('unidad-id');

    $('input[name=unidad_id]').val(id)

    tareas = $("input[name^='unidades["+id+"]'");
    console.log("tareas", tareas);

    var enviar_form = false;
    console.log("enviar_form", enviar_form);

    $.each(tareas, function (i, tarea) { 
         console.log("tarea", tarea);
         if($(tarea).is(':checked')) {
            enviar_form = true;
            return false;
         }
    });

    if(enviar_form) {
        $('#submitOT').submit();
    } else {
        iziToast.error({
            title: 'Error: ',
            message: 'Debe seleccionar al menos una tarea para generar una OT.',
            position: 'topRight',
            timeout: 5000
        });
    }

})

$('#fecha-inicio').on('change', function(e) {
    getHistorialInicio();
})

$('#unidad_id').on('change', function(e) {
    getHistorialInicio();
})

function getHistorialInicio() {
    var fecha = $('#fecha-inicio').val();
    var unidad_id = $('#unidad_id').val();
    var _token = $("input[name='_token']").val();

    //console.log(fecha);
    //console.log(unidad_id);

    if(fecha && unidad_id) {
        //console.log("ajax");
        $.ajax({
            url: HOST + "/api/historiales/getHistorialesFecha/"+unidad_id+"/"+fecha,
            type: 'GET',
            cache: false,
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-Token', _token)
            },
            success: function (resp) {
                //console.log(resp);
                if(resp.respuesta.estado) {
                    if(resp.historiales) {
                        $('#kilometraje').val(resp.historiales.kilometraje);
                        $('#kilometraje').attr('readonly', true);
                        validarKilometraje();
                    }else {
                        $('#kilometraje').attr('readonly', false);
                        validarKilometraje();
                    }
                    if(resp.historial_anterior) {
                        $('.kilometraje_anterior').html('Kilometraje anterior:' + resp.historial_anterior.kilometraje);
                        $('#input_km_anterior').val(resp.historial_anterior.kilometraje);
                        $('.kilometraje_siguiente').html('');
                        $('#input_km_siguiente').val('');
                        if(resp.historial_siguiente) {
                            $('.kilometraje_siguiente').html('Kilometraje siguiente:' + resp.historial_siguiente.kilometraje);
                            $('#input_km_siguiente').val(resp.historial_siguiente.kilometraje);
                        }
                        validarKilometraje();
                    } else {
                        $('.kilometraje_anterior').html('');
                        $('#input_km_anterior').val('');
                        $('.kilometraje_siguiente').html('');
                        $('#input_km_siguiente').val('');
                        validarKilometraje();
                    }
                } else {
                    $('#kilometraje').attr('readonly', false);
                    $('.kilometraje_anterior').html('');
                    $('.kilometraje_siguiente').html('');
                    $('#input_km_anterior').val('');
                    $('#input_km_siguiente').val('');
                    validarKilometraje();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(errorThrown)
            }
        });
    }

    return;
}

function validarKilometraje() {
    var _token = $("input[name='_token']").val();
    var kilometraje = $('#kilometraje').val();
    var input_anterior = $('#input_km_anterior').val();
    var input_siguiente = $('#input_km_siguiente').val();
    var id = $('#unidad_id').val();
    var url = HOST + "/admin/calcular-promedio";
    var estado = document.getElementById(`historial-estado`);
    if(kilometraje.length && id.length) {
        $.ajax({
            url: url,
            data: {
                id
            },
            type: "GET",
            dataType: 'json',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-Token', _token)
            },
            success: function (data) {
                if (data.ultima == null) {
                    // guardarEnBD();
                } else {
                    if (kilometraje == '') {
                        document.getElementById(`historial-mark`).style.display = "none";
                        document.getElementById(`historial-danger`).style.display = "none";
                        document.getElementById(`historial-warning`).style.display = "none";
                        document.getElementById(`historial-check`).style.display = "none";
                        estado.value = "true";
                        validarEstado();

                    } else if (parseInt(kilometraje) < parseInt(input_anterior)) {
                        document.getElementById(`historial-danger`).style.display = "initial";
                        document.getElementById(`historial-mark`).style.display = "none";
                        document.getElementById(`historial-warning`).style.display = "none";
                        document.getElementById(`historial-check`).style.display = "none";

                        estado.value = "false";
                        validarEstado();
                    }
                    else if (input_siguiente.length && parseInt(kilometraje) > parseInt(input_siguiente) && parseInt(input_siguiente) != parseInt(input_anterior)) {
                        document.getElementById(`historial-danger`).style.display = "initial";
                        document.getElementById(`historial-mark`).style.display = "none";
                        document.getElementById(`historial-warning`).style.display = "none";
                        document.getElementById(`historial-check`).style.display = "none";

                        estado.value = "false";
                        validarEstado();
                    } else if (parseInt(kilometraje) > data.porcentaje) {
                        document.getElementById(`historial-danger`).style.display = "none";
                        document.getElementById(`historial-check`).style.display = "none";
                        document.getElementById(`historial-warning`).style.display = "initial";
                        document.getElementById(`historial-mark`).style.display = "initial";
                        estado.value = "false";
                        validarEstado();


                        document.getElementById(`historial-mark`).addEventListener('click', function () {
                            document.getElementById(`historial-warning`).style.display = "none";
                            document.getElementById(`historial-mark`).style.display = "none";

                            document.getElementById(`historial-check`).style.display = "initial";
                            estado.value = "true";
                            validarEstado();
                        });
                    } else {
                        document.getElementById(`historial-danger`).style.display = "none";
                        document.getElementById(`historial-warning`).style.display = "none";
                        document.getElementById(`historial-check`).style.display = "initial";
                        document.getElementById(`historial-mark`).style.display = "none";
                        estado.value = "true";
                        validarEstado();
                    }
                }
            },
            error: function (jqXHR, textStatus, errorThrow) {
                console.log(`${jqXHR}.${textStatus}: ${errorThrow}`)
            }
        })

    } else if(kilometraje.length == 0) {
        document.getElementById(`historial-mark`).style.display = "none";
        document.getElementById(`historial-danger`).style.display = "none";
        document.getElementById(`historial-warning`).style.display = "none";
        document.getElementById(`historial-check`).style.display = "none";
        estado.value = "true";
        validarEstado();
    }
}
$('#kilometraje').on('keyup', function(e) {
    validarKilometraje();
})

function validarEstado() {
    var submit = $("#submit");

    submit.attr('disabled', false);

    $(".estados").each(function () {
        if ($(this).val() == "false") {
            submit.attr('disabled', 'disabled');
        }
    });

}

$('.selector-especialidad').on('change', function(e) {
    var especialidad_ids = $('.selector-especialidad').val();
    $.each(especialidad_ids, function(i, item) {
        if(item === 'todas') {
            $('.selector-especialidad').val('todas');
            especialidad_ids = $('.selector-especialidad').val();
        }
    })
})