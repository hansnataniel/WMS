<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

use App\Models\Setting;
use App\Models\Visitorcounter;

class Pageload
{
    public function handle(Request $request, Closure $next)
    {
        if(!$request->ajax())
        {
            $getrowtoday = Visitorcounter::where('date', '=', date('d'))->where('month', '=', date('m'))->where('year', '=', date('Y'))->first();
                
            /*
                Check page loaded today
            */
            if($getrowtoday != null)
            {
                $pagecounter = $getrowtoday;
                $pagecounter->pageload = $pagecounter->pageload + 1;
                $pagecounter->save();
            }
            else
            {
                $pagecounter = new Visitorcounter;
                $pagecounter->date = date('d');
                $pagecounter->month = date('m');
                $pagecounter->year = date('Y');
                $pagecounter->pageload = 1;
                $pagecounter->save();
            }
        }

        return $next($request);
    }
}