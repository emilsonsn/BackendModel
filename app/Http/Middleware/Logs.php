<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Log;

class Logs
{

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    private function validaLog($request, $response)
    {
        $urlsBloqueadas = [
            'api/log'
        ];
        $urlsBloqueadasStatus200 = [
            'api/login',
            'api/logout'
        ];

        if ($request->method() === 'GET' && ($response->getStatusCode() === 200 || $response->getStatusCode() === 400)) return true;
        if ($response->getStatusCode() === 401) return true;
        if (in_array($request->path(), $urlsBloqueadas)) return true;
        if (in_array($request->path(), $urlsBloqueadasStatus200) && $response->getStatusCode() === 200) return true;
        return false;
    }

    public function terminate($request, $response)
    {
        if ($this->validaLog($request, $response)) return true;

        $user = auth()->user();

        $request_json = '';
        if (!empty($request->all())) {
            $request_json = $request->all();

            if (!is_string($request_json)) {
                $request_json = json_encode($request_json);
            }
        }

        $response_json = '';
        if (!empty($response->content())) {
            $response_json = $response->content();

            if (!is_string($response_json)) {
                $response_json = json_encode($response_json);
            }
        }

        Log::create([
            'path' => $request->path() ?? null,
            'method' => $request->method() ?? null,
            'request' => $request->method() != 'GET' ? addslashes($request_json) : null,
            'response' => $request->method() != 'GET' ? addslashes($response_json) : null,
            'status' => $response->getStatusCode() ?? null,
            'description' => $response->original['log'] ?? null,
            'ip' => $request->getClientIps()[0] ?? null,
            'user_id' => $user->id,
        ]);

        return true;
    }
}
