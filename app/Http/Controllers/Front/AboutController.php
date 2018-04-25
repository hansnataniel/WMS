<?php

/*
	Use Name Space Here
*/
namespace App\Http\Controllers\Front;

/*
	Call Model Here
*/
use App\Models\Setting;
use App\Models\Que;


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


class AboutController extends Controller
{
	/* Get the list of the resource*/
	public function index(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		return view('front.about_us.index', $data);
	}
}