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

use App\Models\Customer;
use App\Models\Rak;
use App\Models\Transaction;


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


class CustomerController extends Controller
{
	/* Get the list of the resource*/
	public function index(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/
		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->customer_r != true)
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
		
		$query = Customer::query();

		$name = htmlspecialchars($request->input('src_name'));
		if ($name != null)
		{
			$query->where('name', 'LIKE', '%' . $name . '%');
			$data['criteria']['src_name'] = $name;
		}

		$code = htmlspecialchars($request->input('src_code'));
		if ($code != null)
		{
			$query->where('code', 'LIKE', '%' . $code . '%');
			$data['criteria']['src_code'] = $code;
		}

		$phone = htmlspecialchars($request->input('src_phone'));
		if ($phone != null)
		{
			$query->where('phone', 'LIKE', '%' . $phone . '%');
			$data['criteria']['src_phone'] = $phone;
		}

		$mobile = htmlspecialchars($request->input('src_mobile'));
		if ($mobile != null)
		{
			$query->where('mobile', 'LIKE', '%' . $mobile . '%');
			$data['criteria']['src_mobile'] = $mobile;
		}

		$email = htmlspecialchars($request->input('src_email'));
		if ($email != null)
		{
			$query->where('email', 'LIKE', '%' . $email . '%');
			$data['criteria']['src_email'] = $email;
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
		$customers = $query->paginate($per_page);
		$data['customers'] = $customers;

		$request->flash();

		$request->session()->put('last_url', URL::full());

		$data['request'] = $request;

        return view('back.customer.index', $data);
	}

	/* Create a customer resource*/
	public function create(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->customer_c != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/customer')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		/*Menu Authentication*/

		$data['navModul'] = true;
		$data['helpModul'] = true;
		$data['searchModul'] = false;
		$data['alertModul'] = true;
		$data['messageModul'] = true;
		
		$data['request'] = $request;
		
		$customer = new Customer;
		$data['customer'] = $customer;

        return view('back.customer.create', $data);
	}

	public function store(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;
		
		$inputs = $request->all();
		$rules = array(
			'name'				=> 'required',
			'code'				=> 'required|unique:customers,code',
		);

		$validator = Validator::make($inputs, $rules);
		if ($validator->passes())
		{
			$customer = new Customer;
			$customer->code = htmlspecialchars($request->input('code'));
			$customer->name = htmlspecialchars($request->input('name'));
			$customer->email = htmlspecialchars($request->input('email'));
			$customer->phone = htmlspecialchars($request->input('phone'));
			$customer->mobile = htmlspecialchars($request->input('mobile'));
			$customer->fax = htmlspecialchars($request->input('fax'));
			$customer->address = htmlspecialchars($request->input('address'));
			$customer->ket = htmlspecialchars($request->input('description'));
			$customer->is_active = htmlspecialchars($request->input('is_active'));

			$customer->create_id = Auth::user()->id;
			$customer->update_id = Auth::user()->id;
			
			$customer->save();

			$admingroup = Admingroup::find(Auth::user()->admingroup_id);
			if ($admingroup->customer_r != true)
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('success-message', "Customer <strong>$customer->name</strong> has been created");
			}
			return redirect(Crypt::decrypt($setting->admin_url) . '/customer')->with('success-message', "Customer <strong>$customer->name</strong> has been created");
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/customer/create')->withInput()->withErrors($validator);
		}
	}

	/* Show a resource*/
	public function show(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->customer_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/customer')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		/*Menu Authentication*/

		$data['navModul'] = true;
		$data['helpModul'] = true;
		$data['searchModul'] = false;
		$data['alertModul'] = true;
		$data['messageModul'] = true;
		
		
		$customer = Customer::find($id);
		if ($customer != null)
		{
			$data['customer'] = $customer;
			$data['request'] = $request;
			
	        return view('back.customer.view', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/customer')->with('error-message', 'Can not find customer with ID ' . $id);
		}
	}

	/* Edit a resource*/
	public function edit(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/
		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->customer_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}
		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->customer_u != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/customer')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		/*Menu Authentication*/

		$data['navModul'] = true;
		$data['helpModul'] = true;
		$data['searchModul'] = false;
		$data['alertModul'] = true;
		$data['messageModul'] = true;
		
		
		$customer = Customer::find($id);
		
		if ($customer != null)
		{
			$data['customer'] = $customer;

			$data['request'] = $request;

	        return view('back.customer.edit', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/customer')->with('error-message', 'Can not find image with ID ' . $id);
		}
	}

	public function update(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;
		
		$inputs = $request->all();
		$rules = array(
			'name'				=> 'required',
			'code'				=> 'required|unique:customers,code,' . $id,
		);

		$validator = Validator::make($inputs, $rules);
		if ($validator->passes())
		{
			$customer = Customer::find($id);
			if ($customer != null)
			{
				$customer->code = htmlspecialchars($request->input('code'));
				$customer->name = htmlspecialchars($request->input('name'));
				$customer->email = htmlspecialchars($request->input('email'));
				$customer->phone = htmlspecialchars($request->input('phone'));
				$customer->mobile = htmlspecialchars($request->input('mobile'));
				$customer->fax = htmlspecialchars($request->input('fax'));
				$customer->address = htmlspecialchars($request->input('address'));
				$customer->ket = htmlspecialchars($request->input('description'));
				$customer->is_active = htmlspecialchars($request->input('is_active'));

				$customer->update_id = Auth::user()->id;
				$customer->save();

				return redirect(Crypt::decrypt($setting->admin_url) . '/customer')->with('success-message', "Customer <strong>$customer->name</strong> has been updated");
			}
			else
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/customer')->with('error-message', 'Can not find image with ID ' . $id);
			}
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/customer/' . $id . '/edit')->withInput()->withErrors($validator);
		}
	}


	/* Delete a resource*/
	public function destroy(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->customer_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}
		if ($admingroup->customer_d != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}
		
		$customer = Customer::find($id);
		if ($customer != null)
		{
			$transaction = Transaction::where('customer_id', '=', $id)->first();
			if($transaction != null)
			{
	 			return redirect(Crypt::decrypt($setting->admin_url) . '/customer')->with('error-message', "Can't delete Customer <strong>$customer->name</strong>");
			}
			
			$customer->delete();

 			return redirect(Crypt::decrypt($setting->admin_url) . '/customer')->with('success-message', "Customer <strong>$customer->name</strong> has been deleted");
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/customer')->with('error-message', 'Can not find customer with ID ' . $id);
		}
	}
}