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
use App\Models\Gudang;

use App\Models\Po;
use App\Models\Podetail;
use App\Models\Ri;
use App\Models\Ridetail;
use App\Models\Product;
use App\Models\Hbt;
use App\Models\Retur;
use App\Models\Invoice;
use App\Models\Invoicedetail;
use App\Models\Supplier;
use App\Models\Inventory;
use App\Models\Productstock;


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
use PDF;


class RiController extends Controller
{
	/* Get the list of the resource*/
	public function index(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/
		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->ri_r != true)
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
		
		$query = Ri::query();

		$no_nota = htmlspecialchars($request->input('src_no_nota'));
		if ($no_nota != null)
		{
			$query->where('no_nota', 'LIKE', '%' . $no_nota . '%');
			$data['criteria']['src_no_nota'] = $no_nota;
		}

		$date = htmlspecialchars($request->input('src_date'));
		if ($date != null)
		{
			$query->where('date', 'LIKE', '%' . $date . '%');
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
		$ris = $query->paginate($per_page);
		$data['ris'] = $ris;

		$request->flash();

		$request->session()->put('last_url', URL::full());

		$data['request'] = $request;

        return view('back.ri.index', $data);
	}

	/* Create a ri resource*/
	public function create(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->ri_c != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/ri')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		/*Menu Authentication*/

		$data['navModul'] = true;
		$data['helpModul'] = true;
		$data['searchModul'] = false;
		$data['alertModul'] = true;
		$data['messageModul'] = true;
		
		$data['request'] = $request;
		
		$ri = new Ri;
		$data['ri'] = $ri;

		$suppliers = Supplier::where('is_active', '=', true)->orderBy('name', 'asc')->get();
		$supplier_options[''] = 'Select Supplier';
		foreach ($suppliers as $supplier) {
			$supplier_options[$supplier->id] = $supplier->name;
		}
		$data['supplier_options'] = $supplier_options;

		$products = Product::where('is_active', '=', true)->orderBy('name')->get();
		if($products->isEmpty())
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/product/create')->with('error-message', "Sorry you don't have product, please create it first.");
		}
		$product_options[''] = 'Select Product';
		foreach ($products as $product) {
			$product_options[$product->id] = $product->name;
		}
		$data['product_options'] = $product_options;

		Session::forget('add_product');

