<?php

namespace App\Http\Middleware;

use Closure;

class TokenHeaderMiddleware
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

        $recognizedToken = env('TOKEN_HEADER_API');

        // TODO : Refractor this, trasnsfer to service
        if($token==$recognizedToken)
        {
            return $next($request);
        }elseif($token==null){ // must be remove after all desktop app are updated 
            return $next($request);
        }else{
            return response() -> json(['status' => 'ERROR', 'msg' => 'Access denied, Token Header Invalid' ], 401);
        }

    }
}
