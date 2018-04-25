<?php

/*
	Use No_nota Space Here
*/
namespace App\Http\Controllers\Back;

/*
	Call Model Here
*/
use App\Models\Setting;
use App\Models\Admingroup;
use App\Models\Que;

use App\Models\Adjustment;
use App\Models\Product;
use App\Models\Notification;
use App\Models\Inventory;
use App\Models\Productstock;

/*
	Call Mail file & mail facades
*/
use App\Mail\Back\Notif;

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
use DB;


class AdjustmentController extends Controller
{
	/* Get the list of the resource*/
	public function index(Request $request)
	{
		$setting = Setting::first();	
		$data['setting'] = $setting;

		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->adjustment_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		/*Menu Authentication*/

		$data['navModul'] = true;
		$data['helpModul'] = true;
		$data['searchModul'] = true;
		$data['alertModul'] = true;
		$data['messageModul'] = true;
		
		
		$query = Adjustment::query();

		$data['criteria'] = '';

		$no_nota = htmlspecialchars($request->input('src_no_nota'));
		if ($no_nota != null)
		{
			$query->where('no_nota', 'LIKE', '%' . $no_nota . '%');
			$data['criteria']['src_no_nota'] = $no_nota;
		}

		$date = htmlspecialchars($request->input('src_date'));
		if ($date != null)
		{
			$query->where('date', '=', $date);
			$data['criteria']['src_date'] = $date;
		}

		$status = htmlspecialchars($request->input('src_status'));
		if ($status != null)
		{
			$query->where('status', '=', $status);
			$data['criteria']['src_status'] = $status;
		}

		$order_by = htmlspecialchars($request->input('order_by'));
		$order_method = htmlspecialchars($request->input('order_method'));
		if ($order_by != null)
		{
			if ($order_by == 'status')
			{
				$query->orderBy($order_by, $order_method)->orderBy('date', 'desc')->orderBy('id', 'desc');
			}
			else
			{
			// return 'Work';
				$query->orderBy($order_by, $order_method);
			}
			$data['order_by'] = $order_by;
			$data['order_method'] = $order_method;
		}
		else
		{
			$query->orderBy('date', 'desc')->orderBy('id', 'desc');
		}

		$all_records = $query->get();
		$records_count = count($all_records);
		$data['records_count'] = $records_count;

		$per_adjustment = 20;
		$data['per_page'] = $per_adjustment;
		$adjustments = $query->paginate($per_adjustment);
		$data['adjustments'] = $adjustments;

		$request->session()->put('last_url', URL::full());

		$data['request'] = $request;

		$request->flash();

        return view('back.adjustment.index', $data);
	}

	/* Create a new resource*/
	public function create(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->adjustment_c != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/adjustment')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		/*Menu Authentication*/

		$data['navModul'] = true;
		$data['helpModul'] = true;
		$data['searchModul'] = false;
		$data['alertModul'] = true;
		$data['messageModul'] = true;
		
		
		$adjustment = new Adjustment;
		$data['adjustment'] = $adjustment;

		$products = Product::where('is_active', '=', true)->orderBy('name', 'asc')->get();
		$data['products'] = $products;
		$product_options[''] = 'Select Product';
		foreach ($products as $product) {
			$product_options[$product->id] = $product->name;
		}
		$data['product_options'] = $product_options;

		$data['request'] = $request;

        return view('back.adjustment.create', $data);
	}

