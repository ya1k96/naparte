<?php

namespace App\Http\Controllers\Admin;

use App\BaseOperacion;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BasesOperacionController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $bases_operaciones = BaseOperacion::get();

        if (!empty($request['buscar'])){
            $bases_operaciones = BaseOperacion::where('nombre', 'like', '%'.$request['buscar'].'%' )->get();
        }
        $buscar = $request['buscar'] ?? "";

        return view('admin.bases_operaciones.index', ['bases_operaciones' => $bases_operaciones, 'buscar' => $buscar]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //return view('admin.bases_operaciones.create');
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

        $base_operacion = BaseOperacion::create($request->all());
        if($base_operacion->save()){
            notify()->success("La base de operación se agregó correctamente","Éxito: ","topRight");
            return redirect()->route('admin.bases_operaciones');
        }else{
            notify()->error("Hubo un error al guardar la base de operación. Por favor, inténtalo nuevamente.","Error: ","topRight");
            return redirect($this->referer());
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Proyecto  $proyecto
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $base_operacion = BaseOperacion::find($id);

        return view('admin.bases_operaciones.edit', compact('base_operacion'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $request->validate([
            'nombre' => 'required'
        ]);

        $base_operacion = BaseOperacion::find($request->id);
        $base_operacion->nombre = $request->nombre;

        if($base_operacion->save()){
            notify()->success("La base de operación se editó correctamente","Éxito: ","topRight");
            return redirect()->route('admin.bases_operaciones');
        }else{
            notify()->error("Hubo un error al editar la base de operación. Por favor, inténtalo nuevamente.","Error: ","topRight");
            return redirect($this->referer());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(BaseOperacion::destroy($id)){
            notify()->success("La base de operación se eliminó correctamente","Éxito: ","topRight");
            return redirect()->route('admin.bases_operaciones');
        }else{
            notify()->error("Hubo un error al eliminar la base de operación. Por favor, inténtalo nuevamente.","Error: ","topRight");
            return redirect($this->referer());
        }
    }

    /**
     * Busca las piezas asociadas a la base de operaciones.
     *
     * @param  int  $base_id
     * @return \Illuminate\Http\Response
     */
    public function getPieza($base_id) {
        $base_operacion = BaseOperacion::where('id', $base_id)
            ->with(['piezas' => function ($q) use ($base_id) {
                $q->whereDoesntHave('inventario', function ($query) use ($base_id) {
                    $query->where('bases_operacion_id', $base_id);
                });
            }])
            ->first();
        return $base_operacion->piezas;
    }
}
