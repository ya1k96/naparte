<?php

namespace App\Http\Controllers\Admin;

use App\AireAcondicionado;
use App\BaseOperacion;
use App\Carroceria;
use App\HistorialUnidad;
use App\Unidad;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Marca;
use App\Modelo;
use App\PlanUnidad;
use App\TiposVehiculo;
use App\UnidadNotificacion;

class UnidadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $tipo_unidades = [
            'todos' => 'Todos',
            'movil' => 'Móvil',
            'equipo' => 'Equipo',
            'instalacion' => 'Instalación'
        ];

        $estados = [
            'todas' => 'Todas',
            'activas' => 'Activas',
            'desactivadas' => 'Desactivadas'
        ];

        $unidades = Unidad::withTrashed()
                    ->orderBy('num_interno', 'ASC');

        if (!empty($request['buscar'])){
            $unidades = $unidades->where('num_interno', 'like', '%'.$request['buscar'].'%');
        }

        if (!empty($request['tipo_unidad'])){
            if($request['tipo_unidad'] != 'Todos') {
                $unidades = $unidades->where('tipo_unidad', $request['tipo_unidad']);
            }
        }

        if(!empty($request['estado'])) {
            if ($request['estado'] == 'Desactivadas') {
                $unidades = $unidades->onlyTrashed();
            } else if ($request['estado'] == 'Activas') {
                $unidades = $unidades->where('deleted_at', NULL);
            }
        }

        $unidades = $unidades->paginate(10);

        $buscar = $request['buscar'] ?? "";
        $buscar_unidad = $request['tipo_unidad'] ?? "";
        $filtro_estado = $request['estado'] ?? "";

        return view('admin.unidades.index', [
            'unidades' => $unidades,
            'buscar' => $buscar,
            'buscar_unidad' => $buscar_unidad,
            'filtro_estado' => $filtro_estado,
            'tipo_unidades' => $tipo_unidades,
            'estados' => $estados
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $marcas = Marca::get();
        $modelos = Modelo::get();
        $carrocerias = Carroceria::get();
        $aires_acondicionados = AireAcondicionado::get();
        $tipos_vehiculos = TiposVehiculo::get();
        $bases_operaciones = BaseOperacion::get();
        // $tipo_unidades = [
        //     'movil' => 'Móvil',
        //     'equipo' => 'Equipo',
        //     'instalacion' => 'Instalacion'
        // ];

        return view('admin.unidades.create', [
            'marcas' => $marcas,
            'carrocerias' => $carrocerias,
            'aires_acondicionados' => $aires_acondicionados,
            'tipos_vehiculos' => $tipos_vehiculos,
            'bases_operaciones' => $bases_operaciones,
            // 'tipo_unidades' => $tipo_unidades,
            'modelos' => $modelos
            ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate(
            [
                // 'tipo_unidad'           => 'required',
                'num_interno'           => 'required|unique:unidades',
                'marca_id'              => 'required',
                'modelo_id'             => 'required|exists:modelos,id',
                'num_serie'             => 'required',
                'num_motor'             => 'required',
                'dominio'               => 'required',
                'carroceria_id'         => 'required|exists:carrocerias,id',
                'cantidad_asientos'     => 'required|integer|min:0',
                'aire_acondicionado_id' => 'required|exists:aires_acondicionados,id',
                'puesta_servicio'       => 'nullable|date',
                'tipo_vehiculo_id'      => 'required|exists:tipos_vehiculos,id',
                'motor'                 => 'required',
                'base_operacion_id'     => 'required|exists:bases_operaciones,id',
                'observaciones'         => 'nullable',
            ],
            [
                'num_interno.required' => 'El campo n° interno es obligatorio.',
                'marca_id.required' => 'El campo marca es obligatorio.',
                'marca_id.exists' => 'La marca seleccionada no existe en la base de datos.',
                'modelo_id.required' => 'El campo modelo es obligatorio.',
                'modelo_id.exists' => 'El modelo seleccionado no existe en la base de datos.',
                'num_serie.required' => 'El campo n° serie es obligatorio.',
                'num_motor.required' => 'El campo n° motor es obligatorio.',
                'carroceria_id.required' => 'El campo carrocería es obligatorio.',
                'carroceria_id.exists' => 'La carroceria seleccionada no existe en la base de datos.',
                'aire_acondicionado_id.required' => 'El campo aire acondicionado es obligatorio.',
                'aire_acondicionado_id.exists' => 'El aire acondicionado seleccionado no existe en la base de datos.',
                'tipo_vehiculo_id.required' => 'El campo tipo de vehículo es obligatorio.',
                'tipo_vehiculo_id.exists' => 'El tipo de vehículo seleccionado no existe en la base de datos.',
                'base_operacion_id.required' => 'El campo asignado a base es obligatorio.',
                'base_operacion_id.exists' => 'La base de operación seleccionada no existe en la base de datos.',
            ]
    );

        $unidad = Unidad::create([
            'modelo_id' => $request->modelo_id,
            'num_interno' => str_pad($request->num_interno, 5, '0', STR_PAD_LEFT),
            'num_serie' => $request->num_serie,
            'num_motor' => $request->num_motor,
            'dominio' => $request->dominio,
            'carroceria_id' => $request->carroceria_id,
            'cantidad_asientos' => $request->cantidad_asientos,
            'aire_acondicionado_id' => $request->aire_acondicionado_id,
            'puesta_servicio' => $request->puesta_servicio,
            'tipo_vehiculo_id' => $request->tipo_vehiculo_id,
            'motor' => $request->motor,
            'base_operacion_id' => $request->base_operacion_id,
            'observaciones' => $request->observaciones
        ]);

        if($unidad->save()){
            notify()->success("La unidad se agregó correctamente","Éxito: ","topRight");
            return redirect()->route('admin.unidades');
        }else{
            notify()->error("Hubo un error al guardar la unidad. Por favor, inténtalo nuevamente.","Error: ","topRight");
            return redirect($this->referer());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Unidad  $unidad
     * @return \Illuminate\Http\Response
     */
    public function show(Unidad $unidad)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Unidad  $unidad
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $unidad = Unidad::find($id);
        $marcas = Marca::get();
        $modelos = Modelo::get();
        $carrocerias = Carroceria::get();
        $aires_acondicionados = AireAcondicionado::get();
        $tipos_vehiculos = TiposVehiculo::get();
        $bases_operaciones = BaseOperacion::get();
        // $tipo_unidades = [
        //     'movil' => 'Móvil',
        //     'equipo' => 'Equipo',
        //     'instalacion' => 'Instalacion'
        // ];
        $modelos_por_marca = Modelo::where('marca_id', $unidad->modelo->marca_id)->get();

        return view('admin.unidades.edit', [
            'unidad' => $unidad,
            'marcas' => $marcas,
            'carrocerias' => $carrocerias,
            'aires_acondicionados' => $aires_acondicionados,
            'tipos_vehiculos' => $tipos_vehiculos,
            'bases_operaciones' => $bases_operaciones,
            // 'tipo_unidades' => $tipo_unidades,
            'modelos' => $modelos,
            'modelos_por_marca' => $modelos_por_marca,
            ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Unidad  $unidad
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Unidad $unidad)
    {
        $unidad = Unidad::find($request->id);

        $request->validate(
        [
            // 'tipo_unidad'           => 'required',
            'num_interno'           => 'required|unique:unidades,num_interno,' . $unidad->id,
            'marca_id'              => 'required',
            'modelo_id'             => 'required|exists:modelos,id',
            'num_serie'             => 'required',
            'num_motor'             => 'required',
            'dominio'               => 'required',
            'carroceria_id'         => 'required|exists:carrocerias,id',
            'cantidad_asientos'     => 'required|integer|min:0',
            'aire_acondicionado_id' => 'required|exists:aires_acondicionados,id',
            'puesta_servicio'       => 'nullable|date',
            'tipo_vehiculo_id'      => 'required|exists:tipos_vehiculos,id',
            'motor'                 => 'required',
            'base_operacion_id'     => 'required|exists:bases_operaciones,id',
            'observaciones'         => 'nullable',
        ],
        [
            'num_interno.required' => 'El campo n° interno es obligatorio.',
            'marca_id.required' => 'El campo marca es obligatorio.',
            'marca_id.exists' => 'La marca seleccionada no existe en la base de datos.',
            'modelo_id.required' => 'El campo modelo es obligatorio.',
            'modelo_id.exists' => 'El modelo seleccionado no existe en la base de datos.',
            'num_serie.required' => 'El campo n° serie es obligatorio.',
            'num_motor.required' => 'El campo n° motor es obligatorio.',
            'carroceria_id.required' => 'El campo carrocería es obligatorio.',
            'carroceria_id.exists' => 'La carroceria seleccionada no existe en la base de datos.',
            'aire_acondicionado_id.required' => 'El campo aire acondicionado es obligatorio.',
            'aire_acondicionado_id.exists' => 'El aire acondicionado seleccionado no existe en la base de datos.',
            'tipo_vehiculo_id.required' => 'El campo tipo de vehículo es obligatorio.',
            'tipo_vehiculo_id.exists' => 'El tipo de vehículo seleccionado no existe en la base de datos.',
            'base_operacion_id.required' => 'El campo asignado a base es obligatorio.',
            'base_operacion_id.exists' => 'La base de operación seleccionada no existe en la base de datos.',
        ]
    );


        // $unidad->tipo_unidad = $request->tipo_unidad;
        $unidad->num_interno = str_pad($request->num_interno, 5, '0', STR_PAD_LEFT);
        $unidad->modelo_id = $request->modelo_id;
        $unidad->num_serie = $request->num_serie;
        $unidad->num_motor = $request->num_motor;
        $unidad->dominio = $request->dominio;
        $unidad->carroceria_id = $request->carroceria_id;
        $unidad->cantidad_asientos = $request->cantidad_asientos;
        $unidad->aire_acondicionado_id = $request->aire_acondicionado_id;
        $unidad->puesta_servicio = $request->puesta_servicio;
        $unidad->tipo_vehiculo_id = $request->tipo_vehiculo_id;
        $unidad->motor = $request->motor;
        $unidad->base_operacion_id = $request->base_operacion_id;
        $unidad->observaciones = $request->observaciones;

        if($unidad->save()){
            notify()->success("La unidad se editó correctamente","Éxito: ","topRight");
            return redirect()->route('admin.unidades');
        }else{
            notify()->error("Hubo un error al editar la unidad. Por favor, inténtalo nuevamente.","Error: ","topRight");
            return redirect($this->referer());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Unidad  $unidad
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $unidad = Unidad::where('id', $id)->with([
                'vinculaciones.plan',
                'ordenes_trabajo.base_operacion',
                'ordenes_trabajo' => function($q) {
                    $q->where('status' , 'Abierta');
                }
        ])->first();

        if($unidad->ordenes_trabajo->count()) {
            $array["estado"] = false;

            $arrayLi = [];
            foreach ($unidad->ordenes_trabajo as $orden_trabajo) { 
                $arrayLi[] = "<li>".$orden_trabajo->numeracion.'-'.$orden_trabajo->base_operacion->nombre."</li>";
            }
           
            $mensaje = "La unidad tienen una orden de trabajo o más abierta por lo que no es posible desactivarla"."<br>
                Ordenes de trabajo relacionadas:
                <ul>".
                   implode($arrayLi);
                "</ul>";
            $array["mensaje"] = $mensaje;

            return response()->json($array);
        }

        $unidad->desactivado = $request->dias;
        $unidad->save();

        $descripcion = "Se cumplió la fecha de desactivación de la unidad " . $unidad->num_interno . ". ¿Desea activarla?";

        UnidadNotificacion::create([
            'descripcion' => $descripcion,
            'fecha' => $unidad->desactivado,
            'user_id' => auth()->user()->id,
            'unidad_id' => $unidad->id
        ]);

        $unidad->delete();

        $array = ["estado" => false, "mensaje" => ""];

       
        if ($unidad->deleted_at != null) {
            $array["estado"] = true;
            $array["mensaje"] = "La unidad se desactivó correctamente";
        } else {
            $array["mensaje"] = "Hubo un error al desactivar la unidad. Por favor, inténtalo nuevamente.";
        }

        return response()->json($array);
    }

    public function restore(Request $request)
    {
        $total_kms = 0;
        $cantidad = 0;
        $promedio = 0;
        $porcentaje = 0;

        $huc = new HistorialUnidadController;

        $unidad = Unidad::withTrashed()->find($request->id);
        $unidad->desactivado = null;
        $unidad->save();

        // Buscamos el historial de la unidad
        $historiales = HistorialUnidad::where('unidad_id', $unidad->id)
                        ->orderByDesc('id')
                        // ->take(3)
                        ->where('created_at', '>=', HistorialUnidad::getValidacionMeses())
                        ->get();

        $ultima = $historiales->first();

        foreach ($historiales as $historial) {
            $total_kms += $historial->kilometraje;
            $cantidad += 1;
        }

        /* if ($cantidad > 0) {
            $promedio = round($total_kms / $cantidad, 2);
        } */
        $promedio = $huc->getPromedioByUnidad($unidad->id);

        $porcentaje = $promedio + round($promedio * 0.30);
        

        // Defino un array de estado de petición
        $array = ["estado" => false, "mensaje" => ""];

        $es_valido = true;

        if ($request->validacion == "true") {
            // Valida el kilometraje ingresado
            if ((float) $request->kilometraje < $ultima->kilometraje) {
                $es_valido = false;
                $mensaje = 'Los kilómetros ingresados son menores al último kilometraje registrado en la unidad. Por favor, inténtelo nuevamente.';
            } 
            
            if ((float) $request->kilometraje > $porcentaje) {
                $es_valido = false;
                $mensaje = 'El kilometraje ingresado supera el 30% del promedio de kilómetros cargados en la unidad. ¿Desea guardar de igual modo?';
            }
        }

        if ($es_valido) {
            // Establecemos el nuevo kilommetraje
            $historial = HistorialUnidad::create([
                'unidad_id' => $unidad->id,
                'kilometraje' => $request->kilometraje,
                'created_at' => now()
            ]);

            // Eliminamos la notificación
            $notificacion = UnidadNotificacion::where('unidad_id', $unidad->id)->first();

            if ($notificacion != null) {
                $notificacion->delete();
            }

            $array["estado"] = true;
            $array["mensaje"] = "La unidad se activó correctamente";
        }

        if ($array["estado"] == true)
        {
            $unidad->restore();
            $historial->save();
        } else {
            $array["mensaje"] = $mensaje;
        }

        return response()->json([$array, $historial]);
    }

    public function forceDelete($id)
    {
        $unidad = Unidad::withTrashed()->find($id);
        $vinculacion = PlanUnidad::where('unidad_id', $id)->with(['plan'])->first();

        if (!empty($vinculacion)) {
            notify()->error('La unidad tienen una vinculación activa al plan '.$vinculacion->plan->nombre.' por lo que no es posible eliminarla. Debe desvincular la unidad del plan.', "Error: ", "topRight");
        } else {
            $unidad->forceDelete();
            notify()->success('La unidad se eliminó definitivamente del sistema', "Éxito: ", "topRight");
        }

        return back();
    }

    /**
     * Buscar unidades del mismo plan que otra.
     *
     */
    public function buscarUnidadesPlan($id) {

        $array = ["estado" => false, "mensaje" => ""];

        $unidad = Unidad::where('id', $id)
            ->whereHas('vinculaciones')
            ->with('vinculaciones.plan')
            ->first();

        $unidades = Unidad::whereHas('vinculaciones', function($q) use($unidad) {
            $q->where('plan_id', $unidad->vinculaciones[0]->plan_id);
        })
            ->where('id', '!=', $id)
            ->get();
    
        if (!empty($unidades)) {
            $array["estado"] = true;
            $array["mensaje"] = 'Se encontraron unidades.';
            $array["respuesta"] = $unidades;
        } else {
            $array["estado"] = false;
            $array["mensaje"] = 'No se encontraron unidades del mismo plan.';
            $array["respuesta"] = '';
        }
    
        return response()->json([
            'estado' => $array["estado"],
            'mensaje' => $array["mensaje"],
            'respuesta' => $array["respuesta"]
        ]);
        
    }
}
