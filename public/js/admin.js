if (typeof jQuery === "undefined") {
    throw new Error("necesita jQuery");
}

$.Admin = {};
$.Admin.opciones = {
    debug: true,
    izi: {
        timeout: 50000,
        position: 'topRight'
    }
};
if (typeof AdminOpciones !== "undefined") {
    $.extend(true, $.Admin.opciones, AdminOpciones);
}

// var o = $.Admin.opciones;

// Constructor
_iniciar();

// Ejecutar apenas inicia todo


// Ejecutar cuando todo este cargado
$(window).on("load", function () {

    "use strict";

    // Cargar aquí los métodos que quiere que carguen
    // $.Admin.sidebar.iniciar();
    $.Admin.flash.iniciar();
    $.Admin.select2.iniciar();
    $.Admin.summernote.iniciar();
    $.Admin.flatpickr.iniciar(); //Fechas
    $.Admin.password.iniciar();
    $.Admin.gmaps.iniciar();
    $.Admin.Unidades.iniciar();


    /* Resalta de Rojo un error en input */
    if ($(".input").hasClass("error")) {
        $(".error").parent().parent().addClass('has-error');
        $(".error-message").css('color', '#dd4b39');
    }
});

// Ejecutar cuando el documento este listo.
$(document).ready(function () {
    $.Admin.sidebar.iniciar();
});

// Ejecutar en cada scroll
$(document).on('scroll', function (e) {

});

// Ejecutar en resize
$(window).on('resize', function (e) {

});

