<?php

namespace App\Http\Controllers\Admin;

use App\Especialidad;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EspecialidadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $especialidades = Especialidad::all();
        $buscar = $request['buscar'] ?? "";

        if (!empty($request['buscar'])){
            $especialidades = Especialidad::where('nombre', 'like', '%'.$request['buscar'].'%' )->get();
        }

        return view('admin.especialidades.index', compact('especialidades', 'buscar'));
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
        $request->validate([
            'nombre' => 'required|unique:especialidades'
        ]);

        $especialidad = Especialidad::create($request->all());

        if ($especialidad->save()) {
            notify()->success("La especialidad se agregó correctamente", "Éxito:", "topRight");

            return redirect()->route('admin.especialidades');
        } else {
            notify()->error("Hubo un error al guardar la especialidad. Por favor, inténtelo nuevamente", "Error:", "topRight");

            return redirect()->route('admin.especialidades');
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
        $especialidad = Especialidad::find($id);

        return view('admin.especialidades.edit', compact('especialidad'));
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
            'nombre' => "required|unique:especialidades,nombre,$id"
        ]);

        $especialidad = Especialidad::find($id);
        $especialidad->nombre = $request->nombre;

        if ($especialidad->save()) {
            notify()->success("La especialidad se editó correctamente", "Éxito:", "topRight");

            return redirect()->route('admin.especialidades');
        } else {
            notify()->error("Hubo un error al editar la especialidad. Por favor, inténtelo nuevamente.", "Error:", "topRight");

            return redirect()->route('admin.especialidades');
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
        $especialidad = Especialidad::find($id);

        if($especialidad->personal()->count() > 0) {
          notify()->error("No puede borrar esta especialidad, se encuentra vinculada con el personal.", "Error:", "topRight");
          return redirect()->route('admin.especialidades');
        }

        if($especialidad->orden_trabajo()->count() > 0) {
          notify()->error("No puede borrar esta especialidad, se encuentra vinculada con ordenes de trabajo.", "Error:", "topRight");
          return redirect()->route('admin.especialidades');
        }

        if ($especialidad->delete()) {
            notify()->success("La especialidad se eliminó correctamente", "Éxito:", "topRight");

            return redirect()->route('admin.especialidades');
        } else {
            notify()->error("Hubo un error al eliminar la especialidad. Por favor, inténtelo nuevamente.", "Error:", "topRight");

            return redirect()->route('admin.especialidades');
        }
    }
}
