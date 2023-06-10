deleteRow();

$('#unidad_id').on("change", function (e) {
    var unidad_id = e.target.value;
    
    $.ajax({
        url: "show-mantenimiento",
        data: {
            unidad_id
        },
        type: "GET",
        dataType: 'json',
        success: function (data) {
            if (data.length > 0) {
                console.log(data);
                $("#historial").empty();

                $.each(data[0], function (i, item) {
                    var ult_mant;
                    var tareas = "-";
                    var frecuencia;
                    var proximo;
                    var tarea_id = "0";
                    var estado;
                    let rows;
                    var prox_modif = " - "
                    var mant;
                    var ot_abierta = " - ";

                    if (item.tareas.length) {

                        let rowspan = item.tareas.length;
                        rows = '<tr class="text-center">'
                        rows = rows + `<td rowspan="${rowspan}">${item.nombre}</td>`;
                        $.each(item.tareas, function (j, tarea) {
                            if(tarea.mantenimientos.length) {
                                var proximo_date;
                                var ult_mant_date;
                                prox_modif = " - ";
                                mant = tarea.mantenimientos[tarea.mantenimientos.length-1];
                                
                                tarea_id = tarea.id;
                                tareas = tarea.descripcion;
                                //console.log(tarea.frecuencia)

                                if(data[3][tarea_id]) {
                                    ot_abierta = data[3][tarea_id]['numeracion'] + " " + data[3][tarea_id]['base_operacion']['nombre'];
                                } else {
                                    ot_abierta = " - ";
                                }
        
                                if (tarea.frecuencia == "kms") {
                                    ult_mant = mant.ult_mantenimiento + ' km';
                                    frecuencia = tarea.kilometros + " km";

                                    //Comento esto porque sumaba mal, usaba el ultimo kilomwetraje. Capaz después hay que volver a usarlo o sumar los km.
                                    //proximo = parseInt(data[1].kilometraje) + parseInt(frecuencia) + " km";
                                    proximo = mant.prox_mantenimiento + " km";
                                    if(mant.mantenimiento_modif) {
                                        prox_modif = mant.mantenimiento_modif + ' km'
                                    }
                                    estado = mant.estado;

                                }
                                if(tarea.frecuencia == "dias") {                    
                                    //Fecha ultimo mantenimiento
                                    var dateMomentObject = moment(mant.ult_mantenimiento_fecha, "YYYY-MM-DD"); // 1st argument - string, 2nd argument - format
                                    var ult_mant = dateMomentObject.format("DD-MM-YYYY"); // convert moment.js object to Date object
                                    ult_mant_date = dateMomentObject.format("DD-MM-YYYY");
                                    //Fecha sumada al proximo
                                    var proxMomentDate = moment(mant.prox_mantenimiento_fecha, "YYYY-MM-DD");
                                    proximo_date = proxMomentDate.format("DD-MM-YYYY");
                                    var proximo = proxMomentDate.format("DD-MM-YYYY");

                                    if(mant.mantenimiento_modif_fecha) {
                                        var proxModifMomentDate = moment(mant.mantenimiento_modif_fecha, "YYYY-MM-DD");
                                        prox_modif = proxModifMomentDate.format("DD-MM-YYYY");
                                    }
    
                                    frecuencia = tarea.dias + " días";
                                    estado = mant.estado;
                                    /* proximo = proximo.toLocaleDateString();
                                    ult_mant = ult_mant.toLocaleDateString(); */
                                }
                                if(tarea.frecuencia == "combinado") {
                                    var ult_mant_km = mant.ult_mantenimiento + ' km';
                                    //Fecha ultimo mantenimiento
                                    var dateMomentObject = moment(mant.ult_mantenimiento_fecha, "YYYY-MM-DD"); // 1st argument - string, 2nd argument - format
                                    var ult_mant_f = dateMomentObject.format("DD-MM-YYYY"); // convert moment.js object to Date object
                                    ult_mant = ult_mant_km + ' / ' + ult_mant_f;
                                    ult_mant_date = dateMomentObject.format("DD-MM-YYYY");

                                    var proximo_km = mant.prox_mantenimiento + " km";
                                    //Fecha sumada al proximo
                                    var proxMomentDate = moment(mant.prox_mantenimiento_fecha, "YYYY-MM-DD");
                                    var proximo_f = proxMomentDate.format("DD-MM-YYYY");
                                    proximo = proximo_km + ' / ' + proximo_f;
                                    proximo_date = proxMomentDate.format("DD-MM-YYYY");

                                    if(mant.mantenimiento_modif && mant.mantenimiento_modif_fecha) {
                                        var prox_modif_km;
                                        var prox_modif_f;
                                        prox_modif_km = mant.mantenimiento_modif + ' km'
                                        var proxModifMomentDate = moment(mant.mantenimiento_modif_fecha, "YYYY-MM-DD");
                                        prox_modif_f = proxModifMomentDate.format("DD-MM-YYYY");
                                        prox_modif = prox_modif_km + ' / ' + prox_modif_f;
                                    }
    
                                    frecuencia = tarea.kilometros + ' km / ' +tarea.dias + " días";
                                    estado = mant.estado;
                                    /* proximo = proximo.toLocaleDateString();
                                    ult_mant = ult_mant.toLocaleDateString(); */
                                }
                                if(j != 0){
                                    //Esto por un tema del rowspan para que quede bien la tabla.
                                    rows += `<tr class="text-center">`;
                                }
                                console.log("mant:", mant);
                                rows += `<td>${tareas}</td>
                                <td id="ult_mant_${i}_${tarea_id}">${ult_mant}</td>
                                <td>${frecuencia}</td>
                                <td id="proximo_${i}_${tarea_id}">${proximo}</td>
                                <td id="proximo_modificado_${i}_${tarea_id}">${prox_modif}</td>
                                <td id="estado_${i}_${tarea_id}">${estado}</td>
                                <td> ${ot_abierta} </td>
                                <td>
                                    <button id="boton_${i}_${tarea_id}" type="button" class="btn btn-light accion" 
                                    data-proximo-km="${proximo}" 
                                    data-proximo-fecha="${proximo_date}" 
                                    data-tarea-id="${tarea_id}" 
                                    data-frecuencia="${tarea.frecuencia}" 
                                    data-ultimo-mant-km="${ult_mant}" 
                                    data-ultimo-mant-fecha="${ult_mant_date}" 
                                    data-prox-modif-km="${mant.mantenimiento_modif}"
                                    data-prox-modif-fecha="${mant.mantenimiento_modif_fecha}"
                                    id="tarea-${tarea_id}" title="Adelantar/Posponer" onclick="lanzar_modal(${i},${tarea_id})">
                                        Adelantar/Posponer
                                    </button>
                                </td>
                                </tr>`;
    
                                rows += `
                                <input type="hidden" id="mantenimiento[${i}][${tarea_id}][mantenimiento_id]" name="mantenimiento[${i}][${tarea_id}][mantenimiento_id]" value="${mant.id}">
                                <input type="hidden" id="mantenimiento[${i}][${tarea_id}][unidad_id]" name="mantenimiento[${i}][${tarea_id}][unidad_id]" value="${unidad_id}">
                                <input type="hidden" id="mantenimiento[${i}][${tarea_id}][componente_id]" name="mantenimiento[${i}][${tarea_id}][componente_id]" value="${item.id}">
                                <input type="hidden" id="mantenimiento[${i}][${tarea_id}][tarea_id]" name="mantenimiento[${i}][${tarea_id}][tarea_id]" value="${tarea_id}">
                                <input type="hidden" id="mantenimiento[${i}][${tarea_id}][ult_mant]" name="mantenimiento[${i}][${tarea_id}][ult_mant]" value="${mant.ult_mantenimiento}">
                                <input type="hidden" id="mantenimiento[${i}][${tarea_id}][ult_mant_fecha]" name="mantenimiento[${i}][${tarea_id}][ult_mant_fecha]" value="${mant.ult_mantenimiento_fecha}">
                                <input type="hidden" id="mantenimiento[${i}][${tarea_id}][frecuencia]" name="mantenimiento[${i}][${tarea_id}][frecuencia]" value="${tarea.kilometros}">
                                <input type="hidden" id="mantenimiento[${i}][${tarea_id}][frecuencia_dias]" name="mantenimiento[${i}][${tarea_id}][frecuencia_dias]" value="${tarea.dias}">
                                <input type="hidden" id="mantenimiento[${i}][${tarea_id}][proximo]" name="mantenimiento[${i}][${tarea_id}][proximo]" value="${mant.prox_mantenimiento}">
                                <input type="hidden" id="mantenimiento[${i}][${tarea_id}][proximo_fecha]" name="mantenimiento[${i}][${tarea_id}][proximo_fecha]" value="${mant.prox_mantenimiento_fecha}">
                                <input type="hidden" id="mantenimiento[${i}][${tarea_id}][mantenimiento_modif]" name="mantenimiento[${i}][${tarea_id}][mantenimiento_modif]" value="${mant.mantenimiento_modif}">
                                <input type="hidden" id="mantenimiento[${i}][${tarea_id}][mantenimiento_modif_fecha]" name="mantenimiento[${i}][${tarea_id}][mantenimiento_modif_fecha]" value="${mant.mantenimiento_modif_fecha}">
                                <input type="hidden" id="mantenimiento[${i}][${tarea_id}][estado]" name="mantenimiento[${i}][${tarea_id}][estado]" value="${estado}">
                                `;

                            }

                        })
                    }else{
                        //Si no hay tareas el item entero.
                        rows = `<tr class="text-center">
                            <td>${item.nombre}</td>
                            <td>${tareas}</td>
                            <td> - </td>
                            <td> - </td>
                            <td> - </td>
                            <td> - </td>
                            <td> - </td>
                            <td> - </td>
                            <td> - </td>
                        </tr>`;
                    }

                    $('#historial').append(rows);

                    //detectButtons(frecuencia, tarea_id);
                })
            } else {
                $("#historial").empty();

                let row = `<tr class="text-center">
                        <td colspan="9">No se encontraron resultados</td>
                    </tr>`;

                $('#historial').append(row);

                var elements = document.querySelectorAll(".accion");

                for(let i = 0; i < elements.length; i++) {
                    //We add eventListener to each element
                    elements[i].addEventListener("click", lanzarModal());
                }
            }
        },
        error: function (jqXHR, textStatus, errorThrow) {
            console.log(`${textStatus}: ${errorThrow}`)
        }
    })
});

