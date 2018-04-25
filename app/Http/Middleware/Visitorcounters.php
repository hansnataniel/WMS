<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

use App\Models\Setting;
use App\Models\Visitorcounter;

class Visitorcounters
{
    public function handle(Request $request, Closure $next)
    {
        $getfirstdate = Visitorcounter::first();
        if($getfirstdate == null)
        {
            $newvisitor = new Visitorcounter;
            $newvisitor->date = date('d');
            $newvisitor->month = date('m');
            $newvisitor->year = date('Y');
            $newvisitor->count = 0;
            $newvisitor->pageload = 0;
            $newvisitor->save();
        }
        
        if(!$request->ajax())
        {
            $setting = Setting::first();
            $vgap = time() - $request->session()->get('lastvisitor');
            $vallowed_gap = 60 * $setting->visitor_lifetime;

            // dd($vgap . '===' . $vallowed_gap);
            // dd($request->session()->get('lastvisitor'));

            if ($vgap > $vallowed_gap)
            {
                $getvisitorcountertoday = Visitorcounter::where('date', '=', date('d'))->where('month', '=', date('m'))->where('year', '=', date('Y'))->first();
                
                /*
                    Check visitor counter today
                */

                if($getvisitorcountertoday != null)
                {
                    $visitorcounter = $getvisitorcountertoday;
                    $visitorcounter->count = $visitorcounter->count + 1;
                    $visitorcounter->save();
                }
                else
                {
                    $visitorcounter = new Visitorcounter;
                    $visitorcounter->date = date('d');
                    $visitorcounter->month = date('m');
                    $visitorcounter->year = date('Y');
                    $visitorcounter->count = 1;
                    $visitorcounter->pageload = 0;
                    $visitorcounter->save();
                }
            }
        }

        return $next($request);
    }
}