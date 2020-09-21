<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\User;
use App\Http\Controllers\helpers\CommonFunctions;
use Auth;
class checkAuth
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
        $header = $request->header('Authorization');
        if (Str::startsWith($header, 'Bearer ')) {
            $auth_header = Str::substr($header, 7);
            $token = explode(':',$auth_header);
            $user = User::find($token[0]);
            
            if($user && (count($token) > 1) ){
                $tokens = json_decode($user->access_tokens);
                if (($key = array_search($auth_header, $tokens)) !== false) {
                    if($user->hasVerifiedEmail()){
                        Auth::login($user);
                        return $next($request);
                    }else{
                        return CommonFunctions::sendResponse(0, "Email Not Verified");
                    }
                }
            }
        }
        return CommonFunctions::sendResponse(0, "Authenticatio Faild");
    }
}
