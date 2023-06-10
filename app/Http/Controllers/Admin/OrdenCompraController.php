<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Log;
use App\BaseOperacion;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Empresa;
use App\Inventario;
use App\OrdenCompra;
use App\OrdenCompraDetalle;
use App\OrdenCompraAccion;
use App\Proveedor;
use App\Pieza;
use App\Movimiento;
use App\User;
use PDF;
use DateTime;
use Illuminate\Support\Facades\Auth;

class OrdenCompraController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $arr_base_emite = BaseOperacion::All();
    $arr_base_recibe = BaseOperacion::All();
    $arr_estados = OrdenCompra::$_estado;
    $arr_prioridades = OrdenCompra::$_prioridad;
    $arr_proveedores = Proveedor::All();
    $arr_piezas = Pieza::All();
    $arr_empresas = Empresa::All();

    $buscar = [];
    $buscar["id"] = $request->id;
    $buscar["empresa_id"] = $request->empresa_id;
    $buscar["base_emite_id"] = $request->base_emite_id;
    $buscar["base_recibe_id"] = $request->base_recibe_id;
    $buscar["estado"] = $request->estado;
    $buscar["prioridad"] = $request->prioridad;
    $buscar["proveedor_id"] = $request->proveedor_id;
    $buscar["pieza_id"] = $request->pieza_id;
    $buscar["fecha_emision_desde"] = $request->fecha_emision_desde;
    $buscar["fecha_emision_hasta"] = $request->fecha_emision_hasta;
    $buscar["fecha_entrega_desde"] = $request->fecha_entrega_desde;
    $buscar["fecha_entrega_hasta"] = $request->fecha_entrega_hasta;

    $ordenes = OrdenCompra::with(['detalle', 'proveedor' => fn($q) => $q->withTrashed()]);

    $ordenes = $ordenes->withTrashed();

    if ($buscar["id"])
      $ordenes = $ordenes->where('id', 'LIKE', '%' . $buscar["id"] . '%');

    if ($buscar["empresa_id"])
      $ordenes = $ordenes->where('empresa_id', $buscar["empresa_id"]);

    if ($buscar["base_emite_id"])
      $ordenes = $ordenes->whereIn('base_emite_id', $buscar["base_emite_id"]);

    if ($buscar["base_recibe_id"])
      $ordenes = $ordenes->whereIn('base_recibe_id', $buscar["base_recibe_id"]);

    if ($buscar["estado"]) {
      ($buscar["estado"] == 'pendiente_parcial') ? $ordenes = $ordenes->whereIn('estado', ['aprobada', 'parcial']) : $ordenes = $ordenes->where('estado', $buscar["estado"]);
    }

    if ($buscar["prioridad"])
      $ordenes = $ordenes->where('prioridad', $buscar["prioridad"]);

    if ($buscar["proveedor_id"])
      $ordenes = $ordenes->where('proveedor_id', $buscar["proveedor_id"]);

    if ($buscar["pieza_id"]) {
      $ordenes = $ordenes->whereHas('detalle', function ($query) use ($buscar) {
        return $query->where('piezas_id', $buscar["pieza_id"]);
      });
    }

    if ($buscar["fecha_emision_desde"])
      $ordenes = $ordenes->whereDate('fecha_emision', '>=', $buscar["fecha_emision_desde"]);

    if ($buscar["fecha_emision_hasta"])
      $ordenes = $ordenes->whereDate('fecha_emision', '<=', $buscar["fecha_emision_hasta"]);

    if ($buscar["fecha_entrega_desde"])
      $ordenes = $ordenes->whereDate('fecha_entrega', '>=', $buscar["fecha_entrega_desde"]);

    if ($buscar["fecha_entrega_hasta"])
      $ordenes = $ordenes->whereDate('fecha_entrega', '<=', $buscar["fecha_entrega_hasta"]);

    $ordenes = $ordenes->orderBy('id', 'DESC')->paginate(10);

    return view('admin.orden_compra.index', compact(
      'arr_empresas',
      'arr_base_emite',
      'arr_base_recibe',
      'arr_estados',
      'arr_prioridades',
      'arr_proveedores',
      'arr_piezas',
      'buscar',
      'ordenes',
    ));
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create(Request $request)
  {
    $arr_base_emite = BaseOperacion::All();
    $arr_base_recibe = BaseOperacion::All();
    $arr_estados = OrdenCompra::$_estado;
    $arr_prioridades = OrdenCompra::$_prioridad;
    $arr_proveedores = Proveedor::All();
    $arr_empresas = Empresa::All();

    $arr_piezas = [];
    $arr_inventario = Inventario::All();
    foreach ($arr_inventario as $key => $value) {
      $arr_piezas[] = array(
        'id' => $value->id,
        'descripcion' => $value->piezas->descripcion,
        'nro_pieza' => $value->piezas->nro_pieza,
        'base_id' => $value->bases_operacion_id,
        'precio' => $value->precio,
        'maximo_compra' => $value->maximo_compra,
        'compra_unica' => $value->compra_unica,
      );
    }

    $oc = OrdenCompra::OrderBy('id', 'desc')->first();
    $next_id = $oc ? $oc->id + 1 : 0;


    return view('admin.orden_compra.create', compact(
      'arr_empresas',
      'arr_base_emite',
      'arr_base_recibe',
      'arr_estados',
      'arr_prioridades',
      'arr_proveedores',
      'arr_piezas',
      'next_id',
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

    $orden = new OrdenCompra();

    $orden->empresa_id = $request['empresa_id'];
    $orden->proveedor_id = $request['proveedor_id'];
    $orden->base_emite_id = $request['base_emite_id'];
    $orden->base_recibe_id = $request['base_recibe_id'];
    $orden->prioridad = $request['prioridad'];
    $orden->observacion = $request['observaciones'];
    $orden->fecha_emision = $request['fecha_emision'];
    $orden->fecha_entrega = $request['fecha_entrega'];
    $orden->estado = OrdenCompra::$_estado['abierta'];

    if ($orden->save()) {
      $cant_detalle = $request['cant_tr'];

      for ($i = 1; $i <= $cant_detalle; $i++) {
        if (isset($request['pieza_id-' . $i]) && isset($request['cantidad-' . $i]) && isset($request['costo-' . $i])) {
          $detalle = new OrdenCompraDetalle();
          $detalle->orden_compra_id = $orden->id;
          $detalle->piezas_id = Inventario::find($request['pieza_id-' . $i])->piezas->id;
          $detalle->cantidad = $request['cantidad-' . $i];
          $detalle->costo = $request['costo-' . $i];
          $detalle->save();
        }
      }

      $oc_accion = new OrdenCompraAccion();
      $oc_accion->orden_compra_id = $orden->id;
      $oc_accion->tipo = 'abierta';
      $oc_accion->user_id = auth()->user()->id;
      if (!$oc_accion->save()) {
        notify()->error("Hubo un error al guardar la acción de la orden de compra. Por favor, inténtalo nuevamente.", "Error: ", "topRight");
      }

      notify()->success("La orden de compra se generó correctamente", "Éxito: ", "topRight");
      return redirect()->route('admin.orden-compra.index');

    } else {
      notify()->error("Hubo un error al guardar la orden de compra. Por favor, inténtalo nuevamente.", "Error: ", "topRight");
      return redirect()->back();
    }
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    $orden = OrdenCompra::where('id', $id)->with(['detalle' => fn($q) => $q->with('pieza')])->first();

    if ($orden->estado != 'abierta') return redirect()->route('admin.orden-compra.index');

    /* dd($orden); */

    $arr_base_emite = BaseOperacion::All();
    $arr_base_recibe = BaseOperacion::All();
    $arr_estados = OrdenCompra::$_estado;
    $arr_prioridades = OrdenCompra::$_prioridad;
    $arr_proveedores = Proveedor::All();
    $arr_empresas = Empresa::All();

    $arr_piezas = [];
    $arr_inventario = Inventario::All();
    foreach ($arr_inventario as $key => $value) {
      $arr_piezas[] = array(
        'id' => $value->id,
        'descripcion' => $value->piezas->descripcion,
        'nro_pieza' => $value->piezas->nro_pieza,
        'base_id' => $value->bases_operacion_id,
        'precio' => $value->precio,
        'maximo_compra' => $value->maximo_compra,
        'compra_unica' => $value->compra_unica,
      );
    }

    return view('admin.orden_compra.edit', compact(
      'arr_empresas',
      'arr_base_emite',
      'arr_base_recibe',
      'arr_estados',
      'arr_prioridades',
      'arr_proveedores',
      'arr_piezas',
      'orden',
    ));
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
    $orden = OrdenCompra::where('id', $id)->with('detalle')->first();

    $orden->empresa_id = $request['empresa_id'];
    $orden->proveedor_id = $request['proveedor_id'];
    $orden->base_emite_id = $request['base_emite_id'];
    $orden->base_recibe_id = $request['base_recibe_id'];
    $orden->prioridad = $request['prioridad'];
    $orden->observacion = $request['observaciones'];
    $orden->fecha_emision = $request['fecha_emision'];
    $orden->fecha_entrega = $request['fecha_entrega'];
    $orden->estado = OrdenCompra::$_estado['abierta'];

    if ($orden->save()) {
      $detalles = OrdenCompraDetalle::where('orden_compra_id', $orden->id)->pluck('id')->toArray();

      foreach ($request['elementos'] as $k => $elemento) {
        if (in_array($k, $detalles)) {
          $editar_detalle = OrdenCompraDetalle::where('id', $k)->first();
          $editar_detalle->cantidad = $elemento['cantidad'];
          $editar_detalle->costo = $elemento['costo'];
          $editar_detalle->update();
        } else {
          $detalle = new OrdenCompraDetalle();
          $detalle->orden_compra_id = $orden->id;
          $detalle->piezas_id = Inventario::find($elemento['pieza_id'])->piezas->id;
          $detalle->cantidad = $elemento['cantidad'];
          $detalle->costo = $elemento['costo'];
          $detalle->save();
        }
      }

      if (!empty($request['elementos_borrados'])) {
        $elementos_borrados = explode(",", $request['elementos_borrados']);

        foreach ($elementos_borrados as $borrar) {
          OrdenCompraDetalle::where('id', $borrar)->forceDelete();
        }
      }

      notify()->success("La orden de compra se editó correctamente", "Éxito: ", "topRight");
      return redirect()->route('admin.orden-compra.index');
    } else {
      notify()->error("Hubo un error al editar la orden de compra. Por favor, inténtalo nuevamente.", "Error: ", "topRight");
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
    $arr_estados = OrdenCompra::$_estado;

    $orden = OrdenCompra::where('id', $id)->withTrashed()
    ->with([
      'accion.user',
      'accion' => fn($q) => $q->with('movimiento'),
      'detalle'=> fn($q) => $q->with('pieza'),
      'proveedor' => fn($q) => $q->withTrashed(),
    ]) ->first();

    return view('admin.orden_compra.show', compact(
      'arr_estados',
      'orden',
    ));
  }

  /**
   * Cambiar estado de una orden de compra. Y cumplir con Validaciones
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function changeStatus(Request $request)
  {
    if (!$request->id) {
      notify()->error("No se cargó la orden de compra.", "Error: ", "topRight");
      return redirect()->back();
    }

    if (!$request->estado) {
      notify()->error("No se cargó el estado.", "Error: ", "topRight");
      return redirect()->back();
    }

    $orden = OrdenCompra::find($request->id);

    if ($request->estado == $orden->estado) {
      notify()->error("El estado que intentas cambiar es el mismo que posee actualmente.", "Error: ", "topRight");
      return redirect()->back();
    }

    $orden->estado = $request->estado;

    if ($orden->save()) {
      notify()->success("Se cambió el estado de la orden de compra correctamente", "Éxito: ", "topRight");
      return redirect()->route('admin.orden-compra.index');
    } else {
      notify()->error("Hubo un error al cambiar el estado de la orden de compra. Por favor, inténtalo nuevamente.", "Error: ", "topRight");
      return redirect()->back();
    }
  }

  /**
   * Función para aprobar una Orden de compra.
   */
  public function aprobar(Request $request)
  {
    $orden = OrdenCompra::find($request->id);

    $orden->estado = 'aprobada';

    if ($orden->save()) {
      $oc_accion = new OrdenCompraAccion();
      $oc_accion->orden_compra_id = $orden->id;
      $oc_accion->tipo = 'aprobada';
      $oc_accion->observacion = $request->observacion_aprobar;
      $oc_accion->user_id = auth()->user()->id;

      if (!$oc_accion->save()) {
        notify()->error("Hubo un error al guardar la acción de la orden de compra. Por favor, inténtalo nuevamente.", "Error: ", "topRight");
      }
      notify()->success("El estado de la orden de compra se guardó correctamente", "Éxito: ", "topRight");
    } else {
      notify()->error("Hubo un error al completar la orden de compra. Por favor, inténtalo nuevamente.", "Error: ", "topRight");
    }

    return redirect()->route('admin.orden-compra.index');
  }

  /**
   * Función para cerrar una Orden de compra parcial
   */
  public function cerrar(Request $request)
  {
    $orden = OrdenCompra::find($request->id);

    $orden->estado = 'cerrada';
    if ($orden->save()) {
      $oc_accion = new OrdenCompraAccion();
      $oc_accion->orden_compra_id = $orden->id;
      $oc_accion->tipo = 'cerrada';
      $oc_accion->observacion = $request->observacion_cerrar;
      $oc_accion->user_id = auth()->user()->id;
      if (!$oc_accion->save()) {
        notify()->error("Hubo un error al guardar la acción de la orden de compra. Por favor, inténtalo nuevamente.", "Error: ", "topRight");
      }
      notify()->success("El estado de la orden de compra se guardó correctamente", "Éxito: ", "topRight");
    } else {
      notify()->error("Hubo un error al completar la orden de compra. Por favor, inténtalo nuevamente.", "Error: ", "topRight");
    }

    return redirect()->route('admin.orden-compra.index');
  }

  /**
   * Función para recibir las Ordenes de Compra
   */
  public function recibir()
  {
    $bases_operaciones = BaseOperacion::all();

    return view('admin.orden_compra.recibir', compact('bases_operaciones'));
  }

  /**
   * Anular OC que estén abiertas o aprobadas.
   * @return void
   */
  public function anular(Request $request)
  {
    $orden = OrdenCompra::where('id', $request['id'])->first();

    if($orden->estado != 'aprobada' && $orden->estado != 'abierta') {
      notify()->error("Sólo pueden anularse las órdenes de compra abiertas o aprobadas.", "Error: ", "topRight");
      return redirect()->back();
    }

    $orden->estado = 'anulada';
    if ($orden->save() && $orden->delete()) {
      $oc_accion = new OrdenCompraAccion();
      $oc_accion->orden_compra_id = $orden->id;
      $oc_accion->tipo = 'anulada';
      $oc_accion->observacion = $request->comentario;
      $oc_accion->user_id = auth()->user()->id;
      if (!$oc_accion->save()) {
        notify()->error("Hubo un error al guardar la acción de la orden de compra. Por favor, inténtalo nuevamente.", "Error: ", "topRight");
      }
      notify()->success("La orden de compra se anuló correctamente", "Éxito: ", "topRight");
    } else {
      notify()->error("Hubo un error al anular la orden de compra. Por favor, inténtalo nuevamente.", "Error: ", "topRight");
    }
    return redirect()->route('admin.orden-compra.index');
  }


  /**
   * Obtener información de una Orden de Compra de una base de operaciones (API)
   *
   * @return void
   */
  public function getOrdenCompraPorBase($id)
  {
    $array = ["estado" => false, "mensaje" => ""];

    $ordenes_compras = OrdenCompra::where('base_recibe_id', $id)
      ->whereIn('estado', ['aprobada', 'parcial']) //* TODO: El estado va a cambiar a aprobada o algo así cuando sumemos lógica -> Realizado.
      ->distinct()
      ->get();

    if (!empty($ordenes_compras)) {
      $array["estado"] = true;
      $array["mensaje"] = 'Se encontraron las Ordenes de Compra.';
      $array["respuesta"] = $ordenes_compras;
    } else {
      $array["estado"] = false;
      $array["mensaje"] = 'No se encontraron Ordenes de Compra.';
      $array["respuesta"] = '';
    }

    return response()->json([
      'estado' => $array["estado"],
      'mensaje' => $array["mensaje"],
      'respuesta' => $array["respuesta"]
    ]);
  }

  /**
   * Obtener información de una Orden de Compra (API)
   *
   * @return void
   */
  public function getOrdenCompra($id)
  {
    $array = ["estado" => false, "mensaje" => ""];

    $orden_compra = OrdenCompra::where('id', $id)
      ->with(['detalle' => function ($q) {
        return $q
          ->whereColumn('cantidad', '>', 'ingreso')
          ->orWhereNull('ingreso')
          ->with(['pieza']);
      }])
      ->first();

    if (!empty($orden_compra)) {
      $array["estado"] = true;
      $array["mensaje"] = 'Se encontró la Orden de Compra.';
      $array["respuesta"] = $orden_compra;
    } else {
      $array["estado"] = false;
      $array["mensaje"] = 'No se encontró la Orden de Compra.';
      $array["respuesta"] = '';
    }

    return response()->json([
      'estado' => $array["estado"],
      'mensaje' => $array["mensaje"],
      'respuesta' => $array["respuesta"]
    ]);
  }

  /**
   * Función para guardar las Ordenes de Compra recibidas
   *
   * @return \Illuminate\Http\Response
   */
  public function storeRecibidas(Request $request)
  {
    if (isset($request['detalle_oc']) && !empty($request['detalle_oc'])) {

      $mensajes = [];

      // Validar recibir y Crear un Movimiento
      foreach ($request['detalle_oc'] as $detalle) {
        if (isset($detalle['checkbox']) && $detalle['checkbox'] == 1) {

          $inventario = Inventario::where(['bases_operacion_id' => $request['base_operacion_id'], 'pieza_id' => $detalle['pieza_id']])->first();
          if (!$inventario) {
            $mensajes[] = 'La pieza N° ' . $detalle['nro_pieza'] . ' no existe en el inventario';
            continue;
          }

          $orden_detalle = OrdenCompraDetalle::find($detalle['detalle_id']);

          if ($orden_detalle->ingreso != null) {
            $orden_detalle->ingreso += $detalle['ingreso'];
          } else {
            $orden_detalle->ingreso = $detalle['ingreso'];
          }

          if ($orden_detalle->save()) {
            $orden = OrdenCompra::where('id', $request['orden_compra_id'])
            ->with(['accion' => fn($q) => $q->with('movimiento')])->first();
            foreach ($orden->accion as $value) {
              if ($value->tipo = 'parcial' or $value->tipo = 'recibida' ) {
                $detalle['orden_compra_accion_id'] = $value->id + 1;
              }
            }

            Movimiento::create([
              'pieza_id' => $detalle['pieza_id'],
              'base_operacion_id' => $request['base_operacion_id'],
              'inventario_id' => $inventario->id,
              'fecha' => new DateTime(),
              'cantidad' => $detalle['ingreso'],
              'precio_unitario' => $detalle['costo'],
              'ubicacion' => $inventario->ubicacion,
              'orden_compra_accion_id' => $detalle['orden_compra_accion_id'],
              'orden_compra_id' => $request['orden_compra_id'],
              'user_id' => auth()->user()->id,
            ]);
          };
        }
      }

      // Buscar orden y sus detalles para asignarle estado en orden_compra_accion segun el movimiento si es recibida o parcial.
      $orden = OrdenCompra::where('id', $request['orden_compra_id'])
        ->with(['detalle' => function ($q) {
          return $q ->whereColumn('cantidad', '>', 'ingreso');
        }]) ->first();

      if ($orden->detalle->isEmpty()) {   // accion para seguimiento cuando se recible oc_completa.
        $orden->estado = 'recibida';
        $oc_accion = new OrdenCompraAccion();
        $oc_accion->orden_compra_id = $orden->id;
        $oc_accion->tipo = 'recibida';
        $oc_accion->user_id = auth()->user()->id;
        if (!$oc_accion->save()) {
        notify()->error("Hubo un error al guardar la acción de la orden de compra. Por favor, inténtalo nuevamente.", "Error: ", "topRight");
        }
      } else {                            // accion para seguimiento cuando se recibe oc_parcial.
        $orden->estado = 'parcial';
        $oc_accion = new OrdenCompraAccion();
        $oc_accion->orden_compra_id = $orden->id;
        $oc_accion->tipo = 'parcial';
        $oc_accion->user_id = auth()->user()->id;
        if (!$oc_accion->save()) {
        notify()->error("Hubo un error al guardar la acción de la orden de compra. Por favor, inténtalo nuevamente.", "Error: ", "topRight");
        }
      }

      if ($orden->nro_factura == null) {
        $orden->nro_factura = $request['nro_factura'];
      }

      if ($orden->save()) {
        notify()->success("La orden de compra se generó correctamente", "Éxito: ", "topRight");
      }

      if (!empty($mensajes)) {
        foreach ($mensajes as $mensaje) {
          notify()->info($mensaje, "Info: ", "topRight");
        }
      }
    } else {
      notify()->error("Hubo un error al recibir la Orden de Compra. Por favor, inténtalo nuevamente.", "Error: ", "topRight");
    }

    return redirect()->route('admin.orden-compra.index');
  }

  /**
   * Generar PDF para impresión de la OC
   *
   * @return void
   */
  public function generarPDF($id) {
    $orden = OrdenCompra::withTrashed()->where('id', $id)->with(['empresa', 'proveedor', 'base_recibe', 'base_emite', 'detalle.pieza.unidadMedida', 'accion'])->first();
    $user = User::where('id', Auth::id())->first();
    $fecha = new DateTime();

    ($orden->fecha_entrega) ? $fecha_entrega = new DateTime($orden->fecha_entrega) : $fecha_entrega = null;

    $data = [
        'id' => $orden->id,
        'user' => $user->name,
        'fecha_entega' => $fecha_entrega->format('d/m/Y'),
        'nombre_proveedor' => $orden->proveedor->nombre,
        'cuit_proveedor' => $orden->proveedor->cuit,
        'entregar_en' => $orden->base_recibe->nombre,
        'fecha' => $fecha->format('d/m/Y H:i'),
        'fecha_entrega' => $fecha_entrega? $fecha_entrega->format('d/m/Y') : '',
        'observacion' => $orden->observacion,
        'empresa_nombre' => $orden->empresa->nombre,
        'empresa_img' => asset('/assets/img/logo_empresas/'.$orden->empresa->img),
        'detalles' => $orden->detalle,
    ];
    $pdf = PDF::loadView('/admin/orden_compra/template_pdf/template', $data);

    return $pdf->stream('OC_'.$orden->id.'.pdf');
    //* Para descargar
    //return $pdf->download('OT_'.$orden->id.'.pdf');
  }
}