$('#unidad_id_recurso_actividad').on("change", function (e) {
    var unidad_id = e.target.value;

    $('.boton-replicar').show('fast');
    $('.unidades_plan').empty().trigger('change');
    buscarUnidadesCopiar();
   
    $.ajax({
        url: "show-mantenimiento",
        data: {
            unidad_id
        },
        type: "GET",
        dataType: 'json',
        success: function (data) {
            if (data.length > 0) {
                $("#historial").empty();

                $.each(data[0], function (i, item) {
                    var tareas = "-";
                    var tarea_id = "0";
                    let rows;

                    if (item.tareas.length) {
                        let rowspan = item.tareas.length;
                        rows = '<tr class="text-center">'
                        rows = rows + `<td rowspan="${rowspan}">${item.nombre}</td>`;
                        $.each(item.tareas, function (j, tarea) {
                            if(tarea.mantenimientos.length) {
                                tarea_id = tarea.id;
                                componente_id = tarea.componente_id;
                                tareas = tarea.descripcion;

                                if(j != 0){
                                    //Esto por un tema del rowspan para que quede bien la tabla.
                                    rows += `<tr class="text-center">`;
                                }
                            
                                rows += `<td>${tareas}</td>
                               
                                <td>`
                      

                                if(tarea.piezas) {
                                    $.each(tarea.piezas , function (k , pieza){
                                        rows+=`<ol>`+ 
                                            pieza.descripcion+" : "+pieza.pivot.cantidad+" "+pieza.unidad_medida.nombre +
                                        `<hr> </ol>` 
                                    })
                                }; 

                     
                               rows += 
                                `</td> 

                                <td>
                                    <a href="asociar-recursos?unidad_id=${unidad_id}&tarea_id=${tarea_id}&componente_id=${componente_id}" id="boton_${i}_${tarea_id}" class="btn btn-primary text-white" 
                                    data-tarea-id="${tarea_id}" 
                     
                              
                                    id="tarea-${tarea_id}" title="Asociar/Desasociar recursos">
                                        Asociar/Desasociar recursos
                                    </a>
                                </td>
                                </tr>`;
                               
                            }

                        })
                    }else{
                        //Si no hay tareas el item entero.
                        rows = `<tr class="text-center">
                            <td>${item.nombre}</td>
                            <td>${tareas}</td>
                            <td> - </td>
                        </tr>`;
                    }

                    $('#historial').append(rows);

                    //detectButtons(frecuencia, tarea_id);
                })
            } else {
                $("#historial").empty();

                let row = `<tr class="text-center">
                        <td colspan="9">No se encontraron resultados</td>
                    </tr>`;

                $('#historial').append(row);
                
            }
        },
        error: function (jqXHR, textStatus, errorThrow) {
            console.log(`${textStatus}: ${errorThrow}`)
        }
    })
});

