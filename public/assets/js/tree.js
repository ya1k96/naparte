$(document).ready(function () {
    const prefix = "|-- ",
        prefix_last = "`-- ",
        spacer = "|   ",
        spacer_e = "    ",
        ul_template = $("#template > ul"),
        subcomponent = `<ul>
        <li class="tree-node">
            <input placeholder="Componente" name="fieldName[]" class="form-control fieldName" />
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

    const action = {
        "add-sibling": function (obj) {
            let template = li_template.clone()
            let componente = obj.find("input").attr("name")
            template.find("input").attr("name", componente)

            obj.after(template);
        },
        "add-child": function (obj) {
            let atributo = obj.find("input").attr("name")
            let template = ul_template.clone()
            let valor = obj.find("input").val()
            atributo = atributo.substring(0, atributo.length - 2)
            template.find("input").attr("name", atributo + "[" + valor + "][]")

            obj.append(template);
        },
        delete: function (obj) {
            obj.remove();
        },
    };

    $(document).on("click", "li.tree-node .controls > a", function () {
        action[this.getAttribute("data-func")]($(this).closest("li"));
        rebuild_tree();
        return false;
    });

    function get_subdir_text(obj, pad) {
        let padding = pad || "",
            out = "",
            items = obj.children("li"),
            last = items.length - 1;

        items.each(function (index) {
            const $this = $(this);
            // console.log("this",$this)

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
        enlarge_input();
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

    // Inicio: se estabalece el ancho de input de acuerdo al texto ingresado
    let nombre_plan = document.querySelector("#nombre_plan")
    let p_name = document.querySelector("#p_name")

    nombre_plan.addEventListener("keypress", event => {
        p_name.setAttribute("size", nombre_plan.value.length)
    })

    // Inicio:
    function enlarge_input() {
        let fieldName = document.querySelectorAll(".fieldName")

        for (const el of fieldName) {
            let size = el.value.length + 5

            el.addEventListener('keypress', event => {
                el.setAttribute('size', size)
            })
        }
    }

    enlarge_input();
    // Fin: se estabalece el ancho de input de acuerdo al texto ingresado

    const tree = document.querySelector('.tree');
    tree.firstElementChild.childNodes[3].lastElementChild.remove()
});
