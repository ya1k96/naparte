<?php

namespace App\Http\Controllers\Admin;

use App\Empresa;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class EmpresaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $empresas = Empresa::withTrashed()->get();

        if (!empty($request['buscar'])) {
            $empresas = Empresa::where('cuit', 'like', '%' . $request['buscar'] . '%')
                ->orWhere('nombre', 'like', '%' . $request['buscar'] . '%')
                ->orWhere('id', 'like', '%' . $request['buscar'] . '%')
                ->withTrashed()
                ->get();
        }
        $buscar = $request['buscar'] ?? "";

        return view('admin.empresas.index', ['empresas' => $empresas, 'buscar' => $buscar]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'cuit' => 'required|unique:empresas',
                'nombre' => 'nullable|string|max:255',
                'img' => 'nullable|mimes:jpeg,png,jpg,svg|max:1024'
            ],
            [
                'img.mimes' => 'Only jpeg,png and bmp images are allowed',
                'img.max' => 'Sorry! Maximum allowed size for an image is 20MB',
            ]
        );
        //  Definimos si la request tiene un archivo con nombre image y lo ingresamos la data de la empresa
        if ($request->hasFile('img')) {
            $file = $request->file('img');
            $name = time() . '-' . $file->getClientOriginalName();
            $file->move('storage/img/empresa_logos/', $name);
            $validated['img'] = $name;
        }
        $empresa = Empresa::create($validated);

        if ($empresa->save()) {
            notify()->success("La empresa se agregó correctamente", "Éxito: ", "topRight");
            return redirect()->route('admin.empresas');
        } else {
            notify()->error("Hubo un error al guardar la empresa. Por favor, inténtalo nuevamente.", "Error: ", "topRight");
            return redirect($this->referer());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Empresa  $empresa
     * @return \Illuminate\Http\Response
     */
    public function show(Empresa $empresa)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Empresa  $empresa
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $empresa = Empresa::find($id);
        return view('admin.empresas.edit', ['empresa' => $empresa]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Empresa  $empresa
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Empresa $empresa)
    {
        $validated = $request->validate(
            [
                'cuit' => ['required', Rule::unique('empresas', 'cuit')->ignore($request->id)],
                'nombre' => 'nullable|string|max:255',
                'img' => 'nullable|mimes:jpeg,png,jpg,svg|max:1024'
            ],
            [
                'img.mimes' => 'Only jpeg,png and bmp images are allowed',
                'img.max' => 'Sorry! Maximum allowed size for an image is 1MB',
            ]
        );

        if ($request->hasFile('img')) {
            $file = $request->file('img');
            if (file_exists('storage/img/empresa_logos/' . $empresa->img)) {
                unlink('storage/img/empresa_logos/' . $empresa->img);
            }
            $name = time() . '-' . $file->getClientOriginalName();
            $file->move('storage/img/empresa_logos/', $name);
            $validated['img'] = $name;
        }

        $empresa = Empresa::find($request->id);
        $empresa->update($validated);

        if ($empresa->save()) {
            notify()->success("La Empresa se editó correctamente", "Éxito: ", "topRight");
            return redirect()->route('admin.empresas');
        } else {
            notify()->error("Hubo un error al editar la Empresa. Por favor, inténtalo nuevamente.", "Error: ", "topRight");
            return redirect($this->referer());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Empresa  $empresa
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $empresa = Empresa::where('id', $id)
                                ->with(['ordenes_compra' => function($q) {
                                    $q->whereIn('estado', ['abierta', 'aprobada', 'parcial']);
                                }])
                                ->first();

        if(count($empresa->ordenes_compra) > 0) {
            notify()->error("No puede anularse la Empresa porque tiene órdenes de compra Abiertas, Aprobadas o Parciales.", "Error: ", "topRight");
            return redirect()->back();
        } else {
            if ($empresa->delete()) {
                notify()->success("El empresa se anuló correctamente", "Éxito: ", "topRight");
                return redirect()->route('admin.empresas');
            } else {
                notify()->error("Hubo un error al anular la Empresa. Por favor, inténtalo nuevamente.", "Error: ", "topRight");
                return redirect()->back();
            }
        }
    }

}
