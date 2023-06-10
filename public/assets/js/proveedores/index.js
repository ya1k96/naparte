$('.submit-proveedor').on('click', function (e) {
    e.preventDefault();
    var cuit = $("#cuit").val();
    if (esCuitValido(cuit)) {
        var _token = $("input[name='_token']").val();
        $.ajax({
            url: HOST+'/api/proveedores/validarCuit/'+cuit,
            type: "GET",
            cache: false,
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-Token', _token)
            },
            success: function (respuesta) {
                if(respuesta.estado) {
                    $('#form-proveedor').trigger("submit");
                } else {
                    if($('#proveedor-id').length > 0 && $('#proveedor-id').val() == respuesta.proveedor.id) {
                        //*Si estoy editando
                        $('#form-proveedor').trigger("submit");
                    } else {
                        iziToast.error({
                            timeout: 50000,
                            position: "topRight",
                            title: "Error :",
                            message:
                                respuesta.mensaje,
                        });
                    }
                }
            },
            error: function (jqXHR, textStatus, errorThrow) {
                console.log(`${textStatus}: ${errorThrow}`)
            }
        })
    } else if (cuit.length < 11) {
        iziToast.error({
            timeout: 50000,
            position: "topRight",
            title: "Error :",
            message:
                "El CUIT debe tener 11 números sin guiones ni espacios.",
        });
    } else {
        iziToast.error({
            timeout: 50000,
            position: "topRight",
            title: "Error :",
            message:
                "El CUIT no es válido.",
        });
    }

});

function esCuitValido(cuit) {
    cuit = cuit.replace(/<br>/g, "\n");
    cuit_tipos = [20, 23, 24, 27, 30, 33, 34];

    if (cuit.length != 11) {
        return false;
    }

    var tipo = cuit.substring(0, 2);

    if (!$.inArray(tipo, cuit_tipos, true)) {
        return false;
    }

    var acumulado = 0;
    var digitos = cuit.split(''); // Convertir en un array
    digito = digitos.pop(); // Extraer último elemento del array

    for ($i = 0; $i < digitos.length; $i++) {
        acumulado += digitos[9 - $i] * (2 + ($i % 6));
    }

    verif = 11 - (acumulado % 11);

    // Si el resultado es 11, el dígito verificador será 0
    // Sino, será el dígito verificador
    verif = verif == 11 ? 0 : verif;
    return (digito == verif);
}