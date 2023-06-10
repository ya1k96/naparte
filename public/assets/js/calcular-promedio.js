var _data = null;

$("#unidad_id").on('change', function (e) {
    let unidad = $("#unidad_id").val(),
        _token = $("input[name='_token']").val();

    if (unidad != '') {
        $.ajax({
            url: url,
            data: {
                unidad
            },
            cache: false,
            type: 'GET',
            dataType: 'json',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-Token', _token)
            },
            success: function (data) {
                _data = data;
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(errorThrown)
            }
        })
    }
})

$("#submit").on('click', function (e) {
    e.preventDefault();

    let kms = $("#kilometraje").val();
    let data = _data;

    if (data.ultima == null) {
        guardarEnBD();
    } else {
        if (parseInt(kms) < data.ultima.kilometraje) {
            $("#btn-menor").trigger("click")
        } else if (parseInt(kms) > data.porcentaje) {
            $("#btn-mayor").trigger("click")
        } else {
            guardarEnBD();
        }
    }
});

$("#btn-menor").fireModal({
    footerClass: 'bg-whitesmoke',
    title: 'Alerta',
    body: 'Los kilómetros ingresados son menores al último kilometraje registrado en la unidad.<br>Por favor, inténtelo nuevamente.',
    buttons: [
        {
            text: 'Cerrar',
            class: 'btn btn-primary btn-shadow',
            handler: function(modal) {
                $.destroyModal(modal);
            }
        }
    ]
});

$("#btn-mayor").fireModal({
    footerClass: 'bg-whitesmoke',
    title: 'Alerta!',
    body: 'El kilometraje ingresado supera el 30% del promedio de kilómetros cargados en la unidad.<br>¿Desea guardar de igual modo?',
    buttons: [
        {
            text: 'Cancelar',
            class: 'btn btn-secondary btn-shadow',
            handler: function(modal) {
                $.destroyModal(modal);
            }
        },
        {
            text: 'Guardar',
            class: 'btn btn-primary btn-shadow',
            handler: function(modal) {
                guardarEnBD();

                $.destroyModal(modal);
            }
        }
    ]
});

function guardarEnBD() {
    let unidad = $("#unidad_id").val(),
        kms = $("#kilometraje").val(),
        _token = $("input[name='_token']").val();

    $.ajax({
        url: url_guardar_historial,
        data: {
            unidad,
            kms
        },
        cache: false,
        type: 'POST',
        dataType: 'json',
        beforeSend: function (xhr) {
            xhr.setRequestHeader('X-CSRF-Token', _token)
        },
        success: function (data) {
            _data = data;

            iziToast.success({
                title: 'Éxito: ',
                message: "El historial de unidad se agregó correctamente",
                position: 'topRight',
                timeout: 5000,
                onClosing: function () {
                    location.href = url_historial;
                }
            });
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(errorThrown)
        }
    })
}
