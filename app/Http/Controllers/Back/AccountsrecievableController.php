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

use App\Models\Transaction;
use App\Models\Transactiondetail;
use App\Models\Rate;
use App\Models\Bank;
use App\Models\Customer;

/*
	Call Mail file & mail facades
*/
use App\Mail\Back\Confirm;
use App\Mail\Back\Decline;

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


class AccountsrecievableController extends Controller
{
	/* Get the list of the resource*/
	public function index(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/
		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->accountsrecievable_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		$data['navModul'] = true;
		$data['helpModul'] = true;
		$data['searchModul'] = false;
		$data['alertModul'] = true;
		$data['messageModul'] = true;
		
		
		$gettransactions = Transaction::where('status', '=', 'Waiting for Payment')->get();
		$custids = [];
		foreach ($gettransactions as $gettransaction) {
			$custids[] = $gettransaction->customer_id;
		}

		$query = Customer::query()->whereIn('id', $custids);

		$data['criteria'] = '';

		$order_by = htmlspecialchars($request->input('order_by'));
		$order_method = htmlspecialchars($request->input('order_method'));
		if ($order_by != null)
		{
			if (($order_by == 'is_active') or ($order_by == 'is_admin'))
			{
				$query->orderBy($order_by, $order_method)->orderBy('name', 'desc');
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
			$query->orderBy('name', 'desc');
		}

		$all_records = $query->get();
		$records_count = count($all_records);
		$data['records_count'] = $records_count;

		$per_page = 20;
		$data['per_page'] = $per_page;
		$customers = $query->paginate($per_page);
		$data['customers'] = $customers;

		$request->session()->put('last_url', URL::full());

		$request->flash();

		$data['request'] = $request;

        return view('back.accountsrecievable.index', $data);
	}


	/* Show a resource*/
	public function show(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/
		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->accountsrecievable_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

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

			$transactions = Transaction::where('customer_id', '=', $id)->where('status', '=', 'Waiting for Payment')->get();
			$data['transactions'] = $transactions;

	        return view('back.accountsrecievable.view', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/accounts-recievable')->with('error-message', 'Can not find any accounts recievable with ID ' . $id);
		}
	}

	public function getReport(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		$data['request'] = $request;

		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->accountsrecievable_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		/*Menu Authentication*/

		$data['navModul'] = true;
		$data['helpModul'] = true;
		$data['searchModul'] = false;
		$data['alertModul'] = true;
		$data['messageModul'] = true;

		$gettransactions = Transaction::where('status', '=', 'Waiting for Payment')->get();
		$custids = [];
		foreach ($gettransactions as $gettransaction) {
			$custids[] = $gettransaction->customer_id;
		}

		$customers = Customer::whereIn('id', $custids)->get();
		$customer_options[''] = 'Select Customer';
		foreach ($customers as $customer) {
			$customer_options[$customer->id] = $customer->name;
		}
		$data['customer_options'] = $customer_options;

		return view('back.accountsrecievable.report', $data);
	}

	public function postReport(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;
		
		$inputs = $request->all();
		$rules = array(
			'customer'			=> 'required',
		);

		$validator = Validator::make($inputs, $rules);
		if ($validator->passes())
		{
			$customer = Customer::find(htmlspecialchars($request->get('customer')));

			$data['customer'] = $customer;

			return view('back.accountsrecievable.showreport', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/accounts-recievable/report')->withInput()->withErrors($validator);
		}
	}

	public function getPrint($customerid)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		$customer = Customer::find($customerid);
		
		$data['customer'] = $customer;
			
		return view('back.accountsrecievable.print', $data);
	}
}