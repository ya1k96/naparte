<?php

namespace App\Http\Controllers\Admin;

use App\TiposVehiculo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TiposVehiculoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $tipos_vehiculos = TiposVehiculo::get();

        if (!empty($request['buscar'])){
            $tipos_vehiculos = TiposVehiculo::where('nombre', 'like', '%'.$request['buscar'].'%' )->get();
        }
        $buscar = $request['buscar'] ?? "";

        return view('admin.tipos_vehiculos.index', ['tipos_vehiculos' => $tipos_vehiculos, 'buscar' => $buscar]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
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

        $tipo_vehiculo = TiposVehiculo::create($request->all());

        if($tipo_vehiculo->save()){
            notify()->success("El tipo de vehículo se agregó correctamente","Éxito: ","topRight");
            return redirect()->route('admin.tipos_vehiculos');
        }else{
            notify()->error("Hubo un error al guardar el tipo de vehículo. Por favor, inténtalo nuevamente.","Error: ","topRight");
            return redirect($this->referer());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\TiposVehiculo  $tiposVehiculo
     * @return \Illuminate\Http\Response
     */
    public function show(TiposVehiculo $tiposVehiculo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\TiposVehiculo  $tiposVehiculo
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $tipo_vehiculo = TiposVehiculo::find($id);

        return view('admin.tipos_vehiculos.edit', compact('tipo_vehiculo'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TiposVehiculo  $tiposVehiculo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TiposVehiculo $tiposVehiculo)
    {
        $request->validate([
            'nombre' => 'required'
        ]);
        
        $tipo_vehiculo = TiposVehiculo::find($request->id);
        $tipo_vehiculo->nombre = $request->nombre;

        if($tipo_vehiculo->save()){
            notify()->success("El tipo de vehículo se editó correctamente","Éxito: ","topRight");
            return redirect()->route('admin.tipos_vehiculos');
        }else{
            notify()->error("Hubo un error al editar el tipo de vehículo. Por favor, inténtalo nuevamente.","Error: ","topRight");
            return redirect($this->referer());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\TiposVehiculo  $tiposVehiculo
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(TiposVehiculo::destroy($id)){
            notify()->success("El tipo de vehículo se eliminó correctamente","Éxito: ","topRight");
            return redirect()->route('admin.tipos_vehiculos');
        }else{
            notify()->error("Hubo un error al eliminar el tipo de vehículo. Por favor, inténtalo nuevamente.","Error: ","topRight");
            return redirect($this->referer());
        }
    }
}
