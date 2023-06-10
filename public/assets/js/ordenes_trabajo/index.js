$(".reabrir").on('click', function (e) {
    let id = $(this).data('id');
    var reabrir = this;

    $(`#reabrir-modal-${id}`).fireModal({
        title: 'Reabrir Orden de Trabajo',
        body: $('#modal-reabrir-part').clone(),
        footerClass: 'bg-whitesmoke',
        autofocus: false,
        removeOnDismiss: true,
        created: function(modal, e, form) {
            modal.find('form')[0].action = reabrir.dataset.route;
        },
        shown: function(modal, form) {
            console.log("shown", modal, form)
        },
        buttons: [{
            text: 'Continuar',
            submit: true,
            class: 'btn btn-primary btn-shadow',
            handler: function(modal) {
            }
        }]
    });
})

$(".anular").on('click', function (e) {
    let id = $(this).data('id');
    var anular = this;

    $(`#anular-modal-${id}`).fireModal({
        title: 'Anular Orden de Trabajo',
        body: $('#modal-anular-part').clone(),
        footerClass: 'bg-whitesmoke',
        autofocus: false,
        removeOnDismiss: true,
        created: function(modal, e, form) {
            modal.find('form')[0].action = anular.dataset.route;
        },
        shown: function(modal, form) {
            console.log("shown", modal, form)
        },
        buttons: [{
            text: 'Continuar',
            submit: true,
            class: 'btn btn-primary btn-shadow',
            handler: function(modal) {
            }
        }]
    });
})

$('#fecha-inicio').on('change', function(e) {
    getHistorialInicio();
})

$('#unidad_id').on('change', function(e) {
    getHistorialInicio();
})

$(document).ready(function () {
    if (window.location.href.indexOf("edit") > -1 &&
        window.location.href.indexOf("editPreventiva") == -1) {
            //Solo buscar si estoy editando en OT Correctiva
            getHistorialInicio();
    }
})

