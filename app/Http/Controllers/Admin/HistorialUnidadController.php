<?php

namespace App\Http\Controllers\Admin;

use App\HistorialUnidad;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\PlanUnidad;
use App\Unidad;
use DateTime;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HistorialUnidadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $historiales = collect();
        $unidades = Unidad::whereHas('historiales')->whereHas('vinculaciones')->get();
        $historiales_grafico = null;
        $calcular_promedios = null;
        $plan = PlanUnidad::where('unidad_id', $request->unidad_id)->first();

        $id_km_inicial = 0;
        $hu = HistorialUnidad::where('unidad_id', $request->unidad_id)->first();        
        if($hu) {
         $id_km_inicial = $hu->id;
        }

        $desde = $request['desde'] ?? "";
        $hasta = $request['hasta'] ?? "";
        $unidad_id = $request->unidad_id;

        // historial
          $historiales = HistorialUnidad::where('unidad_id', $request->unidad_id)->with(['orden_trabajo' => function ($q) {
            return $q->withTrashed()->with('base_operacion');
          }]);

          if(!empty($request->desde) && !empty($request->hasta)) {
            $historiales = $historiales->whereBetween('created_at', [$request->desde, $request->hasta]);

            if($plan) {
              $historiales = $historiales->whereDate('created_at','>=',$plan->fecha);
            }              
          }
          
          $historiales = $historiales->orderBy('created_at', 'desc')->orderBy('kilometraje', 'desc');
          $historiales = $historiales->paginate(10);
        // fin historial

        // historial grafico
          $historiales_grafico = HistorialUnidad::where('unidad_id', $request->unidad_id);

          if(!empty($request->desde) && !empty($request->hasta)) {
            $historiales_grafico = $historiales_grafico->whereBetween('created_at', [$request->desde, $request->hasta]);

            if($plan) {
              $historiales_grafico = $historiales_grafico->whereDate('created_at','>=',$plan->fecha);
            }                                                                     
          }            
              
          $historiales_grafico = $historiales_grafico->orderBy('created_at')
                                                     ->orderBy('kilometraje', 'desc')
                                                     ->get();
        // fin historial grafico

        // historial promedios
          $historiales_promedios = HistorialUnidad::where('unidad_id', $request->unidad_id);

          if(!empty($request->desde) && !empty($request->hasta)) {
            $historiales_promedios = $historiales_promedios->whereBetween('created_at', [$request->desde, $request->hasta]);

            if($plan) {
              $historiales_promedios = $historiales_promedios->whereDate('created_at','>=',$plan->fecha);
            }                                 
          }            
          
          $historiales_promedios = $historiales_promedios->select(array('historiales_unidades.*',
                                                              DB::raw("DATE_FORMAT(created_at, '%m-%Y') new_date"),  DB::raw('YEAR(created_at) year, MONTH(created_at) month')
                                                          ))
                                                          ->orderBy('created_at')
                                                          ->orderBy('kilometraje', 'desc')
                                                          ->get();
                    
          if ($historiales_promedios != "") {
              /* Voy a hacer un array de esta manera, por año y luego mes sumo el total y despues hago count y saco el promedio. */
              //$calcular_promedios[anio][mes] = totalKmMes
              $calcular_promedios = [];                            
              $plan_unidad = PlanUnidad::where('unidad_id',$request->unidad_id)->first();           
              $max_tmp = empty($plan_unidad) ? 0 : $plan_unidad->km_inicial;

              foreach($historiales_promedios as $key => $historial) {
                if($historial->id != $id_km_inicial) {
                  if(!isset($calcular_promedios[$historial->year])) {
                    $calcular_promedios[$historial->year] = [];
                  }
                  if(!isset($calcular_promedios[$historial->year][$historial->month])) {
                    $calcular_promedios[$historial->year][$historial->month]['total'] = 0;
                    $calcular_promedios[$historial->year][$historial->month]['cantidad'] = 0;
                    $calcular_promedios[$historial->year][$historial->month]['min'] = empty($max_tmp) ? 999999999 : $max_tmp;
                    $calcular_promedios[$historial->year][$historial->month]['max'] = 0;                  
                  }
                  $calcular_promedios[$historial->year][$historial->month]['total'] = $calcular_promedios[$historial->year][$historial->month]['total'] + $historial->kilometraje;                  
                  $calcular_promedios[$historial->year][$historial->month]['cantidad'] +=  1;
                    
                  if($calcular_promedios[$historial->year][$historial->month]['min'] > $historial->kilometraje)
                    $calcular_promedios[$historial->year][$historial->month]['min'] = $historial->kilometraje;

                  if($calcular_promedios[$historial->year][$historial->month]['max'] < $historial->kilometraje) {
                    $calcular_promedios[$historial->year][$historial->month]['max'] = $historial->kilometraje;
                    $max_tmp = $historial->kilometraje;
                  }
                }
              }
          }
        // fin historial promedios

        // promedio mensual
          $calcular_promedios_simple = [];              
          foreach ($calcular_promedios as $anio => $meses) {
            if($meses) {
              foreach ($meses as $key => $value) {
                if(!isset($calcular_promedios_simple[$anio.'_'.$key])) {
                  $calcular_promedios_simple[$anio.'_'.$key] = 0;
                }
                $calcular_promedios_simple[$anio.'_'.$key] = $value['total'] / $value['cantidad'];        
              }
            }
          }
          
          $calcular_promedios_simple_tmp = [];
          foreach ($calcular_promedios_simple as $key => $value) {
            $calcular_promedios_simple_tmp[] = $value;
          }
          $calcular_promedios_simple = $calcular_promedios_simple_tmp;          

          $cant_meses = 0;
          $calcular_promedios_estimativo = [];
          foreach ($calcular_promedios_simple as $key => $value) {
            if($cant_meses == 0) {
              $calcular_promedios_estimativo[] = $plan->estimativo;
            } else if ($cant_meses == 1) {
              $calcular_promedios_estimativo[] = $calcular_promedios_simple[0];
            } else if ($cant_meses == 2) { 
              $calcular_promedios_estimativo[] = $calcular_promedios_simple[1];
            } else {                            
              $promedio_3_meses = $calcular_promedios_simple[$cant_meses-1] + $calcular_promedios_simple[$cant_meses-2] + $calcular_promedios_simple[$cant_meses-3];              
              $calcular_promedios_estimativo[] = $promedio_3_meses / 3;
            }
            $cant_meses = $cant_meses + 1;              
          }                  
        // fin promedio mensual        

        // promedio total
          $promedio = $this->getPromedioByUnidad($request->unidad_id);          
        // fin promedio

        return view('admin.historial_unidad.index', compact('unidades', 'desde', 'hasta', 'historiales', 'unidad_id', 'historiales_grafico', 'calcular_promedios', 'calcular_promedios_estimativo', 'promedio', 'id_km_inicial'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $unidades = Unidad::whereHas('historiales', function($q) {
          return $q->where('created_at', '>=', HistorialUnidad::getValidacionMeses());
        })
        ->whereHas('vinculaciones')
        ->orderBy('num_interno', 'asc')
        ->get();

        $historiales = HistorialUnidad::where('created_at', '>=', HistorialUnidad::getValidacionMeses())
        ->orderBy('created_at', 'desc')
        ->get();

        $agrupados = $historiales->groupBy('unidad_id');
        
        $unidades->each(function($item){
          $item["promedio_ok"] = $this->getPromedioByUnidad($item->id);        
        });        

        return view('admin.historial_unidad.create', compact('unidades', 'agrupados'));
    }

    public function getPromedioByUnidad($unidad_id) {        
        // historial promedios
          $historiales_promedios = HistorialUnidad::where('unidad_id', $unidad_id);
                    
          $historiales_promedios = $historiales_promedios->select(array('historiales_unidades.*',
                                                              DB::raw("DATE_FORMAT(created_at, '%m-%Y') new_date"),  DB::raw('YEAR(created_at) year, MONTH(created_at) month')
                                                          ))
                                                          ->orderBy('created_at')
                                                          ->orderBy('kilometraje', 'desc')
                                                          ->get();

          if ($historiales_promedios != "") {
              /* Voy a hacer un array de esta manera, por año y luego mes sumo el total y despues hago count y saco el promedio. */
              //$calcular_promedios[anio][mes] = totalKmMes
              $calcular_promedios = [];                            
              foreach($historiales_promedios as $historial) {
                if(!isset($calcular_promedios[$historial->year])) {
                  $calcular_promedios[$historial->year] = [];
                }
                if(!isset($calcular_promedios[$historial->year][$historial->month])) {
                  $calcular_promedios[$historial->year][$historial->month]['total'] = 0;
                  $calcular_promedios[$historial->year][$historial->month]['cantidad'] = 0;
                }
                $calcular_promedios[$historial->year][$historial->month]['total'] = $calcular_promedios[$historial->year][$historial->month]['total'] + $historial->kilometraje;
                $calcular_promedios[$historial->year][$historial->month]['cantidad'] +=  1;
              }
          }
        // fin historial promedios

        // promedio mensual
          $calcular_promedios_simple = [];              
          foreach ($calcular_promedios as $anio => $meses) {
            if($meses) {
              foreach ($meses as $key => $value) {
                if(!isset($calcular_promedios_simple[$anio.'_'.$key])) {
                  $calcular_promedios_simple[$anio.'_'.$key] = 0;
                }
                $calcular_promedios_simple[$anio.'_'.$key] = $value['total'] / $value['cantidad'];        
              }
            }
          }
          
          $calcular_promedios_simple_tmp = [];
          foreach ($calcular_promedios_simple as $key => $value) {
            $calcular_promedios_simple_tmp[] = $value;
          }
          $calcular_promedios_simple = $calcular_promedios_simple_tmp;          

          $cant_meses = 0;
          $calcular_promedios_estimativo = [];
          foreach ($calcular_promedios_simple as $key => $value) {
            if($cant_meses == 0) {
              $calcular_promedios_estimativo[] = PlanUnidad::where('unidad_id', $unidad_id)->first()->estimativo;
            } else if ($cant_meses == 1) {
              $calcular_promedios_estimativo[] = $calcular_promedios_simple[0];
            } else if ($cant_meses == 2) { 
              $calcular_promedios_estimativo[] = $calcular_promedios_simple[1];
            } else {                            
              $promedio_3_meses = $calcular_promedios_simple[$cant_meses-1] + $calcular_promedios_simple[$cant_meses-2] + $calcular_promedios_simple[$cant_meses-3];              
              $calcular_promedios_estimativo[] = $promedio_3_meses / 3;
            }
            $cant_meses = $cant_meses + 1;              
          }                            
        // fin promedio mensual     

        // promedio final        
          $promedio_ok = 0;

          /*
          if($calcular_promedios_estimativo) {
            foreach ($calcular_promedios_estimativo as $key => $value) {
              $promedio_ok = $promedio_ok + $value;
            }
            $promedio_ok = $promedio_ok / count($calcular_promedios_estimativo);
          }          
          */

          if($calcular_promedios_estimativo) {
            $promedio_ok = $calcular_promedios_estimativo[count($calcular_promedios_estimativo)-1];
          }
        // fin promedio final
        
        return round($promedio_ok,2);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $errores = [];
        if ($request->has("historial")) {

            // validaciones generales
              $now = Carbon::now()->format('Y-m-d H:i');
              $valid = true;
              foreach ($request->historial as $unidad_id => $datos) {              
                $fechaLectura = new DateTime($datos["fecha"]);
                $fechaLectura = $fechaLectura->format("Y-m-d H:i");
                              
                if($fechaLectura > $now) {
                  notify()->error('No puede agregar una lectura con fecha futura.', "Error:", "topRight");

                  return back();
                }
                
                if ($datos['kilometraje'] != null) {
                  $valid = false;
                }
              }
              if($valid) {
                notify()->info('No se cargaron valores de lectura.', "Info:", "topRight");

                return back();
              }            
            // fin
            
            foreach ($request->historial as $unidad_id => $datos) {
                if ($datos['kilometraje'] != null) {
                    if ($datos['estado'] == "true") {
                        if(isset($datos["fecha"])) {
                            $fecha = new DateTime($datos["fecha"]);
                        } else {
                            $fecha = new DateTime();
                        }
                        $fecha = $fecha->format("Y-m-d H:i:s");
                        $historial = HistorialUnidad::create([
                            'unidad_id' => $unidad_id,
                            'kilometraje' => $datos["kilometraje"],
                            'created_at' => $fecha,
                        ]);

                        if (!$historial->save()) {
                            $errores[] = $datos["num_interno"];
                        }
                    }
                }
            }
        } else {
            notify()->error('No procesaron las unidades. Por favor, inténtelo nuevamente.', "Error:", "topRight");

            //return redirect($this->referer());
            return back();
        }

        if (empty($errores)) {
            notify()->success("El historial de unidad se agregó correctamente","Éxito: ","topRight");

            return redirect()->route('admin.historial');
        } else {
            notify()->error("Hubo un error al guardar las siguientes unidades: " . implode(',', $errores) . ". Por favor, inténtalo nuevamente.","Error: ","topRight");

            //return redirect($this->referer());
            return back();
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
        //
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
     * Busca los ultimos 4 historiales de una unidad.
     *
     * @param  int  $unidad_id
     * @return \Illuminate\Http\Response
     */
    public function getHistoriales($unidad_id) {
        $historiales = HistorialUnidad::where('unidad_id', $unidad_id)
            ->orderByDesc('created_at')
            ->orderByDesc('kilometraje')
            ->take(4)
            ->where('created_at', '>=', HistorialUnidad::getValidacionMeses())
            ->get();
        return $historiales;
    }

    public function editarHistoriales(Request $request) {

        $array = ["estado" => false, "mensaje" => ""];

        $historial_1 = HistorialUnidad::find($request->historial_1);
        $historial_1->kilometraje = $request->kilometraje_1;

        $errores_1 = false;
        $errores_2 =  false;
        $errores_3 = false;

        $historiales = [];

        if (!$historial_1->save()) {
            $errores_1 = true;
        }
        array_push($historiales, $historial_1);

        if($request->historial_2) {
            $historial_2 = HistorialUnidad::find($request->historial_2);
            $historial_2->kilometraje = $request->kilometraje_2;
    
            if (!$historial_2->save()) {
                $errores_2 = true;
            }
            array_push($historiales, $historial_2);
        }

        if($request->historial_3) {
            $historial_3 = HistorialUnidad::find($request->historial_3);
            $historial_3->kilometraje = $request->kilometraje_3;
    
            if (!$historial_3->save()) {
                $errores_3 =  true;
            }
            array_push($historiales, $historial_3);
        }

        if(!$errores_1 && !$errores_2 && !$errores_3) {
            $array["estado"] = true;
            $array["mensaje"] = "Los historiales se editaron correctamente.";
        } else {
            $array["mensaje"] = "Se produjo un error al editar los historiales. Por favor, inténtelo nuevamente.";
        }

        return response()->json(["respuesta" => $array, "historiales" => $historiales]);
    }

    /**
     * Busca por fecha el historial correspondiente a una unidad
     * 
     * @param  int  $unidad_id
     * @param  datetime  $fecha
     * @return \Illuminate\Http\Response
     */
    public function getHistorialesFecha($unidad_id = null, $fecha = null) {

      $array = ["estado" => false, "mensaje" => ""];

      $fecha_dmy = Carbon::parse($fecha);
      $fecha_dmy_0 = Carbon::parse($fecha)->startOfDay();
      $fecha_dmy_23 = Carbon::parse($fecha)->endOfDay();

      $historiales = HistorialUnidad::where('unidad_id', $unidad_id)
      ->whereDate('created_at', '>=',  $fecha_dmy_0)
      ->whereDate('created_at','<=', $fecha_dmy_23)
      ->orderBy('created_at', 'desc')
      ->first();

      $fecha_dmy = Carbon::parse($fecha)->subDays(1);
      $fecha_dmy_23_anterior = Carbon::parse($fecha)->subDays(1)->endOfDay();

      $historial_anterior = HistorialUnidad::where('unidad_id', $unidad_id)
      ->whereDate('created_at','<=', $fecha_dmy_23_anterior)
      ->orderBy('created_at', 'desc')
      ->first();

      //! Comento esto porque Aye pidio que saque esta validacion de momento. 
      //! Hay que ver como quieren hacer para editar una lectura que ya tiene una lectura siguiente
      /* $historial_siguiente = HistorialUnidad::where('unidad_id', $unidad_id)
      ->whereDate('created_at','>=', $fecha_dmy_23)
      ->orderBy('created_at', 'desc')
      ->first();

      if($historial_anterior == $historial_siguiente){
        $historial_siguiente = null;
      } */
      $historial_siguiente = null;

      if(!empty($historiales) || !empty($historial_anterior)) {
        $array["estado"] = true;
        $array["mensaje"] = 'Se encontró el historial.';
      } else {
        $array["mensaje"] = 'No se encontró ningun historial para la fecha de inicio.';
      }

      return response()->json(["respuesta" => $array, "historiales" => $historiales, "historial_anterior" => $historial_anterior, "historial_siguiente" => $historial_siguiente]);

    }
}
