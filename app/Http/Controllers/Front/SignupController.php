<?php

/*
	Use Name Space Here
*/
namespace App\Http\Controllers\Front;

/*
	Call Model Here
*/
use App\Models\Setting;
use App\Models\Admingroup;
use App\Models\Que;

use App\Models\Province;
use App\Models\Area;
use App\Models\User;
use App\Models\Newslettersubscriber;

/*
	Call Mail file & mail facades
*/
use App\Mail\Front\registration;

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
use Image;
use Session;
use File;


class SignupController extends Controller
{
	/* Get the list of the resource*/
	public function index(Request $request)
	{
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
				$area_options["$area->id"] = "$area->name";
			}
		}
		$data['area_options'] = $area_options;
		return view('front.member.sign_up', $data);
	}

	public function getAjaxCity(Request $request, $province_id)
	{
		$areas = Area::where('province_id', '=', $province_id)->where('is_active', '=', 1)->orderBy('name', 'asc')->get();
		$area_options[''] = 'Choose City';
		if(count($areas) != 0)
		{
			foreach ($areas as $area) 
			{
				$area_options["$area->id"] = "$area->name";
			}
		}
		$data['area_options'] = $area_options;
		return view('front.member.ajax_city', $data);
	}

	public function store(Request $request)
	{
		$inputs = $request->all();
		$rules = array(
			'email' 			=> 'required|email|unique:users,email',
			'password' 			=> 'confirmed|min:6',
			'name'		 		=> 'required|regex:/^[A-z ]+$/',
			'phone'				=> 'required',
			'address'			=> 'required',
			'city' 				=> 'required',
		);

		$validator = Validator::make($inputs, $rules);
		if ($validator->passes())
		{
			$user = new User;
			$user->area_id = htmlspecialchars($request->input('city'));
			$user->name = htmlspecialchars($request->input('name'));
			$user->phone = htmlspecialchars($request->input('phone'));
			$user->birthdate = htmlspecialchars($request->input('date_of_birth'));
			$user->address = htmlspecialchars($request->input('address'));
			// $user->province = htmlspecialchars($request->input('province'));
			$user->zip_code = htmlspecialchars($request->input('zip_code'));
			$user->email = htmlspecialchars($request->input('email'));
			$user->new_password = $request->input('password');
			$user->is_banned = false;

			$user->create_id = 0;
			$user->update_id = 0;
			$user->banned_id = 0;
			$user->unbanned_id = 0;

			$user->unbanned = date('Y-m-d H:i:s');
			$user->banned = date('Y-m-d H:i:s');
			
			$user->is_active = false;
			$user->save();

			if($request->input('newsletter') == true)
			{
				$newsletter_email = htmlspecialchars($request->input('newsletter'));
				$cek_newsletter = Newslettersubscriber::where('email', '=', $newsletter_email)->first();
				if($cek_newsletter == null)
				{
					$newslettersubscriber = new Newslettersubscriber;
					$newslettersubscriber->email = $user->email;
					$newslettersubscriber->save();
				}
			}

			$data['user_id'] = $user->id;

			$setting = Setting::first();
			$subject = "Aktivasi akun " . $setting->name;

			Mail::to($setting->receiver_email)
			    ->send(new registration($subject, $user));

			return redirect('sign-up')->with('success-message', "Your account has been created, please check your email to activate your account.");
		}
		else
		{
			return redirect('sign-up')->withInput()->withErrors($validator);
		}
	}
}