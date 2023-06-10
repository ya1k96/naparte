<?php

namespace App\Http\Controllers\Admin;

use App\Modelo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Marca;

class ModeloController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $modelos = Modelo::get();

        $marcas = Marca::get();

        if (!empty($request['buscar'])){
            $modelos = Modelo::where('nombre', 'like', '%'.$request['buscar'].'%' )->get();
        }
        $buscar = $request['buscar'] ?? "";

        return view('admin.modelos.index', ['modelos' => $modelos, 'buscar' => $buscar, 'marcas' => $marcas]);
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
            'marca_id' => 'required',
        ]);
        
        $modelo = Modelo::create($request->all());
        if($modelo->save()){
            notify()->success("El modelo se agregó correctamente","Éxito: ","topRight");
            return redirect()->route('admin.modelos');
        }else{
            notify()->error("Hubo un error al guardar el modelo. Por favor, inténtalo nuevamente.","Error: ","topRight");
            return redirect($this->referer());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Modelos  $modelos
     * @return \Illuminate\Http\Response
     */
    public function show(Modelos $modelos)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $modelo = Modelo::find($id);
        $marcas = Marca::get();

        return view('admin.modelos.edit', compact('modelo', 'marcas'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Modelo $modelo)
    {
        $request->validate([
            'nombre' => 'required',
            'marca_id' => 'required',
        ]);

        $modelo = Modelo::find($request->id);
        $modelo->nombre = $request->nombre;
        $modelo->marca_id = $request->marca_id;

        if($modelo->save()){
            notify()->success("El modelo se editó correctamente","Éxito: ","topRight");
            return redirect()->route('admin.modelos');
        }else{
            notify()->error("Hubo un error al editar el modelo. Por favor, inténtalo nuevamente.","Error: ","topRight");
            return redirect($this->referer());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Modelo  $modelo
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(Modelo::destroy($id)){
            notify()->success("El modelo se eliminó correctamente","Éxito: ","topRight");
            return redirect()->route('admin.modelos');
        }else{
            notify()->error("Hubo un error al eliminar el modelo. Por favor, inténtalo nuevamente.","Error: ","topRight");
            return redirect($this->referer());
        }
    }
}
