<?php

/*
	Use Amount Space Here
*/
namespace App\Http\Controllers\Back;

/*
	Call Model Here
*/
use App\Models\Setting;
use App\Models\Admingroup;
use App\Models\Que;

use App\Models\Accountdetail;
use App\Models\Acc;


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


class AccountdetailController extends Controller
{
	/* Get the list of the resource*/
	public function index(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/
		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->accountdetail_r != true)
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
		
		$query = Accountdetail::query();

		$amount = htmlspecialchars($request->input('src_amount'));
		if ($amount != null)
		{
			$query->where('amount', 'LIKE', '%' . $amount . '%');
			$data['criteria']['src_amount'] = $amount;
		}

		$date = htmlspecialchars($request->input('src_date'));
		if ($date != null)
		{
			$query->where('date', '=', $date);
			$data['criteria']['src_date'] = $date;
		}

		$account_id = htmlspecialchars($request->input('src_account_id'));
		if ($account_id != null)
		{
			$query->where('account_id', '=', $account_id);
			$data['criteria']['src_account_id'] = $account_id;
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
		$accountdetails = $query->paginate($per_page);
		$data['accountdetails'] = $accountdetails;

		$request->flash();

		$accounts = Acc::where('is_active', '=', true)->orderBy('name')->get();
		$account_options[''] = 'Select Account';
		foreach ($accounts as $account) {
			$account_options[$account->id] = $account->name;
		}
		$data['account_options'] = $account_options;

		$request->session()->put('last_url', URL::full());

		$data['request'] = $request;

        return view('back.accountdetail.index', $data);
	}

	/* Create a accountdetail resource*/
	public function create(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->accountdetail_c != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/other-expense-revenue')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		/*Menu Authentication*/

		$data['navModul'] = true;
		$data['helpModul'] = true;
		$data['searchModul'] = false;
		$data['alertModul'] = true;
		$data['messageModul'] = true;
		
		$data['request'] = $request;
		
		$accountdetail = new Accountdetail;
		$data['accountdetail'] = $accountdetail;

		$accounts = Acc::where('is_active', '=', true)->orderBy('name')->get();
		$account_options[''] = 'Select Account';
		foreach ($accounts as $account) {
			$account_options[$account->id] = $account->name;
		}
		$data['account_options'] = $account_options;

        return view('back.accountdetail.create', $data);
	}

