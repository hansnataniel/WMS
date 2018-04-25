<?php

namespace App\Http\Middleware;

use Closure;
use URL;
use Auth;
use Crypt;
use Illuminate\Http\Request;

use App\Models\Setting;

class Authfront
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::guest())
		{
			if ($request->ajax())
			{
				return response('Unauthorized', 401);
			}
			else
			{
				return redirect('/');
			}
		}

		if ((!Auth::guest()) AND (Auth::user()->is_admin == true))
		{
			$request->session()->flush();
			Auth::logout();
			session_start();
			session_destroy();
			return redirect('login');
		}

		return $next($request);
    }
}