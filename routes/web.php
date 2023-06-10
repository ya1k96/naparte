<?php

use App\Categoria;
use App\Componente;
use App\Especialidad;
use App\HistorialUnidad;
use App\Http\Controllers\Admin\HistorialUnidadController;
use App\Http\Controllers\Admin\UnidadNotificacionController;
use App\Marca;
use App\Modelo;
use App\PlanUnidad;
use App\Unidad;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    return redirect(route('admin.dashboard'));
});

Route::get('home', function () {
    return redirect(route('admin.dashboard'));
});

Route::name('admin.')->prefix('admin')->middleware('auth')->group(function () {
    Route::get('dashboard', 'DashboardController')->name('dashboard');

    Route::get('users/roles', 'UserController@roles')->name('users.roles');

    Route::resource('users', 'UserController', [
        'names' => [
            'index' => 'users'
        ]
    ]);

    Route::resource('bases_operaciones', 'Admin\BasesOperacionController', [
        'names' => [
            'index' => 'bases_operaciones'
        ]
    ]);


    // Enterprises
    Route::resource('empresas', 'Admin\EmpresaController', [
        'names' => [
            'index' => 'empresas'
        ]
    ]);

    Route::resource('unidades-de-medida', 'Admin\UnidadMedidaController', [
        'names' => [
            'index' => 'unidades-de-medida'
        ]
    ]);

    Route::resource('marcas', 'Admin\MarcaController', [
        'names' => [
            'index' => 'marcas'
        ]
    ]);

    // Proveedor
    Route::get('/proveedor/importar', 'Admin\ProveedorController@importar')->name('proveedor.importar');
    Route::get('/proveedor/descargarEjemplo', 'Admin\ProveedorController@descargarEjemplo')->name('proveedor.descargarEjemplo');
    Route::post('/proveedor/importarStore', 'Admin\ProveedorController@importarStore')->name('proveedor.importarStore');
    Route::post('proveedor/restore', 'Admin\ProveedorController@restore')->name('proveedor.restore');
    Route::resource('proveedor', 'Admin\ProveedorController', [
        'names' => [
            'index' => 'proveedor'
        ]
    ]);

    Route::post('crear-marca-unidad', function () {
        $marca = Marca::create([
            'nombre' => request()->nombre
        ]);

        if ($marca->save()) {
            return response()->json($marca);
        } else {
            return response()->json([]);
        }
    });

    Route::resource('modelos', 'Admin\ModeloController', [
        'names' => [
            'index' => 'modelos'
        ]
    ]);

    Route::post('crear-modelo-unidad', function () {
        $modelo = Modelo::create([
            'nombre' => request()->modelo,
            'marca_id' => request()->marca_id
        ]);

        if ($modelo->save()) {
            return response()->json($modelo);
        } else {
            return response()->json([]);
        }
    });

    Route::get('/piezas/importar', 'Admin\PiezaController@importar')->name('piezas.importar');
    Route::get('/piezas/descargarEjemplo', 'Admin\PiezaController@descargarEjemplo')
        ->name('piezas.descargarEjemplo');
    Route::post('/piezas/importarStore', 'Admin\PiezaController@importarStore')
        ->name('piezas.importarStore');
    Route::resource('piezas-de-catalogo', 'Admin\PiezaController', [
        'names' => [
            'index' => 'piezas-de-catalogo',
        ]
    ]);

    Route::get('piezas-de-catalogo/restore/{id}', 'Admin\PiezaController@restore')
        ->name('piezas-de-catalogo.restore');

    Route::resource('especialidades', 'Admin\EspecialidadController', [
        'names' => [
            'index' => 'especialidades'
        ]
    ]);

    Route::post('personal/restore', 'Admin\PersonalController@restore')->name('personal.restore');;
    Route::resource('personal', 'Admin\PersonalController', [
        'names' => [
            'index' => 'personal'
        ]
    ]);

    Route::resource('tipos_vehiculos', 'Admin\TiposVehiculoController', [
        'names' => [
            'index' => 'tipos_vehiculos'
        ]
    ]);

    Route::resource('carrocerias', 'Admin\CarroceriaController', [
        'names' => [
            'index' => 'carrocerias'
        ]
    ]);

    Route::resource('aires_acondicionados', 'Admin\AireAcondicionadoController', [
        'names' => [
            'index' => 'aires_acondicionados'
        ]
    ]);

    Route::resource('unidades', 'Admin\UnidadController', [
        'names' => [
            'index' => 'unidades'
        ]
    ]);

    Route::name('unidad-notificacion.')->group(function () {
        Route::get('unidad-notificacion', 'Admin\UnidadNotificacionController@index')->name('index');
        Route::post('unidad-notificacion/desactivar', 'Admin\UnidadNotificacionController@desactivar')->name('desactivar');
        Route::post('unidad-notificacion/extender', 'Admin\UnidadNotificacionController@extender')->name('extender');
    });


    Route::post('historial/editarHistoriales', 'Admin\HistorialUnidadController@editarHistoriales')->name('historial.editarHistoriales');;
    Route::resource('historial', 'Admin\HistorialUnidadController', [
        'names' => [
            'index' => 'historial'
        ]
    ]);


    Route::get('show-historial', function () {
        $historial = HistorialUnidad::where('unidad_id', request()->id)
            ->orderBy('created_at', 'desc')
            ->orderBy('kilometraje', 'desc')
            ->get();

        if ($historial != "") {
            return response()->json($historial);
        } else {
            return response()->json([]);
        }
    })->name('show-historial');

    Route::get('show-historial-promedios', function () {
        $historiales = HistorialUnidad::where('unidad_id', request()->id)
            ->select(array(
                'historiales_unidades.*',
                DB::raw("DATE_FORMAT(created_at, '%m-%Y') new_date"),  DB::raw('YEAR(created_at) year, MONTH(created_at) month')
            ))
            ->get();

        if ($historiales != "") {
            /* Voy a hacer un array de esta manera, por año y luego mes sumo el total y despues hago count y saco el promedio. */
            //$calcular_promedios[anio][mes] = totalKmMes
            $calcular_promedios = [];
            foreach ($historiales as $historial) {
                if (!isset($calcular_promedios[$historial->year])) {
                    $calcular_promedios[$historial->year] = [];
                }
                if (!isset($calcular_promedios[$historial->year][$historial->month])) {
                    $calcular_promedios[$historial->year][$historial->month]['total'] = 0;
                    $calcular_promedios[$historial->year][$historial->month]['cantidad'] = 0;
                }
                $calcular_promedios[$historial->year][$historial->month]['total'] = $calcular_promedios[$historial->year][$historial->month]['total'] + $historial->kilometraje;
                $calcular_promedios[$historial->year][$historial->month]['cantidad'] +=  1;
            }
            return response()->json($calcular_promedios);
        } else {
            return response()->json([]);
        }
    })->name('show-historial-promedios');

    Route::get('calcular-promedio', function () {
        $total_kms = 0;
        $cantidad = 0;
        $promedio = 0;
        $porcentaje = 0;

        $huc = new HistorialUnidadController;

        $historiales = HistorialUnidad::where('unidad_id', request()->id)
            ->where('created_at', '>=', HistorialUnidad::getValidacionMeses())
            ->orderBy('created_at', 'desc')
            ->orderBy('kilometraje', 'desc')
            ->get();

        $ultima = $historiales->first();
        $total_kms = $historiales->sum('kilometraje');
        $cantidad = $historiales->count();

        /* if ($cantidad > 0) {
          $promedio = round($total_kms / $cantidad, 2);
        } else {
          $promedio = round(0, 2);
        } */

        $promedio = $huc->getPromedioByUnidad(request()->id);

        $porcentaje = $promedio + round($promedio * 0.30);

        return response()->json([
            'total_kms' => $total_kms,
            'promedio' => $promedio,
            'porcentaje' => $porcentaje,
            'ultima' => $ultima
        ])
            ->header('Content-Type', 'application/json');
    })->name('calcular-promedio');

    Route::post('guardar-historial', function () {
        for ($i = 0; $i < count(request()->kms); $i++) {
            foreach (request()->kms[$i] as $id => $km) {
                if ($km != "") {
                    $historial = HistorialUnidad::create([
                        'unidad_id' => $id,
                        'kilometraje' => (int)$km
                    ]);
                }
            }
        }

        if ($historial->save()) {
            return response()->json($historial)->header('Content-Type', 'application/json');
        } else {
            return response()->json([]);
        }
    })->name('guardar-historial');

    Route::get('unidades/restore/{id}', 'Admin\UnidadController@restore')
        ->name('unidades.restore');

    Route::get('unidades/force-delete/{id}', 'Admin\UnidadController@forceDelete')
        ->name('unidades.forcedelete');

    Route::resource('plan-mantenimiento-preventivo', 'Admin\PlanMantenimientoPreventivoController', [
        'names' => [
            'index' => 'plan-mantenimiento-preventivo'
        ]
    ]);

    Route::get('plan-mantenimiento-preventivo/replicate/{id}', 'Admin\PlanMantenimientoPreventivoController@replicate')
        ->name('plan-mantenimiento-preventivo.replicate');

    Route::post('crear-componente', function () {
        if (request()->nombre_componente != '') {
            $componente = Componente::create([
                'nombre' => request()->nombre_componente,
                'plan_id' => request()->plan_id,
                'componente_padre' => request()->padre_id,
            ]);

            if ($componente->save()) {
                return response()->json($componente);
            } else {
                return response()->json([]);
            }
        }
    });

    Route::post('actualizar-componente', function () {
        $componente = Componente::find(request()->id);
        $componente->nombre = request()->nombre;

        if ($componente->save()) {
            return response()->json($componente);
        } else {
            return response()->json([]);
        }
    });

    Route::delete('eliminar-componente', function () {
        $componente = Componente::with('subcomponentes')
            ->where('id', request()->componente_id)
            ->orWhere('componente_padre', request()->componente_id);

        if ($componente->delete()) {
            return response()->json([]);
        } else {
            return response()->json([]);
        }
    });

    Route::post('crear-especialidad', function () {
        $especialidad = Especialidad::create([
            'nombre' => request()->nombre
        ]);

        if ($especialidad->save()) {
            return response()->json($especialidad);
        } else {
            return response()->json([]);
        }
    });

    Route::resource('tareas', 'Admin\TareaController', [
        'names' => [
            'index' => 'tareas'
        ]
    ]);

    Route::get('/tareas/create/{componente_id?}', 'Admin\TareaController@create')->name('tarea.subcomponente');

    Route::resource('vinculaciones', 'Admin\VinculacionController', [
        'names' => [
            'index' => 'vinculaciones'
        ]
    ]);

    Route::resource('categorias', 'Admin\CategoriaController', [
        'names' => [
            'index' => 'categorias'
        ]
    ]);

    Route::post('crear-categoria', function () {
        $categoria = Categoria::create([
            'nombre' => request()->nombre
        ]);

        if ($categoria->save()) {
            return response()->json($categoria);
        } else {
            return response()->json([]);
        }
    });

    Route::resource('kilometros', 'Admin\KilometroController', [
        'names' => [
            'index' => 'kilometros'
        ]
    ]);

    Route::resource('dias', 'Admin\DiaController', [
        'names' => [
            'index' => 'dias'
        ]
    ]);

    Route::get('show-mantenimiento-historial', 'Admin\MantenimientoRutinarioController@showMantenimientoHistorial')
        ->name('mantenimiento-rutinario.show-mantenimiento-historial');
    Route::get('historial-mantenimientos', 'Admin\MantenimientoRutinarioController@historialMantenimientos')
        ->name('mantenimiento-rutinario.historial-mantenimientos');
    Route::resource('mantenimiento-rutinario', 'Admin\MantenimientoRutinarioController', [
        'names' => [
            'index' => 'mantenimiento-rutinario'
        ]
    ]);

    Route::resource('recurso-actividad', 'Admin\RecursoActividadController', [
        'names' => [
            'index' => 'recurso-actividad'
        ]
    ]);

    Route::post('replicarRecursosUnidad', 'Admin\RecursoActividadController@replicarRecursosUnidad')
        ->name('recurso-actividad.replicar-recursos-unidad');
    Route::get('asociar-recursos', 'Admin\RecursoActividadController@asociarRecursos')
        ->name('recurso-actividad.asociar-recursos');

    Route::post('save-recursos', 'Admin\TareaController@saveRecursos')
        ->name('tarea.save-recursos');

    Route::get('show-mantenimiento', 'Admin\MantenimientoRutinarioController@showMaintenance')
        ->name('mantenimiento-rutinario.show-maintenance');
    Route::get('show-mantenimiento-especialidad', 'Admin\MantenimientoRutinarioController@showMaintenanceEspecialidad')
        ->name('mantenimiento-rutinario.show-maintenance-especialidad');

    Route::get('show-tareas-correctiva/{id?}', 'Admin\OrdenesTrabajoController@showTareasCorrectiva')
        ->name('ordenes-trabajo.show-tareas-correctiva');
    Route::post('actualizarTareasPreventivas', 'Admin\OrdenesTrabajoController@actualizarTareasPreventivas')
        ->name('ordenes-trabajo.actualizarTareasPreventivas');

    Route::get('planillaNecesidad', 'Admin\OrdenesTrabajoController@planillaNecesidad')
        ->name('ordenes-trabajo.planillaNecesidad');
    Route::get('generadorOTS', 'Admin\OrdenesTrabajoController@generadorOTS')
        ->name('ordenes-trabajo.generadorOTS');
    Route::post('generarOT', 'Admin\OrdenesTrabajoController@generarOT')
        ->name('ordenes-trabajo.generarOT');
    Route::post('storeOTPreventiva', 'Admin\OrdenesTrabajoController@storeOTPreventiva')
        ->name('ordenes-trabajo.storeOTPreventiva');
    Route::get('generarPDF/{id?}', 'Admin\OrdenesTrabajoController@generarPDF')
        ->name('ordenes-trabajo.generarPDF');
    Route::get('generarPDFPreventiva/{id?}', 'Admin\OrdenesTrabajoController@generarPDFPreventiva')
        ->name('ordenes-trabajo.generarPDFPreventiva');
    Route::post('reabrir/{id?}', 'Admin\OrdenesTrabajoController@reabrir')
        ->name('ordenes-trabajo.reabrir');
    Route::post('anular/{id?}', 'Admin\OrdenesTrabajoController@anular')
        ->name('ordenes-trabajo.anular');
    Route::get('mostrarPDF/{id?}', 'Admin\OrdenesTrabajoController@mostrarPDF')
        ->name('ordenes-trabajo.mostrarPDF');
    Route::get('createPreventiva', 'Admin\OrdenesTrabajoController@createPreventiva')
        ->name('ordenes-trabajo.createPreventiva');
    Route::post('storePreventiva', 'Admin\OrdenesTrabajoController@storePreventiva')
        ->name('ordenes-trabajo.storePreventiva');
    Route::get('editPreventiva/{id?}', 'Admin\OrdenesTrabajoController@editPreventiva')
        ->name('ordenes-trabajo.editPreventiva');
    Route::patch('updatePreventiva/{id?}', 'Admin\OrdenesTrabajoController@updatePreventiva')
        ->name('ordenes-trabajo.updatePreventiva');
    Route::post('create', 'Admin\OrdenesTrabajoController@create')
        ->name('ordenes-trabajo.create');

    //Route::get('asociar-recursos')
    Route::resource('ordenes-trabajo', 'Admin\OrdenesTrabajoController', [
        'names' => [
            'index' => 'ordenes-trabajo'
        ]
    ])->except(['create']);

    Route::get('verMovimientos/{id}', 'Admin\InventarioController@verMovimientos')
        ->name('inventario.verMovimientos');
    //* Inventario
    Route::get('importar', 'Admin\InventarioController@importar')
        ->name('inventario.importar');
        Route::get('planilla_abastecimiento', 'Admin\InventarioController@planillaAbastecimiento')
        ->name('inventario.planilla_abastecimiento');
    Route::get('descargarEjemplo', 'Admin\InventarioController@descargarEjemplo')
        ->name('inventario.descargarEjemplo');;
    Route::post('importarStore', 'Admin\InventarioController@importarStore')
        ->name('inventario.importarStore');
    Route::resource('inventario', 'Admin\InventarioController', [
        'names' => [
            'index' => 'inventario'
            ]
        ]);
    Route::get('exportar', 'Admin\InventarioController@exportar')
        ->name('inventario.exportar');

    //* Vales
    Route::get('vale/editar/{id}', 'Admin\ValeController@editar')
        ->name('vale.editar');
    Route::get('vale/cerrar/{id}', 'Admin\ValeController@cerrar')
        ->name('vale.cerrar');
    Route::get('vale/reabrir/{id}', 'Admin\ValeController@reabrir')
        ->name('vale.reabrir');
    Route::patch('vale/updateCerrar/{id?}', 'Admin\ValeController@updateCerrar')
        ->name('vale.updateCerrar');
    Route::get('vale/devolucion/{id?}', 'Admin\ValeController@devolucion')
        ->name('vale.devolucion');
    Route::patch('vale/updateDevolucion/{id?}', 'Admin\ValeController@updateDevolucion')
        ->name('vale.updateDevolucion');
    Route::patch('vale/cerrarCorrectiva/{id?}', 'Admin\ValeController@cerrarCorrectiva')
        ->name('vale.cerrarCorrectiva');
    Route::get('vale/generarPDF/{id?}', 'Admin\ValeController@generarPDF')
        ->name('vale.generarPDF');
    Route::resource('vale', 'Admin\ValeController', [
        'names' => [
            'index' => 'vale',
        ]
    ]);

    //Orden de Compra
    Route::get('orden-compra/generarPDF/{id}', 'Admin\OrdenCompraController@generarPDF')->name('orden-compra.generarPDF');
    Route::get('orden-compra', 'Admin\OrdenCompraController@index')->name('orden-compra.index');
    Route::get('orden-compra/create', 'Admin\OrdenCompraController@create')->name('orden-compra.create');
    Route::post('orden-compra/store', 'Admin\OrdenCompraController@store')->name('orden-compra.store');
    Route::post('orden-compra/storeRecibidas', 'Admin\OrdenCompraController@storeRecibidas')->name('orden-compra.storeRecibidas');
    Route::post('orden-compra/cerrar', 'Admin\OrdenCompraController@cerrar')->name('orden-compra.cerrar');
    Route::post('orden-compra/aprobar', 'Admin\OrdenCompraController@aprobar')->name('orden-compra.aprobar');
    Route::post('orden-compra/anular/{id?}', 'Admin\OrdenCompraController@anular')->name('orden-compra.anular');
    Route::put('orden-compra/change-status', 'Admin\OrdenCompraController@changeStatus')->name('orden-compra.change-status');
    Route::get('orden-compra/recibir', 'Admin\OrdenCompraController@recibir')->name('orden-compra.recibir');
    Route::get('orden-compra/{id}', 'Admin\OrdenCompraController@show')->name('orden-compra.show');
    Route::get('orden-compra/edit/{id}', 'Admin\OrdenCompraController@edit')->name('orden-compra.edit');
    Route::patch('orden-compra/update/{id?}', 'Admin\OrdenCompraController@update')->name('orden-compra.update');

    //Orden de Transferencia
    Route::get('orden-transferencia', 'Admin\OrdenTransferenciaController@index')->name('orden-transferencia.index');
    Route::get('orden-transferencia/create', 'Admin\OrdenTransferenciaController@create')->name('orden-transferencia.create');
    Route::post('orden-transferencia/store', 'Admin\OrdenTransferenciaController@store')->name('orden-transferencia.store');
    Route::post('orden-transferencia/storeRecibidas', 'Admin\OrdenTransferenciaController@storeRecibidas')->name('orden-transferencia.storeRecibidas');
    Route::get('orden-transferencia/recibir', 'Admin\OrdenTransferenciaController@recibir')->name('orden-transferencia.recibir');
    Route::get('orden-transferencia/{id}', 'Admin\OrdenTransferenciaController@show')->name('orden-transferencia.show');
    Route::post('orden-transferencia/cerrar', 'Admin\OrdenTransferenciaController@cerrar')->name('orden-transferencia.cerrar');
});

Route::middleware('auth')->get('logout', function () {
    Auth::logout();
    return redirect(route('login'))->withInfo('¡Has cerrado sesión exitosamente!');
})->name('logout');

Auth::routes(['verify' => true]);

Route::name('js.')->group(function () {
    Route::get('dynamic.js', 'JsController@dynamic')->name('dynamic');
});

// Get authenticated user
Route::get('users/auth', function () {
    return response()->json(['user' => Auth::check() ? Auth::user() : false]);
});
