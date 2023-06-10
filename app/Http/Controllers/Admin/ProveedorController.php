<?php

namespace App\Http\Controllers\Admin;

use App\Proveedor;
use App\Rules\CuitValido;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProveedorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $proveedores = Proveedor::withTrashed()->get();

        $arrProveedorActivo = Proveedor::with('ordenes_compra');
        $arrProveedorInactivo = Proveedor::with('ordenes_compra')->onlyTrashed();

        if (!empty($request['buscar'])) {
            $proveedores = Proveedor::withTrashed()
                ->where('id', 'like', '%' . $request['buscar'] . '%')
                ->orWhere('nombre', 'like', '%' . $request['buscar'] . '%')
                ->orWhere('cuit', 'like', '%' . $request['buscar'] . '%')->get();
            $arrProveedorActivo = $arrProveedorActivo->where('nombre', 'like', '%' . $request['buscar'] . '%')
                                    ->orWhere('cuit', 'like', '%' . $request['buscar'] . '%');

            $arrProveedorInactivo = $arrProveedorInactivo->where('nombre', 'like', '%' . $request['buscar'] . '%')
                                    ->orWhere('cuit', 'like', '%' . $request['buscar'] . '%')
                                    ->onlyTrashed();
        }
        $buscar = $request['buscar'] ?? "";

        $arrProveedorActivo = $arrProveedorActivo->get();
        $arrProveedorInactivo = $arrProveedorInactivo->get();

        return view('admin.proveedores.index', ['proveedores' => $proveedores, 'buscar' => $buscar, 'arrProveedorActivo' => $arrProveedorActivo, 'arrProveedorInactivo'=> $arrProveedorInactivo ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => ['required'],
            'cuit' => ['required', 'size:11', 'unique:proveedores,cuit,NULL,id,deleted_at,NULL', new CuitValido]
        ], [
            'nombre' => "El campo nombre es Requerido",
        ]);

        $proveedor = Proveedor::create($request->all());

        if ($proveedor->save()) {
            notify()->success("El proveedor se agregó correctamente", "Éxito: ", "topRight");
            return redirect()->route('admin.proveedor');
        } else {
            notify()->error("Hubo un error al guardar el proveedor. Por favor, inténtalo nuevamente.", "Error: ", "topRight");
            return redirect($this->referer());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Proveedor  $proveedor
     * @return \Illuminate\Http\Response
     */
    public function show(Proveedor $proveedor)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Proveedor  $proveedor
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $proveedor = Proveedor::find($id);

        return view('admin.proveedores.edit', compact('proveedor'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Proveedor  $proveedor
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Proveedor $proveedor)
    {
        $request->validate([
            "nombre" => ["required"],
            "cuit" => ["required", "size:11", "unique:proveedores,cuit,$request->id,id,deleted_at,NULL", new CuitValido]
        ]);

        $proveedor = Proveedor::find($request->id);
        $proveedor->nombre = $request->nombre;
        $proveedor->cuit = $request->cuit;

        if ($proveedor->save()) {
            notify()->success("El proveedor se editó correctamente", "Éxito: ", "topRight");
            return redirect()->route('admin.proveedor');
        } else {
            notify()->error("Hubo un error al editar el proveedor. Por favor, inténtalo nuevamente.", "Error: ", "topRight");
            return redirect($this->referer());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Proveedor  $proveedor
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $proveedor = Proveedor::where('id', $id)
                                ->with(['ordenes_compra' => function($q) {
                                    $q->whereIn('estado', ['abierta', 'aprobada', 'parcial']);
                                }])
                                ->first();

        if(count($proveedor->ordenes_compra) > 0) {
            notify()->error("No puede anularse el proveedor porque tiene órdenes de compra abiertas, aprobadas o parciales.", "Error: ", "topRight");
            return redirect()->back();
        } else {
            if ($proveedor->delete()) {
                notify()->success("El proveedor se anuló correctamente", "Éxito: ", "topRight");
                return redirect()->route('admin.proveedor');
            } else {
                notify()->error("Hubo un error al anular el proveedor. Por favor, inténtalo nuevamente.", "Error: ", "topRight");
                return redirect()->back();
            }
        }
    }

    /**
     * Restore the specified resource from storage.
     *
     * @param  \App\Proveedor  $proveedor
     * @return \Illuminate\Http\Response
     */
    public function restore(Request $request)
    {
        $proveedor = Proveedor::where('id', $request->id)->onlyTrashed()->first();
        $proveedor->deleted_at = null;
        $proveedor->save();

        notify()->success("El Proveedor se editó correctamente", "Éxito:", "topRight");
        return redirect()->route('admin.proveedor');
    }

    /**
     * Función para importar por un csv un listado de proveedores
     */
    public function importar()
    {
        return view('admin.proveedores.importar');
    }

    /**
     * Función que tiene la lógica para guardar proveedores desde el importador
     */
    public function importarStore(Request $request)
    {
        ini_set('max_execution_time', 0);
        ini_set("memory_limit", "-1");

        if ($request->hasFile('archivo')) {
            $mensajes = [];
            $tipos = ['application/vnd.ms-excel', 'text/csv'];

            if (in_array($_FILES['archivo']['type'], $tipos)) {

                if (($gestor = fopen($_FILES['archivo']['tmp_name'], "r")) !== FALSE) {
                    $fila = 0;
                    $data = [];
                    while (($linea = fgetcsv($gestor, 1000, ";")) !== FALSE) {
                        $numero = count($linea);
                        $fila++;
                        if ($fila == 1) continue;
                        if ($numero <= 1) {
                            notify()->error('El archivo no posee un formato valido, corrobore que el delimitador del archivo sea ";" y vuelva a intentarlo', "Error: ", "topRight");
                            return redirect()->route('admin.proveedores.importar');
                        }

                        if ($numero < 2) {
                            notify()->error("A la fila nro. $fila le faltan columnas, por favor verifique la que la cantidad sea la misma que en el ejemplo y vuelva a intentarlo.", "Error: ", "topRight");
                            return redirect()->route('admin.proveedores.importar');
                        }

                        list(
                            $data['Nombre'],
                            $data['CUIT'],
                        ) = $linea;

                        /* =======================
                        * == GUARDANDO EL DATA ==
                        * ======================= */

                        $proveedor = new Proveedor();

                        $output_cuit = str_replace("-", "", $data['CUIT']);
                        $output_cuit = trim($output_cuit);

                        if (!$proveedor->validarCuit($output_cuit)) {
                            $mensajes[$data['CUIT']] = 'El número de CUIT: ' . $data['CUIT'] . ' es inválido.';
                            continue;
                        }

                        if ($proveedor->existeCuit((int)$output_cuit)) {
                            $mensajes[$data['CUIT']] = 'El número de CUIT: ' . $data['CUIT'] . ' ya existe en el sistema.';
                            continue;
                        }

                        $proveedor->nombre = $data['Nombre'];
                        $proveedor->cuit = $output_cuit;
                        if (!$proveedor->save()) {
                            $mensajes[$data['Nombre']] = 'No se pudo guardar el proveedor ' . $data['Nombre'];
                            continue;
                        }
                    }
                    fclose($gestor);
                    unset($gestor);
                }

                if (empty($mensajes)) {
                    notify()->success("La importación se realizó con éxito.", "Éxito: ", "topRight");
                } else {
                    notify()->info("La importación se realizó de forma parcial.", "Éxito: ", "topRight");
                }
            } else {
                notify()->error('El archivo tiene un formato incompatible. Solo se admite ".csv"', "Error: ", "topRight");
                return redirect()->route('admin.proveedores.importar');
            }
        }

        return view('admin.proveedores.importar', compact('mensajes'));
    }

    /**
     * Función para descargar el ejemplo de la plantilla para carga masiva de proveedores
     */
    public function descargarEjemplo()
    {
        return response()->download(public_path('assets/files/tabla_ejemplo_proveedores.csv'));
    }

    /** Validar que un CUIT sea único
     *
     * @return void
     */
    public function validarCuit($cuit)
    {

        //*Si es necesario agregar acá validación de cuit por backend.

        $array = ["estado" => false, "mensaje" => ""];

        $existe_proveedor = Proveedor::where('cuit', $cuit)->get();
        $proveedor = null;

        if (count($existe_proveedor) > 0) {
            $proveedor = $existe_proveedor[0];
            $array['mensaje'] = 'Ya se encuentra registrado un proveedor con ese CUIT.';
        } else {
            $array['estado'] = true;
            $array['mensaje'] = 'No se encontró a ningún proveedor con ese CUIT.';
        }

        return response()->json([
            'estado' => $array["estado"],
            'mensaje' => $array["mensaje"],
            'proveedor' => $proveedor,
        ]);
    }
}
