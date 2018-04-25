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

use App\Models\Province;
use App\Models\Area;


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


class SigninController extends Controller
{
	/* Get the list of the resource*/
	public function index(Request $request)
	{
		if($request->session()->get('member_email') != null)
		{
			return redirect('/');
		}

		$provinces = Province::where('is_active', '=', 1)->orderBy('name', 'asc')->get();
		$province_options[''] = 'Choose Province';
		if(count($provinces) != 0)
		{
			foreach ($provinces as $province) 
			{
				$province_options["$province->id"] = "$province->name";
			}
		}
		$data['province_options'] = $province_options;
		
		$areas = Area::where('is_active', '=', 1)->orderBy('name', 'asc')->get();
		$area_options[''] = 'Choose City';
		if(count($areas) != 0)
		{
			foreach ($areas as $area) 
			{
				$area_options[$area->id] = $area->name;
			}
		}
		$data['area_options'] = $area_options;

		return view('front.member.sign_in', $data);
	}

	public function postLogin(Request $request)
	{
		$inputs = $request->all();
		$rules = array(
			'email_sign_in'		=> 'required|email',
			'password' 			=> 'required|min:6',
		);

		$validator = Validator::make($inputs, $rules);
		if ($validator->passes())
		{
			$email = $request->input('email_sign_in');
			$password = $request->input('password');
			$remember = $request->input('remember', 0);
			if ($remember == 1)
			{
				$remember = true;
			}
			else
			{
				$remember = false;
			}

			if (Auth::attempt(array('email' => $email, 'password' => $password, 'is_active' => true, 'is_banned' => false), $remember))
			{
				session_start();
				$request->session()->put('member_email', $email);
				return redirect('checkout/step1');
			}
			else
			{
				return redirect('sign-in')->withInput()->with('success-message', 'Invalid username/password');
			}
		}
		else
		{
			return redirect('sign-in')->withInput()->withErrors($validator);
		}
	}

	public function postSignup(Request $request)
	{
		$inputs = $request->all();
		$rules = array(
			'email_sign_up'		=> 'required|email|unique:users,email',
			'password' 			=> 'confirmed|min:6',
			'name'		 		=> 'required|regex:/^[A-z ]+$/',
			'address'			=> 'required',
			'city' 				=> 'required',
		);

		$validator = Validator::make($inputs, $rules);
		if ($validator->passes())
		{
			$member = new User;
			$member->area_id = htmlspecialchars($request->input('city'));
			$member->usergroup_id = 0;
			$member->name = htmlspecialchars($request->input('name'));
			$member->phone = htmlspecialchars($request->input('phone'));
			$member->birthday = htmlspecialchars($request->input('date_of_birth'));
			$member->address = htmlspecialchars($request->input('address'));
			// $member->province = htmlspecialchars($request->input('province'));
			$member->zip_code = htmlspecialchars($request->input('zip_code'));
			$member->email = htmlspecialchars($request->input('email_sign_up'));
			$member->new_password = $request->input('password');
			$member->is_admin = false;
			$member->is_member = true;
			$member->is_active = false;
			$member->save();

			if($request->input('newsletter') == true)
			{
				$newsletter_email = htmlspecialchars($request->input('newsletter'));
				$cek_newsletter = Newsletter::where('email', '=', $newsletter_email)->first();
				if($cek_newsletter == null)
				{
					$newsletter = new Newsletter;
					$newsletter->email = $member->email;
					$newsletter->save();
				}
			}

			$data['user_id'] = $member->id;

			$setting = Setting::first();
			$subject = "Halo" . $member->name . ", ini adalah aktivasi akun Anda.";
			if($setting->email_sender != null)
			{
				Mail::queue('emails.to_member.activation', $data, function($message) use ($subject, $setting, $member)
				{
					$message->from($setting->email_sender, $setting->name);
					$message->to($member->email, $member->name);
					$message->subject($subject);
				});
			}

			return redirect('sign-in')->with('success-message', "Your account has been created, check your email for activation.");
		}
		else
		{
			return redirect('sign-in')->withInput()->withErrors($validator);
		}
	}

	// public function getCekemail(Request $request)
	// {
	// 	$data['user_id'] = 4;

	// 	return view('emails.to_member.activation', $data);
	// }

	public function postCheckout(Request $request)
	{
		$inputs = $request->all();
		$rules = array(
			'email'		=> 'required|email',
		);

		$validator = Validator::make($inputs, $rules);
		if ($validator->passes())
		{
			$email = $request->input('email');
			$request->session()->put('member_email', $email);

			return redirect('checkout/step1');
		}
		else
		{
			return redirect('sign-in')->withInput()->withErrors($validator);
		}
	}
}