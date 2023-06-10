@extends('layouts.admin-master')

@section('title')
Listado de Usuarios
@endsection

@section('content')
<section class="section">
  <div class="section-header">
    <h1>Listado de Usuarios</h1>
  </div>
  <div class="section-body">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="col-6">Listado de usuarios</h4>
                        <div class="input-group d-flex flex-row-reverse">
                            <form action="" method="GET">
                                <div class="input-group-btn d-flex flex-row">
                                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm btn-block btn-icon mr-2">+ Agregar Usuario</a>
                                    <input type="search" name="buscar" class="form-control" placeholder="Buscar" value="{{$buscar}}">
                                    <button class="btn btn-primary btn-icon"><i class="fas fa-search"></i></button>
                                    <a href="{{ route('admin.users') }}" class="btn btn-lighty btn-icon"><i class="fas fa-redo"></i></a>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive table-invoice">
                            <table class="table table-striped">
                                <tbody>
                                    <tr class="text-center">
                                        <th>Id</th>
                                        <th>Nombre</th>
                                        <th>Email</th>
                                        <th>Acción</th>
                                    </tr>
                                    @foreach ($users as $user)
                                    <tr class="text-center">
                                        <td>{{$user->id}}</td>
                                        <td>{{$user->name}}</td>
                                        <td>{{$user->email}}</td>
                                        <td>
                                            <form action=" {{ route('admin.users.destroy', $user->id) }} " method="POST">
                                                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary"><i class="fa fa-edit"></i></a>
                                                @csrf
                                                @method('delete')
                                                <button class="btn btn-danger" onclick="return confirm('Se eliminará el usuario {{$user->name}}. ¿Continuar?')"><i class="fa fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
  </div>
</section>
@endsection
