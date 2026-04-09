<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     * path="/api/v1/login",
     * summary="Iniciar sesión",
     * tags={"Auth"},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"email","password"},
     * @OA\Property(property="email", type="string", format="email", example="admin@example.com"),
     * @OA\Property(property="password", type="string", example="password")
     * )
     * ),
     * @OA\Response(response=200, description="Token generado exitosamente"),
     * @OA\Response(response=422, description="Errores de validación")
     * )
     */
    public function login(Request $request)
    {
        // Validación de inputs
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Buscar user
        $user = User::where('email', $request->email)->first();

        // Verificación del user
        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }
        $user->tokens()->delete(); 
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'message' => 'Inicio de sesión exitoso',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role->name ?? 'Sin rol'
            ]
        ], 200);
    }

    /**
     * @OA\Post(
     * path="/api/v1/logout",
     * summary="Cerrar sesión",
     * tags={"Auth"},
     * security={{"sanctum":{}}},
     * @OA\Response(response=200, description="Cierre de sesión exitoso")
     * )
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada correctamente'
        ], 200);
    }
}