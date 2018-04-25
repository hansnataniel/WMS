<?php

/*
	Use Name Space Here
*/
namespace App\Http\Controllers\Front;

/*
	Call Model Here
*/
	use App\Models\User;


/*
	Use this if you want to use Request
*/
use Illuminate\Http\Request;
use Auth;
use Validator;
use Crypt;
use Cart;


class AuthController extends Controller
{
	/*
		ACTIVATION
	*/
		public function getActivation(Request $request, $code)
		{
			$user_id = Crypt::decrypt($code);
			$user = User::find($user_id);
			if($user != null)
			{
				$user->is_active = 1;
				$user->save();
				return redirect('/')->with('success-message', 'Your account has been activate.');
			}
			return redirect('/')->with('success-message', "Sorry, your account can't be found");
		}

	/*
		LOGIN
	*/
    public function postLogin(Request $request)
    {
		$inputs = $request->all();
		$rules = array(
			'email'			=> 'required|email',
			'password' 		=> 'required|min:6',
		);

		$validator = Validator::make($inputs, $rules);
		if ($validator->passes())
		{
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

			if (Auth::attempt(array('email' => $email, 'password' => $password, 'is_active' => true, 'is_banned' => false), $remember))
			{
				session_start();
				$request->session()->put('last_activity', time());
				$request->session()->put('member_email', $email);
				return redirect('/')->with('success-message', 'Login Success');
			}
			else
			{
				return redirect('/')->withInput()->with('success-message', 'Invalid username/password');
			}
		}
		else
		{
			return redirect('/')->with('success-message', 'Invalid username/password');
		}
    }

    /*
    	LOGOUT
    */
    public function getLogout(Request $request)
    {
    	/*
    		UNCOMMAND THIS SCRIPT IF YOU WANT TO DELETE ALL SESSION WHEN LOGOUT
    	*/
	    
	    $request->session()->forget('member_email');
    	$request->session()->flush();
	    Cart::destroy();
	    Auth::logout();
	    session_start();
	    session_destroy();

		return redirect('/');
    }
}