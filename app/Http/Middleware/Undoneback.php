<?php

namespace App\Http\Middleware;

use Closure;
use URL;
use Illuminate\Http\Request;

class Undoneback
{
    public function handle(Request $request, Closure $next)
    {
        if (($request->session()->get('undone-back-url') != null) && (URL::full() != $request->session()->get('undone-back-url')))
		{ 
			return redirect($request->session()->get('undone-back-url'))->with('warning-message', $request->session()->get('undone-back-message'));
		}

		return $next($request);
    }
}