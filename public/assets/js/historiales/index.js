function isEmpty(x) {
    if (!x) return true;
    if (x == null)  return true;
    if (x == undefined) return true;
    if (x === undefined) return true;
    if (typeof x == 'undefined') return true;
    if (x === '') return true;
    if (x === ' ') return true;
    if (x.length == 0)  return true;
    if (!x.length) return true;

    return false;
}

var url = HOST;

let inputs_kms = [...document.querySelectorAll('.input-kms')];
var _token = $("input[name='_token']").val();

inputs_kms.map((input_kms) => {
    input_kms.addEventListener('keyup', function () {

        let id = input_kms.dataset.id,
            url = HOST + "/admin/calcular-promedio",
            kms = document.getElementById(`kilometraje-${id}`).value;

        let estado = document.getElementById(`historial-${id}-estado`);

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
                    if (kms == '') {
                        document.getElementById(`historial-mark-${id}`).style.display = "none";
                        document.getElementById(`historial-danger-${id}`).style.display = "none";
                        document.getElementById(`historial-warning-${id}`).style.display = "none";
                        document.getElementById(`historial-check-${id}`).style.display = "none";
                        estado.value = "true";
                        validarEstado();

                    } else if (kms < data.ultima.kilometraje) {
                        document.getElementById(`historial-danger-${id}`).style.display = "initial";
                        document.getElementById(`historial-mark-${id}`).style.display = "none";
                        document.getElementById(`historial-warning-${id}`).style.display = "none";
                        document.getElementById(`historial-check-${id}`).style.display = "none";

                        estado.value = "false";
                        validarEstado();
                    } else if (kms > data.porcentaje) {
                        document.getElementById(`historial-danger-${id}`).style.display = "none";
                        document.getElementById(`historial-check-${id}`).style.display = "none";
                        document.getElementById(`historial-warning-${id}`).style.display = "initial";
                        document.getElementById(`historial-mark-${id}`).style.display = "initial";
                        estado.value = "false";
                        validarEstado();


                        document.getElementById(`historial-mark-${id}`).addEventListener('click', function () {
                            document.getElementById(`historial-warning-${id}`).style.display = "none";
                            document.getElementById(`historial-mark-${id}`).style.display = "none";

                            document.getElementById(`historial-check-${id}`).style.display = "initial";
                            estado.value = "true";
                            validarEstado();
                        });
                    } else {
                        document.getElementById(`historial-danger-${id}`).style.display = "none";
                        document.getElementById(`historial-warning-${id}`).style.display = "none";
                        document.getElementById(`historial-check-${id}`).style.display = "initial";
                        document.getElementById(`historial-mark-${id}`).style.display = "none";
                        estado.value = "true";
                        validarEstado();
                    }
                }
            },
            error: function (jqXHR, textStatus, errorThrow) {
                console.log(`${jqXHR}.${textStatus}: ${errorThrow}`)
            }
        })
    })
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

