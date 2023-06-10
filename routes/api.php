<?php

use App\Http\Controllers\Admin\MarcaController;
use App\Http\Controllers\Admin\HistorialUnidadController;
use App\Http\Controllers\Admin\MantenimientoRutinarioController;
use App\Http\Controllers\Admin\PersonalController;
use App\Http\Controllers\Admin\BasesOperacionController;
use App\Http\Controllers\Admin\InventarioController;
use App\Http\Controllers\Admin\OrdenesTrabajoController;
use App\Http\Controllers\Admin\OrdenCompraController;
use App\Http\Controllers\Admin\OrdenTransferenciaController;
use App\Http\Controllers\Admin\ProveedorController;
use App\Http\Controllers\Admin\RecursoActividadController;
use App\Http\Controllers\Admin\UnidadController;
use App\Http\Controllers\Admin\ValeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/marcas/{id}', [MarcaController::class, 'getModelos']);

//* Historiales
Route::get('/historiales/{id}', [HistorialUnidadController::class, 'getHistoriales'])->name('api.historiales');
Route::post('/historiales/editarHistoriales', [HistorialUnidadController::class, 'editarHistoriales'])->name('api.historiales.editar');
Route::get('/historiales/getHistorialesFecha/{unidad_id}/{fecha}', [HistorialUnidadController::class, 'getHistorialesFecha'])->name('api.historiales.get_fecha');

//* Mantenimientos Rutinarios
Route::put('/mantenimiento-rutinario/editarMantenimiento', [MantenimientoRutinarioController::class, 'editarMantenimiento'])->name('api.mantenimiento_rutinario.editar');
Route::get('/mantenimiento-rutinario/show-mantenimiento-historial', [MantenimientoRutinarioController::class, 'showMantenimientoHistorial'])->name('api.mantenimiento-rutinario.show-mantenimiento-historial');
Route::get('/personal/filtrar_especialidad', [PersonalController::class, 'filtrarEspecialidad'])->name('api.personal.filtrar_especialidad');
Route::get('/bases_operaciones/get_pieza/{base_id}', [BasesOperacionController::class, 'getPieza'])->name('api.bases_operaciones.getPieza');

//* Ordenes de trabajo
Route::get('/ordenes-trabajo/getOrdenHistoriales/{id}', [OrdenesTrabajoController::class, 'getOrdenHistoriales'])->name('api.ordenes-trabajo.getOrdenHistoriales');

//* Asociar Recursos
Route::put('/recurso-actividad/agregarRecurso', [RecursoActividadController::class, 'agregarRecurso'])->name('api.recurso_actividad.agregarRecurso');
Route::put('/recurso-actividad/agregarRecursoReplicar', [RecursoActividadController::class, 'agregarRecursoReplicar'])->name('api.recurso_actividad.agregarRecursoReplicar');
Route::put('/recurso-actividad/editarRecurso', [RecursoActividadController::class, 'editarRecurso'])->name('api.recurso_actividad.editarRecurso');
Route::put('/recurso-actividad/editarRecursoReplicar', [RecursoActividadController::class, 'editarRecursoReplicar'])->name('api.recurso_actividad.editarRecursoReplicar');
Route::put('/recurso-actividad/eliminarRecurso', [RecursoActividadController::class, 'eliminarRecurso'])->name('api.recurso_actividad.eliminarRecurso');
Route::put('/recurso-actividad/eliminarRecursoReplicar', [RecursoActividadController::class, 'eliminarRecursoReplicar'])->name('api.recurso_actividad.eliminarRecursoReplicar');

//* Inventario
Route::get('/inventario/get-inventario-por-pieza', [InventarioController::class, 'getInventarioPorPieza'])->name('api.inventario.get-inventario-por-pieza');

//* Vales
Route::get('/vale/recursosAsociados/{id}', [ValeController::class, 'recursosAsociados'])->name('api.vale.recursosAsociados');

//* Ordenes de compra
Route::get('/orden-compra/getOrdenCompraPorBase/{id}', [OrdenCompraController::class, 'getOrdenCompraPorBase'])->name('api.orden-compra.getOrdenCompraPorBase');
Route::get('/orden-compra/getOrdenCompra/{id}', [OrdenCompraController::class, 'getOrdenCompra'])->name('api.orden-compra.getOrdenCompra');

//* Ordenes de transferencia
Route::get('/orden-transferencia/getOrdenTransferenciaPorBase/{id}', [OrdenTransferenciaController::class, 'getOrdenTransferenciaPorBase'])->name('api.orden-transferencia.getOrdenTransferenciaPorBase');
Route::get('/orden-transferencia/getOrdenTransferencia/{id}', [OrdenTransferenciaController::class, 'getOrdenTransferencia'])->name('api.orden-transferencia.getOrdenTransferencia');

//*Proveedores
Route::get('/proveedores/validarCuit/{id}', [ProveedorController::class, 'validarCuit'])->name('api.proveedores.validarCuit');

//* Unidades
Route::get('/unidades/buscar-unidades-plan/{id}', [UnidadController::class, 'buscarUnidadesPlan'])->name('api.unidades.buscarUnidadesPlan');