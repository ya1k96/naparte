<?php

namespace App\Http\Controllers\Admin;

use App\MantenimientoRutinario;
use App\Componente;
use App\HistorialUnidad;
use App\Http\Controllers\Controller;
use App\Plan;
use App\PlanUnidad;
use App\Tarea;
use App\RecursoActividad;
use App\Unidad;
use DateInterval;
use DateTime;
use Illuminate\Http\Request;

class VinculacionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $vinculaciones = PlanUnidad::with(['plan', 'unidad'])
            ->whereHas('unidad')
            ->orderBy('id', 'desc')
            ->paginate(10);
        $planes = Plan::all();
        $buscar = $request['unidad'] ?? "";

        if (!empty($request['unidad'])) {
            $vinculaciones = PlanUnidad::whereHas('unidad', function ($q) use ($request) {
                $q->where('num_interno', 'like', '%' . $request['unidad'] . '%');
            })->orderBy('id', 'desc')
                ->paginate(10);
        }

        if (!empty($request['plan_id'])) {
            $vinculaciones = PlanUnidad::with('plan')
                ->where('plan_id', '=', $request['plan_id'])
                ->orderBy('id', 'desc')
                ->paginate(10);
        }

        return view('admin.vinculaciones.index', compact('vinculaciones', 'planes', 'buscar'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $planes = Plan::all();
        $unidades = Unidad::all();
        $componentes = Componente::with(['subcomponentes', 'tareas'])->get();
        //Solo permitir vincular unidades que no tengan ni hayan tenido vinculaciones.
        $unidades = Unidad::whereDoesntHave('vinculaciones', function ($q) {
            $q->withTrashed();
        })->get();

        return view('admin.vinculaciones.create', compact('planes', 'unidades', 'componentes'));
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
            'unidad' => 'required',
            'plan' => 'required',
            'km_inicial' => 'required',
            'fecha' => 'required',
            'estimativo' => 'required'
        ]);

        $existe = PlanUnidad::where('plan_id', $request->plan)
            ->where('unidad_id', $request->unidad)
            ->first();

        if ($existe == null) {
            $vinculacion = PlanUnidad::create([
                'plan_id' => $request->plan,
                'unidad_id' => $request->unidad,
                'km_inicial' => $request->km_inicial,
                'fecha' => $request->fecha,
                'estimativo' =>  $request->estimativo
            ]);

            if ($vinculacion->save()) {
                HistorialUnidad::create([
                    'unidad_id' => $request->unidad,
                    'kilometraje' => $request->km_inicial,
                    'created_at' => $request->fecha
                ]);

                //Generar los mantenimientos para esta unidad según el plan
                if(isset($request->modifica) && $request->modifica == 'si') {
                  $mantenimiento_rutinario_inicial = [];

                  foreach($request->all() as $key => $value) {
                    if(str_contains($key, "mr_fecha_")) {
                      $arr_index = explode('_',str_replace('mr_fecha_','',$key));
                      $tarea = Tarea::find($arr_index[1]);

                      $fecha_prox = new DateTime($value);
                      $fecha_prox->add(new DateInterval('P'.$tarea->dias.'D'));

                      $mantenimiento_rutinario_inicial[] = array(
                        'frecuencia' => $tarea->frecuencia,
                        'frecuencia_fecha' => $tarea->dias,
                        'fecha' => $value,
                        'prox_fecha' => $fecha_prox,
                        'frecuencia_km' => null,
                        'km' => null,
                        'prox_km' => null,
                        'componente_id' => $arr_index[0],
                        'tarea_id' => $tarea->id
                      );
                    }
                    if(str_contains($key, "mr_km_")) {
                      $arr_index = explode('_',str_replace('mr_km_','',$key));
                      $tarea = Tarea::find($arr_index[1]);

                      $mantenimiento_rutinario_inicial[] = array(
                        'frecuencia' => $tarea->frecuencia,
                        'frecuencia_fecha' => null,
                        'fecha' => null,
                        'prox_fecha' => null,
                        'frecuencia_km' => $tarea->kilometros,
                        'km' => $value,
                        'prox_km' => (float) $value + $tarea->kilometros,
                        'componente_id' => $arr_index[0],
                        'tarea_id' => $tarea->id
                      );
                    }
                    if(str_contains($key, "mr_c_fecha_") || str_contains($key, "mr_c_km_")) {
                      if(str_contains($key, "mr_c_fecha_")) {
                        $arr_index = explode('_',str_replace('mr_c_fecha_','',$key));
                        $tarea = Tarea::find($arr_index[1]);

                        $fecha_prox = new DateTime($value);
                        $fecha_prox->add(new DateInterval('P'.$tarea->dias.'D'));

                        $existe = false;
                        foreach ($mantenimiento_rutinario_inicial as $k => $mri) {
                          if($mri["componente_id"] == $arr_index[0] && $mri["tarea_id"] == $tarea->id) {
                            $mantenimiento_rutinario_inicial[$k]["frecuencia"] = $tarea->frecuencia;
                            $mantenimiento_rutinario_inicial[$k]["frecuencia_fecha"] = $tarea->dias;
                            $mantenimiento_rutinario_inicial[$k]["fecha"] = $value;
                            $mantenimiento_rutinario_inicial[$k]["prox_fecha"] = $fecha_prox;

                            $existe = true;
                          }
                        }

                        if(!$existe) {
                          $mantenimiento_rutinario_inicial[] = array(
                            'frecuencia' => $tarea->frecuencia,
                            'frecuencia_fecha' => $tarea->dias,
                            'fecha' => $value,
                            'prox_fecha' => $fecha_prox,
                            'frecuencia_km' => null,
                            'km' => null,
                            'prox_km' => null,
                            'componente_id' => $arr_index[0],
                            'tarea_id' => $tarea->id
                          );
                        }
                      }
                      if(str_contains($key, "mr_c_km_")) {
                        $arr_index = explode('_',str_replace('mr_c_km_','',$key));
                        $tarea = Tarea::find($arr_index[1]);

                        $existe = false;
                        foreach ($mantenimiento_rutinario_inicial as $k => $mri) {
                          if($mri["componente_id"] == $arr_index[0] && $mri["tarea_id"] == $tarea->id) {
                            $mantenimiento_rutinario_inicial[$k]["frecuencia"] = $tarea->frecuencia;
                            $mantenimiento_rutinario_inicial[$k]["frecuencia_km"] = $tarea->kilometros;
                            $mantenimiento_rutinario_inicial[$k]["km"] = $value;
                            $mantenimiento_rutinario_inicial[$k]["prox_km"] = (float) $value + $tarea->kilometros;

                            $existe = true;
                          }
                        }

                        if(!$existe) {
                          $mantenimiento_rutinario_inicial[] = array(
                            'frecuencia' => $tarea->frecuencia,
                            'frecuencia_fecha' => null,
                            'fecha' => null,
                            'prox_fecha' => null,
                            'frecuencia_km' => $tarea->kilometros,
                            'km' => $value,
                            'prox_km' => (float) $value + $tarea->kilometros,
                            'componente_id' => $arr_index[0],
                            'tarea_id' => $tarea->id
                          );
                        }
                      }
                    }
                  }

                  foreach ($mantenimiento_rutinario_inicial as $key => $value) {
                    $mantenimiento = MantenimientoRutinario::create([
                        'unidad_id' => $request->unidad,
                        'componente_id' => $value["componente_id"],
                        'tarea_id' => $value["tarea_id"],
                        'ult_mantenimiento' => $value["km"],
                        'ult_mantenimiento_fecha' => $value["fecha"],
                        'frecuencia' => $value["frecuencia_km"],
                        'frecuencia_dias' => $value["frecuencia_fecha"],
                        'prox_mantenimiento' => $value["prox_km"],
                        'prox_mantenimiento_fecha' => $value["prox_fecha"],
                        'created_at' => new DateTime($request->fecha),
                    ]);
                  }

                } else {
                  $componentes = Componente::with(['subcomponentes', 'tareas'])
                  ->where('plan_id', $vinculacion->plan_id)->get();

                  if(!empty($componentes)) {
                      foreach($componentes as $componente) {
                          foreach($componente->tareas as $tarea) {
                              $ult_mant = null;
                              $ult_mant_fecha = null;
                              $prox_mantenimiento = null;
                              $prox_mantenimiento_fecha = null;
                              $frecuencia = null;
                              $frecuencia_dias = null;
                              if($tarea->frecuencia == 'kms') {
                                  $ult_mant = $request->km_inicial;
                                  $frecuencia = $tarea->kilometros;
                                  $prox_mantenimiento = $ult_mant + $frecuencia;
                              }
                              if($tarea->frecuencia == 'dias') {
                                  $ult_mant_fecha = new DateTime($request->fecha);
                                  $frecuencia_dias = $tarea->dias;
                                  $fecha_prox = new DateTime($request->fecha);
                                  $fecha_prox->add(new DateInterval('P'.$tarea->dias.'D'));
                                  $prox_mantenimiento_fecha = $fecha_prox;
                              }
                              if($tarea->frecuencia == 'combinado') {
                                  $ult_mant = $request->km_inicial;
                                  $ult_mant_fecha = new DateTime($request->fecha);
                                  $frecuencia = $tarea->kilometros;
                                  $frecuencia_dias = $tarea->dias;
                                  $fecha_prox = new DateTime($request->fecha);
                                  $fecha_prox->add(new DateInterval('P'.$tarea->dias.'D'));
                                  $prox_mantenimiento = $ult_mant + $frecuencia;
                                  $prox_mantenimiento_fecha = $fecha_prox;
                              }
                              $mantenimiento = MantenimientoRutinario::create([
                                  'unidad_id' => $request->unidad,
                                  'componente_id' => $componente->id,
                                  'tarea_id' => $tarea->id,
                                  'ult_mantenimiento' => $ult_mant,
                                  'ult_mantenimiento_fecha' => $ult_mant_fecha,
                                  'frecuencia' => $frecuencia,
                                  'frecuencia_dias' => $frecuencia_dias,
                                  'prox_mantenimiento' => $prox_mantenimiento,
                                  'prox_mantenimiento_fecha' => $prox_mantenimiento_fecha,
                                  'created_at' => new DateTime($request->fecha),
                              ]);
                          }
                      }
                  }
                }

                notify()->success('La vinculación se realizó correctamente', 'Éxito: ', 'topRight');

                if ($request->action == 'guardar_y_nuevo') {
                    return redirect()->back()->with('vinculacion', $vinculacion);
                } elseif ($request->action == 'guardar') {
                    return redirect()->route('admin.vinculaciones');
                }
            }
        } else {
            notify()->error('Ya existe una vinculación de la Unidad con el Plan de mantenimiento. Por favor, compruebe los datos', 'Error: ', 'topRight');

            return redirect()->back()->with('vinculacion', $existe);
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
        $vinculacion = PlanUnidad::with(['unidad', 'plan'])
            ->where('id', $id)
            ->get();
        $unidades = Unidad::all();
        $planes = Plan::all();

        return view('admin.vinculaciones.edit', compact('vinculacion', 'unidades', 'planes'));
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
        $request->validate([
            'unidad' => 'required',
            'plan' => 'required',
            'km_inicial' => 'required',
            'fecha' => 'required',
            'estimativo' => 'required'
        ]);

        $vinculacion = PlanUnidad::find($id);

        $vinculacion->plan_id =$request->plan;
        $vinculacion->unidad_id = $request->unidad;
        $vinculacion->km_inicial = $request->km_inicial;
        $vinculacion->fecha = $request->fecha;
        $vinculacion->estimativo = $request->estimativo;

        if ($vinculacion->save()) {
            notify()->success('La vinculación se actualizó correctamente', 'Éxito: ', 'topRight');

            return redirect()->route('admin.vinculaciones');
        } else {
            notify()->error('Hubo un error en actualizar la vinculación. Por favor, inténtelo nuevamente', 'Error: ', 'topRight');

            return redirect($this->referer());
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($unidad_id)
    {
        $vinculacion = PlanUnidad::where('unidad_id', $unidad_id)->first();

        if($vinculacion->delete()) {
            $recursos = RecursoActividad::where('unidad_id', $unidad_id)->delete();
            notify()->success("La vinculación se eliminó correctamente", "Éxito:", "topRight");

            return redirect()->route('admin.vinculaciones');
        } else {
            notify()->error("Hubo un error al eliminar la vinculación. Por favor, inténtelo nuevamente.", "Error:", "topRight");

            return redirect()->route('admin.vinculaciones');
        }
    }
}
