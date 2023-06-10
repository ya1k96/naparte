<?php

namespace App\Http\Controllers\Admin;

use App\AireAcondicionado;
use App\Componente;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Tarea;
use App\Unidad;
use App\Pieza;
use App\RecursoActividad;

class RecursoActividadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $unidades = Unidad::whereHas('vinculaciones')->get();

        if ($request !== null) {
            $unidad_id = (int )$request->query("unidad");
            return view('admin.recursos_actividades.index', compact('unidades', 'unidad_id'));
        }

        return view('admin.recursos_actividades.index', compact('unidades'));
    }

    public function asociarRecursos(Request $request) 
    {
        $tarea = Tarea::find($request->query('tarea_id'),['id','descripcion']);
        $componente = Componente::find($request->query('componente_id'),['id','nombre']);
        $recursos = Pieza::get();
        $unidadId = $request->query('unidad_id');

        $unidad = Unidad::whereHas('vinculaciones')->where('id', $unidadId)->with('vinculaciones.plan')->get();
        //dd($unidad[0]->vinculaciones[0]->plan->id);

        $recursosActividades = RecursoActividad::with('pieza.unidadMedida')
            ->where('tarea_id' , $request->query('tarea_id'))
            ->where('unidad_id' , $unidadId)
            ->get();
            
        return view('admin.recursos_actividades.asociar_recursos', compact('tarea' , 'componente','recursos','unidadId' , 'recursosActividades', 'unidad'));
    }   

    /**
     * Agregar un recurso la tarea de un plan de una unidad.
     *
     * @param Request $request
     * @return response
     */
    public function agregarRecurso(Request $request) {
        $array = ["estado" => false, "mensaje" => ""];

        $requestData = $request->all();
        $data_recurso = [
            $requestData['pieza_id'] => [
                'tarea_id' => $requestData['tarea_id'],
                'pieza_id' => $requestData['pieza_id'],
                'unidad_id' => $requestData['unidad_id'],
                'cantidad' => $requestData['cantidad'],
            ]
        ];
        $tarea = Tarea::find($requestData['tarea_id']);

        $tarea->piezas()->wherePivot('unidad_id', $requestData['unidad_id'])->attach($data_recurso);
        $asociacion = $tarea->piezas()->wherePivot('unidad_id', $requestData['unidad_id'])->wherePivot('pieza_id', $requestData['pieza_id'])->wherePivot('tarea_id', $requestData['tarea_id'])->get();
        if($asociacion) {
            $array = ["estado" => true, "mensaje" => "Se agregó correctamente el recurso.", "asociacion" => $asociacion];
        } else {
            $array = ["estado" => false, "mensaje" => "Se produjo un error al agregar el recurso. Por favor, inténtelo nuevamente."];
        }

        return response()->json(["respuesta" => $array]);
    }

        /**
     * Agregar un recurso la tarea de un plan de todas las unidades del mismo plan.
     *
     * @param Request $request
     * @return response
     */
    public function agregarRecursoReplicar(Request $request) {
        $array = ["estado" => false, "mensaje" => ""];

        $requestData = $request->all();
        //dd($requestData);

        $unidades = Unidad::whereHas('vinculaciones', function($query) use($requestData) {
            return $query->where('plan_id', $requestData['plan_id'])->whereHas('plan.componentes', function($query) use($requestData) {
                return $query->where('componentes.id', $requestData['componente_id']);
            });
        })->with(['vinculaciones.plan.componentes' => function($query) use($requestData) {
            return $query->where('componentes.id', $requestData['componente_id']);
        }])->get();
        //dd($unidades);

        $tarea = Tarea::find($requestData['tarea_id']);
        $error = false;
        $unidades_error = [];

        if(count($unidades) > 1) {
            foreach($unidades as $unidad) {
                //dd($unidad->vinculaciones[0]->plan->componentes[0]);
                if($unidad->id != $requestData['unidad_id']) {
                    $data_recurso = [
                        $requestData['pieza_id'] => [
                            'tarea_id' => $requestData['tarea_id'],
                            'pieza_id' => $requestData['pieza_id'],
                            'unidad_id' => $unidad->id,
                            'cantidad' => $requestData['cantidad'],
                        ]
                    ];
        
                    $asociacion_anterior = $tarea->piezas()->wherePivot('unidad_id', $unidad->id)->wherePivot('pieza_id', $requestData['pieza_id'])->wherePivot('tarea_id', $requestData['tarea_id'])->get(); 

                    if(count($asociacion_anterior) > 0) {
                        $tarea->piezas()->newPivotStatement()->where('unidad_id', $unidad->id)->where('pieza_id', $requestData['pieza_id'])->where('tarea_id', $requestData['tarea_id'])->update(['cantidad' => $requestData['cantidad']]);
                    } else {
                        $tarea->piezas()->wherePivot('unidad_id', $unidad->id)->attach($data_recurso);
                    }

                    $asociacion = $tarea->piezas()->wherePivot('unidad_id', $unidad->id)->wherePivot('pieza_id', $requestData['pieza_id'])->wherePivot('tarea_id', $requestData['tarea_id'])->get(); 
                    if($asociacion) {
                        $array = ["estado" => true, "mensaje" => "Se agregó correctamente el recurso a todas las unidades del mismo plan."];
                    } else {
                        $error =  true;
                        $unidades_error[] = $unidad->num_serie;
                    }
                }
            }
        } else {
            $array = ["estado" => false, "mensaje" => "No existen otras unidades con este plan."];
        }
        if($error) {
            $text = '';
            foreach($unidades_error as $index=>$unidad) {
                $index != 0 ? $text .= ', '.$unidad->num_interno : $text .= $unidad->num_interno;
            }
            $array = ["estado" => false, "mensaje" => "Error al agregar el recurso en las siguientes unidades: ".$text.". Por favor, inténtelo nuevamente."];
        }
        return response()->json(["respuesta" => $array]);
    }

    public function editarRecurso(Request $request) {
        $array = ["estado" => false, "mensaje" => ""];

        $requestData = $request->all();

        $tarea = Tarea::find($requestData['tarea_id']);

        
        if($tarea->piezas()->newPivotStatement()->where('unidad_id', $requestData['unidad_id'])->where('pieza_id', $requestData['pieza_id'])->where('tarea_id', $requestData['tarea_id'])->update(['cantidad' => $requestData['cantidad']])) {
            $array = ["estado" => true, "mensaje" => "Se actualizó el recurso en la unidad actual."];
        } else {
            $array = ["estado" => false, "mensaje" => "Ocurrió un error al actualizar el recurso. Por favor, inténtelo nuevamente."];
        }

        return response()->json(["respuesta" => $array]);
    }

    public function editarRecursoReplicar(Request $request) {
        $array = ["estado" => false, "mensaje" => ""];

        $requestData = $request->all();

        $tarea = Tarea::find($requestData['tarea_id']);

        // Esto actualiza para todas las que ya tengan una vinculacion con esta pieza. Si no tienen, no la agrega.
        
        if($tarea->piezas()->newPivotStatement()/* ->where('unidad_id', $requestData['unidad_id']) */->where('pieza_id', $requestData['pieza_id'])->where('tarea_id', $requestData['tarea_id'])->update(['cantidad' => $requestData['cantidad']])) {
            $array = ["estado" => true, "mensaje" => "Se actualizó correctamente el recurso a todas las unidades del mismo plan."];
        } else {
            $array = ["estado" => false, "mensaje" => "Ocurrió un error al actualizar el recurso a todas las unidades. Por favor, inténtelo nuevamente."];
        }

        return response()->json(["respuesta" => $array]);
    }

    public function eliminarRecurso(Request $request) {
        $array = ["estado" => false, "mensaje" => ""];

        $requestData = $request->all();

        $tarea = Tarea::find($requestData['tarea_id']);

        $tarea->piezas()->wherePivot('unidad_id', $requestData['unidad_id'])->detach($requestData['pieza_id']);
        $asociacion = $tarea->piezas()->wherePivot('unidad_id', $requestData['unidad_id'])->wherePivot('pieza_id', $requestData['pieza_id'])->wherePivot('tarea_id', $requestData['tarea_id'])->get();
        if(count($asociacion) == 0) {
            $array = ["estado" => true, "mensaje" => "Se eliminó correctamente el recurso de esta tarea.", "asociacion" => $asociacion];
        } else {
            $array = ["estado" => false, "mensaje" => "Se produjo un error al eliminar el recurso de esta tarea. Por favor, inténtelo nuevamente."];
        }

        return response()->json(["respuesta" => $array]);
    }

    public function eliminarRecursoReplicar(Request $request) {
        $array = ["estado" => false, "mensaje" => ""];

        $requestData = $request->all();

        $unidades = Unidad::whereHas('vinculaciones', function($query) use($requestData) {
            return $query->where('plan_id', $requestData['plan_id'])->whereHas('plan.componentes', function($query) use($requestData) {
                return $query->where('componentes.id', $requestData['componente_id']);
            });
        })->with(['vinculaciones.plan.componentes' => function($query) use($requestData) {
            return $query->where('componentes.id', $requestData['componente_id']);
        }])->get();
        //dd($unidades);

        $tarea = Tarea::find($requestData['tarea_id']);
        $error = false;
        $unidades_error = [];

        foreach($unidades as $unidad) {
            //dd($unidad->vinculaciones[0]->plan->componentes[0]);
            $data_recurso = [
                $requestData['pieza_id'] => [
                    'tarea_id' => $requestData['tarea_id'],
                    'pieza_id' => $requestData['pieza_id'],
                    'unidad_id' => $unidad->id,
                    'cantidad' => $requestData['cantidad'],
                ]
            ];

            $asociacion_anterior = $tarea->piezas()->wherePivot('unidad_id', $unidad->id)->wherePivot('pieza_id', $requestData['pieza_id'])->wherePivot('tarea_id', $requestData['tarea_id'])->get(); 

            if(count($asociacion_anterior) > 0) {
                $tarea->piezas()->wherePivot('unidad_id', $unidad->id)->wherePivot('tarea_id', $requestData['tarea_id'])->detach($requestData['pieza_id']);
            }

            $asociacion = $tarea->piezas()->wherePivot('unidad_id', $unidad->id)->wherePivot('pieza_id', $requestData['pieza_id'])->wherePivot('tarea_id', $requestData['tarea_id'])->get(); 
            
            if(count($asociacion)==0) {
                $array = ["estado" => true, "mensaje" => "Se eliminó correctamente el recurso a todas las unidades del mismo plan."];
            } else {
                $error =  true;
                $unidades_error[] = $unidad->num_serie;
            }
        }

        if($error) {
            $text = '';
            foreach($unidades_error as $index=>$unidad) {
                $index != 0 ? $text .= ', '.$unidad->num_interno : $text .= $unidad->num_interno;
            }
            $array = ["estado" => false, "mensaje" => "Error al agregar el recurso en las siguientes unidades: ".$text.". Por favor, inténtelo nuevamente."];
        }

        return response()->json(["respuesta" => $array]);
    }

    public function replicarRecursosUnidad(Request $request) {

        if($request['unidad'] == null || $request['unidad_copiar'] == null) {
            notify()->error("Debe informar las dos unidades para copiar los recursos.","Error: ","topRight");
            return back();
        }

        $unidad = Unidad::where('id', $request['unidad'])->first();
        $unidad_a_copiar = Unidad::where('id', $request['unidad_copiar'])->first();

        $recursos_unidad_nueva = RecursoActividad::where('unidad_id', $unidad->id)->get();
        if(count($recursos_unidad_nueva) > 0) {
            notify()->error("No puede copiar los recursos. La unidad ya tiene recursos vinculados.","Error: ","topRight");
            return back();
        }

        $recursosActividades = RecursoActividad::with('pieza.unidadMedida')
            ->where('unidad_id' , $unidad_a_copiar->id)
            ->get();

        if(count($recursosActividades) == 0) {
            notify()->error("La unidad para copiar no tiene recursos asignados.","Error: ","topRight");
            return back();
        }

        foreach($recursosActividades as $recursoActividad) {
            RecursoActividad::create([
                'tarea_id' => $recursoActividad->tarea_id,
                'pieza_id' => $recursoActividad->pieza_id,
                'unidad_id' => $unidad->id,
                'cantidad' => $recursoActividad->cantidad,
            ]);
        }

        notify()->success("Los recursos se copiaron correctamente", "Éxito: ", "topRight");
        return redirect()->route('admin.recurso-actividad');
    }
}
