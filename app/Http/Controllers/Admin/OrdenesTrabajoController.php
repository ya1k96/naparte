<?php

namespace App\Http\Controllers\Admin;

use App\MantenimientoRutinario;
use App\BaseOperacion;
use App\Componente;
use App\Dia;
use App\Especialidad;
use App\HistorialOrdenesTrabajo;
use App\HistorialUnidad;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\OrdenesTrabajo;
use App\Personal;
use App\PlanUnidad;
use App\Unidad;
use App\Tarea;
use App\User;
use App\Vale;
use App\ValeDetalle;
use App\RecursoActividad;
use App\Http\Controllers\Admin\HistorialUnidadController;
use Barryvdh\Debugbar\LaravelDebugbar;
use Carbon\Carbon;
use DateInterval;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache as FacadesCache;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use PDF;

use function PHPSTORM_META\type;

class OrdenesTrabajoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $ordenes = OrdenesTrabajo::withTrashed()->with(['unidad', 'user', 'vale']);
        $tipos_ordenes = OrdenesTrabajo::$tipos_ordenes;
        $status_ordenes = OrdenesTrabajo::$status_ordenes;
        $numero_unidades = Unidad::all();
        $bases_operacion = BaseOperacion::all();

        if (!empty($request['buscar'])){
            $ordenes = $ordenes->where('numeracion', $request['buscar']);
        }
        if (!empty($request['tipo_orden'])){
            $ordenes = $ordenes->where('tipo_orden', $request['tipo_orden']);
        }
        if (!empty($request['status_orden'])){
            $ordenes = $ordenes->where('status', $request['status_orden']);
        }
        if (!empty($request['numero_unidad'])){
            /* $ordenes = $ordenes->where('unidad.num_interno', $request['numero_unidad']); */
            $ordenes = $ordenes->whereHas('unidad', function ($q) use($request) {
                return $q->where('num_interno', $request['numero_unidad']);
                });
        }
        if (!empty($request['base_operacion'])){
            $ordenes = $ordenes->whereHas('base_operacion', function ($q) use($request) {
                return $q->where('base_operacion_id', $request['base_operacion']);
                });
        }
        $buscar = $request['buscar'] ?? "";
        $buscar_tipo_orden = $request['tipo_orden'] ?? "";
        $buscar_status_orden = $request['status_orden'] ?? "";
        $buscar_numero_unidad = $request['numero_unidad'] ?? "";
        $buscar_base_operacion = $request['base_operacion'] ?? "";
        $ordenes = $ordenes->orderBy('id', 'desc');
        $ordenes = $ordenes->paginate(10);
        $hoy = Carbon::now();
        return view('admin.ordenes_trabajo.index', compact(
            'ordenes',
            'tipos_ordenes',
            'status_ordenes',
            'numero_unidades',
            'buscar',
            'buscar_tipo_orden',
            'buscar_status_orden',
            'buscar_numero_unidad',
            'bases_operacion',
            'buscar_base_operacion',
            'hoy'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $user = User::where('id', Auth::id())->first();
        $unidades = Unidad::whereHas('vinculaciones')->get();
        $bases_operaciones = BaseOperacion::all();
        $arrPersonal = Personal::with('especialidad')->get();
        $fecha_hoy = now()->toDateString();
        $cantidad_dias = $request['dias'] ?? 15;
        $fecha_fin = now()->addDays($cantidad_dias)->toDateString();
        return view('admin.ordenes_trabajo.create', compact(['user', 'unidades', 'bases_operaciones', 'arrPersonal', 'fecha_hoy', 'fecha_fin']));
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
                'fecha_hora_devolucion' => 'nullable|after_or_equal:fecha_hora_recepcion',
                //'fecha_hora_inicio' => 'nullable|required_with:kilometraje',
            ]);

        $orden = new OrdenesTrabajo();

        $orden->user_id = $request['user_id'];
        $orden->unidad_id = $request['unidad_id'];
        $orden->tarea_a_realizar = $request['tarea_a_realizar'];

        if($request['personal'])
          $orden->personal = json_encode($request['personal']);

        $orden->tipo_orden = $request['tipo_orden'];
        $orden->base_operacion_id = $request['base_operacion_id'];
        $orden->fecha_hora_recepcion = $request['fecha_hora_recepcion'];
        $orden->fecha_hora_devolucion = $request['fecha_hora_devolucion'];
        $orden->hora_inicio_trabajo = $request['hora_inicio_trabajo'];
        $orden->hora_fin_trabajo = $request['hora_fin_trabajo'];
        $orden->fecha_hora_inicio = $request['fecha_hora_inicio'];
        $orden->fecha_hora_fin = $request['fecha_hora_fin'];
        $orden->status = $request['status'];
        $orden->revisado_por = $request['revisado_por'];
        $orden->observaciones = $request['observaciones'];
        $orden->fecha_inicio_periodo = $request['fecha_inicio_periodo'];
        $orden->fecha_fin_periodo = $request['fecha_fin_periodo'];

        if($request->hasFile('url')) {
            $orden->url = Storage::putFile('ordenes_trabajo_correctivas', $request->file('url'));
        }

        if($request['kilometraje']) {
            $orden->kilometraje = $request['kilometraje'];
        }

        $orden->numeracion = $this->asignarNumeracion($orden->base_operacion_id);

        if($orden->save()) {
            $historial = new HistorialOrdenesTrabajo();
            $historial->user_id = $request['user_id'];
            $historial->status = $request['status'];
            $historial->ordenes_trabajo_id = $orden->id;
            $historial->fecha = $orden->created_at;
            if($request['kilometraje']) {
                $fecha_historial = new DateTime($request['fecha_hora_inicio']);
                $historial_unidad = new HistorialUnidad();
                $historial_unidad->unidad_id = $request['unidad_id'];
                $historial_unidad->kilometraje = $request['kilometraje'];
                $historial_unidad->orden_trabajo_id = $orden->id;
                $historial_unidad->created_at = $fecha_historial;
                $historial_unidad->save();
            }
            if($historial->save()) {
                $base_operacion = BaseOperacion::where('id', $orden->base_operacion_id)->first();
                notify()->success("La orden de trabajo ".$orden->numeracion. ' ' .$base_operacion->nombre." se agregó correctamente","Éxito: ","topRight");
                if($request['imprimir']) {
                    $url_imprimir = '/admin/generarPDF/'.$orden->id;
                    session()->flash('url_imprimir', $url_imprimir);
                }
                return redirect()->route('admin.ordenes-trabajo');
            }
        }else{
            notify()->error("Hubo un error al guardar la orden de trabajo. Por favor, inténtalo nuevamente.","Error: ","topRight");
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
        $orden = OrdenesTrabajo::withTrashed()->where('id', $id)->with(['unidad', 'user', 'historiales.user', 'tareas.personal' => function($query) use ($id) {
            $query->where('orden_trabajo_id', $id);
        }])->first();
        //dd($orden);
        $users = User::all();
        $unidades = Unidad::whereHas('vinculaciones')->get();
        $bases_operaciones = BaseOperacion::all();       
        $especialidades_todas = Especialidad::all();     
        $especialidades_ot = $orden->especialidad()->allRelatedIds()->toArray();
        $arrPersonal = Personal::with('especialidad')->whereIn('especialidad_id', $especialidades_ot)->get();
        $especialidades = Especialidad::whereIn('id', $especialidades_ot)->get();
        if (Storage::exists($orden->url)) {
            $url = Storage::path($orden->url);
        }else {
            $url = '';
        }
        if($orden->tipo_orden == 'Correctiva') {
            return view('admin.ordenes_trabajo.show', compact(['orden', 'users', 'unidades', 'bases_operaciones', 'url', 'especialidades_todas', 'arrPersonal' , 'especialidades']));
        } else {
            return view('admin.ordenes_trabajo.show_preventiva', compact(['orden', 'users', 'unidades', 'bases_operaciones', 'url', 'especialidades_todas', 'arrPersonal' , 'especialidades']));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $orden = OrdenesTrabajo::withTrashed()->where('id', $id)->with(['unidad', 'user'])->first();
        $user = User::where('id', Auth::id())->first();
        $unidades = Unidad::whereHas('vinculaciones')->get();
        $bases_operaciones = BaseOperacion::all();
        $arrPersonal = Personal::with('especialidad')->get();
        if (Storage::exists($orden->url)) {
            $url = Storage::path($orden->url);
        }else {
            $url = '';
        }

        return view('admin.ordenes_trabajo.edit', compact(['orden', 'user', 'unidades', 'bases_operaciones', 'url', 'arrPersonal']));
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
        $request->validate(
            [
                'fecha_hora_devolucion' => 'nullable|after_or_equal:fecha_hora_recepcion',
                'fecha_hora_inicio' => 'nullable|required_with:kilometraje',
            ]);

        $orden = OrdenesTrabajo::withTrashed()->where('id', $id)->first();

        $orden->user_id = $request['user_id'];
        $orden->unidad_id = $request['unidad_id'];
        $orden->tarea_a_realizar = $request['tarea_a_realizar'];

        if($request['personal'])
          $orden->personal = json_encode($request['personal']);

        $orden->tipo_orden = $request['tipo_orden'];
        $orden->base_operacion_id = $request['base_operacion_id'];
        $orden->fecha_hora_recepcion = $request['fecha_hora_recepcion'];
        $orden->fecha_hora_devolucion = $request['fecha_hora_devolucion'];
        $orden->hora_inicio_trabajo = $request['hora_inicio_trabajo'];
        $orden->hora_fin_trabajo = $request['hora_fin_trabajo'];
        $orden->fecha_hora_inicio = $request['fecha_hora_inicio'];
        $orden->fecha_hora_fin = $request['fecha_hora_fin'];
        $orden->revisado_por = $request['revisado_por'];
        $orden->observaciones = $request['observaciones'];
        $orden->comentario_mecanico = $request['comentario_mecanico'];

        if($request->hasFile('url')) {
            //Storage::delete($orden->url);
            //FacadesCache::flush();
            $orden->url = Storage::putFile('ordenes_trabajo_correctivas', $request->file('url'));
        }

        if($request['kilometraje'] && $orden->kilometraje != $request['kilometraje']) {

            $fecha_historial = new DateTime($request['fecha_hora_inicio']);
            if($orden->kilometraje == null) {
                $orden->kilometraje = $request['kilometraje'];
                $historial_unidad = HistorialUnidad::create([
                    'unidad_id' => $request['unidad_id'],
                    'kilometraje' => $request['kilometraje'],
                    'orden_trabajo_id' => $orden->id,
                    'created_at' => $fecha_historial,
                ]);
            } else {
                $orden->kilometraje = $request['kilometraje'];
                //Busco y modifico el historial existente
                $historial_unidad = HistorialUnidad::where('orden_trabajo_id', $orden->id)->first();;
                $historial_unidad->kilometraje = $orden->kilometraje;
                $historial_unidad->update();
            }

        }

        if($orden->update()) {
            notify()->success("La orden de trabajo se editó correctamente","Éxito: ","topRight");
            return redirect()->route('admin.ordenes-trabajo');
        }else{
            notify()->error("Hubo un error al editar la orden de trabajo. Por favor, inténtalo nuevamente.","Error: ","topRight");
            return redirect($this->referer());
        }

    }

    /**
     * Cerrar OT
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $orden = OrdenesTrabajo::find($request['id']);

        if($orden->tipo_orden == 'Preventiva') {
            $orden = OrdenesTrabajo::with(['tareas.componente'])->find($request['id']);
        }

        $orden->status = $request['status'];

        if($request['status'] == 'Cerrada') {
            $orden->fecha_cierre = new DateTime();

            //! Tenemos que reemplazar el orderby por el id en concreto que no se está guardando
            $historial_unidad = HistorialUnidad::where('unidad_id', $request['unidad_id'])->orderBy('id', 'DESC')->first();

            //Comento la validacion porque si no modifican el input no guarda
            //if ($historial_unidad->kilometraje != $request['kilometraje_1']) {
                $historial_unidad->create([
                    'unidad_id' => $request['unidad_id'],
                    'kilometraje' => $request['kilometraje_1'],
                ]);
            //}

        }

        if($orden->save()){
            if($orden->delete()) {
                $historial = new HistorialOrdenesTrabajo();
                $historial->user_id = Auth::id();
                $historial->status = $orden->status;
                $historial->ordenes_trabajo_id = $orden->id;
                $historial->fecha = $orden->deleted_at;
                if($historial->save()) {
                    if($request['status'] == 'Anulada') {
                        notify()->success("La orden de trabajo se anuló correctamente","Éxito: ","topRight");
                    } else {
                        //GENERAR MANTENIMIENTO RUTINARIO
                        if($orden->tipo_orden == 'Preventiva') {
                            $this->generarMantenimientosOrdenTrabajo($orden);
                        }

                        notify()->success("La orden de trabajo se cerró correctamente","Éxito: ","topRight");
                    }
                    if($orden->tipo_orden == 'Preventiva') {
                        return redirect()->route('admin.ordenes-trabajo');
                    } else {
                        if($request->has('cargar_correctivas')){
                            return redirect()->route('admin.ordenes-trabajo.show-tareas-correctiva', $orden->id);
                        } else {
                            return redirect()->route('admin.ordenes-trabajo');
                        }
                    }
                }

            }
        }
        if($request['status'] == 'Anulada') {
            notify()->error("Hubo un error al anular la orden de trabajo. Por favor, inténtalo nuevamente.","Error: ","topRight");
        }else {
            notify()->error("Hubo un error al cerrar la orden de trabajo. Por favor, inténtalo nuevamente.","Error: ","topRight");
        }
        return redirect($this->referer());
    }

    /**
     * Generar PDF para impresión de la OT
     *
     * @return void
     */
    public function generarPDF($id) {
        $orden = OrdenesTrabajo::withTrashed()->where('id', $id)->with(['unidad.modelo', 'user'])->first();
        $user = User::where('id', Auth::id())->first();
        $fecha = new DateTime();
        ($orden->fecha_hora_inicio) ? $f_h_i = new DateTime($orden->fecha_hora_inicio) : $f_h_i = null;
        ($orden->fecha_hora_fin) ? $f_h_f = new DateTime($orden->fecha_hora_fin) : $f_h_f = null;
        $personal = $orden->showPersonal;
        //dd($personal);
        $personal_list = '';
        if($personal) {
            foreach($personal as $key => $persona) {
                if($key == 0) {
                    $personal_list = $personal_list . $persona->nombre;
                } else {
                    $personal_list = $personal_list . ', '.$persona->nombre;
                }
            }
        }

        //TODO:Cambiar estas fechas por las que corresponden
        if(!empty($orden->fecha_inicio_periodo) && !empty($orden->fecha_fin_periodo)) {
            $fechaInicioPeriodo = new DateTime($orden->fecha_inicio_periodo);
            $fechaFinPeriodo = new DateTime($orden->fecha_fin_periodo);

            $dias = [];
            do {
                $dias[]= $fechaInicioPeriodo->format('d');
                $fechaInicioPeriodo->modify('+1 day');
            } while ($fechaInicioPeriodo <= $fechaFinPeriodo);

        }

        $fecha_inicio_periodo = new DateTime($orden->fecha_inicio_periodo);
        $fecha_fin_periodo = new DateTime($orden->fecha_fin_periodo);

        $data = [
            'id' => $orden->numeracion,
            'user' => $user->name,
            'unidad' => $orden->unidad->num_interno . ' / ' . $orden->unidad->modelo->nombre,
            'tarea_a_realizar' => $orden->tarea_a_realizar,
            'tipo_orden' => $orden->tipo_orden,
            'periodo' => 'del ' . $fecha_inicio_periodo->format('d/m/Y') . ' al ' . $fecha_fin_periodo->format('d/m/Y'),
            'personal' => $personal_list,
            'base_operacion' => $orden->base_operacion->nombre,
            'comentario_mecanico' => $orden->comentario_mecanico,
            'fecha_hora_inicio' => ($f_h_i)? $f_h_i->format('d/m/Y H:i') : '',
            'fecha_hora_fin' =>($f_h_f)? $f_h_f->format('d/m/Y H:i') : '',
            'lectura' => $orden->kilometraje ? $orden->kilometraje . 'km'  : '',
            'hora_inicio_trabajo' => $orden->hora_inicio_trabajo? $orden->hora_inicio_trabajo : '____________   ____________',
            'hora_fin_trabajo' => $orden->hora_fin_trabajo? $orden->hora_fin_trabajo : '____________   ____________',
            'status' => $orden->status,
            'revisado_por' => $orden->revisado_por,
            'fecha_inicio_periodo' => $fechaInicioPeriodo->format('d/m/Y'),
            'fecha_fin_periodo' => $fechaFinPeriodo->format('d/m/Y'),
            'dias' => !empty($dias)? $dias : '',
            'observaciones' => $orden->observaciones,
            'fecha_cierre' => $orden->fecha_cierre,
            'impresa' => $orden->impresa ? 'COPIA' : ' ',
            'fecha' => $fecha->format('d/m/Y H:i'),
            'dias' => !empty($dias)? $dias : '',
            'procedimiento_realizado' => ($orden->comentario_mecanico)? $orden->comentario_mecanico : '________________________________________________________________________
            ________________________________________________________________________________________________
            ________________________________________________________________________________________________
            ________________________________________________________________________________________________',
        ];
        $pdf = PDF::loadView('/admin/ordenes_trabajo/template_pdf/template', $data);
        $orden->impresa = true;
        if($orden->save()) {
            return $pdf->stream('OT_'.$orden->id.'.pdf');
            //* Para descargar
            //return $pdf->download('OT_'.$orden->id.'.pdf');
        }else{
            notify()->error("Hubo un error al descargar la OT. Por favor, inténtalo nuevamente.","Error: ","topRight");
            return redirect($this->referer());
        }
    }

    /**
     * Reabrir una OT cerrada
     *
     * @param Request $request
     * @return void
     */
    public function reabrir(Request $request) {

        $orden = OrdenesTrabajo::withTrashed()->where('id', $request['id'])->with(['vale' => function ($q) {
            return $q->withTrashed();
        }])->first();

        $orden->status = 'Abierta';
        $orden->fecha_hora_reabierta = new DateTime();
        $orden->observaciones = $orden->observaciones . ' // ' . $request->observaciones;
        $orden->usuario_reabierta_id = Auth::id();

        if($orden->restore() && $orden->update()) {

            $historial = new HistorialOrdenesTrabajo();
            $historial->user_id = Auth::id();
            $historial->status = 'Re-abierta';
            $historial->ordenes_trabajo_id = $orden->id;
            $historial->fecha = $orden->updated_at;

            if($request->has('reabrir_vale') && $orden->vale && $orden->vale->cerrado) {
                $orden->vale->restore();
                $orden->vale->cerrado = false;
                $orden->vale->update();
            }

            if($historial->save()) {
                notify()->success("La orden de trabajo se reabrió correctamente","Éxito: ","topRight");
                return redirect()->route('admin.ordenes-trabajo');
            }

        }else{
            notify()->error("Hubo un error al reabrir la orden de trabajo. Por favor, inténtalo nuevamente.","Error: ","topRight");
            return redirect()->route('admin.ordenes-trabajo');
        }

    }

    /**
     * Anular la OT.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function anular(Request $request)
    {
        $orden = OrdenesTrabajo::withTrashed()->where('id', $request['id'])->with('vale', 'movimiento')->first();

        $orden->status = 'Anulada';
        $orden->fecha_hora_anulada = new DateTime();
        $orden->observaciones = $orden->observaciones . ' // ' . $request->comentario;
        $orden->usuario_anulada_id = Auth::id();

        if($orden->update() && $orden->delete()) {
            $historial = new HistorialOrdenesTrabajo();
            $historial->user_id = Auth::id();
            $historial->status = $orden->status;
            $historial->ordenes_trabajo_id = $orden->id;
            $historial->fecha = $orden->updated_at;
            if($orden->vale) {
                $orden->vale->delete();
            }
            if($orden->movimiento) {
                $orden->movimiento->delete();
            }
            if($historial->save()) {
                notify()->success("La orden de trabajo se anuló correctamente","Éxito: ","topRight");
                return redirect()->route('admin.ordenes-trabajo');
            }
        }else{
            notify()->error("Hubo un error al anular la orden de trabajo. Por favor, inténtalo nuevamente.","Error: ","topRight");
            return redirect()->route('admin.ordenes-trabajo');
        }
    }

    /**
     * Mostrar archivo de la OT
     *
     * @param int $id
     * @return void
     */
    public function mostrarPDF($id) {
        $orden = OrdenesTrabajo::withTrashed()->where('id', $id)->with(['unidad.modelo', 'user'])->first();

        if (Storage::exists($orden->url)) {
            $file = Storage::path($orden->url);
            $explode = explode('/', $orden->url);
            return response()->download($file, $explode[1], ['Cache-Control' => 'no-cache, must-revalidate'], 'inline');
        }else {
            return redirect()->back();
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createPreventiva($fecha_inicio_periodo=null, $fecha_fin_periodo=null)
    {
        $user = User::where('id', Auth::id())->first();
        $unidades = Unidad::whereHas('vinculaciones')->get();
        $bases_operaciones = BaseOperacion::all();
        $especialidades = Especialidad::all();
        return view('admin.ordenes_trabajo.create_preventiva', compact(['user', 'unidades', 'bases_operaciones', 'especialidades']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storePreventiva(Request $request)
    {
        $request->validate(
            [
                'fecha_hora_devolucion' => 'nullable|after_or_equal:fecha_hora_recepcion',
                //'fecha_hora_inicio' => 'nullable|required_with:kilometraje',
            ]);

        $orden = new OrdenesTrabajo();

        $orden->user_id = $request['user_id'];
        $orden->unidad_id = $request['unidad_id'];

        if($request['personal'])
          $orden->personal = json_encode($request['personal']);

        $orden->tipo_orden = $request['tipo_orden'];
        $orden->base_operacion_id = $request['base_operacion_id'];
        //$orden->especialidad_id = $request['especialidad_id'];
        $orden->fecha_hora_recepcion = $request['fecha_hora_recepcion'];
        $orden->fecha_hora_devolucion = $request['fecha_hora_devolucion'];
        $orden->hora_inicio_trabajo = $request['hora_inicio_trabajo'];
        $orden->hora_fin_trabajo = $request['hora_fin_trabajo'];
        $orden->fecha_hora_inicio = $request['fecha_hora_inicio'];
        $orden->fecha_hora_fin = $request['fecha_hora_fin'];
        $orden->status = $request['status'];
        $orden->revisado_por = $request['revisado_por'];
        $orden->observaciones = $request['observaciones'];
        $orden->fecha_inicio_periodo = $request['fecha_inicio_periodo'];
        $orden->fecha_fin_periodo = $request['fecha_fin_periodo'];

        if($request->hasFile('url')) {
            $orden->url = Storage::putFile('ordenes_trabajo_correctivas', $request->file('url'));
        }

        if($request['kilometraje']) {
            $orden->kilometraje = $request['kilometraje'];
        }

        $orden->numeracion = $this->asignarNumeracion($orden->base_operacion_id);

        //dd($orden);
        if($orden->save()) {

            if($request['kilometraje']) {
                $fecha_historial = new DateTime($request['fecha_hora_inicio']);
                $historial_unidad = HistorialUnidad::create([
                    'unidad_id' => $request['unidad_id'],
                    'kilometraje' => $request['kilometraje'],
                    'orden_trabajo_id' => $orden->id,
                    'created_at' => $fecha_historial,
                ]);
            }

            if(!empty($request['tareas'])) {
                foreach($request['tareas'] as $tarea) {
                    $orden->tareas()->attach($tarea['id'], ['comentario' => $tarea['comentario'], 'fecha_estimada' => $tarea['fecha_estimada']]);

                    if(!empty($tarea['personal'])) {
                        $tarea_buscar = $orden->tareas()->where('tarea_id', $tarea['id'])->first();
                        foreach($tarea['personal'] as $personal) {
                            $tarea_buscar->personal()->attach($personal, ['orden_trabajo_id' => $orden->id]);
                        }
                    }
                }
            }

            if(!empty($request['especialidad_id'])) {
                if($request['especialidad_id'][0] == 'todas') {
                    $especialidades = Especialidad::all()->modelKeys();
                    $orden->especialidad()->attach($especialidades);
                } else {
                    $orden->especialidad()->attach($request['especialidad_id']);
                }
            }

            $historial = new HistorialOrdenesTrabajo();
            $historial->user_id = $request['user_id'];
            $historial->status = $request['status'];
            $historial->ordenes_trabajo_id = $orden->id;
            $historial->fecha = $orden->created_at;
            if($historial->save()) {
                $base_operacion = BaseOperacion::where('id', $orden->base_operacion_id)->first();
                notify()->success("La orden de trabajo ".$orden->numeracion. ' ' .$base_operacion->nombre." se agregó correctamente","Éxito: ","topRight");
                return redirect()->route('admin.ordenes-trabajo');
            }
        }else{
            notify()->error("Hubo un error al guardar la orden de trabajo. Por favor, inténtalo nuevamente.","Error: ","topRight");
            return redirect($this->referer());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editPreventiva($id)
    {
        $orden = OrdenesTrabajo::withTrashed()->where('id', $id)
            ->with([
                'unidad',
                'user',
                'tareas.componente',
                'tareas.personal' => function($query) use ($id) {
                    $query->where('orden_trabajo_id', $id);
                }, 'especialidad'])->first();
        $user = User::where('id', Auth::id())->first();
        $unidades = Unidad::whereHas('vinculaciones')->get();
        $bases_operaciones = BaseOperacion::all();
        $especialidades_ot = $orden->especialidad()->allRelatedIds()->toArray();
        $arrPersonal = Personal::with('especialidad')->whereIn('especialidad_id', $especialidades_ot)->get();
        $especialidades = Especialidad::whereIn('id', $especialidades_ot)->get();
        $todas_especialidades = false;
        //Obtengo el ultimo historial y el anterior
        if($orden->kilometraje) {
            $historial_actual = HistorialUnidad::where('unidad_id', $orden->unidad_id)->where('orden_trabajo_id', $orden->id)->first();
            $ultimo_historial = HistorialUnidad::where('unidad_id', $orden->unidad_id)->where('id', '<', $historial_actual->id)->orderByDesc('id')->first();
        } else {
            $ultimo_historial = HistorialUnidad::where('unidad_id', $orden->unidad_id)
                ->orderByDesc('updated_at')
                ->orderByDesc('kilometraje')
                ->where('updated_at', '>=', HistorialUnidad::getValidacionMeses())
                ->first();
        }
        /* if(count($especialidades) == count($especialidades_ot)){
            $todas_especialidades = true;
        } */
        if (Storage::exists($orden->url)) {
            $url = Storage::path($orden->url);
        }else {
            $url = '';
        }

        return view('admin.ordenes_trabajo.edit_preventiva', compact(['orden', 'user', 'unidades', 'bases_operaciones', 'url', 'arrPersonal', 'especialidades', 'especialidades_ot', 'todas_especialidades', 'ultimo_historial']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updatePreventiva(Request $request, $id)
    {
        $request->validate(
            [
                'fecha_hora_devolucion' => 'nullable|after_or_equal:fecha_hora_recepcion',
                //'fecha_hora_inicio' => 'nullable|required_with:kilometraje',
            ]);

        $orden = OrdenesTrabajo::withTrashed()->where('id', $id)->first();

        // $orden->user_id = $request['user_id'];
        $orden->unidad_id = $request['unidad_id'];

        // if($request['personal'])
        //   $orden->personal = json_encode($request['personal']);

        // $orden->tipo_orden = $request['tipo_orden'];
        $orden->base_operacion_id = $request['base_operacion_id'];
        //$orden->especialidad_id = $request['especialidad_id'];
        // $orden->fecha_hora_recepcion = $request['fecha_hora_recepcion'];
        // $orden->fecha_hora_devolucion = $request['fecha_hora_devolucion'];
        // $orden->hora_inicio_trabajo = $request['hora_inicio_trabajo'];
        // $orden->hora_fin_trabajo = $request['hora_fin_trabajo'];
        $orden->fecha_hora_inicio = $request['fecha_hora_inicio'];
        $orden->fecha_hora_fin = $request['fecha_hora_fin'];
        // $orden->status = $request['status'];
        // $orden->revisado_por = $request['revisado_por'];
        $orden->observaciones = $request['observaciones'];
        // $orden->fecha_inicio_periodo = $request['fecha_inicio_periodo'];
        // $orden->fecha_fin_periodo = $request['fecha_fin_periodo'];

        if($request->hasFile('url')) {
            $orden->url = Storage::putFile('ordenes_trabajo_correctivas', $request->file('url'));
        }
        if($request['kilometraje'] && $orden->kilometraje != $request['kilometraje']) {

            $fecha_historial = new DateTime($request['fecha_hora_inicio']);

            if($orden->kilometraje == null) {
                $orden->kilometraje = $request['kilometraje'];
                $historial_unidad = HistorialUnidad::create([
                    'unidad_id' => $request['unidad_id'],
                    'kilometraje' => $request['kilometraje'],
                    'orden_trabajo_id' => $orden->id,
                    'created_at' => $fecha_historial,
                ]);
            } else {
                $orden->kilometraje = $request['kilometraje'];
                //Busco y modifico el historial existente
                $historial_unidad = HistorialUnidad::where('orden_trabajo_id', $orden->id)->first();
                $historial_unidad->kilometraje = $orden->kilometraje;
                $historial_unidad->update();
            }
        }

        if($orden->update()) {

            $orden->tareas()->detach(); //Le quito todas las tareas y se las vuelvo a pegar desde la request.
            if(!empty($request['tareas'])) {
                foreach($request['tareas'] as $tarea) {
                    $orden->tareas()->attach($tarea['id'], ['comentario' => $tarea['comentario'], 'fecha_realizacion' => $tarea['fecha_realizacion'], 'fecha_estimada' => $tarea['fecha_estimada']]);

                    $tarea_buscar = $orden->tareas()->where('tarea_id', $tarea['id'])->first();
                    $tarea_buscar->personal()->detach(); //Le quito todas las tareas y se las vuelvo a pegar desde la request.
                    if(!empty($tarea['personal'])) {
                        foreach($tarea['personal'] as $personal) {
                            $tarea_buscar->personal()->attach($personal, ['orden_trabajo_id' => $orden->id]);
                        }
                    }
                }
            }

            if(!empty($request['especialidad_id'])) {
                if($request['especialidad_id'][0] == 'todas') {
                    $especialidades = Especialidad::all()->modelKeys();
                    $orden->especialidad()->sync($especialidades);
                } else {
                    $orden->especialidad()->sync($request['especialidad_id']);
                }
            }

            if($orden->status == 'Cerrada') {
                //Elimino los anteriores y los genero actualizados
                $collection = MantenimientoRutinario::where('orden_trabajo_id', $orden->id)->get(['id']);
                MantenimientoRutinario::destroy($collection->toArray());
                $this->generarMantenimientosOrdenTrabajo($orden);
            }

            notify()->success("La orden de trabajo se editó correctamente","Éxito: ","topRight");
            return redirect()->route('admin.ordenes-trabajo');

        }else{
            notify()->error("Hubo un error al guardar la orden de trabajo. Por favor, inténtalo nuevamente.","Error: ","topRight");
            return redirect($this->referer());
        }
    }

    /**
     * Generar PDF para impresión de la OT
     *
     * @return void
     */
    public function generarPDFPreventiva($id) {

        $orden = OrdenesTrabajo::withTrashed()->where('id', $id)->with([
                'unidad.modelo',
                'user',
                'especialidad',
                'tareas.componente',
                'tareas.personal' => function($query) use ($id) {
                    $query->where('orden_trabajo_id', $id);
                }
            ])->first();

        $user = User::where('id', Auth::id())->first();
        $fecha = new DateTime();
        ($orden->fecha_hora_inicio) ? $f_h_i = new DateTime($orden->fecha_hora_inicio) : $f_h_i = null;
        ($orden->fecha_hora_fin) ? $f_h_f = new DateTime($orden->fecha_hora_fin) : $f_h_f = null;
        $personal = $orden->showPersonal;
        //dd($orden);
        $personal_list = '';
        if($personal) {
            foreach($personal as $key => $persona) {
                if($key == 0) {
                    $personal_list = $personal_list . $persona->nombre;
                } else {
                    $personal_list = $personal_list . ', '.$persona->nombre;
                }
            }
        }

        //TODO:Cambiar estas fechas por las que corresponden
        if(!empty($orden->fecha_inicio_periodo) && !empty($orden->fecha_fin_periodo)) {
            $fechaInicioPeriodo = new DateTime($orden->fecha_inicio_periodo);
            $fechaFinPeriodo = new DateTime($orden->fecha_fin_periodo);

            $dias = [];
            do {
                $dias[]= $fechaInicioPeriodo->format('d');
                $dias_fecha[] = $fechaInicioPeriodo->format('d/m/Y');
                $fechaInicioPeriodo->modify('+1 day');
            } while ($fechaInicioPeriodo <= $fechaFinPeriodo);

        }

        $tareas = '';
        foreach($orden->tareas as $tarea) {
            //$tareas .= '<tr><td colspan="3" valign="TOP"><b>'.$tarea->descripcion.'</b></td></tr><tr><td colspan="1" valign="TOP"></td><td colspan="2" valign="TOP"><br>Especialidad: '.$orden->especialidad->nombre.'<br><br>Procedimiento: '.$tarea->observacion.'<br><br>Comentario: '.$tarea->pivot->observacion.'<br><br></td></tr>';
        }
        /* dd($orden); */

        $fecha_inicio_periodo = new DateTime($orden->fecha_inicio_periodo);
        $fecha_fin_periodo = new DateTime($orden->fecha_fin_periodo);
        $data = [
            'id' => $orden->numeracion,
            'user' => $user->name,
            'unidad' => $orden->unidad->num_interno . ' / ' . $orden->unidad->modelo->nombre,
            'tipo_orden' => $orden->tipo_orden,
            'personal' => $personal_list,
            'periodo' => 'del ' . $fecha_inicio_periodo->format('d/m/Y') . ' al ' . $fecha_fin_periodo->format('d/m/Y'),
            'base_operacion' => $orden->base_operacion->nombre,
            'comentario_mecanico' => $orden->comentario_mecanico,
            'fecha_hora_inicio' => ($f_h_i)? $f_h_i->format('d/m/Y H:i') : '',
            'fecha_hora_fin' =>($f_h_f)? $f_h_f->format('d/m/Y H:i') : '',
            'lectura' => $orden->kilometraje ? $orden->kilometraje . 'km' : '',
            'fecha_inicio_periodo' => $fechaInicioPeriodo->format('d/m/Y'),
            'fecha_fin_periodo' => $fechaFinPeriodo->format('d/m/Y'),
            'hora_inicio_trabajo' => $orden->hora_inicio_trabajo? $orden->hora_inicio_trabajo : '____________   ____________',
            'hora_fin_trabajo' => $orden->hora_fin_trabajo? $orden->hora_fin_trabajo : '____________   ____________',
            'status' => $orden->status,
            'revisado_por' => $orden->revisado_por,
            'observaciones' => $orden->observaciones,
            'fecha_cierre' => $orden->fecha_cierre,
            'impresa' => $orden->impresa ? 'COPIA' : ' ',
            'fecha' => $fecha->format('d/m/Y H:i'),
            'dias' => !empty($dias)? $dias : '',
            'dias_fecha' => !empty($dias_fecha)? $dias_fecha : '',
            'procedimiento_realizado' => ($orden->comentario_mecanico)? $orden->comentario_mecanico : '________________________________________________________________________
            ________________________________________________________________________________________________
            ________________________________________________________________________________________________
            ________________________________________________________________________________________________',
            'tareas' => $orden->tareas,
            'especialidad' => $orden->especialidad,
        ];
        //dd($data['tareas']);
        $pdf = PDF::loadView('/admin/ordenes_trabajo/template_pdf/template_preventiva', $data);
        $orden->impresa = true;
        if($orden->save()) {
            return $pdf->stream('OT_'.$orden->id.'.pdf');
            //return $pdf->download('OT_'.$orden->id.'.pdf');
        }else{
            notify()->error("Hubo un error al descargar la OT. Por favor, inténtalo nuevamente.","Error: ","topRight");
            return redirect($this->referer());
        }
    }

    /**
     * Asignar numeración a ordenes de trabajo según la base de operaciones
     *
     * @return void
     */
    public function asignarNumeracion($base_id) {
        $ot_existentes = OrdenesTrabajo::withTrashed()->where('base_operacion_id', $base_id)->max('numeracion');
        return $ot_existentes + 1;
    }

    /**
     * Asignar numeración a ordenes de trabajo según la base de operaciones
     *
     * @return void
     */
    public function getOrdenHistoriales($id) {
        $orden_trabajo = OrdenesTrabajo::where('id', $id)
            ->first();
        $historial_unidades = HistorialUnidad::where('unidad_id', $orden_trabajo->unidad_id)->orderBy('id', 'DESC')->get();

        return $historial_unidades;
    }

    /**
     * Lista las unidades que deban hacerse un mantenimiento preventivo en el período (según fecha, kms o combinado)
     *
     * @return void
     */
    public function generadorOTS(Request $request) {

        $fecha_hoy = now()->toDateString();
        $cantidad_dias = $request['dias'] ?? 15;
        $fecha_fin = now()->addDays($cantidad_dias)->toDateString();
        //dd($fecha_fin);

        //* Trae todas las unidades con los ultimos mantenimientos de cada tarea.
        $unidades = Unidad::whereHas('vinculaciones')
                            ->whereHas('mantenimientos'/* , function($query){
                                $query->whereIn('mantenimiento_rutinario.id', function($q) {
                                    $q->selectRaw("MAX(mantenimiento_rutinario.id) as max_id")
                                    ->groupBy(['mantenimiento_rutinario.unidad_id', 'mantenimiento_rutinario.tarea_id'])
                                    ->orderBy('max_id', 'DESC');
                                })
                                ->selectRaw("unidad_id, tarea_id, id, MAX(`id`) as mantenimiento_id, ult_mantenimiento, ult_mantenimiento_fecha, frecuencia, frecuencia_dias, prox_mantenimiento, prox_mantenimiento_fecha, mantenimiento_modif, mantenimiento_modif_fecha")
                                ->groupBy(['unidad_id', 'tarea_id'])
                                ->orderBy('id', 'DESC');
                            } */)
                            ->with([
                                'modelo',
                                'mantenimientos' => function ($query) {
                                    // para no traer tareas eliminadas.
                                    $query->orderBy('created_at', 'DESC')->whereHas('tarea');
                                },
                                //'mantenimientos.tarea',
                                'historiales' => function($query){
                                    $query->where('created_at', '>=', HistorialUnidad::getValidacionMeses())
                                    ->orderBy('created_at', 'desc')
                                    ->orderBy('kilometraje', 'desc');
                                },
                                'ordenes_trabajo' => function($query) use ($fecha_hoy, $fecha_fin){
                                    $query->with('tareas')
                                    ->whereDate('fecha_inicio_periodo', '<=', $fecha_hoy)
                                    ->whereDate('fecha_fin_periodo', '>=', $fecha_fin);
                                },
                                'ordenes_trabajo.tareas',
                                /* 'vinculaciones.plan.componentes.tareas', */
                                ])
                            ->orderBy('id', 'desc')
                            ->get();
        //dd($unidades[0]);

        $especialidades = Especialidad::all();
        $dias = OrdenesTrabajo::$dias;

        //Agregar acá las unidades que tengan tareas para mantenimiento.
        $unidades_para_mantenimiento = $this->funcionGeneradorOts($unidades, $request, $cantidad_dias, $fecha_hoy, $fecha_fin);

        /* Armé un array así:
        $unidades_para_mantenimiento[$key] => [
                                'unidad' => $unidad,
                                'tareas_pendientes' => [
                                                    $tarea1,
                                                    $tarea2,
                                ]
        ]
        */

        //dd($unidades_para_mantenimiento);
        $buscar = $request['buscar'] ?? "";

        return view('admin.ordenes_trabajo.generador_ots', compact(['unidades_para_mantenimiento', 'especialidades', 'buscar', 'dias', 'fecha_hoy', 'fecha_fin']));

    }

    /**
     * Pantalla de alta de nueva OT Preventiva a partir de las tareas seleccionadas en el filtro.
     *
     * @param Request $request
     * @return void
     */
    public function generarOT(Request $request) {

        //dd($request);
        $req = $request;

        $fecha_hoy = Carbon::now()->toDateString();
        $fecha_fin = Carbon::parse($request['fecha_fin'])->toDateString();

        $unidad_id = $req['unidad_id'];
        $tareas_ids = array_keys($req['unidades'][$unidad_id]['tareas']);
        $fechas_estimadas = $req['unidades_tareas'][$unidad_id]['tareas'];

        $user = User::where('id', Auth::id())->first();
        $unidad = Unidad::where('id', $unidad_id)->first();
        $bases_operaciones = BaseOperacion::all();

        $tareas = Tarea::whereIn('id', $tareas_ids)->with('especialidad')->get();
        $especialidades_ids = Tarea::select(['especialidad_id'])->whereIn('id', $tareas_ids)->get();
        $especialidades = Especialidad::whereIn('id', $especialidades_ids)->get();
        $especialidades_ids = $especialidades->pluck('id')->toArray();
        $personal = Personal::all()->whereIn('especialidad_id', $especialidades_ids)/* ->makeHidden(['show_orden_trabajo']) */->load('especialidad');

        return view('admin.ordenes_trabajo.generar_ot', compact(['user', 'req', 'unidad', 'bases_operaciones', 'especialidades', 'tareas', 'personal', 'fecha_hoy', 'fecha_fin', 'fechas_estimadas']));
    }

    /**
     * Guarda la nueva OT en BD
     *
     * @param Request $request
     * @return void
     */
    public function storeOTPreventiva(Request $request) {

        $request->validate(
            [
                'fecha_hora_devolucion' => 'nullable|after_or_equal:fecha_hora_recepcion',
                //'fecha_hora_inicio' => 'nullable|required_with:kilometraje',
            ]);

        $orden = new OrdenesTrabajo();

        $orden->user_id = $request['user_id'];
        $orden->unidad_id = $request['unidad_id'];

        if($request['personal'])
          $orden->personal = json_encode($request['personal']);

        $orden->tipo_orden = $request['tipo_orden'];
        $orden->base_operacion_id = $request['base_operacion_id'];
        $orden->fecha_hora_recepcion = $request['fecha_hora_recepcion'];
        $orden->fecha_hora_devolucion = $request['fecha_hora_devolucion'];
        $orden->hora_inicio_trabajo = $request['hora_inicio_trabajo'];
        $orden->hora_fin_trabajo = $request['hora_fin_trabajo'];
        $orden->fecha_hora_inicio = $request['fecha_hora_inicio'];
        $orden->fecha_hora_fin = $request['fecha_hora_fin'];
        $orden->status = $request['status'];
        $orden->revisado_por = $request['revisado_por'];
        $orden->observaciones = $request['observaciones'];
        $orden->fecha_inicio_periodo = $request['fecha_inicio_periodo'];
        $orden->fecha_fin_periodo = $request['fecha_fin_periodo'];

        if($request->hasFile('url')) {
            $orden->url = Storage::putFile('ordenes_trabajo_correctivas', $request->file('url'));
        }

        if($request['kilometraje']) {
            $orden->kilometraje = $request['kilometraje'];
        }

        $orden->numeracion = $this->asignarNumeracion($orden->base_operacion_id);

        if($orden->save()) {

            if($request['kilometraje']) {
                $fecha_historial = new DateTime($request['fecha_hora_inicio']);
                $historial_unidad = HistorialUnidad::create([
                    'unidad_id' => $request['unidad_id'],
                    'kilometraje' => $request['kilometraje'],
                    'orden_trabajo_id' => $orden->id,
                    'created_at' => $fecha_historial,
                ]);
            }

            if(!empty($request['tareas'])) {
                foreach($request['tareas'] as $tarea) {
                    $orden->tareas()->attach($tarea['id'], ['comentario' => $tarea['comentario'], 'fecha_estimada' => $tarea['fecha_estimada']]);

                    if(!empty($tarea['personal'])) {
                        $tarea_buscar = $orden->tareas()->where('tarea_id', $tarea['id'])->first();
                        foreach($tarea['personal'] as $personal) {
                            $tarea_buscar->personal()->attach($personal, ['orden_trabajo_id' => $orden->id]);
                        }
                    }
                }
            }

            if(!empty($request['especialidad_id'])) {
                if($request['especialidad_id'][0] == 'todas') {
                    $especialidades = Especialidad::all()->modelKeys();
                    $orden->especialidad()->attach($especialidades);
                } else {
                    $orden->especialidad()->attach($request['especialidad_id']);
                }
            }

            //* Vales
            if(!empty($request['tareas'])) {
                $tareas = [];
                foreach ($request['tareas'] as $tarea) {
                    $tareas[] = $tarea['id'];
                }

                $recurso_actividad = RecursoActividad::where('unidad_id', $orden->unidad_id)->whereIn('tarea_id', $tareas)->get();

                if (!empty($recurso_actividad)) {

                    $vale = new Vale();
                    $vale->ordenes_trabajo_id = $orden->id;
                    $vale->fecha = $orden->created_at;
                    if($vale->save()) {
                        foreach ($recurso_actividad as $data) {
                            $vale_detalle = new ValeDetalle();
                            $vale_detalle->vale_id = $vale->id;
                            $vale_detalle->tarea_id = $data['tarea_id'];
                            $vale_detalle->pieza_id = $data['pieza_id'];
                            $vale_detalle->cantidad = $data['cantidad'];
                            $vale_detalle->save();
                        }
                    } else {
                        notify()->error("Hubo un error al crear el Vale. Por favor, inténtalo nuevamente.","Error: ","topRight");
                    }
                }
            }

            $historial = new HistorialOrdenesTrabajo();
            $historial->user_id = $request['user_id'];
            $historial->status = $request['status'];
            $historial->ordenes_trabajo_id = $orden->id;
            $historial->fecha = $orden->created_at;
            if($historial->save()) {
                if($request['imprimir']) {
                    $url_imprimir = '/admin/generarPDFPreventiva/'.$orden->id;
                    session()->flash('url_imprimir', $url_imprimir);
                }
                $base_operacion = BaseOperacion::where('id', $orden->base_operacion_id)->first();
                notify()->success("La orden de trabajo ".$orden->numeracion. ' ' .$base_operacion->nombre." se agregó correctamente","Éxito: ","topRight");
                return redirect()->route('admin.ordenes-trabajo');
            }
        }else{
            notify()->error("Hubo un error al guardar la orden de trabajo. Por favor, inténtalo nuevamente.","Error: ","topRight");
            return redirect($this->referer());
        }
    }

    /**
     * Obtener las tareas para una OT Correctiva y filtrar por las que ya tienen una OT Preventiva abierta.
     *
     * @return json
     */
    public function showTareasCorrectiva($id) {

        $orden = OrdenesTrabajo::withTrashed()->where('id', $id)->with(['unidad', 'user', 'historiales.user', 'tareas.personal' => function($query) use ($id) {
            $query->where('orden_trabajo_id', $id);
        }])->first();

        $plan = PlanUnidad::where('unidad_id', $orden->unidad_id)->get();
        $componentes = Componente::with(['subcomponentes', 'tareas', 'tareas.especialidad', 'tareas.mantenimientos'])->where('plan_id', $plan[0]->plan_id)->get();

        $tareas_arr = [];

        foreach($componentes as $componente) {
            foreach($componente->tareas as $tarea) {
                $ultima_ot = OrdenesTrabajo::withTrashed()->with('tareas', 'base_operacion')->where('unidad_id', $orden->unidad_id)->where('status', 'Abierta')->whereHas('tareas', function ($query) use ($tarea) {
                    $query->where('tarea_id', '=', $tarea->id);
                })->orderBy('ordenes_trabajo.id', 'DESC')->first();


                if($ultima_ot) {
                    $tareas_arr[$tarea->id]['tarea'] = $tarea;
                    $tareas_arr[$tarea->id]['ot_abierta'] = $ultima_ot->numeracion;
                    $tareas_arr[$tarea->id]['base_operacion'] = $ultima_ot->base_operacion->nombre;
                } else {
                    $tareas_arr[$tarea->id]['tarea'] = $tarea;
                    $tareas_arr[$tarea->id]['ot_abierta'] = null;
                }
            }
        }
        /* dd($tareas_arr); */

        return view('admin.ordenes_trabajo.show_tareas_correctiva', compact(['orden', 'tareas_arr']));
    }

    /**
     * Actualizar mant. de tareas preventivas que se hacen en una OT Correctiva.
     *
     * @param Request $request
     */
    public function actualizarTareasPreventivas(Request $request) {

        $orden = OrdenesTrabajo::withTrashed()->where('id', $request['orden_trabajo_id'])->first();

        $tareas = $request['tareas_seleccionadas'];

        if(!empty($tareas)) {
            foreach($tareas as $tarea) {

                $ultimo_mantenimiento = MantenimientoRutinario::where('unidad_id', $orden->unidad_id)->where('tarea_id', $tarea)->orderBy('updated_at', 'DESC')->with('tarea')->first();

                $ult_mant = null;
                $ult_mant_fecha = null;
                $prox_mantenimiento = null;
                $prox_mantenimiento_fecha = null;
                $frecuencia = null;
                $frecuencia_dias = null;

                if($ultimo_mantenimiento->tarea->frecuencia == 'kms') {
                    $ult_mant = $orden->kilometraje;
                    $frecuencia = $ultimo_mantenimiento->tarea->kilometros;
                    $prox_mantenimiento = $ult_mant + $frecuencia;
                }
                if($ultimo_mantenimiento->tarea->frecuencia == 'dias') {
                    $ult_mant_fecha = new DateTime($orden->fecha_hora_inicio);
                    $frecuencia_dias = $ultimo_mantenimiento->tarea->dias;
                    $fecha_prox = new DateTime($orden->fecha_hora_inicio);
                    $fecha_prox->add(new DateInterval('P'.$ultimo_mantenimiento->tarea->dias.'D'));
                    $prox_mantenimiento_fecha = $fecha_prox;
                }
                if($ultimo_mantenimiento->tarea->frecuencia == 'combinado') {
                    $ult_mant = $orden->kilometraje;
                    $ult_mant_fecha = new DateTime($orden->fecha_hora_inicio);
                    $frecuencia = $ultimo_mantenimiento->tarea->kilometros;
                    $frecuencia_dias = $ultimo_mantenimiento->tarea->dias;
                    $fecha_prox = new DateTime($orden->fecha_hora_inicio);
                    $fecha_prox->add(new DateInterval('P'.$ultimo_mantenimiento->tarea->dias.'D'));
                    $prox_mantenimiento = $ult_mant + $frecuencia;
                    $prox_mantenimiento_fecha = $fecha_prox;
                }
                $mantenimiento = MantenimientoRutinario::create([
                    'unidad_id' => $orden->unidad_id,
                    'componente_id' => $ultimo_mantenimiento->tarea->componente_id,
                    'tarea_id' => $ultimo_mantenimiento->tarea->id,
                    'ult_mantenimiento' => $ult_mant,
                    'ult_mantenimiento_fecha' => $ult_mant_fecha,
                    'frecuencia' => $frecuencia,
                    'frecuencia_dias' => $frecuencia_dias,
                    'prox_mantenimiento' => $prox_mantenimiento,
                    'prox_mantenimiento_fecha' => $prox_mantenimiento_fecha,
                    'orden_trabajo_id' => $orden->id,
                    'created_at' => new DateTime($orden->fecha_hora_inicio),
                ]);

            }
        }

        notify()->success("Se generaron los mantenimientos y la orden se cerró correctamente.","Éxito: ","topRight");
        return redirect()->route('admin.ordenes-trabajo');

    }

    /**
     * Vista para generar la planilla de necasidad
    */
    public function planillaNecesidad(Request $request)
    {

        $fecha_hoy = now()->toDateString();
        $cantidad_dias = $request['dias'] ?? 15;
        $fecha_fin = now()->addDays($cantidad_dias)->toDateString();

        $bases = BaseOperacion::all();
        (!empty($request['base_operaciones'])) ? $base_busqueda = [(int) $request['base_operaciones']] : $base_busqueda = $bases->pluck('id');

        //* Trae todas las unidades con los ultimos mantenimientos de cada tarea.
        $unidades = Unidad::whereHas('vinculaciones')
            ->whereHas('mantenimientos')
            ->with([
                'modelo',
                'mantenimientos' => function ($query) {
                    // para no traer tareas eliminadas.
                    $query->orderBy('created_at', 'DESC')->whereHas('tarea');
                },
                //'mantenimientos.tarea',
                'historiales' => function($query){
                    $query->where('created_at', '>=', HistorialUnidad::getValidacionMeses())
                    ->orderBy('created_at', 'desc')
                    ->orderBy('kilometraje', 'desc');
                },
                'ordenes_trabajo' => function($query) use ($fecha_hoy, $fecha_fin){
                    $query->with('tareas')
                    ->whereDate('fecha_inicio_periodo', '<=', $fecha_hoy)
                    ->whereDate('fecha_fin_periodo', '>=', $fecha_fin);
                },
                'ordenes_trabajo.tareas',
                /* 'vinculaciones.plan.componentes.tareas', */
                ])
            ->orderBy('id', 'desc')
            ->whereIn('base_operacion_id', $base_busqueda)
            ->get();

        $dias = OrdenesTrabajo::$dias;

        //Agregar acá las unidades que tengan tareas para mantenimiento.
        $unidades_para_mantenimiento = $this->funcionGeneradorOts($unidades, $request, $cantidad_dias, $fecha_hoy, $fecha_fin, 'planilla_necesidad');
        // dd($unidades_para_mantenimiento);

        /* Armé un array así:
        $unidades_para_mantenimiento[$key] => [
                                'unidad' => $unidad,
                                'tareas_pendientes' => [
                                                    $tarea1,
                                                    $tarea2,
                                ]
        ]
        */

        $lista_recursos = [];
        $listado_piezas = [];
        foreach ($unidades_para_mantenimiento as $k => $unidad) {
            $lista_tareas = [];
            foreach ($unidad['tareas'][0] as $tareas) {
                $lista_tareas[] = $tareas['tarea']->id;
            }

            $lista_recursos[$k] = RecursoActividad::with(['pieza' => function($q) { $q->with('unidadMedida');}])
                ->where('unidad_id', $unidad[0]->id)
                ->whereIn('tarea_id', $lista_tareas)
                ->get();

            if ($lista_recursos[$k]) {
                foreach ($lista_recursos[$k] as $recurso) {
                    if (isset($listado_piezas[$recurso->pieza[0]->id])) {
                        if (isset($listado_piezas[$recurso->pieza[0]->id]['cantidad'])) {
                            $listado_piezas[$recurso->pieza[0]->id]['cantidad'] += $recurso->cantidad;
                        }
                    } else {
                        $listado_piezas[$recurso->pieza[0]->id]['descripcion'] = $recurso->pieza[0]->descripcion;
                        $listado_piezas[$recurso->pieza[0]->id]['cantidad'] = $recurso->cantidad;
                        $listado_piezas[$recurso->pieza[0]->id]['nro_pieza'] = $recurso->pieza[0]->nro_pieza;
                        $listado_piezas[$recurso->pieza[0]->id]['unidad'] = $recurso->pieza[0]->unidadMedida->nombre;
                    }
                }
            }
        }

        $buscar = $request['buscar'] ?? "";
        $base_operacion = $request['base_operaciones'] ?? "";

        return view('admin.ordenes_trabajo.planilla_necesidad', compact([
            'unidades_para_mantenimiento',
            'buscar',
            'dias',
            'fecha_hoy',
            'fecha_fin',
            'bases',
            'base_operacion',
            'listado_piezas'
        ]));
    }

    private function funcionGeneradorOts($unidades, $request, $cantidad_dias, string $fecha_hoy, string $fecha_fin, string $planilla_necesidad = null) : array {
        $huc = new HistorialUnidadController;

        $unidades_para_mantenimiento = [];

        foreach($unidades as $key=>$unidad) {
            $tareas_para_hacer = [];
            $tareas_ya_evaluadas = [];

                foreach($unidad->mantenimientos as $mantenimiento) {

                    //Esto es porque no puedo obtener los ultimos mantenimientos, asi que ordene los mant por id desc
                    //Si para una unidad ya guarde una tarea en $tareas_ya_evaluadas entonces tengo que skipear esta iteracion.
                    if(in_array($mantenimiento->tarea_id, $tareas_ya_evaluadas)) {
                        continue;
                    } else {
                        array_push($tareas_ya_evaluadas, $mantenimiento->tarea_id);
                    }

                    if($request['especialidad_id'] != null && !in_array($mantenimiento->tarea->especialidad_id, $request['especialidad_id']) && $request['especialidad_id'][0] != 'todas') {
                        //Filtro por especialidad
                        continue;
                    }

                    //* Busco el promedio, y el último historial.
                    if(count($unidad->historiales) != 0) {
                        $ultima = $unidad->historiales->first();
                        $total_kms = $unidad->historiales->sum('kilometraje');
                        $cantidad = $unidad->historiales->count();

                        $promedio = $huc->getPromedioByUnidad($unidad->id);

                        //*Busco la última lectura, le sumo el promedio por dia * cant de dias del periodo que busco
                        $promedio_dia = $promedio/30;
                        $suma_kms = $ultima->kilometraje + $promedio_dia * $cantidad_dias;

                        /* dump($mantenimiento->id);
                        dump($mantenimiento->tarea);
                        dd($mantenimiento); */

                        if($mantenimiento->tarea->frecuencia == 'kms') {
                            //*Valido sobre el prox mantenimiento en kms, (que puede estar modificado).

                            if($mantenimiento->mantenimiento_modif) {
                                if($suma_kms > $mantenimiento->mantenimiento_modif){
                                    //array_push($tareas_para_hacer, $mantenimiento->tarea);
                                    /* dump($mantenimiento->tarea->id);
                                    dump($suma_kms);
                                    dump($mantenimiento->mantenimiento_modif); */
                                    $dias_estimados = ($mantenimiento->mantenimiento_modif - $ultima->kilometraje)/$promedio_dia;
                                    $fecha_estimada = now()->addDays($dias_estimados)->toDateString();
                                    $tareas_para_hacer[$mantenimiento->tarea->id]['tarea'] = $mantenimiento->tarea;
                                    $tareas_para_hacer[$mantenimiento->tarea->id]['fecha_estimada'] = $fecha_estimada;
                                    if(!isset($unidades_para_mantenimiento[$key])) {
                                        $unidades_para_mantenimiento[$key] = [];
                                        array_push($unidades_para_mantenimiento[$key], $unidad);
                                    }
                                }
                            } else {
                                if($suma_kms > $mantenimiento->prox_mantenimiento){
                                    //array_push($tareas_para_hacer, $mantenimiento->tarea);
                                    /* dump($mantenimiento->id);
                                    dump($mantenimiento->tarea->id);
                                    dump($suma_kms);
                                    dump($mantenimiento->prox_mantenimiento); */
                                    $dias_estimados = ($mantenimiento->prox_mantenimiento - $ultima->kilometraje)/$promedio_dia;
                                    $fecha_estimada = now()->addDays($dias_estimados)->toDateString();
                                    $tareas_para_hacer[$mantenimiento->tarea->id]['tarea'] = $mantenimiento->tarea;
                                    $tareas_para_hacer[$mantenimiento->tarea->id]['fecha_estimada'] = $fecha_estimada;
                                    if(!isset($unidades_para_mantenimiento[$key])) {
                                        $unidades_para_mantenimiento[$key] = [];
                                        array_push($unidades_para_mantenimiento[$key], $unidad);
                                    }
                                }
                            }
                        }
                        if($mantenimiento->tarea->frecuencia == 'dias') {
                            //*Valido sobre la fecha del próx. mantenimiento (que puede estar modificada).
                            if($mantenimiento->mantenimiento_modif_fecha) {
                                if($mantenimiento->mantenimiento_modif_fecha >= $fecha_hoy && $mantenimiento->mantenimiento_modif_fecha <= $fecha_fin) {
                                    //array_push($tareas_para_hacer, $mantenimiento->tarea);
                                    $tareas_para_hacer[$mantenimiento->tarea->id]['tarea'] = $mantenimiento->tarea;
                                    $tareas_para_hacer[$mantenimiento->tarea->id]['fecha_estimada'] = $mantenimiento->mantenimiento_modif_fecha;
                                    if(!isset($unidades_para_mantenimiento[$key])) {
                                        $unidades_para_mantenimiento[$key] = [];
                                        array_push($unidades_para_mantenimiento[$key], $unidad);
                                    }
                                }
                            } else {
                                if($mantenimiento->prox_mantenimiento_fecha >= $fecha_hoy && $mantenimiento->prox_mantenimiento_fecha <= $fecha_fin) {
                                    //array_push($tareas_para_hacer, $mantenimiento->tarea);
                                    $tareas_para_hacer[$mantenimiento->tarea->id]['tarea'] = $mantenimiento->tarea;
                                    $tareas_para_hacer[$mantenimiento->tarea->id]['fecha_estimada'] = $mantenimiento->prox_mantenimiento_fecha;
                                    if(!isset($unidades_para_mantenimiento[$key])) {
                                        $unidades_para_mantenimiento[$key] = [];
                                        array_push($unidades_para_mantenimiento[$key], $unidad);
                                    }
                                }
                            }

                        }
                        if($mantenimiento->tarea->frecuencia == 'combinado') {

                            //* Valido tanto fecha como kms, lo que ocurra primero

                            //*Valido sobre la fecha del próx. mantenimiento (que puede estar modificada).
                            if($mantenimiento->mantenimiento_modif_fecha) {
                                if($mantenimiento->mantenimiento_modif_fecha >= $fecha_hoy && $mantenimiento->mantenimiento_modif_fecha <= $fecha_fin) {
                                    //array_push($tareas_para_hacer, $mantenimiento->tarea);
                                    $tareas_para_hacer[$mantenimiento->tarea->id]['tarea'] = $mantenimiento->tarea;
                                    if(!isset($unidades_para_mantenimiento[$key])) {
                                        $unidades_para_mantenimiento[$key] = [];
                                        array_push($unidades_para_mantenimiento[$key], $unidad);
                                    }
                                }
                            } else {
                                if($mantenimiento->prox_mantenimiento_fecha >= $fecha_hoy && $mantenimiento->prox_mantenimiento_fecha <= $fecha_fin) {
                                    //array_push($tareas_para_hacer, $mantenimiento->tarea);
                                    $tareas_para_hacer[$mantenimiento->tarea->id]['tarea'] = $mantenimiento->tarea;
                                    if(!isset($unidades_para_mantenimiento[$key])) {
                                        $unidades_para_mantenimiento[$key] = [];
                                        array_push($unidades_para_mantenimiento[$key], $unidad);
                                    }
                                }
                            }

                            //*Valido sobre el prox mantenimiento en kms, (que puede estar modificado).
                            if($mantenimiento->mantenimiento_modif) {
                                if($suma_kms > $mantenimiento->mantenimiento_modif){
                                    if(!array_key_exists($mantenimiento->tarea->id, $tareas_para_hacer)) {
                                        //array_push($tareas_para_hacer, $mantenimiento->tarea);
                                        $tareas_para_hacer[$mantenimiento->tarea->id]['tarea'] = $mantenimiento->tarea;
                                        if(!isset($unidades_para_mantenimiento[$key])) {
                                            $unidades_para_mantenimiento[$key] = [];
                                            array_push($unidades_para_mantenimiento[$key], $unidad);
                                        }
                                    }
                                }
                            } else {
                                if($suma_kms > $mantenimiento->prox_mantenimiento){
                                    if(!array_key_exists($mantenimiento->tarea->id, $tareas_para_hacer)) {
                                        //array_push($tareas_para_hacer, $mantenimiento->tarea);
                                        $tareas_para_hacer[$mantenimiento->tarea->id]['tarea'] = $mantenimiento->tarea;
                                        if(!isset($unidades_para_mantenimiento[$key])) {
                                            $unidades_para_mantenimiento[$key] = [];
                                            array_push($unidades_para_mantenimiento[$key], $unidad);
                                        }
                                    }
                                }
                            }

                            if(array_key_exists($mantenimiento->tarea->id, $tareas_para_hacer) && in_array($mantenimiento->tarea, $tareas_para_hacer[$mantenimiento->tarea->id])){
                                //Al final agrego la fecha estimada porque puede ser menor una que la otra.
                                if($mantenimiento->mantenimiento_modif) {
                                    $dias_estimados = ($mantenimiento->mantenimiento_modif - $ultima->kilometraje)/$promedio_dia;
                                    $fecha_estimada = now()->addDays($dias_estimados)->toDateString();
                                }else{
                                    $dias_estimados = ($mantenimiento->prox_mantenimiento - $ultima->kilometraje)/$promedio_dia;
                                    $fecha_estimada = now()->addDays($dias_estimados)->toDateString();
                                }

                                if($mantenimiento->mantenimiento_modif_fecha) {
                                    if($mantenimiento->mantenimiento_modif_fecha <= $fecha_estimada) {
                                        $tareas_para_hacer[$mantenimiento->tarea->id]['fecha_estimada'] = $mantenimiento->mantenimiento_modif_fecha;
                                    } else {
                                        $tareas_para_hacer[$mantenimiento->tarea->id]['fecha_estimada'] = $fecha_estimada;
                                    }
                                } else {
                                    if($mantenimiento->prox_mantenimiento_fecha <= $fecha_estimada) {
                                        $tareas_para_hacer[$mantenimiento->tarea->id]['fecha_estimada'] = $mantenimiento->prox_mantenimiento_fecha;
                                    } else {
                                        $tareas_para_hacer[$mantenimiento->tarea->id]['fecha_estimada'] = $fecha_estimada;
                                    }
                                }


                            }

                        }
                    }

                }
                //dd("fin");
                if ($planilla_necesidad != 'planilla_necesidad') {
                    if($unidad->ordenes_trabajo){
                        //Filtro, si ya tiene una OT en el período tengo que sacar las tareas de esa OT
                        foreach($unidad->ordenes_trabajo as $orden) {
                            foreach($orden->tareas as $tarea) {
                                //dd($tarea->id, $tareas_para_hacer);
                                $tarea_id = $tarea->id;
                                if (array_key_exists($tarea_id, $tareas_para_hacer)) {
                                    unset($tareas_para_hacer[$tarea_id]);
                                }
                            }
                        }
                    }
                }
                if(!empty($tareas_para_hacer)) {
                    $unidades_para_mantenimiento[$key]['tareas'] = [];
                    array_push($unidades_para_mantenimiento[$key]['tareas'], $tareas_para_hacer);
                } else {
                    unset($unidades_para_mantenimiento[$key]);
                }

        }

        return $unidades_para_mantenimiento;
    }

    /**
     * Generar los mantenimientos para las tareas de una OT.
     *
     * @param OrdenesTrabajo $orden
     * @return void
     */
    private function generarMantenimientosOrdenTrabajo($orden){
        foreach($orden->tareas as $tarea) {
            $ult_mant = null;
            $ult_mant_fecha = null;
            $prox_mantenimiento = null;
            $prox_mantenimiento_fecha = null;
            $frecuencia = null;
            $frecuencia_dias = null;
            if($tarea->frecuencia == 'kms') {
                $ult_mant = $orden->kilometraje;
                $frecuencia = $tarea->kilometros;
                $prox_mantenimiento = $ult_mant + $frecuencia;
            }
            if($tarea->frecuencia == 'dias') {
                $ult_mant_fecha = new DateTime($orden->fecha_hora_inicio);
                $frecuencia_dias = $tarea->dias;
                $fecha_prox = new DateTime($orden->fecha_hora_inicio);
                $fecha_prox->add(new DateInterval('P'.$tarea->dias.'D'));
                $prox_mantenimiento_fecha = $fecha_prox;
            }
            if($tarea->frecuencia == 'combinado') {
                $ult_mant = $orden->kilometraje;
                $ult_mant_fecha = new DateTime($orden->fecha_hora_inicio);
                $frecuencia = $tarea->kilometros;
                $frecuencia_dias = $tarea->dias;
                $fecha_prox = new DateTime($orden->fecha_hora_inicio);
                $fecha_prox->add(new DateInterval('P'.$tarea->dias.'D'));
                $prox_mantenimiento = $ult_mant + $frecuencia;
                $prox_mantenimiento_fecha = $fecha_prox;
            }
            $mantenimiento = MantenimientoRutinario::create([
                'unidad_id' => $orden->unidad_id,
                'componente_id' => $tarea->componente_id,
                'tarea_id' => $tarea->id,
                'ult_mantenimiento' => $ult_mant,
                'ult_mantenimiento_fecha' => $ult_mant_fecha,
                'frecuencia' => $frecuencia,
                'frecuencia_dias' => $frecuencia_dias,
                'prox_mantenimiento' => $prox_mantenimiento,
                'prox_mantenimiento_fecha' => $prox_mantenimiento_fecha,
                'orden_trabajo_id' => $orden->id,
                'created_at' => new DateTime($orden->fecha_hora_inicio),
            ]);
        }
        return;
    }
}
