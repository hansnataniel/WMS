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

use App\Models\Paypalconfig;
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


class AdmingroupController extends Controller
{
    /* 
    	GET THE LIST OF THE RESOURCE
    */
	public function index(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*
			PAYPALCONFIG AUTHENTICATION
		*/
		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->admingroup_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any priviledge to access this page.");
		}

		/*Menu Authentication*/

		$data['messageModul'] = true;
		$data['alertModul'] = true;
		$data['searchModul'] = true;
		$data['helpModul'] = true;
		$data['navModul'] = true;
		
		$query = Admingroup::query();

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
		$admingroups = $query->paginate($per_page);
		$data['admingroups'] = $admingroups;

		$request->flash();

		$request->session()->put('last_url', URL::full());

		$data['request'] = $request;

        return view('back.admingroups.index', $data);
	}

	/* 
		CREATE A NEW RESOURCE
	*/
	public function create(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*Paypalconfig Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->admingroup_c != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/admingroup')->with('error-message', "Sorry you don't have any priviledge to access this page.");
		}

		/*Menu Authentication*/

		$data['messageModul'] = true;
		$data['alertModul'] = true;
		$data['searchModul'] = false;
		$data['helpModul'] = true;
		$data['navModul'] = true;
		
		$admingroup = new Admingroup;
		$data['admingroup'] = $admingroup;

		$data['request'] = $request;
		
        return view('back.admingroups.create', $data);
	}

	public function store(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;
		
		$inputs = $request->all();
		$rules = array(
			'name' 				=> 'required|unique:admingroups,name',
		);

		$validator = Validator::make($inputs, $rules);
		if ($validator->passes())
		{
			$admingroup = new Admingroup;
			$admingroup->name = htmlspecialchars($request->input('name'));

			$admingroup->admingroup_c = $request->input('admingroup_c', 0);
			$admingroup->admingroup_r = $request->input('admingroup_r', 0);
			$admingroup->admingroup_u = $request->input('admingroup_u', 0);
			$admingroup->admingroup_d = $request->input('admingroup_d', 0);

			$admingroup->admin_c = $request->input('admin_c', 0);
			$admingroup->admin_r = $request->input('admin_r', 0);
			$admingroup->admin_u = $request->input('admin_u', 0);
			$admingroup->admin_d = $request->input('admin_d', 0);

			$admingroup->gudang_c = $request->input('gudang_c', 0);
			$admingroup->gudang_r = $request->input('gudang_r', 0);
			$admingroup->gudang_u = $request->input('gudang_u', 0);
			$admingroup->gudang_d = $request->input('gudang_d', 0);

			$admingroup->rak_c = $request->input('rak_c', 0);
			$admingroup->rak_r = $request->input('rak_r', 0);
			$admingroup->rak_u = $request->input('rak_u', 0);
			$admingroup->rak_d = $request->input('rak_d', 0);

			$admingroup->product_c = $request->input('product_c', 0);
			$admingroup->product_r = $request->input('product_r', 0);
			$admingroup->product_u = $request->input('product_u', 0);

			$admingroup->kendaraan_c = $request->input('kendaraan_c', 0);
			$admingroup->kendaraan_r = $request->input('kendaraan_r', 0);
			$admingroup->kendaraan_u = $request->input('kendaraan_u', 0);
			$admingroup->kendaraan_d = $request->input('kendaraan_d', 0);

			$admingroup->supplier_c = $request->input('supplier_c', 0);
			$admingroup->supplier_r = $request->input('supplier_r', 0);
			$admingroup->supplier_u = $request->input('supplier_u', 0);
			$admingroup->supplier_d = $request->input('supplier_d', 0);

			$admingroup->po_c = $request->input('po_c', 0);
			$admingroup->po_r = $request->input('po_r', 0);
			$admingroup->po_u = $request->input('po_u', 0);
			$admingroup->po_d = $request->input('po_d', 0);

			$admingroup->user_c = $request->input('user_c', 0);
			$admingroup->user_r = $request->input('user_r', 0);
			$admingroup->user_u = $request->input('user_u', 0);
			$admingroup->user_d = $request->input('user_d', 0);

			$admingroup->ri_c = $request->input('ri_c', 0);
			$admingroup->ri_r = $request->input('ri_r', 0);
			$admingroup->ri_u = $request->input('ri_u', 0);
			$admingroup->ri_d = $request->input('ri_d', 0);

			$admingroup->hbt_r = $request->input('hbt_r', 0);

			$admingroup->invoice_c = $request->input('invoice_c', 0);
			$admingroup->invoice_r = $request->input('invoice_r', 0);
			$admingroup->invoice_u = $request->input('invoice_u', 0);
			$admingroup->invoice_d = $request->input('invoice_d', 0);

			$admingroup->return_c = $request->input('return_c', 0);
			$admingroup->return_r = $request->input('return_r', 0);
			$admingroup->return_u = $request->input('return_u', 0);
			$admingroup->return_d = $request->input('return_d', 0);

			$admingroup->payment_c = $request->input('payment_c', 0);
			$admingroup->payment_r = $request->input('payment_r', 0);
			$admingroup->payment_u = $request->input('payment_u', 0);
			$admingroup->payment_d = $request->input('payment_d', 0);
			
			$admingroup->setting_u = $request->input('setting_u', 0);

			$admingroup->productphoto_c = $request->input('productphoto_c', 0);
			$admingroup->productphoto_r = $request->input('productphoto_r', 0);
			$admingroup->productphoto_d = $request->input('productphoto_d', 0);

			$admingroup->inventory_c = $request->input('inventory_c', 0);
			$admingroup->inventory_r = $request->input('inventory_r', 0);

			$admingroup->bank_c = $request->input('bank_c', 0);
			$admingroup->bank_r = $request->input('bank_r', 0);
			$admingroup->bank_u = $request->input('bank_u', 0);

			$admingroup->adjustment_c = $request->input('adjustment_c', 0);
			$admingroup->adjustment_r = $request->input('adjustment_r', 0);

			$admingroup->stockcard_r = $request->input('stockcard_r', 0);

			$admingroup->customer_c = $request->input('customer_c', 0);
			$admingroup->customer_r = $request->input('customer_r', 0);
			$admingroup->customer_u = $request->input('customer_u', 0);
			$admingroup->customer_d = $request->input('customer_d', 0);

			$admingroup->transaction_c = $request->input('transaction_c', 0);
			$admingroup->transaction_r = $request->input('transaction_r', 0);
			$admingroup->transaction_u = $request->input('transaction_u', 0);
			$admingroup->transaction_d = $request->input('transaction_d', 0);

			$admingroup->tpayment_c = $request->input('tpayment_c', 0);
			$admingroup->tpayment_r = $request->input('tpayment_r', 0);
			$admingroup->tpayment_u = $request->input('tpayment_u', 0);
			$admingroup->tpayment_d = $request->input('tpayment_d', 0);

			$admingroup->treturn_c = $request->input('treturn_c', 0);
			$admingroup->treturn_r = $request->input('treturn_r', 0);
			$admingroup->treturn_u = $request->input('treturn_u', 0);
			$admingroup->treturn_d = $request->input('treturn_d', 0);

			$admingroup->accountsrecievable_r = $request->input('accountsrecievable_r', 0);

			$admingroup->account_c = $request->input('account_c', 0);
			$admingroup->account_r = $request->input('account_r', 0);
			$admingroup->account_u = $request->input('account_u', 0);
			$admingroup->account_d = $request->input('account_d', 0);

			$admingroup->accountdetail_c = $request->input('accountdetail_c', 0);
			$admingroup->accountdetail_r = $request->input('accountdetail_r', 0);
			$admingroup->accountdetail_u = $request->input('accountdetail_u', 0);
			$admingroup->accountdetail_d = $request->input('accountdetail_d', 0);

			$admingroup->income_r = $request->input('income_r', 0);


			$admingroup->is_active = htmlspecialchars($request->input('is_active', 0));

			$admingroup->create_id = Auth::user()->id;
			$admingroup->update_id = Auth::user()->id;
			
			$admingroup->save();

			return redirect(Crypt::decrypt($setting->admin_url) . '/admingroup')->with('success-message', "Admingroup <strong>$admingroup->name</strong> has been Created.");
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/admingroup/create')->withInput()->withErrors($validator);
		}
	}

	/* 
		SHOW A RESOURCE
	*/
	public function show(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;


		/*Paypalconfig Authentication*/
		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->admingroup_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/admingroup')->with('error-message', "Sorry you don't have any priviledge to access this page.");
		}

		/*Menu Authentication*/

		$data['messageModul'] = true;
		$data['alertModul'] = true;
		$data['searchModul'] = false;
		$data['helpModul'] = true;
		$data['navModul'] = true;
		
		$admingroup = Admingroup::find($id);
		if ($admingroup != null)
		{
			$data['request'] = $request;

			$data['admingroup'] = $admingroup;
	        return view('back.admingroups.view', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/admingroup')->with('error-message', 'Can not find any admingroup with ID ' . $id);
		}
	}

	/* 
		EDIT A RESOURCE
	*/
	public function edit(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*Paypalconfig Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->admingroup_u != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/admingroup')->with('error-message', "Sorry you don't have any priviledge to access this page.");
		}

		/*Menu Authentication*/

		$data['messageModul'] = true;
		$data['alertModul'] = true;
		$data['searchModul'] = false;
		$data['helpModul'] = true;
		$data['navModul'] = true;
		
		$admingroup = Admingroup::find($id);

		if ($admingroup != null)
		{
			$data['request'] = $request;

			$data['admingroup'] = $admingroup;

	        return view('back.admingroups.edit', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/admingroup')->with('error-message', 'Can not find any admingroup with ID ' . $id);
		}
	}

	public function update(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;
		
		$inputs = $request->all();
		$rules = array(
			'name' 				=> 'required|unique:admingroups,name,' . $id,
		);

		$validator = Validator::make($inputs, $rules);
		if ($validator->passes())
		{
			$admingroup = Admingroup::find($id);
			if ($admingroup != null)
			{
				$admingroup->name = htmlspecialchars($request->input('name'));

				$admingroup->admingroup_c = $request->input('admingroup_c', 0);
				$admingroup->admingroup_r = $request->input('admingroup_r', 0);
				$admingroup->admingroup_u = $request->input('admingroup_u', 0);
				$admingroup->admingroup_d = $request->input('admingroup_d', 0);

				$admingroup->admin_c = $request->input('admin_c', 0);
				$admingroup->admin_r = $request->input('admin_r', 0);
				$admingroup->admin_u = $request->input('admin_u', 0);
				$admingroup->admin_d = $request->input('admin_d', 0);

				$admingroup->gudang_c = $request->input('gudang_c', 0);
				$admingroup->gudang_r = $request->input('gudang_r', 0);
				$admingroup->gudang_u = $request->input('gudang_u', 0);
				$admingroup->gudang_d = $request->input('gudang_d', 0);

				$admingroup->rak_c = $request->input('rak_c', 0);
				$admingroup->rak_r = $request->input('rak_r', 0);
				$admingroup->rak_u = $request->input('rak_u', 0);
				$admingroup->rak_d = $request->input('rak_d', 0);

				$admingroup->product_c = $request->input('product_c', 0);
				$admingroup->product_r = $request->input('product_r', 0);
				$admingroup->product_u = $request->input('product_u', 0);

				$admingroup->kendaraan_c = $request->input('kendaraan_c', 0);
				$admingroup->kendaraan_r = $request->input('kendaraan_r', 0);
				$admingroup->kendaraan_u = $request->input('kendaraan_u', 0);
				$admingroup->kendaraan_d = $request->input('kendaraan_d', 0);

				$admingroup->supplier_c = $request->input('supplier_c', 0);
				$admingroup->supplier_r = $request->input('supplier_r', 0);
				$admingroup->supplier_u = $request->input('supplier_u', 0);
				$admingroup->supplier_d = $request->input('supplier_d', 0);

				$admingroup->po_c = $request->input('po_c', 0);
				$admingroup->po_r = $request->input('po_r', 0);
				$admingroup->po_u = $request->input('po_u', 0);
				$admingroup->po_d = $request->input('po_d', 0);

				$admingroup->user_c = $request->input('user_c', 0);
				$admingroup->user_r = $request->input('user_r', 0);
				$admingroup->user_u = $request->input('user_u', 0);
				$admingroup->user_d = $request->input('user_d', 0);

				$admingroup->ri_c = $request->input('ri_c', 0);
				$admingroup->ri_r = $request->input('ri_r', 0);
				$admingroup->ri_u = $request->input('ri_u', 0);
				$admingroup->ri_d = $request->input('ri_d', 0);

				$admingroup->hbt_r = $request->input('hbt_r', 0);

				$admingroup->invoice_c = $request->input('invoice_c', 0);
				$admingroup->invoice_r = $request->input('invoice_r', 0);
				$admingroup->invoice_u = $request->input('invoice_u', 0);
				$admingroup->invoice_d = $request->input('invoice_d', 0);

				$admingroup->return_c = $request->input('return_c', 0);
				$admingroup->return_r = $request->input('return_r', 0);
				$admingroup->return_u = $request->input('return_u', 0);
				$admingroup->return_d = $request->input('return_d', 0);

				$admingroup->payment_c = $request->input('payment_c', 0);
				$admingroup->payment_r = $request->input('payment_r', 0);
				$admingroup->payment_u = $request->input('payment_u', 0);
				$admingroup->payment_d = $request->input('payment_d', 0);
				
				$admingroup->setting_u = $request->input('setting_u', 0);

				$admingroup->productphoto_c = $request->input('productphoto_c', 0);
				$admingroup->productphoto_r = $request->input('productphoto_r', 0);
				$admingroup->productphoto_d = $request->input('productphoto_d', 0);

				$admingroup->inventory_c = $request->input('inventory_c', 0);
				$admingroup->inventory_r = $request->input('inventory_r', 0);

				$admingroup->bank_c = $request->input('bank_c', 0);
				$admingroup->bank_r = $request->input('bank_r', 0);
				$admingroup->bank_u = $request->input('bank_u', 0);

				$admingroup->adjustment_c = $request->input('adjustment_c', 0);
				$admingroup->adjustment_r = $request->input('adjustment_r', 0);

				$admingroup->stockcard_r = $request->input('stockcard_r', 0);

				$admingroup->customer_c = $request->input('customer_c', 0);
				$admingroup->customer_r = $request->input('customer_r', 0);
				$admingroup->customer_u = $request->input('customer_u', 0);
				$admingroup->customer_d = $request->input('customer_d', 0);

				$admingroup->transaction_c = $request->input('transaction_c', 0);
				$admingroup->transaction_r = $request->input('transaction_r', 0);
				$admingroup->transaction_u = $request->input('transaction_u', 0);
				$admingroup->transaction_d = $request->input('transaction_d', 0);

				$admingroup->tpayment_c = $request->input('tpayment_c', 0);
				$admingroup->tpayment_r = $request->input('tpayment_r', 0);
				$admingroup->tpayment_u = $request->input('tpayment_u', 0);
				$admingroup->tpayment_d = $request->input('tpayment_d', 0);

				$admingroup->treturn_c = $request->input('treturn_c', 0);
				$admingroup->treturn_r = $request->input('treturn_r', 0);
				$admingroup->treturn_u = $request->input('treturn_u', 0);
				$admingroup->treturn_d = $request->input('treturn_d', 0);

				$admingroup->accountsrecievable_r = $request->input('accountsrecievable_r', 0);

				$admingroup->account_c = $request->input('account_c', 0);
				$admingroup->account_r = $request->input('account_r', 0);
				$admingroup->account_u = $request->input('account_u', 0);
				$admingroup->account_d = $request->input('account_d', 0);

				$admingroup->accountdetail_c = $request->input('accountdetail_c', 0);
				$admingroup->accountdetail_r = $request->input('accountdetail_r', 0);
				$admingroup->accountdetail_u = $request->input('accountdetail_u', 0);
				$admingroup->accountdetail_d = $request->input('accountdetail_d', 0);

				$admingroup->income_r = $request->input('income_r', 0);

				$admingroup->is_active = htmlspecialchars($request->input('is_active', 0));

				$admingroup->update_id = Auth::user()->id;
				
				$admingroup->save();

				if($request->session()->has('last_url'))
	            {
					return redirect($request->session()->get('last_url'))->with('success-message', "Admingroup <strong>$admingroup->name</strong> has been Updated.");
	            }
	            else
	            {
					return redirect(Crypt::decrypt($setting->admin_url) . '/admingroup')->with('success-message', "Admingroup <strong>$admingroup->name</strong> has been Updated.");
	            }
			}
			else
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/admingroup')->with('error-message', 'Can not find any admingroup with ID ' . $id);
			}
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/admingroup/' . $id . '/edit')->withInput()->withErrors($validator);
		}
	}

	/* 
		DELETE A RESOURCE
	*/
	public function destroy(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*Paypalconfig Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->admingroup_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any priviledge to access this page.");
		}
		if ($admingroup->admingroup_d != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any priviledge to access this page.");
		}
		
		$admingroup = Admingroup::find($id);
		if ($admingroup != null)
		{
			$admin = Admin::where('admingroup_id', '=', $admingroup->id)->first();
			if ($admin != null)
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/admingroup')->with('error-message', "Can't delete Admingroup <strong>$admingroup->name</strong>, because this data is in use in other data.");
			}
			
			$admingroup_name = $admingroup->name;
			$admingroup->delete();

			if($request->session()->has('last_url'))
            {
				return redirect($request->session()->get('last_url'))->with('success-message', "Admingroup <strong>$admingroup->name</strong> has been Deleted.");
            }
            else
            {
				return redirect(Crypt::decrypt($setting->admin_url) . '/admingroup')->with('success-message', "Admingroup <strong>$admingroup->name</strong> has been Deleted.");
            }
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/admingroup')->with('error-message', 'Can not find any admingroup with ID ' . $id);
		}
	}
}