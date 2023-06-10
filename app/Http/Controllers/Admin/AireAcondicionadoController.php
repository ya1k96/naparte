<?php

namespace App\Http\Controllers\Admin;

use App\AireAcondicionado;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AireAcondicionadoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $aires_acondicionados = AireAcondicionado::get();

        if (!empty($request['buscar'])){
            $aires_acondicionados = AireAcondicionado::where('nombre', 'like', '%'.$request['buscar'].'%' )->get();
        }
        $buscar = $request['buscar'] ?? "";

        return view('admin.aires_acondicionados.index', ['aires_acondicionados' => $aires_acondicionados, 'buscar' => $buscar]);
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
        
        $aire_acondicionado = AireAcondicionado::create($request->all());

        if($aire_acondicionado->save()){
            notify()->success("La marca de aire acondicionado se agregó correctamente","Éxito: ","topRight");
            return redirect()->route('admin.aires_acondicionados');
        }else{
            notify()->error("Hubo un error al guardar la marca de aire acondicionado. Por favor, inténtalo nuevamente.","Error: ","topRight");
            return redirect($this->referer());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\AireAcondicionado  $aireAcondicionado
     * @return \Illuminate\Http\Response
     */
    public function show(AireAcondicionado $aireAcondicionado)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\AireAcondicionado  $aireAcondicionado
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $aire_acondicionado = AireAcondicionado::find($id);

        return view('admin.aires_acondicionados.edit', compact('aire_acondicionado'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\AireAcondicionado  $aireAcondicionado
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AireAcondicionado $aireAcondicionado)
    {
        $request->validate([
            'nombre' => 'required'
        ]);

        $aire_acondicionado = AireAcondicionado::find($request->id);
        $aire_acondicionado->nombre = $request->nombre;

        if($aire_acondicionado->save()){
            notify()->success("La marca de aire acondicionado se editó correctamente","Éxito: ","topRight");
            return redirect()->route('admin.aires_acondicionados');
        }else{
            notify()->error("Hubo un error al editar la marca de aire acondicionado. Por favor, inténtalo nuevamente.","Error: ","topRight");
            return redirect($this->referer());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\AireAcondicionado  $aireAcondicionado
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(AireAcondicionado::destroy($id)){
            notify()->success("La marca de aire acondicionado se eliminó correctamente","Éxito: ","topRight");
            return redirect()->route('admin.aires_acondicionados');
        }else{
            notify()->error("Hubo un error al eliminar la marca de aire acondicionado. Por favor, inténtalo nuevamente.","Error: ","topRight");
            return redirect($this->referer());
        }
    }
}
