<aside id="sidebar-wrapper">
    <div class="sidebar-brand">
        <a href="{{ route('admin.dashboard') }}">
            <img style="max-width: 175px;" src="{{ asset('assets/img/brand/logo/masterbus.webp') }}"
            alt="{{ env('APP_NAME') }}" >
        </a>
    </div>
    <div class="sidebar-brand sidebar-brand-sm">
        <a href="{{ route('admin.dashboard') }}">
            <img style="max-width: 25px;" src="{{ asset('assets/img/brand/logo/favicon-masterbus-150x150.png') }}"
            alt="{{ env('APP_NAME') }}" >
        </a>
    </div>
    <ul class="sidebar-menu mb-5">
        <li class="menu-header">Dashboard</li>
        <li class="{{ Request::route()->getName() == 'admin.dashboard' ? ' active' : '' }}">
            <a class="nav-link" href="{{ route('admin.dashboard') }}">
                <i class="fas fa-columns"></i> <span>Dashboard</span>
            </a>
        </li>

        @if (Auth::user()->can('manage-users'))
            <li class="menu-header">Usuarios</li>
            <li class="{{ Request::route()->getName() == 'admin.users' ? ' active' : '' }}">
                <a class="nav-link" href="{{ route('admin.users') }}">
                    <i class="fas fa-users"></i> <span>Usuarios</span>
                </a>
            </li>
        @endif

        <li class="menu-header">Ajustes</li>
        <li class="dropdown">
            <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                <i class="fas fa-bus"></i>
                <span>Inventario</span>
            </a>
            <ul class="dropdown-menu">
                <li>
                    <a href="{{ route('admin.inventario') }}" class="nav-link">Listado del inventario</a>
                </li>
                <li>
                    <a href="{{ route('admin.ordenes-trabajo.planillaNecesidad') }}" class="nav-link">Planilla de
                        Necesidad</a>
                </li>
                <li>
                    <a href="{{ route('admin.inventario.planilla_abastecimiento') }}" class="nav-link">Planilla de Abastecimiento</a>
                </li>
                <li>
                    <a href="{{ route('admin.inventario.importar') }}" class="nav-link">Importar</a>
                </li>
            </ul>
        </li>
        <li class="dropdown">
            <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                <i class="fas fa-bus"></i>
                <span>Unidades</span>
            </a>
            <ul class="dropdown-menu">
                <li>
                    <a href="{{ route('admin.unidades') }}" class="nav-link">Listado de unidades</a>
                </li>
            </ul>
        </li>
        <li class="dropdown">
            <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                <i class="fas fa-tools"></i>
                <span>Plan de mantenimiento preventivo</span>
            </a>
            <ul class="dropdown-menu">
                <li
                    class="{{ Request::route()->getName() == 'admin.plan-mantenimiento-preventivo' ? ' active' : '' }}">
                    <a href="{{ route('admin.plan-mantenimiento-preventivo') }}" class="nav-link">Ver planes</a>
                </li>
                <li class="{{ Request::route()->getName() == 'admin.vinculaciones' ? ' active' : '' }}">
                    <a href="{{ route('admin.vinculaciones') }}" class="nav-link">Vincular unidad</a>
                </li>
            </ul>
        </li>
        <li class="dropdown" style='margin-top: 10px;'>
            <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                <i class="fas fa-tools"></i>
                <span>Mantenimientos Rutinarios</span>
            </a>
            <ul class="dropdown-menu">
                <li class="">
                    <a href="{{ route('admin.mantenimiento-rutinario') }}" class="nav-link"><span>Mantenimientos
                            próximos</span></a>
                </li>
                <li class="">
                    <a href="{{ route('admin.mantenimiento-rutinario.historial-mantenimientos') }}"
                        class="nav-link"><span>Historial de Mant.</span></a>
                </li>
            </ul>
        </li>
        <li class="{{ Request::route()->getName() == 'admin.recurso-actividad' ? ' active' : '' }}">
            <a class="nav-link" href="{{ route('admin.recurso-actividad') }}">
                <i class="fas fa-tools"></i> <span>Asociar recursos</span>
            </a>
        </li>
        <li class="dropdown">
            <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                <i class="fas fa-tasks"></i>
                <span>Ordenes de trabajo</span>
            </a>
            <ul class="dropdown-menu">
                <li>
                    <a href="{{ route('admin.ordenes-trabajo') }}" class="nav-link">Listado de ordenes de trabajo</a>
                </li>
                <li>
                    <a href="{{ route('admin.ordenes-trabajo.generadorOTS') }}" class="nav-link">Generador de OTs</a>
                </li>
                <li>
                    <a href="{{ route('admin.vale') }}" class="nav-link">Consultar Vales</a>
                </li>
            </ul>
        </li>
        <li class="dropdown">
            <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                <i class="fas fa-tasks"></i>
                <span>Orden de Compra</span>
            </a>
            <ul class="dropdown-menu">
                <li>
                    <a href="{{ route('admin.orden-compra.index') }}" class="nav-link">Listado de ordenes</a>
                </li>
                <li>
                    <a href="{{ route('admin.orden-compra.create') }}" class="nav-link">Generar Orden</a>
                </li>
                <li>
                    <a href="{{ route('admin.orden-compra.recibir') }}" class="nav-link">Recibir Orden</a>
                </li>
            </ul>
        </li>
        <li class="dropdown">
            <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                <i class="fas fa-tasks"></i>
                <span>Orden de Transferencia</span>
            </a>
            <ul class="dropdown-menu">
                <li>
                    <a href="{{ route('admin.orden-transferencia.index') }}" class="nav-link">Listado de ordenes</a>
                </li>                 
                <li>
                    <a href="{{ route('admin.orden-transferencia.create') }}" class="nav-link">Generar Orden</a>
                </li>                                         
                <li>
                    <a href="{{ route('admin.orden-transferencia.recibir') }}" class="nav-link">Recibir Orden</a>
                </li>                              
            </ul>
        </li>        

        <li class="dropdown">
            <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                <i class="fas fa-book"></i>           
                <span>Control de lecturas</span>
            </a>
            <ul class="dropdown-menu">
                <li>
                    <a href="{{ route('admin.historial.create') }}" class="nav-link">Actualizar lecturas</a>
                </li>
                <li>
                    <a href="{{ route('admin.historial') }}" class="nav-link">Historial de lecturas</a>
                </li>
            </ul>
        </li>

        <li class="menu-header">Catálogos auxiliares</li>
        <li class="{{ Request::route()->getName() == 'admin.bases_operaciones' ? ' active' : '' }}">
            <a class="nav-link" href="{{ route('admin.bases_operaciones') }}">
                <i class="fas fa-warehouse"></i> <span>Bases de Operación</span>
            </a>
        </li>
        <li class="{{ Request::route()->getName() == 'admin.unidades-de-medida' ? ' active' : '' }}">
            <a class="nav-link" href="{{ route('admin.unidades-de-medida') }}">
                <i class="fas fa-ruler-combined"></i> <span>Unidades de Medida</span>
            </a>
        </li>
        <li class="{{ Request::route()->getName() == 'admin.categorias' ? ' active' : '' }}">
            <a class="nav-link" href="{{ route('admin.categorias') }}">
                <i class="fas fa-certificate"></i> <span>Categorías</span>
            </a>
        </li>
        {{-- <li class="{{ Request::route()->getName() == 'admin.piezas-de-catalogo' ? ' active' : '' }}">
                <a class="nav-link" href="{{ route('admin.piezas-de-catalogo') }}">
                    <i class="fas fa-book-open"></i> <span>Piezas de Catálogo</span>
                </a>
            </li> --}}
        <li class="dropdown">
            <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                <i class="fas fa-book-open"></i>
                <span>Piezas de Catálogo</span>
            </a>
            <ul class="dropdown-menu">
                <li>
                    <a href="{{ route('admin.piezas-de-catalogo') }}" class="nav-link">Listado</a>
                </li>
                <li>
                    <a href="{{ route('admin.piezas.importar') }}" class="nav-link">Importar</a>
                </li>
            </ul>
        </li>
        <li class="{{ Request::route()->getName() == 'admin.especialidades' ? ' active' : '' }}">
            <a class="nav-link" href="{{ route('admin.especialidades') }}">
                <i class="fas fa-star-half-alt"></i> <span>Especialidades</span>
            </a>
        </li>
        <!-- empresas -->
        <li class="{{ Request::route()->getName() == 'admin.empresas' ? ' active' : '' }}">
            <a class="nav-link" href="{{ route('admin.empresas') }}">
                <i class="fas fa-building"></i> <span>Empresas</span>
            </a>
        </li>
        <li class="{{ Request::route()->getName() == 'admin.personal' ? ' active' : '' }}">
            <a class="nav-link" href="{{ route('admin.personal') }}">
                <i class="fas fa-hard-hat"></i> <span>Personal</span>
            </a>
        </li>
        <li class="{{ Request::route()->getName() == 'admin.marcas' ? ' active' : '' }}">
            <a class="nav-link" href="{{ route('admin.marcas') }}">
                <i class="far fa-registered"></i> <span>Marcas de Unidades</span>
            </a>
        </li>
        <li class="{{ Request::route()->getName() == 'admin.modelos' ? ' active' : '' }}">
            <a class="nav-link" href="{{ route('admin.modelos') }}">
                <i class="fas fa-truck"></i> <span>Modelos de Unidades</span>
            </a>
        </li>
        <li class="{{ Request::route()->getName() == 'admin.tipos_vehiculos' ? ' active' : '' }}">
            <a class="nav-link" href="{{ route('admin.tipos_vehiculos') }}">
                <i class="fas fa-list-ul"></i> <span>Tipos de Vehículos</span>
            </a>
        </li>
        <li class="{{ Request::route()->getName() == 'admin.kilometros' ? ' active' : '' }}">
            <a class="nav-link" href="{{ route('admin.kilometros') }}">
                <i class="fas fa-tachometer-alt"></i> <span>Kilómetros</span>
            </a>
        </li>
        <li class="{{ Request::route()->getName() == 'admin.dias' ? ' active' : '' }}">
            <a class="nav-link" href="{{ route('admin.dias') }}">
                <i class="fas fa-calendar-day"></i> <span>Días</span>
            </a>
        </li>
        <li class="{{ Request::route()->getName() == 'admin.carrocerias' ? ' active' : '' }}">
            <a class="nav-link" href="{{ route('admin.carrocerias') }}">
                <i class="fas fa-car"></i> <span>Carrocerías</span>
            </a>
        </li>
        <li class="{{ Request::route()->getName() == 'admin.aires_acondicionados' ? ' active' : '' }}">
            <a class="nav-link" href="{{ route('admin.aires_acondicionados') }}">
                <i class="fas fa-wind"></i> <span>Marcas Aires Acond.</span>
            </a>
        </li>
        <li class="dropdown {{ Request::route()->getName() == 'admin.proveedor' ? ' active' : '' }}">
            <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                <i class="fas fa-book-open"></i>
                <span>Proveedor</span>
            </a>
            <ul class="dropdown-menu">
                <li>
                    <a href="{{ route('admin.proveedor') }}" class="nav-link">Listado</a>
                </li>
                <li>
                    <a href="{{ route('admin.proveedor.importar') }}" class="nav-link">Importar</a>
                </li>
            </ul>
        </li>
    </ul>
</aside>
