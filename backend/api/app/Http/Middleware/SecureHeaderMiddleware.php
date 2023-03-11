<?php

namespace App\Http\Middleware;

use Firebase\JWT\JWT;
use Closure;

class SecureHeaderMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = $request -> bearerToken();

        $authSecret = getenv('AUTH_API_SECRET');

        // TODO : Refractor this, trasnsfer to service
        try {
            JWT::decode($token, $authSecret, ['HS256'] );
        }
        catch( \UnexpectedValueException $e ){
            return response() -> json(['status' => 'ERROR', 'msg' => 'Access denied' ], 401);
        }
        catch( \Firebase\JWT\ExpiredException $e ){
            return response() -> json(['status' => 'ERROR', 'msg' => 'Token Expired. Try again.' ], 401);
        }

        return $next($request);
    }
}
