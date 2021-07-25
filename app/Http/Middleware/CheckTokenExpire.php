<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class CheckTokenExpire
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if(!Redis::exists($user->id)){
            return response()->json(['message' => 'Token expired'], 401);
        }
        Redis::setex($user->id, 3600, $user->api_token);

        return $next($request);
    }
}
