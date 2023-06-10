
$(".continuar").on('click', function (e) {
    var boton_continuar = this;

    let id = $(this).data('unidad_id');

    $(`#continuar-modal-${id}`).fireModal({
        title: 'Continuar deshabilitada',
        body: $('#modal-continuar-part').clone(),
        footerClass: 'bg-whitesmoke',
        autofocus: false,
        removeOnDismiss: true,
        onFormSubmit: function(modal, e, form) {
            e.preventDefault();

            let extender = $(this).find('#continuar').val(),
                _token = boton_continuar.dataset.token,
                notification_id = boton_continuar.dataset.notification_id,
                unidad_id = boton_continuar.dataset.unidad_id;
                url = boton_continuar.dataset.url;

            $.ajax({
                url: url,
                type: "POST",
                data: {
                    extender,
                    notification_id,
                    unidad_id
                },
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', _token);
                },
                success: function (data) {
                    modal.removeClass("modal-progress");
                    modal.modal("hide");

                    document.getElementById(`card-${notification_id}`).remove();
                    // location.reload();
                    if (data.estado) {
                        iziToast.success({
                            title: 'Éxito! ',
                            message: data.mensaje,
                            position: 'topRight',
                            timeout: 5000
                        });
                    }else{
                        iziToast.error({
                            title: 'Error: ',
                            message: data.mensaje,
                            position: 'topRight',
                            timeout: 5000
                        });
                    }
                }
            })
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


$(".habilitar").on('click', function (e) {
    var boton_habilitar = this;

    let id = $(this).data('unidad_id');

    $(`#habilitar-modal-${id}`).fireModal({
        title: 'Habilitar unidad',
        body: $('#modal-habilitar-part').clone(),
        footerClass: 'bg-whitesmoke',
        autofocus: false,
        removeOnDismiss: true,
        onFormSubmit: function(modal, e, form) {
            e.preventDefault();

            let kilometraje = $(this).find('#habilitar').val(),
                _token = boton_habilitar.dataset.token,
                notification_id = boton_habilitar.dataset.notification_id,
                unidad_id = boton_habilitar.dataset.unidad_id;
                url = boton_habilitar.dataset.url;

            if (kilometraje != null) {
                $.ajax({
                    url: url,
                    type: "POST",
                    data: {
                        kilometraje,
                        notification_id,
                        unidad_id,
                        validacion: true
                    },
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('X-CSRF-Token', _token);
                    },
                    success: function (data) {
                        modal.removeClass("modal-progress")
                        modal.modal("hide");

                        if (data.estado) {
                            document.getElementById(`card-${notification_id}`).remove();

                            iziToast.success({
                                title: 'Éxito! ',
                                message: data.mensaje,
                                position: 'topRight',
                                timeout: 5000
                            });
                        } else {
                            if (data.mensaje.includes("menores")) {
                                alert(data.mensaje);
                            } else {
                                $confirm = confirm(data.mensaje);

                                if ($confirm) {
                                    $.ajax({
                                        url: url,
                                        data: {
                                            kilometraje,
                                            notification_id,
                                            unidad_id,
                                            validacion: false
                                        },
                                        cache: false,
                                        type: 'POST',
                                        dataType: 'json',
                                        beforeSend: function (xhr) {
                                            xhr.setRequestHeader('X-CSRF-Token', _token);
                                        },
                                        success: function (data) {
                                            if (data.estado) {
                                                document.getElementById(`card-${notification_id}`).remove();

                                                iziToast.success({
                                                    title: 'Éxito! ',
                                                    message: data.mensaje,
                                                    position: 'topRight',
                                                    timeout: 5000
                                                });
                                            }
                                        },
                                        error: function (jqXHR, textStatus, errorThrown) {
                                            //
                                        }
                                    })
                                }
                            }
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        //
                    }
                })
            }
        },
        shown: function(modal, form) {
            console.log("shown", modal, form)
        },
        buttons: [{
            text: 'Habilitar',
            submit: true,
            class: 'btn btn-primary btn-shadow',
            handler: function(modal) {

            }
        }]
    });
})