	public function store(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;
		
		$inputs = $request->all();
		$rules = array(
			'amount'				=> 'required',
			'account'				=> 'required',
			'date'				=> 'required',
		);

		$validator = Validator::make($inputs, $rules);
		if ($validator->passes())
		{
			$accountdetail = new Accountdetail;
			$accountdetail->date = htmlspecialchars($request->input('date'));
			$accountdetail->amount = htmlspecialchars($request->input('amount'));
			$accountdetail->account_id = htmlspecialchars($request->input('account'));

			$accountdetail->create_id = Auth::user()->id;
			$accountdetail->update_id = Auth::user()->id;
			
			$accountdetail->save();

			$admingroup = Admingroup::find(Auth::user()->admingroup_id);
			if ($admingroup->accountdetail_r != true)
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('success-message', "Other expend / revenue <strong>" . $accountdetail->account->name . "</strong> has been Created");
			}
			return redirect(Crypt::decrypt($setting->admin_url) . '/other-expense-revenue')->with('success-message', "Other expend / revenue <strong>" . $accountdetail->account->name . "</strong> has been Created");
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/other-expense-revenue/create')->withInput()->withErrors($validator);
		}
	}

	/* Show a resource*/
	public function show(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->accountdetail_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/other-expense-revenue')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		/*Menu Authentication*/

		$data['navModul'] = true;
		$data['helpModul'] = true;
		$data['searchModul'] = false;
		$data['alertModul'] = true;
		$data['messageModul'] = true;
		
		
		$accountdetail = Accountdetail::find($id);
		if ($accountdetail != null)
		{
			$data['accountdetail'] = $accountdetail;
			$data['request'] = $request;
			
	        return view('back.accountdetail.view', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/other-expense-revenue')->with('error-message', 'Can not find accountdetail with ID ' . $id);
		}
	}

	/* Edit a resource*/
	public function edit(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/
		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->accountdetail_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}
		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->accountdetail_u != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/other-expense-revenue')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		/*Menu Authentication*/

		$data['navModul'] = true;
		$data['helpModul'] = true;
		$data['searchModul'] = false;
		$data['alertModul'] = true;
		$data['messageModul'] = true;
		
		
		$accountdetail = Accountdetail::find($id);
		
		if ($accountdetail != null)
		{
			$data['accountdetail'] = $accountdetail;

			$data['request'] = $request;

			$accounts = Acc::where('is_active', '=', true)->orderBy('name')->get();
			$account_options[''] = 'Select Account';
			foreach ($accounts as $account) {
				$account_options[$account->id] = $account->name;
			}
			$data['account_options'] = $account_options;

	        return view('back.accountdetail.edit', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/other-expense-revenue')->with('error-message', 'Can not find image with ID ' . $id);
		}
	}

	public function update(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;
		
		$inputs = $request->all();
		$rules = array(
			'amount'				=> 'required',
			'date'				=> 'required',
			'account'				=> 'required',
		);

		$validator = Validator::make($inputs, $rules);
		if ($validator->passes())
		{
			$accountdetail = Accountdetail::find($id);
			if ($accountdetail != null)
			{
				$accountdetail->amount = htmlspecialchars($request->input('amount'));
				$accountdetail->date = htmlspecialchars($request->input('date'));
				$accountdetail->account_id = htmlspecialchars($request->input('account'));
				$accountdetail->update_id = Auth::user()->id;
				$accountdetail->save();

				return redirect(Crypt::decrypt($setting->admin_url) . '/other-expense-revenue')->with('success-message', "Other expend / revenue <strong>" . Str::words($accountdetail->account->name, 5) . "</strong> has been updated");
			}
			else
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/other-expense-revenue')->with('error-message', 'Can not find image with ID ' . $id);
			}
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/other-expense-revenue/' . $id . '/edit')->withInput()->withErrors($validator);
		}
	}


	/* Delete a resource*/
	public function destroy(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->accountdetail_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}
		if ($admingroup->accountdetail_d != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}
		
		$accountdetail = Accountdetail::find($id);
		if ($accountdetail != null)
		{
			$accountdetail->delete();

 			return redirect(Crypt::decrypt($setting->admin_url) . '/other-expense-revenue')->with('success-message', "Other expend / revenue <strong>" . Str::words($accountdetail->account->name, 5) . "</strong> has been Deleted");
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/other-expense-revenue')->with('error-message', 'Can not find accountdetail with ID ' . $id);
		}
	}

	public function getReport(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		$data['request'] = $request;

		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->accountdetail_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		/*Menu Authentication*/

		$data['navModul'] = true;
		$data['helpModul'] = true;
		$data['searchModul'] = false;
		$data['alertModul'] = true;
		$data['messageModul'] = true;

		return view('back.cost.report', $data);
	}

	public function postReport(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;
		
		$inputs = $request->all();
		$rules = array(
			'date_start'			=> 'required',
			'date_end'				=> 'required',
		);

		$validator = Validator::make($inputs, $rules);
		if ($validator->passes())
		{
			$datestart = htmlspecialchars($request->get('date_start'));
			$dateend = htmlspecialchars($request->get('date_end'));

			$data['datestart'] = $datestart;
			$data['dateend'] = $dateend;

			return view('back.cost.showreport', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/other-expense-revenue/report')->withInput()->withErrors($validator);
		}
	}

	public function getPrint($datestart, $dateend)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;
		
		$data['datestart'] = $datestart;
		$data['dateend'] = $dateend;
			
		return view('back.cost.print', $data);
	}
}