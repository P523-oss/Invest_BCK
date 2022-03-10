<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Illuminate\Http\Request;

class SelectTokenMiddleware
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
        if (!session()->has('currentsymbol') && !session()->has('currenttoken')){
            $chk_tkn = ['info' => 'Please select an Token symbol in order to use that menu option.'];
            return redirect(route('user.token-list'))->with($chk_tkn);
        }
        return $next($request); 
    }
}
