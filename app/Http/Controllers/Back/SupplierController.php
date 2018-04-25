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

use App\Models\Supplier;
use App\Models\Rak;


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


class SupplierController extends Controller
{
	/* Get the list of the resource*/
	public function index(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/
		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->supplier_r != true)
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
		
		$query = Supplier::query();

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
		$suppliers = $query->paginate($per_page);
		$data['suppliers'] = $suppliers;

		$request->flash();

		$request->session()->put('last_url', URL::full());

		$data['request'] = $request;

        return view('back.supplier.index', $data);
	}

	/* Create a supplier resource*/
	public function create(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->supplier_c != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/supplier')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		/*Menu Authentication*/

		$data['navModul'] = true;
		$data['helpModul'] = true;
		$data['searchModul'] = false;
		$data['alertModul'] = true;
		$data['messageModul'] = true;
		
		$data['request'] = $request;
		
		$supplier = new Supplier;
		$data['supplier'] = $supplier;

        return view('back.supplier.create', $data);
	}

	public function store(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;
		
		$inputs = $request->all();
		$rules = array(
			'name'				=> 'required',
			'code'				=> 'required|unique:suppliers,code',
			'address'				=> 'required',
			'due_date'				=> 'required',
		);

		$validator = Validator::make($inputs, $rules);
		if ($validator->passes())
		{
			$supplier = new Supplier;
			$supplier->code = htmlspecialchars($request->input('code'));
			$supplier->name = htmlspecialchars($request->input('name'));
			$supplier->address = htmlspecialchars($request->input('address'));
			$supplier->phone = htmlspecialchars($request->input('phone'));
			$supplier->ket = htmlspecialchars($request->input('description'));
			$supplier->tempo = htmlspecialchars($request->input('due_date'));
			$supplier->is_active = htmlspecialchars($request->input('is_active'));

			$supplier->create_id = Auth::user()->id;
			$supplier->update_id = Auth::user()->id;
			
			$supplier->save();

			$admingroup = Admingroup::find(Auth::user()->admingroup_id);
			if ($admingroup->supplier_r != true)
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('success-message', "Supplier <strong>$supplier->name</strong> has been created");
			}
			return redirect(Crypt::decrypt($setting->admin_url) . '/supplier')->with('success-message', "Supplier <strong>$supplier->name</strong> has been created");
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/supplier/create')->withInput()->withErrors($validator);
		}
	}

	/* Show a resource*/
	public function show(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->supplier_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/supplier')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		/*Menu Authentication*/

		$data['navModul'] = true;
		$data['helpModul'] = true;
		$data['searchModul'] = false;
		$data['alertModul'] = true;
		$data['messageModul'] = true;
		
		
		$supplier = Supplier::find($id);
		if ($supplier != null)
		{
			$data['supplier'] = $supplier;
			$data['request'] = $request;
			
	        return view('back.supplier.view', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/supplier')->with('error-message', 'Can not find supplier with ID ' . $id);
		}
	}

	/* Edit a resource*/
	public function edit(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/
		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->supplier_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}
		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->supplier_u != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/supplier')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		/*Menu Authentication*/

		$data['navModul'] = true;
		$data['helpModul'] = true;
		$data['searchModul'] = false;
		$data['alertModul'] = true;
		$data['messageModul'] = true;
		
		
		$supplier = Supplier::find($id);
		
		if ($supplier != null)
		{
			$data['supplier'] = $supplier;

			$data['request'] = $request;

	        return view('back.supplier.edit', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/supplier')->with('error-message', 'Can not find image with ID ' . $id);
		}
	}

	public function update(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;
		
		$inputs = $request->all();
		$rules = array(
			'name'				=> 'required',
			'code'				=> 'required|unique:suppliers,code,' . $id,
			'address'				=> 'required',
			'due_date'				=> 'required',
		);

		$validator = Validator::make($inputs, $rules);
		if ($validator->passes())
		{
			$supplier = Supplier::find($id);
			if ($supplier != null)
			{
				$supplier->code = htmlspecialchars($request->input('code'));
				$supplier->name = htmlspecialchars($request->input('name'));
				$supplier->address = htmlspecialchars($request->input('address'));
				$supplier->phone = htmlspecialchars($request->input('phone'));
				$supplier->ket = htmlspecialchars($request->input('description'));
				$supplier->tempo = htmlspecialchars($request->input('due_date'));
				$supplier->update_id = Auth::user()->id;
				$supplier->save();

				return redirect(Crypt::decrypt($setting->admin_url) . '/supplier')->with('success-message', "Supplier <strong>$supplier->name</strong> has been updated");
			}
			else
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/supplier')->with('error-message', 'Can not find image with ID ' . $id);
			}
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/supplier/' . $id . '/edit')->withInput()->withErrors($validator);
		}
	}


	/* Delete a resource*/
	public function destroy(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->supplier_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}
		if ($admingroup->supplier_d != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}
		
		$supplier = Supplier::find($id);
		if ($supplier != null)
		{
			$po = Po::where('supplier_id', '=', $id)->first();
			if($po != null)
			{
	 			return redirect(Crypt::decrypt($setting->admin_url) . '/supplier')->with('error-message', "Can't delete Supplier <strong>$supplier->name</strong>");
			}
			
			$supplier->delete();

 			return redirect(Crypt::decrypt($setting->admin_url) . '/supplier')->with('success-message', "Supplier <strong>$supplier->name</strong> has been deleted");
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/supplier')->with('error-message', 'Can not find supplier with ID ' . $id);
		}
	}
}