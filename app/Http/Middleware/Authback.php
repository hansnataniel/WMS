<?php

namespace App\Http\Middleware;

use Closure;
use URL;
use Auth;
use Crypt;
use Illuminate\Http\Request;

use App\Models\Setting;

class Authback
{
    public function handle(Request $request, Closure $next)
    {
        $setting = Setting::first();
		if (Auth::guest())
		{
			if ($request->ajax())
			{
				return response('Unauthorized', 401);
			}
			else
			{
				return redirect(Crypt::decrypt($setting->admin_url));
			}
		}

		/**
		 * Uncomment this only if the front end user have login access too
		 */
		
		// if ((!Auth::guest()) AND (Auth::user()->is_admin != true))
		// {
		// 	$request->session()->flush();
		// 	Auth::logout();
		// 	session_start();
		// 	session_destroy();
		// 	return redirect(Crypt::decrypt($setting->admin_url));
		// }

		return $next($request);
    }
}