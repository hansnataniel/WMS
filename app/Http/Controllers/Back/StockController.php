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

use App\Models\Stock;


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


class StockController extends Controller
{
	/*
		GET THE RESOURCE LIST
	*/
    public function index(Request $request)
    {
    	$setting = Setting::first();
		$data['setting'] = $setting;

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->stock_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any priviledge to access this page.");
		}

		$data['messageModul'] = true;
		$data['alertModul'] = true;
		$data['searchModul'] = true;
		$data['helpModul'] = true;
		$data['navModul'] = true;
		
		$query = Inventory::query();

		$data['criteria'] = '';

		$date = htmlspecialchars($request->input('src_date'));
		if ($date != null)
		{
			$query->where('date', '=', $date);
			$data['criteria']['src_date'] = $date;
		}

		$product_id = htmlspecialchars($request->input('src_product_id'));
		if ($product_id != null)
		{
			$query->where('product_id', '=', $product_id);
			$data['criteria']['product_id'] = $product_id;
		}

		$order_by = htmlspecialchars($request->input('order_by'));
		$order_method = htmlspecialchars($request->input('order_method'));
		if ($order_by != null)
		{
			$query->orderBy($order_by, $order_method);
			$data['order_by'] = $order_by;
			$data['order_method'] = $order_method;
		}
		/* Don't forget to adjust the default order */
		$query->orderBy('created_at', 'desc');

		$all_records = $query->get();
		$records_count = count($all_records);
		$data['records_count'] = $records_count;

		$per_page = 20;
		$data['per_page'] = $per_page;
		$stocks = $query->paginate($per_page);
		$data['stocks'] = $stocks;

		$request->flash();

		$request->session()->put('last_url', URL::full());

		$data['request'] = $request;

        return view('back.stock.index', $data);
    }

    /*
		CREATE A RESOURCE
	*/
    public function create(Request $request)
    {
    	$setting = Setting::first();
		$data['setting'] = $setting;

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->stock_c != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/stock')->with('error-message', "Sorry you don't have any priviledge to access this page.");
		}

		$data['messageModul'] = true;
		$data['alertModul'] = true;
		$data['searchModul'] = false;
		$data['helpModul'] = true;
		$data['navModul'] = true;
		
		$stock = new Inventory;
		$data['stock'] = $stock;

		$data['request'] = $request;

        return view('back.stock.create', $data);
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
			'date'				=> 'required',
			'quantity'			=> 'required|numeric|min:0',
		);

		$validator = Validator::make($inputs, $rules);
		if ($validator->passes())
		{
			$product = Product::find(htmlspecialchars($request->input('product')));

			$stock = new Inventory;
			$stock->product_id = htmlspecialchars($request->input('product'));
			$stock->date = htmlspecialchars($request->input('date'));
			$stock->quantity = htmlspecialchars($request->input('quantity'));
			$stock->last_stock = $product->stock;
			if ($request->input('status') == 0) {
				$inventory->final_stock = $product->stock + $request->input('quantity');
			}
			else
			{
				$inventory->final_stock = $product->stock - $request->input('quantity');
			}
			$stock->status = htmlspecialchars($request->input('is_active', false));
			$stock->note = htmlspecialchars($request->input('is_active', false));

			$stock->create_id = Auth::user()->id;

			$stock->save();

			return redirect(Crypt::decrypt($setting->admin_url) . '/stock')->with('success-message', "Stock <strong>" . Str::words($stock->title, 5) . "</strong> has been Created");
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/stock/create')->withInput()->withErrors($validator);
		}
    }


    /*
		SHOW A RESOURCE
	*/
	public function show(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->stock_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/stock')->with('error-message', "Sorry you don't have any priviledge to access this page.");
		}

		$data['messageModul'] = true;
		$data['alertModul'] = true;
		$data['searchModul'] = false;
		$data['helpModul'] = true;
		$data['navModul'] = true;
		
		$stock = Inventory::find($id);
		if ($stock != null)
		{
			$data['request'] = $request;
			
			$data['stock'] = $stock;
	        return view('back.stock.view', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/stock')->with('error-message', "Can't find Stock with ID " . $id);
		}
	}
}