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

use App\Models\Acc;
use App\Models\Accountdetail;


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


class AccountController extends Controller
{
	/* Get the list of the resource*/
	public function index(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/
		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->account_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		/*Menu Authentication*/

		$data['navModul'] = true;
		$data['helpModul'] = true;
		$data['searchModul'] = true;
		$data['alertModul'] = true;
		$data['messageModul'] = true;
		

		$data['criteria'] = array();
		
		$query = Acc::query();

		$name = htmlspecialchars($request->input('src_name'));
		if ($name != null)
		{
			$query->where('name', 'LIKE', '%' . $name . '%');
			$data['criteria']['src_name'] = $name;
		}

		$type = htmlspecialchars($request->input('src_type'));
		if ($type != null)
		{
			$query->where('type', '=', $type);
			$data['criteria']['src_type'] = $type;
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
				$query->orderBy($order_by, $order_method)->orderBy('created_at', 'asc');
			}
			else
			{
			// return 'Work';
				$query->orderBy($order_by, $order_method);
			}
			$data['criteria']['order_by'] = $order_by;
			$data['criteria']['order_method'] = $order_method;
		}
		else
		{
			$query->orderBy('created_at', 'asc');
		}

		$all_records = $query->get();
		$records_count = count($all_records);
		$data['records_count'] = $records_count;

		$per_page = 20;
		$data['per_page'] = $per_page;
		$accounts = $query->paginate($per_page);
		$data['accounts'] = $accounts;

		$request->flash();

		$request->session()->put('last_url', URL::full());

		$data['request'] = $request;

        return view('back.account.index', $data);
	}

	/* Create a account resource*/
	public function create(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->account_c != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/account')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		/*Menu Authentication*/

		$data['navModul'] = true;
		$data['helpModul'] = true;
		$data['searchModul'] = false;
		$data['alertModul'] = true;
		$data['messageModul'] = true;
		
		$data['request'] = $request;
		
		$account = new Acc;
		$data['account'] = $account;

        return view('back.account.create', $data);
	}

	public function store(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;
		
		$inputs = $request->all();
		$rules = array(
			'name'				=> 'required',
			'type'				=> 'required',
		);

		$validator = Validator::make($inputs, $rules);
		if ($validator->passes())
		{
			$account = new Acc;
			$account->name = htmlspecialchars($request->input('name'));
			$account->type = htmlspecialchars($request->input('type'));
			$account->is_active = htmlspecialchars($request->input('is_active'));

			$account->create_id = Auth::user()->id;
			$account->update_id = Auth::user()->id;
			
			$account->save();

			$admingroup = Admingroup::find(Auth::user()->admingroup_id);
			if ($admingroup->account_r != true)
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('success-message', "Account <strong>$account->name</strong> has been created");
			}
			return redirect(Crypt::decrypt($setting->admin_url) . '/account')->with('success-message', "Account <strong>$account->name</strong> has been created");
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/account/create')->withInput()->withErrors($validator);
		}
	}

	/* Show a resource*/
	public function show(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->account_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/account')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		/*Menu Authentication*/

		$data['navModul'] = true;
		$data['helpModul'] = true;
		$data['searchModul'] = false;
		$data['alertModul'] = true;
		$data['messageModul'] = true;
		
		
		$account = Acc::find($id);
		if ($account != null)
		{
			$data['account'] = $account;
			$data['request'] = $request;
			
	        return view('back.account.view', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/account')->with('error-message', 'Can not find account with ID ' . $id);
		}
	}

	/* Edit a resource*/
	public function edit(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/
		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->account_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}
		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->account_u != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/account')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		/*Menu Authentication*/

		$data['navModul'] = true;
		$data['helpModul'] = true;
		$data['searchModul'] = false;
		$data['alertModul'] = true;
		$data['messageModul'] = true;
		
		
		$account = Acc::find($id);
		
		if ($account != null)
		{
			$data['account'] = $account;

			$data['request'] = $request;

	        return view('back.account.edit', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/account')->with('error-message', 'Can not find image with ID ' . $id);
		}
	}

	public function update(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;
		
		$inputs = $request->all();
		$rules = array(
			'name'				=> 'required',
			'type'				=> 'required',
		);

		$validator = Validator::make($inputs, $rules);
		if ($validator->passes())
		{
			$account = Acc::find($id);
			if ($account != null)
			{
				$account->name = htmlspecialchars($request->input('name'));
				$account->type = htmlspecialchars($request->input('type'));
				$account->is_active = htmlspecialchars($request->input('is_active', 0));
				$account->update_id = Auth::user()->id;
				$account->save();

				return redirect(Crypt::decrypt($setting->admin_url) . '/account')->with('success-message', "Account <strong>$account->name</strong> has been updated");
			}
			else
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/account')->with('error-message', 'Can not find image with ID ' . $id);
			}
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/account/' . $id . '/edit')->withInput()->withErrors($validator);
		}
	}


	/* Delete a resource*/
	public function destroy(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->account_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}
		if ($admingroup->account_d != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}
		
		$account = Acc::find($id);
		if ($account != null)
		{
			$accountdetail = Accountdetail::where('account_id', '=', $id)->first();
			if($accountdetail != null)
			{
	 			return redirect(Crypt::decrypt($setting->admin_url) . '/account')->with('error-message', "Can't delete Account <strong>$account->name</strong>");
			}
			
			$account->delete();

 			return redirect(Crypt::decrypt($setting->admin_url) . '/account')->with('success-message', "Account <strong>$account->name</strong> has been deleted");
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/account')->with('error-message', 'Can not find account with ID ' . $id);
		}
	}
}