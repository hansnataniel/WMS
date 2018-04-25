<?php

/*
	Use Name Space Here
*/
namespace App\Http\Controllers\Back;

/*
	Call Model Here
*/
use App\Models\Setting;
use App\Models\Admingroup;
use App\Models\Que;

use App\Models\User;
use App\Models\Area;
use App\Models\Transaction;


/*
	Call Another Function  you want to use
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


class UserController extends Controller
{
    /* 
    	GET THE LIST OF THE RESOURCE
    */
	public function index(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->user_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any priviledge to access this page.");
		}

		/*Menu Authentication*/

		$data['messageModul'] = true;
		$data['alertModul'] = true;
		$data['searchModul'] = true;
		$data['helpModul'] = true;
		$data['navModul'] = true;
		
		$query = User::query();

		$data['criteria'] = '';

		$area_id = htmlspecialchars($request->input('src_area_id'));
		if ($area_id != null)
		{
			$query->where('area_id', '=', $area_id);
			$data['criteria']['src_area_id'] = $area_id;
		}

		$name = htmlspecialchars($request->input('src_name'));
		if ($name != null)
		{
			$query->where('name', 'LIKE', '%' . $name . '%');
			$data['criteria']['src_name'] = $name;
		}

		$email = htmlspecialchars($request->input('src_email'));
		if ($email != null)
		{
			$query->where('email', 'LIKE', '%' . $email . '%');
			$data['criteria']['src_email'] = $email;
		}

		$is_banned = htmlspecialchars($request->input('src_is_banned'));
		if ($is_banned != null)
		{
			$query->where('is_banned', '=', $is_banned);
			$data['criteria']['src_is_banned'] = $is_banned;
		}

		$is_active = htmlspecialchars($request->input('src_is_active'));
		if ($is_active != null)
		{
			$query->where('is_active', '=', $is_active);
			$data['criteria']['src_is_active'] = $is_active;
		}

		$order_by = htmlspecialchars($request->input('order_by'));
		$order_method = htmlspecialchars($request->input('order_method'));
		if ($order_by != null)
		{
			if ($order_by == 'is_active')
			{
				$query->orderBy($order_by, $order_method)->orderBy('name', 'asc');
			}
			else
			{
				$query->orderBy($order_by, $order_method);
			}
			$data['criteria']['order_by'] = $order_by;
			$data['criteria']['order_method'] = $order_method;
		}
		else
		{
			$query->orderBy('name', 'asc');
		}

		$all_records = $query->get();
		$records_count = count($all_records);
		$data['records_count'] = $records_count;

		$per_page = 20;
		$data['per_page'] = $per_page;
		$users = $query->paginate($per_page);
		$data['users'] = $users;

		$areas = Area::where('is_active', '=', true)->orderBy('name', '=', 'asc')->get();
		$area_options[''] = 'Select Area';
		foreach ($areas as $area) {
			$area_options[$area->id] = $area->name;
		}
		$data['area_options'] = $area_options;

		$request->flash();

		$request->session()->put('last_url', URL::full());

		$data['request'] = $request;

        return view('back.users.index', $data);
	}

	/* 
		CREATE A NEW RESOURCE
	*/
	public function create(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;
		
		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->user_c != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/user')->with('error-message', "Sorry you don't have any priviledge to access this page.");
		}

		/*Menu Authentication*/

		$data['messageModul'] = true;
		$data['alertModul'] = true;
		$data['searchModul'] = false;
		$data['helpModul'] = true;
		$data['navModul'] = true;
		
		$user = new User;
		$data['user'] = $user;

		$data['scripts'] = array('js/jquery-ui.js');
        $data['styles'] = array('css/jquery-ui-back.css');

        $areas = Area::where('is_active', '=', true)->orderBy('name', '=', 'asc')->get();
		$area_options[''] = 'Select Area';
		foreach ($areas as $area) {
			$area_options[$area->id] = $area->name;
		}
		$data['area_options'] = $area_options;

        $data['request'] = $request;

        return view('back.users.create', $data);
	}

	public function store(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;
		
		$inputs = $request->all();
		$rules = array(
			'area' 				=> 'required',
			'phone'				=> 'numeric|nullable',
			'name' 				=> 'required|regex:/^[A-z ]+$/',
			'email' 			=> 'required|email|unique:users,email',
			'password'	 		=> 'required|confirmed|min:6',
		);

		$validator = Validator::make($inputs, $rules);
		if ($validator->passes())
		{
			$user = new User;
			$user->area_id = htmlspecialchars($request->input('area'));
			$user->name = htmlspecialchars($request->input('name'));
			$user->email = htmlspecialchars($request->input('email'));
			$user->phone = htmlspecialchars($request->input('phone'));
			$user->birthdate = htmlspecialchars($request->input('birthdate'));
			$user->address = htmlspecialchars($request->input('address'));
			$user->new_password = $request->input('password');
			$user->is_banned = false;
			$user->is_active = htmlspecialchars($request->input('is_active', 0));

			$user->banned_id = 0;
			$user->unbanned_id = 0;

			$user->banned = date('Y-m-d H:i:s');
			$user->unbanned = date('Y-m-d H:i:s');

			$user->create_id = Auth::user()->id;
			$user->update_id = Auth::user()->id;
			
			$user->save();

			return redirect(Crypt::decrypt($setting->admin_url) . '/user')->with('success-message', "User <strong>$user->name</strong> has been Created.");
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/user/create')->withInput()->withErrors($validator);
		}
	}

	/* 
		SHOW A RESOURCE
	*/
	public function show(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;
		
		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->user_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/user')->with('error-message', "Sorry you don't have any priviledge to access this page.");
		}

		/*Menu Authentication*/

		$data['messageModul'] = true;
		$data['alertModul'] = true;
		$data['searchModul'] = false;
		$data['helpModul'] = true;
		$data['navModul'] = true;
		
		$user = User::find($id);
		if ($user != null)
		{
			$data['request'] = $request;

			$data['user'] = $user;
	        return view('back.users.view', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/user')->with('error-message', 'Can not find any user with ID ' . $id);
		}
	}

	/* 
		EDIT A RESOURCE
	*/
	public function edit(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;
		
		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->user_u != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/user')->with('error-message', "Sorry you don't have any priviledge to access this page.");
		}

		/*Menu Authentication*/

		$data['messageModul'] = true;
		$data['alertModul'] = true;
		$data['searchModul'] = false;
		$data['helpModul'] = true;
		$data['navModul'] = true;
		
		$user = User::find($id);

		if ($user != null)
		{
			// if($user->id == Auth::user()->id)
			// {
				// return redirect(Crypt::decrypt($setting->admin_url) . '/user/edit-profile');
			// }
				
			$data['request'] = $request;

			$data['user'] = $user;

			$areas = Area::where('is_active', '=', true)->orderBy('name', '=', 'asc')->get();
			$area_options[''] = 'Select Area';
			foreach ($areas as $area) {
				$area_options[$area->id] = $area->name;
			}
			$data['area_options'] = $area_options;

			$data['scripts'] = array('js/jquery-ui.js');
	        $data['styles'] = array('css/jquery-ui-back.css');

	        return view('back.users.edit', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/user')->with('error-message', 'Can not find any user with ID ' . $id);
		}
	}

	public function update(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;
		
		$inputs = $request->all();
		$rules = array(
			'area' 				=> 'required',
			'phone'				=> 'numeric|nullable',
			'name' 				=> 'required|regex:/^[A-z ]+$/',
			'email' 			=> 'required|email|unique:users,email,' . $id,
			'new_password' 		=> 'nullable|confirmed|min:6',
		);

		$validator = Validator::make($inputs, $rules);
		if ($validator->passes())
		{
			$user = User::find($id);
			if ($user != null)
			{
				$user->area_id = htmlspecialchars($request->input('area'));
				$user->name = htmlspecialchars($request->input('name'));
				$user->email = htmlspecialchars($request->input('email'));
				$user->phone = htmlspecialchars($request->input('phone'));
				$user->birthdate = htmlspecialchars($request->input('birthdate'));
				$user->address = htmlspecialchars($request->input('address'));
				if ($request->input('new_password') != null) {
					$user->new_password = $request->input('new_password');
				}
				$user->is_active = htmlspecialchars($request->input('is_active', 0));

				$user->update_id = Auth::user()->id;
				
				$user->save();

				if($request->session()->has('last_url'))
	            {
					return redirect($request->session()->get('last_url'))->with('success-message', "User <strong>$user->name</strong> has been Updated.");
	            }
	            else
	            {
					return redirect(Crypt::decrypt($setting->admin_url) . '/user')->with('success-message', "User <strong>$user->name</strong> has been Updated.");
	            }
			}
			else
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/user')->with('error-message', 'Can not find any user with ID ' . $id);
			}
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/user/' . $id . '/edit')->withInput()->withErrors($validator);
		}
	}

	/* 
		DELETE A RESOURCE
	*/
	public function destroy(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->user_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any priviledge to access this page.");
		}
		if ($admingroup->user_d != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any priviledge to access this page.");
		}
		
		$user = User::find($id);
		if ($user != null)
		{
			$transaction = Transaction::where('user_id', '=', $id)->first();
			if($transaction != null)
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/user')->with('error-message', "You can'n delete this user");
			}

			$user_name = $user->name;
			$user->delete();

			if($request->session()->has('last_url'))
            {
				return redirect($request->session()->get('last_url'))->with('success-message', "User <strong>$user->name</strong> has been Deleted.");
            }
            else
            {
				return redirect(Crypt::decrypt($setting->admin_url) . '/user')->with('success-message', "User <strong>$user->name</strong> has been Deleted.");
            }
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/user')->with('error-message', 'Can not find any user with ID ' . $id);
		}
	}

	/*
		BANNED USER
	*/
	public function getBanned(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;
		
		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->user_u != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/user')->with('error-message', "Sorry you don't have any priviledge to access this page.");
		}

		$user = User::find($id);

		if ($user != null)
		{
			if (Auth::user()->id == $id)
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/user')->with('error-message', 'You can not delete yourself from your own account');
			}

			if($user->is_banned == true)
			{
				$getbannedtime = $user->banned;
				$user->is_banned = false;

				$user->unbanned_id = Auth::user()->id;
				$user->unbanned = date('Y-m-d H:i:s');
				$user->banned = $user->banned;

				$user->save();

				return redirect(Crypt::decrypt($setting->admin_url) . '/user')->with('success-message', "User <strong>$user->name</strong> has been Unbanned");
			}
			else
			{
				$getunbannedtime = $user->unbanned;
				$user->is_banned = true;

				$user->banned_id = Auth::user()->id;
				$user->banned = date('Y-m-d H:i:s');
				$user->unbanned = $getunbannedtime;

				$user->save();

				return redirect(Crypt::decrypt($setting->admin_url) . '/user')->with('success-message', "User <strong>$user->name</strong> has been Banned");
			}
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/user')->with('error-message', 'Can not find any user with ID ' . $id);
		}
	}
}