$('#agregar-recurso-pieza').on('click', function () {
    let recursoId = $("#recurso_id").val();
    let recursoText = $("#recurso_id option:selected").text();
    let unidadText = $("#recurso_id option:selected").data("unidad-text");
    let unidadId = $("#recurso_id option:selected").data("unidad-id");
    let actividadId = $("#recurso_id option:selected").data("actividad-id");

    let cantidad_recurso = $('#cant_recurso').val();
    let check_replicar = $('#replicar_todas').is(':checked');
    var _token = $("input[name='_token']").val();

    if(recursoId) {
        //Valido que no haya ingresado ya ese recurso/pieza.
        existe_recurso = $('input[name="listado_piezas_recursos['+recursoId+'][pieza_id]"]');

        if(existe_recurso.length > 0) {
            iziToast.info({
                timeout: 15000,
                position: "topRight",
                title: "Info :",
                message:
                    "La tarea ya tiene asociado este recurso. Si desea, puede editarlo.",
            });
            return;
        }
        if(!cantidad_recurso) {
            iziToast.error({
                timeout: 15000,
                position: "topRight",
                title: "Error :",
                message:
                    "Primero ingrese la cantidad.",
            });
        } else {
            var data = {
                'pieza_id':  recursoId,
                'unidad_id':  unidadId,
                'tarea_id':  actividadId,
                'cantidad':  cantidad_recurso,
            }
            $.ajax({
                type: "PUT",
                url: HOST + "/api/recurso-actividad/agregarRecurso",
                data: data,
                dataType: 'json',
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', _token)
                },
                success: function (respuesta) {

                    if(respuesta.respuesta.estado) {
                        agregarRow(recursoId,recursoText,unidadText,unidadId,actividadId, cantidad_recurso);
                        iziToast.success({
                            title: 'Bien: ',
                            message: respuesta.respuesta.mensaje,
                            position: 'topRight',
                            timeout: 15000,
                        });
                    } else {
                        iziToast.error({
                            title: 'Error: ',
                            message: respuesta.respuesta.mensaje,
                            position: 'topRight',
                            timeout: 15000,
                        });
                    }
                },
                fail: function () {
                    iziToast.error({
                        title: 'Error: ',
                        message: "Se produjo un error al agregar el recurso. Por favor, inténtelo nuevamente.",
                        position: 'topRight',
                        timeout: 15000,
                    });
                },
                error: function (jqXHR, textStatus, errorThrow) {
                    console.log(`${textStatus}: ${errorThrow}`)
                }
            });
            if(check_replicar) {

                let componente_id = $('#componente_id').val();
                let plan_id = $('#plan_id').val();

                var data = {
                    'pieza_id':  recursoId,
                    'unidad_id':  unidadId,
                    'tarea_id':  actividadId,
                    'cantidad':  cantidad_recurso,
                    'plan_id': plan_id,
                    'componente_id': componente_id
                }
                $.ajax({
                    type: "PUT",
                    url: HOST + "/api/recurso-actividad/agregarRecursoReplicar",
                    data: data,
                    dataType: 'json',
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('X-CSRF-Token', _token)
                    },
                    success: function (respuesta) {

                        console.log(respuesta);

                        if(respuesta.respuesta.estado) {
                            iziToast.success({
                                title: 'Bien: ',
                                message: respuesta.respuesta.mensaje,
                                position: 'topRight',
                                timeout: 15000,
                            });
                        } else {
                            iziToast.error({
                                title: 'Error: ',
                                message: respuesta.respuesta.mensaje,
                                position: 'topRight',
                                timeout: 15000,
                            });
                        }
                    },
                    fail: function () {
                        iziToast.error({
                            title: 'Error: ',
                            message: "Se produjo un error al agregar el recurso. Por favor, inténtelo nuevamente.",
                            position: 'topRight',
                            timeout: 15000,
                        });
                    },
                    error: function (jqXHR, textStatus, errorThrow) {
                        console.log(`${textStatus}: ${errorThrow}`)
                    }
                });
            }
            $("#recurso_id").val(null).trigger('change');
            $('#cant_recurso').val(null);
        }
    } else {
        iziToast.error({
            timeout: 15000,
            position: "topRight",
            title: "Error :",
            message:
                "Primero seleccione un recurso.",
        });
    }
});

$(document).on('click', '.editar-recurso', editar_recurso);

function editar_recurso() {
    console.log($(this).closest('.tr-padre').find('.cantidad').val());

    let valor_editando = $('.editando');
    if(valor_editando.length > 0) {
        iziToast.info({
            timeout: 15000,
            position: "topRight",
            title: "Info :",
            message:
                "Por favor, termine de editar, o cancele la edición de la pieza actual.",
        });
        return;
    }

    console.log($(this).closest('.tr-padre').find('.cancelar-editar-recurso-span'));
    $('.cantidad').attr('readonly', true);
    $('.editar-recurso-span').attr('hidden', false);
    $('.cancelar-editar-recurso-span').attr('hidden', true);
    $('.confirmar-editar-recurso-span').attr('hidden', true);
    $(this).closest('.tr-padre').find('.cantidad').attr('readonly', false);
    $(this).closest('.tr-padre').find('.editar-recurso-span').attr('hidden', true);
    $(this).closest('.tr-padre').find('.cancelar-editar-recurso-span').attr('hidden', false);
    $(this).closest('.tr-padre').find('.confirmar-editar-recurso-span').attr('hidden', false);
    $(this).closest('.tr-padre').find('.cantidad').addClass('editando'); 

}

$(document).on('click', '.cancelar-editar-recurso', cancelar_editar_recurso);

function cancelar_editar_recurso() {
    $(this).closest('.tr-padre').find('.cantidad').removeClass('editando');
    $('.cantidad').attr('readonly', true);
    $('.editar-recurso-span').attr('hidden', false);
    $('.cancelar-editar-recurso-span').attr('hidden', true);
    $('.confirmar-editar-recurso-span').attr('hidden', true);
}

$(document).on('click', '.confirmar-editar-recurso', confirmar_editar_recurso);

function confirmar_editar_recurso() {

    var valor_anterior = $(this).closest('.tr-padre').find('.cantidad').data('valor');
    var cantidad_recurso = $(this).closest('.tr-padre').find('.cantidad').val();

    if(valor_anterior == cantidad_recurso) {
        iziToast.info({
            title: 'Info: ',
            message: "No modificó el valor. Para guardar cambios, modifique la cantidad.",
            position: 'topRight',
            timeout: 15000,
        });
        return;
    }


    var recurso_nombre = $(this).closest('.tr-padre').find('.id_descripcion').data('nombre')

    var retVal = confirm("Desea replicar la edicion de "+recurso_nombre+" a todas las unidades del plan?");
    if (retVal == true) {
        let pieza_id = $(this).closest('.tr-padre').find('.pieza_id').val();
        let unidad_id = $('input[name="unidad_id"]').val();
        let tarea_id = $(this).closest('.tr-padre').find('.tarea_id').val();
        let _this = this;
    
        let cantidad_recurso = $(this).closest('.tr-padre').find('.cantidad').val();
    
        var _token = $("input[name='_token']").val();
    
        var data = {
            'pieza_id':  pieza_id,
            'unidad_id':  unidad_id,
            'tarea_id':  tarea_id,
            'cantidad':  cantidad_recurso,
        }
        $.ajax({
            type: "PUT",
            url: HOST + "/api/recurso-actividad/editarRecursoReplicar",
            data: data,
            dataType: 'json',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-Token', _token)
            },
            success: function (respuesta) {
    
                console.log(respuesta);
                if(respuesta.respuesta.estado) {
                    iziToast.success({
                        title: 'Bien: ',
                        message: respuesta.respuesta.mensaje,
                        position: 'topRight',
                        timeout: 15000,
                    });
    
                    $(_this).closest('.tr-padre').find('.cantidad').removeClass('editando');
                    $('.cantidad').attr('readonly', true);
                    $('.cancelar-editar-recurso-span').attr('hidden', true);
                    $('.confirmar-editar-recurso-span').attr('hidden', true);
                    $('.editar-recurso-span').attr('hidden', false);
    
                } else {
                    iziToast.error({
                        title: 'Error: ',
                        message: respuesta.respuesta.mensaje,
                        position: 'topRight',
                        timeout: 15000,
                    });
                }
            },
            fail: function () {
                iziToast.error({
                    title: 'Error: ',
                    message: "Se produjo un error al agregar el recurso. Por favor, inténtelo nuevamente.",
                    position: 'topRight',
                    timeout: 15000,
                });
            },
            error: function (jqXHR, textStatus, errorThrow) {
                console.log(`${textStatus}: ${errorThrow}`)
            }
        });
    } else {

        let pieza_id = $(this).closest('.tr-padre').find('.pieza_id').val();
        let unidad_id = $('input[name="unidad_id"]').val();
        let tarea_id = $(this).closest('.tr-padre').find('.tarea_id').val();
        let _this = this;

        let cantidad_recurso = $(this).closest('.tr-padre').find('.cantidad').val();

        var _token = $("input[name='_token']").val();

        var data = {
            'pieza_id':  pieza_id,
            'unidad_id':  unidad_id,
            'tarea_id':  tarea_id,
            'cantidad':  cantidad_recurso,
        }
        $.ajax({
            type: "PUT",
            url: HOST + "/api/recurso-actividad/editarRecurso",
            data: data,
            dataType: 'json',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-Token', _token)
            },
            success: function (respuesta) {

                console.log(respuesta);
                if(respuesta.respuesta.estado) {
                    iziToast.success({
                        title: 'Bien: ',
                        message: respuesta.respuesta.mensaje,
                        position: 'topRight',
                        timeout: 15000,
                    });

                    $(_this).closest('.tr-padre').find('.cantidad').removeClass('editando');
                    $('.cantidad').attr('readonly', true);
                    $('.cancelar-editar-recurso-span').attr('hidden', true);
                    $('.confirmar-editar-recurso-span').attr('hidden', true);
                    $('.editar-recurso-span').attr('hidden', false);

                } else {
                    iziToast.error({
                        title: 'Error: ',
                        message: respuesta.respuesta.mensaje,
                        position: 'topRight',
                        timeout: 15000,
                    });
                }
            },
            fail: function () {
                iziToast.error({
                    title: 'Error: ',
                    message: "Se produjo un error al agregar el recurso. Por favor, inténtelo nuevamente.",
                    position: 'topRight',
                    timeout: 15000,
                });
            },
            error: function (jqXHR, textStatus, errorThrow) {
                console.log(`${textStatus}: ${errorThrow}`)
            }
        });
    }
}

