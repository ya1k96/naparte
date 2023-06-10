<?php

namespace App\Http\Controllers\Admin;

use App\BaseOperacion;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Inventario;
use App\OrdenTransferencia;
use App\OrdenTransferenciaDetalle;
use App\OrdenTransferenciaAccion;
use Carbon\Carbon;
use App\Movimiento;
use DateTime;

class OrdenTransferenciaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {                   
      $arr_base_origen = BaseOperacion::All();
      $arr_base_destino = BaseOperacion::All();
      $arr_estados = OrdenTransferencia::$_estado;
      
      $buscar = [];
      $buscar["id"] = $request->id;      
      $buscar["base_origen_id"] = $request->base_origen_id;      
      $buscar["base_destino_id"] = $request->base_destino_id;
      $buscar["estado"] = $request->estado;      
      $buscar["fecha_emision_desde"] = $request->fecha_emision_desde;
      $buscar["fecha_emision_hasta"] = $request->fecha_emision_hasta;      

      $ordenes = new OrdenTransferencia();

      if($buscar["id"])
        $ordenes = $ordenes->where('id','LIKE','%'.$buscar["id"].'%');

      if($buscar["base_origen_id"])
        $ordenes = $ordenes->where('base_origen_id',$buscar["base_origen_id"]);

      if($buscar["base_destino_id"])
        $ordenes = $ordenes->where('base_destino_id',$buscar["base_destino_id"]);

      if($buscar["estado"]) { // TODO: arreglar cuando se definan los estados
        ($buscar["estado"] == 'pendiente_parcial') ? $ordenes = $ordenes->whereIn('estado', ['aprobada', 'parcial']) : $ordenes = $ordenes->where('estado',$buscar["estado"]);
      }
              
      if($buscar["fecha_emision_desde"]) {
        $ordenes = $ordenes->whereDate('fecha_emision','>=',$buscar["fecha_emision_desde"]);                        
      }

      if($buscar["fecha_emision_hasta"]) {
        $ordenes = $ordenes->whereDate('fecha_emision','<=',$buscar["fecha_emision_hasta"]);                          
      }
      
      $ordenes = $ordenes->orderBy('id','DESC')->paginate(10);            
      
