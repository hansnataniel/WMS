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

use App\Models\Invoice;
use App\Models\Invoicedetail;
use App\Models\Ri;
use App\Models\Ridetail;
use App\Models\Po;
use App\Models\Podetail;
use App\Models\Retur;
use App\Models\Returndetail;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Productstock;
use App\Models\Inventory;
use App\Models\Bank;
use App\Models\Pricegap;


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


class ReturnController extends Controller
{
	/* Get the list of the resource*/
	public function index(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/
		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->return_r != true)
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
		
		$query = Retur::query();

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
			$query->orderBy('created_at', 'desc');
		}

		$all_records = $query->get();
		$records_count = count($all_records);
		$data['records_count'] = $records_count;

		$per_page = 20;
		$data['per_page'] = $per_page;
		$returns = $query->paginate($per_page);
		$data['returns'] = $returns;

		$request->flash();

		$request->session()->put('last_url', URL::full());

		$data['request'] = $request;

		$suppliers = Supplier::where('is_active', '=', true)->orderBy('name')->get();
		$supplier_options[''] = 'Select Supplier';
		foreach ($suppliers as $supplier) {
			$supplier_options[$supplier->id] = $supplier->name;
		}
		$data['supplier_options'] = $supplier_options;

        return view('back.return.index', $data);
	}

	/* Create a return resource*/
	public function create(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->return_c != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/return')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		/*Menu Authentication*/

		$data['navModul'] = true;
		$data['helpModul'] = false;
		$data['searchModul'] = false;
		$data['alertModul'] = true;
		$data['messageModul'] = true;
		
		$data['request'] = $request;
		
		$return = new Retur;
		$data['return'] = $return;

		$suppliers = Supplier::where('is_active', '=', true)->orderBy('name')->get();
		$supplier_options[''] = 'Select Supplier';
		foreach ($suppliers as $supplier) {
			$supplier_options[$supplier->id] = $supplier->name;
		}
		$data['supplier_options'] = $supplier_options;

		Session::forget('add_product');
		Session::forget('returnid');

        return view('back.return.create', $data);
	}

	public function store(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;
		
		$inputs = $request->all();
		$rules = array(
			'no_nota'	=> 'required',
			'supplier'	=> 'required',
			'receive_item'	=> 'required',
			'date'		=> 'required',
		);

		$validator = Validator::make($inputs, $rules);
		if ($validator->passes())
		{
			$ridetails = $request->input('ridetail');

			if($ridetails == 0)
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/return/create')->withInput()->with('error-message', "Please select the product first");
			}

			/*Check maximum qty*/
			foreach ($ridetails as $id => $item) {
				if(($item['qty'] != null) AND ($item['qty'] > 0))
				{
					/*Get Maximum Qty*/
					$total = 0;
					$ridetailget = Ridetail::find($id);
					$returndetails = Returndetail::where('ridetail_id', '=', $id)->get();
					foreach ($returndetails as $returndetail) {
						$total = $total + $returndetail->qty;
					}

					if(!$returndetails->isEmpty())
					{
						$max = $ridetailget->qty - $total;
					}
					else
					{
						$max = $ridetailget->qty;
					}


					if($item['qty'] > $max)
					{
						$product = Product::find($item['bahan']);

						$getmaxproducts[] = $product->name . " quantity must be less than or equal to " . $max;
					}
				}
				else
				{
					$product = Product::find($item['bahan']);

					$getmaxproducts[] = $product->name . " quantity must greater than or equal to 1";
				}
			}
			if(isset($getmaxproducts))
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/return/create')->withInput()->withErrors($getmaxproducts);
			}


			$receiveitem = htmlspecialchars($request->input('receive_item'));
			$date = htmlspecialchars($request->input('date'));
			$checkri = Ri::find($receiveitem);
			// dd($receiveitem);
			if(($date < $checkri->date) OR ($date > date('Y-m-d')))
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/return/create')->withInput()->with('error-message', "You can only setting Return date range from Recieve Item date until today");
			}

			DB::transaction(function() use ($ridetails, $request) 
			{
				global $return;

				$return = new Retur;
				$return->no_nota = htmlspecialchars($request->input('no_nota'));
				$return->ri_id = htmlspecialchars($request->input('receive_item'));
				$return->supplier_id = htmlspecialchars($request->input('supplier'));
				$return->date = htmlspecialchars($request->input('date'));
				$return->message = htmlspecialchars($request->input('msg'));

				$return->create_id = Auth::user()->id;
				$return->update_id = Auth::user()->id;

				$return->save();

				$totalprice = 0;
				foreach ($ridetails as $id => $item) {
					if(($item['qty'] != null) AND ($item['qty'] > 0))
					{
						$returndetail = new Returndetail;
						$returndetail->return_id = $return->id;
						$returndetail->ridetail_id = $id;
						$returndetail->qty = $item['qty'];
						$returndetail->price = $item['price'];
						$returndetail->save();

						/*Adding stock on product stock*/
						if($returndetail->ridetail->product_id == 0)
						{
							$productstock = Productstock::where('product_id', '=', $returndetail->ridetail->podetail->product_id)->where('rak_id', '=', $returndetail->ridetail->rak_id)->first();

							$productstock->product_id = $returndetail->ridetail->podetail->product_id;
						}
						else
						{
							$productstock = Productstock::where('product_id', '=', $returndetail->ridetail->product_id)->where('rak_id', '=', $returndetail->ridetail->rak_id)->first();

							$productstock->product_id = $returndetail->ridetail->product_id;
						}
						
						$productstock->stock = $productstock->stock - $returndetail->qty;
						$productstock->rak_id = $returndetail->ridetail->rak_id;
						$productstock->save();

						if($returndetail->qty != 0)
						{
							/*Adding Inventory Data*/
							$inventory = new Inventory;
							$inventory->date = $return->date;							
							$inventory->productstock_id = $productstock->id;							
							$inventory->type = 'R';
							$inventory->type_id = $returndetail->id;

							$inventory->real_price = $returndetail->price;

							$getlastinv = Inventory::where('productstock_id', '=', $productstock->id)->where('date', '<=', $return->date)->orderBy('date', 'desc')->orderBy('id', 'desc')->first();

							$inventory->qty_last = $getlastinv->qty_z;
							$inventory->price_last = $getlastinv->price_z;

							$inventory->qty_in = 0;
							$inventory->price_in = 0;

							$inventory->qty_out = $returndetail->qty;
							$inventory->price_out = $getlastinv->price_z;

							$inventory->qty_z = $getlastinv->qty_z - $returndetail->qty;
							if($getlastinv->qty_z - $returndetail->qty == 0)
							{
								$inventory->price_z = 0;
							}
							else
							{
								$inventory->price_z = $getlastinv->price_z;
							}
							$inventory->save();

							if($inventory->real_price != $inventory->price_out)
							{
								$pricegap = new Pricegap;
								$pricegap->date = $return->date;
								$pricegap->returndetail_id = $returndetail->id;
								$pricegap->price = $inventory->real_price - $inventory->price_out;
								$pricegap->save();
							}

							$lastinventory = Inventory::where('productstock_id', '=', $inventory->productstock_id)->orderBy('date', 'desc')->orderBy('id', 'desc')->first();

							$productstock = Productstock::find($lastinventory->productstock_id);
							$productstock->stock = $lastinventory->qty_z;
							$productstock->save();

							$product = Product::find($productstock->product_id);
							$product->price = $lastinventory->price_z;
							$product->save();

							update_inventory($inventory->productstock_id, $inventory->date, $inventory->id);
						}
					}
				}
			});

			global $return;
			
			$admingroup = Admingroup::find(Auth::user()->admingroup_id);
			if ($admingroup->return_r != true)
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('success-message', "Return <strong>$return->no_nota</strong> has been created");
			}
			return redirect(Crypt::decrypt($setting->admin_url) . '/return')->with('success-message', "Return <strong>$return->no_nota</strong> has been created");
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/return/create')->withInput()->withErrors($validator);
		}
	}

	/* Show a resource*/
	public function show(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->return_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/return')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		/*Menu Authentication*/

		$data['navModul'] = true;
		$data['helpModul'] = true;
		$data['searchModul'] = false;
		$data['alertModul'] = true;
		$data['messageModul'] = true;
		
		
		$return = Retur::find($id);
		if ($return != null)
		{
			$data['return'] = $return;
			$data['request'] = $request;
			
	        return view('back.return.view', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/return')->with('error-message', 'Can not find return with ID ' . $id);
		}
	}

	/* Edit a resource*/
	public function edit(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/
		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->return_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}
		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->return_u != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/return')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		/*Menu Authentication*/

		$data['navModul'] = true;
		$data['helpModul'] = false;
		$data['searchModul'] = false;
		$data['alertModul'] = true;
		$data['messageModul'] = true;
		
		
		$return = Retur::find($id);
		
		if ($return != null)
		{
			$data['return'] = $return;

			Session::forget('add_product');
			Session::forget('returnid');

			Session::put('returnid', $return->id);

			$returndetails = Returndetail::where('return_id', '=', $return->id)->get();
			foreach ($returndetails as $returndetail) {
				Session::push('add_product', $returndetail->ridetail_id);
			}

			/*GET Supplier*/
			$suppliers = Supplier::where('is_active', '=', true)->orderBy('name')->get();
			$supplier_options[''] = 'Select Supplier';
			foreach ($suppliers as $supplier) {
				$supplier_options[$supplier->id] = $supplier->name;
			}
			$data['supplier_options'] = $supplier_options;


			/*GET RI Data*/
			$pos = Po::where('supplier_id', '=', $return->supplier_id)->get();
			foreach ($pos as $po) {
				$poids[] = $po->id;
			}

			$ris = Ri::whereIn('po_id', $poids)->orderBy('date', 'desc')->get();
			$ri_options[''] = 'Select Receive Item No.Nota';
			foreach ($ris as $ri) {
				$ri_options[$ri->id] = $ri->no_nota;
			}
			$data['ri_options'] = $ri_options;


			/*Get Product*/
			$ridetails = Ridetail::where('ri_id', '=', $return->ri_id)->whereNotIn('id', Session::get('add_product'))->where('product_id', '=', 0)->get();
			$data['ridetails'] = $ridetails;

			$frees = Ridetail::where('ri_id', '=', $return->ri_id)->whereNotIn('id', Session::get('add_product'))->where('product_id', '!=', 0)->get();
			$data['frees'] = $frees;

			$data['request'] = $request;

	        return view('back.return.edit', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/return')->with('error-message', 'Can not find image with ID ' . $id);
		}
	}

	public function update(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;
		
		$inputs = $request->all();
		$rules = array(
			'no_nota'	=> 'required',
			'supplier'	=> 'required',
			'receive_item'	=> 'required',
			'date'		=> 'required',
		);

		$validator = Validator::make($inputs, $rules);
		if ($validator->passes())
		{
			$return = Retur::find($id);
			if ($return != null)
			{
				$ridetails = $request->input('ridetail');

				if($ridetails == 0)
				{
					return redirect(Crypt::decrypt($setting->admin_url) . '/return/' . $id . '/edit')->withInput()->with('error-message', "Please select the product first");
				}

				/*Check maximum qty*/
				foreach ($ridetails as $getid => $item) {
					if(($item['qty'] != null) AND ($item['qty'] > 0))
					{
						/*Get Maximum Qty*/
						$total = 0;
						$ridetailget = Ridetail::find($getid);
						$returndetails = Returndetail::where('ridetail_id', '=', $getid)->get();
						foreach ($returndetails as $returndetail) {
							$total = $total + $returndetail->qty;
						}

						if($returndetails->isEmpty())
						{
							$max = $ridetailget->qty;
						}
						else
						{
							$returndetailget = Returndetail::where('ridetail_id', '=', $getid)->where('return_id', '=', $return->id)->first();
							$max = ($ridetailget->qty - $total) + $returndetailget->qty;
						}


						if($item['qty'] > $max)
						{
							$product = Product::find($item['bahan']);

							$getmaxproducts[] = $product->name . " quantity must be less than or equal to " . $max;
						}
					}
					else
					{
						$product = Product::find($item['bahan']);

						$getmaxproducts[] = $product->name . " quantity must greater than or equal to 1";
					}
				}
				if(isset($getmaxproducts))
				{
					return redirect(Crypt::decrypt($setting->admin_url) . '/return/' . $id . '/edit')->withInput()->withErrors($getmaxproducts);
				}



				$receiveitem = htmlspecialchars($request->input('receive_item'));
				$date = htmlspecialchars($request->input('date'));
				$checkri = Ri::find($receiveitem);
				// dd($checkri);
				if(($date < $checkri->date) OR ($date > date('Y-m-d')))
				{
					return redirect(Crypt::decrypt($setting->admin_url) . '/return/' . $id . '/edit')->withInput()->with('error-message', "You can only setting Return date range from Recieve Item date until today");
				}



				DB::transaction(function() use ($request, $ridetails, $return) 
				{
					$return->no_nota = htmlspecialchars($request->input('no_nota'));
					$return->ri_id = htmlspecialchars($request->input('receive_item'));
					$return->supplier_id = htmlspecialchars($request->input('supplier'));
					$return->date = htmlspecialchars($request->input('date'));
					$return->message = htmlspecialchars($request->input('msg'));
					$return->save();
					

					/*Delete Return Detail*/
					$getreturndetails = Returndetail::where('return_id', '=', $return->id)->get();
					foreach ($getreturndetails as $getreturndetail) 
					{
						/*Delete Inventory then recalculate it*/
						$getinventory = Inventory::where('type', '=', 'R')->where('type_id', '=', $getreturndetail->id)->first();
						$inventoryproductstockid = $getinventory->productstock_id;
						$inventorydate = $getinventory->date;
						$inventoryid = $getinventory->id;
						$getinventory->delete();

						$getpricegap = Pricegap::where('returndetail_id', '=', $getreturndetail->id)->first();
						if($getpricegap != null)
						{
							$getpricegap->delete();
						}

						update_inventory($inventoryproductstockid, $inventorydate, $inventoryid);

						$lastinventory = Inventory::where('productstock_id', '=', $inventoryproductstockid)->orderBy('date', 'desc')->orderBy('id', 'desc')->first();

						$productstock = Productstock::find($lastinventory->productstock_id);
						$productstock->stock = $lastinventory->qty_z;
						$productstock->save();

						$product = Product::find($productstock->product_id);
						$product->price = $lastinventory->price_z;
						$product->save();
						
						$getreturndetail->delete();
					}


					$totalprice = 0;
					foreach ($ridetails as $id => $item) {
						if(($item['qty'] != null) AND ($item['qty'] >= 0))
						{
							$returndetail = new Returndetail;
							$returndetail->return_id = $return->id;
							$returndetail->ridetail_id = $id;
							$returndetail->qty = $item['qty'];
							$returndetail->price = $item['price'];
							$returndetail->save();

							/*Adding stock on product stock*/
							if($returndetail->ridetail->product_id == 0)
							{
								$productstock = Productstock::where('product_id', '=', $returndetail->ridetail->podetail->product_id)->where('rak_id', '=', $returndetail->ridetail->rak_id)->first();

								$productstock->product_id = $returndetail->ridetail->podetail->product_id;
							}
							else
							{
								$productstock = Productstock::where('product_id', '=', $returndetail->ridetail->product_id)->where('rak_id', '=', $returndetail->ridetail->rak_id)->first();

								$productstock->product_id = $returndetail->ridetail->product_id;
							}
							
							$productstock->stock = $productstock->stock - $returndetail->qty;
							$productstock->rak_id = $returndetail->ridetail->rak_id;
							$productstock->save();

							if($returndetail->qty != 0)
							{
								/*Adding Inventory Data*/
								$inventory = new Inventory;
								$inventory->date = $return->date;							
								$inventory->productstock_id = $productstock->id;							
								$inventory->type = 'R';
								$inventory->type_id = $returndetail->id;

								$inventory->real_price = $returndetail->price;

								$getlastinv = Inventory::where('productstock_id', '=', $productstock->id)->where('date', '<=', $return->date)->orderBy('date', 'desc')->orderBy('id', 'desc')->first();

								$inventory->qty_last = $getlastinv->qty_z;
								$inventory->price_last = $getlastinv->price_z;

								$inventory->qty_in = 0;
								$inventory->price_in = 0;

								$inventory->qty_out = $returndetail->qty;
								$inventory->price_out = $getlastinv->price_z;

								$inventory->qty_z = $getlastinv->qty_z - $returndetail->qty;
								if($getlastinv->qty_z - $returndetail->qty == 0)
								{
									$inventory->price_z = 0;
								}
								else
								{
									$inventory->price_z = $getlastinv->price_z;
								}
								$inventory->save();

								if($inventory->real_price != $inventory->price_out)
								{
									$pricegap = new Pricegap;
									$pricegap->date = $return->date;
									$pricegap->returndetail_id = $returndetail->id;
									$pricegap->price = $inventory->real_price - $inventory->price_out;
									$pricegap->save();
								}

								$lastinventory = Inventory::where('productstock_id', '=', $inventory->productstock_id)->orderBy('date', 'desc')->orderBy('id', 'desc')->first();

								$productstock = Productstock::find($lastinventory->productstock_id);
								$productstock->stock = $lastinventory->qty_z;
								$productstock->save();

								$product = Product::find($productstock->product_id);
								$product->price = $lastinventory->price_z;
								$product->save();

								update_inventory($inventory->productstock_id, $inventory->date, $inventory->id);
							}
						}
					}
				});

				return redirect(Crypt::decrypt($setting->admin_url) . '/return')->with('success-message', "Return <strong>$return->no_nota</strong> has been updated");
			}
			else
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/return')->with('error-message', 'Can not find image with ID ' . $id);
			}
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/return/' . $id . '/edit')->withInput()->withErrors($validator);
		}
	}


	/* Delete a resource*/
	public function destroy(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->return_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}
		if ($admingroup->return_d != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}
		
		$return = Retur::find($id);
		if ($return != null)
		{
			DB::transaction(function() use ($return, $setting) 
			{
				$returndetails = Returndetail::where('return_id', '=', $return->id)->get();
				if (count($returndetails) != 0)
				{
					foreach ($returndetails as $returndetail) 
					{
						$getinventory = Inventory::where('type', '=', 'R')->where('type_id', '=', $returndetail->id)->first();
						$inventoryproductstockid = $getinventory->productstock_id;
						$inventorydate = $getinventory->date;
						$inventoryid = $getinventory->id;
						$getinventory->delete();

						$pricegap = Pricegap::where('returndetail_id', '=', $returndetail->id)->first();
						if($pricegap != null)
						{
							$pricegap->delete();
						}

						$lastinventory = Inventory::where('productstock_id', '=', $inventoryproductstockid)->orderBy('date', 'desc')->orderBy('id', 'desc')->first();

						$productstock = Productstock::find($lastinventory->productstock_id);
						$productstock->stock = $lastinventory->qty_z;
						$productstock->save();

						$product = Product::find($productstock->product_id);
						$product->price = $lastinventory->price_z;
						$product->save();

						update_inventory($inventoryproductstockid, $inventorydate, $inventoryid);

						$returndetail->delete();
					}
				}

				$return->delete();
			});

 			return redirect(Crypt::decrypt($setting->admin_url) . '/return')->with('success-message', "Return <strong>$return->no_nota</strong> has been deleted");
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/return')->with('error-message', 'Can not find return with ID ' . $id);
		}
	}

	public function getRi(Request $request, $supplier)
	{
		$pos = Po::where('supplier_id', '=', $supplier)->where('status', '=', 'Dikirim')->orderBy('id', 'desc')->get();
		foreach ($pos as $po) {
			$poids[] = $po->id;
		}
		$data['pos'] = $pos;

		if(!$pos->isEmpty())
		{
			$ris = Ri::whereIn('po_id', $poids)->orderBy('date', 'desc')->orderBy('id', 'desc')->get();
			$ri_options[''] = 'Select Receive Item No.Nota';
			foreach ($ris as $ri) {
				$ri_options[$ri->id] = $ri->no_nota;
			}
		}
		else
		{
			$ri_options[''] = 'Select Receive Item No.Nota';
		}
		$data['ri_options'] = $ri_options;

		return view('back.return.ri', $data);
	}

	public function getProduct(Request $request, $id)
	{
		$ri = Ri::find($id);
		$data['ri'] = $ri;

		$ridetails = Ridetail::where('ri_id', '=', $id)->where('product_id', '=', 0)->get();
		$data['ridetails'] = $ridetails;

		$frees = Ridetail::where('ri_id', '=', $id)->where('product_id', '!=', 0)->get();
		$data['frees'] = $frees;

		return view('back.return.product', $data);
	}

	public function getReplace(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		$ridetails = Ridetail::where('ri_id', '=', $id)->whereNotIn('id', Session::get('add_product'))->where('product_id', '=', 0)->get();
		$data['ridetails'] = $ridetails;

		$frees = Ridetail::where('ri_id', '=', $id)->whereNotIn('id', Session::get('add_product'))->where('product_id', '!=', 0)->get();
		$data['frees'] = $frees;

		return view('back.return.replace', $data);
	}

	// public function getAdd(Request $request, $id)
	// {
	// 	$setting = Setting::first();
	// 	$data['setting'] = $setting;
	// 	$data['dataid'] = $id;

	// 	$ridetail = Ridetail::find($id);
	// 	$data['ridetail'] = $ridetail;

	// 	Session::push('add_product', $id);
			
	// 	return view('back.return.add', $data);
	// }

	// public function getAddedit(Request $request, $id, $returndetail_id)
	// {
	// 	$setting = Setting::first();
	// 	$data['setting'] = $setting;
	// 	$data['dataid'] = $id;

	// 	$ridetail = Ridetail::find($id);
	// 	$data['ridetail'] = $ridetail;

	// 	Session::push('add_product', $id);

	// 	return view('back.return.addedit', $data);
	// }

	// public function getReplace()
	// {
	// 	$setting = Setting::first();
	// 	$data['setting'] = $setting;

	// 	$products = Product::whereNotIn('id', Session::get('add_product'))->where('is_active', '=', true)->orderBy('name')->get();

	// 	$data['products'] = $products;

	// 	$product_options[] = 'Select Product';
	// 	foreach ($products as $product) {
	// 		$product_options[$product->id] = $product->name;
	// 	}
	// 	$data['product_options'] = $product_options;

	// 	return view('back.return.replace', $data);
	// }

	public function getAdd($id, $ridetailid = null, $qty = null, $price = null)
	{
		if($ridetailid != null)
		{
			// dd($id . " - " . $ridetailid . " - " . $qty . " - " . $price);
			$data['ridetailid'] = $ridetailid;
		}
		if($qty != null)
		{
			$data['qty'] = $qty;
		}
		if($price != null)
		{
			$data['price'] = $price;
		}

		$setting = Setting::first();
		$data['setting'] = $setting;

		$ridetails = Ridetail::where('ri_id', '=', $id)->where('product_id', '=', 0)->get();
		$data['ridetails'] = $ridetails;

		$frees = Ridetail::where('ri_id', '=', $id)->where('product_id', '!=', 0)->get();
		$data['frees'] = $frees;

		return view('back.return.form', $data);
	}

	public function getForm($ridetailid, $productid, $qty, $price)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		$ridetail = Ridetail::find($ridetailid);
		$data['ridetail'] = $ridetail;

		Session::push('add_product', $productid);

		$product = Product::find($productid);

		// dd($ridetailid);
		// dd($ridetail->id);
		// dd($ridetail->ri_id);
		// dd($product->id);
		$data['product'] = $product;
		$data['qty'] = $qty;
		$data['price'] = $price;

		$subtotal = $qty * $price;
		$data['subtotal'] = $subtotal;

		return view('back.return.search', $data);
	}



	public function getDrop(Request $request, $id)
	{
		$getridetail = Ridetail::find($id);

		$getsessions = Session::get('add_product');
		foreach ($getsessions as $getsession) {
			$getsessionids[] = $getsession;
		}
		// dd($getsessions);
		$removedatas = array_diff($getsessionids, array($id));
		// dd($removedatas);
		Session::forget('add_product');
		foreach($removedatas as $removedata)
		{
			Session::push('add_product', $removedata);
		}
		// dd(Session::get('add_product'));

		if(count($removedatas) == 0)
		{
			$ridetails = Ridetail::where('ri_id', '=', $getridetail->ri_id)->where('product_id', '=', 0)->get();

			$frees = Ridetail::where('ri_id', '=', $getridetail->ri_id)->where('product_id', '!=', 0)->get();
		}
		else
		{
			$ridetails = Ridetail::where('ri_id', '=', $getridetail->ri_id)->whereNotIn('id', $removedatas)->where('product_id', '=', 0)->get();

			$frees = Ridetail::where('ri_id', '=', $getridetail->ri_id)->whereNotIn('id', $removedatas)->where('product_id', '!=', 0)->get();
		}

		$data['ridetails'] = $ridetails;
		$data['frees'] = $frees;

		return view('back.return.replace', $data);
	}

	public function getPrint(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		$return = Retur::find($id);
		if($return != null)
		{
			$data['return'] = $return;

			return view('back.return.print', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/return')->with('error-message', 'Can not find Return with ID ' . $id);
		}
	}

	public function getPdf(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		$return = Retur::find($id);
		if($return != null)
		{
			$data['return'] = $return;

			$rak = Rak::find($return->ri->po->rak_id);
			$data['rak'] = $rak;

			$supplier = Supplier::find($return->ri->po->supplier_id);
			$data['supplier'] = $supplier;

			$html = \view('back.return.print', $data);
		
			// $pdf = App::make('dompdf.wrapper');
			$pdf = PDF::loadHTML($html);
			return $pdf->setPaper('a4', 'portrait')->stream();

			// return view('back.return.print', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/return')->with('error-message', 'Can not find Return with ID ' . $id);
		}
	}
}