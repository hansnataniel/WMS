<?php

namespace App\Http\Middleware;

use Closure;
use URL;
use Illuminate\Http\Request;
use Auth;
use Crypt;

use App\Models\Setting;

class Sessiontimefront
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->session()->has('front_last_activity'))
		{
			$setting = Setting::first();
			$gap = time() - $request->session()->get('front_last_activity');
			$allowed_gap = 60 * $setting->front_session_lifetime;
			if ($gap > $allowed_gap)
			{
				/*
		    		UNCOMMAND THIS SCRIPT IF YOU WANT TO DELETE ALL SESSION WHEN LOGOUT
		    	*/
				// $request->session()->flush();
			    
			    Auth::logout();
			    session_start();
			    session_destroy();
			    return redirect('/')->with('message', 'Your Session Lifetime has been expired.');
			}
		}

		return $next($request);
    }
}