      return view('admin.orden_transferencia.index', compact(                
        'arr_base_origen',
        'arr_base_destino',
        'arr_estados',        
        'buscar',
        'ordenes'
      ));      
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {      
      $arr_base_origen = BaseOperacion::All();
      $arr_base_destino = BaseOperacion::All();
      $arr_estados = OrdenTransferencia::$_estado;
      
      $arr_piezas = [];
      $arr_inventario = Inventario::All();      
      foreach ($arr_inventario as $key => $value) {
        if($value->stock_total > 0) {
          $arr_piezas[] = array(
            'id' => $value->id,
            'descripcion' => $value->piezas->descripcion,
            'nro_pieza' => $value->piezas->nro_pieza,
            'base_id' => $value->bases_operacion_id,
            'stock' => $value->stock_total,
          );
        }
      }      

      $oc = OrdenTransferencia::OrderBy('id','desc')->first();      
      $next_id = $oc ? $oc->id + 1 : 1;

      return view('admin.orden_transferencia.create', compact(                
        'arr_base_origen',
        'arr_base_destino',
        'arr_estados',        
        'arr_piezas',
        'next_id'
      ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {           
      //dd($request->all());

      $orden = new OrdenTransferencia();
      
      $orden->base_origen_id = $request['base_origen_id'];
      $orden->base_destino_id = $request['base_destino_id'];      
      $orden->observacion = $request['observaciones'];      
      $orden->fecha_emision = Carbon::now();            
      $orden->solicitado_nombre = $request['solicitado_nombre'];      
      //$orden->entregado_nombre = $request['entregado_nombre'];      
      $orden->estado = OrdenTransferencia::$_estado['aprobada'];
      
      if($orden->save()) {                
        $cant_detalle = $request['cant_tr'];

        for ($i=1; $i <= $cant_detalle; $i++) { 
          if(isset($request['pieza_id-'.$i]) && isset($request['cantidad-'.$i])) {
            $detalle = new OrdenTransferenciaDetalle();
            $detalle->orden_transferencia_id = $orden->id;
            $detalle->piezas_id = Inventario::find($request['pieza_id-'.$i])->piezas->id;
            $detalle->cantidad = $request['cantidad-'.$i];            
            $detalle->save();
          }
        }
        
        notify()->success("La orden de transferencia se generó correctamente","Éxito: ","topRight");
        return redirect()->route('admin.orden-transferencia.index');        
      }else{
        notify()->error("Hubo un error al guardar la orden de transferencia. Por favor, inténtalo nuevamente.","Error: ","topRight");
        return redirect()->back();
      }      
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $arr_estados = OrdenTransferencia::$_estado;
        $orden = OrdenTransferencia::where('id', $id)->with([
          'accion.user',
          'accion' => fn($q) => $q->latest()
          ])->first();
        
        return view('admin.orden_transferencia.show', compact(
          'arr_estados',
          'orden'
        ));
    }

    /**
     * Función para cerrar una Orden de compra parcial
     */
    public function cerrar(Request $request)
    {
      $orden = OrdenTransferencia::find($request->id);

      $orden->estado = OrdenTransferencia::$_estado['cerrada'];
      if ($orden->save()) {
        $oc_accion = new OrdenTransferenciaAccion();
        $oc_accion->orden_transferencia_id = $orden->id;
        $oc_accion->tipo = 'cerrada';
        $oc_accion->observacion = $request->observacion_cerrar;
        $oc_accion->user_id = auth()->user()->id;
        if (!$oc_accion->save()) {
          notify()->error("Hubo un error al guardar la acción de la orden de transferencia. Por favor, inténtalo nuevamente.", "Error: ", "topRight");
        }
        notify()->success("El estado de la orden de transferencia se guardó correctamente", "Éxito: ", "topRight");
      } else {
        notify()->error("Hubo un error al completar la orden de transferencia. Por favor, inténtalo nuevamente.", "Error: ", "topRight");
      }

      return redirect()->route('admin.orden-transferencia.index');
    }


  /**
   * Función para recibir las Ordenes de Transferencia
  */
  public function recibir()
  {
    $bases_operaciones = BaseOperacion::all();

    return view('admin.orden_transferencia.recibir', compact('bases_operaciones'));
  }

  /**
   * Función para guardar las Ordenes de Transferencia recibidas
   * 
   * @return \Illuminate\Http\Response
  */
  public function storeRecibidas(Request $request)
  {
    if (isset($request['detalle_ot']) && !empty($request['detalle_ot']) ) {

      $mensajes = [];

      $orden = OrdenTransferencia::find($request['orden_transferencia_id']);

      foreach ($request['detalle_ot'] as $detalle) {
        if (isset($detalle['checkbox']) && $detalle['checkbox'] == 1) {
          $inventario_egreso = Inventario::where(['bases_operacion_id' => $orden['base_origen_id'], 'pieza_id' => $detalle['pieza_id']])->first();
          if (!$inventario_egreso) {
            $mensajes[] = 'La pieza N° '. $detalle['nro_pieza'].' no existe en el inventario de origen';
            continue;
          }

          if ($inventario_egreso->stock_total < $detalle['ingreso']) {
            $mensajes[] = 'La pieza N° '. $detalle['nro_pieza'].' no tiene suficiente stock para hacer la transferencia';
            continue;
          }

          $orden_detalle = OrdenTransferenciaDetalle::find($detalle['detalle_id']);

          if ($orden_detalle->ingreso != null) {
            $orden_detalle->ingreso += $detalle['ingreso'];
          } else {
            $orden_detalle->ingreso = $detalle['ingreso'];
          }

          $precio_unitario = OrdenTransferencia::getLastPriceOrdenCompra($detalle['pieza_id'], $orden['base_origen_id']);

          if ($orden_detalle->save()) {

            /*** MOVIMIENTO DE EGRESO ***/
            Movimiento::create([
              'pieza_id' => $detalle['pieza_id'],
              'base_operacion_id' => $orden['base_origen_id'],
              'inventario_id' => $inventario_egreso->id,
              'fecha' => new DateTime(),
              'balance' => '-',
              'cantidad' => $detalle['ingreso'],
              'precio_unitario' => $precio_unitario,
              'ubicacion' => $inventario_egreso->ubicacion,
              'orden_transferencia_id' => $request['orden_transferencia_id'],
              'user_id' => auth()->user()->id,
            ]);
            /*** FIN MOVIMIENTO DE EGRESO ***/


            /*** MOVIMIENTO DE INGRESO ***/
            $inventario_ingreso = Inventario::where(['bases_operacion_id' => $orden['base_destino_id'], 'pieza_id' => $detalle['pieza_id']])->first();
            
            if(empty($inventario_ingreso)){
              $inventario_ingreso = new Inventario;
              $inventario_ingreso->bases_operacion_id = $orden['base_destino_id'];
              $inventario_ingreso->pieza_id = $detalle['pieza_id'];
              $inventario_ingreso->stock = 0;
              $inventario_ingreso->ubicacion = '';
              $inventario_ingreso->precio = 0;
              $inventario_ingreso->save();
            }

            Movimiento::create([
              'pieza_id' => $detalle['pieza_id'],
              'base_operacion_id' => $orden['base_destino_id'],
              'inventario_id' => $inventario_ingreso->id,
              'fecha' => new DateTime(),
              'balance' => '+',
              'cantidad' => $detalle['ingreso'],
              'precio_unitario' => $precio_unitario,
              'ubicacion' => $inventario_ingreso->ubicacion,
              'orden_transferencia_id' => $request['orden_transferencia_id'],
              'user_id' => auth()->user()->id,
            ]);
            /*** FIN MOVIMIENTO DE INGRESO ***/
          };
        }
      }

      if(empty($mensajes)){
        $orden = OrdenTransferencia::
          where('id', $request['orden_transferencia_id'])
          ->with(['detalle' => function ($q) {
            return $q
              ->whereColumn('cantidad', '>', 'ingreso');
          }])
          ->first(); 

        if ($orden->detalle->isEmpty()) {
          $orden->estado = 'recibida';
        } else {
          $orden->estado = 'parcial';
        }

        if($orden->save()) {
          notify()->success("La orden de transferencia se generó correctamente","Éxito: ","topRight");
        }
      } else{
        foreach ($mensajes as $mensaje) {
          notify()->info($mensaje, "Info: ", "topRight");
        }
        return redirect()->back();
      }
    } else {
      notify()->error("Hubo un error al recibir la Orden de Transferencia. Por favor, inténtalo nuevamente.","Error: ","topRight");
      return redirect()->back();      
    }

    return redirect()->route('admin.orden-transferencia.index'); 
  }

  /**
   * Obtener información de una Orden de Transferencia de una base de operaciones (API)
   *
   * @return void
   */
  public function getOrdenTransferenciaPorBase($id) {
    $array = ["estado" => false, "mensaje" => ""];

    $ordenes_transferencias = OrdenTransferencia::where('base_origen_id', $id)
                                                ->whereIn('estado', ['aprobada', 'parcial'])
                                                ->distinct()
                                                ->get(); 

    if(!empty($ordenes_transferencias)) {
      $array["estado"] = true;
      $array["mensaje"] = 'Se encontraron las Ordenes de Transferencia.';
      $array["respuesta"] = $ordenes_transferencias;
    } else {
        $array["estado"] = false;
        $array["mensaje"] = 'No se encontraron Ordenes de Transferencia.';
        $array["respuesta"] = '';
    }

    return response()->json([
        'estado' => $array["estado"],
        'mensaje' => $array["mensaje"],
        'respuesta' => $array["respuesta"]
    ]);
  }

  /**
   * Obtener información de una Orden de Transferencia (API)
   *
   * @return void
   */
  public function getOrdenTransferencia($id) {
    $array = ["estado" => false, "mensaje" => ""];

    $orden_transferencia = OrdenTransferencia::
      where('id', $id)
      ->with(['detalle' => function ($q) {
        return $q
          ->whereColumn('cantidad', '>', 'ingreso')
          ->orWhereNull('ingreso')
          ->with(['pieza']);
      }])
      ->first();

    if(!empty($orden_transferencia)) {
      $array["estado"] = true;
      $array["mensaje"] = 'Se encontró la Orden de Transferencia.';
      $array["respuesta"] = $orden_transferencia;
    } else {
        $array["estado"] = false;
        $array["mensaje"] = 'No se encontró la Orden de Transferencia.';
        $array["respuesta"] = '';
    }

    return response()->json([
        'estado' => $array["estado"],
        'mensaje' => $array["mensaje"],
        'respuesta' => $array["respuesta"]
    ]);
  }
}
