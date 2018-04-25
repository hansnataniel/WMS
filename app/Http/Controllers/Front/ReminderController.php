<?php

/*
	Use Name Space Here
*/
namespace App\Http\Controllers\Front;

/*
	Call Model Here
*/
use App\Models\Setting;
use App\Models\User;
use App\Models\Reminder;

/*
	Call Mail file & mail facades
*/
use App\Mail\Front\Mailreminder;

use Illuminate\Support\Facades\Mail;


/*
	Call Another Function you want to use
*/
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Auth;
use Validator;
use Crypt;
use URL;


/*
	------------------------------
	JANGAN LUPA UNTUK MERUBAH "MAIL" MENJADI "SMTP" DI FILE ".ENV"
	------------------------------
*/


class ReminderController extends Controller
{
	// public function __construct($token)
    // {
    	// $this->token = $token;
        // $this->middleware('guest');
    // }

	public function getRemind(Request $request)
	{
		$data['request'] = $request;
		
        return view('front.member.remind', $data);
	}

	public function postRemind(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		$inputs = $request->all();
		$rules = array(
			'email'			=> 'required',
		);

		$validator = Validator::make($inputs, $rules);
		if (!$validator->fails())
		{
			$user = User::where('email', '=', $request->input('email'))->where('is_active', '=', true)->where('is_banned', '=', false)->first();
			if ($user != null)
			{
				$getEmail = $request->input('email');
				// $this->sendResetLinkEmail($request);
				$checkrequest = Reminder::where('email', '=', $getEmail)->first();
				if($checkrequest != null)
				{
					$remind = $checkrequest;
				}
				else
				{
					$remind = new Reminder;
				}
				$remind->email = $getEmail;
				$remind->token = Crypt::encrypt($getEmail);
				$remind->save();

				$tokens = $remind->token;
				// dd($tokens);

				Mail::to($getEmail)
				    ->send(new mailreminder($tokens));
				
				return redirect('password/remind')->with("success-message", "Email for Password Reminder has been sent, <br> Please check your email to reset your password");
			}
			else
			{
				return redirect('password/remind')->withErrors("Sorry, Your email is not registered");
			}
		}
		else
		{
			return redirect('password/remind')->withInput()->withErrors($validator);
		}
	}

	public function getReset(Request $request, $token = null)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		$data['request'] = $request;

		if($token == null)
		{
		// 	// return view('front.reminder.reset', $data);
			return view('errors.404');
		}
		else
		{
			$reminder = Reminder::where('token', '=', $token)->first();
			if($reminder != null)
			{
				if(date('Y-m-d H:i:s', strtotime($reminder->updated_at . "+60 minutes")) < date('Y-m-d H:i:s'))
				{
					return redirect('password/remind')->withErrors("Sorry, the link is expired, please fill this form again to get a new forgot password email");
				}
				else
				{
					return view('front.member.reset', $data);
				}
			}
			else
			{
				return view('errors.404');
			}
			// $this->showResetForm($request, $token);
		}
	}

	public function postReset(Request $request, $token = null)
	{
		if($token == null)
		{
			return view('errors.404');
		}
		else
		{
			$setting = Setting::first();
			$data['setting'] = $setting;
			
			$inputs = $request->all();
			$rules = array(
				'email' 			=> 'required|email',
				'new_password'	 		=> 'required|confirmed|min:6',
			);

			$validator = Validator::make($inputs, $rules);
			if ($validator->passes())
			{
				$reminder = Reminder::where('token', '=', $token)->first();

				if($reminder->email == $request->input('email'))
				{
					$user = User::where('email', '=', $request->input('email'))->first();
					if($user != null)
					{
						$user->email = $request->input('email');
						$user->new_password = htmlspecialchars($request->input('new_password'));
						$user->save();

						return redirect('/')->with("success-message", "Your password has been changed, <br> Now you can login using your new password");
					}
					else
					{
						return back()->withInput()->withErrors("Sorry, the email you entered can't be found");
					}
				}
				else
				{
					return back()->withInput()->withErrors("Sorry, the email you entered can't be found");
				}
			}
			else
			{
				return back()->withInput()->withErrors($validator);
			}
		}
	}
}