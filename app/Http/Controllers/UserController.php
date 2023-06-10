<?php

namespace App\Http\Controllers;

use App;
use App\User;
use App\Http\Requests\{UserUpdateRequest,UserAddRequest};

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /* TODO: Comento esto porque de momento no debe haber permisos de usuarios */
    /* public function __construct()
    {
        $this->authorizeResource(User::class);
    } */

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize(User::class, 'index');
        /* if($request->ajax())
        {
            $users = new User;
            if($request->q)
            {
                $users = $users->where('name', 'like', '%'.$request->q.'%')->orWhere('email', $request->q);
            }
            $users = $users->paginate(config('stisla.perpage'))->appends(['q' => $request->q]);
            return response()->json($users);
        } */
        $users = User::get();

        if (!empty($request['buscar'])){
            $users = User::where('name', 'like', '%'.$request['buscar'].'%' )->get();
        }
        $buscar = $request['buscar'] ?? "";

        return view('admin.users.index', ['users' => $users, 'buscar' => $buscar]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //dd($request);

        $request->validate(
            [
                'name'                      => 'required',
                'email'                     => 'required|email|unique:users',
                'password'                  => 'required|confirmed',
                'password_confirmation'     => 'required',
            ],
            [
                'name.required' => 'El campo nombre es obligatorio.',
                'email.required' => 'El campo email es obligatorio.',
                'email.email' => 'El campo email debe ser una dirección de correo válida.',
                'email.unique' => 'El email ya existe en el sistema.',
                'password.required' => 'El campo contraseña es obligatorio.',
                'password.confirmed' => 'Los campos contraseña y confirmar contraseña deben coincidir.',
                'password_confirmation.required' => 'El campo confirmar contraseña es obligatorio.',
            ]
        );

        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $role = Role::find($request->role);
        
        if($user->save()){
            if($role)
            {
                $user->assignRole($role);
            }
            notify()->success("El usuario se guardó correctamente","Éxito: ","topRight");
            return redirect()->route('admin.users');
        }else{
            notify()->error("Hubo un error al guardar el usuario. Por favor, inténtalo nuevamente.","Error: ","topRight");
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::find($id);

        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     * TODO: Agregar contraseña antigua a la validación
     * El tema con esto es que cualquier admin podria editar la password del otro y no saber la contraseña antigua
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        /*
            if(!App::environment('demo'))
            {
                $user->update($request->only([
                    'name', 'email'
                ]));

                if($request->password)
                {
                    $user->update(['password' => Hash::make($request->password)]);
                }

                if($request->role && $request->user()->can('edit-users') && !$user->isme)
                {
                    $role = Role::find($request->role);
                    if($role)
                    {
                        $user->syncRoles([$role]);
                    }
                }
            }

            return response()->json($user);
        */

        //dd($request);
        // $user = User::find($request->id);
        //dd($user);

        /* TODO: Agregar contraseña antigua a la validación */
        /* El tema con esto es que cualquier admin podria editar la password del otro y no saber la contraseña antigua */
        /*
            $request->validate([
                //'password' => 'confirmed',
                //'current_password' => 'required_with:password|in:'.$password_actual,
            ],
            [
                //'password.confirmed' => 'Para cambiar la contraseña debe completar el campo contraseña actual y confirmar contraseña, y deben coincidir.',
                //'current_password.required_with' => 'Para cambiar la contraseña debe completar el campo contraseña actual y confirmar contraseña.',
                //'current_password.in' => 'La contraseña actual no coincide con la contraseña del usuario.',
            ]
            );
        */

        $user = User::find($request->id);
        $request->validate(
            [
                'name'                      => 'required',
                'email'                     => ['required', Rule::unique('users', 'email')->ignore($request->id)],
                'password'                  => 'required_with:password_confirmation|confirmed',
                'password_confirmation'     => 'required_with:password',
            ],
            [
                'name.required' => 'El campo nombre es obligatorio.',
                'email.required' => 'El campo email es obligatorio.',
                'email.email' => 'El campo email debe ser una dirección de correo válida.',
                'email.unique' => 'El email ya existe en el sistema.',
                'password.required_with' => 'Para cambiar la contraseña complete el campo contraseña y confirmar contraseña.',
                'password.confirmed' => 'Los campos contraseña y confirmar contraseña deben coincidir.',
                'password_confirmation.required_with' => 'Para cambiar la contraseña complete el campo contraseña y confirmar contraseña.',
            ]
        );

        $user->name = $request->name;
        $user->email = $request->email;

        if($request->has('password')) {
            $user->password = Hash::make($request->password);
        }

        if($user->save()){
            notify()->success("El usuario se editó correctamente","Éxito: ","topRight");
            return redirect()->route('admin.users');
        }else{
            notify()->error("Hubo un error al editar el usuario. Por favor, inténtalo nuevamente.","Error: ","topRight");
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
        /* if(!App::environment('demo') && !$user->isme)
        {
            $user->delete();
        } else
        {
            return response()->json(['message' => 'User accounts cannot be deleted in demo mode.'], 400);
        } */

        if(User::destroy($id)){
            notify()->success("El usuario se eliminó correctamente","Éxito: ","topRight");
            return redirect()->route('admin.users');
        }else{
            notify()->error("Hubo un error al eliminar el usuario. Por favor, inténtalo nuevamente.","Error: ","topRight");
            return redirect($this->referer());
        }
    }

    public function roles()
    {
        return response()->json(Role::get());
    }
}