	public function store(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;
		
		$inputs = $request->all();
		$rules = array(
			'product'		=> 'required',
			'rak'		=> 'required',
			'date'			=> 'required',
			'quantity'		=> 'required|numeric|min:0',
		);

		$validator = Validator::make($inputs, $rules);
		if ($validator->passes())
		{
			DB::transaction(function () use ($request){
				global $adjustment;

				$adjustment = new Adjustment;
				$adjustment->no_nota = htmlspecialchars($request->input('nota'));
				$adjustment->product_id = htmlspecialchars($request->input('product'));
				$adjustment->rak_id = htmlspecialchars($request->input('rak'));
				$adjustment->quantity = htmlspecialchars($request->input('quantity'));
				$adjustment->date = htmlspecialchars($request->input('date'));
				$adjustment->status = htmlspecialchars($request->input('status'));
				$adjustment->note = htmlspecialchars($request->input('note'));
				$adjustment->create_id = Auth::user()->id;
				$adjustment->save();

				$inventory = new Inventory;
				$inventory->date = $adjustment->date;
				$inventory->productstock_id = $productstock->id;
				$inventory->type = 'Adj';
				$inventory->type_id = $adjustment->id;

				$getlastinv = Inventory::where('productstock_id', '=', $productstock->id)->where('date', '<=', $adjustment->date)->orderBy('date', 'desc')->orderBy('id', 'desc')->first();

				if($adjustment->status == 'In')
				{
					if($getlastinv == null)
					{
						$inventory->qty_last = 0;
						$inventory->price_last = 0;

						$inventory->qty_z = $adjustment->quantity;
						$inventory->price_z = 0;
					}
					else
					{
						$inventory->qty_last = $getlastinv->qty_z;
						$inventory->price_last = $getlastinv->price_z;

						$inventory->qty_z = $getlastinv->qty_z + $adjustment->quantity;
						$inventory->price_z = (($getlastinv->qty_z * $getlastinv->price_z) + ($adjustment->quantity * 0)) / ($getlastinv->qty_z + $adjustment->quantity);
					}

					$inventory->qty_in = $adjustment->quantity;
					$inventory->price_in = 0;
					$inventory->qty_out = 0;
					$inventory->price_out = 0;
				}
				else
				{
					if($getlastinv == null)
					{
						$inventory->qty_last = 0;
						$inventory->price_last = 0;

						$inventory->qty_z = -$adjustment->quantity;
						$inventory->price_z = 0;
					}
					else
					{
						$inventory->qty_last = $getlastinv->qty_z;
						$inventory->price_last = $getlastinv->price_z;

						$inventory->qty_z = $getlastinv->qty_z - $adjustment->quantity;
						$inventory->price_z = (($getlastinv->qty_z * $getlastinv->price_z) + ($adjustment->quantity * 0)) / ($getlastinv->qty_z + $adjustment->quantity);
					}

					$inventory->qty_out = $adjustment->quantity;
					$inventory->price_out = 0;
					$inventory->qty_in = 0;
					$inventory->price_in = 0;
				}
				$inventory->real_price = 0;
				$inventory->save();

				$lastinventory = Inventory::where('productstock_id', '=', $productstock->id)->orderBy('date', 'desc')->orderBy('id', 'desc')->first();

				$productstock = Productstock::where('product_id', '=', $adjustment->product_id)->where('rak_id', '=', $adjustment->rak_id)->first();
				$productstock->stock = $lastinventory->qty_z;
				$productstock->save();
					
				$product = Product::find($adjustment->product_id);
				$product->price = $lastinventory->price_z;
				$product->save();

				update_inventory($adjustment->product_id, $adjustment->date, $inventory->id);
			});

			global $adjustment;

			return redirect(Crypt::decrypt($setting->admin_url) . '/adjustment')->with('success-message', "Adjustment <strong>" . Str::words($adjustment->no_nota, 5) . "</strong> has been Created");
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/adjustment/create')->withInput()->withErrors($validator);
		}
	}

