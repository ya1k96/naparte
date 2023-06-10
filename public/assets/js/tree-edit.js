$(document).ready(function () {
    const prefix = "|-- ",
    prefix_last = "`-- ",
    spacer = "|   ",
    spacer_e = "    ",
        ul_template = $("#template > ul"),
        subcomponent = `<ul>
        <li class="tree-node">
            <input placeholder="Componente" name="fieldName[]" class="form-control fieldName" autocomplete="off"/>
            <span class="controls">
                <a class="btn btn-success mr-1" title="Agregar componente" href="#" data-func="add-sibling"
                    ><i class="fas fa-plus"></i></a
                >
                <a class="btn btn-info mr-1" href="#" title="Agregar sub-componente" data-func="add-child"
                    ><i class="fas fa-indent"></i>
                </a>
                <a class="btn btn-danger" href="#" data-func="delete"
                    ><i class="fas fa-times"></i>
                </a>
            </span>
        </li>
    </ul>`,
        li_template = $("li", subcomponent).first();

        var url = HOST

    const action = {
        "add-sibling": function (obj) {
            var nombre_componente = prompt("Ingrese nombre del componente:"),
                _token = $('input[name="_token"]').val()

            if (nombre_componente != '') {
                $.ajax({
                    url: url + "/admin/crear-componente",
                    data: {
                        padre_id: null,
                        nombre_componente,
                        plan_id
                    },
                    cache: false,
                    type: 'POST',
                    dataType: 'json',
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('X-CSRF-Token', _token);
                    },
                    success: function (resp) {
                        let template = li_template.clone()
                        template.find("input").attr("value", nombre_componente).attr("data-id", resp.id)

                        let appended = template.find("input")[0];
                        appended.setAttribute('size', nombre_componente.length)

                        obj.after(template);
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log(errorThrown);
                    }
                });
            }
        },
        "add-child": function (obj) {
            var nombre_componente = prompt("Ingrese nombre del componente:"),
                _token = $('input[name="_token"]').val();

            if (nombre_componente != '') {
                let padre_id = obj.find("input").data("id")

                $.ajax({
                    url: url + "/admin/crear-componente",
                    data: {
                        padre_id,
                        nombre_componente,
                        plan_id
                    },
                    cache: false,
                    type: 'POST',
                    dataType: 'json',
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('X-CSRF-Token', _token);
                    },
                    success: function (resp) {
                        let template = ul_template.clone()
                        template.find("input").attr("value", nombre_componente).attr("data-id", resp.id)

                        let href = template.find(".subcomponente-task").attr("href")
                        href += "/" + resp.id
                        template.find(".subcomponente-task").attr("href", href)

                        template.find("input").attr('size', nombre_componente.length)

                        obj.append(template);
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log(errorThrown);
                    }
                });
            }
        },
        delete: function (obj) {

            //Valido que haya mínimo un componente si lo que estoy eliminando es un componente

            //TODO: al generar componentes nuevos no se le agrega la class .componente , por lo que no se verifica y no funciona correctamente
            //Lo dejo comentado porque esto genera errores, hay que encontrar la manera de que
            // al agregar un componente nuevo (digamos de primer orden) le agregue la clase .componente pero si es de segundo orden no.

            /* cantidad_componentes = $('.componente').length
            if(cantidad_componentes == 1 && obj.hasClass('componente')) {
                alert("No se puede eliminar. Debe ingresar un componente como mínimo.");
            }else{} */

                //Agarro con un confirm para que no elimine de una.
                if(confirm("Se eliminará el componente. ¿Continuar?")){
                    let componente_id = obj.find("input").data("id"),
                        _token = $('input[name="_token"]').val();

                    $.ajax({
                        url: url + "/admin/eliminar-componente",
                        data: {
                            componente_id,
                        },
                        cache: false,
                        type: 'DELETE',
                        dataType: 'json',
                        beforeSend: function (xhr) {
                            xhr.setRequestHeader('X-CSRF-Token', _token);
                        },
                        success: function (resp) {
                            console.log(resp)
                            obj.remove();
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            console.log(jqXHR, textStatus, errorThrown);
                        }
                    });
                }

        }
    };

    $(document).on("click", "li.tree-node .controls > a", function () {
        action[this.getAttribute("data-func")]($(this).closest("li"));
        rebuild_tree();
        return false;
    });

    // $(document).on("click", "input[name='fieldName']")

    // Inicio: Se establece el ancho del input de acuerdo a la cantidad e caracteres
    let input_componente = document.querySelectorAll(".fieldName")

    for (const el of input_componente) {
        let size = el.value.length + 5

        el.setAttribute('size', size)

        $(".fieldName").on('keyup paste change', function () {
            let size = el.value.length + 5

            el.setAttribute('size', size)
        })
    }
    // Fin: Se establece el ancho del input de acuerdo a la cantidad e caracteres

    function get_subdir_text(obj, pad) {
        let padding = pad || "",
            out = "",
            items = obj.children("li"),
            last = items.length - 1;

        items.each(function (index) {
            const $this = $(this);

            out +=
                padding +
                (index == last ? prefix_last : prefix) +
                $this.children("input").val() +
                "\n";

            const subdirs = $this.children("ul");
            if (subdirs.length) {
                out += get_subdir_text(
                    subdirs,
                    padding + (index == last ? spacer_e : spacer)
                );
            }
        });
        return out;
    }

    function rebuild_tree() {
        $("#out").text($("#p_name").val() + "\n" + get_subdir_text($("#tree")));
    }

    $("#tree").append(li_template.clone());
    $(document).on("keyup", "#tree input", rebuild_tree);
    $("#p_name").on("keyup", rebuild_tree);

    $("#tree")
        .on("mouseover", "li", function (e) {
            $(this).children(".controls").show();
            e.stopPropagation();
        })
        .on("mouseout", "li", function (e) {
            $(this).children(".controls").show();
            e.stopPropagation();
        });

    rebuild_tree();
});
