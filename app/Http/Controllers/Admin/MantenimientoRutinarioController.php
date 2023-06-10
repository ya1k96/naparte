<?php

namespace App\Http\Controllers\Admin;

use App\MantenimientoRutinario;
use App\Componente;
use App\Especialidad;
use App\HistorialUnidad;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\OrdenesTrabajo;
use App\PlanUnidad;
use App\Unidad;
use DateTime;

class MantenimientoRutinarioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $unidades = Unidad::whereHas('vinculaciones')->get();

        return view('admin.mantenimiento_rutinario.index', compact('unidades'));
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
        //
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

    public function showMaintenance()
    {      
        $plan = PlanUnidad::where('unidad_id', request()->unidad_id)->get();

        if (!$plan->isEmpty()) {
            $componentes = Componente::with(['subcomponentes', 'tareas.mantenimientos' => function ($query) {
                $query->where('unidad_id', request()->unidad_id)->orderBy('updated_at', 'ASC');
            }, 'tareas.especialidad','tareas.piezas' => function ($query) {
                $query->where('unidad_id', request()->unidad_id);
            }, 'tareas.piezas.unidadMedida'])
                ->where('plan_id', $plan[0]->plan_id)->get();

            $ultimo_mantenimiento = HistorialUnidad::orderBy('id', 'desc')
                ->where('unidad_id', request()->unidad_id)
                ->first();

            $km_inicial = $plan->first();
            
            $ot_tareas = [];
            foreach($componentes as $componente) {
                foreach($componente->tareas as $tarea) {
                    $ultima_ot = OrdenesTrabajo::with('tareas', 'base_operacion')->where('unidad_id', request()->unidad_id)->where('status', 'Abierta')->whereHas('tareas', function ($query) use ($tarea) {
                        $query->where('tarea_id', '=', $tarea->id);
                    })->orderBy('ordenes_trabajo.id', 'DESC')->first();


                    if($ultima_ot) {
                        $ot_tareas[$tarea->id] = $ultima_ot->makeHidden(['show_personal'])->toArray();
                    }else {
                        $ot_tareas[$tarea->id] = null;
                    }

                }
            }

            if ($componentes != "") {
                return response()->json([$componentes, $ultimo_mantenimiento, $km_inicial, $ot_tareas]);
            } else {
                return response()->json();
            }
        }

    }

    public function showMaintenanceEspecialidad()
    {
        $plan = PlanUnidad::where('unidad_id', request()->unidad_id)->get();
        $especialidad_ids = request()->especialidad_ids;
        if($especialidad_ids[0] == 'todas') {
            $especialidad_ids = Especialidad::all()->modelKeys();
        }
        
        if (!$plan->isEmpty()) {
            $componentes = Componente::with(['subcomponentes', 'tareas' => function($query) use ($especialidad_ids) {
                $query->whereIn('especialidad_id', $especialidad_ids);
            }, 'tareas.especialidad', 'tareas.mantenimientos' => function($q) {
                $q->orderBy('created_at', 'DESC');
            }])
                ->where('plan_id', $plan[0]->plan_id)->get();

            if ($componentes != "") {
                return response()->json([$componentes]);
            } else {
                return response()->json();
            }
        }

    }

    public function showMantenimientoHistorial()
    {
        $plan = PlanUnidad::withTrashed()->where('unidad_id', request()->unidad_id)->get();

        if (!$plan->isEmpty()) {
            $componentes = Componente::with(['subcomponentes', 'tareas' => function($query) {
                $query->withTrashed();
            },'tareas.mantenimientos' => function ($query) {
                $query->where('unidad_id', request()->unidad_id)->orderBy('created_at', 'ASC');
            }, 'tareas.especialidad', 'tareas.mantenimientos.orden_trabajo' => function ($query) {
                $query->withTrashed()->with(['base_operacion', 'tareas.personal']);
            }])
                ->where('plan_id', $plan[0]->plan_id)->get();


            if ($componentes != "") {
                return response()->json([$componentes]);
            } else {
                return response()->json();
            }
        }

    }

    public function editarMantenimiento(Request $request) {

        //dump($request);
        $array = ["estado" => false, "mensaje" => ""];
        $mantenimiento = MantenimientoRutinario::find($request->mantenimiento_id);

        $requestData = $request->all();
        foreach($requestData as $i => $data) {
            if($data == "null") {
                $requestData[$i] = null;
            }
        }
        //dd($requestData);

        $mantenimiento->unidad_id = $requestData['unidad_id'];
        $mantenimiento->componente_id = $requestData['componente_id'];
        $mantenimiento->tarea_id = $requestData['tarea_id'];
        $mantenimiento->ult_mantenimiento = $requestData['ult_mant'];
        $mantenimiento->ult_mantenimiento_fecha = $requestData['ult_mant_fecha']? new DateTime($requestData['ult_mant_fecha']) : null;
        $mantenimiento->frecuencia = $requestData['frecuencia'];
        $mantenimiento->frecuencia_dias = $requestData['frecuencia_dias'];
        $mantenimiento->prox_mantenimiento = $requestData['proximo'];
        $mantenimiento->prox_mantenimiento_fecha = $requestData['proximo_fecha']? new DateTime($requestData['proximo_fecha']) : null;
        $mantenimiento->mantenimiento_modif = $requestData['mantenimiento_modif'];
        $mantenimiento->mantenimiento_modif_fecha = $requestData['mantenimiento_modif_fecha']? new DateTime($requestData['mantenimiento_modif_fecha']) : null;
        $mantenimiento->estado = $requestData['estado'];

        if($mantenimiento->save()) {
            $array["estado"] = true;
            $array["mensaje"] = "El mantenimiento se editó correctamente.";
        }else{
            $array["mensaje"] = "El mantenimiento no se pudo editar correctamente. Por favor, inténtelo nuevamente.";
        }

        return response()->json(["respuesta" => $array, "mantenimiento" => $mantenimiento]);
    }

    /**
     * Listar mantenimientos de cada unidad
     *
     * @return \Illuminate\Http\Response
     */
    public function historialMantenimientos() {

        $unidades = Unidad::whereHas('vinculaciones', function ($q) {
            $q->withTrashed();
        })->get();


        return view('admin.mantenimiento_rutinario.historial-mantenimientos', compact('unidades'));
    }
}
