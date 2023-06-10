<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Kilometro;

class KilometroController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $kilometros = Kilometro::orderBy('id', 'desc')->get();
        $buscar = $request['buscar'] ?? "";

        if (!empty($request['buscar'])) {
            $kilometros = Kilometro::where('cantidad', 'like', '%' . $request['buscar'] . '%')->get();
        }

        return view('admin.kilometros.index', compact('kilometros', 'buscar'));
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
            'cantidad' => 'required|unique:kilometros'
        ]);

        $kilometro = Kilometro::create([
            'cantidad' => $request->cantidad
        ]);

        if ($kilometro->save()) {
            notify()->success("El kilómetro se agregó correctamente", "Éxito: ", "topRight");

            return redirect()->route('admin.kilometros');
        } else {
            notify()->error("Hubo un error al guardar el kilómetro. Por favor, inténtelo nuevamente", "Error: ", "topRight");

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
        $kilometro = Kilometro::find($id);

        return view('admin.kilometros.edit', compact('kilometro'));
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
            'cantidad' => 'required|unique:kilometros,cantidad,$id'
        ]);

        $kilometro = Kilometro::find($id);
        $kilometro->cantidad = $request->cantidad;

        if ($kilometro->save()) {
            notify()->success("El kilómetro se editó correctamente", "Éxito: ", "topRight");

            return redirect()->route('admin.kilometros');
        } else {
            notify()->error("Hubo un error al editar el kilómetro. Por favor, inténtelo nuevamente.", "Error: ", "topRight");

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
        if (Kilometro::destroy($id)) {
            notify()->success("El kilómetro se eliminó correctamente", "Éxito: ", "topRight");

            return redirect()->route('admin.kilometros');
        } else {
            notify()->error("Hubo un error al eliminar el kilómetro. Por favor, inténtelo nuevamente.", "Error: ", "topRight");

            return redirect($this->referer());
        }
    }
}
