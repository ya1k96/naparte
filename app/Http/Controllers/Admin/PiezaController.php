<?php

namespace App\Http\Controllers\Admin;

use App\BaseOperacion;
use App\Categoria;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Pieza;
use App\UnidadMedida;
use Illuminate\Support\Facades\DB;

class PiezaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $categorias = Categoria::all();
        $piezas = Pieza::withTrashed();

        if (!empty($request['categorias'])) {
            foreach ($request['categorias'] as $cat) {
                $piezas = $piezas->whereHas('categorias', function ($q) use ($cat) {
                        return $q->where('categorias.id', '=' , $cat);
                    });
            }
        }

        if (!empty($request['buscar'])) {
            $piezas = $piezas->where('descripcion', 'like', '%'.$request['buscar'].'%' )
                ->orWhere('nro_pieza', 'like', '%' . $request['buscar'] . '%');
        }

        $piezas = $piezas->paginate(10);

        $buscar = $request['buscar'] ?? "";
        $filtro_categoria['id'] = $request['categoria'] ?? "";

        return view('admin.piezas.index', compact('piezas', 'buscar', 'categorias', 'filtro_categoria'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $bases_operaciones = BaseOperacion::all();
        $unidades_medidas = UnidadMedida::all();
        $categorias = Categoria::all();

        return view('admin.piezas.create', compact('bases_operaciones', 'unidades_medidas', 'categorias'));
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
            'nro_pieza' => 'required',
            'descripcion' => 'required',
            'unidad_medida' => 'required',
            'base_operacion' => 'required',
            //'categorias' => 'required'
        ]);

        $pieza = Pieza::create([
            'nro_pieza' => $request->nro_pieza,
            'descripcion' => $request->descripcion,
            'unidad_medida_id' => (int)$request->unidad_medida,
            'observacion' => $request->observaciones
        ]);

        if ($request->base_operacion) {
            $pieza->baseOperacion()->attach($request->base_operacion);
        }

        if ($request->categorias) {
            $pieza->categorias()->attach($request->categorias);
        }

        if ($pieza->save()) {
            notify()->success("La pieza se agregó al catálogo correctamente", "Éxito:", "topRight");

            return redirect()->route('admin.piezas-de-catalogo');
        } else {
            notify()->error("Hubo un error al guardar la pieza en el catálogo. Por favor, inténtelo nuevamente", "Error:", "topRight");

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
        $pieza = Pieza::find($id);

        return view('admin.piezas.show', compact('pieza'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $pieza = Pieza::with(['baseOperacion', 'categorias', 'unidadMedida'])
            ->where('id', $id)
            ->get();

        $unidades_medidas = UnidadMedida::all();
        $categorias = Categoria::all();
        $bases_operaciones = BaseOperacion::all();

        return view('admin.piezas.edit', compact('pieza', 'unidades_medidas', 'categorias', 'bases_operaciones'));
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
            'nro_pieza' => 'required',
            'descripcion' => 'required',
            'unidad_medida' => 'required',
            'base_operacion' => 'required',
            //'categorias' => 'required'
        ]);

        $pieza = Pieza::find($id);

        $pieza->nro_pieza = $request->nro_pieza;
        $pieza->descripcion = $request->descripcion;
        $pieza->unidad_medida_id = $request->unidad_medida;
        $pieza->observacion = $request->observacion;

        if ($request->base_operacion) {
            $pieza->baseOperacion()->sync($request->base_operacion);
        }

        if ($request->categorias) {
            $pieza->categorias()->sync($request->categorias);
        }

        if ($pieza->save()) {
            notify()->success("La pieza se actualizó correctamente", "Éxito:", "topRight");

            return redirect()->route('admin.piezas-de-catalogo');
        } else {
            notify()->error("Hubo un error al actualizar la pieza en el catálogo. Por favor, inténtelo nuevamente", "Error:", "topRight");

            return redirect($this->referer());
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
        $pieza = Pieza::find($id);
        $pieza->delete();

        if ($pieza->deleted_at != null) {
            notify()->success("La pieza del catálogo se desactivó correctamente", "Éxito:", "topRight");

            return redirect()->route('admin.piezas-de-catalogo');
        } else {
            notify()->error("Hubo un error al desactivar la pieza del catálogo. Por favor, inténtelo nuevamente.", "Error:", "topRight");

            return redirect($this->referer());
        }
    }

    /**
     * Se cambia el estado de la pieza
     */
    public function restore($id)
    {
        Pieza::withTrashed()->find($id)->restore();

        notify()->success("La pieza se activó correctamente","Éxito: ","topRight");

        return back();
    }

        /**
     * Función para importar por un csv un listado de piezas 
    */
   public function importar() 
   {
       $bases_operacion = BaseOperacion::pluck('nombre', 'id');
       return view('admin.piezas.importar', compact('bases_operacion'));
   }

   /**
    * Función que tiene la lógica para almacenar piezas desde el importador
   */
   public function importarStore(Request $request)
   {

       $bases_operacion = BaseOperacion::pluck('nombre', 'id');
       $piezas = Pieza::with('baseOperacion')->get();
       $piezas = $piezas->keyBy('id');
       $listado_piezas = Pieza::pluck('nro_pieza', 'id')->all();
       $unidades_medidas = UnidadMedida::pluck('nombre', 'id')->all();
       $unidades_medidas = array_map('strtoupper', $unidades_medidas);
       //dd($unidades_medidas);
       /* dump($piezas);
       dd($listado_piezas); */
       
       ini_set('max_execution_time', 0);
       ini_set("memory_limit", "-1");

       if ($request->hasFile('archivo')) {
           $mensajes = [];
           $tipos = ['application/vnd.ms-excel', 'text/csv'];

           if(in_array($_FILES['archivo']['type'], $tipos)){

               if (($gestor = fopen($_FILES['archivo']['tmp_name'], "r")) !== FALSE) {
                   $fila = 0;
                   $data = [];
                   while (($linea = fgetcsv($gestor, 1000, ";")) !== FALSE) {
                       $numero = count($linea);
                       $fila++;
                       if ($fila == 1) continue;
                       if ($numero <= 1) {
                           notify()->error('El archivo no posee un formato valido, corrobore que el delimitador del archivo sea ";" y vuelva a intentarlo',"Error: ","topRight");
                           return redirect()->route('admin.piezas.importar');
                       }

                       if ($numero < 4) {
                           notify()->error("A la fila nro. $fila le faltan columnas, por favor verifique la que la cantidad sea la misma que en el ejemplo y vuelva a intentarlo.","Error: ","topRight");
                           return redirect()->route('admin.piezas.importar');
                       }

                       list(
                           $data['No. de Parte'],
                       ) = $linea;

                       if (!in_array($data['No. de Parte'], $listado_piezas)) {

                           //*Generar si no existe.
                           list(
                                $data['No. de Parte'],
                                $data['Descripcion'],
                                $data['Unidad'],
                                $data['Categoria'],
                            ) = $linea;

                            //*Validar con unidad de medida.

                            if(!in_array(strtoupper($data['Unidad']), $unidades_medidas)) {
                                $mensajes[$data['No. de Parte']] = 'No existe la unidad de medida'.' '.$data['Unidad'];
                                continue;
                            }
                            
                            $pieza = new Pieza();
                            $pieza->nro_pieza = $data['No. de Parte'];
                            $pieza->descripcion = utf8_encode($data['Descripcion']);
                            $pieza->unidad_medida_id  = array_search(strtoupper($data['Unidad']), $unidades_medidas);
    
                            if(!$pieza->save()) {
                                $mensajes[$data['No. de Parte']] = 'No se pudo guardar la pieza '.$data['No. de Parte'];
                                continue;
                            } else {
    
                                //* Agregarle relacion con las bases.
                                $pieza->baseOperacion()->syncWithoutDetaching($request->bases_operacion_id);
                                
                                //* Agregar al array de piezas existentes.
                                $listado_piezas[$pieza->id] = $pieza->nro_pieza;

                            }

                       } else {
                            //* Agregarle relacion con las bases.
                            $pieza = $piezas->get(array_search($data['No. de Parte'], $listado_piezas));
                            $pieza->baseOperacion()->syncWithoutDetaching($request->bases_operacion_id);
                       }


                   }
                   fclose($gestor);
                   unset($gestor);
               }

               if(empty($mensajes)){
                   notify()->success("La importación se realizó con éxito.","Éxito: ","topRight");
               }else{
                   notify()->info("La importación se realizó de forma parcial.","Éxito: ","topRight");
               }
           
           }else{
               notify()->error('El archivo tiene un formato incompatible. Solo se admite ".csv"',"Error: ","topRight");
               return redirect()->route('admin.piezas.importar');
           }
       }

       return view('admin.piezas.importar', compact('mensajes', 'bases_operacion'));
   }

   /**
    * Función para descargar el ejemplo de la plantilla para carga masiva de inventario
   */
   public function descargarEjemplo() {
       return response()->download(public_path('assets/files/tabla_ejemplo_catalogo.csv'));
   }
}
