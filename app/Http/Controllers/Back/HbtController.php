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

use App\Models\Hbt;
use App\Models\Transaction;
use App\Models\Transactionitem;
use App\Models\Rate;
use App\Models\Bank;
use App\Models\Supplier;
use App\Models\Po;
use App\Models\Ri;

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


class HbtController extends Controller
{
	/* Get the list of the resource*/
	public function index(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/
		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->hbt_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		$data['navModul'] = true;
		$data['helpModul'] = true;
		$data['searchModul'] = false;
		$data['alertModul'] = true;
		$data['messageModul'] = true;
		
		
		$query = Hbt::query()->where('status', '=', false);

		$data['criteria'] = '';

		$order_by = htmlspecialchars($request->input('order_by'));
		$order_method = htmlspecialchars($request->input('order_method'));
		if ($order_by != null)
		{
			if (($order_by == 'is_active') or ($order_by == 'is_admin'))
			{
				$query->orderBy($order_by, $order_method)->orderBy('transaction_number', 'desc');
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
			$query->orderBy('transaction_number', 'desc');
		}

		$all_records = $query->get();
		$records_count = count($all_records);
		$data['records_count'] = $records_count;

		$per_page = 20;
		$data['per_page'] = $per_page;
		$hbts = $query->paginate($per_page);
		$data['hbts'] = $hbts;

		$request->session()->put('last_url', URL::full());

		$request->flash();

		$data['request'] = $request;

        return view('back.hbt.index', $data);
	}


	/* Show a resource*/
	public function show(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/
		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->hbt_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

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

			$pos = Po::where('supplier_id', '=', $id)->where('status', '=', 'Dikirim')->get();
			foreach ($pos as $po) {
				$poids[] = $po->id;
			}

			$ris = Ri::whereIn('po_id', $poids)->where('is_invoice', '=', false)->get();
			foreach ($ris as $ri) {
				$riids[] = $ri->id;
			}

			$hbts = Hbt::whereIn('ri_id', $riids)->where('status', '=', false)->orderBy('id', 'desc')->get();
			$data['hbts'] = $hbts;

	        return view('back.hbt.view', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/uninvoiced-debt')->with('error-message', 'Can not find any hbt with ID ' . $id);
		}
	}
}