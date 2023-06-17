<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

class CheckPrivilege 
{
   
    public function handle(Request $request,Closure $next)
    {
        if (!$request->user()->is_admin) {
            throw new AuthenticationException("You are unauthorized");
        }
        
        return $next($request);
    }
}