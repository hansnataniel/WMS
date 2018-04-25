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


class GudangController extends Controller
{
	/* Get the list of the resource*/
	public function index(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/
		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->gudang_r != true)
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
		
		$query = Gudang::query();

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
		$gudangs = $query->paginate($per_page);
		$data['gudangs'] = $gudangs;

		$request->flash();

		$request->session()->put('last_url', URL::full());

		$data['request'] = $request;

        return view('back.gudang.index', $data);
	}

	/* Create a gudang resource*/
	public function create(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->gudang_c != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/gudang')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		/*Menu Authentication*/

		$data['navModul'] = true;
		$data['helpModul'] = true;
		$data['searchModul'] = false;
		$data['alertModul'] = true;
		$data['messageModul'] = true;
		
		$data['request'] = $request;
		
		$gudang = new Gudang;
		$data['gudang'] = $gudang;

        return view('back.gudang.create', $data);
	}

	public function store(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;
		
		$inputs = $request->all();
		$rules = array(
			'name'				=> 'required',
			'barcode'				=> 'required|unique:gudangs,code_id',
		);

		$validator = Validator::make($inputs, $rules);
		if ($validator->passes())
		{
			$gudang = new Gudang;
			$gudang->name = htmlspecialchars($request->input('name'));
			$gudang->code_id = htmlspecialchars($request->input('barcode'));
			$gudang->is_active = htmlspecialchars($request->input('is_active'));

			$gudang->create_id = Auth::user()->id;
			$gudang->update_id = Auth::user()->id;
			
			$gudang->save();

			$admingroup = Admingroup::find(Auth::user()->admingroup_id);
			if ($admingroup->gudang_r != true)
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('success-message', "Gudang <strong>$gudang->name</strong> has been created");
			}
			return redirect(Crypt::decrypt($setting->admin_url) . '/gudang')->with('success-message', "Gudang <strong>$gudang->name</strong> has been created");
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/gudang/create')->withInput()->withErrors($validator);
		}
	}

	/* Show a resource*/
	public function show(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->gudang_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/gudang')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		/*Menu Authentication*/

		$data['navModul'] = true;
		$data['helpModul'] = true;
		$data['searchModul'] = false;
		$data['alertModul'] = true;
		$data['messageModul'] = true;
		
		
		$gudang = Gudang::find($id);
		if ($gudang != null)
		{
			$data['gudang'] = $gudang;
			$data['request'] = $request;
			
	        return view('back.gudang.view', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/gudang')->with('error-message', 'Can not find gudang with ID ' . $id);
		}
	}

	/* Edit a resource*/
	public function edit(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/
		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->gudang_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}
		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->gudang_u != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/gudang')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		/*Menu Authentication*/

		$data['navModul'] = true;
		$data['helpModul'] = true;
		$data['searchModul'] = false;
		$data['alertModul'] = true;
		$data['messageModul'] = true;
		
		
		$gudang = Gudang::find($id);
		
		if ($gudang != null)
		{
			$data['gudang'] = $gudang;

			$data['request'] = $request;

	        return view('back.gudang.edit', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/gudang')->with('error-message', 'Can not find image with ID ' . $id);
		}
	}

	public function update(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;
		
		$inputs = $request->all();
		$rules = array(
			'name'				=> 'required',
			'barcode'				=> 'required|unique:gudangs,code_id,' . $id,
		);

		$validator = Validator::make($inputs, $rules);
		if ($validator->passes())
		{
			$gudang = Gudang::find($id);
			if ($gudang != null)
			{
				$gudang->name = htmlspecialchars($request->input('name'));
				$gudang->code_id = htmlspecialchars($request->input('barcode'));
				$gudang->is_active = htmlspecialchars($request->input('is_active', 0));
				$gudang->update_id = Auth::user()->id;
				$gudang->save();

				return redirect(Crypt::decrypt($setting->admin_url) . '/gudang')->with('success-message', "Gudang <strong>$gudang->name</strong> has been updated");
			}
			else
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/gudang')->with('error-message', 'Can not find image with ID ' . $id);
			}
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/gudang/' . $id . '/edit')->withInput()->withErrors($validator);
		}
	}


	/* Delete a resource*/
	public function destroy(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->gudang_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}
		if ($admingroup->gudang_d != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}
		
		$gudang = Gudang::find($id);
		if ($gudang != null)
		{
			$rak = Rak::where('gudang_id', '=', $id)->first();
			if($rak != null)
			{
	 			return redirect(Crypt::decrypt($setting->admin_url) . '/gudang')->with('error-message', "Can't delete Gudang <strong>$gudang->name</strong>");
			}
			
			$gudang->delete();

 			return redirect(Crypt::decrypt($setting->admin_url) . '/gudang')->with('success-message', "Gudang <strong>$gudang->name</strong> has been deleted");
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/gudang')->with('error-message', 'Can not find gudang with ID ' . $id);
		}
	}
}