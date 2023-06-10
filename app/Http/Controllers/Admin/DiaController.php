<?php

namespace App\Http\Controllers\Admin;

use App\Dia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DiaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $dias = Dia::orderBy('id', 'desc')->get();
        $buscar = $request['buscar'] ?? "";

        if (!empty($request['buscar'])) {
            $dias = Dia::where('cantidad', 'like', '%' . $request['buscar'] . '%')->get();
        }

        return view('admin.dias.index', compact('dias', 'buscar'));
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
            'cantidad' => 'required|unique:dias'
        ]);

        $dia = Dia::create([
            'cantidad' => $request->cantidad
        ]);

        if ($dia->save()) {
            notify()->success("La cantidad de días  se agregaron correctamente", "Éxito: ", "topRight");

            return redirect()->route('admin.dias');
        } else {
            notify()->error("Hubo un error al guardar la cantidad de días. Por favor, inténtelo nuevamente", "Error: ", "topRight");

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
        $dia = Dia::find($id);

        return view('admin.dias.edit', compact('dia'));
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
            'cantidad' => 'required|unique:dias,cantidad,$id'
        ]);

        $dia = Dia::find($id);
        $dia->cantidad = $request->cantidad;

        if ($dia->save()) {
            notify()->success("La cantidad de días se actualizó correctamente", "Éxito: ", "topRight");

            return redirect()->route('admin.dias');
        } else {
            notify()->error("Hubo un error al actualizar la cantidad de días. Por favor, inténtelo nuevamente.", "Error: ", "topRight");

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
        if (Dia::destroy($id)) {
            notify()->success("La cantidad de días se eliminó correctamente", "Éxito: ", "topRight");

            return redirect()->route('admin.dias');
        } else {
            notify()->error("Hubo un error al eliminar la cantidad de días. Por favor, inténtelo nuevamente.", "Error: ", "topRight");

            return redirect($this->referer());
        }
    }
}
