<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Credenciais inválidas'], 401);
        }

        $user = JWTAuth::user();

        return response()->json([
            'token' => $token,
            'name' => $user->name,
            'group' => $user->group,
            'id' => $user->id,
        ]);
    }

    public function validateToken(Request $request)
    {
        try {
            // Obtém o token do header Authorization
            $token = JWTAuth::getToken();

            // Tenta decodificar o token
            $user = JWTAuth::toUser($token);

            return response()->json(['success' => true, 'status' => 'success', 'message' => 'Token válido']);
        } catch (JWTException $e) {
            // Se houver algum erro ao decodificar o token
            return response()->json(['success' => false, 'status' => 'error', 'message' => 'Token inválido']);
        }
    }

    public function logout()
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::invalidate(true);

            return response()->json(['success' => true, 'status' => 'success', 'message' => 'Logout realizado']);
        } catch (JWTException $e) {
            // Se houver algum erro ao decodificar o token
            return response()->json(['success' => false, 'status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
