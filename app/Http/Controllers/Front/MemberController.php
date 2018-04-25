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

use App\Models\User;
use App\Models\Area;
use App\Models\Province;
use App\Models\Transaction;
use App\Models\Transactionitem;


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
use Hash;


class MemberController extends Controller
{
	/* Get the list of the resource*/
	public function getProfile(Request $request)
	{
		$user = User::find(Auth::user()->id);
		$data['user'] = $user;

		$areas = Area::where('is_active', '=', 1)->where('province_id', '=', $user->area->province_id)->orderBy('name', 'asc')->get();
		$area_options[''] = '-- Choose City --';
		if(count($areas) != 0)
		{
			foreach ($areas as $area) 
			{
				$area_options[$area->id] = $area->name;
			}
		}
		$data['area_options'] = $area_options;

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

		return view('front.member.profile', $data);
	}

	public function postProfile(Request $request)
	{
		$member = User::find(Auth::user()->id);

		$inputs = $request->all();
		$rules = array(
			'email' 			=> 'required|email|unique:users,email,' . Auth::user()->id,
			'name'		 		=> 'required|regex:/^[A-z ]+$/',
			'phone'				=> 'required',
			'address'			=> 'required',
			'city' 				=> 'required',
		);

		$validator = Validator::make($inputs, $rules);
		if ($validator->passes())
		{
			$member->area_id = htmlspecialchars($request->input('city'));
			$member->name = htmlspecialchars($request->input('name'));
			$member->phone = htmlspecialchars($request->input('phone'));
			$member->birthdate = htmlspecialchars($request->input('date_of_birth'));
			$member->address = htmlspecialchars($request->input('address'));
			// $member->province = htmlspecialchars($request->input('province'));
			$member->zip_code = htmlspecialchars($request->input('zip_code'));
			$member->email = htmlspecialchars($request->input('email'));

			$member->update_id = 0;

			$member->save();

			return redirect('member/profile')->with('success-message', "Your profile has been updated.");
		}
		else
		{
			return redirect('member/profile')->withInput()->withErrors($validator);
		}
	}

	public function getMyTransaction(Request $request)
	{
		$user = User::find(Auth::user()->id);
		$data['user'] = $user;

		$transactions = Transaction::where('user_id', '=', $user->id)->orderBy('id', 'desc')->get();
		$data['transactions'] = $transactions;

		return view('front.member.my_transaction', $data);
	}

	public function getMyTransactionDetail(Request $request, $code)
	{
		$convertnonota = str_replace('-', '/', $code);

		$transaction = Transaction::where('no_nota', '=', $convertnonota)->first();
		$data['transaction'] = $transaction;

		$transactionitems = Transactionitem::where('transaction_id', '=', $transaction->id)->get();
		$data['transactionitems'] = $transactionitems;

		return view('front.member.my_transaction_detail', $data);
	}

	public function getChangePassword(Request $request)
	{
		$user = User::find(Auth::user()->id);
		$data['user'] = $user;

		return view('front.member.change_password', $data);
	}

	public function postChangePassword(Request $request)
	{
		$user = User::find(Auth::user()->id);

		$inputs = $request->all();
		$rules = array(
			'old_password'		=> 'required',
			'new_password'		=> 'confirmed|min:6',
		);

		$validator = Validator::make($inputs, $rules);
		if ($validator->passes())
		{
			$hashedPassword = $user->password;
			if (Hash::check($request->input('old_password'), $hashedPassword))
			{
				$user->new_password = $request->input('new_password');
			}
			else
			{
				return redirect('member/change-password')->with('success-message', "Your old password invalid.");
			}

			return redirect('member/change-password')->with('success-message', "Your Password has been Updated.");
		}
		else
		{
			return redirect('member/change-password')->withInput()->withErrors($validator);
		}
	}
}