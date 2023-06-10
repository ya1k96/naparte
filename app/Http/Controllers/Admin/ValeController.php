<?php

namespace App\Http\Controllers\Admin;

use App\Vale;
use App\ValeDetalle;
use App\OrdenesTrabajo;
use App\Pieza;
use App\Tarea;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Inventario;
use App\Movimiento;
use Carbon\Carbon;
use DateTime;
use PDF;
use Illuminate\Support\Facades\Auth;
use App\User;

class ValeController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param Request request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
{
    $vales = Vale::withTrashed()
        ->orderByRaw('CASE WHEN fecha = CURDATE() THEN 0 ELSE 1 END, fecha')
        ->with(['ordenes_trabajo' => function($q) {
            $q->withTrashed()
            ->with(['base_operacion', 'unidad' => function($q) {
                $q->withTrashed()
                ->with('modelo');
            }]);
        }])
        ->paginate(10);

    return view('admin.vales.index', compact('vales'));
}


    /**
     * Función para crear vales manuales
     * @param Request $request
     * @return \Illuminate\Http\Response
    */
    public function create(Request $request)
    {
        $orden_trabajo = OrdenesTrabajo::withTrashed()->with('base_operacion')->find($request['id']);
        $recursos = Inventario::
            where('bases_operacion_id', $orden_trabajo->base_operacion_id)
            ->with(['piezas' => fn($q) => $q->with('unidadMedida')])
            ->get();
        return view('admin.vales.create', 
            compact(['orden_trabajo', 'recursos'])
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        //* Validar que haya inventario, si no existe o no alcanza el stock guardo mensajes de error.
        $errores = [];
        $inventarios= [];
        if($request['listado_piezas_recursos']) {
            foreach ($request['listado_piezas_recursos'] as $listado) {
    
                $inventario = Inventario::where('pieza_id', $listado['pieza_id'])
                    ->where('bases_operacion_id', $request['base_operacion_id'])
                    ->with('movimientos', 'piezas.unidadMedida')
                    ->first();
    
                if($inventario) {
                    $inventarios[$listado['pieza_id']] = $inventario;
                    if($inventario->stock_total < $listado['cantidad']) {
                        $errores[] = 'No hay stock suficiente para la pieza '.$listado['pieza_descripcion'];
                    }
                } else {
                    $errores[] = 'No existe un inventario para la pieza '.$listado['pieza_descripcion'];
                }
                
            }
        } else {
            $errores[] = 'No puede guardar un vale sin recursos.';
        }
        if(!empty($errores)) {
            //* Si hay errores (no hay inventario/no hay stock) devuelve error.
            //FIXME: Esto podría ser un solo error en vez de tirar uno x cada uno pero notify no me deja hacer una lista.
            foreach($errores as $error) {
                notify()->error($error,"Error: ","topRight");
            }
            return back();
        } else {
            $vale = new Vale();
            $vale->fecha = Carbon::now()->toDateString();
            $vale->ordenes_trabajo_id = $request['ot_id'];
    
            if($vale->save()) {
                foreach ($request['listado_piezas_recursos'] as $listado) {
                    $vale_detalle = new ValeDetalle();
                    $vale_detalle->vale_id = $vale->id;
                    $vale_detalle->pieza_id = $listado['pieza_id'];
                    $vale_detalle->cantidad = $listado['cantidad'];
                    if (!$vale_detalle->save()) {
                        notify()->error("Hubo un error al guardar el detalle del Vale. Por favor, inténtalo nuevamente.","Error: ","topRight");
                        return redirect($this->referer());
                    }
                }
            } else{
                notify()->error("Hubo un error al guardar el Vale. Por favor, inténtalo nuevamente.","Error: ","topRight");
                return redirect($this->referer());
            }
        }


        notify()->success("El Vale se agregó correctamente","Éxito: ","topRight");
        return redirect()->route('admin.ordenes-trabajo');
    }

    /**
     * Muestra la pantalla para cerrar vales de tanto OT Preventiva y Correctiva
    */
    public function editar(int $id)
    {
        $vale = Vale::where(['id' => $id])
        ->with(['ordenes_trabajo' => function($q) {
            $q->withTrashed();
        }])
        ->with(['vale_detalle' => function($q) {
            $q->with(['pieza' => function($q) {
                $q->with(['unidadMedida']);
            }])->with(['tarea' => function($q) {
                $q->with(['componente']);
            }]);
        }])->first();

        $excepciones = [];
        if (!empty($vale->vale_detalle)) {
            foreach ($vale->vale_detalle as $detalle) {
                $excepciones[] = $detalle->pieza_id;
            }
        }
        $recursos = Pieza::with('unidadMedida')->whereNotIn('id', $excepciones)->get();
        /* Comentario */

        //* Si es un Vale con tareas
        $tareas = null;
        if ($vale->vale_detalle != null) {
            $lista_tareas = [];
            foreach ($vale->vale_detalle as $vale_tarea) {
                $lista_tareas[] = $vale_tarea->tarea_id;
            }
            $tareas = Tarea::whereIn('id', $lista_tareas)->get();
        }

        if($vale->ordenes_trabajo->tipo_orden == 'Preventiva') {
            return view('admin.vales.editar', 
                compact(['vale', 'recursos', 'tareas'])
            );
        } else {
            return view('admin.vales.editar_correctiva', 
                compact(['vale', 'recursos', 'tareas'])
            );
        }
    }

    /**
     * Función para CERRAR los vales manuales de OT Correctivas
    */
    public function cerrarCorrectiva($id = null, Request $request)
    {
        $vale = Vale::where('id', $id)
            ->with([
                'ordenes_trabajo' => function($q) {
                    $q->withTrashed();
                },
                'vale_detalle.pieza.unidadMedida'
            ])
            ->first();

        $vales_guardados = ValeDetalle::where('vale_id', $id)->where('cerrado', false)->get();

        //* Validar que haya inventario, si no existe o no alcanza el stock guardo mensajes de error.
        $errores = [];
        $inventarios= [];
        if($request['listado_piezas_recursos']) {
            foreach ($request['listado_piezas_recursos'] as $listado) {
                if (!isset($listado['checkbox'])) continue;
    
                $inventario = Inventario::where('pieza_id', $listado['pieza_id'])
                    ->where('bases_operacion_id', $vale->ordenes_trabajo->base_operacion_id)
                    ->with('movimientos', 'piezas.unidadMedida')
                    ->first();
    
                if($inventario) {
                    $inventarios[$listado['pieza_id']] = $inventario;
                    if($inventario->stock_total < $listado['cantidad']) {
                        $errores[] = 'No hay stock suficiente para la pieza '.$listado['pieza_descripcion'];
                    }
                } else {
                    $errores[] = 'No existe un inventario para la pieza '.$listado['pieza_descripcion'];
                }
                
            }
        }
        if(!empty($errores)) {
            //* Si hay errores (no hay inventario/no hay stock) devuelve error.
            //FIXME: Esto podría ser un solo error en vez de tirar uno x cada uno pero notify no me deja hacer una lista.
            foreach($errores as $error) {
                notify()->error($error,"Error: ","topRight");
            }
            return back();
        } else {
            //* Guardar vales.

            //* Borro los asociados y los creo otra vez
            foreach ($vales_guardados as $guardado) {
                ValeDetalle::destroy($guardado->id);
            }

            //*Guardar los vales actualizados
            if($request['listado_piezas_recursos']) {
                foreach ($request['listado_piezas_recursos'] as $listado) {
                    if (!isset($listado['checkbox'])) continue;   
        
                    $vale_detalle = new ValeDetalle();
                    $vale_detalle->vale_id = $vale->id;
                    if (isset($listado['tarea_id'])) $vale_detalle->tarea_id = $listado['tarea_id'];
                    $vale_detalle->pieza_id = $listado['pieza_id'];
                    $vale_detalle->cantidad = $listado['cantidad'];
                    $vale_detalle->cerrado = true;
                    if (!$vale_detalle->save()) {
                        notify()->error("Hubo un error al guardar el detalle del Vale. Por favor, inténtalo nuevamente.","Error: ","topRight");
                        return redirect()->route('admin.vale');
                    } else {
                        //*Generar movimiento para cada vale
                        Movimiento::create([
                            'pieza_id' => $listado['pieza_id'],
                            'base_operacion_id' => $vale->ordenes_trabajo->base_operacion_id,
                            'inventario_id' => $inventarios[$listado['pieza_id']]->id,
                            'fecha' => new DateTime(),
                            'cantidad' => $listado['cantidad'],
                            'precio_unitario' => $inventarios[$listado['pieza_id']]->last_price,
                            'ubicacion' => $inventarios[$listado['pieza_id']]->ubicacion,
                            'orden_trabajo_id' => $vale? $vale->ordenes_trabajo_id : null,
                            'vale_id' => $vale? $vale->id : null,
                            'user_id' => auth()->user()->id,
                        ]);
                    }
                }
            }
        }
        
        //$vale->cerrado = true;
        if(!$vale->save()) {
            notify()->error("Hubo un error al guardar el Vale. Por favor, inténtalo nuevamente.","Error: ","topRight");
            return redirect()->route('admin.vale');
        }
        notify()->success("El Vale se editó correctamente","Éxito: ","topRight");
        return redirect()->route('admin.vale');
    }

    /**
     * Función para GUARDAR los vales manuales cerrados
     * @param Request $request
    */
    public function updateCerrar(int $id, Request $request)
    {
        $vale = Vale::where('id', $id)->with(['ordenes_trabajo' => function($q) {
            $q->withTrashed();
        }])->first();

        $vales_guardados = ValeDetalle::where('vale_id', $id)->where('cerrado', false)->get();

        //* Validar que haya inventario, si no existe o no alcanza el stock guardo mensajes de error.
        $errores = [];
        $inventarios= [];
        //dd($request['listado_piezas_recursos']);
        if($request['listado_piezas_recursos']) {
            foreach ($request['listado_piezas_recursos'] as $listado) {
                if (!isset($listado['checkbox'])) continue;
    
                $inventario = Inventario::where('pieza_id', $listado['pieza_id'])
                    ->where('bases_operacion_id', $vale->ordenes_trabajo->base_operacion_id)
                    ->with('movimientos', 'piezas.unidadMedida')
                    ->first();
    
                if($inventario) {
                    $inventarios[$listado['pieza_id']] = $inventario;
                    if($inventario->stock_total < $listado['cantidad']) {
                        $errores[] = 'No hay stock suficiente para la pieza '.$listado['pieza_descripcion'];
                    }
                } else {
                    $errores[] = 'No existe un inventario para la pieza '.$listado['pieza_descripcion'];
                }
                
            }
        }
        if(!empty($errores)) {
            //* Si hay errores (no hay inventario/no hay stock) devuelve error.
            //FIXME: Esto podría ser un solo error en vez de tirar uno x cada uno pero notify no me deja hacer una lista.
            foreach($errores as $error) {
                notify()->error($error,"Error: ","topRight");
            }
            return back();
        } else {
            //* Guardar vales.

            //* Borro los asociados y los creo otra vez
            foreach ($vales_guardados as $guardado) {
                ValeDetalle::destroy($guardado->id);
            }

            //*Guardar los vales actualizados
            if($request['listado_piezas_recursos']) {
                foreach ($request['listado_piezas_recursos'] as $listado) {
                    if (!isset($listado['checkbox'])) continue;   
        
                    $vale_detalle = new ValeDetalle();
                    $vale_detalle->vale_id = $vale->id;
                    if (isset($listado['tarea_id'])) $vale_detalle->tarea_id = $listado['tarea_id'];
                    $vale_detalle->pieza_id = $listado['pieza_id'];
                    $vale_detalle->cantidad = $listado['cantidad'];
                    $vale_detalle->cerrado = true;
                    if (!$vale_detalle->save()) {
                        notify()->error("Hubo un error al guardar el detalle del Vale. Por favor, inténtalo nuevamente.","Error: ","topRight");
                        return redirect()->route('admin.vale');
                    } else {
                        //*Generar movimiento para cada vale
                        Movimiento::create([
                            'pieza_id' => $listado['pieza_id'],
                            'base_operacion_id' => $vale->ordenes_trabajo->base_operacion_id,
                            'inventario_id' => $inventarios[$listado['pieza_id']]->id,
                            'fecha' => new DateTime(),
                            'cantidad' => $listado['cantidad'],
                            'precio_unitario' => $inventarios[$listado['pieza_id']]->last_price,
                            'ubicacion' => $inventarios[$listado['pieza_id']]->ubicacion,
                            'orden_trabajo_id' => $vale? $vale->ordenes_trabajo_id : null,
                            'vale_id' => $vale? $vale->id : null,
                            'user_id' => auth()->user()->id,
                        ]);
                    }
                }
            }
        }

        //$vale->cerrado = true;
        if(!$vale->save()) {
            notify()->error("Hubo un error al guardar el Vale. Por favor, inténtalo nuevamente.","Error: ","topRight");
            return redirect()->route('admin.vale');
        }

        notify()->success("El Vale se editó correctamente","Éxito: ","topRight");
        return redirect()->route('admin.vale');
    }

    /**
     * Cerrar un vale manualmente.
     *
     * @param int $id
     */
    public function cerrar($id) {
        $vale = Vale::where('id', $id)->with(['ordenes_trabajo' => function($q) {
            $q->withTrashed();
        }])->first();

        $vale->cerrado = true;
        if(!$vale->save()) {
            notify()->error("Hubo un error al cerrar la Vale. Por favor, inténtalo nuevamente.","Error: ","topRight");
            return redirect()->route('admin.vale');
        }

        notify()->success("El Vale se cerró correctamente","Éxito: ","topRight");
        return redirect()->route('admin.vale');
    }

    /**
     * Función que trae los recursos asociados
     * @param int $id
     * @return \Illuminate\Http\Response
    */
    public function recursosAsociados($id) 
    {
        $array = ["estado" => false, "mensaje" => ""];

        $vale = Vale::withTrashed()->where(['id' => $id])
        ->with(['ordenes_trabajo' => function($q) {
            $q->withTrashed();
        }])
        ->with(['vale_detalle' => function($q) {
            $q
            ->with(['pieza' => function($q) {
                $q->with(['unidadMedida']);
            }])
            ->with(['tarea' => function($q) {
                $q->with(['componente']);
            }]);
        }])
        ->first();

        $recursos = [];
        if ($vale->ordenes_trabajo->tipo_orden == 'Preventiva') {
            foreach ($vale->vale_detalle as $recur) {
                $recursos[] = [
                    'material' => $recur->pieza->descripcion,
                    'parte' => $recur->tarea->componente->nombre,
                    'cantidad' => $recur->cantidad,
                    'unidad' => $recur->pieza->unidadMedida->nombre,
                ];
            }
        }
        if ($vale->ordenes_trabajo->tipo_orden == 'Correctiva') {
            foreach ($vale->vale_detalle as $recur) {
                $recursos[] = [
                    'material' => $recur->pieza->descripcion,
                    'parte' => '-',
                    'cantidad' => $recur->cantidad,
                    'unidad' => $recur->pieza->unidadMedida->nombre,
                ];
            }
        }

        if(!empty($recursos)) {
            $array["estado"] = true;
            $array["mensaje"] = 'Se encontraron los recursos.';
            $array["respuesta"] = $recursos;
        } else {
            $array["estado"] = false;
            $array["mensaje"] = 'No se encontraron recursos.';
            $array["respuesta"] = '';
        }

        return response()->json([
            'estado' => $array["estado"],
            'mensaje' => $array["mensaje"],
            'respuesta' => $array["respuesta"]
        ]);

    }
    
    /**
     * Generar PDF de un vale
     *
     * @return void
     */
    public function generarPDF($id) {
        
        $vale = Vale::withTrashed()->where(['id' => $id])
        ->with(['ordenes_trabajo' => fn($q) => $q->withTrashed()->with(['base_operacion'])])
        ->with(['vale_detalle' => function($q) {
            $q
            ->with(['pieza' => fn($q) => $q->with(['unidadMedida'])])
            ->with(['tarea' => fn($q) => $q->with(['componente'])]);
        }])
        ->first();

        $array_piezas = [];
        foreach ($vale->vale_detalle as $vale_detalle) {
            $localizacion = Inventario::where([
                'bases_operacion_id' => $vale->ordenes_trabajo->base_operacion_id,
                'pieza_id' => $vale_detalle->pieza_id
                ])
                ->select('ubicacion')
                ->first();
            $array_piezas[] = [
                'material' => $vale_detalle->pieza->descripcion,
                'nro_parte' => $vale_detalle->pieza->nro_pieza,
                'cantidad' => $vale_detalle->cantidad,
                'unidad' => $vale_detalle->pieza->unidadMedida->nombre,
                'localizacion' => $localizacion->ubicacion ?? ''
            ];
        }

        $user = User::where('id', Auth::id())->first();
        $fecha = new DateTime();

        // dd($vale, $array_piezas);

        $data = [
            'id' => $vale->id,
            'orden_id' => $vale->ordenes_trabajo->id,
            'fecha_vale' => $vale->fecha,
            'vales_detalles' => $array_piezas,
            'user' => $user->name,
            'fecha' => $fecha->format('M d,Y H:i'),
            'base_operacion' => $vale->ordenes_trabajo->base_operacion->nombre,
            'unidad' => $vale->ordenes_trabajo->unidad->num_interno . ' / ' . $vale->ordenes_trabajo->unidad->modelo->nombre,
        ];
        $pdf = PDF::loadView('/admin/vales/template_pdf/template_vale_pdf', $data);
        if($pdf) {
            return $pdf->stream('OT_'.$vale->id.'.pdf');
        }else{
            notify()->error("Hubo un error al descargar la OT. Por favor, inténtalo nuevamente.","Error: ","topRight");
            return redirect($this->referer());
        }
    }

    /**
     * Reabrir manualmente vale si su OT está abierta
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function reabrir($id) {
        $vale = Vale::withTrashed()
            ->where('id', $id)
            ->with(['ordenes_trabajo' => function($q) {
                $q->withTrashed();
            }])
            ->first();

        if($vale->checkReabrir()) {
            $vale->cerrado = false;
            if($vale->restore() && $vale->update()) {
                notify()->success("El Vale se reabrió correctamente","Éxito: ","topRight");
            } else {
                notify()->error("El Vale no pudo ser reabierto. Por favor, inténtelo nuevamente.","Éxito: ","topRight");
            }
        } else {
            notify()->error("El Vale no puede ser reabierto porque su OT está anulada o cerrada.","Éxito: ","topRight");
        }
        return redirect()->route('admin.vale');
    }

    /**
     * Poder generar devolución desde un vale para poder modificar y sacar algo que no se usó realmente.
     */
    public function devolucion($id) {
        $vale = Vale::where(['id' => $id])
            ->with(['ordenes_trabajo' => function($q) {
                $q->withTrashed();
            }])
            ->with(['vale_detalle' => function($q) {
                $q->with(['pieza' => function($q) {
                    $q->with(['unidadMedida']);
                }])->with(['tarea' => function($q) {
                    $q->with(['componente']);
                }]);
            }])->first();

        return view('admin.vales.devolucion', 
            compact(['vale'])
        );
    }

    public function updateDevolucion(int $id, Request $request) {

        $vale = Vale::where(['id' => $id])
            ->with(['ordenes_trabajo' => function($q) {
                $q->withTrashed();
            }])
            ->with(['vale_detalle' => function($q) {
                $q->with(['pieza' => function($q) {
                    $q->with(['unidadMedida']);
                }])->with(['tarea' => function($q) {
                    $q->with(['componente']);
                }]);
            }])->first();

        $inventarios = Inventario::where('bases_operacion_id', $vale->ordenes_trabajo->base_operacion_id)
            ->with('movimientos', 'piezas.unidadMedida')
            ->get();

        try {
            foreach ($request['listado_piezas_recursos'] as $k => $recurso) {
                if(array_key_exists('checkbox', $recurso)) {
                    $vale_detalle = $vale->vale_detalle->where('id', $recurso['id'])->first();
                    $vale_detalle->cantidad -= $recurso['cantidad_devolucion'];

                    $inventario = $inventarios->where('pieza_id', $recurso['pieza_id'])->first();
                    
                    //Busco el movimiento de egreso generado por el vale para obtener el precio de la pieza
                    $movimiento_vale = Movimiento::where('vale_id', $id)->where('pieza_id', $recurso['pieza_id'])->first();
                    
                    if($vale_detalle->cantidad == 0) {
                        //Si devolvió todo eliminar el detalle.
                        if($vale_detalle->delete()){
                            //*Generar movimiento para cada vale
                            $movimiento = Movimiento::create([
                                'pieza_id' => $recurso['pieza_id'],
                                'base_operacion_id' => $vale->ordenes_trabajo->base_operacion_id,
                                'inventario_id' => $inventario->id,
                                'fecha' => new DateTime(),
                                'cantidad' => $recurso['cantidad_devolucion'],
                                'precio_unitario' => $movimiento_vale->precio_unitario,
                                'ubicacion' => $inventario->ubicacion,
                                'user_id' => auth()->user()->id,
                                'devolucion_detalle' => 'DEVOLUCION Vale n° ' . $vale->id,
                            ]);
                        }
                    } else{
                        if($vale_detalle->save()) {
                            //*Generar movimiento para cada vale
                            $movimiento = Movimiento::create([
                                'pieza_id' => $recurso['pieza_id'],
                                'base_operacion_id' => $vale->ordenes_trabajo->base_operacion_id,
                                'inventario_id' => $inventario->id,
                                'fecha' => new DateTime(),
                                'cantidad' => $recurso['cantidad_devolucion'],
                                'precio_unitario' => $movimiento_vale->precio_unitario,
                                'ubicacion' => $inventario->ubicacion,
                                'user_id' => auth()->user()->id,
                                'devolucion_detalle' => 'DEVOLUCION Vale n° ' . $vale->id,
                            ]);
    
                        }
                    }
    
    
                }
            }
            notify()->success("El Vale se editó correctamente","Éxito: ","topRight");
            return redirect()->route('admin.vale');
        } catch (\Throwable $th) {
            notify()->error($th->getMessage(), "Error: " , "topRight");
            return redirect()->back();
        }

    }
}
