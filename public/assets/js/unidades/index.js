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

// Begin: Deshabilitar unidades
$(".button-disabled").on('click', function (e) {
    let id = $(this).data('id');
    var button_disabled = this;

    $(`#deshabilitar-modal-${id}`).fireModal({
        title: 'Deshabilitar unidad',
        body: $('#modal-deshabilitar-part').clone(),
        footerClass: 'bg-whitesmoke',
        autofocus: false,
        removeOnDismiss: false,
        onFormSubmit: function(modal, e, form) {
            e.preventDefault();

            let dias = $(this).find('#deshabilitar').val(),
                _token = button_disabled.dataset.token,
                id = button_disabled.dataset.id,
                enabled_box = document.querySelector(`#enabled-${id}`),
                disabled_box = document.querySelector(`#disabled-${id}`);
            if (!isEmpty(dias) && id && enabled_box && disabled_box && _token) {
                $.ajax({
                    url: button_disabled.dataset.route,
                    data: {
                        id,
                        dias,
                        _method: "delete",
                        type: "isAJAX"
                    },
                    cache: false,
                    type: 'POST',
                    dataType: 'json',
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('X-CSRF-Token', _token)
                    },
                    success: function (resp) {
                        modal.removeClass("modal-progress")
                        modal.modal("hide");

                        if (resp.estado) {
                            enabled_box.style.display = "none";
                            disabled_box.style.display = "initial";

                            iziToast.success({
                                title: 'Éxito: ',
                                message: resp.mensaje,
                                position: 'topRight',
                                timeout: 5000,
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
                })
            }
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


$(".button-enabled").on('click', function (e) {
    let id = $(this).data('id');
    var button_enabled = this;

    $(`#habilitar-modal-${id}`).fireModal({
        title: 'Habilitar unidad',
        body: $('#modal-habilitar-part').clone(),
        footerClass: 'bg-whitesmoke',
        autofocus: false,
        removeOnDismiss: true,
        onFormSubmit: function(modal, e, form) {
            e.preventDefault();

            let _token = button_enabled.dataset.token,
                id = button_enabled.dataset.id,
                enabled_box = document.querySelector(`#enabled-${id}`),
                disabled_box = document.querySelector(`#disabled-${id}`),
                kilometraje = $(this).find('#habilitar').val();

            if (kilometraje != null)
            {
                $.ajax({
                    url: button_enabled.dataset.route,
                    data: {
                        id,
                        kilometraje,
                        validacion: true
                    },
                    cache: false,
                    type: 'GET',
                    dataType: 'json',
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('X-CSRF-Token', _token)
                    },
                    success: function (resp) {
                        modal.removeClass("modal-progress")
                        modal.modal("hide");

                        if (resp[0].estado) {
                            enabled_box.style.display = "initial";
                            disabled_box.style.display = "none";

                            iziToast.success({
                                title: 'Éxito: ',
                                message: resp[0].mensaje,
                                position: 'topRight',
                                timeout: 5000,
                            });
                        } else {
                            if (resp[0].mensaje.includes("menores")) {
                                alert(resp[0].mensaje);
                            } else {

                                $confirm = confirm(resp[0].mensaje);

                                if ($confirm) {
                                    $.ajax({
                                        url: button_enabled.dataset.route,
                                        data: {
                                            id,
                                            kilometraje,
                                            validacion: false
                                        },
                                        cache: false,
                                        type: 'GET',
                                        dataType: 'json',
                                        beforeSend: function (xhr) {
                                            xhr.setRequestHeader('X-CSRF-Token', _token)
                                        },
                                        success: function (resp) {
                                            if (resp[0].estado) {
                                                enabled_box.style.display = "initial";
                                                disabled_box.style.display = "none";

                                                iziToast.success({
                                                    title: 'Éxito: ',
                                                    message: resp[0].mensaje,
                                                    position: 'topRight',
                                                    timeout: 5000,
                                                });
                                            }
                                        },
                                        error: function (jqXHR, textStatus, errorThrown) {
                                            //
                                        }
                                    });
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
            text: 'Continuar',
            submit: true,
            class: 'btn btn-primary btn-shadow',
            handler: function(modal) {
            }
        }]
    });
})
