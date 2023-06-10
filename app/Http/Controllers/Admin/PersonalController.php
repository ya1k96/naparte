<?php

namespace App\Http\Controllers\Admin;

use App\Especialidad;
use App\Personal;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PersonalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $personal = null;        
        $especialidades = Especialidad::all();        
        $buscar = $request['buscar'] ?? "";
        
        
        $arrPersonalActivo = Personal::with('especialidad');
        $arrPersonalInactivo = Personal::with('especialidad')->onlyTrashed();

        
        if (!empty($request['buscar'])){
            $arrPersonalActivo = $arrPersonalActivo->where(function($q) use ($request) {
              return $q->orWhere('nombre', 'like', '%'.$request['buscar'].'%' )
                ->orWhereHas('especialidad', function($query) use ($request) {
                      $query->where('nombre', 'like', '%'.$request['buscar'].'%' );
                });
            });

            $arrPersonalInactivo = $arrPersonalInactivo->where(function($q) use ($request) {
              return $q->orWhere('nombre', 'like', '%'.$request['buscar'].'%' )
                ->orWhereHas('especialidad', function($query) use ($request) {
                      $query->where('nombre', 'like', '%'.$request['buscar'].'%' );
                });
            });
        }
        
        $arrPersonalActivo = $arrPersonalActivo->get();        
        $arrPersonalInactivo = $arrPersonalInactivo->get();        

        return view('admin.personal.index', compact('personal', 'especialidades', 'arrPersonalActivo', 'arrPersonalInactivo', 'buscar'));
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
            'nombre' => 'required',
            'especialidad_id' => 'required|exists:especialidades,id'
        ]);

        $personal = Personal::create($request->all());

        if ($personal->save()) {
            notify()->success("El personal se agregó correctamente", "Éxito:", "topRight");

            return redirect()->route('admin.personal');
        } else {
            notify()->error("Hubo un error al guardar el personal. Por favor, inténtelo nuevamente", "Error:", "topRight");

            return redirect()->route('admin.personal');
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
      $personal = Personal::find($id);
      $especialidades = Especialidad::all();
      $arrPersonalActivo = null;
      $arrPersonalInactivo = null;
      $buscar = "";

      return view('admin.personal.index', compact('personal', 'especialidades', 'arrPersonalActivo', 'arrPersonalInactivo', 'buscar'));              
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
            'especialidad_id' => 'required|exists:especialidades,id'            
        ]);

        $personal = Personal::find($id);
        $personal->nombre = $request->nombre;
        $personal->especialidad_id = $request->especialidad_id;

        if ($personal->save()) {
            notify()->success("El personal se editó correctamente", "Éxito:", "topRight");

            return redirect()->route('admin.personal');
        } else {
            notify()->error("Hubo un error al editar el personal. Por favor, inténtelo nuevamente.", "Error:", "topRight");

            return redirect()->route('admin.personal');
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
        $per = Personal::find($id)->makeVisible('show_orden_trabajo');

        if($per->show_orden_trabajo) {
          notify()->error("No se puede eliminar un personal vinculado a una orden de trabajo.", "Error:", "topRight");
          return redirect()->route('admin.personal');
        }

        if ($per->delete()) {
            notify()->success("El personal se eliminó correctamente", "Éxito:", "topRight");

            return redirect()->route('admin.personal');
        } else {
            notify()->error("Hubo un error al eliminar el personal. Por favor, inténtelo nuevamente.", "Error:", "topRight");

            return redirect()->route('admin.personal');
        }
    }

    public function restore(Request $request)
    {
        $personal = Personal::where('id', $request->id)->onlyTrashed()->first();
        $personal->deleted_at = null;
        $personal->save();

        notify()->success("El personal se editó correctamente", "Éxito:", "topRight");

        return redirect()->route('admin.personal');
    }

    public function filtrarEspecialidad() {
        
        $array = ["estado" => false, "mensaje" => ""];

        if(request()->especialidad_ids[0] == 'todas') {
            $personal = Personal::all()/* ->makeHidden(['show_orden_trabajo']) */->load('especialidad');
        } else {
            $personal = Personal::all()->whereIn('especialidad_id', request()->especialidad_ids)/* ->makeHidden(['show_orden_trabajo']) */->load('especialidad');
        }
  
        if($personal) {
            $array["estado"] = true;
            $array["mensaje"] = 'Se encontró personal para la especialidad.';
            $array['personal'] = $personal;
        } else {
            $array["mensaje"] = 'No se encontró personal para la especialidad seleccionada.';
        }

        return response()->json(["respuesta" => $array]);
    }
}
