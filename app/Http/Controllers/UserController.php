<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class UserController extends BaseController
{
    public function store(Request $request)
    {
        // Valida los datos de entrada (puedes personalizar las reglas de validación)
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8',
        ]);

        // Crea un nuevo usuario
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')), // Encripta la contraseña
        ]);

        return response()->json(['message' => 'User created successfully', 'user' => $user], 201);
    }

    public function index()
    {
        $users = User::all();

        return response()->json(['users' => $users], 200);
    }

    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json(['user' => $user], 200);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Valida los datos de entrada (puedes personalizar las reglas de validación)
        $request->validate([
            'name' => 'string|max:255',
            'email' => 'string|email|unique:users,email,' . $id,
            'password' => 'string|min:8',
        ]);

        // Actualiza el usuario
        $user->update($request->only(['name', 'email', 'password']));

        return response()->json(['message' => 'User updated successfully', 'user' => $user], 200);
    }

    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not Found'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully'], 200);
    }
}