	/* Show a resource*/
	public function show(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->adjustment_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/adjustment')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		/*Menu Authentication*/

		$data['navModul'] = true;
		$data['helpModul'] = false;
		$data['searchModul'] = false;
		$data['alertModul'] = true;
		$data['messageModul'] = true;
		
		
		$adjustment = Adjustment::find($id);
		if ($adjustment != null)
		{
			$data['adjustment'] = $adjustment;
			$data['request'] = $request;
	        return view('back.adjustment.view', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/adjustment')->with('error-message', 'Can not find adjustment with ID ' . $id);
		}
	}

	/* Edit a resource*/
	public function edit(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/
		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->adjustment_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}
		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->adjustment_u != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/adjustment')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		/*Menu Authentication*/

		$data['navModul'] = true;
		$data['helpModul'] = true;
		$data['searchModul'] = false;
		$data['alertModul'] = true;
		$data['messageModul'] = true;
		
		
		$adjustment = adjustment::find($id);
		
		if ($adjustment != null)
		{
			$data['adjustment'] = $adjustment;

			$products = Product::where('is_active', '=', true)->orderBy('name', 'asc')->get();
			$data['products'] = $products;
			$product_options[''] = 'Select Product';
			foreach ($products as $product) {
				$product_options[$product->id] = $product->name;
			}
			$data['product_options'] = $product_options;

			$data['request'] = $request;

	        return view('back.adjustment.edit', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/adjustment')->with('error-message', 'Can not find image with ID ' . $id);
		}
	}

	public function update(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;
		
		$inputs = $request->all();
		$rules = array(
			'product'		=> 'required',
			'rak'		=> 'required',
			'date'			=> 'required',
			'quantity'		=> 'required|numeric|min:0',
		);

		$validator = Validator::make($inputs, $rules);
		if ($validator->passes())
		{
			$adjustment = adjustment::find($id);
			if ($adjustment != null)
			{
				DB::transaction(function () use ($request, $adjustment){
					$adjustment->no_nota = htmlspecialchars($request->input('nota'));
					$adjustment->product_id = htmlspecialchars($request->input('product'));
					$adjustment->rak_id = htmlspecialchars($request->input('rak'));
					$adjustment->quantity = htmlspecialchars($request->input('quantity'));
					$adjustment->date = htmlspecialchars($request->input('date'));
					$adjustment->status = htmlspecialchars($request->input('status'));
					$adjustment->note = htmlspecialchars($request->input('note'));
					$adjustment->create_id = Auth::user()->id;
					$adjustment->save();

					/*Delete Inventory then recalculate it*/
					$getinventory = Inventory::where('type', '=', 'Adj')->where('type_id', '=', $adjustment->id)->first();
					$inventorymaterialstockid = $getinventory->materialstock_id;
					$inventorydate = $getinventory->date;
					$inventoryid = $getinventory->id;
					$getinventory->delete();

					update_inventory($inventorymaterialstockid, $inventorydate, $inventoryid);


					$inventory = new Inventory;
					$inventory->date = $adjustment->date;
					$inventory->productstock_id = $productstock->id;
					$inventory->type = 'Adj';
					$inventory->type_id = $adjustment->id;

					$getlastinv = Inventory::where('productstock_id', '=', $productstock->id)->where('date', '<=', $adjustment->date)->orderBy('date', 'desc')->orderBy('id', 'desc')->first();

					if($adjustment->status == 'In')
					{
						if($getlastinv == null)
						{
							$inventory->qty_last = 0;
							$inventory->price_last = 0;

							$inventory->qty_z = $adjustment->quantity;
							$inventory->price_z = 0;
						}
						else
						{
							$inventory->qty_last = $getlastinv->qty_z;
							$inventory->price_last = $getlastinv->price_z;

							$inventory->qty_z = $getlastinv->qty_z + $adjustment->quantity;
							$inventory->price_z = (($getlastinv->qty_z * $getlastinv->price_z) + ($adjustment->quantity * 0)) / ($getlastinv->qty_z + $adjustment->quantity);
						}

						$inventory->qty_in = $adjustment->quantity;
						$inventory->price_in = 0;
						$inventory->qty_out = 0;
						$inventory->price_out = 0;
					}
					else
					{
						if($getlastinv == null)
						{
							$inventory->qty_last = 0;
							$inventory->price_last = 0;

							$inventory->qty_z = -$adjustment->quantity;
							$inventory->price_z = 0;
						}
						else
						{
							$inventory->qty_last = $getlastinv->qty_z;
							$inventory->price_last = $getlastinv->price_z;

							$inventory->qty_z = $getlastinv->qty_z - $adjustment->quantity;
							$inventory->price_z = (($getlastinv->qty_z * $getlastinv->price_z) + ($adjustment->quantity * 0)) / ($getlastinv->qty_z + $adjustment->quantity);
						}

						$inventory->qty_out = $adjustment->quantity;
						$inventory->price_out = 0;
						$inventory->qty_in = 0;
						$inventory->price_in = 0;
					}
					$inventory->real_price = 0;
					$inventory->save();

					$lastinventory = Inventory::where('productstock_id', '=', $productstock->id)->orderBy('date', 'desc')->orderBy('id', 'desc')->first();

					$productstock = Productstock::where('product_id', '=', $adjustment->product_id)->where('rak_id', '=', $adjustment->rak_id)->first();
					$productstock->stock = $lastinventory->qty_z;
					$productstock->save();

					$product = Product::find($adjustment->product_id);
					$product->price = $lastinventory->price_z;
					$product->save();

					update_inventory($adjustment->product_id, $adjustment->date, $inventory->id);
				});

				return redirect(Crypt::decrypt($setting->admin_url) . '/adjustment')->with('success-message', "adjustment <strong>$adjustment->no_nota</strong> has been updated");
			}
			else
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/adjustment')->with('error-message', 'Can not find image with ID ' . $id);
			}
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/adjustment/' . $id . '/edit')->withInput()->withErrors($validator);
		}
	}


	/* Delete a resource*/
	public function destroy(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->adjustment_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}
		if ($admingroup->adjustment_d != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}
		
		$adjustment = adjustment::find($id);
		if ($adjustment != null)
		{
			DB::transaction(function() use ($adjustment, $request, $setting){

				$getinventory = Inventory::where('type', '=', 'Adj')->where('type_id', '=', $adjustment->id)->first();
				$inventoryproductstockid = $getinventory->productstock_id;
				$inventorydate = $getinventory->date;
				$inventoryid = $getinventory->id;
				$getinventory->delete();

				$lastinventory = Inventory::where('productstock_id', '=', $inventoryproductstockid)->orderBy('date', 'desc')->orderBy('id', 'desc')->first();

				$productstock = Productstock::find($inventoryproductstockid);
				if($lastinventory != null)
				{
					$productstock->stock = $lastinventory->qty_z;
				}
				else
				{
					$productstock->stock = 0;
				}
				$productstock->save();

				$product = Product::find($productstock->product_id);
				if($lastinventory != null)
				{
					$product->price = $lastinventory->price_z;
				}
				else
				{
					$product->price = 0;
				}
				$product->save();

				update_inventory($inventoryproductstockid, $inventorydate, $inventoryid);
				
				$adjustment->delete();
			});

 			return redirect(Crypt::decrypt($setting->admin_url) . '/adjustment')->with('success-message', "adjustment <strong>$adjustment->no_nota</strong> has been deleted");
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/adjustment')->with('error-message', 'Can not find adjustment with ID ' . $id);
		}
	}
}