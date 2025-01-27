<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {
        // Definir el orden personalizado de roles
        $roleOrder = [
            'admin' => 1,
            'empleado' => 2,
            'cliente' => 3
        ];

        $users = User::all()
            ->sortBy(function($user) use ($roleOrder) {
                // Primero ordenar por rol usando el orden personalizado
                return [$roleOrder[$user->rol] ?? 4, $user->id];
            })
            ->values(); // Reindexar la colección

        return view('users.index', compact('users'));
    }

    public function updateRole(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'rol' => 'required|in:admin,empleado,cliente'
            ]);

            $user = User::findOrFail($id);
            $user->rol = $request->rol;
            $user->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Rol actualizado correctamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el rol: ' . $e->getMessage()
            ], 500);
        }
    }
}
