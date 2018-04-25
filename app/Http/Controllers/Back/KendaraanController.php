<?php

/*
	Use Brand Space Here
*/
namespace App\Http\Controllers\Back;

/*
	Call Model Here
*/
use App\Models\Setting;
use App\Models\Admingroup;
use App\Models\Que;

use App\Models\Kendaraan;
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


class KendaraanController extends Controller
{
	/* Get the list of the resource*/
	public function index(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/
		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->kendaraan_r != true)
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
		
		$query = Kendaraan::query();

		$brand = htmlspecialchars($request->input('src_brand'));
		if ($brand != null)
		{
			$query->where('brand', 'LIKE', '%' . $brand . '%');
			$data['criteria']['src_brand'] = $brand;
		}

		$code = htmlspecialchars($request->input('src_code'));
		if ($code != null)
		{
			$query->where('code', 'LIKE', '%' . $code . '%');
			$data['criteria']['src_code'] = $code;
		}

		$th_start = htmlspecialchars($request->input('src_th_start'));
		if ($th_start != null)
		{
			$query->where('th_start', 'LIKE', '%' . $th_start . '%');
			$data['criteria']['src_th_start'] = $th_start;
		}

		$th_end = htmlspecialchars($request->input('src_th_end'));
		if ($th_end != null)
		{
			$query->where('th_end', 'LIKE', '%' . $th_end . '%');
			$data['criteria']['src_th_end'] = $th_end;
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
		$kendaraans = $query->paginate($per_page);
		$data['kendaraans'] = $kendaraans;

		$request->flash();

		$request->session()->put('last_url', URL::full());

		$data['request'] = $request;

        return view('back.kendaraan.index', $data);
	}

	/* Create a kendaraan resource*/
	public function create(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->kendaraan_c != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/kendaraan')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		/*Menu Authentication*/

		$data['navModul'] = true;
		$data['helpModul'] = true;
		$data['searchModul'] = false;
		$data['alertModul'] = true;
		$data['messageModul'] = true;
		
		$data['request'] = $request;
		
		$kendaraan = new Kendaraan;
		$data['kendaraan'] = $kendaraan;

        return view('back.kendaraan.create', $data);
	}

