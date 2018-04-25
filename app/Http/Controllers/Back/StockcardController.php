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

use App\Models\Adjustment;
use App\Models\Transaction;
use App\Models\Transactiondetail;
use App\Models\Ridetail;
use App\Models\Ri;
use App\Models\Po;
use App\Models\Podetail;
use App\Models\Retur;
use App\Models\Returndetail;
use App\Models\Treturn;
use App\Models\Treturndetail;
use App\Models\Inventory;
use App\Models\Productstock;
use App\Models\Product;


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


class StockcardController extends Controller
{
	/*
		GET THE RESOURCE LIST
	*/
    public function index(Request $request)
    {
    	$setting = Setting::first();
		$data['setting'] = $setting;

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->stockcard_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any priviledge to access this page.");
		}

		$data['messageModul'] = true;
		$data['alertModul'] = true;
		$data['searchModul'] = true;
		$data['helpModul'] = true;
		$data['navModul'] = true;
		
		$products = Product::where('is_active', '=', true)->orderBy('name', 'asc')->get();
		$product_options[''] = 'Select Product';
		foreach ($products as $product) {
			$product_options[$product->id] = $product->name;
		}
		$data['product_options'] = $product_options;

		$data['request'] = $request;

        return view('back.stockcard.report', $data);
    }

    public function store(Request $request)
    {
    	$setting = Setting::first();
		$data['setting'] = $setting;

		/**
		 * Validation
		 */
		$inputs = $request->all();
		$rules = array(
			'product'			=> 'required',
			'date_start'				=> 'required',
			'date_end'				=> 'required',
		);

		$validator = Validator::make($inputs, $rules);
		if ($validator->passes())
		{
			$ridetails = Ridetail::get();

			$ridetailids = [];
			foreach ($ridetails as $ridetail) {
				$ridetailids[] = $ridetail->id;
			}
			$inventorys = Inventory::where('type', '=', 'Ri')->whereNotIn('type_id', $ridetailids)->get();
			foreach ($inventorys as $inventory) {
				// echo "$inventory->id";
				$inventory->delete();
			}

			$productid = htmlspecialchars($request->input('product'));
			$datestart = htmlspecialchars($request->input('date_start'));
			$dateend = htmlspecialchars($request->input('date_end'));

			$data['datestart'] = $datestart;
			$data['dateend'] = $dateend;

			$products = Productstock::where('product_id', '=', $productid)->where('is_active', '=', true)->get();
			$productids = [];
			foreach ($products as $product) {
				$productids[] = $product->id;
			}

			$getproduct = Product::find($productid);
			$data['product'] = $getproduct;

			$inventories = Inventory::where('date', '>=', $datestart)->where('date', '<=', $dateend)->whereIn('productstock_id', $productids)->orderBy('date', 'asc')->orderBy('id', 'asc')->get();
			foreach ($inventories as $inventory) {
				$inventoryids[] = $inventory->type_id;
			}
			$data['inventories'] = $inventories;

			return view('back.stockcard.showreport', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/stock-card')->withInput()->withErrors($validator);
		}
    }


    /*
		SHOW A RESOURCE
	*/
	public function getPrint($productid, $datestart, $dateend)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->stockcard_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/stock-card')->with('error-message', "Sorry you don't have any priviledge to access this page.");
		}

		$data['datestart'] = $datestart;
		$data['dateend'] = $dateend;

		$product = Product::find($productid);
		$data['product'] = $product;

		$inventories = Inventory::where('date', '>=', $datestart)->where('date', '<=', $dateend)->where('product_id', '=', $productid)->orderBy('date', 'asc')->orderBy('id', 'asc')->get();
		foreach ($inventories as $inventory) {
			$inventoryids[] = $inventory->type_id;
		}
		$data['inventories'] = $inventories;
			
		return view('back.stockcard.print', $data);
	}
}