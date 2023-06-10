<?php

namespace App\Http\Controllers;

use App\UnidadNotificacion;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $notificaciones = UnidadNotificacion::where('fecha', '<=', now())->get();

        return view('admin.dashboard.index', compact('notificaciones'));
    }
}
