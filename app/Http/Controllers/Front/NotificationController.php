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

use App\Models\Notification;


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


class NotificationController extends Controller
{
	/* Get the list of the resource*/
	public function store(Request $request)
	{
		$inputs = $request->all();
		$rules = array(
			'notify_email'		=> 'required',
		);

		$validator = Validator::make($inputs, $rules);
		if ($validator->passes())
		{
			$email = $request->input('notify_email');
			$product_id = $request->input('product_id');

			$notification = Notification::where('product_id', '=', $product_id)->where('email', '=', $email)->where('is_notify', '=', 1)->first();
			if($notification == null)
			{
				$notification_in = new Notification;
				$notification_in->product_id = $product_id;
				$notification_in->email = $email;
				$notification_in->is_notify = 1;
				$notification_in->save();
			}
			return back()->with('success-message', 'Thank you, we will notify you when product is back in stock');

		}
		else
		{
			return redirect('/')->with('success-message', 'Your email not valid');
		}
	}
}