/* $(document).on('click', '.confirmar-editar-recurso-replicar', confirmar_editar_recurso_replicar);

function confirmar_editar_recurso_replicar() {
    let pieza_id = $(this).closest('.tr-padre').find('.pieza_id').val();
    let unidad_id = $('input[name="unidad_id"]').val();
    let tarea_id = $(this).closest('.tr-padre').find('.tarea_id').val();
    let _this = this;

    let cantidad_recurso = $(this).closest('.tr-padre').find('.cantidad').val();

    var _token = $("input[name='_token']").val();

    var data = {
        'pieza_id':  pieza_id,
        'unidad_id':  unidad_id,
        'tarea_id':  tarea_id,
        'cantidad':  cantidad_recurso,
    }
    $.ajax({
        type: "PUT",
        url: HOST + "/api/recurso-actividad/editarRecursoReplicar",
        data: data,
        dataType: 'json',
        beforeSend: function (xhr) {
            xhr.setRequestHeader('X-CSRF-Token', _token)
        },
        success: function (respuesta) {

            console.log(respuesta);
            if(respuesta.respuesta.estado) {
                iziToast.success({
                    title: 'Bien: ',
                    message: respuesta.respuesta.mensaje,
                    position: 'topRight',
                    timeout: 15000,
                });

                $(_this).closest('.tr-padre').find('.cantidad').removeClass('editando');
                $('.cantidad').attr('readonly', true);
                $('.cancelar-editar-recurso-span').attr('hidden', true);
                $('.confirmar-editar-recurso-span').attr('hidden', true);

            } else {
                iziToast.error({
                    title: 'Error: ',
                    message: respuesta.respuesta.mensaje,
                    position: 'topRight',
                    timeout: 15000,
                });
            }
        },
        fail: function () {
            iziToast.error({
                title: 'Error: ',
                message: "Se produjo un error al agregar el recurso. Por favor, inténtelo nuevamente.",
                position: 'topRight',
                timeout: 15000,
            });
        },
        error: function (jqXHR, textStatus, errorThrow) {
            console.log(`${textStatus}: ${errorThrow}`)
        }
    });

}
 */
$(document).on('click', '.delete-row', eliminar_recurso);

function eliminar_recurso() {

    var recurso_nombre = $(this).closest('.tr-padre').find('.id_descripcion').data('nombre')

    var retVal = confirm("Se eliminará el recurso "+recurso_nombre+" de esta tarea. Continuar?");
    if (retVal == true)
    {
        var retVal2 = confirm("Desea eliminar el recurso "+recurso_nombre+" de TODAS las unidades del plan?");
        if(retVal2 == true) {
            var recurso_nombre = $(this).closest('.tr-padre').find('.id_descripcion').data('nombre')

            let pieza_id = $(this).closest('.tr-padre').find('.pieza_id').val();
            let unidad_id = $('input[name="unidad_id"]').val();
            let tarea_id = $(this).closest('.tr-padre').find('.tarea_id').val();
            let _this = this;
            let plan_id = $('#plan_id').val();
            let componente_id = $('#componente_id').val();
        
            let cantidad_recurso = $(this).closest('.tr-padre').find('.cantidad').val();
        
            var _token = $("input[name='_token']").val();
        
            console.log("entre");
        
            var data = {
                'pieza_id':  pieza_id,
                'tarea_id':  tarea_id,
                'cantidad':  cantidad_recurso,
                'plan_id': plan_id,
                'componente_id': componente_id
            }
            $.ajax({
                type: "PUT",
                url: HOST + "/api/recurso-actividad/eliminarRecursoReplicar",
                data: data,
                dataType: 'json',
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', _token)
                },
                success: function (respuesta) {
        
                    console.log(respuesta);
                    if(respuesta.respuesta.estado) {
                        iziToast.success({
                            title: 'Bien: ',
                            message: respuesta.respuesta.mensaje,
                            position: 'topRight',
                            timeout: 15000,
                        });
        
                        let index = $(_this).data('index');
                        $('.index-'+index).remove();
        
                    } else {
                        iziToast.error({
                            title: 'Error: ',
                            message: respuesta.respuesta.mensaje,
                            position: 'topRight',
                            timeout: 15000,
                        });
                    }
                },
                fail: function () {
                    iziToast.error({
                        title: 'Error: ',
                        message: "Se produjo un error al agregar el recurso. Por favor, inténtelo nuevamente.",
                        position: 'topRight',
                        timeout: 15000,
                    });
                },
                error: function (jqXHR, textStatus, errorThrow) {
                    console.log(`${textStatus}: ${errorThrow}`)
                }
            });
        } else {
            let pieza_id = $(this).closest('.tr-padre').find('.pieza_id').val();
            let unidad_id = $('input[name="unidad_id"]').val();
            let tarea_id = $(this).closest('.tr-padre').find('.tarea_id').val();
            let _this = this;
        
            let cantidad_recurso = $(this).closest('.tr-padre').find('.cantidad').val();
        
            var _token = $("input[name='_token']").val();
        
            console.log("entre");
        
            var data = {
                'pieza_id':  pieza_id,
                'unidad_id':  unidad_id,
                'tarea_id':  tarea_id,
                'cantidad':  cantidad_recurso,
            }
            $.ajax({
                type: "PUT",
                url: HOST + "/api/recurso-actividad/eliminarRecurso",
                data: data,
                dataType: 'json',
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', _token)
                },
                success: function (respuesta) {
        
                    console.log(respuesta);
                    if(respuesta.respuesta.estado) {
                        iziToast.success({
                            title: 'Bien: ',
                            message: respuesta.respuesta.mensaje,
                            position: 'topRight',
                            timeout: 15000,
                        });
        
                        let index = $(_this).data('index');
                        $('.index-'+index).remove();
        
                    } else {
                        iziToast.error({
                            title: 'Error: ',
                            message: respuesta.respuesta.mensaje,
                            position: 'topRight',
                            timeout: 15000,
                        });
                    }
                },
                fail: function () {
                    iziToast.error({
                        title: 'Error: ',
                        message: "Se produjo un error al agregar el recurso. Por favor, inténtelo nuevamente.",
                        position: 'topRight',
                        timeout: 15000,
                    });
                },
                error: function (jqXHR, textStatus, errorThrow) {
                    console.log(`${textStatus}: ${errorThrow}`)
                }
            });
        }
    } 
    else
    {
        return false;
    }

}

