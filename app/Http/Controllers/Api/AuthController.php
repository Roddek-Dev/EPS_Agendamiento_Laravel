<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // Asegúrate de que esta ruta sea correcta a tu modelo User
use Illuminate\Support\Facades\Hash; // Para hashear la contraseña en el registro

class AuthController extends Controller
{
    /**
     * Register a new user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        // Validar los datos de entrada
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users', // 'unique:users' verifica que el email no exista en la tabla 'users'
            'password' => 'required|string|min:8|confirmed', // 'confirmed' requiere un campo password_confirmation
        ]);

        // Si la validación falla, devolver errores
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Crear el nuevo usuario
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Hashear la contraseña antes de guardar
            'role' => 'user', // Asignar rol por defecto
        ]);

        // Opcional: Autenticar al usuario recién registrado y generar un token
        // Si quieres que el usuario inicie sesión automáticamente después de registrarse
        // $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Usuario registrado exitosamente',
            // 'access_token' => $token, // Descomentar si generas token aquí
            // 'token_type' => 'Bearer', // Descomentar si generas token aquí
        ], 201); // Código 201: Creado
    }

    /**
     * Handle user login and generate a Sanctum token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        // Validar las credenciales de entrada
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Intentar autenticar al usuario
        // Auth::attempt() verifica las credenciales contra la base de datos y establece la sesión si es exitoso
        if (Auth::attempt($request->only('email', 'password'))) {
            // Obtener el usuario autenticado
            $user = Auth::user();

            // Crear un token de Sanctum para este usuario
            // 'auth_token' es el nombre del token (puedes usar algo descriptivo)
            $token = $user->createToken('auth_token')->plainTextToken;

            // Devolver la respuesta JSON con el token
            return response()->json([
                'message' => 'Inicio de sesión exitoso',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user // Opcional: devolver datos del usuario
            ]);
        } else {
            // Si la autenticación falla
            return response()->json(['message' => 'Credenciales inválidas'], 401); // Código 401: No autorizado
        }
    }

    /**
     * Log the user out (revoke the token).
     * Requires 'auth:sanctum' middleware.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        // Obtener el usuario autenticado a través del middleware 'auth:sanctum'
        $user = $request->user();

        if ($user) {
            // Eliminar el token actual que se usó para esta solicitud
            $user->currentAccessToken()->delete();

            return response()->json(['message' => 'Sesión cerrada exitosamente']);
        } else {
            // Esto no debería ocurrir si la ruta está protegida por 'auth:sanctum',
            // pero se añade por seguridad.
            return response()->json(['message' => 'No autenticado'], 401);
        }
    }

    /**
     * Get the authenticated user's details.
     * Requires 'auth:sanctum' middleware.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function user(Request $request): JsonResponse
    {
        // El middleware 'auth:sanctum' ya ha autenticado al usuario
        // $request->user() devuelve el modelo del usuario autenticado
        return response()->json($request->user());
    }
}