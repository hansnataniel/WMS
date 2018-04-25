<?php

namespace App\Http\Middleware;

use Closure;
use URL;
use Illuminate\Http\Request;

class Undonefront
{
    public function handle(Request $request, Closure $next)
    {
        if (($request->session()->get('undone-front-url') != null) && (URL::full() != $request->session()->get('undone-front-url')))
		{ 
			return redirect($request->session()->get('undone-front-url'))->with('warning-message', $request->session()->get('undone-front-message'));
		}

		return $next($request);
    }
}