/* $(document).on('click', '.delete-row-replicar', eliminar_recurso_replicar);

function eliminar_recurso_replicar() {

    var recurso_nombre = $(this).closest('.tr-padre').find('.id_descripcion').data('nombre')

    let pieza_id = $(this).closest('.tr-padre').find('.pieza_id').val();
    let unidad_id = $('input[name="unidad_id"]').val();
    let tarea_id = $(this).closest('.tr-padre').find('.tarea_id').val();
    let _this = this;
    let plan_id = $('#plan_id').val();
    let componente_id = $('#componente_id').val();

    let cantidad_recurso = $(this).closest('.tr-padre').find('.cantidad').val();

    var _token = $("input[name='_token']").val();

    console.log("entre");

    var data = {
        'pieza_id':  pieza_id,
        'tarea_id':  tarea_id,
        'cantidad':  cantidad_recurso,
        'plan_id': plan_id,
        'componente_id': componente_id
    }
    $.ajax({
        type: "PUT",
        url: HOST + "/api/recurso-actividad/eliminarRecursoReplicar",
        data: data,
        dataType: 'json',
        beforeSend: function (xhr) {
            xhr.setRequestHeader('X-CSRF-Token', _token)
        },
        success: function (respuesta) {

            console.log(respuesta);
            if(respuesta.respuesta.estado) {
                iziToast.success({
                    title: 'Bien: ',
                    message: respuesta.respuesta.mensaje,
                    position: 'topRight',
                    timeout: 15000,
                });

                let index = $(_this).data('index');
                $('.index-'+index).remove();

            } else {
                iziToast.error({
                    title: 'Error: ',
                    message: respuesta.respuesta.mensaje,
                    position: 'topRight',
                    timeout: 15000,
                });
            }
        },
        fail: function () {
            iziToast.error({
                title: 'Error: ',
                message: "Se produjo un error al agregar el recurso. Por favor, inténtelo nuevamente.",
                position: 'topRight',
                timeout: 15000,
            });
        },
        error: function (jqXHR, textStatus, errorThrow) {
            console.log(`${textStatus}: ${errorThrow}`)
        }
    });
    
} */


