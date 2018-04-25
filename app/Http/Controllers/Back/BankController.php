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

use App\Models\Bank;


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


class BankController extends Controller
{
	/* Get the list of the resource*/
	public function index(Request $request)
	{
		$setting = Setting::first();	
		$data['setting'] = $setting;

		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->bank_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		/*Menu Authentication*/

		$data['navModul'] = true;
		$data['helpModul'] = true;
		$data['searchModul'] = true;
		$data['alertModul'] = true;
		$data['messageModul'] = true;
		
		
		$query = Bank::query();

		$data['criteria'] = '';

		$name = htmlspecialchars($request->input('src_name'));
		if ($name != null)
		{
			$query->where('name', 'LIKE', '%' . $name . '%');
			$data['criteria']['src_name'] = $name;
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
			// return 'Work';
				$query->orderBy($order_by, $order_method);
			}
			$data['order_by'] = $order_by;
			$data['order_method'] = $order_method;
		}
		else
		{
			$query->orderBy('name', 'asc');
		}

		$all_records = $query->get();
		$records_count = count($all_records);
		$data['records_count'] = $records_count;

		$per_bank = 20;
		$data['per_page'] = $per_bank;
		$banks = $query->paginate($per_bank);
		$data['banks'] = $banks;

		$request->session()->put('last_url', URL::full());

		$data['request'] = $request;

		$request->flash();

        return view('back.bank.index', $data);
	}

	/* Create a new resource*/
	public function create(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->bank_c != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/bank')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		/*Menu Authentication*/

		$data['navModul'] = true;
		$data['helpModul'] = true;
		$data['searchModul'] = false;
		$data['alertModul'] = true;
		$data['messageModul'] = true;
		
		
		$bank = new Bank;
		$data['bank'] = $bank;

		$data['request'] = $request;

        return view('back.bank.create', $data);
	}

	public function store(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;
		
		$inputs = $request->all();
		$rules = array(
			'bank_name'				=> 'required',
			'account_name'		=> 'required',
			'account_number'	=> 'required',
		);

		$validator = Validator::make($inputs, $rules);
		if ($validator->passes())
		{
			$bank = new Bank;
			$bank->name = htmlspecialchars($request->input('bank_name'));
			$bank->account_name = htmlspecialchars($request->input('account_name'));
			$bank->account_number = htmlspecialchars($request->input('account_number'));
			$bank->is_active = htmlspecialchars($request->input('is_active', 0));

			$bank->create_id = Auth::user()->id;
			$bank->update_id = Auth::user()->id;

			$bank->save();

			return redirect(Crypt::decrypt($setting->admin_url) . '/bank')->with('success-message', "Bank <strong>" . Str::words($bank->name, 5) . "</strong> has been Created");
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/bank/create')->withInput()->withErrors($validator);
		}
	}

	/* Show a resource*/
	public function show(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->bank_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/bank')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		/*Menu Authentication*/

		$data['navModul'] = true;
		$data['helpModul'] = true;
		$data['searchModul'] = false;
		$data['alertModul'] = true;
		$data['messageModul'] = true;
		
		
		$bank = Bank::find($id);
		if ($bank != null)
		{
			$data['bank'] = $bank;
			$data['request'] = $request;
	        return view('back.bank.view', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/bank')->with('error-message', 'Can not find bank with ID ' . $id);
		}
	}

	/* Edit a resource*/
	public function edit(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->bank_u != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/bank')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		/*Menu Authentication*/

		$data['navModul'] = true;
		$data['helpModul'] = true;
		$data['searchModul'] = false;
		$data['alertModul'] = true;
		$data['messageModul'] = true;
		
		
		$bank = Bank::find($id);
		
		if ($bank != null)
		{
			$data['bank'] = $bank;

			$data['request'] = $request;

	        return view('back.bank.edit', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/bank')->with('error-message', 'Can not find bank with ID ' . $id);
		}
	}

	public function update(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;
		
		$inputs = $request->all();
		$rules = array(
			'bank_name'				=> 'required',
			'account_name'		=> 'required',
			'account_number'	=> 'required',
		);

		$validator = Validator::make($inputs, $rules);
		if ($validator->passes())
		{
			$bank = Bank::find($id);
			if ($bank != null)
			{
				$bank->name = htmlspecialchars($request->input('bank_name'));
				$bank->account_name = htmlspecialchars($request->input('account_name'));
				$bank->account_number = htmlspecialchars($request->input('account_number'));
				$bank->is_active = htmlspecialchars($request->input('is_active', 0));

				$bank->update_id = Auth::user()->id;
				
				$bank->save();

				return redirect(Crypt::decrypt($setting->admin_url) . '/bank')->with('success-message', "Bank <strong>" . Str::words($bank->name, 5) . "</strong> has been Updated");
			}
			else
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/bank')->with('error-message', 'Can not find bank with ID ' . $id);
			}
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/bank/' . $id . '/edit')->withInput()->withErrors($validator);
		}
	}

}