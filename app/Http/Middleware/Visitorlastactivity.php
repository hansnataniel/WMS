<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Http\Exceptions\MaintenanceModeException;

class Visitorlastactivity
{
    public function handle(Request $request, Closure $next)
    {
        $request->session()->put('lastvisitor', time());

        return $next($request);
    }
}