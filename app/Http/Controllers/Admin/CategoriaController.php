<?php

namespace App\Http\Controllers\Admin;

use App\Categoria;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $categorias = Categoria::all();
        $buscar = $request['buscar'] ?? "";

        if (!empty($request['buscar'])) {
            $categorias = Categoria::where('nombre', 'like', '%' . $request['buscar'] . '%')->get();
        }

        return view('admin.categorias.index', compact('categorias', 'buscar'));
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
            'nombre' => 'required|unique:categorias'
        ]);

        $categoria = Categoria::create($request->all());

        if ($categoria->save()) {
            notify()->success("La categoría se agregó correctamente", "Éxito: ", "topRight");

            return redirect()->route('admin.categorias');
        } else {
            notify()->error("Hubo un error al guardar la categoría. Por favor, inténtelo nuevamente", "Error: ", "topRight");

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
        $categoria = Categoria::find($id);

        return view('admin.categorias.edit', compact('categoria'));
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
            'nombre' => "required|unique:categorias,nombre,$id"
        ]);

        $categoria = Categoria::find($id);
        $categoria->nombre = $request->nombre;

        if ($categoria->save()) {
            notify()->success("La categoría se editó correctamente", "Éxito: ", "topRight");

            return redirect()->route('admin.categorias');
        } else {
            notify()->error("Hubo un error al editar la categoría. Por favor, inténtelo nuevamente", "Error: ", "topRight");

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
        if (Categoria::destroy($id)) {
            notify()->success("La categoría se eliminó correctamente", "Éxito: ", "topRight");

            return redirect()->route('admin.categorias');
        } else {
            notify()->error("Hubo un error al eliminar la categoría. Por favor, inténtelo nuevamente.", "Error: ", "topRight");

            return redirect($this->referer());
        }
    }
}
