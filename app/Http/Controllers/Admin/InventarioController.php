<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Inventario;
use App\Exports\InventarioExport;
use App\Pieza;
use App\BaseOperacion;
use App\Movimiento;
use App\Http\Controllers\Controller;
use DateTime;
use Carbon\Carbon;

class InventarioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $inventarios = Inventario::withTrashed()
        ->with(['piezas', 'base_operacion', 'movimientos']);

        $bases_operaciones = BaseOperacion::all();
        $piezas = Pieza::all();

        if (!empty($request['buscar'])){
            //Filtra por ubicacion
            $inventarios = $inventarios->where('ubicacion', 'LIKE', '%'.$request['buscar'].'%');
        }

        if (!empty($request['base_operacion'])){
            $inventarios = $inventarios->whereHas('base_operacion', function ($q) use($request) {
                return $q->where('bases_operacion_id', $request['base_operacion']);
                });
        }

        if (!empty($request['pieza'])){
            $inventarios = $inventarios->whereHas('piezas', function ($q) use($request) {
                return $q->where('pieza_id', $request['pieza']);
                });
        }

        if (!empty($request['fecha_hasta'])){
            $inventarios = $inventarios->with(['movimientos' => function ($q) use($request) {
                return $q->whereDate('fecha', '<=', $request['fecha_hasta']);
                }]);
        }

        $buscar = $request['buscar'] ?? "";
        $buscar_base_operacion = $request['base_operacion'] ?? "";
        $buscar_pieza = $request['pieza'] ?? "";
        $buscar_fecha_hasta = $request['fecha_hasta'] ?? "";

        // sort asc or dsc table in view 'inventario.index' by 'ubicacion'. default:asc
        if (request('order') && request('direction')) {
            $inventarios = $inventarios->orderBy(request('order'), request('direction'))->paginate(10);
        }else {
            $inventarios = $inventarios->orderBy('ubicacion','asc')->paginate(10);
        }
        return view('admin.inventario.index', compact('inventarios', 'bases_operaciones', 'piezas', 'buscar', 'buscar_base_operacion', 'buscar_pieza', 'buscar_fecha_hasta'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $bases_operacion_id = BaseOperacion::all();
        return view('admin.inventario.create', compact(['bases_operacion_id']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!isset($request['bases_operacion_id'])) {
            notify()->error("El campo Base de operación es requerido.","Error: ","topRight");
            return redirect($this->referer());
        }

        $errores = [];
        foreach ($request->listado_piezas as $piezas_validacion) {

            // Hago este tipo de validación por cuestion de tiempo y porque la función
            // de validate no me funciona si no es un objeto request

            if (!isset($piezas_validacion['pieza_id'])) {
                $errores[] = "El campo es Pieza requerido.";
                continue;
            }

            $pieza_catalogo = Pieza::where('id', $piezas_validacion['pieza_id'])->first();
            if($pieza_catalogo->validarExisteInventario($request['bases_operacion_id'])) {
                $errores[] = "La pieza ".$pieza_catalogo->nro_pieza." - ".$pieza_catalogo->descripcion." ya existe en el inventario para la base de operación seleccionada. No se agregó al inventario.";
                continue;
            }

            if (!isset($piezas_validacion['stock'])) {
                $errores[] = "El campo es Stock requerido.";
                continue;
            }
            if (!isset($piezas_validacion['precio'])) {
                $errores[] = "El campo es Precio requerido.";
                continue;
            }
            if (!isset($piezas_validacion['ubicacion'])) {
                $errores[] = "El campo Ubicación es requerido.";
                continue;
            }

            $pieza_inv = new Inventario();
            $pieza_inv->bases_operacion_id = $request['bases_operacion_id'];
            $pieza_inv->pieza_id = $piezas_validacion['pieza_id'];
            if (isset($piezas_validacion['compra_unica'])) {
                $pieza_inv->compra_unica = $piezas_validacion['compra_unica'];
            }
            $pieza_inv->stock = 0;
            $pieza_inv->precio = $piezas_validacion['precio'];
            $pieza_inv->ubicacion = $piezas_validacion['ubicacion'];
            if (isset($piezas_validacion['maximo_compra'])) {
                $pieza_inv->maximo_compra = $piezas_validacion['maximo_compra'];
            }if (isset($piezas_validacion['minimo_compra'])) {
                $pieza_inv->minimo_compra = $piezas_validacion['minimo_compra'];
            }

            if(!$pieza_inv->save()) {
                notify()->error("Hubo un error al guardar el inventario. Por favor, inténtalo nuevamente.","Error: ","topRight");
                return redirect()->route('admin.inventario');
            }

            Movimiento::create([
                'pieza_id' => $pieza_inv->pieza_id,
                'base_operacion_id' => $pieza_inv->bases_operacion_id,
                'inventario_id' => $pieza_inv->id,
                'fecha' => new DateTime(),
                'cantidad' => $piezas_validacion['stock'],
                'precio_unitario' => $pieza_inv->precio,
                'ubicacion' => $pieza_inv->ubicacion,
                'user_id' => auth()->user()->id,
              ]);

        }
        if(!empty($errores)) {
            foreach($errores as $error) {
                notify()->error($error,"Error: ","topRight");
                return redirect($this->referer());
            }
        }
        return redirect()->route('admin.inventario');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $inventario = Inventario::with(['piezas' , 'base_operacion'])
            ->where('id' , $id)
            ->first();
        return view('admin.inventario.show' , compact('inventario'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Función para importar por un csv un listado de piezas
    */
    public function importar()
    {
        $bases_operacion = BaseOperacion::pluck('nombre', 'id');
        return view('admin.inventario.importar', compact('bases_operacion'));
    }

    /**
     * Función que tiene la lógica para almacenar piezas desde el importador
    */
    public function importarStore(Request $request)
    {
        $bases_operacion = BaseOperacion::pluck('nombre', 'id');
        $listado_piezas = Pieza::pluck('nro_pieza', 'id')->all();
        $listado_inventario = Inventario::where('bases_operacion_id', $request['bases_operacion_id'])->with('piezas')->pluck('pieza_id', 'id')->all();

        ini_set('max_execution_time', 0);
        ini_set("memory_limit", "-1");

        if ($request->hasFile('archivo')) {
            $mensajes = [];
            $tipos = ['application/vnd.ms-excel', 'text/csv'];

            if(in_array($_FILES['archivo']['type'], $tipos)){

                if (($gestor = fopen($_FILES['archivo']['tmp_name'], "r")) !== FALSE) {
                    $fila = 0;
                    $data = [];
                    while (($linea = fgetcsv($gestor, 1000, ";")) !== FALSE) {
                        $numero = count($linea);
                        $fila++;
                        if ($fila == 1) continue;
                        if ($numero <= 1) {
                            notify()->error('El archivo no posee un formato valido, corrobore que el delimitador del archivo sea ";" y vuelva a intentarlo',"Error: ","topRight");
                            return redirect()->route('admin.inventario.importar');
                        }

                        if ($numero < 7) {
                            notify()->error("A la fila nro. $fila le faltan columnas, por favor verifique la que la cantidad sea la misma que en el ejemplo y vuelva a intentarlo.","Error: ","topRight");
                            return redirect()->route('admin.inventario.importar');
                        }

                        list(
                            $data['No. de Parte'],
                        ) = $linea;

                        if (!in_array($data['No. de Parte'], $listado_piezas)) {
                            $mensajes[$data['No. de Parte']] = 'No existe la pieza'.' '.$data['No. de Parte'];
                            continue;
                        }

                        if (!empty($listado_inventario)) {
                            $k = array_search($data['No. de Parte'], $listado_piezas);
                            if (in_array($k, $listado_inventario)) {
                                $mensajes[$data['No. de Parte']] = 'Ya existe en el inventario la pieza'.' '.$data['No. de Parte'];
                                continue;
                            }
                        }

                        list(
                            $data['No. de Parte'],
                            $data['Descripción'],
                            $data['Unidad'],
                            $data['Cantidad'],
                            $data['C. Unitario'],
                            $data['Monto'],
                            $data['Localización'],
                        ) = $linea;

                        /* =======================
                        * == GUARDANDO EL DATA ==
                        * ======================= */

                        $output = str_replace(".","",$data['C. Unitario']);
                        $output = str_replace(",",".",$output);
                        $output = doubleval(preg_replace("/[^-0-9\.]/","",$output));

                        $inventario_pieza = new Inventario();
                        $inventario_pieza->bases_operacion_id = $request['bases_operacion_id'];
                        $inventario_pieza->pieza_id = array_search($data['No. de Parte'], $listado_piezas);
                        $inventario_pieza->stock = 0;
                        $inventario_pieza->precio  = $output;
                        $inventario_pieza->ubicacion  = utf8_encode($data['Localización']);

                        if(!$inventario_pieza->save()) {
                            $mensajes[$inventario_pieza->pieza_id] = 'No se pudo guardar la pieza '.$inventario_pieza->pieza_id;
                            continue;
                        }

                        //* Nuevo movimiento para el inventario
                        Movimiento::create([
                            'pieza_id' => $inventario_pieza->pieza_id,
                            'base_operacion_id' => $inventario_pieza->bases_operacion_id,
                            'inventario_id' => $inventario_pieza->id,
                            'fecha' => new DateTime(),
                            'cantidad' => $data['Cantidad'],
                            'precio_unitario' => $inventario_pieza->precio,
                            'ubicacion' => $inventario_pieza->ubicacion,
                            'user_id' => auth()->user()->id,
                          ]);
                    }
                    fclose($gestor);
                    unset($gestor);
                }

                if(empty($mensajes)){
                    notify()->success("La importación se realizó con éxito.","Éxito: ","topRight");
                }else{
                    notify()->info("La importación se realizó de forma parcial.","Éxito: ","topRight");
                }

            }else{
                notify()->error('El archivo tiene un formato incompatible. Solo se admite ".csv"',"Error: ","topRight");
                return redirect()->route('admin.inventario.importar');
            }
        }

        return view('admin.inventario.importar', compact('mensajes', 'bases_operacion'));
    }

    /**
     * Función para descargar el ejemplo de la plantilla para carga masiva de inventario
    */
    public function descargarEjemplo() {
        return response()->download(public_path('assets/files/tabla_ejemplo.csv'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Muestra por base de operación, las piezas requeridas, en pedidos y a comprar.
     * Se calcula según stock, máximos de compra, OC pendientes, etc.
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function planillaAbastecimiento(Request $request)
    {
        $bases_operacion = BaseOperacion::all();

        if(!empty($request['base_operacion'])) {
            $inventarios = Inventario::with(['piezas.unidadMedida', 'base_operacion'])
            ->whereHas('base_operacion', function ($q) use($request) {
                return $q->where('bases_operacion_id', $request['base_operacion']);
            })
            ->with('piezas') //Necesario para que no rompa getCantidadEnPedidosAttribute
            ->where('compra_unica', 0)
            ->get();
            foreach($inventarios as $inventario) { 
                \Log::debug($inventario);
            }
            return view('admin.inventario.planilla_abastecimiento' , compact('bases_operacion', 'inventarios'));
        } else {
            return view('admin.inventario.planilla_abastecimiento' , compact('bases_operacion'));
        }
    }

    /**
     * Devuelve si existe un inventario para la pieza y la base de operacion dadas
     *
     * @return bool
     */
    public function getInventarioPorPieza(Request $request)
    {
        $array = ["estado" => false, "mensaje" => ""];

        $inventario = Inventario::where('pieza_id', $request['recursoId'])
        ->where('bases_operacion_id', $request['base_operacion_id'])
        ->with('movimientos', 'piezas.unidadMedida')
        ->with(['piezas' => fn($q) => $q->with(['orden_compra' => fn($w) => $w->with('orden_compra')])]) //Necesario para que no rompa getCantidadEnPedidosAttribute
        ->first();

        if($inventario) {
            $array["estado"] = true;
            $array["mensaje"] = 'Se encontró inventario.';
        } else {
            $array["mensaje"] = 'No se encontró inventario.';
        }

        return response()->json(["respuesta" => $array, "inventario" => $inventario]);
    }

    /**
     * Ver movimientos relacionados al inventario seleccionado.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function verMovimientos($id)
    {
        $inventario = Inventario::where('id', $id)
            ->with([
                'piezas.unidadMedida',
                'base_operacion',
                'movimientos' => function ($q) {
                    return $q->orderBy('created_at', 'DESC');
                },
                'movimientos.pieza',
                'movimientos.base_operacion',
                'movimientos.user',
                'movimientos.orden_trabajo' => function ($q) {
                    return $q->withTrashed();
                    }
                ])
            ->first();
        return view('admin.inventario.ver_movimientos' , compact('inventario'));
    }

    public function exportar(Request $request) {
        $buscar = $request['buscar'] ?? "";
        $buscar_base_operacion = $request['base_operacion'] ?? "";
        $buscar_pieza = $request['pieza'] ?? "";
        $buscar_fecha_hasta = $request['fecha_hasta'] ?? "";

        return (new InventarioExport(
                $buscar_base_operacion,
                $buscar_pieza,
                $buscar_fecha_hasta,
                $buscar
            )
        )->download('inventario - '.Carbon::now()->format('Y-m-d').'.xlsx');
    }
}