        return view('back.ri.create', $data);
	}

	public function store(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;
		
		$inputs = $request->all();
		$rules = array(
			'no_nota'			=> 'required',
			'date'				=> 'required',
			'supplier'			=> 'required',
		);

		$validator = Validator::make($inputs, $rules);
		if ($validator->passes())
		{
			$podetails = $request->input('podetail');
			$freedetails = $request->input('freedetail');

			// if(($podetails == 0) OR ($podetails == null))
			// {
			// 	return redirect(Crypt::decrypt($setting->admin_url) . '/ri/create')->withInput()->with('error-message', "Please select the Purchase Order first");
			// }

			/*Check maximum qty*/
			if($podetails != null)
			{
				foreach ($podetails as $getid => $item) {
					if($item['product'] == 0)
					{
						$product = Product::find($item['bahan']);

						if(($item['qty'] != null) AND ($item['qty'] > 0))
						{
							// dd($item['qty']);
							if($item['qty'] > $item['max'])
							{
								$getmaxproducts[] = $product->name . " quantity must be less then or equal to " . $item['max'];
							}
						}

						if($item['rak'] == null)
						{
							$getmaxproducts[] = "Rak for $product->name is required";
						}
					}
				}

				if(isset($getmaxproducts))
				{
					return redirect(Crypt::decrypt($setting->admin_url) . '/ri/create')->withInput()->withErrors($getmaxproducts);
				}

				if($request->input('purchase_order') != null)
				{
					$po = htmlspecialchars($request->input('purchase_order'));
					$date = htmlspecialchars($request->input('date'));
					$checkpo = Po::find($po);
					if(($date < $checkpo->date) OR ($date > date('Y-m-d')))
					{
						return redirect(Crypt::decrypt($setting->admin_url) . '/ri/create')->withInput()->withErrors("Recieve Item date must between Purchase Order date and today");
					}
				}
			}

			if($freedetails != null)
			{
				foreach ($freedetails as $getid => $item) {
					$product = Product::find($item['product']);

					if($item['rak'] == null)
					{
						$getmaxproducts[] = "Rak for $product->name is required";
					}
				}

				if(isset($getmaxproducts))
				{
					return redirect(Crypt::decrypt($setting->admin_url) . '/ri/create')->withInput()->withErrors($getmaxproducts);
				}
			}

			DB::transaction(function() use ($request, $podetails, $freedetails) {
				global $ri;

				$ri = new Ri;
				$ri->no_nota = htmlspecialchars($request->input('no_nota'));
				if($request->input('purchase_order') != null)
				{
					$ri->po_id = htmlspecialchars($request->input('purchase_order'));
				}
				else
				{
					$ri->po_id = 0;
				}
				$ri->supplier_id = htmlspecialchars($request->input('supplier'));
				$ri->date = htmlspecialchars($request->input('date'));
				$ri->message = htmlspecialchars($request->input('msg'));

				$ri->create_id = Auth::user()->id;
				$ri->update_id = Auth::user()->id;

				$ri->save();

				// $rakids = htmlspecialchars($request->input('rak'));

				$totalprice = 0;
				if($podetails != null)
				{
					foreach ($podetails as $id => $item) {
						if(($item['qty'] != null) AND ($item['qty'] > 0))
						{
							$getridetails = Ridetail::where('podetail_id', '=', $id)->get();

							$ridetail = new Ridetail;
							$ridetail->product_id = $item['product'];
							$ridetail->ri_id = $ri->id;
							$ridetail->rak_id = $item['rak'];
							if($request->input('purchase_order') != null)
							{
								$ridetail->podetail_id = $id;
							}
							else
							{
								$ridetail->podetail_id = 0;
							}
							$ridetail->qty = $item['qty'];
							// $ridetail->price = $product->price;
							$ridetail->save();

							/*Adding stock on product stock*/
							if($item['product'] == 0)
							{
								$checkproductstock = Productstock::where('product_id', '=', $ridetail->podetail->product_id)->where('rak_id', '=', $ridetail->rak_id)->first();
							}
							else
							{
								$checkproductstock = Productstock::where('product_id', '=', $ridetail->product_id)->where('rak_id', '=', $ridetail->rak_id)->first();
							}

							// if($checkproductstock != null)
							// {
								$productstock = $checkproductstock;
								$productstock->stock = $productstock->stock + $ridetail->qty;
							// }
							// else
							// {
							// 	$productstock = new Productstock;
							// 	$productstock->stock = $ridetail->qty;
							// }
							
							if($item['product'] == 0)
							{
								// $productstock->product_id = $ridetail->podetail->product_id;
								$productstock->product_id = $ridetail->podetail->product_id;
							}
							else
							{
								$productstock->product_id = $ridetail->product_id;
							}

							$productstock->rak_id = $ridetail->rak_id;
							$productstock->save();

							if($ridetail->qty != 0)
							{
								/*Adding Inventory Data*/
								$inventory = new Inventory;
								$inventory->date = $ri->date;							
								$inventory->productstock_id = $productstock->id;							
								$inventory->type = 'Ri';
								$inventory->type_id = $ridetail->id;

								$getlastinv = Inventory::where('productstock_id', '=', $productstock->id)->where('date', '<=', $ri->date)->orderBy('date', 'desc')->orderBy('id', 'desc')->first();

								if($ridetail->product_id == 0)
								{
									if($getlastinv == null)
									{
										$inventory->qty_last = 0;
										$inventory->price_last = 0;

										$inventory->qty_z = $ridetail->qty;
										$inventory->price_z = $ridetail->podetail->price;
									}
									else
									{
										$inventory->qty_last = $getlastinv->qty_z;
										$inventory->price_last = $getlastinv->price_z;

										$inventory->qty_z = $getlastinv->qty_z + $ridetail->qty;
										$inventory->price_z = (($getlastinv->qty_z * $getlastinv->price_z) + ($ridetail->qty * $ridetail->podetail->price)) / ($getlastinv->qty_z + $ridetail->qty);
									}
									$inventory->real_price = $ridetail->podetail->price;
									$inventory->qty_in = $ridetail->qty;
									$inventory->price_in = $ridetail->podetail->price;
								}
								else
								{
									if($getlastinv == null)
									{
										$inventory->qty_last = 0;
										$inventory->price_last = 0;

										$inventory->qty_z = $ridetail->qty;
										$inventory->price_z = 0;
									}
									else
									{
										$inventory->qty_last = $getlastinv->qty_z;
										$inventory->price_last = $getlastinv->price_z;

										$inventory->qty_z = $getlastinv->qty_z + $ridetail->qty;
										$inventory->price_z = (($getlastinv->qty_z * $getlastinv->price_z) + ($ridetail->qty * 0)) / ($getlastinv->qty_z + $ridetail->qty);
									}
									$inventory->real_price = 0;
									$inventory->qty_in = $ridetail->qty;
									$inventory->price_in = 0;
								}

								$inventory->qty_out = 0;
								$inventory->price_out = 0;
								$inventory->save();

								$lastinventory = Inventory::where('productstock_id', '=', $inventory->productstock_id)->orderBy('date', 'desc')->orderBy('id', 'desc')->first();

								$productstock = Productstock::find($lastinventory->productstock_id);
								$productstock->rak_id = $ridetail->rak_id;
								$productstock->stock = $lastinventory->qty_z;
								$productstock->save();

								$product = Product::find($productstock->product_id);
								$product->price = $lastinventory->price_z;
								$product->save();

								update_inventory($inventory->productstock_id, $inventory->date, $inventory->id);
							}

							/*counting RI Detail price*/
							if($item['product'] == 0)
							{
								$totalprice = $totalprice + ($ridetail->qty * $ridetail->podetail->price);
							}
						}
					}
				}

				if($freedetails != null)
				{
					foreach ($freedetails as $id => $item) {
						if(($item['qty'] != null) AND ($item['qty'] > 0))
						{
							$ridetail = new Ridetail;
							$ridetail->product_id = $item['product'];
							$ridetail->ri_id = $ri->id;
							$ridetail->rak_id = $item['rak'];
							if($request->input('purchase_order') != null)
							{
								$ridetail->podetail_id = $id;
							}
							else
							{
								$ridetail->podetail_id = 0;
							}
							$ridetail->qty = $item['qty'];
							// $ridetail->price = $product->price;
							$ridetail->save();

							/*Adding stock on product stock*/
							if($item['product'] == 0)
							{
								$checkproductstock = Productstock::where('product_id', '=', $ridetail->podetail->product_id)->where('rak_id', '=', $ridetail->rak_id)->first();
							}
							else
							{
								$checkproductstock = Productstock::where('product_id', '=', $ridetail->product_id)->where('rak_id', '=', $ridetail->rak_id)->first();
							}

							// if($checkproductstock != null)
							// {
								$productstock = $checkproductstock;
								$productstock->stock = $productstock->stock + $ridetail->qty;
							// }
							// else
							// {
							// 	$productstock = new Productstock;
							// 	$productstock->stock = $ridetail->qty;
							// }
							
							if($item['product'] == 0)
							{
								// $productstock->product_id = $ridetail->podetail->product_id;
								$productstock->product_id = $ridetail->podetail->product_id;
							}
							else
							{
								$productstock->product_id = $ridetail->product_id;
							}

							$productstock->rak_id = $ridetail->rak_id;
							$productstock->save();

							if($ridetail->qty != 0)
							{
								/*Adding Inventory Data*/
								$inventory = new Inventory;
								$inventory->date = $ri->date;							
								$inventory->productstock_id = $productstock->id;							
								$inventory->type = 'Ri';
								$inventory->type_id = $ridetail->id;

								$getlastinv = Inventory::where('productstock_id', '=', $productstock->id)->where('date', '<=', $ri->date)->orderBy('date', 'desc')->orderBy('id', 'desc')->first();

								if($ridetail->product_id == 0)
								{
									if($getlastinv == null)
									{
										$inventory->qty_last = 0;
										$inventory->price_last = 0;

										$inventory->qty_z = $ridetail->qty;
										$inventory->price_z = $ridetail->podetail->price;
									}
									else
									{
										$inventory->qty_last = $getlastinv->qty_z;
										$inventory->price_last = $getlastinv->price_z;

										$inventory->qty_z = $getlastinv->qty_z + $ridetail->qty;
										$inventory->price_z = (($getlastinv->qty_z * $getlastinv->price_z) + ($ridetail->qty * $ridetail->podetail->price)) / ($getlastinv->qty_z + $ridetail->qty);
									}
									$inventory->real_price = $ridetail->podetail->price;
									$inventory->qty_in = $ridetail->qty;
									$inventory->price_in = $ridetail->podetail->price;
								}
								else
								{
									if($getlastinv == null)
									{
										$inventory->qty_last = 0;
										$inventory->price_last = 0;

										$inventory->qty_z = $ridetail->qty;
										$inventory->price_z = 0;
									}
									else
									{
										$inventory->qty_last = $getlastinv->qty_z;
										$inventory->price_last = $getlastinv->price_z;

										$inventory->qty_z = $getlastinv->qty_z + $ridetail->qty;
										$inventory->price_z = (($getlastinv->qty_z * $getlastinv->price_z) + ($ridetail->qty * 0)) / ($getlastinv->qty_z + $ridetail->qty);
									}
									$inventory->real_price = 0;
									$inventory->qty_in = $ridetail->qty;
									$inventory->price_in = 0;
								}

								$inventory->qty_out = 0;
								$inventory->price_out = 0;
								$inventory->save();

								$lastinventory = Inventory::where('productstock_id', '=', $inventory->productstock_id)->orderBy('date', 'desc')->orderBy('id', 'desc')->first();

								$productstock = Productstock::find($lastinventory->productstock_id);
								$productstock->rak_id = $ridetail->rak_id;
								$productstock->stock = $lastinventory->qty_z;
								$productstock->save();

								$product = Product::find($productstock->product_id);
								$product->price = $lastinventory->price_z;
								$product->save();

								update_inventory($inventory->productstock_id, $inventory->date, $inventory->id);
							}

							/*counting RI Detail price*/
							if($item['product'] == 0)
							{
								$totalprice = $totalprice + ($ridetail->qty * $ridetail->podetail->price);
							}
						}
					}
				}

				/*Adding data to Hutang Belum Tertagih*/
				$hbt = new Hbt;
				$hbt->ri_id = $ri->id;
				$hbt->amount = $totalprice;
				$hbt->save();

				if($request->input('purchase_order') != null)
				{
					update_po_status($ri->po_id);
				}
			});

			global $ri;

			$admingroup = Admingroup::find(Auth::user()->admingroup_id);
			if ($admingroup->ri_r != true)
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('success-message', "Ri <strong>$ri->no_nota</strong> has been created");
			}
			return redirect(Crypt::decrypt($setting->admin_url) . '/ri')->with('success-message', "Ri <strong>$ri->no_nota</strong> has been created");
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/ri/create')->withInput()->withErrors($validator);
		}
	}

	/* Show a resource*/
	public function show(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->ri_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/ri')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		/*Menu Authentication*/

		$data['navModul'] = true;
		$data['helpModul'] = true;
		$data['searchModul'] = false;
		$data['alertModul'] = true;
		$data['messageModul'] = true;
		
		
		$ri = Ri::find($id);
		if ($ri != null)
		{
			$data['ri'] = $ri;
			$data['request'] = $request;

			$ridetails = Ridetail::where('ri_id', '=', $ri->id)->where(function($qr){
				$qr->where('product_id', '=', 0);
				$qr->orWhere('product_id', '=', '0');
				$qr->orWhere('product_id', '=', null);
			})->get();
			// $ridetails = Ridetail::where('ri_id', '=', $id)->get();
			$data['ridetails'] = $ridetails;
			
	        return view('back.ri.view', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/ri')->with('error-message', 'Can not find ri with ID ' . $id);
		}
	}

	/* Edit a resource*/
	public function edit(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/
		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->ri_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}
		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->ri_u != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/ri')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		/*Menu Authentication*/

		$data['navModul'] = true;
		$data['helpModul'] = true;
		$data['searchModul'] = false;
		$data['alertModul'] = true;
		$data['messageModul'] = true;
		
		
		$ri = Ri::find($id);
		
		if ($ri != null)
		{
			$data['ri'] = $ri;

			if($ri->is_invoice == true)
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/ri')->with('error-message', 'You can not edit this Receive Item');
			}

			$return = Retur::where('ri_id', '=', $ri->id)->first();
			if($return != null)
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/ri')->with('error-message', 'You can not edit this Receive Item');
			}

			$data['ri'] = $ri;

			Session::forget('add_product');

			$checkridetails = Ridetail::where('ri_id', '=', $ri->id)->get();
			foreach ($checkridetails as $checkridetail) {
				$checkridetailids[] = $checkridetail->id;
			}

			if(!$checkridetails->isEmpty())
			{				
				$checkinvoices = Invoicedetail::whereIn('ridetail_id', $checkridetailids)->get();

				if(!$checkinvoices->isEmpty())
				{
					return redirect(Crypt::decrypt($setting->admin_url) . '/ri')->with('error-message', "You can't edit Recieve Item that already have the Invoice");
				}
			}

			$suppliers = Supplier::where('is_active', '=', true)->orderBy('name', 'asc')->get();
			$supplier_options[''] = 'Select Supplier';
			foreach ($suppliers as $supplier) {
				$supplier_options[$supplier->id] = $supplier->name;
			}
			$data['supplier_options'] = $supplier_options;

			$pos = Po::where('supplier_id', '=', $ri->supplier_id)->where('status', '=', 'Dikirim')->where(function($qr) use ($ri) {
				$qr->where('ri_status', '=', 'Belum Diterima');
				$qr->orWhere('ri_status', '=', 'Diterima Sebagian');
				$qr->orWhere('id', '=', $ri->po_id);
			})->orderBy('id', 'desc')->get();

			$data['pos'] = $pos;

			$po_options[''] = 'Select Purchase Order';
			foreach ($pos as $po) {
				$po_options[$po->id] = $po->no_nota;
			}
			$data['po_options'] = $po_options;

			$podetails = Podetail::where('po_id', '=', $ri->po_id)->get();
			$data['podetails'] = $podetails;

			$frees = Ridetail::where('product_id', '!=', 0)->get();
			$data['frees'] = $frees;

			// $getproducts = Ridetail::where('ri_id', '=', $id)->where('product_id', '!=', 0)->get();
			$getproducts = $frees;
			$data['ridetails'] = $getproducts;
			if(!$getproducts->isEmpty())
			{
				foreach ($getproducts as $getproduct) {
					Session::push('add_product', $getproduct->product_id);
				}

				$createsessions = Session::get('add_product');

				$products = Product::whereNotIn('id', Session::get('add_product'))->where('is_active', '=', true)->orderBy('name')->get();
			}
			else
			{
				$products = Product::where('is_active', '=', true)->orderBy('name')->get();
			}

			$product_options[''] = 'Select Product';
			foreach ($products as $product) {
				$product_options[$product->id] = $product->name;
			}
			$data['product_options'] = $product_options;

			$po = Po::find($ri->po_id);
			$data['po'] = $po;

			$podetails = Podetail::where('po_id', '=', $ri->po_id)->get();
			$data['podetails'] = $podetails;

			$data['request'] = $request;

	        return view('back.ri.edit', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/ri')->with('error-message', 'Can not find image with ID ' . $id);
		}
	}

	public function update(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;
		
		$inputs = $request->all();
		$rules = array(
			'no_nota'				=> 'required',
			'supplier'				=> 'required',
			'date'					=> 'required',
		);

		$validator = Validator::make($inputs, $rules);
		if ($validator->passes())
		{
			$ri = Ri::find($id);
			if ($ri != null)
			{
				$podetails = $request->input('podetail');
				$freedetails = $request->input('freedetail');

				/*Check maximum qty*/
				if($podetails != 0)
				{
					foreach ($podetails as $getid => $item) {
						if($item['product'] == 0)
						{
							if(($item['qty'] != null) AND ($item['qty'] > 0))
							{
								// dd($item['qty']);
								if($item['qty'] > $item['max'])
								{
									$product = Product::find($item['bahan']);

									$getmaxproducts[] = $product->name . " quantity must be less then or equal to " . $item['max'];
								}
							}
						}
					}

					if(isset($getmaxproducts))
					{
						return redirect(Crypt::decrypt($setting->admin_url) . '/ri/' . $ri->id . '/edit')->withInput()->withErrors($getmaxproducts);
					}

					if($request->input('purchase_order') != null)
					{
						$po = htmlspecialchars($request->input('purchase_order'));
						$date = htmlspecialchars($request->input('date'));
						$checkpo = Po::find($po);
						if(($date < $checkpo->date) OR ($date > date('Y-m-d')))
						{
							return redirect(Crypt::decrypt($setting->admin_url) . '/ri/' . $id . '/edit')->withInput()->withErrors("Recieve Item date must between Purchase Order date and today");
						}
					}
				}

				if($freedetails != null)
				{
					foreach ($freedetails as $getid => $item) {
						$product = Product::find($item['product']);

						if($item['rak'] == null)
						{
							$getmaxproducts[] = "Rak for $product->name is required";
						}
					}

					if(isset($getmaxproducts))
					{
						return redirect(Crypt::decrypt($setting->admin_url) . '/ri/' . $id . '/edit')->withInput()->withErrors($getmaxproducts);
					}
				}

				DB::transaction(function() use ($request, $podetails, $freedetails, $ri) {
					$ri->no_nota = htmlspecialchars($request->input('no_nota'));
					if($request->input('purchase_order') != null)
					{
						$ri->po_id = htmlspecialchars($request->input('purchase_order'));
					}
					else
					{
						$ri->po_id = 0;
					}
					// $ri->rak_id = htmlspecialchars($request->input('rak'));
					$ri->supplier_id = htmlspecialchars($request->input('supplier'));
					$ri->date = htmlspecialchars($request->input('date'));
					$ri->message = htmlspecialchars($request->input('msg'));

					$ri->update_id = Auth::user()->id;

					$ri->save();

					// $rakids = htmlspecialchars($request->input('rak'));
					

					/*Delete RI Detail*/
					$getridetails = Ridetail::where('ri_id', '=', $ri->id)->get();
					foreach ($getridetails as $getridetail) 
					{
						/*Delete Inventory then recalculate it*/
						if($getridetail->qty != 0)
						{
							$getinventory = Inventory::where('type', '=', 'Ri')->where('type_id', '=', $getridetail->id)->first();
							if($getinventory != null)
							{
								$inventoryproductstockid = $getinventory->productstock_id;
								$inventorydate = $getinventory->date;
								$inventoryid = $getinventory->id;
								$getinventory->delete();

								update_inventory($inventoryproductstockid, $inventorydate, $inventoryid);
							}
						}


						if($getridetail->product_id == 0)
						{
							$getproductstock = Productstock::where('product_id', '=', $getridetail->podetail->product_id)->where('rak_id', '=', $getridetail->rak_id)->first();
						}
						else
						{
							$getproductstock = Productstock::where('product_id', '=', $getridetail->product_id)->where('rak_id', '=', $getridetail->rak_id)->first();
						}
							
						if($getproductstock != null)
						{
							$getproductstock->stock = $getproductstock->stock - $getridetail->qty;
							$getproductstock->save();
						}
						
						$getridetail->delete();
					}


					$totalprice = 0;
					foreach ($podetails as $id => $item) {
						if(($item['qty'] != null) AND ($item['qty'] > 0))
						{
							/*Make New RI Detail*/
							$ridetail = new Ridetail;
							$ridetail->product_id = $item['product'];
							$ridetail->ri_id = $ri->id;
							$ridetail->rak_id = $item['rak'];
							if($request->input('purchase_order') != null)
							{
								$ridetail->podetail_id = $id;
							}
							else
							{
								$ridetail->podetail_id = 0;
							}
							$ridetail->qty = $item['qty'];
							// $ridetail->price = $product->price;
							$ridetail->save();

							/*Adding stock on product stock*/
							if($item['product'] == 0)
							{
								$checkproductstock = Productstock::where('product_id', '=', $ridetail->podetail->product_id)->where('rak_id', '=', $ridetail->rak_id)->first();
							}
							else
							{
								$checkproductstock = Productstock::where('product_id', '=', $ridetail->product_id)->where('rak_id', '=', $ridetail->rak_id)->first();
							}

							// if($checkproductstock != null)
							// {
								$productstock = $checkproductstock;
								$productstock->stock = $productstock->stock + $ridetail->qty;
							// }
							// else
							// {
							// 	$productstock = new Productstock;
							// 	$productstock->stock = $ridetail->qty;
							// }
							
							if($item['product'] == 0)
							{
								$productstock->product_id = $ridetail->podetail->product_id;
							}
							else
							{
								$productstock->product_id = $ridetail->product_id;
							}
							$productstock->rak_id = $ridetail->rak_id;
							$productstock->save();

							/*Adding Inventory Data*/
							if($ridetail->qty != 0)
							{
								/*Adding Inventory Data*/
								$inventory = new Inventory;
								$inventory->date = $ri->date;
								$inventory->productstock_id = $productstock->id;
								$inventory->type = 'Ri';
								$inventory->type_id = $ridetail->id;

								$getlastinv = Inventory::where('productstock_id', '=', $productstock->id)->where('date', '<=', $ri->date)->orderBy('date', 'desc')->orderBy('id', 'desc')->first();

								if($ridetail->product_id == 0)
								{
									if($getlastinv == null)
									{
										$inventory->qty_last = 0;
										$inventory->price_last = 0;

										$inventory->qty_z = $ridetail->qty;
										$inventory->price_z = $ridetail->podetail->price;
									}
									else
									{
										$inventory->qty_last = $getlastinv->qty_z;
										$inventory->price_last = $getlastinv->price_z;

										$inventory->qty_z = $getlastinv->qty_z + $ridetail->qty;
										$inventory->price_z = (($getlastinv->qty_z * $getlastinv->price_z) + ($ridetail->qty * $ridetail->podetail->price)) / ($getlastinv->qty_z + $ridetail->qty);
									}
									$inventory->real_price = $ridetail->podetail->price;
									$inventory->qty_in = $ridetail->qty;
									$inventory->price_in = $ridetail->podetail->price;
								}
								else
								{
									if($getlastinv == null)
									{
										$inventory->qty_last = 0;
										$inventory->price_last = 0;

										$inventory->qty_z = $ridetail->qty;
										$inventory->price_z = 0;
									}
									else
									{
										$inventory->qty_last = $getlastinv->qty_z;
										$inventory->price_last = $getlastinv->price_z;

										$inventory->qty_z = $getlastinv->qty_z + $ridetail->qty;
										$inventory->price_z = (($getlastinv->qty_z * $getlastinv->price_z) + ($ridetail->qty * 0)) / ($getlastinv->qty_z + $ridetail->qty);
									}
									$inventory->real_price = 0;
									$inventory->qty_in = $ridetail->qty;
									$inventory->price_in = 0;
								}

								$inventory->qty_out = 0;
								$inventory->price_out = 0;
								$inventory->save();

								$productstock = Productstock::find($inventory->productstock_id);
								$productstock->rak_id = $ridetail->rak_id;
								$productstock->stock = $inventory->qty_z;
								$productstock->save();

								$product = Product::find($productstock->product_id);
								$product->price = $inventory->price_z;
								$product->save();

								update_inventory($inventory->productstock_id, $inventory->date, $inventory->id);
							}

							/*counting RI Detail price*/
							if($item['product'] == 0)
							{
								$totalprice = $totalprice + ($ridetail->qty * $ridetail->podetail->price);
							}
						}
					}

					foreach ($freedetails as $id => $item) {
						if(($item['qty'] != null) AND ($item['qty'] > 0))
						{
							/*Make New RI Detail*/
							$ridetail = new Ridetail;
							$ridetail->product_id = $item['product'];
							$ridetail->ri_id = $ri->id;
							$ridetail->rak_id = $item['rak'];
							if($request->input('purchase_order') != null)
							{
								$ridetail->podetail_id = $id;
							}
							else
							{
								$ridetail->podetail_id = 0;
							}
							$ridetail->qty = $item['qty'];
							// $ridetail->price = $product->price;
							$ridetail->save();

							/*Adding stock on product stock*/
							if($item['product'] == 0)
							{
								$checkproductstock = Productstock::where('product_id', '=', $ridetail->podetail->product_id)->where('rak_id', '=', $ridetail->rak_id)->first();
								// dd("null");
							}
							else
							{
								// dd($ridetail->product_id . " - " . $ridetail->rak_id);
								$checkproductstock = Productstock::where('product_id', '=', $ridetail->product_id)->where('rak_id', '=', $ridetail->rak_id)->first();
							}

							// if($checkproductstock != null)
							// {
							// dd($checkproductstock);
								$productstock = $checkproductstock;
								$productstock->stock = $productstock->stock + $ridetail->qty;
							// }
							// else
							// {
							// 	$productstock = new Productstock;
							// 	$productstock->stock = $ridetail->qty;
							// }
							
							if($item['product'] == 0)
							{
								$productstock->product_id = $ridetail->podetail->product_id;
							}
							else
							{
								$productstock->product_id = $ridetail->product_id;
							}
							$productstock->rak_id = $ridetail->rak_id;
							$productstock->save();

							/*Adding Inventory Data*/
							if($ridetail->qty != 0)
							{
								/*Adding Inventory Data*/
								$inventory = new Inventory;
								$inventory->date = $ri->date;
								$inventory->productstock_id = $productstock->id;
								$inventory->type = 'Ri';
								$inventory->type_id = $ridetail->id;

								$getlastinv = Inventory::where('productstock_id', '=', $productstock->id)->where('date', '<=', $ri->date)->orderBy('date', 'desc')->orderBy('id', 'desc')->first();

								if($ridetail->product_id == 0)
								{
									if($getlastinv == null)
									{
										$inventory->qty_last = 0;
										$inventory->price_last = 0;

										$inventory->qty_z = $ridetail->qty;
										$inventory->price_z = $ridetail->podetail->price;
									}
									else
									{
										$inventory->qty_last = $getlastinv->qty_z;
										$inventory->price_last = $getlastinv->price_z;

										$inventory->qty_z = $getlastinv->qty_z + $ridetail->qty;
										$inventory->price_z = (($getlastinv->qty_z * $getlastinv->price_z) + ($ridetail->qty * $ridetail->podetail->price)) / ($getlastinv->qty_z + $ridetail->qty);
									}
									$inventory->real_price = $ridetail->podetail->price;
									$inventory->qty_in = $ridetail->qty;
									$inventory->price_in = $ridetail->podetail->price;
								}
								else
								{
									if($getlastinv == null)
									{
										$inventory->qty_last = 0;
										$inventory->price_last = 0;

										$inventory->qty_z = $ridetail->qty;
										$inventory->price_z = 0;
									}
									else
									{
										$inventory->qty_last = $getlastinv->qty_z;
										$inventory->price_last = $getlastinv->price_z;

										$inventory->qty_z = $getlastinv->qty_z + $ridetail->qty;
										$inventory->price_z = (($getlastinv->qty_z * $getlastinv->price_z) + ($ridetail->qty * 0)) / ($getlastinv->qty_z + $ridetail->qty);
									}
									$inventory->real_price = 0;
									$inventory->qty_in = $ridetail->qty;
									$inventory->price_in = 0;
								}

								$inventory->qty_out = 0;
								$inventory->price_out = 0;
								$inventory->save();

								$productstock = Productstock::find($inventory->productstock_id);
								$productstock->rak_id = $ridetail->rak_id;
								$productstock->stock = $inventory->qty_z;
								$productstock->save();

								$product = Product::find($productstock->product_id);
								$product->price = $inventory->price_z;
								$product->save();

								update_inventory($inventory->productstock_id, $inventory->date, $inventory->id);
							}

							/*counting RI Detail price*/
							if($item['product'] == 0)
							{
								$totalprice = $totalprice + ($ridetail->qty * $ridetail->podetail->price);
							}
						}
					}

					/*Adding data to Hutang Belum Tertagih*/
					$hbt = Hbt::where('ri_id', '=', $ri->id)->first();
					$hbt->ri_id = $ri->id;
					$hbt->amount = $totalprice;
					$hbt->save();

					if($request->input('purchase_order') != null)
					{
						update_po_status($ri->po_id);
					}
				});

				return redirect(Crypt::decrypt($setting->admin_url) . '/ri')->with('success-message', "Ri <strong>$ri->no_nota</strong> has been updated");
			}
			else
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/ri')->with('error-message', 'Can not find recieve item with ID ' . $id);
			}
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/ri/' . $id . '/edit')->withInput()->withErrors($validator);
		}
	}


	/* Delete a resource*/
	public function destroy(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->ri_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}
		if ($admingroup->ri_d != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}
		
		$ri = Ri::find($id);
		if ($ri != null)
		{
			if($ri->is_invoice == true)
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/ri')->with('error-message', 'You can not delete this Receive Item');
			}

			$return = Retur::where('ri_id', '=', $ri->id)->first();
			if($return != null)
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/ri')->with('error-message', 'You can not delete this Receive Item');
			}

			DB::transaction(function() use ($ri, $setting) 
			{
				$ridetails = Ridetail::where('ri_id', '=', $ri->id)->get();
				if (!$ridetails->isEmpty())
				{
					foreach ($ridetails as $ridetail) 
					{
						$ridetailids[] = $ridetail->id;
						/*Returning stock to before deleting RI*/
						// if($ridetail->product_id == 0)
						// {
						// 	$backproductstock = Productstock::where('product_id', '=', $ridetail->podetail->product_id)->where('rak_id', '=', $ri->po->rak_id)->first();
						// }
						// else
						// {
						// 	$backproductstock = Productstock::where('product_id', '=', $ridetail->product_id)->where('rak_id', '=', $ri->po->rak_id)->first();
						// }
						// $backproductstock->stock = $backproductstock->stock - $ridetail->qty;
						// $backproductstock->save();

						// $inventory = Inventory::where('type', '=', 'Ri')->where('type_id', '=', $ridetail->id)->first();
						// $inventory->delete();

						$getinventory = Inventory::where('type', '=', 'Ri')->where('type_id', '=', $ridetail->id)->first();
						$inventoryproductstockid = $getinventory->productstock_id;
						$inventorydate = $getinventory->date;
						$inventoryid = $getinventory->id;
						$getinventory->delete();

						$lastinventory = Inventory::where('productstock_id', '=', $inventoryproductstockid)->orderBy('date', 'desc')->orderBy('id', 'desc')->first();

						// dd($lastinventory->id);
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

						$podetailid = $ridetail->podetail_id;

						$ridetail->delete();
					}
				}

				$hbt = Hbt::where('ri_id', '=', $ri->id)->first();
				$hbt->delete();

				$poid = $ri->po_id;

				$ri->delete();

				if($ri->po_id != 0)
				{
					update_po_status($poid);
				}
			});

 			return redirect(Crypt::decrypt($setting->admin_url) . '/ri')->with('success-message', "Ri <strong>" . $ri->no_nota . "</strong> has been deleted");
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/ri')->with('error-message', 'Can not find ri with ID ' . $id);
		}
	}

	public function getFindPo(Request $request, $supplier_id)
	{
		$pos = Po::where('supplier_id', '=', $supplier_id)->where('status', '=', 'Dikirim')->where(function($qr){
			$qr->where('ri_status', '=', 'Belum Diterima');
			$qr->orWhere('ri_status', '=', 'Diterima Sebagian');
		})->orderBy('id', 'desc')->get();

		$po_options[''] = 'Select Purchase Order';
		foreach ($pos as $po) {
			$po_options[$po->id] = $po->no_nota;
		}
		$data['po_options'] = $po_options;

		return view('back.ri.poajax', $data);
	}

	public function getPodetail(Request $request, $id)
	{
		$po = Po::find($id);
		$data['po'] = $po;

		$podetails = Podetail::where('po_id', '=', $id)->get();
		$data['podetails'] = $podetails;

		return view('back.ri.podetail', $data);
	}

	public function getPo(Request $request, $date)
	{
		$pos = Po::where('date', '=', $date)->where('status', '=', 'Dikirim')->orderBy('id', 'desc')->get();
		$data['pos'] = $pos;

		$po_options[''] = 'Select Purchase Order';
		foreach ($pos as $po) {
			$po_options[$po->id] = $po->no_nota;
		}
		$data['po_options'] = $po_options;

		return view('back.ri.po', $data);
	}



	public function getReplace()
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		// dd(Session::get('add_product'));

		// dd(Session::get('add_product'));
		// $getproductids[] = Session::get('add_product');
		// if(count(Session::get('add_product')) == 1)
		// {
		// 	// return "done";
		// 	$productids = $getproductids;
		// 	$products = Product::whereNotIn('id', $productids)->where('is_active', '=', true)->get();
		// }
		// else
		// {
			// return "done 1";
			$products = Product::whereNotIn('id', Session::get('add_product'))->where('is_active', '=', true)->orderBy('name')->get();
		// }
		// dd($products);
		// if($getproducts->isEmpty())
		// {
		// 	$products = Product::where('is_active', '=', true)->get();
		// }
		// else
		// {
		// 	$products = Product::where('is_active', '=', true)->get();
		// }

		$product_options[] = 'Select Product';
		foreach ($products as $product) {
			$product_options[$product->id] = $product->name;
		}
		$data['product_options'] = $product_options;

		return view('back.ri.replace', $data);
	}

	public function getAdd($id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;
		$data['dataid'] = $id;

		// dd(Session::get('add_product'));

		$product = Product::find($id);
		$data['product'] = $product;

		// if(Session::has('add_product'))
		// {
			// return "gak cocok";
			// $ids = array(
				// Session::get('add_product'),
				// $id,
			// );

			// dd($ids);
			Session::push('add_product', $id);
			// Session::push(key, value)
		// }
		// else
		// {
			// return "cocok";
			// Session::put('add_product', $id);
		// }

		return view('back.ri.search', $data);
	}

	public function getDrop($id)
	{
		$getsessions = Session::get('add_product');
		foreach ($getsessions as $getsession) {
			$getsessionids[] = $getsession;
		}
		// dd($getsessions);
		$removedatas = array_diff($getsessionids, array($id));
		// dd($removedata);
		Session::forget('add_product');
		foreach($removedatas as $removedata)
		{
			Session::push('add_product', $removedata);
		}
		// dd(Session::get('add_product'));

		if(count($removedatas) == 0)
		{
			$products = Product::where('is_active', '=', true)->orderBy('name')->get();
		}
		else
		{
			$products = Product::whereNotIn('id', $removedatas)->where('is_active', '=', true)->orderBy('name')->get();
		}
		$product_options[] = 'Select Product';
		foreach ($products as $product) {
			$product_options[$product->id] = $product->name;
		}
		$data['product_options'] = $product_options;

		return view('back.ri.replace', $data);
	}

	public function getPrint($id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		$ri = ri::find($id);
		if($ri != null)
		{
			$data['ri'] = $ri;

			$ridetails = Ridetail::where('ri_id', '=', $ri->id)->where('product_id', '=', 0)->get();
			$data['ridetails'] = $ridetails;

			$frees = Ridetail::where('ri_id', '=', $ri->id)->where('product_id', '!=', 0)->get();
			$data['frees'] = $frees;

			$supplier = Supplier::find($ri->po->supplier_id);
			$data['supplier'] = $supplier;

			return view('back.ri.print', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/ri')->with('error-message', 'Can not find Purchase Order with ID ' . $id);
		}
	}
}