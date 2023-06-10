<?php

namespace App\Http\Controllers\Admin;

use App\Carroceria;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CarroceriaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $carrocerias = Carroceria::get();

        if (!empty($request['buscar'])){
            $carrocerias = Carroceria::where('nombre', 'like', '%'.$request['buscar'].'%' )->get();
        }
        $buscar = $request['buscar'] ?? "";

        return view('admin.carrocerias.index', ['carrocerias' => $carrocerias, 'buscar' => $buscar]);
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

        $carroceria = Carroceria::create($request->all());

        if($carroceria->save()){
            notify()->success("La carrocería se agregó correctamente","Éxito: ","topRight");
            return redirect()->route('admin.carrocerias');
        }else{
            notify()->error("Hubo un error al guardar la carrocería. Por favor, inténtalo nuevamente.","Error: ","topRight");
            return redirect($this->referer());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Carroceria  $carroceria
     * @return \Illuminate\Http\Response
     */
    public function show(Carroceria $carroceria)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Carroceria  $carroceria
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $carroceria = Carroceria::find($id);

        return view('admin.carrocerias.edit', compact('carroceria'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Carroceria  $carroceria
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Carroceria $carroceria)
    {
        $request->validate([
            'nombre' => 'required'
        ]);
        
        $carroceria = Carroceria::find($request->id);
        $carroceria->nombre = $request->nombre;

        if($carroceria->save()){
            notify()->success("La carrocería se editó correctamente","Éxito: ","topRight");
            return redirect()->route('admin.carrocerias');
        }else{
            notify()->error("Hubo un error al editar la carrocería. Por favor, inténtalo nuevamente.","Error: ","topRight");
            return redirect($this->referer());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Carroceria  $carroceria
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(Carroceria::destroy($id)){
            notify()->success("La carrocería se eliminó correctamente","Éxito: ","topRight");
            return redirect()->route('admin.carrocerias');
        }else{
            notify()->error("Hubo un error al eliminar la carrocería. Por favor, inténtalo nuevamente.","Error: ","topRight");
            return redirect($this->referer());
        }
    }
}