/**
 * !Deprecada para pantalla de editar OT Preventiva.
 * Antes había un campo de fecha inicio por el que había que buscar la última lectura y validar contra la ingresada.
 */
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
                console.log(resp);
                if(resp.respuesta.estado) {
                    if(resp.historiales) {
                        if(window.location.href.indexOf("edit") == -1) {
                            $('#kilometraje').val(resp.historiales.kilometraje);
                        }
                        //$('#kilometraje').attr('readonly', true);
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
                    else if ($('#input_km_anterior').length && parseInt(kilometraje) > parseInt(input_siguiente) && parseInt(input_siguiente) != parseInt(input_anterior)) {
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

$('.buscar_tareas').on('change', function (e) {
    //console.log("buscar tareas");
    var unidad_id = $('#unidad_id').val();
    var especialidad_ids = $('#especialidad_id').val();
    var _token = $("input[name='_token']").val();

    $.each(especialidad_ids, function(i, item) {
        if(item === 'todas') {
            especialidad_ids = ["todas"];
        }
    })

    if(unidad_id.length > 0 && especialidad_ids.length > 0) {
        $(".tareas").empty();
    
        $.ajax({
            url: HOST + "/admin/show-mantenimiento-especialidad",
            data: {
                unidad_id,
                especialidad_ids
            },
            type: "GET",
            dataType: 'json',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-Token', _token)
            },
            success: function (data) {
                console.log(data);
                if (data.length > 0) {

                    $.ajax({
                        url: HOST + "/api/personal/filtrar_especialidad",
                        data: {
                            especialidad_ids
                        },
                        type: "GET",
                        dataType: 'json',
                        cache: false,
                        beforeSend: function (xhr) {
                            xhr.setRequestHeader('X-CSRF-Token', _token)
                        },
                        success: function (data_especialidad) {

                            $.each(data[0], function (i, item) {
                                if (item.tareas.length) {
                                    $.each(item.tareas, function (j, tarea) {
                                        //console.log(tarea);
                                        var tarea_id = tarea.id;
                                        var tareas = tarea.descripcion;
            
                                        rows = '<div>';
                                        /* Nombre de la tarea */
                                        rows += '<div class="form-group row mb-4">';
                                        rows += `<label for="tareas[${tarea_id}][descripcion]" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Tarea</label>`;
                                        rows += '<div class="col-sm-12 col-md-7">';
                                        rows += `<input type="text" value="${tareas}" class="form-control" name="tareas[${tarea_id}][descripcion]" autocomplete="off" required readonly>`;
                                        rows += `<input type="hidden" value="${tarea_id}" class="form-control" name="tareas[${tarea_id}][id]" required>`;
                                        rows  += '</div>';
                                        rows  += '</div>';
                                        /* Especialidad */
                                        rows += '<div class="form-group row mb-4">';
                                        rows += `<label for="tareas[${tarea_id}][especialidad_id]" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Especialidad</label>`;
                                        rows += '<div class="col-sm-12 col-md-7">';
                                        rows += `<input type="text" value="${tarea.especialidad.nombre}" class="form-control" name="tareas[${tarea_id}][especialidad_id]" autocomplete="off" required readonly>`;
                                        rows  += '</div>';
                                        rows  += '</div>';
                                        if(tarea.observaciones != null) {
                                            var obs = tarea.observaciones
                                            /* Descripcion de la tarea */
                                            rows += '<div class="form-group row mb-4">';
                                            rows += `<label for="tareas[${tarea_id}][observaciones]" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Procedimiento</label>`;
                                            rows += '<div class="col-sm-12 col-md-7">';
                                            rows += `<textarea class="form-control" name="tareas[${tarea_id}][observaciones]" style="height: 75px" readonly>${obs}</textarea>`;
                                            rows  += '</div>';
                                            rows  += '</div>';
                                        }
                                        /* Comentario */
                                        rows += '<div class="form-group row mb-4">';
                                        rows += `<label for="tareas[${tarea_id}][comentario]" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Comentario</label>`;
                                        rows += '<div class="col-sm-12 col-md-7">';
                                        rows += `<textarea class="form-control" name="tareas[${tarea_id}][comentario]" style="height: 75px"></textarea>`;
                                        rows  += '</div>';
                                        rows  += '</div>';
                                        /* Selector personal */
                                        rows += '<div class="form-group row mb-4">';
                                        rows += `<label for="tareas[${tarea_id}][personal]" class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Personal</label>`;
                                        rows += '<div class="col-sm-12 col-md-7">';
                                        rows += `<select class="form-control select2-personal" name="tareas[${tarea_id}][personal][]" multiple>"`;
                                        //rows += '<option label="Selecciona un personal" value="1">Selecciona un personal</option>';
                                        $.each(data_especialidad.respuesta.personal, function(i, item) {
                                            if(item.especialidad.nombre == tarea.especialidad.nombre) {
                                                rows += '<option label="'+item.nombre + ' - ' + item.especialidad.nombre+'" value="'+item.id+'">'+item.nombre + ' - ' + item.especialidad.nombre+'</option>';
                                            }
                                        })
                                        rows += '</select>';
                                        rows  += '</div>';
                                        rows  += '</div>';
        
        
                                        rows  += '</div>';
                                        rows += '<hr />';
            
                                        $('.tareas').append(rows);
                                        $('.select2-personal').select2({
                                            placeholder: 'Selecciona al personal',
                                        });
            
                                    })
            
                                }
                            })
                
                        },
                        error: function (jqXHR, textStatus, errorThrow) {
                            console.log(`${textStatus}: ${errorThrow}`)
                        }
                    })
    
                }
            },
            error: function (jqXHR, textStatus, errorThrow) {
                console.log(`${textStatus}: ${errorThrow}`)
            }
        });
    }
})
var personal_especialidad;
$('#especialidad_id').on('change', function(e) {

    var especialidad_ids = $('#especialidad_id').val();
    var _token = $("input[name='_token']").val();
    $.each(especialidad_ids, function(i, item) {
        if(item === 'todas') {
            $('#especialidad_id').val('todas');
            especialidad_ids = $('#especialidad_id').val();
        }
    })

    $.ajax({
        url: HOST + "/api/personal/filtrar_especialidad",
        data: {
            especialidad_ids
        },
        type: "GET",
        dataType: 'json',
        cache: false,
        beforeSend: function (xhr) {
            xhr.setRequestHeader('X-CSRF-Token', _token)
        },
        success: function (data) {
            personal_especialidad = data;
            console.log("asigno: ",personal_especialidad);
            var seleccionados = $('#personal').find(':selected');
            var seleccionados = $.makeArray(seleccionados);
            $('#personal').empty().trigger("change");
            $.each(data.respuesta.personal, function (i, item) {
                var newOption = new Option(item.nombre + ' - ' + item.especialidad.nombre, item.id, false, false);
                $('#personal').append(newOption).trigger('change');
                $('#personal option[value="'+item.id+'"]').addClass(item.especialidad.nombre);
                $('#personal').trigger('change');
            })
            var ids_seleccionados = [];
            $.each(seleccionados, function (i, item) {
                ids_seleccionados.push($(item).val());
            });
            $('#personal').val(ids_seleccionados);
            $('#personal').trigger('change');

        },
        error: function (jqXHR, textStatus, errorThrow) {
            console.log(`${textStatus}: ${errorThrow}`)
        }
    })

})

$(".button-cerrar-historial").on('click', function (e) {
    let _this = this;
    let cerrar = this;
    let id = $(this).data('id');
    let orden_id = $(this).data('id');
    let route = $(this).data('route');
    let api = $(this).data('api');
    var _token = $('input[name="_token"]').val();
    var unidad_anterior = null;
    var unidad_1 = null;
    var modal_listo = false;
    let tipo_orden = $(this).data('tipo_orden');

    $(`#cerrar-historial-${id}`).fireModal({
        title: 'Kilometraje de la Orden de Retiro',
        body: $('#cerrar-kilometraje-part').clone(),
        footerClass: 'bg-whitesmoke',
        autofocus: false,
        removeOnDismiss: true,
        center: true,
        created: function(modal, e, form) {

            $.ajax({
                type: "GET",
                url: api,
                data: {},
                dataType: "json",
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', _token);
                },
                success: function (respuesta) {

                    console.log(respuesta);
                    unidad_1 = respuesta[0];

                    modal.find('input[name=id]').val(id).trigger("change");
                    modal.find('input[name=unidad_id]').val(unidad_1.unidad_id).trigger("change");
                    modal.find('input[name=kilometraje_1]').val(unidad_1.kilometraje).trigger("change");
                    modal.find('input[name=historial_1_id]').val(unidad_1.id).trigger("change");
                    fecha = formatearFecha(unidad_1.created_at);
                    modal.find('label[id=kilometraje_1_label]').append(fecha+"<code>*</code>");

                    if(tipo_orden == 'Preventiva') {
                        modal.find('.carga-preventivas').attr('hidden', true);
                    }

                    modal.find('form')[0].action = cerrar.dataset.route;
                },
                fail: function () {
                    iziToast.error({
                        timeout: 50000,
                        position: "topRight",
                        title: "Error :",
                        message:
                            "Se produjo un error al obtener el historial de la unidad. Por favor, inténtelo nuevamente.",
                    });
                }
            })
        },
        onFormSubmit: function(modal, e, form) {
            if (!modal_listo) {

                e.preventDefault();
                //validaciones
                let id = modal.find('input[name=unidad_id]').val();
                unidad_anterior = unidad_1;    
                
                $.ajax({
                    url: HOST + "/admin/calcular-promedio",
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
                            //no hay nada, cerrar modal
                            modal.removeClass("modal-progress")
                            modal.modal("hide");
    
                            iziToast.error({
                                title: 'Error: ',
                                message: 'No se encontraron historiales para esta unidad.',
                                position: 'topRight',
                                timeout: 5000,
                            });
    
                        } else {
                            var input_kilometraje_1 = modal.find('input[name=kilometraje_1]').val();
    
                            if(input_kilometraje_1.length) {
                                kilometraje_1_estado_modal = modal.find('input[name=kilometraje_1_estado_modal]')[0];
                                if(unidad_anterior) {
    
                                    //* Cerramos el modal si coiniciden el valor anterior con el actual
                                    if (input_kilometraje_1 == unidad_anterior.kilometraje) {
                                        modal_listo = true;
                                        modal.find('form').submit();
                                        return;
                                    }
    
                                    //hay 4 o mas lecturas
                                    if(parseInt(input_kilometraje_1) < unidad_anterior.kilometraje) {
                                        modal.removeClass("modal-progress")
                                        kilometraje_1_estado_modal.value = "false";
                                        modal.find("a[id=historial-modal-danger-1]")[0].style.display = "initial";
                                        modal.find("a[id=historial-modal-mark-1]")[0].style.display = "none";
                                        modal.find("a[id=historial-modal-warning-1]")[0].style.display = "none";
                                        modal.find("a[id=historial-modal-check-1]")[0].style.display = "none";
                                    } else if (parseInt(input_kilometraje_1) > data.porcentaje) {
                                        var kilometraje_1_valor_comparar = modal.find('input[name=kilometraje_1_valor_comparar]').val();
                                        if(parseInt(input_kilometraje_1) != parseInt(kilometraje_1_valor_comparar)) {
                                            modal.removeClass("modal-progress")
                                            kilometraje_1_estado_modal.value = "false";
                                            modal.find("a[id=historial-modal-danger-1]")[0].style.display = "none";
                                            modal.find("a[id=historial-modal-mark-1]")[0].style.display = "initial";
                                            modal.find("a[id=historial-modal-warning-1]")[0].style.display = "initial";
                                            modal.find("a[id=historial-modal-check-1]")[0].style.display = "none";
        
                                            modal.find("a[id=historial-modal-mark-1]")[0].addEventListener('click', function () {
                                                //Esto funciona mal, hay que guardar algo como que ya "aceptó" el valor, xq se resetea cada vez que se hace submit.
                                                //SOLUCIÓN: voy a poner un input hidden y si el valor es el mismo y pasó por aca entonces lo pongo válido
                                                modal.find('input[name=kilometraje_1_valor_comparar]').val(parseInt(input_kilometraje_1));
                                                modal.find("a[id=historial-modal-warning-1]")[0].style.display = "none";
                                                modal.find("a[id=historial-modal-mark-1]")[0].style.display = "none";
            
                                                modal.find("a[id=historial-modal-check-1]")[0].style.display = "initial";
                                                kilometraje_1_estado_modal.value = "true";
                                            });
    
                                        }
                                    } else {
                                        modal.find("a[id=historial-modal-danger-1]")[0].style.display = "none";
                                        modal.find("a[id=historial-modal-mark-1]")[0].style.display = "none";
                                        modal.find("a[id=historial-modal-warning-1]")[0].style.display = "none";
                                        modal.find("a[id=historial-modal-check-1]")[0].style.display = "initial";
                                        kilometraje_1_estado_modal.value = "true";
                                    }
                                }else {
                                    //el kilometraje 1 es la primer lectura entonces esta ok
                                    modal.find("a[id=historial-modal-danger-1]")[0].style.display = "none";
                                    modal.find("a[id=historial-modal-mark-1]")[0].style.display = "none";
                                    modal.find("a[id=historial-modal-warning-1]")[0].style.display = "none";
                                    modal.find("a[id=historial-modal-check-1]")[0].style.display = "initial";
                                    kilometraje_1_estado_modal.value = "true";
                                }
                            }else {
                                kilometraje_1_estado_modal = modal.find('input[name=kilometraje_1_estado_modal]')[0];
                                kilometraje_1_estado_modal.value = "true";
                            }
    
                            if(kilometraje_1_estado_modal.value == "true") {
    
                                historial_1 = modal.find('input[name=historial_1_id]').val();
    
                                $.ajax({
                                    url: HOST + "/admin/calcular-promedio",
                                    data: {
                                        id
                                    },
                                    type: "GET",
                                    dataType: 'json',
                                    beforeSend: function (xhr) {
                                        xhr.setRequestHeader('X-CSRF-Token', _token)
                                    },
                                    success: function (data) {
                                        $('#historial_tabla_promedio_'+id).html(data.promedio + ' kms');
    
                                        kms = $('#kilometraje-'+id).val();
                   
                                        let estado_validar = document.getElementById(`historial-${orden_id}-estado`);
    
                                        if (data.ultima == null) {
                                            // guardarEnBD();
                                        } else {
                                            if (kms == '') {
                                                document.getElementById(`historial-mark-${orden_id}`).style.display = "none";
                                                document.getElementById(`historial-danger-${orden_id}`).style.display = "none";
                                                document.getElementById(`historial-warning-${orden_id}`).style.display = "none";
                                                document.getElementById(`historial-check-${orden_id}`).style.display = "none";
                                                estado_validar.value = "false";
                        
                                            } else if (kms < data.ultima.kilometraje) {
                                                document.getElementById(`historial-danger-${orden_id}`).style.display = "initial";
                                                document.getElementById(`historial-mark-${orden_id}`).style.display = "none";
                                                document.getElementById(`historial-warning-${orden_id}`).style.display = "none";
                                                document.getElementById(`historial-check-${orden_id}`).style.display = "none";
                        
                                                estado_validar.value = "false";
                                                validarEstadoHistorial();
                                            } else if (kms > data.porcentaje) {
                                                document.getElementById(`historial-danger-${orden_id}`).style.display = "none";
                                                document.getElementById(`historial-check-${orden_id}`).style.display = "none";
                                                document.getElementById(`historial-warning-${orden_id}`).style.display = "initial";
                                                document.getElementById(`historial-mark-${orden_id}`).style.display = "initial";
                                                estado_validar.value = "false";
                                                validarEstadoHistorial();
                        
                        
                                                document.getElementById(`historial-mark-${orden_id}`).addEventListener('click', function () {
                                                    document.getElementById(`historial-warning-${orden_id}`).style.display = "none";
                                                    document.getElementById(`historial-mark-${orden_id}`).style.display = "none";
                        
                                                    document.getElementById(`historial-check-${orden_id}`).style.display = "initial";
                                                    estado_validar.value = "true";
                                                    validarEstadoHistorial();
                                                });
                                            } else {
                                                document.getElementById(`historial-danger-${orden_id}`).style.display = "none";
                                                document.getElementById(`historial-warning-${orden_id}`).style.display = "none";
                                                document.getElementById(`historial-check-${orden_id}`).style.display = "initial";
                                                document.getElementById(`historial-mark-${orden_id}`).style.display = "none";
                                                estado_validar.value = "true";
                                                validarEstadoHistorial();

                                                modal_listo = true;
                                                modal.find('form').submit();
                                            }
                                        }
                                    }
                                })
    
                                
                            }
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrow) {
                        console.log(`${jqXHR}.${textStatus}: ${errorThrow}`)
                    }
                })
            }
            
            
        },
        shown: function(modal, form) {
            console.log("shown", modal, form)
        },
        buttons: [{
            text: 'Cerrar la Orden de Trabajo',
            submit: true,
            class: 'btn btn-primary btn-shadow',
            handler: function(modal) {
            }
        }]
    });

});

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

function validarEstadoHistorial() {
    var submit = $("#submit");

    submit.attr('disabled', false);

    $(".estados").each(function () {
        if ($(this).val() == "false") {
            submit.attr('disabled', 'disabled');
        }
    });

}

$('.info-vales').on('click', function () {
    iziToast.info({
        timeout: 50000,
        position: "topRight",
        title: "Info :",
        message:
            "La Orden de Trabajo ya cuenta con un Vale.",
    });
});

$('#replicar-fechas').on('click', function() {
    if ($('.fecha-realizacion-1').val() != '') {
        $('.insertar-fecha').val($('.fecha-realizacion-1').val());
    } else {
        iziToast.info({
            timeout: 50000,
            position: "topRight",
            title: "Info :",
            message:
                "Primero debe seleccionar una fecha.",
        });
    }
});