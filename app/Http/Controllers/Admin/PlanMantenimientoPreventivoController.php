<?php

namespace App\Http\Controllers\Admin;

use App\Componente;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Plan;
use App\Tarea;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class PlanMantenimientoPreventivoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
	    $planes = Plan::withTrashed();
	    $buscar = $request['buscar'];

        if (!empty($request['buscar'])) {
            $planes = $planes->where('nombre', 'like', '%'.$request['buscar'].'%' );
        }

        if(!empty($request['estado'])) {
            if($request['estado'] == 'anulado') {
                $planes = $planes->whereNotNull('deleted_at');
            } elseif($request['estado'] == 'activo') {
                $planes = $planes->where('deleted_at', null);
            }
        }

        $planes = $planes->orderBy('id', 'desc')->paginate(10);

	    return view('admin.plan_mantenimiento_preventivo.index', compact('buscar', 'planes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.plan_mantenimiento_preventivo.create');
    }

    /**
     * Función recursiva que recibe el arreglo de componentes, el plan creado y establece el componente padre en null.
     * Se recorre el arreglo y se comprueba que el componente sea distinto de nulo y que sea un arreglo.
     * Se crea el componente y se establece el valor del componente padre.
     * Se evalúa si el componente es un arreglo y se asigna el id del componente padre.
     */
    public function saveComponents(array &$componentes, $plan, $componente_padre = null)
    {
        foreach ($componentes as $componente) {

            if ($componente == null) continue;
            if (is_array($componente)) continue;

            $new_componente = Componente::create([
                'plan_id' => $plan->id,
                'nombre' => $componente,
                'componente_padre' => $componente_padre
            ]);

            if (!empty($componente_padre)) {
                $new_componente['componente_padre'] = $componente_padre;
            }

            if (!empty($componentes[$componente]) && is_array($componentes[$componente])) {
                $this->saveComponents($componentes[$componente], $plan, $new_componente->id);
            }
        }
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
            'nombre' => 'required',
            'fieldName' => 'required'
        ]);

        $plan = Plan::create([
            'nombre' => $request->nombre,
        ]);

        $componentes = $request['fieldName'];

        $this->saveComponents($componentes, $plan);

        if ($plan->save()) {
            notify()->success("El plan se agregó correctamente", "Éxito:", "topRight");

            return redirect()->route('admin.plan-mantenimiento-preventivo');
        } else {
            notify()->error("Hubo un error al guardar el plan. Por favor, inténtelo nuevamente", "Error:", "topRight");

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
        $plan = Plan::find($id);
        $componentes = Componente::with(['subcomponentes', 'tareas.especialidad'])
                                ->whereNull('componente_padre')
                                ->where('plan_id', $id)
                                ->get();

        return view('admin.plan_mantenimiento_preventivo.show', compact('plan', 'componentes'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $plan = Plan::find($id);
        $componentes = Componente::whereNull('componente_padre')
                                  ->where('plan_id', $id)
                                  ->get();

        return view('admin.plan_mantenimiento_preventivo.edit', compact('plan', 'componentes'));
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
            'nombre' => 'required',
        ]);

        $plan = Plan::find($id);

        $plan->nombre = $request->nombre;

        if ($plan->save()) {
            notify()->success("El plan se actualizó correctamente", "Éxito:", "topRight");

            return redirect()->route('admin.plan-mantenimiento-preventivo');
        } else {
            notify()->error("Hubo un error al actualizar el plan. Por favor, inténtelo nuevamente", "Error:", "topRight");

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
        $plan = Plan::where('id', $id)->with('vinculaciones.unidad')->first();

        if(!empty($plan->vinculaciones)) {
            $unidades = '';
            foreach($plan->vinculaciones as $i=>$vinculacion) {
                if(!empty($vinculacion->unidad)) {
                    if($i == 0) {
                        $unidades .= $vinculacion->unidad->num_interno;
                    } else {
                        $unidades .= ', ' . $vinculacion->unidad->num_interno;
                    }
                }
            }

            if($unidades != '') {
                notify()->error("Unidades vinculadas: ". $unidades, "Error:", "topRight");
                notify()->error("El plan tiene vinculaciones activas. Por favor, elimine las vinculaciones para poder anularlo.", "Error:", "topRight");
                return back();
            }
        }

        if ($plan->delete()) {
            notify()->success("El plan se anuló correctamente", "Éxito:", "topRight");

            return redirect()->route('admin.plan-mantenimiento-preventivo');
        } else {
            notify()->error("Hubo un error al anular el plan. Por favor, inténtelo nuevamente.", "Error:", "topRight");

            return back();
        }
    }

    public function replicate($id)
    {
        $plan = Plan::find($id);
        $copia_plan = $plan->replicate();
        $copia_plan->nombre = "Réplica " . $plan->nombre;
        $copia_plan->created_at = Carbon::now();
        $copia_plan->save();

        $componentes = Componente::where('plan_id', $plan->id)->get();

        $componente_padre_id = [];
        foreach ($componentes as $componente) {
            $copia_componente = $componente->replicate();
            $copia_componente->plan_id = $copia_plan->id;

            if ($copia_componente->componente_padre) {
                if (!empty($componente_padre_id[$copia_componente->componente_padre])) {
                    $copia_componente->componente_padre = $componente_padre_id[$copia_componente->componente_padre];
                }
            }

            $copia_componente->save();

            $componente_padre_id[$componente->id] = $copia_componente->id;

            $tareas = Tarea::where('componente_id', $componente->id)->get();

            foreach ($tareas as $tarea) {
                $copia_tarea = $tarea->replicate();
                $copia_tarea->componente_id = $copia_componente->id;
                $copia_tarea->save();
            }
        }

        notify()->success("El plan se duplicó correctamente", "Éxito:", "topRight");

        return redirect()->route('admin.plan-mantenimiento-preventivo.edit', $copia_plan->id);
    }
}
