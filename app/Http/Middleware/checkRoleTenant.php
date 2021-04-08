<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class checkRoleTenant
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
        $token = $request->bearerToken();
        if($token)
        {
            $user = \App\Models\Tenant::where('access_token', $token)->first();
            if ($user) {
                if($token == $user->access_token)
                {
                    return $next($request);
                }
                else
                {
                    return response([
                        'message' => 'Unauthenticated User'
                    ], 403);
                }
            }
            return response([
                'message' => 'Unauthenticated'
            ], 403);
        }
        else
        {
            return response([
                'message' => 'Access token missing'
            ], 403);
        }
        

    }
}