function lanzar_modal(i, j) {    
  var frecuencia = document.getElementById('boton_' + i +'_'+ j).getAttribute('data-frecuencia');
  var tarea_id = document.getElementById('boton_' + i +'_'+ j).getAttribute('data-tarea-id');
  var data_proximo_km = document.getElementById('boton_' + i +'_'+ j).getAttribute('data-proximo-km');
  var data_proximo_fecha = document.getElementById('boton_' + i +'_'+ j).getAttribute('data-proximo-fecha');
  var data_ultimo_mant_km = document.getElementById('boton_' + i +'_'+ j).getAttribute('data-ultimo-mant-km');
  var data_ultimo_mant_fecha = document.getElementById('boton_' + i +'_'+ j).getAttribute('data-ultimo-mant-fecha');
  var data_prox_modif_km = document.getElementById('boton_' + i +'_'+ j).getAttribute('data-prox-modif-km');
  var data_prox_modif_fecha = document.getElementById('boton_' + i +'_'+ j).getAttribute('data-prox-modif-fecha');
  console.log(data_prox_modif_km);
  console.log(data_prox_modif_fecha);
  var fecha_proximo;
  var fecha_ultimo;
  var proximo_string;
  var ultimo_string;
  var fecha_modif;

  if (frecuencia.includes("dias")) {
      $('#boton_' + i +'_'+ j).fireModal({
            title: 'Ingrese la fecha',
            body: $('#modal-fecha-part').clone(),
            footerClass: 'bg-whitesmoke',
            autofocus: false,
            removeOnDismiss: true,          
            created: function(modal, e, form) {
                fecha_proximo = moment(data_proximo_fecha, "DD-MM-YYYY");
                if(data_prox_modif_fecha != 'null') {
                    fecha_modif = moment(data_prox_modif_fecha, "YYYY-MM-DD");
                    modal.find('input[name=fecha]').val(fecha_modif.format("YYYY-MM-DD"));
                } else {
                    modal.find('input[name=fecha]').val(fecha_proximo.format("YYYY-MM-DD"));
                }
                fecha_ultimo = moment(data_ultimo_mant_fecha, "DD-MM-YYYY").format("YYYY-MM-DD");
            },
            onFormSubmit: function (modal, e, form) {
                e.preventDefault();

                let fecha_nueva = modal.find('input[name=fecha]').val();
                let fecha = moment(fecha_nueva, "YYYY-MM-DD");
                console.log("fecha proximo", fecha_proximo);
                console.log("fecha ultimo", fecha_ultimo);
                if(fecha.isBefore(fecha_ultimo,'day')){
                    modal.removeClass("modal-progress");
                    iziToast.error({
                        title: 'Error: ',
                        message: "La fecha debe ser mayor a la del último mantenimiento.",
                        position: 'topRight',
                        timeout: 15000,
                    });
                }else{

                    if(!fecha_proximo.isSame(fecha,'day')) {
                        if(fecha.isBefore(fecha_proximo,'day')){
                            $('input[name="mantenimiento['+ i +']['+ j +'][estado]"]').val("Adelantada");
                        }
                        if(fecha.isAfter(fecha_proximo)){
                            $('input[name="mantenimiento['+ i +']['+ j +'][estado]"]').val("Pospuesta");
                        }
                        $('input[name="mantenimiento['+ i +']['+ j +'][mantenimiento_modif_fecha]"]').val(fecha_nueva);

                        var _token = $("input[name='_token']").val();

                        var data = {
                            'mantenimiento_id':  document.getElementById('mantenimiento['+i+']['+j+'][mantenimiento_id]').value,
                            'unidad_id':  document.getElementById('mantenimiento['+i+']['+j+'][unidad_id]').value,
                            'componente_id':  document.getElementById('mantenimiento['+i+']['+j+'][componente_id]').value,
                            'tarea_id':  document.getElementById('mantenimiento['+i+']['+j+'][tarea_id]').value,
                            'ult_mant':  document.getElementById('mantenimiento['+i+']['+j+'][ult_mant]').value,
                            'ult_mant_fecha':  document.getElementById('mantenimiento['+i+']['+j+'][ult_mant_fecha]').value,
                            'frecuencia':  document.getElementById('mantenimiento['+i+']['+j+'][frecuencia]').value,
                            'frecuencia_dias':  document.getElementById('mantenimiento['+i+']['+j+'][frecuencia_dias]').value,
                            'proximo':  document.getElementById('mantenimiento['+i+']['+j+'][proximo]').value,
                            'proximo_fecha':  document.getElementById('mantenimiento['+i+']['+j+'][proximo_fecha]').value,
                            'mantenimiento_modif':  document.getElementById('mantenimiento['+i+']['+j+'][mantenimiento_modif]').value,
                            'mantenimiento_modif_fecha':  document.getElementById('mantenimiento['+i+']['+j+'][mantenimiento_modif_fecha]').value,
                            'estado':  document.getElementById('mantenimiento['+i+']['+j+'][estado]').value,
                        }
        
                        $.ajax({
                            type: "PUT",
                            url: HOST + "/api/mantenimiento-rutinario/editarMantenimiento",
                            data: data,
                            dataType: 'json',
                            beforeSend: function (xhr) {
                                xhr.setRequestHeader('X-CSRF-Token', _token)
                            },
                            success: function (respuesta) {
                                console.log(respuesta);

                                $('#proximo_modificado_' + i +'_'+ j).html('').append(fecha.format("DD-MM-YYYY"));
                                document.getElementById('boton_' + i +'_'+ j).setAttribute('data-prox-modif-fecha', fecha.format("YYYY-MM-DD"));
                                if(fecha.isBefore(fecha_proximo,'day')){
                                    $('#estado_' + i +'_'+ j).html('').append("Adelantada");
                                }
                                if(fecha.isAfter(fecha_proximo)){
                                    $('#estado_' + i +'_'+ j).html('').append("Pospuesta");
                                }

                                iziToast.success({
                                    title: 'Éxito: ',
                                    message: "El mantenimiento se actualizó correctamente.",
                                    position: 'topRight',
                                    timeout: 15000,
                                });

                                modal.removeClass("modal-progress")
                                modal.modal("hide");
                            },
                            fail: function () {
                                modal.removeClass("modal-progress");
                                iziToast.error({
                                    title: 'Error: ',
                                    message: "Se produjo un error al modificar el mantenimiento. Por favor, inténtelo nuevamente.",
                                    position: 'topRight',
                                    timeout: 15000,
                                });

                            },
                            error: function (jqXHR, textStatus, errorThrow) {
                                console.log(`${textStatus}: ${errorThrow}`)
                            }
                        });

                    }else{
                        modal.removeClass("modal-progress");
                        iziToast.info({
                            title: 'Atención: ',
                            message: "La nueva fecha debe ser diferente a la actual.",
                            position: 'topRight',
                            timeout: 15000,
                        });
                    }
    
                }
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
      })
  } 
  if (frecuencia.includes("kms")) {
    $('#boton_' + i +'_'+ j).fireModal({
        title: 'Ingrese el kilometraje',
        body: $('#modal-kilometros-part').clone(),
        footerClass: 'bg-whitesmoke',
        autofocus: false,
        removeOnDismiss: true,          
        created: function(modal, e, form) {
            proximo_string = data_proximo_km.match(/\d+/g);
            if(data_prox_modif_km != 'null') {
                modal.find('input[name=kilometros]').val(data_prox_modif_km);
            }else {
                modal.find('input[name=kilometros]').val(proximo_string);
            }
            ultimo_string = data_ultimo_mant_km.match(/\d+/g);
        },
        onFormSubmit: function (modal, e, form) {
            e.preventDefault();

            let km_nuevo = modal.find('input[name=kilometros]').val();
            if(parseInt(km_nuevo) < parseInt(ultimo_string)){
                modal.removeClass("modal-progress");
                iziToast.error({
                    title: 'Error: ',
                    message: "El kilometraje debe ser mayor al último mantenimiento.",
                    position: 'topRight',
                    timeout: 15000,
                });
            }else{

                if(parseInt(km_nuevo) < parseInt(proximo_string)) {
                    $('input[name="mantenimiento['+ i +']['+ j +'][estado]"]').val("Adelantada");
                }
                if(parseInt(km_nuevo) > parseInt(proximo_string)){
                    $('input[name="mantenimiento['+ i +']['+ j +'][estado]"]').val("Pospuesta");
                }
                $('input[name="mantenimiento['+ i +']['+ j +'][mantenimiento_modif]"]').val(parseInt(km_nuevo));

                var _token = $("input[name='_token']").val();

                var data = {
                    'mantenimiento_id':  document.getElementById('mantenimiento['+i+']['+j+'][mantenimiento_id]').value,
                    'unidad_id':  document.getElementById('mantenimiento['+i+']['+j+'][unidad_id]').value,
                    'componente_id':  document.getElementById('mantenimiento['+i+']['+j+'][componente_id]').value,
                    'tarea_id':  document.getElementById('mantenimiento['+i+']['+j+'][tarea_id]').value,
                    'ult_mant':  document.getElementById('mantenimiento['+i+']['+j+'][ult_mant]').value,
                    'ult_mant_fecha':  document.getElementById('mantenimiento['+i+']['+j+'][ult_mant_fecha]').value,
                    'frecuencia':  document.getElementById('mantenimiento['+i+']['+j+'][frecuencia]').value,
                    'frecuencia_dias':  document.getElementById('mantenimiento['+i+']['+j+'][frecuencia_dias]').value,
                    'proximo':  document.getElementById('mantenimiento['+i+']['+j+'][proximo]').value,
                    'proximo_fecha':  document.getElementById('mantenimiento['+i+']['+j+'][proximo_fecha]').value,
                    'mantenimiento_modif':  document.getElementById('mantenimiento['+i+']['+j+'][mantenimiento_modif]').value,
                    'mantenimiento_modif_fecha':  document.getElementById('mantenimiento['+i+']['+j+'][mantenimiento_modif_fecha]').value,
                    'estado':  document.getElementById('mantenimiento['+i+']['+j+'][estado]').value,
                }

                $.ajax({
                    type: "PUT",
                    url: HOST + "/api/mantenimiento-rutinario/editarMantenimiento",
                    data: data,
                    dataType: 'json',
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('X-CSRF-Token', _token)
                    },
                    success: function (respuesta) {
                        console.log(respuesta);

                        $('#proximo_modificado_' + i +'_'+ j).html('').append(km_nuevo + ' km');
                        document.getElementById('boton_' + i +'_'+ j).setAttribute('data-prox-modif-km', km_nuevo);
                        if(parseInt(km_nuevo) < parseInt(proximo_string)) {
                            $('#estado_' + i +'_'+ j).html('').append("Adelantada");
                        }
                        if(parseInt(km_nuevo) > parseInt(proximo_string)){
                            $('#estado_' + i +'_'+ j).html('').append("Pospuesta");
                        }

                        iziToast.success({
                            title: 'Éxito: ',
                            message: "El mantenimiento se actualizó correctamente.",
                            position: 'topRight',
                            timeout: 15000,
                        });

                        modal.removeClass("modal-progress")
                        modal.modal("hide");
                    },
                    fail: function () {
                        modal.removeClass("modal-progress");
                        iziToast.error({
                            title: 'Error: ',
                            message: "Se produjo un error al modificar el mantenimiento. Por favor, inténtelo nuevamente.",
                            position: 'topRight',
                            timeout: 15000,
                        });

                    },
                    error: function (jqXHR, textStatus, errorThrow) {
                        console.log(`${textStatus}: ${errorThrow}`)
                    }
                });

                $('input[name="mantenimiento.'+ i +'.'+ j +'.mantenimiento_modif"]').val(km_nuevo);
                modal.removeClass("modal-progress");
                modal.modal("hide");
            }
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
  })
  }
  if (frecuencia.includes("combinado")) {
        $('#boton_' + i +'_'+ j).fireModal({
            title: 'Ingrese el kilometraje y/o fecha',
            body: $('#modal-combinado-part').clone(),
            footerClass: 'bg-whitesmoke',
            autofocus: false,
            removeOnDismiss: true,          
            created: function(modal, e, form) {
                proximo_string = data_proximo_km.match(/\d+/g); //Devuelve como array porque también encuentra la fecha.
                if(data_prox_modif_km != 'null') {
                    modal.find('input[name=kilometros_combinado]').val(data_prox_modif_km);
                }else {
                    modal.find('input[name=kilometros_combinado]').val(proximo_string[0]);
                }
                ultimo_string = data_ultimo_mant_km.match(/\d+/g);
                fecha_proximo = moment(data_proximo_fecha, "DD-MM-YYYY");
                if(data_prox_modif_fecha != 'null') {
                    fecha_modif = moment(data_prox_modif_fecha, "YYYY-MM-DD");
                    modal.find('input[name=fecha_combinado]').val(fecha_modif.format("YYYY-MM-DD"));
                } else {
                    modal.find('input[name=fecha_combinado]').val(fecha_proximo.format("YYYY-MM-DD"));
                }
                fecha_ultimo = moment(data_ultimo_mant_fecha, "DD-MM-YYYY").format("YYYY-MM-DD");
                console.log(proximo_string);
            },
            onFormSubmit: function (modal, e, form) {
                e.preventDefault();

                let km_nuevo = modal.find('input[name=kilometros_combinado]').val();
                let fecha_nueva = modal.find('input[name=fecha_combinado]').val();
                let fecha = moment(fecha_nueva, "YYYY-MM-DD");
                let error = false;
                if(parseInt(km_nuevo) < parseInt(ultimo_string[0])){
                    modal.removeClass("modal-progress");
                    iziToast.error({
                        title: 'Error: ',
                        message: "El kilometraje debe ser mayor al último mantenimiento.",
                        position: 'topRight',
                        timeout: 15000,
                    });
                    error = true;
                }
                if(fecha.isBefore(fecha_ultimo,'day')){
                    modal.removeClass("modal-progress");
                    iziToast.error({
                        title: 'Error: ',
                        message: "La fecha debe ser mayor a la del último mantenimiento.",
                        position: 'topRight',
                        timeout: 15000,
                    });
                    error = true;
                }
                if(parseInt(km_nuevo) == parseInt(proximo_string) && fecha.isSame(fecha_ultimo,'day')) {
                    modal.removeClass("modal-progress");
                    iziToast.info({
                        title: 'Atención: ',
                        message: "La nueva fecha y/o el kilometraje debe ser diferente al actual.",
                        position: 'topRight',
                        timeout: 15000,
                    });
                    error = true;
                }
                /* if((fecha.isBefore(fecha_proximo,'day') && parseInt(km_nuevo) > parseInt(proximo_string[0]))) {
                    modal.removeClass("modal-progress");
                    iziToast.error({
                        title: 'Error: ',
                        message: "Debe posponer o adelantar ambos campos.",
                        position: 'topRight',
                        timeout: 15000,
                    });
                    error = true;
                }
                if((fecha.isAfter(fecha_proximo,'day') && parseInt(km_nuevo) < parseInt(proximo_string[0]))) {
                    modal.removeClass("modal-progress");
                    iziToast.error({
                        title: 'Error: ',
                        message: "Debe posponer o adelantar ambos campos.",
                        position: 'topRight',
                        timeout: 15000,
                    });
                    error = true;
                } */
                if(!error) {

                    //TODO: Esto preguntar qué pasa o cómo queda si adelantan uno y el otro lo posponen. Ahora solo valida con km.
                    if(parseInt(km_nuevo) > parseInt(proximo_string) || fecha.isAfter(fecha_proximo,'day')) {
                        $('input[name="mantenimiento['+ i +']['+ j +'][estado]"]').val("Pospuesta");
                    }
                    if(parseInt(km_nuevo) < parseInt(proximo_string) || fecha.isBefore(fecha_proximo,'day')) {
                        $('input[name="mantenimiento['+ i +']['+ j +'][estado]"]').val("Adelantada");
                    } 
                    $('input[name="mantenimiento['+ i +']['+ j +'][mantenimiento_modif]"]').val(parseInt(km_nuevo));
                    $('input[name="mantenimiento['+ i +']['+ j +'][mantenimiento_modif_fecha]"]').val(fecha);

                    document.getElementById('boton_' + i +'_'+ j).setAttribute('data-prox-modif-km', km_nuevo);
                    document.getElementById('boton_' + i +'_'+ j).setAttribute('data-prox-modif-fecha', fecha.format("YYYY-MM-DD"));

                    var _token = $("input[name='_token']").val();

                    var data = {
                        'mantenimiento_id':  document.getElementById('mantenimiento['+i+']['+j+'][mantenimiento_id]').value,
                        'unidad_id':  document.getElementById('mantenimiento['+i+']['+j+'][unidad_id]').value,
                        'componente_id':  document.getElementById('mantenimiento['+i+']['+j+'][componente_id]').value,
                        'tarea_id':  document.getElementById('mantenimiento['+i+']['+j+'][tarea_id]').value,
                        'ult_mant':  document.getElementById('mantenimiento['+i+']['+j+'][ult_mant]').value,
                        'ult_mant_fecha':  document.getElementById('mantenimiento['+i+']['+j+'][ult_mant_fecha]').value,
                        'frecuencia':  document.getElementById('mantenimiento['+i+']['+j+'][frecuencia]').value,
                        'frecuencia_dias':  document.getElementById('mantenimiento['+i+']['+j+'][frecuencia_dias]').value,
                        'proximo':  document.getElementById('mantenimiento['+i+']['+j+'][proximo]').value,
                        'proximo_fecha':  document.getElementById('mantenimiento['+i+']['+j+'][proximo_fecha]').value,
                        'mantenimiento_modif':  document.getElementById('mantenimiento['+i+']['+j+'][mantenimiento_modif]').value,
                        'mantenimiento_modif_fecha':  document.getElementById('mantenimiento['+i+']['+j+'][mantenimiento_modif_fecha]').value,
                        'estado':  document.getElementById('mantenimiento['+i+']['+j+'][estado]').value,
                    }

                    $.ajax({
                        type: "PUT",
                        url: HOST + "/api/mantenimiento-rutinario/editarMantenimiento",
                        data: data,
                        dataType: 'json',
                        beforeSend: function (xhr) {
                            xhr.setRequestHeader('X-CSRF-Token', _token)
                        },
                        success: function (respuesta) {
                            console.log(respuesta);

                            $('#proximo_modificado_' + i +'_'+ j).html('').append(km_nuevo + ' km / '+ fecha.format("DD-MM-YYYY"));
                            if(parseInt(km_nuevo) > parseInt(proximo_string) || fecha.isAfter(fecha_proximo,'day')){
                                $('#estado_' + i +'_'+ j).html('').append("Pospuesta");
                            }
                            if(parseInt(km_nuevo) < parseInt(proximo_string) || fecha.isBefore(fecha_proximo,'day')) {
                                $('#estado_' + i +'_'+ j).html('').append("Adelantada");
                            }

                            iziToast.success({
                                title: 'Éxito: ',
                                message: "El mantenimiento se actualizó correctamente.",
                                position: 'topRight',
                                timeout: 15000,
                            });

                            modal.removeClass("modal-progress")
                            modal.modal("hide");
                        },
                        fail: function () {
                            modal.removeClass("modal-progress");
                            iziToast.error({
                                title: 'Error: ',
                                message: "Se produjo un error al modificar el mantenimiento. Por favor, inténtelo nuevamente.",
                                position: 'topRight',
                                timeout: 15000,
                            });

                        },
                        error: function (jqXHR, textStatus, errorThrow) {
                            console.log(`${textStatus}: ${errorThrow}`)
                        }
                    });

                    $('input[name="mantenimiento.'+ i +'.'+ j +'.mantenimiento_modif"]').val(km_nuevo);
                    modal.removeClass("modal-progress");
                    modal.modal("hide");
                }
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
    })
  }
}

var index = 1000;

function agregarRow(recursoId , recursoText,unidadText , unidadId , actividadId, cantidad_recurso) {
    //var _token = $("input[name='_token']").val();
    var tabla_listado_recursos = $('#tabla_listado_recursos');

    var rows;
    rows += `<tr class="nuevos-inventario index-${index} tr-padre">`;
    rows += `<td class="id_descripcion" data-nombre="${recursoText}"><p>${recursoText}</p></td>`;
    rows += `<td><input type="number" value="${cantidad_recurso}" required class="form-control cantidad" readonly min="0" name="listado_piezas_recursos[${recursoId}][cantidad]" data-valor="${cantidad_recurso}"></td>`;
    rows += `<td><input type="text" disabled value="${unidadText}" class="form-control"></td>`;
    rows += `<td><span class="controls"><a class="btn btn-primary editar-recurso text-white" title="Editar recurso" data-index="{{ ${index} }}"><i class="fas fa-edit"></i></a></span>
    <span class="controls cancelar-editar-recurso-span" hidden><a class="btn btn-warning cancelar-editar-recurso text-white" title="Cancelar editar recurso" data-index="{{ ${index} }}"><i class="fas fa-times"></i></a></span>
    <span class="controls confirmar-editar-recurso-span" hidden><a class="btn btn-success confirmar-editar-recurso text-white" title="Confirmar editar recurso" data-index="{{ ${index} }}"><i class="fas fa-check"></i></a></span>
    <span class="controls"><a class="btn btn-danger delete-row text-white" data-index="${index}"><i class="fas fa-trash"></i></a></span>
    </td>`;
    rows += `<input type="hidden" class="form-control tarea_id" value="${actividadId}" min="0" name="listado_piezas_recursos[${recursoId}][tarea_id]">`;
    rows += `<input type="hidden" class="form-control unidad_id" value="${unidadId}" min="0" name="listado_piezas_recursos[${recursoId}][unidad_id]">`;
    rows += `<input type="hidden" class="form-control pieza_id" value="${recursoId}" min="0" name="listado_piezas_recursos[${recursoId}][pieza_id]">`;

    tabla_listado_recursos.append(rows);
    
    deleteRow();

    index++;
    
}

function formatearFecha(fecha) {
    console.log("fecha:", fecha);
    var today = new Date(fecha);
    console.log("today:", today);
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
    return today = yyyy+'-'+mm+'-'+dd;
}

function deleteRow() 
{
    /* $('.delete-row').on('click', function (e) {
        let index = $(this).data('index');
        $('.index-'+index).remove();
    }) */
}

$('.boton-replicar').on('click', function (e) {
    $('.div-asociar').show('fast');
})

$('.cancelar-replicar').on('click', function(e) {
    $('.div-asociar').hide('fast');
})

function buscarUnidadesCopiar() {

    var unidad_id = $('#unidad_id_recurso_actividad').val();

    $.ajax({
        url: HOST + "/api/unidades/buscar-unidades-plan/"+unidad_id,
        type: "GET",
        dataType: 'json',
        success: function (data) {
            if(data.estado) {
                console.log(data);

                //$('.div-asociar').show('fast');
                var input = $('.unidades_plan');
                input.empty().trigger('change');
                
                var opcion = new Option(
                    'Seleccione una unidad',
                    '',
                    false,
                    false
                );
                input.append(opcion)
                data.respuesta.forEach(function (da) {
                    var opcion = new Option(
                        da.num_interno,
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

$('#unidad_id_historial').on("change", function (e) {

    var unidad_id = e.target.value;

    $.ajax({
        url: HOST + "/api/mantenimiento-rutinario/show-mantenimiento-historial",
        data: {
            unidad_id
        },
        type: "GET",
        dataType: 'json',
        success: function (data) {
            if (data.length > 0) {
                console.log(data);
                $("#historial").empty();

                $.each(data[0], function (i, item) {

                    if (item.tareas.length) {

                        rows = `<hr><h3 class="text-info">${item.nombre}</h3>`;

                        $.each(item.tareas, function (j, tarea) {
                            if(tarea.mantenimientos.length) {

                                rows += `<h5 class="text-primary">${tarea.descripcion}</h5>`;
                                rows += `<table class="table table-striped"><thead><th>Fecha</th><th>Kilometraje</th><th>Folio OT</th><th>Responsable</th><th>Estado</th></thead><tbody>`;

                                $.each(tarea.mantenimientos, function(k, mantenimiento) {
                                    console.log(mantenimiento.orden_trabajo);

                                    //Esto es para que no se vea el mantenimiento original que se pone al generar la tarea.
                                    /* if(mantenimiento.ult_mantenimiento == null && mantenimiento.orden_trabajo == null) {
                                        return;
                                    } */
                                    if(mantenimiento.ult_mantenimiento) {
                                        var ult_mant = mantenimiento.ult_mantenimiento;
                                    } else {
                                        var ult_mant = '-';
                                    }
                                    if(mantenimiento.ult_mantenimiento_fecha) {
                                        var dateMomentObject = moment(mantenimiento.ult_mantenimiento_fecha, "YYYY-MM-DD"); // 1st argument - string, 2nd argument - format
                                        var ult_mant_fecha = dateMomentObject.format("DD-MM-YYYY"); // convert moment.js object to Date object
                                    } else {
                                        var ult_mant_fecha = '-';
                                    }

                                    if(mantenimiento.orden_trabajo) {
                                        var ot = mantenimiento.orden_trabajo.numeracion +' '+ mantenimiento.orden_trabajo.base_operacion.nombre

                                        if(mantenimiento.orden_trabajo.tareas) {
                                            console.log("tarea");
                                            var responsable = '';
                                            $.each(mantenimiento.orden_trabajo.tareas, function (index, tarea_resp) {
                                                if(tarea_resp.id == tarea.id) {
                                                    if(tarea_resp.pivot.tarea_id == tarea_resp.id) {
                                                        $.each(tarea_resp.personal, function (j, personal) { 
                                                            if(j == 0) {
                                                                responsable += personal.nombre;
                                                            } else {
                                                                responsable += ' // ' + personal.nombre;
                                                            }
                                                        });
                                                    }
                                                }
                                            });
                                        } else {
                                            var responsable = '-';
                                        }
                                    } else {
                                        var ot = ' - ';
                                        var responsable = '-';
                                    }
                                    rows += `<tr>
                                                <td width="20%">${ult_mant_fecha}</td>
                                                <td width="20%">${ult_mant}</td>
                                                <td width="20%">${ot}</td>
                                                <td width="20%">${responsable}</td>
                                                <td width="20%">${mantenimiento.estado}</td>
                                            </tr>`;
                                })
                                rows += `</tbody></table>`;

                            } else {
                                rows += `<h5 class="text-primary">${tarea.descripcion}</h5>`;
                                rows += `<p>No se encontraron mantenimientos para esta tarea</p>`;
                            }

                        })
                    }else{
                        //Si no hay tareas solo el nombre del componente.
                        rows = `<hr><h3 class="text-info">${item.nombre}</h3>`;
                        rows += `<p>No se encontraron tareas.</p>`;
                    }

                    $('#historial').append(rows);

                    //detectButtons(frecuencia, tarea_id);
                })
            } else {
                $("#historial").empty();

                let row = `<p>No se encontraron resultados.</p>`;

                $('#historial').append(row);

                var elements = document.querySelectorAll(".accion");

                for(let i = 0; i < elements.length; i++) {
                    //We add eventListener to each element
                    elements[i].addEventListener("click", lanzarModal());
                }
            }
        },
        error: function (jqXHR, textStatus, errorThrow) {
            console.log(`${textStatus}: ${errorThrow}`)
        }
    })
})
