<?php

namespace App\Http\Middleware;

use Closure;
use URL;
use Illuminate\Http\Request;
use Auth;
use Crypt;

use App\Models\Setting;

class Sessiontimeback
{
    public function handle(Request $request, Closure $next)
    {
    	if ($request->session()->has('back_last_activity'))
		{
	        $setting = Setting::first();
			$gap = time() - $request->session()->get('back_last_activity');
			$allowed_gap = 60 * $setting->back_session_lifetime;
			if ($gap > $allowed_gap)
			{
				/*
		    		UNCOMMAND THIS SCRIPT IF YOU WANT TO DELETE ALL SESSION WHEN LOGOUT
		    	*/
				// $request->session()->flush();
			    
			    Auth::logout();
			    session_start();
			    session_destroy();
			    return redirect(Crypt::decrypt($setting->admin_url))->with('message', 'Your Session Lifetime has been expired.');
			}
		}

		return $next($request);
    }
}