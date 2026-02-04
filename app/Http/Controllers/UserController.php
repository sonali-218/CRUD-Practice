<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Resources\UserResource;
use GuzzleHttp\Psr7\Query;
use Illuminate\Database\Eloquent\ModelNotFoundException;

// php artisan make:controller UserController --api --model=User
// Genera un controlador para API: Solo incluye los métodos típicos de API (index, store, show, update, destroy).
// Vincula automáticamente el controlador con el modelo User.

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // para listar todos los usuarios
    
    public function index(Request $request)
    {
        // dd($request->all());   //mostrar todos los valores que llegan en la peticion
        // dd(vars: $request->input()); //mostrar solo los valores del body de la peticion

        $users = User::query()
        ->when(
            value: $request->input('is_trashed') === 'true',
            callback: fn($query) => $query->onlyTrashed()
        )
        ->when (
            value: $request->has(key:'username'),
            callback: fn($query) =>$query->where('username', 'like', '%'. $request->input('username') . '%')
        )
        ->when (
            value: $request->has(key:'email'),
            callback: fn($query) =>$query->where('email', 'like', '%'. $request->input('email') . '%')
        )
        ->get();
        // dd(vars:$users);
        return UserResource::collection(resource: $users);
    }


    /**
     * Store a newly created resource in storage.
     */
    // para crear un nuevo usuario
    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Str::random(8); // Le colocamos una contraseña por defecto

        $user = User::create($data);
        
        return response()->json(UserResource::make($user), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'error' => 'Usuario no existe en nuestro registro.'
            ], 404);
        }

        return UserResource::make($user);
        // model binding: es el proceso mediante el cual un framework toma datos de una solicitud HTTP (como un formulario, ruta, o cuerpo de la solicitud) y los convierte automáticamente en objetos o modelos que tu código puede usar directamente.
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            // solo devolverá los campos de la petición
            $user -> update($request->validated());

            // res sirve para indicar el éxito o fracaso de la operación de forma lógica
            return response()->json([
                'res' => true,
                'message' => 'Usuario actualizado correctamente',
                'data' => $user
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'res' => false,
                'message' => 'El usuario con ID ' . $id . ' no existe'
            ], 404);
        }
        
        // $user = User::find($id);

        //if (!$user) {
            //return response()->json([
                //'message' => "El usuario con ID {$id} no fue encontrado en nuestra base de datos."
            //], 404);
        //}
        // saca primero el error de usernames repetidos, en vez de que no existe ese ID

        //$user->update($request->validated());
        //return response()->json($user, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            // buscar
            $user = User::findOrFail($id);

            // eliminarlo de la bd
            $user->delete();

            // win
            return response()->json([
                'res' => true,
                'message' => 'El usuario se eliminó correctamente'
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'res' => false,
                'message' => 'El usuario con ID ' . $id . ' no existe y no se logró eliminar'
            ], 400);
        }
    }

    public function restore($id)
    {
        // Buscamos específicamente en los registros eliminados
        $user = User::onlyTrashed()->find($id);

        if (!$user) {
            return response()->json([
                'message' => 'El usuario no existe entre los eliminados.'
            ], 404);
        }

        $user->restore();

        return response()->json([
            'message' => 'Usuario restaurado correctamente.'
        ], 200);
    }
}
