<?php

namespace App\Http\Controllers\Admin;

use App\Marca;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modelo;

class MarcaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $marcas = Marca::get();

        if (!empty($request['buscar'])){
            $marcas = Marca::where('nombre', 'like', '%'.$request['buscar'].'%' )->get();
        }
        $buscar = $request['buscar'] ?? "";

        return view('admin.marcas.index', ['marcas' => $marcas, 'buscar' => $buscar]);
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
            'nombre' => 'required'
        ]);
        
        $marca = Marca::create($request->all());

        if($marca->save()){
            notify()->success("La marca se agregó correctamente","Éxito: ","topRight");
            return redirect()->route('admin.marcas');
        }else{
            notify()->error("Hubo un error al guardar la marca. Por favor, inténtalo nuevamente.","Error: ","topRight");
            return redirect($this->referer());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Marca  $marca
     * @return \Illuminate\Http\Response
     */
    public function show(Marca $marca)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Marca  $marca
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $marca = Marca::find($id);

        return view('admin.marcas.edit', compact('marca'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Marca  $marca
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Marca $marca)
    {
        $request->validate([
            'nombre' => 'required'
        ]);

        $marca = Marca::find($request->id);
        $marca->nombre = $request->nombre;

        if($marca->save()){
            notify()->success("La marca se editó correctamente","Éxito: ","topRight");
            return redirect()->route('admin.marcas');
        }else{
            notify()->error("Hubo un error al editar la marca. Por favor, inténtalo nuevamente.","Error: ","topRight");
            return redirect($this->referer());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Marca  $marca
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(Marca::destroy($id)){
            notify()->success("La marca se eliminó correctamente","Éxito: ","topRight");
            return redirect()->route('admin.marcas');
        }else{
            notify()->error("Hubo un error al eliminar la marca. Por favor, inténtalo nuevamente.","Error: ","topRight");
            return redirect($this->referer());
        }
    }

    public function getModelos($marca_id) {
        $modelos = Modelo::where("marca_id", $marca_id)->get();
        return $modelos;
    }
}