$(".button-editar-historial").on('click', function (e) {
    let _this = this;
    let id = $(this).data('id');
    let route = $(this).data('route');
    var button_editar_historial = this;
    var _token = $('input[name="_token"]').val();
    var unidad = [];
    var unidad_anterior = null;
    var unidad_1 = null;
    var unidad_2 = null;
    var unidad_3 = null;

    $(`#editar-historial-${id}`).fireModal({
        title: 'Editar historial de kilometraje',
        body: $('#editar-kilometraje-part').clone(),
        footerClass: 'bg-whitesmoke',
        autofocus: false,
        removeOnDismiss: true,
        created: function(modal, e, form) {

            $.ajax({
                type: "GET",
                url: route,
                data: {},
                dataType: "json",
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', _token);
                },
                success: function (respuesta) {
                    unidad = respuesta;
                    unidad = unidad.reverse();
                    console.log(unidad);

                    if(unidad.length > 3) {
                        //Quiere decir que hay 4 o más entonces tengo que validar con la que no se edita
                        unidad_anterior = unidad[0];
                        unidad_1 = unidad[1];
                        unidad_2 = unidad[2];
                        unidad_3 = unidad[3];

                        modal.find('p[class=kilometraje_anterior]').append("Kilometraje anterior: "+unidad_anterior.kilometraje+".");
                    }else{
                        if(unidad[0]) {
                            unidad_1 = unidad[0];
                        }
                        if(unidad[1]) {
                            unidad_2 = unidad[1];
                        }
                        if(unidad[2]) {
                            unidad_3 = unidad[2];
                        }
                        modal.find('p[class=kilometraje_anterior]').attr('hidden', 'true');
                    }

                    modal.find('input[name=unidad_id]').val(unidad_1.unidad_id).trigger("change");
                    modal.find('input[name=kilometraje_1]').val(unidad_1.kilometraje).trigger("change");
                    modal.find('input[name=historial_1_id]').val(unidad_1.id).trigger("change");
                    fecha = formatearFecha(unidad_1.created_at);
                    modal.find('label[id=kilometraje_1_label]').append(fecha+"<code>*</code>");

                    if(unidad_2) {
                        modal.find('input[name=kilometraje_2]').val(unidad_2.kilometraje).trigger("change");
                        modal.find('input[name=historial_2_id]').val(unidad_2.id).trigger("change");
                        fecha = formatearFecha(unidad_2.created_at);
                        modal.find('label[id=kilometraje_2_label]').append(fecha+"<code>*</code>");
                    }else{
                        modal.find('input[name=kilometraje_2]').attr('disabled', 'true').trigger("change");
                    }

                    if(unidad_3) {
                        modal.find('input[name=kilometraje_3]').val(unidad_3.kilometraje).trigger("change");
                        modal.find('input[name=historial_3_id]').val(unidad_3.id).trigger("change");
                        fecha = formatearFecha(unidad_3.created_at);
                        modal.find('label[id=kilometraje_3_label]').append(fecha+"<code>*</code>");
                    }else{
                        modal.find('input[name=kilometraje_3]').attr('disabled', 'true').trigger("change");
                    }
        
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
            });

        },
        onFormSubmit: function(modal, e, form) {
            e.preventDefault();
            //validaciones
            let id = modal.find('input[name=unidad_id]').val();
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
                        var input_kilometraje_2 = modal.find('input[name=kilometraje_2]').val();
                        var input_kilometraje_3 = modal.find('input[name=kilometraje_3]').val();

                        if(input_kilometraje_1.length) {
                            kilometraje_1_estado_modal = modal.find('input[name=kilometraje_1_estado_modal]')[0];
                            if(unidad_anterior) {
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
                        if(input_kilometraje_2.length) {
                            kilometraje_2_estado_modal = modal.find('input[name=kilometraje_2_estado_modal]')[0];
                            if(parseInt(input_kilometraje_2) < parseInt(input_kilometraje_1)) {
                                modal.removeClass("modal-progress")
                                kilometraje_2_estado_modal.value = "false";
                                modal.find("a[id=historial-modal-danger-2]")[0].style.display = "initial";
                                modal.find("a[id=historial-modal-mark-2]")[0].style.display = "none";
                                modal.find("a[id=historial-modal-warning-2]")[0].style.display = "none";
                                modal.find("a[id=historial-modal-check-2]")[0].style.display = "none";
                            } else if (parseInt(input_kilometraje_2) > data.porcentaje) {
                                var kilometraje_2_valor_comparar = modal.find('input[name=kilometraje_2_valor_comparar]').val();
                                if(parseInt(input_kilometraje_2) != parseInt(kilometraje_2_valor_comparar)) {
                                    modal.removeClass("modal-progress")
                                    kilometraje_2_estado_modal.value = "false";
                                    modal.find("a[id=historial-modal-danger-2]")[0].style.display = "none";
                                    modal.find("a[id=historial-modal-mark-2]")[0].style.display = "initial";
                                    modal.find("a[id=historial-modal-warning-2]")[0].style.display = "initial";
                                    modal.find("a[id=historial-modal-check-2]")[0].style.display = "none";
    
                                    modal.find("a[id=historial-modal-mark-2]")[0].addEventListener('click', function () {
                                        modal.find('input[name=kilometraje_2_valor_comparar]').val(parseInt(input_kilometraje_2));
                                        modal.find("a[id=historial-modal-warning-2]")[0].style.display = "none";
                                        modal.find("a[id=historial-modal-mark-2]")[0].style.display = "none";
    
                                        modal.find("a[id=historial-modal-check-2]")[0].style.display = "initial";
                                        kilometraje_2_estado_modal.value = "true";
                                    });
                                }
                            } else {
                                modal.find("a[id=historial-modal-danger-2]")[0].style.display = "none";
                                modal.find("a[id=historial-modal-mark-2]")[0].style.display = "none";
                                modal.find("a[id=historial-modal-warning-2]")[0].style.display = "none";
                                modal.find("a[id=historial-modal-check-2]")[0].style.display = "initial";
                                kilometraje_2_estado_modal.value = "true";
                            }
                        } else {
                            kilometraje_2_estado_modal = modal.find('input[name=kilometraje_2_estado_modal]')[0];
                            kilometraje_2_estado_modal.value = "true";
                        }
                        if(input_kilometraje_3.length) {
                            kilometraje_3_estado_modal = modal.find('input[name=kilometraje_3_estado_modal]')[0];
                            if(parseInt(input_kilometraje_3) < parseInt(input_kilometraje_2)) {
                                modal.removeClass("modal-progress")
                                kilometraje_3_estado_modal.value = "false";
                                modal.find("a[id=historial-modal-danger-3]")[0].style.display = "initial";
                                modal.find("a[id=historial-modal-mark-3]")[0].style.display = "none";
                                modal.find("a[id=historial-modal-warning-3]")[0].style.display = "none";
                                modal.find("a[id=historial-modal-check-3]")[0].style.display = "none";
                            } else if (parseInt(input_kilometraje_3) > data.porcentaje) {
                                var kilometraje_3_valor_comparar = modal.find('input[name=kilometraje_3_valor_comparar]').val();
                                if(parseInt(input_kilometraje_3) != kilometraje_3_valor_comparar) {
                                    modal.removeClass("modal-progress")
                                    kilometraje_3_estado_modal.value = "false";
                                    modal.find("a[id=historial-modal-danger-3]")[0].style.display = "none";
                                    modal.find("a[id=historial-modal-mark-3]")[0].style.display = "initial";
                                    modal.find("a[id=historial-modal-warning-3]")[0].style.display = "initial";
                                    modal.find("a[id=historial-modal-check-3]")[0].style.display = "none";
    
                                    modal.find("a[id=historial-modal-mark-3]")[0].addEventListener('click', function () {
                                        modal.find('input[name=kilometraje_3_valor_comparar]').val(parseInt(input_kilometraje_3));
                                        modal.find("a[id=historial-modal-warning-3]")[0].style.display = "none";
                                        modal.find("a[id=historial-modal-mark-3]")[0].style.display = "none";
    
                                        modal.find("a[id=historial-modal-check-3]")[0].style.display = "initial";
                                        kilometraje_3_estado_modal.value = "true";
                                    });
                                }
                            } else {
                                modal.find("a[id=historial-modal-danger-3]")[0].style.display = "none";
                                modal.find("a[id=historial-modal-mark-3]")[0].style.display = "none";
                                modal.find("a[id=historial-modal-warning-3]")[0].style.display = "none";
                                modal.find("a[id=historial-modal-check-3]")[0].style.display = "initial";
                                kilometraje_3_estado_modal.value = "true";
                            }
                        } else {
                            kilometraje_3_estado_modal = modal.find('input[name=kilometraje_3_estado_modal]')[0];
                            kilometraje_3_estado_modal.value = "true";
                        }

                        if(kilometraje_1_estado_modal.value == "true" && kilometraje_2_estado_modal.value == "true" && kilometraje_3_estado_modal.value == "true") {

                            historial_1 = modal.find('input[name=historial_1_id]').val();
                            historial_2 = modal.find('input[name=historial_2_id]').val();
                            historial_3 = modal.find('input[name=historial_3_id]').val();

                            $.ajax({
                                url: HOST + "/api/historiales/editarHistoriales",
                                data: {
                                    'unidad_id' : id,
                                    'historial_1': historial_1,
                                    'kilometraje_1': input_kilometraje_1,
                                    'historial_2': historial_2,
                                    'kilometraje_2': input_kilometraje_2,
                                    'historial_3': historial_3,
                                    'kilometraje_3': input_kilometraje_3
                                },
                                cache: false,
                                type: 'POST',
                                dataType: 'json',
                                beforeSend: function (xhr) {
                                    xhr.setRequestHeader('X-CSRF-Token', _token)
                                },
                                success: function (data) {
                                    
                                    _data = data;

                                    //el último debo ponerlo en la tabla en la última lectura y después volver a calcular el promedio.
                                    $('#historial_tabla_kilometraje_'+id).html(_data.historiales[_data.historiales.length-1].kilometraje + ' km');

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
                                            console.log("Verificar afuera");
                                            console.log(kms);
                                            console.log(data);
                       
                                            let estado_validar = document.getElementById(`historial-${id}-estado`);
                                            console.log(estado_validar);

                                            if (data.ultima == null) {
                                                // guardarEnBD();
                                            } else {
                                                if (kms == '') {
                                                    document.getElementById(`historial-mark-${id}`).style.display = "none";
                                                    document.getElementById(`historial-danger-${id}`).style.display = "none";
                                                    document.getElementById(`historial-warning-${id}`).style.display = "none";
                                                    document.getElementById(`historial-check-${id}`).style.display = "none";
                                                    estado_validar.value = "false";
                            
                                                } else if (kms < data.ultima.kilometraje) {
                                                    document.getElementById(`historial-danger-${id}`).style.display = "initial";
                                                    document.getElementById(`historial-mark-${id}`).style.display = "none";
                                                    document.getElementById(`historial-warning-${id}`).style.display = "none";
                                                    document.getElementById(`historial-check-${id}`).style.display = "none";
                            
                                                    estado_validar.value = "false";
                                                    validarEstado();
                                                } else if (kms > data.porcentaje) {
                                                    document.getElementById(`historial-danger-${id}`).style.display = "none";
                                                    document.getElementById(`historial-check-${id}`).style.display = "none";
                                                    document.getElementById(`historial-warning-${id}`).style.display = "initial";
                                                    document.getElementById(`historial-mark-${id}`).style.display = "initial";
                                                    estado_validar.value = "false";
                                                    validarEstado();
                            
                            
                                                    document.getElementById(`historial-mark-${id}`).addEventListener('click', function () {
                                                        document.getElementById(`historial-warning-${id}`).style.display = "none";
                                                        document.getElementById(`historial-mark-${id}`).style.display = "none";
                            
                                                        document.getElementById(`historial-check-${id}`).style.display = "initial";
                                                        estado_validar.value = "true";
                                                        validarEstado();
                                                    });
                                                } else {
                                                    document.getElementById(`historial-danger-${id}`).style.display = "none";
                                                    document.getElementById(`historial-warning-${id}`).style.display = "none";
                                                    document.getElementById(`historial-check-${id}`).style.display = "initial";
                                                    document.getElementById(`historial-mark-${id}`).style.display = "none";
                                                    estado_validar.value = "true";
                                                    validarEstado();
                                                }
                                            }


                                        },
                                        error: function (jqXHR, textStatus, errorThrow) {
                                            console.log('${jqXHR}.${textStatus}: ${errorThrow}')
                                        }
                                    });
                        
                                    iziToast.success({
                                        title: 'Éxito: ',
                                        message: "El historial de unidad se guardó correctamente",
                                        position: 'topRight',
                                        timeout: 5000,
                                    });


                                    modal.removeClass("modal-progress")
                                    modal.modal("hide");
                                },
                                error: function (jqXHR, textStatus, errorThrown) {
                                    console.log(errorThrown)

                                    iziToast.error({
                                        title: 'Error: ',
                                        message: "Se produjo un error al guardar los historiales. Por favor, inténtelo nuevamente.",
                                        position: 'topRight',
                                        timeout: 5000,
                                    });

                                }
                            })
                        }
                    }
                },
                error: function (jqXHR, textStatus, errorThrow) {
                    console.log(`${jqXHR}.${textStatus}: ${errorThrow}`)
                }
            })
            
            
        },
        shown: function(modal, form) {
            console.log("shown", modal, form)
        },
        buttons: [{
            text: 'Guardar',
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

