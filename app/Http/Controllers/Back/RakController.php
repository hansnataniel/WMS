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
use App\Models\Gudang;

use App\Models\Rak;
use App\Models\Product;


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


class RakController extends Controller
{
	/* Get the list of the resource*/
	public function index(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/
		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->rak_r != true)
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
		
		$query = Rak::query();

		$name = htmlspecialchars($request->input('src_name'));
		if ($name != null)
		{
			$query->where('name', 'LIKE', '%' . $name . '%');
			$data['criteria']['src_name'] = $name;
		}

		$code_id = htmlspecialchars($request->input('src_code_id'));
		if ($code_id != null)
		{
			$query->where('code_id', 'LIKE', '%' . $code_id . '%');
			$data['criteria']['src_code_id'] = $code_id;
		}

		$gudang_id = htmlspecialchars($request->input('src_gudang_id'));
		if ($gudang_id != null)
		{
			$query->where('gudang_id', '=', $gudang_id);
			$data['criteria']['src_gudang_id'] = $gudang_id;
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
		$raks = $query->paginate($per_page);
		$data['raks'] = $raks;

		$request->flash();

		$gudangs = Gudang::where('is_active', '=', true)->orderBy('name')->get();
		if($gudangs->isEmpty())
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/gudang/create')->with('error-message', "Your gudang is Empty, Please create it first");
		}

		$gudang_options[''] = "Select Gudang";
		foreach ($gudangs as $gudang) {
			$gudang_options[$gudang->id] = $gudang->name;
		}
		$data['gudang_options'] = $gudang_options;

		$request->session()->put('last_url', URL::full());

		$data['request'] = $request;

        return view('back.rak.index', $data);
	}

	/* Create a rak resource*/
	public function create(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->rak_c != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/rak')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		/*Menu Authentication*/

		$data['navModul'] = true;
		$data['helpModul'] = true;
		$data['searchModul'] = false;
		$data['alertModul'] = true;
		$data['messageModul'] = true;
		
		$data['request'] = $request;
		
		$rak = new Rak;
		$data['rak'] = $rak;

		$gudangs = Gudang::where('is_active', '=', true)->orderBy('name')->get();
		if($gudangs->isEmpty())
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/gudang/create')->with('error-message', "Your gudang is Empty, Please create it first");
		}

		$gudang_options[''] = "Select Gudang";
		foreach ($gudangs as $gudang) {
			$gudang_options[$gudang->id] = $gudang->name;
		}
		$data['gudang_options'] = $gudang_options;

        return view('back.rak.create', $data);
	}

	public function store(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;
		
		$inputs = $request->all();
		$rules = array(
			'name'				=> 'required',
			'barcode'				=> 'required|unique:raks,code_id',
			'gudang'				=> 'required',
		);

		$validator = Validator::make($inputs, $rules);
		if ($validator->passes())
		{
			$rak = new Rak;
			$rak->code_id = htmlspecialchars($request->input('barcode'));
			$rak->name = htmlspecialchars($request->input('name'));
            $rak->gudang_id = htmlspecialchars($request->input('gudang', 0));
			$rak->is_active = htmlspecialchars($request->input('is_active'));

			$rak->create_id = Auth::user()->id;
			$rak->update_id = Auth::user()->id;
			
			$rak->save();

			$admingroup = Admingroup::find(Auth::user()->admingroup_id);
			if ($admingroup->rak_r != true)
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('success-message', "Rak <strong>$rak->name</strong> has been created");
			}
			return redirect(Crypt::decrypt($setting->admin_url) . '/rak')->with('success-message', "Rak <strong>$rak->name</strong> has been created");
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/rak/create')->withInput()->withErrors($validator);
		}
	}

	/* Show a resource*/
	public function show(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->rak_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/rak')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		/*Menu Authentication*/

		$data['navModul'] = true;
		$data['helpModul'] = true;
		$data['searchModul'] = false;
		$data['alertModul'] = true;
		$data['messageModul'] = true;
		
		
		$rak = Rak::find($id);
		if ($rak != null)
		{
			$data['rak'] = $rak;
			$data['request'] = $request;
			
	        return view('back.rak.view', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/rak')->with('error-message', 'Can not find rak with ID ' . $id);
		}
	}

	/* Edit a resource*/
	public function edit(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/
		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->rak_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}
		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->rak_u != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/rak')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		/*Menu Authentication*/

		$data['navModul'] = true;
		$data['helpModul'] = true;
		$data['searchModul'] = false;
		$data['alertModul'] = true;
		$data['messageModul'] = true;
		
		
		$rak = Rak::find($id);
		
		if ($rak != null)
		{
			$data['rak'] = $rak;

			$gudangs = Gudang::where('is_active', '=', true)->orderBy('name')->get();
			$gudang_options[''] = "Select Gudang";
			foreach ($gudangs as $gudang) {
				$gudang_options[$gudang->id] = $gudang->name;
			}
			$data['gudang_options'] = $gudang_options;

			$data['request'] = $request;

	        return view('back.rak.edit', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/rak')->with('error-message', 'Can not find image with ID ' . $id);
		}
	}

	public function update(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;
		
		$inputs = $request->all();
		$rules = array(
			'name'				=> 'required',
			'barcode'				=> 'required|unique:raks,code_id,' . $id,
			'gudang'				=> 'required',
		);

		$validator = Validator::make($inputs, $rules);
		if ($validator->passes())
		{
			$rak = Rak::find($id);
			if ($rak != null)
			{
				$rak->name = htmlspecialchars($request->input('name'));
				$rak->code_id = htmlspecialchars($request->input('barcode'));
                $rak->gudang_id = htmlspecialchars($request->input('gudang', 0));
				$rak->is_active = htmlspecialchars($request->input('is_active', 0));
				$rak->update_id = Auth::user()->id;
				$rak->save();

				return redirect(Crypt::decrypt($setting->admin_url) . '/rak')->with('success-message', "Rak <strong>$rak->name</strong> has been updated");
			}
			else
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/rak')->with('error-message', 'Can not find image with ID ' . $id);
			}
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/rak/' . $id . '/edit')->withInput()->withErrors($validator);
		}
	}


	/* Delete a resource*/
	public function destroy(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->rak_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}
		if ($admingroup->rak_d != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}
		
		$rak = Rak::find($id);
		if ($rak != null)
		{
			$product = Product::where('rak_id', '=', $id)->first();
			if($product != null)
			{
	 			return redirect(Crypt::decrypt($setting->admin_url) . '/rak')->with('error-message', "Can't delete Rak <strong>$rak->name</strong>");
			}
			
			$rak->delete();

 			return redirect(Crypt::decrypt($setting->admin_url) . '/rak')->with('success-message', "Rak <strong>$rak->name</strong> has been deleted");
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/rak')->with('error-message', 'Can not find rak with ID ' . $id);
		}
	}
}