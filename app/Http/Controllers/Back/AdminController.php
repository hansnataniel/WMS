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

use App\Models\Admin;


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


class AdminController extends Controller
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
		if ($admingroup->admin_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any priviledge to access this page.");
		}

		/*Menu Authentication*/

		$data['messageModul'] = true;
		$data['alertModul'] = true;
		$data['searchModul'] = true;
		$data['helpModul'] = true;
		$data['navModul'] = true;
		
		$query = Admin::query();

		$data['criteria'] = '';

		$name = htmlspecialchars($request->input('src_name'));
		if ($name != null)
		{
			$query->where('name', 'LIKE', '%' . $name . '%');
			$data['criteria']['src_name'] = $name;
		}

		$admingroup_id = htmlspecialchars($request->input('src_admingroup_id'));
		if ($admingroup_id != null)
		{
			$query->where('admingroup_id', '=', $admingroup_id);
			$data['criteria']['src_admingroup_id'] = $admingroup_id;
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

		$is_admin = htmlspecialchars($request->input('src_is_admin'));
		if ($is_admin != null)
		{
			$query->where('is_admin', '=', $is_admin);
			$data['criteria']['src_is_admin'] = $is_admin;
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
		$admins = $query->paginate($per_page);
		$data['admins'] = $admins;

		$admingroups = Admingroup::where('is_active', '=', true)->get();
		if (!($admingroups->isEmpty())) {
			$admingroup_options[''] = 'Select Admin Group';
			foreach ($admingroups as $admingroup) {
				$admingroup_options[$admingroup->id] = $admingroup->name;
			}
			$data['admingroup_options'] = $admingroup_options;
		} else {
			return redirect(Crypt::decrypt($setting->admin_url) . '/admingroup/create')->with('warning-message', "You don't have admingroup, please create it first.");
		}

		$request->flash();

		$request->session()->put('last_url', URL::full());

		$data['request'] = $request;

        return view('back.admins.index', $data);
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
		if ($admingroup->admin_c != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/admin')->with('error-message', "Sorry you don't have any priviledge to access this page.");
		}

		/*Menu Authentication*/

		$data['messageModul'] = true;
		$data['alertModul'] = true;
		$data['searchModul'] = false;
		$data['helpModul'] = true;
		$data['navModul'] = true;
		
		$admin = new Admin;
		$data['admin'] = $admin;

		$admingroups = Admingroup::where('is_active', '=', true)->get();
		if (!($admingroups->isEmpty())) {
			$admingroup_options[''] = 'Select Admin Group';
			foreach ($admingroups as $admingroup) {
				$admingroup_options[$admingroup->id] = $admingroup->name;
			}
			$data['admingroup_options'] = $admingroup_options;
		} else {
			return redirect(Crypt::decrypt($setting->admin_url) . '/admingroup/create')->with('warning-message', "You don't have admingroup, please create it first");
		}

		$data['scripts'] = array('js/jquery-ui.js');
        $data['styles'] = array('css/jquery-ui-back.css');

        $data['request'] = $request;

        return view('back.admins.create', $data);
	}

	public function store(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;
		
		$inputs = $request->all();
		$rules = array(
			'admingroup'			=> 'required',
			'name' 				=> 'required|regex:/^[A-z ]+$/',
			'email' 			=> 'required|email|unique:admins,email',
			'password'	 		=> 'required|confirmed|min:6',
		);

		$validator = Validator::make($inputs, $rules);
		if ($validator->passes())
		{
			$admin = new Admin;
			$admin->admingroup_id = $request->input('admingroup');
			$admin->name = htmlspecialchars($request->input('name'));
			$admin->email = htmlspecialchars($request->input('email'));
			$admin->new_password = $request->input('password');
			$admin->is_banned = false;

			if(Auth::user()->is_admin == true)
			{
				$admin->is_admin = htmlspecialchars($request->input('is_admin', 0));
			}
			else
			{
				$admin->is_admin = false;
			}
			$admin->is_active = true;

			$admin->banned_id = 0;
			$admin->unbanned_id = 0;

			$admin->banned = date('Y-m-d H:i:s');
			$admin->unbanned = date('Y-m-d H:i:s');

			$admin->create_id = Auth::user()->id;
			$admin->update_id = Auth::user()->id;
			
			$admin->save();

			return redirect(Crypt::decrypt($setting->admin_url) . '/admin')->with('success-message', "User <strong>$admin->name</strong> has been Created.");
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/admin/create')->withInput()->withErrors($validator);
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
		if ($admingroup->admin_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/admin')->with('error-message', "Sorry you don't have any priviledge to access this page.");
		}

		/*Menu Authentication*/

		$data['messageModul'] = true;
		$data['alertModul'] = true;
		$data['searchModul'] = false;
		$data['helpModul'] = true;
		$data['navModul'] = true;
		
		$admin = Admin::find($id);
		if ($admin != null)
		{
			$data['request'] = $request;

			$data['admin'] = $admin;
	        return view('back.admins.view', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/admin')->with('error-message', 'Can not find any admin with ID ' . $id);
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
		if ($admingroup->admin_u != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/admin')->with('error-message', "Sorry you don't have any priviledge to access this page.");
		}

		/*Menu Authentication*/

		$data['messageModul'] = true;
		$data['alertModul'] = true;
		$data['searchModul'] = false;
		$data['helpModul'] = true;
		$data['navModul'] = true;
		
		$admin = Admin::find($id);

		if ($admin != null)
		{
			if($admin->id == Auth::user()->id)
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/admin/edit-profile');
			}
				
			$data['request'] = $request;

			$data['admin'] = $admin;

			$admingroups = Admingroup::where('is_active', '=', true)->get();
			$admingroup_options[''] = 'Select Admin Group';
			foreach ($admingroups as $admingroup) 
			{
				$admingroup_options[$admingroup->id] = $admingroup->name;
			}
			$data['admingroup_options'] = $admingroup_options;

			$data['scripts'] = array('js/jquery-ui.js');
	        $data['styles'] = array('css/jquery-ui-back.css');

	        return view('back.admins.edit', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/admin')->with('error-message', 'Can not find any admin with ID ' . $id);
		}
	}

	public function update(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;
		
		$inputs = $request->all();
		$rules = array(
			'admingroup'			=> 'required',
			'name' 				=> 'required|regex:/^[A-z ]+$/',
			'email' 			=> 'required|email|unique:admins,email,' . $id,
			'new_password' 		=> 'nullable|confirmed|min:6',
		);

		$validator = Validator::make($inputs, $rules);
		if ($validator->passes())
		{
			$admin = Admin::find($id);
			if ($admin != null)
			{
				$admin->admingroup_id = $request->input('admingroup');
				$admin->name = htmlspecialchars($request->input('name'));
				$admin->email = htmlspecialchars($request->input('email'));
				if ($request->input('new_password') != null) {
					$admin->new_password = $request->input('new_password');
				}
				if(Auth::user()->is_admin == true)
				{
					$admin->is_admin = htmlspecialchars($request->input('is_admin', 0));
					$admin->is_active = htmlspecialchars($request->input('is_active', 0));
				}

				$admin->update_id = Auth::user()->id;
				
				$admin->save();

				if($request->session()->has('last_url'))
	            {
					return redirect($request->session()->get('last_url'))->with('success-message', "User <strong>$admin->name</strong> has been Updated.");
	            }
	            else
	            {
					return redirect(Crypt::decrypt($setting->admin_url) . '/admin')->with('success-message', "User <strong>$admin->name</strong> has been Updated.");
	            }
			}
			else
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/admin')->with('error-message', 'Can not find any admin with ID ' . $id);
			}
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/admin/' . $id . '/edit')->withInput()->withErrors($validator);
		}
	}

	/*
		EDIT PROFILE
	*/
    public function getEditProfile(Request $request)
    {
        $setting = Setting::first();
        $data['setting'] = $setting;

        $data['messageModul'] = true;
		$data['alertModul'] = true;
		$data['searchModul'] = false;
		$data['helpModul'] = true;
		$data['navModul'] = true;

        $admin = Admin::find(Auth::user()->id);
        if ($admin != null) 
        {
        	$data['request'] = $request;

            $data['admin'] = $admin;

            $data['scripts'] = array('js/jquery-ui.js');
            $data['styles'] = array('css/jquery-ui-back.css');

            return view('back.admins.editprofile', $data);
        } 
        else 
        {
            return redirect(Crypt::decrypt($setting->admin_url) . '/admin/' . $id . '/edit')->withInput()->withErrors($validator);
        }
    }

    public function postEditProfile(Request $request)
    {
        $setting = Setting::first();
        $data['setting'] = $setting;

        $id = Auth::user()->id;
        
        $inputs = $request->all();
        $rules = array(
			'name' 				=> 'required|regex:/^[A-z ]+$/',
			'email' 			=> 'required|email|unique:admins,email,' . $id,
			'new_password' 		=> 'nullable|confirmed|min:6',
        );

        $validator = Validator::make($inputs, $rules);
        if ($validator->passes())
        {
            $admin = Admin::find(Auth::user()->id);
			$admin->name = htmlspecialchars($request->input('name'));
			$admin->email = htmlspecialchars($request->input('email'));
			if ($request->input('new_password') != null) {
				$admin->new_password = htmlspecialchars($request->input('new_password'));
			}

			$admin->update_id = Auth::user()->id;
            
            $admin->save();

            return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('success-message', "Your profile has been updated.");
        }
        else
        {
            return redirect(Crypt::decrypt($setting->admin_url) . '/admin/edit-profile')->withInput()->withErrors($validator);
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
		if ($admingroup->admin_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any priviledge to access this page.");
		}
		if ($admingroup->admin_d != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any priviledge to access this page.");
		}
		
		$admin = Admin::find($id);
		if ($admin != null)
		{
			if (Auth::user()->id == $id)
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/admin')->with('error-message', 'You can not delete yourself from your own account');
			}
			if ($admin->is_admin == true)
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/admin')->with('error-message', 'You can not delete user who have status admin');
			}

			$admin_name = $admin->name;
			$admin->delete();

			if($request->session()->has('last_url'))
            {
				return redirect($request->session()->get('last_url'))->with('success-message', "User <strong>$admin->name</strong> has been Deleted.");
            }
            else
            {
				return redirect(Crypt::decrypt($setting->admin_url) . '/admin')->with('success-message', "User <strong>$admin->name</strong> has been Deleted.");
            }
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/admin')->with('error-message', 'Can not find any admin with ID ' . $id);
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
		if ($admingroup->admin_u != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/admin')->with('error-message', "Sorry you don't have any priviledge to access this page.");
		}

		$admin = Admin::find($id);

		if ($admin != null)
		{
			if (Auth::user()->id == $id)
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/admin')->with('error-message', 'You can not banned yourself from your own account');
			}
			if ($admin->is_admin == true)
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/admin')->with('error-message', 'You can not banned user who have status admin');
			}

			if($admin->is_banned == true)
			{
				$getbannedtime = $admin->banned;
				$admin->is_banned = false;

				$admin->unbanned_id = Auth::user()->id;
				$admin->unbanned = date('Y-m-d H:i:s');
				$admin->banned = $admin->banned;

				$admin->save();

				return redirect(Crypt::decrypt($setting->admin_url) . '/admin')->with('success-message', "User <strong>$admin->name</strong> has been Unbanned");
			}
			else
			{
				$getunbannedtime = $admin->unbanned;
				$admin->is_banned = true;

				$admin->banned_id = Auth::user()->id;
				$admin->banned = date('Y-m-d H:i:s');
				$admin->unbanned = $getunbannedtime;

				$admin->save();

				return redirect(Crypt::decrypt($setting->admin_url) . '/admin')->with('success-message', "User <strong>$admin->name</strong> has been Banned");
			}
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/admin')->with('error-message', 'Can not find any admin with ID ' . $id);
		}
	}
}