<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\User;
use App\Enum\UserGroupEnum;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $user = User::where("id", $user->id)->first();  // Recarrega o usuário do banco de dados
        } catch (JWTException $e) {
            return response()->json(['status' => false, 'data' => null, 'error' => 'Token inválido'], 401);
        }

        if (isset($user) && $user->group == UserGroupEnum::MASTER->value) {
            return $next($request);
        } else {
            return response()->json(['status' => false, 'data' => null, 'error' => 'Acesso não autorizado'], 401);
        }
    }
}
