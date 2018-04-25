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

use App\Models\Newslettersubscriber;


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


class NewsletterController extends Controller
{
	/* Get the list of the resource*/
	public function store(Request $request)
	{
		$inputs = $request->all();
		$rules = array(
			'email'			=> 'required|email|unique:newslettersubscribers,email',
		);

		$validator = Validator::make($inputs, $rules);
		if ($validator->passes())
		{
			$checknewsletter = Newslettersubscriber::where('email', '=', htmlspecialchars($request->input('email')))->first();

			if($checknewsletter == null)
			{
				$newslettersubscrier = new Newslettersubscriber;
				$newslettersubscrier->email = htmlspecialchars($request->input('email'));
				$newslettersubscrier->save();
			}

			return back()->withInput()->with('success-message', 'Your newsletter registration has been successfull.');
		}
		else
		{
			return back()->with('success-message', 'Email you have entered already exists');
		}
	}
}