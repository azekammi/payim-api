<?php

namespace App\Http\Middleware;

use Closure;

use Illuminate\Support\Facades\Session;
use DB;
use Request;

class TokenCheck
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
        $response["status"] = 0;

        $token = Request::only("token");

        if($token) {
            $user = DB::table("all_users")
                ->where(["token" => $token])
                ->select("id")
                ->first();

            if($user) {
                return $next($request);
            }
        }

        return response()->json($response);
    }
}