	public function store(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;
		
		$inputs = $request->all();
		$rules = array(
			'brand'				=> 'required',
			'type'				=> 'required',
			'th_start'				=> 'required',
			'th_end'				=> 'required',
			'transmition'				=> 'required',
			'cc'				=> 'required',
			'code'				=> 'required',
		);

		$validator = Validator::make($inputs, $rules);
		if ($validator->passes())
		{
			$kendaraan = new Kendaraan;
			$kendaraan->brand = htmlspecialchars($request->input('brand'));
			$kendaraan->type = htmlspecialchars($request->input('type'));
			$kendaraan->th_start = htmlspecialchars($request->input('th_start'));
			$kendaraan->th_end = htmlspecialchars($request->input('th_end'));
			$kendaraan->transmition = htmlspecialchars($request->input('transmition'));
			$kendaraan->cc = htmlspecialchars($request->input('cc'));
			$kendaraan->code = htmlspecialchars($request->input('code'));
			$kendaraan->is_active = htmlspecialchars($request->input('is_active'));

			$kendaraan->create_id = Auth::user()->id;
			$kendaraan->update_id = Auth::user()->id;
			
			$kendaraan->save();

			$admingroup = Admingroup::find(Auth::user()->admingroup_id);
			if ($admingroup->kendaraan_r != true)
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('success-message', "Kendaraan <strong>$kendaraan->brand ($kendaraan->type | $kendaraan->th_start - $kendaraan->th_end)</strong> has been created");
			}
			return redirect(Crypt::decrypt($setting->admin_url) . '/kendaraan')->with('success-message', "Kendaraan <strong>$kendaraan->brand ($kendaraan->type | $kendaraan->th_start - $kendaraan->th_end)</strong> has been created");
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/kendaraan/create')->withInput()->withErrors($validator);
		}
	}

	/* Show a resource*/
	public function show(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->kendaraan_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/kendaraan')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		/*Menu Authentication*/

		$data['navModul'] = true;
		$data['helpModul'] = true;
		$data['searchModul'] = false;
		$data['alertModul'] = true;
		$data['messageModul'] = true;
		
		
		$kendaraan = Kendaraan::find($id);
		if ($kendaraan != null)
		{
			$data['kendaraan'] = $kendaraan;
			$data['request'] = $request;
			
	        return view('back.kendaraan.view', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/kendaraan')->with('error-message', 'Can not find kendaraan with ID ' . $id);
		}
	}

	/* Edit a resource*/
	public function edit(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/
		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->kendaraan_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}
		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->kendaraan_u != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/kendaraan')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		/*Menu Authentication*/

		$data['navModul'] = true;
		$data['helpModul'] = true;
		$data['searchModul'] = false;
		$data['alertModul'] = true;
		$data['messageModul'] = true;
		
		
		$kendaraan = Kendaraan::find($id);
		
		if ($kendaraan != null)
		{
			$data['kendaraan'] = $kendaraan;

			$data['request'] = $request;

	        return view('back.kendaraan.edit', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/kendaraan')->with('error-message', 'Can not find image with ID ' . $id);
		}
	}

	public function update(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;
		
		$inputs = $request->all();
		$rules = array(
			'brand'				=> 'required',
			'type'				=> 'required',
			'th_start'				=> 'required',
			'th_end'				=> 'required',
			'transmition'				=> 'required',
			'cc'				=> 'required',
			'code'				=> 'required',
		);

		$validator = Validator::make($inputs, $rules);
		if ($validator->passes())
		{
			$kendaraan = Kendaraan::find($id);
			if ($kendaraan != null)
			{
				$kendaraan->brand = htmlspecialchars($request->input('brand'));
				$kendaraan->type = htmlspecialchars($request->input('type'));
				$kendaraan->th_start = htmlspecialchars($request->input('th_start'));
				$kendaraan->th_end = htmlspecialchars($request->input('th_end'));
				$kendaraan->transmition = htmlspecialchars($request->input('transmition'));
				$kendaraan->cc = htmlspecialchars($request->input('cc'));
				$kendaraan->code = htmlspecialchars($request->input('code'));
				$kendaraan->is_active = htmlspecialchars($request->input('is_active', 0));
				$kendaraan->update_id = Auth::user()->id;
				$kendaraan->save();

				return redirect(Crypt::decrypt($setting->admin_url) . '/kendaraan')->with('success-message', "Kendaraan <strong>$kendaraan->brand ($kendaraan->type | $kendaraan->th_start - $kendaraan->th_end)</strong> has been updated");
			}
			else
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/kendaraan')->with('error-message', 'Can not find image with ID ' . $id);
			}
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/kendaraan/' . $id . '/edit')->withInput()->withErrors($validator);
		}
	}


	/* Delete a resource*/
	public function destroy(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->kendaraan_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}
		if ($admingroup->kendaraan_d != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}
		
		$kendaraan = Kendaraan::find($id);
		if ($kendaraan != null)
		{
			$product = Product::where('kendaraan_id', '=', $id)->first();
			if($product != null)
			{
	 			return redirect(Crypt::decrypt($setting->admin_url) . '/kendaraan')->with('error-message', "Can't delete Kendaraan <strong>$kendaraan->brand</strong>");
			}
			
			$kendaraan->delete();

 			return redirect(Crypt::decrypt($setting->admin_url) . '/kendaraan')->with('success-message', "Kendaraan <strong>$kendaraan->brand ($kendaraan->type | $kendaraan->th_start - $kendaraan->th_end)</strong> has been deleted");
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/kendaraan')->with('error-message', 'Can not find kendaraan with ID ' . $id);
		}
	}
}