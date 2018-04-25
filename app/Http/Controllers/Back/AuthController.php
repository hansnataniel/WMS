<?php

/*
	Use Name Space Here
*/
namespace App\Http\Controllers\Back;

/*
	Call Model Here
*/
use App\Models\Setting;
use App\Models\Que;


/*
	Use this if you want to use Request
*/
use Illuminate\Http\Request;
use Auth;
use Validator;
use Crypt;


class AuthController extends Controller
{
	/*
		LOGIN
	*/
    public function getLogin(Request $request)
    {
    	// $request->session()->flush();
	    session_start();
	    session_destroy();
	    Auth::logout();

	    $data['request'] = $request;

    	return view('back.login.index', $data);
    }

    public function postLogin(Request $request)
    {
    	$setting = Setting::first();

		// Validating Input
		$inputs = $request->all();
		$rules = array(
			// Choose one between username and email by commenting the other one
			// 'username'	=> 'required|regex:/^[-A-z0-9._]+$/',
			'email'		=> 'required|email',
			'password'	=> 'required|min:6',
		);
		$validator = Validator::make($inputs, $rules);

		if (!$validator->fails())
		{
			// Authenticating
			$email = $request->input('email');
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

			if (Auth::attempt(array('email' => $email, 'password' => $password, 'is_banned' => false, 'is_active' => true), $remember))
			{
				// return "sukses";
				session_start();
				$request->session()->put('back_last_activity', time());
				$_SESSION['KCFINDER']['disabled'] = false;

				$que = Que::where('admin_id', '=', Auth::guard('admin')->user()->id)->first();
				if($que != null)
				{
					return redirect($que->url)->with('warning-message', "Please crop this image first to continue");
				}
				else
				{
					return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard');
				}
			}
			else
			{
				return redirect(Crypt::decrypt($setting->admin_url))->withInput()->with('message', 'Invalid username/password');
			}
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url))->withInput()->withErrors($validator);
		}
    }

    /*
    	LOGOUT
    */
    public function getLogout(Request $request)
    {
    	$setting = Setting::first();

    	/*
    		UNCOMMAND THIS SCRIPT IF YOU WANT TO DELETE ALL SESSION WHEN LOGOUT
    	*/
    	// $request->session()->flush();
	    
	    Auth::logout();
	    session_start();
	    session_destroy();

		return redirect(Crypt::decrypt($setting->admin_url));
    }
}