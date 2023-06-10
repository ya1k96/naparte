<?php

namespace App\Http\Controllers\Admin;

use App\Componente;
use App\Dia;
use App\Especialidad;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Kilometro;
use App\MantenimientoRutinario;
use App\OrdenesTrabajo;
use App\Plan;
use App\Tarea;
use App\Unidad;
use DateInterval;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use DateTime;

class TareaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($componente_id = null)
    {
        $especialidades = Especialidad::all();
        $kms = Kilometro::all();
        $dias = Dia::all();

        if (!empty($componente_id)) {
            # Se obtiene el componente y se lo pasa a la vista
            $componente = Componente::where('id', $componente_id)->first();
        }

        return view('admin.tareas.create', compact('especialidades', 'componente', 'kms', 'dias'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $componente = json_decode($request->componente);

        $request->validate([
            'descripcion' => 'required',
            'tipo_frecuencia' => 'required',
            'especialidad' => 'required'
        ]);

        if ($request->tipo_frecuencia == 'combinado') {
            $request->validate([
                'kms' => 'required',
                'dias' => 'required'
            ]);

            $tarea = Tarea::create([
                'componente_id' => $componente->id,
                'descripcion' => $request->descripcion,
                'frecuencia' => $request->tipo_frecuencia,
                'dias' => $request->dias,
                'kilometros' => $request->kms,
                'especialidad_id' => $request->especialidad,
                'observaciones' => $request->observaciones
            ]);
        }

        if ($request->tipo_frecuencia == 'dias') {
            $request->validate([
                'dias' => 'required'
            ]);

            $tarea = Tarea::create([
                'componente_id' => $componente->id,
                'descripcion' => $request->descripcion,
                'frecuencia' => $request->tipo_frecuencia,
                'dias' => $request->dias,
                'especialidad_id' => $request->especialidad,
                'observaciones' => $request->observaciones
            ]);
        }

        if ($request->tipo_frecuencia == 'kms'){
            $request->validate([
                'kms' => 'required'
            ]);

            $tarea = Tarea::create([
                'componente_id' => $componente->id,
                'descripcion' => $request->descripcion,
                'frecuencia' => $request->tipo_frecuencia,
                'kilometros' => $request->kms,
                'especialidad_id' => $request->especialidad,
                'observaciones' => $request->observaciones
            ]);
        }

        if ($tarea->save()) {
            notify()->success("La tarea se agregó correctamente", "Éxito:", "topRight");

            /* Busco el plan, si tiene vinculaciones tengo que generar el mantenimiento de esta tarea para las unidades ya vinculadas */
            $plan = Plan::whereHas('componentes', function ($query) use ($componente) {
                $query->where('id', $componente->id);
            })->whereHas('vinculaciones')->with(['vinculaciones', 'vinculaciones.unidad.historiales' => function($q) {
                $q->orderBy('created_at', 'desc')
                ->orderBy('kilometraje', 'desc');
            }])->first();

            if($plan) {
                foreach($plan->vinculaciones as $vinculacion) {
                    if (empty($vinculacion->unidad))
                        continue;
                    $ult_mant = null;
                    $ult_mant_fecha = null;
                    $prox_mantenimiento = null;
                    $prox_mantenimiento_fecha = null;
                    $frecuencia = null;
                    $frecuencia_dias = null;
                    /* Le pongo como kilometraje o fecha la del último historial registrado de la unidad. */
                    if($tarea->frecuencia == 'kms') {
                        $ult_mant = $vinculacion->unidad->historiales[0]->kilometraje;
                        $frecuencia = $tarea->kilometros;
                        $prox_mantenimiento = $ult_mant + $frecuencia;
                    }
                    if($tarea->frecuencia == 'dias') {
                        $ult_mant_fecha = new DateTime($vinculacion->unidad->historiales[0]->created_at);
                        $frecuencia_dias = $tarea->dias;
                        $fecha_prox = new DateTime($vinculacion->unidad->historiales[0]->created_at);
                        $fecha_prox->add(new DateInterval('P'.$tarea->dias.'D'));
                        $prox_mantenimiento_fecha = $fecha_prox;
                    }
                    if($tarea->frecuencia == 'combinado') {
                        $ult_mant = $vinculacion->unidad->historiales[0]->kilometraje;
                        $ult_mant_fecha = new DateTime($vinculacion->unidad->historiales[0]->created_at);
                        $frecuencia = $tarea->kilometros;
                        $frecuencia_dias = $tarea->dias;
                        $fecha_prox = new DateTime($vinculacion->unidad->historiales[0]->created_at);
                        $fecha_prox->add(new DateInterval('P'.$tarea->dias.'D'));
                        $prox_mantenimiento = $ult_mant + $frecuencia;
                        $prox_mantenimiento_fecha = $fecha_prox;
                    }
                    $mantenimiento = MantenimientoRutinario::create([
                        'unidad_id' => $vinculacion->unidad->id,
                        'componente_id' => $componente->id,
                        'tarea_id' => $tarea->id,
                        'ult_mantenimiento' => $ult_mant,
                        'ult_mantenimiento_fecha' => $ult_mant_fecha,
                        'frecuencia' => $frecuencia,
                        'frecuencia_dias' => $frecuencia_dias,
                        'prox_mantenimiento' => $prox_mantenimiento,
                        'prox_mantenimiento_fecha' => $prox_mantenimiento_fecha,
                        'created_at' => new DateTime($vinculacion->unidad->historiales[0]->created_at),
                    ]);
                }
            }

            if ($request->get('action') == 'guardar_y_nuevo') {
                return redirect()->route('admin.tarea.subcomponente', $componente->id);
            } elseif ($request->get('action') == 'guardar') {
                return redirect()->route('admin.plan-mantenimiento-preventivo.edit', $componente->plan_id);
            }
        } else {
            notify()->error("Hubo un error al guardar la tarea. Por favor, inténtelo nuevamente", "Error:", "topRight");

            return redirect($this->referer());
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
    public function edit($id, $componente_id = null)
    {
        $tarea = Tarea::find($id);
        $tipo_frecuencia = ['Días' => 'dias', 'Kilómetros' => 'kms', 'Combinado' => 'combinado'];
        $kms = Kilometro::all();
        $dias = Dia::all();
        $especialidades = Especialidad::all();

        return view('admin.tareas.edit', compact('tarea', 'tipo_frecuencia', 'especialidades', 'kms', 'dias'));
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
            'descripcion' => 'required',
            'tipo_frecuencia' => 'required',
            'especialidad' => 'required'
        ]);

        $tarea = Tarea::find($id);

        $especialidad_anterior = $tarea->especialidad_id;
        $tarea_km_anterior = $tarea->kilometros;
        $tarea_dias_anterior = $tarea->dias;

        if ($request->tipo_frecuencia == 'combinado') {
            $request->validate([
                'kms' => 'required',
                'dias' => 'required'
            ]);

            $tarea->descripcion = $request->descripcion;
            $tarea->frecuencia = $request->tipo_frecuencia;
            $tarea->kilometros = $request->kms;
            $tarea->dias = $request->dias;
            $tarea->especialidad_id = $request->especialidad;
            $tarea->observaciones = $request->observaciones;
        }

        if ($request->tipo_frecuencia == 'dias') {
            $request->validate([
                'dias' => 'required'
            ]);

            $tarea->descripcion = $request->descripcion;
            $tarea->frecuencia = $request->tipo_frecuencia;
            $tarea->kilometros = null;
            $tarea->dias = $request->dias;
            $tarea->especialidad_id = $request->especialidad;
            $tarea->observaciones = $request->observaciones;
        }

        if ($request->tipo_frecuencia == 'kms') {
            $request->validate([
                'kms' => 'required'
            ]);

            $tarea->descripcion = $request->descripcion;
            $tarea->frecuencia = $request->tipo_frecuencia;
            $tarea->kilometros = $request->kms;
            $tarea->dias = null;
            $tarea->especialidad_id = $request->especialidad;
            $tarea->observaciones = $request->observaciones;
        }

        //Obtengo por unidad los mantenimientos ordenados por fecha descendente
        $unidades = Unidad::whereHas('mantenimientos', function ($query) use($tarea) {
            $query->orderBy('created_at', 'DESC');
        })
        ->whereHas('mantenimientos.tarea', function ($query) use($tarea) {
            $query->where('tareas.id', $tarea->id);
        })
        ->with(['mantenimientos' => function($query) {
            $query->orderBy('created_at', 'DESC');
        },
        'mantenimientos.tarea' => function($query) use($tarea) {
            $query->where('tareas.id', $tarea->id);
        }])->get();
        //dump($unidades);

        if ($tarea->save()) {
            notify()->success("La tarea se actualizó correctamente", "Éxito:", "topRight");

            /* Actualizar las OTP abiertas para la especialidad, y despues los ultimos mantenimientos. */
            $ordenes = OrdenesTrabajo::where('status', 'Abierta')->whereHas('especialidad', function ($query) use ($especialidad_anterior) {
                $query->where('ordenes_trabajo_especialidad.especialidad_id', $especialidad_anterior);
            })->whereHas('tareas', function ($query) use($tarea) {
                $query->where('tareas.id', $tarea->id);
            })->with(['especialidad', 'tareas'])->get();

            foreach($ordenes as $orden) {
                $orden->especialidad()->detach($especialidad_anterior);
                $orden->especialidad()->attach($tarea->especialidad_id);
            }

            //! OJO CON ESTO!!!
            //TODO: PUEDE SER QUE MODIFIQUE LA FRECUENCIA (CANT DE DIDAS O KM) PERO TAMBIEN PUEDE SER QUE MODIFIQUE EL TIPO DE FRECUENCIA, OJO
            /* FIXME: Todavia no hice la segunda parte, solo estoy teniendo en cuenta que modifique la cant de dias o km. */
            /* Modifico el último mantenimiento de todas las unidades que tengan esta tarea por si cambió  la cantidad de la frecuencia. */
            foreach($unidades as $unidad) {
                //Por cada unidad busco el primer mantenimiento (ordenado descendente) que sea de la tarea que estoy modificando, al encontrar corto loop.
                foreach($unidad->mantenimientos as $mantenimiento) {
                    if($mantenimiento->tarea_id == $tarea->id) {
            
                        if($tarea->frecuencia == 'kms') {
                            $prox_mant = $mantenimiento->prox_mantenimiento - $tarea_km_anterior; //Le resto la frecuencia anterior
                            $mantenimiento->prox_mantenimiento = $prox_mant + $tarea->kilometros; //Le sumo la nueva
                            //*Valido sobre el prox mantenimiento en kms, (que puede estar modificado).
                            if($mantenimiento->mantenimiento_modif) {
                                //Le resto y sumo la nueva frecuencia
                                $nuevo_mant_modif = $mantenimiento->mantenimiento_modif - $tarea_km_anterior + $tarea->kilometros;
                                $mantenimiento->mantenimiento_modif = $nuevo_mant_modif;
                            }
                            $mantenimiento->frecuencia = $tarea->kilometros;
                        }
                        if($tarea->frecuencia == 'dias') {
    
                            $prox_mant_fecha = new DateTime($mantenimiento->prox_mantenimiento_fecha);
                            $prox_mant_fecha->sub(new DateInterval('P'.$tarea_dias_anterior.'D')); // Le resto los dias de la frecuencia anterior
                            $prox_mant_fecha->add(new DateInterval('P'.$tarea->dias.'D')); //Le sumo los dias de la frecuencia nueva
                            $mantenimiento->prox_mantenimiento_fecha = $prox_mant_fecha;
    
                            //*Valido sobre la fecha del próx. mantenimiento (que puede estar modificada).
                            if($mantenimiento->mantenimiento_modif_fecha != null) {
    
                                //Le resto los dias de la frecuencia anterior y le sumo los nuevos.
                                $prox_fecha_modif = new DateTime($mantenimiento->mantenimiento_modif_fecha);
                                $prox_fecha_modif->sub(new DateInterval('P'.$tarea_dias_anterior.'D')); // Le resto los dias de la frecuencia anterior
    
                                $prox_fecha_modif->add(new DateInterval('P'.$tarea->dias.'D')); //Le sumo los dias de la frecuencia nueva
                                $mantenimiento->mantenimiento_modif_fecha = $prox_fecha_modif;
    
                            }
                            $mantenimiento->frecuencia_dias = $tarea->dias;
    
                        }
                        if($tarea->frecuencia == 'combinado') {
                            //Kilometros
                            $prox_mant = $mantenimiento->prox_mantenimiento - $tarea_km_anterior; //Le resto la frecuencia anterior
                            $mantenimiento->prox_mantenimiento = $prox_mant + $tarea->kilometros; //Le sumo la nueva
                            //*Valido sobre el prox mantenimiento en kms, (que puede estar modificado).
                            if($mantenimiento->mantenimiento_modif) {
                                //Le resto y sumo la nueva frecuencia
                                $nuevo_mant_modif = $mantenimiento->mantenimiento_modif - $tarea_km_anterior + $tarea->kilometros;
                                $mantenimiento->mantenimiento_modif = $nuevo_mant_modif;
                            }
                            $mantenimiento->frecuencia = $tarea->kilometros;
                            
                            //Dias
                            $prox_mant_fecha = new DateTime($mantenimiento->prox_mantenimiento_fecha);
                            $prox_mant_fecha->sub(new DateInterval('P'.$tarea_dias_anterior.'D')); // Le resto los dias de la frecuencia anterior
                            $prox_mant_fecha->add(new DateInterval('P'.$tarea->dias.'D')); //Le sumo los dias de la frecuencia nueva
                            $mantenimiento->prox_mantenimiento_fecha = $prox_mant_fecha;
    
                            //*Valido sobre la fecha del próx. mantenimiento (que puede estar modificada).
                            if($mantenimiento->mantenimiento_modif_fecha != null) {
    
                                //Le resto los dias de la frecuencia anterior y le sumo los nuevos.
                                $prox_fecha_modif = new DateTime($mantenimiento->mantenimiento_modif_fecha);
                                $prox_fecha_modif->sub(new DateInterval('P'.$tarea_dias_anterior.'D')); // Le resto los dias de la frecuencia anterior
    
                                $prox_fecha_modif->add(new DateInterval('P'.$tarea->dias.'D')); //Le sumo los dias de la frecuencia nueva
                                $mantenimiento->mantenimiento_modif_fecha = $prox_fecha_modif;
    
                            }
                            $mantenimiento->frecuencia_dias = $tarea->dias;
    
                        }
    
                        $mantenimiento->save();
    
                        //Si encontre el ultimo mantenimiento para la tarea que estoy modificando, corto loop.
                        break;
                    }
                }
            }

            if ($request->get('action') == 'guardar_y_nuevo') {
                return redirect()->route('admin.tarea.subcomponente', $tarea->componente_id);
            } elseif ($request->get('action') == 'guardar') {
                return redirect()->route('admin.plan-mantenimiento-preventivo');
            }
        } else {
            notify()->error("Hubo un error al actualizar la tarea. Por favor, inténtelo nuevamente", "Error:", "topRight");

            return redirect($this->referer());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (Tarea::destroy($id)) {
            notify()->success("La tarea se eliminó correctamente", "Éxito:", "topRight");

            return redirect()->route('admin.plan-mantenimiento-preventivo');
        } else {
            notify()->error("Hubo un error al eliminar el plan. Por favor, inténtelo nuevamente.", "Error:", "topRight");

            return redirect($this->referer());
        }
    }

    public function saveRecursos(Request $request) 
    {
        $tarea = Tarea::find($request->tarea_id);

        $tarea->piezas()->wherePivot('unidad_id', $request->unidad_id)->sync($request->listado_piezas_recursos);

        notify()->success("Los recursos se guardaron correctamente", "Éxito:", "topRight");

        return redirect()->route('admin.recurso-actividad');
    }
}
