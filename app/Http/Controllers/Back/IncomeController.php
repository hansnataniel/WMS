<?php

/*
	Use Amount Space Here
*/
namespace App\Http\Controllers\Back;

/*
	Call Model Here
*/
use App\Models\Setting;
use App\Models\Admingroup;
use App\Models\Que;

use App\Models\Accountdetail;
use App\Models\Acc;


/*
	Call Another Function you want to use
*/
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Auth;
use Validator;
use Crypt;
use URL;
use Image;
use Session;
use File;


class IncomeController extends Controller
{
	public function getReport(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		$data['request'] = $request;

		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->income_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		/*Menu Authentication*/

		$data['navModul'] = true;
		$data['helpModul'] = true;
		$data['searchModul'] = false;
		$data['alertModul'] = true;
		$data['messageModul'] = true;

		return view('back.income.report', $data);
	}

	public function postReport(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;
		
		$inputs = $request->all();
		$rules = array(
			'date_start'			=> 'required',
			'date_end'				=> 'required',
		);

		$validator = Validator::make($inputs, $rules);
		if ($validator->passes())
		{
			$datestart = htmlspecialchars($request->get('date_start'));
			$dateend = htmlspecialchars($request->get('date_end'));

			$data['datestart'] = $datestart;
			$data['dateend'] = $dateend;

			return view('back.income.showreport', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/income/report')->withInput()->withErrors($validator);
		}
	}

	public function getPrint($datestart, $dateend)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;
		
		$data['datestart'] = $datestart;
		$data['dateend'] = $dateend;
			
		return view('back.income.print', $data);
	}
}