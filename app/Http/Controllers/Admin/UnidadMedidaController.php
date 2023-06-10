<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\UnidadMedida;

class UnidadMedidaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $unidades_medidas = UnidadMedida::all();
        $buscar = $request['buscar'] ?? "";

        if (!empty($request['buscar'])){
            $unidades_medidas = UnidadMedida::where('nombre', 'like', '%'.$request['buscar'].'%' )->get();
        }

        return view('admin.unidades_medidas.index', compact('unidades_medidas', 'buscar'));
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
            'nombre' => 'required|unique:unidades_medidas'
        ]);

        $unidad_medida = UnidadMedida::create($request->all());

        if ($unidad_medida->save()) {
            notify()->success("La unidad de medida se agregó correctamente", "Éxito:", "topRight");

            return redirect()->route('admin.unidades-de-medida');
        } else {
            notify()->error("Hubo un error al guardar la unidad de medida. Por favor, inténtelo nuevamente", "Error:", "topRight");

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
    public function edit($id)
    {
        $unidad_medida = UnidadMedida::find($id);

        return view('admin.unidades_medidas.edit', compact('unidad_medida'));
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
            'nombre' => "required|unique:unidades_medidas,nombre,$id"
        ]);

        $unidad_medida = UnidadMedida::find($id);
        $unidad_medida->nombre = $request->nombre;

        if ($unidad_medida->save()) {
            notify()->success("La unidad de medida se editó correctamente", "Éxito:", "topRight");

            return redirect()->route('admin.unidades-de-medida');
        } else {
            notify()->error("Hubo un error al editar la unidad de medida. Por favor, inténtelo nuevamente.", "Error:", "topRight");

            return redirect()->route($this->referer());
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
        if (UnidadMedida::destroy($id)) {
            notify()->success("La unidad de medida se eliminó correctamente", "Éxito:", "topRight");

            return redirect()->route('admin.unidades-de-medida');
        } else {
            notify()->error("Hubo un error al eliminar la unidad de medida. Por favor, inténtelo nuevamente.", "Error:", "topRight");

            return redirect($this->referer());
        }
    }
}
