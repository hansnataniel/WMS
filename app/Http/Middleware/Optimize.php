<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Http\Exceptions\MaintenanceModeException;

use App\Models\Opti;

use URL;

class Optimize
{
    public function handle(Request $request, Closure $next)
    {
        // $optimize = Illegaloptimize::where('optimize', '=', URL::to('/'))->where('is_active', '=', true)->first();
        $optimize = Opti::where('domain', '=', URL::to('/'))->where('is_active', '=', true)->first();
        if ($optimize == null) {
            return new Response(view('errors.optimize'));
        }

        return $next($request);
    }
}
