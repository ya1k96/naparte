<?php

namespace App\Http\Controllers\Admin;

use App\HistorialUnidad;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Unidad;
use App\UnidadNotificacion;

class UnidadNotificacionController extends Controller
{
    public function index(Request $request)
    {
    }

    /**
     * Desactivar.
     * Esta función va a eliminar la notificación y vuelve a activar la unidad correspondiente.
     * @param Request $request
     * @return json
     */
    public function desactivar(Request $request)
    {
        try {
            $status_code = 200;
            $total_kms = 0;
            $cantidad = 0;
            $promedio = 0;
            $porcentaje = 0;
            $array = ["estado" => false, "mensaje" => ""];

            $notificacion = UnidadNotificacion::where('id', $request->notification_id)->first();
            $unidad = Unidad::withTrashed()->find($request->unidad_id);

            $huc = new HistorialUnidadController;

            if ($request->kilometraje != "") {
                $historiales = HistorialUnidad::where('unidad_id', $request->unidad_id)
                    ->orderByDesc('id')
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

                $es_valido = true;

                // Validamos el kilometraje ingresado
                if ($request->validacion == "true") {
                    if($ultima){
                        if ((float) $request->kilometraje < $ultima->kilometraje) {
                            $es_valido = false;
                            $mensaje = 'Los kilómetros ingresados son menores al último kilometraje registrado en la unidad. Por favor, inténtelo nuevamente.';
                        }
                        if ((float) $request->kilometraje > $porcentaje) {
                            $es_valido = false;
                            $mensaje = 'El kilometraje ingresado supera el 30% del promedio de kilómetros cargados en la unidad. ¿Desea guardar de igual modo?';
                        }
                    }
                }

                if ($es_valido) {
                    $historial = HistorialUnidad::create([
                        'unidad_id' => $request->unidad_id,
                        'kilometraje' => $request->kilometraje
                    ]);

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
            }else{
                $array["mensaje"] = "No se ha enviado kilometraje";
                $status_code = 404;
            }
        } catch (\Throwable $th) {
            $array["mensaje"] = $th->getMessage();
            $status_code = $th->getCode();
        }

        return response()->json($array, $status_code);
    }

    /**
     * Extender.
     * Esta función va a extender el plazo de notificación de vencimiento de la unidad.
     * @param Request $request
     * @return json
     */
    public function extender(Request $request)
    {
        try {
            $status_code = 200;
            $array = ["estado" => false, "mensaje" => ""];

            $notificacion = UnidadNotificacion::where('id', $request->notification_id)->first();
            $unidad = Unidad::withTrashed()->find($request->unidad_id);

            if ($request->extender != "") {
                $unidad->desactivado = $request->extender;

                if ($unidad->save()) {
                    $descripcion = "Se cumplió la fecha de desactivación de la unidad " . $unidad->num_interno . ". ¿Desea activarla?";

                    //'fecha' => now()->addDays($unidad->desactivado)->toDateString(), //--> solo funcionaba con la cantidad de días
                    $notificacion->fecha = $unidad->desactivado;
                    $notificacion->descripcion = $descripcion;
                    if ($notificacion->save()) {
                        $array["estado"] = true;
                        $array["mensaje"] = "La unidad se actualizó correctamente. Aviso actualizado al ". date('d-m-Y', strtotime($unidad->desactivado));
                    }else{
                        $array["mensaje"] = "El aviso no se pudo actualizar correctamente.";
                    }

                } else {
                    $array["mensaje"] = "La unidad no se pudo actualizar correctamente.";
                }

            }else{
                $array["mensaje"] = "No se ha enviado la fecha a la que quiere extender.";
                $status_code = 404;
            }
        } catch (\Throwable $th) {
            $array["mensaje"] = $th->getMessage();
            $status_code = $th->getCode();
        }

        return response()->json($array, $status_code);
    }
}
