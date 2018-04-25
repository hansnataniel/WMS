<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Http\Exceptions\MaintenanceModeException;

class Appisup
{
	protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function handle(Request $request, Closure $next)
    {
        if ($this->app->isDownForMaintenance()) {
            return new Response(view('errors.maintenance'));
        }

        return $next($request);
    }
}