//
function _iniciar() {

    'use strict';

    /**
     * Mensajes FLASH
     * Método para manejar los mensajes
     * Cualquier duda ver la doc: https://izitoast.marcelodolza.com/
     */
    $.Admin.flash = {
        opciones: {
            timeout: 50000,
            position: 'topRight',
            message: ""
        },
        iniciar: function () {
            var _this = this;
        },
        info: function (msj) {
            var _this = this;
            if (msj) {
                _this.opciones.message = msj,
                iziToast.info(_this.opciones);
            }
        },
        error: function (msj) {
            var _this = this;
            if (msj) {
                _this.opciones.message = msj,
                iziToast.error(_this.opciones);
            }
        },
    };

    /**
     * Método para manejar los datetimepicker del desplegable
     */
    $.Admin.flatpickr = {
        opciones: {
            dateFormat: "d/m/Y",
            // locale: "es",  // TODO: VER SI SE PUEDE USAR DIRECTAMENTE ESTO
            locale: {
                firstDayOfWeek: 1,
                weekdays: {
                    shorthand: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
                    longhand: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
                },
                months: {
                    shorthand: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Оct', 'Nov', 'Dic'],
                    longhand: ['Enero', 'Febreo', 'Мarzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                },
            },
            // wrap: true, // Boton de agenda y de limpiar campo
            inline: false, // TRUE: Visualizar el calendario en un estado siempre abierto.
        },
        iniciar: function () {
            var _this =  this;

            if ($('.fechahora').length) {
                _this.opciones.dateFormat = "d-m-Y H:i";
                _this.opciones.enableTime = true;
                $('.fechahora').flatpickr(_this.opciones);
                /* .keydown(function (e) {
                    return false;
                }); */

            }

            if ($('.fechas').length) {
                _this.opciones.dateFormat = "d-m-Y";
                $('.fechas').flatpickr(_this.opciones);
                /* .keydown(function (e) {
                    return false;
                }); */
            }

            if ($('.horas').length) {
                _this.opciones.dateFormat = "H:i";
                _this.opciones.enableTime = true;
                _this.opciones.noCalendar = true;
                // _this.opciones.time_24hr = true; //Formato 24hs
                // _this.opciones.defaultDate = "13:45"; //Hora por defecto
                $('.horas').flatpickr(_this.opciones);
                /* .keydown(function (e) {
                    return false;
                }); */

            }
        },
        reiniciar: function () {
            if ($('[data-toggle="datetimepicker"]').length) {
                var _this = this;
                $('[data-toggle="datetimepicker"]').datetimepicker('destroy');

                _this.iniciar();
            }
        }
    };


    $.Admin.gmaps = {
        input: null,
        autocomplete: null,
        geocoder: null,
        boton: null,
        event: null,
        marker: null,
        map: null,
        iniciar: function () {
            var _this = this;

            if ($('#ubicacion-gmaps').length) {
                _this.input = /** @type {!HTMLInputElement} */(document.getElementById('ubicacion-gmaps'));
                this.autocompletado();
            }
            if ($('#mapa').length) {
                _this.input = /** @type {!HTMLInputElement} */(document.getElementById('ubicacion-gmaps'));
                _this.div = /** @type {!HTMLInputElement} */(document.getElementById('mapa'));
                this.mapa();
            }
        },
        autocompletado: function () {
            try {
                var _this = this;
                if(!_this.input){
                    throw new Error("No se encontró el input para la función de autocompletado.");
                }

                _this.autocomplete = new google.maps.places.Autocomplete(_this.input, { types: ["geocode"] });

                //Evite pagar por datos que no necesita al restringir el conjunto de campos de lugar que se devuelven solo a los componentes de la dirección
                //Revisar: https://developers.google.com/maps/documentation/javascript/reference/places-service#PlaceResult
                // _this.autocomplete.setFields(["address_component", "geometry"]);
                _this.autocomplete.setFields(["geometry"]);

                _this.autocomplete.addListener('place_changed', function () {
                    setTimeout(() => {
                        let place = _this.autocomplete.getPlace();

                        if (place.geometry) {
                            $('#lat').val(place.geometry.location.lat());
                            $('#lng').val(place.geometry.location.lng());
                        }

                        if (place.address_components) {
                            var address_components = place.address_components;

                            var components = {};
                            jQuery.each(address_components, function(k,v1) {
                                jQuery.each(v1.types, function(k2, v2){
                                    components[v2]=v1.long_name
                                });
                            });

                            $('#street_name').val(components.route);
                            $('#street_number').val(components.street_number);
                            $('#country').val(components.country);
                            $('#province').val(components.administrative_area_level_1);
                            if (components.locality) {
                                $('#city').val(components.locality);
                            }else if(components.sublocality){
                                $('#city').val(components.sublocality);
                            }else{
                                $('#city').val(components.administrative_area_level_2);
                            }
                            $('#zipcode').val(components.postal_code);
                        }
                    }, 600);

                });

            } catch (error) {
                console.log(error);
            }
        },
        mapa: function () {
            try {
                var _this = this;

                if(!_this.input){
                    throw new Error("No se encontró el input para la función de autocompletado.");
                }
                if(!_this.div){
                    throw new Error("No se encontró el div para el renderizado del mapa.");
                }

                if (_this.input.value != "") {
                    _this.event = new Event('change');
                    _this.input.dispatchEvent(_this.event);
                }

                let lat = -32.9596572;
                let lon = -60.646229;

                if ($('#lat').val() && $('#lng').val()) {
                    lat = $('#lat').val();
                    lon = $('#lng').val();
                }

                var latLng = new google.maps.LatLng(lat, lon);

                $('#lat').val(lat);
                $('#lng').val(lon);

                //FIXME: chequear que exista el div con ID "map"
                _this.map = new google.maps.Map(_this.div, {
                    zoom: 15,
                    center: latLng
                });

                _this.marker = new google.maps.Marker({
                    position: latLng,
                    title: 'Dirección',
                    map: _this.map,
                    draggable: true
                });

                _this.geocoder = new google.maps.Geocoder;

                google.maps.event.addListener(_this.marker, 'drag', function() {
                    $('#lat').val(_this.marker.getPosition().lat());
                    $('#lng').val(_this.marker.getPosition().lng());
                });

                google.maps.event.addListener(_this.marker, 'dragend', function() {
                    var latlng = {lat: parseFloat(_this.marker.getPosition().lat()), lng: parseFloat(_this.marker.getPosition().lng())};
                    _this.geocoder.geocode({'location': latlng}, function(results, status) {
                        if (status === 'OK') {
                            if (results[1]) {
                                var address_components = results[0].address_components;
                                var components={};
                                jQuery.each(address_components, function(k,v1) {jQuery.each(v1.types, function(k2, v2){components[v2]=v1.long_name});});

                                $('#street_name').val(components.route);
                                $('#street_number').val(components.street_number);
                                $('#country').val(components.country);
                                $('#province').val(components.administrative_area_level_1);
                                if (components.locality) {
                                    $('#city').val(components.locality);
                                }else if(components.sublocality){
                                    $('#city').val(components.sublocality);
                                }else{
                                    $('#city').val(components.administrative_area_level_2);
                                }
                                $('#zipcode').val(components.postal_code);
                                $('#lat').val(results[0].geometry.location.lat());
                                $('#lng').val(results[0].geometry.location.lng());

                                $('#title').val(components.route + ' ' + _this.redondear(components.street_number));

                                /* $.Profesionales.loader.mostrar();
                                $.Profesionales.loader.ocultar(); */

                            } else {
                                _d('No results found');
                            }
                        } else {
                            _d('Geocoder failed due to: ' + status);
                        }
                    });
                });

                _this.autocomplete = new google.maps.places.Autocomplete(_this.input);
                _this.autocomplete.bindTo('bounds', _this.map);
                _this.autocomplete.addListener('place_changed', function () {

                    _this.marker.setVisible(false);
                    var place = _this.autocomplete.getPlace();
                    if (!place.geometry) {
                        _d('No existe lugar para esa búsqueda');
                        return;
                    }

                    if (place.geometry.viewport) {
                        _this.map.fitBounds(place.geometry.viewport);
                    } else {
                        _this.map.setCenter(place.geometry.location);
                        _this.map.setZoom(17);
                    }

                    _this.marker.setIcon(({
                        url: place.icon,
                        size: new google.maps.Size(71, 71),
                        origin: new google.maps.Point(0, 0),
                        anchor: new google.maps.Point(17, 34),
                        scaledSize: new google.maps.Size(35, 35)
                    }));
                    _this.marker.setPosition(place.geometry.location);
                    _this.marker.setVisible(true);

                    setTimeout(() => {
                        let place = _this.autocomplete.getPlace();

                        if (place.geometry) {
                            $('#lat').val(place.geometry.location.lat());
                            $('#lng').val(place.geometry.location.lng());
                        }

                        if (place.address_components) {
                            var address_components = place.address_components;

                            var components = {};
                            jQuery.each(address_components, function(k,v1) {
                                jQuery.each(v1.types, function(k2, v2){
                                    components[v2]=v1.long_name
                                });
                            });

                            $('#street_name').val(components.route);
                            $('#street_number').val(components.street_number);
                            $('#country').val(components.country);
                            $('#province').val(components.administrative_area_level_1);
                            if (components.locality) {
                                $('#city').val(components.locality);
                            }else if(components.sublocality){
                                $('#city').val(components.sublocality);
                            }else{
                                $('#city').val(components.administrative_area_level_2);
                            }
                            $('#zipcode').val(components.postal_code);
                        }
                    }, 600);

                });
            } catch (error) {
                console.log(error);
            }

        },
        mapa_estatico: function () {
            try {
                var _this = this;
                if(!_this.div){
                    throw new Error("No se encontró el div para el renderizado del mapa.");
                }
                if (!parseFloat(_this.div.dataset.lat)) {
                    throw new Error("La latitud esta mal");
                }
                if (!parseFloat(_this.div.dataset.lng)) {
                    throw new Error("La longitud esta mal");
                }

                let title = _this.div.dataset.title || "Dirección";

                const myLatLng = {lat: parseFloat(_this.div.dataset.lat), lng: parseFloat(_this.div.dataset.lng)};

                const map = new google.maps.Map(_this.div, {
                    zoom: 15,
                    center: myLatLng,
                });

                const contentString = "<p>"+title+"</p>"
                const infowindow = new google.maps.InfoWindow({
                    content: contentString,
                });

                const marker = new google.maps.Marker({
                    position: myLatLng,
                    map,
                    title: title,
                });
                marker.addListener("click", () => {
                    infowindow.open(map, marker);
                });


            } catch (error) {
                console.log(error);
            }
        }
    }

    /**
     * SELECT2
     * Método para configurar el select2
     * Cualquier duda consultar la doc: https://select2.org/
     */
    $.Admin.select2 = {
        elementos: [],
        historico: false,
        opciones: {
            language: "es",
            placeholder: "",
            allowClear: true,
            dropdownParent: null,
            miembro_email: "",
        },
        iniciar: function () {
            var _this = this;

            if ($(".select2").length) {
                $('.select2').each(function (i, e) {
                    let elemJquery = $(e);
                    let opciones = Object.create(_this.opciones);

                    if (elemJquery.hasClass('select2-usuarios')) {
                        var rol = elemJquery.data('rol');
                        opciones.ajax = {
                            url: HOST + "admin/api/getUsuarios",
                            dataType: 'json',
                            delay: 250,
                            data: function (params) {
                                return {
                                    rol: rol || '',
                                    search: params.term,
                                    page: params.page || 1,
                                };
                            },
                            processResults: function (data, params) {
                                params.page = params.page || 1;

                                return {
                                    results: data.items,
                                    pagination: {
                                        more: (params.page * 25) < data.total_count
                                    }
                                };
                            },
                            cache: true
                        };
                        opciones.escapeMarkup = function (markup) {
                            return markup;
                        };
                        opciones.minimumInputLength = 3;
                        opciones.templateResult = _this.formatResultado;
                        opciones.templateSelection = _this.formatResultadoSelection;
                    }

                    if (elemJquery.data('dropdownparent')) {
                        opciones.dropdownParent = $(elemJquery.data('dropdownparent'));
                    }
                    if (elemJquery.data('placeholder')) {
                        opciones.placeholder = elemJquery.data('placeholder');
                    }
                    _this.elementos.push(e);
                    elemJquery.select2(opciones);
                });

            }
        },
        reiniciar: function () {
            var _this = this;
            if ($(".select2").length) {
                _this.elementos.forEach(function (e) {
                    $(e).select2("destroy"),
                        $(e).val(null),
                         // $(e).select2(_this.opciones);
                        _this.iniciar();
                });
            }
         },
        formatResultadoSelection: function (resultado) {
            return resultado.text || "Selecciona una opción";
        },
        formatResultado: function (resultado) {
            if (resultado.loading) return resultado.text; //return "Buscando...";

            return '<span>' + resultado.text + '</span>';

            /* var render = '<div class="consulta" style="line-height: 2px;"><h4 style="font-size: 1rem;font-weight: 700;">' + resultado.text + '</h4>';
            render += "</div>";
            return render; */

        }
    };

    /* SUMMERNOTE - Editor de texto */
    $.Admin.summernote = {
        iniciar: function () {

            if ($(".summernote-disabled").length) {
                $('.summernote-disabled').summernote('disable');
            }
        }
    };

    $.Admin.sidebar = {
        iniciar: function () {
            var opciones = $.Admin.opciones;
            opciones.controlAccion = opciones.controlador+opciones.accion;

            var opcion = $(".sidebar-menu").find("."+opciones.controlAccion);

            if(opcion.length) {
                opcion.addClass('active');

                this._padres(opcion);

            } else {
                var opcion = $(".sidebar-menu").find("."+opciones.controlador);
                if(opcion.length) {
                    opcion.addClass('active');

                    this._padres(opcion);
                }
            }
        },
        _padres: function(opcion){
            let padres = opcion.parents('ul');
            if(padres.length) {
                padres.each((index,element) => {
                    if($(element).hasClass('dropdown-menu')) {
                        $(element).css('display', 'block');
                    }
                });
            }

            let grupos = opcion.parents("li");
            if(grupos.length){
                grupos.each((index, element) => {
                    if($(element).hasClass('dropdown')){
                        $(element).addClass('active');
                    }
                });
            }
        }
    };

    $.Admin.password = {
        selector: $(".pwstrength"),
        iniciar: function () {
            var _this = this;

            if (_this.selector.length) {
                _this.selector.pwstrength({
                    texts: ['muy débil', 'débil', 'mediocre', 'fuerte', 'muy fuerte']
                });
            }
        }
    }

    $.Admin.Unidades = {
        iniciar: function () {
            var _this = this;
            $('#marca_id').on('change', function () {
                _this.buscarModelos();
            })
            $('#num_interno').on('change', function () {
                var num_interno = $('#num_interno').val();
                var maxLength = 5; // maxLength is the max string length, not max # of fills
                var res = num_interno.padStart(maxLength, "0");  //Le indicas la long maxima que debe llegar y con qué completar adelante.
                $('#num_interno').val(res);
            })
        },
        buscarModelos: function() {
            const _this = this;

            if ($("#marca_id").length) {
                    console.log("marca id:", $("#marca_id").val());
                    var id = $("#marca_id").val();
                    let _token = $('input[name="_token"]').val();

                    $.ajax({
                        type: 'GET',
                        url: HOST + '/api/marcas/' + id,
                        data: {

                        },
                        dataType: "json",
                        beforeSend: function (xhr) {
                            xhr.setRequestHeader('X-CSRF-Token', _token);
                        },
                        success: (resp) => {
                            console.log("modelos:", resp);
                            if(resp) {
                                $("#modelo_id option[value]").each(function() {
                                    $(this).remove();
                                });
                                $('#modelo_id').append($('<option>', {
                                    value: '',
                                    text : "Seleccione un contacto"
                                }));
                                $.each(resp, function (i, modelo) {
                                    $('#modelo_id').append($('<option>', {
                                        value: modelo.id,
                                        text : modelo.nombre
                                    }));
                                });
                            }
                        },
                        error: (resp => {
                            console.log("Hay error");
                        })
                    })
            }
        }
    }
};

// Funcion para loguear
function _d(d, v) {
    if ($.Admin.opciones.debug) {
        m = d;
        if (v || v === 0 || v === false) {
            m = m + " : " + v;
        }
        console.log(m);
    }
}



function verImagen(url) {
    var w = window.open();
    w.document.open();
    w.document.write('<img src="' + url + '">');
    w.document.close();
    // w.onload = function() { w.print(); w.close(); };
}



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


function validarForm(form) {
    if(form){
        for (var value of form) {
            let input = $(value);
            if (input.attr('required')) {
                if (!input.attr('disabled')) {
                    if (!input.val()) {
                        let title = input.attr('title');
                        $.Master.flash.error("Debe completar el campo "+title);
                        input.focus();
                        return false;
                    }
                }
            }
        }
        return true;
    }
    return false;
}


function copiar(texto) {
    if (document.queryCommandSupported('copy')) {

        // Crea un campo de texto "oculto"
        var aux = document.createElement("input");

        // Asigna el contenido del elemento especificado al valor del campo
        // aux.setAttribute("value", document.getElementById(id_elemento).innerHTML);
        aux.setAttribute("value", texto);

        // Añade el campo a la página
        document.body.appendChild(aux);

        // Selecciona el contenido del campo
        aux.select();

        // Copia el texto seleccionado
        document.execCommand("copy");

        // Elimina el campo de la página
        document.body.removeChild(aux);

        mensaje('Copiado!.');
    }else{
        alert("Su navegador no tiene soporte para copiar.");
    }
}


function mensaje(texto){
    iziToast.info({
        title: 'Mensaje del sistema',
        message: texto
    });
}
