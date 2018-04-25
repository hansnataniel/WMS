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
use App\Models\Transaction;
use App\Models\Transactiondetail;
use App\Models\Po;
use App\Models\Podetail;
use App\Models\Treturn;
use App\Models\Treturndetail;
use App\Models\Customer;
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


class TransactionreturnController extends Controller
{
	/* Get the list of the resource*/
	public function index(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/
		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->treturn_r != true)
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
		
		$query = Treturn::query();

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

		$customers = Customer::where('is_active', '=', true)->orderBy('name')->get();
		$customer_options[''] = 'Select Customer';
		foreach ($customers as $customer) {
			$customer_options[$customer->id] = $customer->name;
		}
		$data['customer_options'] = $customer_options;

        return view('back.treturn.index', $data);
	}

	/* Create a return resource*/
	public function create(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->treturn_c != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/treturn')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		/*Menu Authentication*/

		$data['navModul'] = true;
		$data['helpModul'] = false;
		$data['searchModul'] = false;
		$data['alertModul'] = true;
		$data['messageModul'] = true;
		
		$data['request'] = $request;
		
		$return = new Treturn;
		$data['return'] = $return;

		$customers = Customer::where('is_active', '=', true)->orderBy('name')->get();
		$customer_options[''] = 'Select Customer';
		foreach ($customers as $customer) {
			$customer_options[$customer->id] = $customer->name;
		}
		$data['customer_options'] = $customer_options;

		Session::forget('add_product');
		Session::forget('returnid');

        return view('back.treturn.create', $data);
	}

	public function store(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;
		
		$inputs = $request->all();
		$rules = array(
			'no_nota'	=> 'required',
			'customer'	=> 'required',
			'transaction'	=> 'required',
			'date'		=> 'required',
		);

		$validator = Validator::make($inputs, $rules);
		if ($validator->passes())
		{
			$transactiondetails = $request->input('transactiondetail');

			if($transactiondetails == 0)
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/treturn/create')->withInput()->with('error-message', "Please select the product first");
			}

			/*Check maximum qty*/
			foreach ($transactiondetails as $id => $item) {
				if(($item['qty'] != null) AND ($item['qty'] > 0))
				{
					/*Get Maximum Qty*/
					$total = 0;
					$transactiondetailget = Transactiondetail::find($id);
					$treturndetails = Treturndetail::where('transactiondetail_id', '=', $id)->get();
					foreach ($treturndetails as $treturndetail) {
						$total = $total + $treturndetail->qty;
					}

					if(!$treturndetails->isEmpty())
					{
						$max = $transactiondetailget->qty - $total;
					}
					else
					{
						$max = $transactiondetailget->qty;
					}


					if($item['qty'] > $max)
					{
						$product = Product::find($id);

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
				return redirect(Crypt::decrypt($setting->admin_url) . '/treturn/create')->withInput()->withErrors($getmaxproducts);
			}


			$transaction = htmlspecialchars($request->input('transaction'));
			$date = htmlspecialchars($request->input('date'));
			$checktransaction = Transaction::find($transaction);
			// dd($transaction);
			if(($date < $checktransaction->date) OR ($date > date('Y-m-d')))
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/treturn/create')->withInput()->with('error-message', "You can only setting Return date range from Recieve Item date until today");
			}

			DB::transaction(function() use ($transactiondetails, $request) 
			{
				global $return;

				$return = new Treturn;
				$return->no_nota = htmlspecialchars($request->input('no_nota'));
				$return->transaction_id = htmlspecialchars($request->input('transaction'));
				$return->customer_id = htmlspecialchars($request->input('customer'));
				$return->date = htmlspecialchars($request->input('date'));
				$return->message = htmlspecialchars($request->input('msg'));

				$return->create_id = Auth::user()->id;
				$return->update_id = Auth::user()->id;

				$return->save();

				$totalprice = 0;
				foreach ($transactiondetails as $id => $item) {
					if(($item['qty'] != null) AND ($item['qty'] > 0))
					{
						$treturndetail = new Treturndetail;
						$treturndetail->treturn_id = $return->id;
						$treturndetail->transactiondetail_id = $id;
						$treturndetail->qty = $item['qty'];
						$treturndetail->price = $item['price'];
						$treturndetail->save();

						/*Adding stock on product stock*/
						$productstock = Productstock::where('product_id', '=', $treturndetail->transactiondetail->product_id)->where('rak_id', '=', $treturndetail->transactiondetail->rak_id)->first();

						$productstock->product_id = $treturndetail->transactiondetail->product_id;
						
						$productstock->stock = $productstock->stock + $treturndetail->qty;
						// $productstock->rak_id = $treturndetail->transactiondetail->rak_id;
						$productstock->save();

						if($treturndetail->qty != 0)
						{
							/*Adding Inventory Data*/
							$inventory = new Inventory;
							$inventory->date = $return->date;
							$inventory->productstock_id = $productstock->id;
							$inventory->type = 'TR';
							$inventory->type_id = $treturndetail->id;

							$inventory->real_price = $treturndetail->price;

							$getlastinv = Inventory::where('productstock_id', '=', $productstock->id)->where('date', '<=', $return->date)->orderBy('date', 'desc')->orderBy('id', 'desc')->first();

							$inventory->qty_last = $getlastinv->qty_z;
							$inventory->price_last = $getlastinv->price_z;

							$inventory->qty_in = $treturndetail->qty;
							$inventory->price_in = $treturndetail->price;

							$inventory->qty_out = 0;
							$inventory->price_out = 0;

							$inventory->qty_z = $getlastinv->qty_z + $treturndetail->qty;
							if($getlastinv->qty_z - $treturndetail->qty == 0)
							{
								$inventory->price_z = 0;
							}
							else
							{
								$inventory->price_z = (($getlastinv->qty_z * $getlastinv->price_z) + ($treturndetail->qty * $treturndetail->price)) / ($getlastinv->qty_z + $treturndetail->qty);
							}
							$inventory->save();

							if($inventory->real_price != $inventory->price_out)
							{
								$pricegap = new Pricegap;
								$pricegap->date = $return->date;
								$pricegap->returndetail_id = $treturndetail->id;
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
			if ($admingroup->treturn_r != true)
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('success-message', "Return <strong>$return->no_nota</strong> has been created");
			}
			return redirect(Crypt::decrypt($setting->admin_url) . '/treturn')->with('success-message', "Return <strong>$return->no_nota</strong> has been created");
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/treturn/create')->withInput()->withErrors($validator);
		}
	}

	/* Show a resource*/
	public function show(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->treturn_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/treturn')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		/*Menu Authentication*/

		$data['navModul'] = true;
		$data['helpModul'] = true;
		$data['searchModul'] = false;
		$data['alertModul'] = true;
		$data['messageModul'] = true;
		
		
		$return = Treturn::find($id);
		if ($return != null)
		{
			$data['return'] = $return;
			$data['request'] = $request;
			
	        return view('back.treturn.view', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/treturn')->with('error-message', 'Can not find return with ID ' . $id);
		}
	}

	/* Edit a resource*/
	public function edit(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/
		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->treturn_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}
		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->treturn_u != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/treturn')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		/*Menu Authentication*/

		$data['navModul'] = true;
		$data['helpModul'] = false;
		$data['searchModul'] = false;
		$data['alertModul'] = true;
		$data['messageModul'] = true;
		
		
		$return = Treturn::find($id);
		
		if ($return != null)
		{
			$data['return'] = $return;

			Session::forget('add_product');
			Session::forget('returnid');

			Session::put('returnid', $return->id);

			$treturndetails = Treturndetail::where('treturn_id', '=', $return->id)->get();
			foreach ($treturndetails as $treturndetail) {
				Session::push('add_product', $treturndetail->transactiondetail_id);
			}

			/*GET Customer*/
			$customers = Customer::where('is_active', '=', true)->orderBy('name')->get();
			$customer_options[''] = 'Select Customer';
			foreach ($customers as $customer) {
				$customer_options[$customer->id] = $customer->name;
			}
			$data['customer_options'] = $customer_options;


			$transactions = Transaction::where('customer_id', '=', $return->customer_id)->orderBy('date', 'desc')->get();
			$transaction_options[''] = 'Select Transaction';
			foreach ($transactions as $transaction) {
				$transaction_options[$transaction->id] = $transaction->trans_id;
			}
			$data['transaction_options'] = $transaction_options;


			/*Get Product*/
			$transactiondetails = Transactiondetail::where('transaction_id', '=', $return->transaction_id)->whereNotIn('id', Session::get('add_product'))->get();
			$data['transactiondetails'] = $transactiondetails;

			$data['request'] = $request;

	        return view('back.treturn.edit', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/treturn')->with('error-message', 'Can not find image with ID ' . $id);
		}
	}

	public function update(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;
		
		$inputs = $request->all();
		$rules = array(
			'no_nota'	=> 'required',
			'customer'	=> 'required',
			'transaction'	=> 'required',
			'date'		=> 'required',
		);

		$validator = Validator::make($inputs, $rules);
		if ($validator->passes())
		{
			$return = Treturn::find($id);
			if ($return != null)
			{
				$transactiondetails = $request->input('transactiondetail');

				if($transactiondetails == 0)
				{
					return redirect(Crypt::decrypt($setting->admin_url) . '/treturn/' . $id . '/edit')->withInput()->with('error-message', "Please select the product first");
				}

				/*Check maximum qty*/
				foreach ($transactiondetails as $getid => $item) {
					if(($item['qty'] != null) AND ($item['qty'] > 0))
					{
						/*Get Maximum Qty*/
						$total = 0;
						$transactiondetailget = Transactiondetail::find($getid);
						$treturndetails = Treturndetail::where('transactiondetail_id', '=', $getid)->get();
						foreach ($treturndetails as $treturndetail) {
							$total = $total + $treturndetail->qty;
						}

						if($treturndetails->isEmpty())
						{
							$max = $transactiondetailget->qty;
						}
						else
						{
							$treturndetailget = Treturndetail::where('transactiondetail_id', '=', $getid)->where('treturn_id', '=', $return->id)->first();
							$max = ($transactiondetailget->qty - $total) + $treturndetailget->qty;
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
					return redirect(Crypt::decrypt($setting->admin_url) . '/treturn/' . $id . '/edit')->withInput()->withErrors($getmaxproducts);
				}



				$transaction = htmlspecialchars($request->input('transaction'));
				$date = htmlspecialchars($request->input('date'));
				$checktransaction = Transaction::find($transaction);
				// dd($checktransaction);
				if(($date < $checktransaction->date) OR ($date > date('Y-m-d')))
				{
					return redirect(Crypt::decrypt($setting->admin_url) . '/treturn/' . $id . '/edit')->withInput()->with('error-message', "You can only setting Return date range from Recieve Item date until today");
				}



				DB::transaction(function() use ($request, $transactiondetails, $return) 
				{
					$return->no_nota = htmlspecialchars($request->input('no_nota'));
					$return->transaction_id = htmlspecialchars($request->input('transaction'));
					$return->customer_id = htmlspecialchars($request->input('customer'));
					$return->date = htmlspecialchars($request->input('date'));
					$return->message = htmlspecialchars($request->input('msg'));
					$return->save();
					

					/*Delete Return Detail*/
					$getreturndetails = Treturndetail::where('treturn_id', '=', $return->id)->get();
					foreach ($getreturndetails as $getreturndetail) 
					{
						/*Delete Inventory then recalculate it*/
						$getinventory = Inventory::where('type', '=', 'TR')->where('type_id', '=', $getreturndetail->id)->first();
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
					foreach ($transactiondetails as $id => $item) {
						if(($item['qty'] != null) AND ($item['qty'] >= 0))
						{
							$treturndetail = new Treturndetail;
							$treturndetail->treturn_id = $return->id;
							$treturndetail->transactiondetail_id = $id;
							$treturndetail->qty = $item['qty'];
							$treturndetail->price = $item['price'];
							$treturndetail->save();

							/*Adding stock on product stock*/
							
							$productstock = Productstock::where('product_id', '=', $treturndetail->transactiondetail->product_id)->where('rak_id', '=', $treturndetail->transactiondetail->rak_id)->first();

							$productstock->product_id = $treturndetail->transactiondetail->product_id;
							
							$productstock->stock = $productstock->stock + $treturndetail->qty;
							// $productstock->rak_id = $treturndetail->transactiondetail->rak_id;
							$productstock->save();

							if($treturndetail->qty != 0)
							{
								/*Adding Inventory Data*/
								$inventory = new Inventory;
								$inventory->date = $return->date;							
								$inventory->productstock_id = $productstock->id;							
								$inventory->type = 'TR';
								$inventory->type_id = $treturndetail->id;

								$inventory->real_price = $treturndetail->price;

								$getlastinv = Inventory::where('productstock_id', '=', $productstock->id)->where('date', '<=', $return->date)->orderBy('date', 'desc')->orderBy('id', 'desc')->first();

								$inventory->qty_last = $getlastinv->qty_z;
								$inventory->price_last = $getlastinv->price_z;

								$inventory->qty_in = $treturndetail->qty;
								$inventory->price_in = $treturndetail->price;

								$inventory->qty_out = 0;
								$inventory->price_out = 0;

								$inventory->qty_z = $getlastinv->qty_z + $treturndetail->qty;
								if($getlastinv->qty_z - $treturndetail->qty == 0)
								{
									$inventory->price_z = 0;
								}
								else
								{
									$inventory->price_z = (($getlastinv->qty_z * $getlastinv->price_z) + ($treturndetail->qty * $treturndetail->price)) / ($getlastinv->qty_z + $treturndetail->qty);
								}
								$inventory->save();

								if($inventory->real_price != $inventory->price_out)
								{
									$pricegap = new Pricegap;
									$pricegap->date = $return->date;
									$pricegap->returndetail_id = $treturndetail->id;
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

				return redirect(Crypt::decrypt($setting->admin_url) . '/treturn')->with('success-message', "Return <strong>$return->no_nota</strong> has been updated");
			}
			else
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/treturn')->with('error-message', 'Can not find image with ID ' . $id);
			}
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/treturn/' . $id . '/edit')->withInput()->withErrors($validator);
		}
	}


	/* Delete a resource*/
	public function destroy(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->treturn_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}
		if ($admingroup->treturn_d != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}
		
		$return = Treturn::find($id);
		if ($return != null)
		{
			DB::transaction(function() use ($return, $setting) 
			{
				$treturndetails = Treturndetail::where('treturn_id', '=', $return->id)->get();
				if (count($treturndetails) != 0)
				{
					foreach ($treturndetails as $treturndetail) 
					{
						$getinventory = Inventory::where('type', '=', 'TR')->where('type_id', '=', $treturndetail->id)->first();
						$inventoryproductstockid = $getinventory->productstock_id;
						$inventorydate = $getinventory->date;
						$inventoryid = $getinventory->id;
						$getinventory->delete();

						$pricegap = Pricegap::where('returndetail_id', '=', $treturndetail->id)->first();
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

						$treturndetail->delete();
					}
				}

				$return->delete();
			});

 			return redirect(Crypt::decrypt($setting->admin_url) . '/treturn')->with('success-message', "Return <strong>$return->no_nota</strong> has been deleted");
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/treturn')->with('error-message', 'Can not find return with ID ' . $id);
		}
	}

	public function getRi(Request $request, $customer)
	{
		$transactions = Transaction::where('customer_id', '=', $customer)->orderBy('date', 'desc')->orderBy('id', 'desc')->get();
		$transaction_options[''] = 'Select Transaction';
		foreach ($transactions as $transaction) {
			$transaction_options[$transaction->id] = $transaction->trans_id;
		}
		$data['transaction_options'] = $transaction_options;

		return view('back.treturn.ri', $data);
	}

	public function getProduct(Request $request, $id)
	{
		$transaction = Transaction::find($id);
		$data['transaction'] = $transaction;

		$transactiondetails = Transactiondetail::where('transaction_id', '=', $id)->get();
		$data['transactiondetails'] = $transactiondetails;

		return view('back.treturn.product', $data);
	}

	public function getReplace(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		$transactiondetails = Transactiondetail::where('transaction_id', '=', $id)->whereNotIn('id', Session::get('add_product'))->get();
		$data['transactiondetails'] = $transactiondetails;

		return view('back.treturn.replace', $data);
	}

	// public function getAdd(Request $request, $id)
	// {
	// 	$setting = Setting::first();
	// 	$data['setting'] = $setting;
	// 	$data['dataid'] = $id;

	// 	$transactiondetail = Transactiondetail::find($id);
	// 	$data['transactiondetail'] = $transactiondetail;

	// 	Session::push('add_product', $id);
			
	// 	return view('back.treturn.add', $data);
	// }

	// public function getAddedit(Request $request, $id, $treturndetail_id)
	// {
	// 	$setting = Setting::first();
	// 	$data['setting'] = $setting;
	// 	$data['dataid'] = $id;

	// 	$transactiondetail = Transactiondetail::find($id);
	// 	$data['transactiondetail'] = $transactiondetail;

	// 	Session::push('add_product', $id);

	// 	return view('back.treturn.addedit', $data);
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

	// 	return view('back.treturn.replace', $data);
	// }

	public function getAdd($id, $transactiondetailid = null, $qty = null, $price = null)
	{
		if($transactiondetailid != null)
		{
			// dd($id . " - " . $transactiondetailid . " - " . $qty . " - " . $price);
			$data['transactiondetailid'] = $transactiondetailid;
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

		$transactiondetails = Transactiondetail::where('transaction_id', '=', $id)->get();
		$data['transactiondetails'] = $transactiondetails;

		return view('back.treturn.form', $data);
	}

	public function getForm($transactiondetailid, $productid, $qty, $price)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		$transactiondetail = Transactiondetail::find($transactiondetailid);
		$data['transactiondetail'] = $transactiondetail;

		Session::push('add_product', $productid);

		$product = Product::find($productid);

		// dd($transactiondetailid);
		// dd($transactiondetail->id);
		// dd($transactiondetail->transaction_id);
		// dd($product->id);
		$data['product'] = $product;
		$data['qty'] = $qty;
		$data['price'] = $price;

		$subtotal = $qty * $price;
		$data['subtotal'] = $subtotal;

		return view('back.treturn.search', $data);
	}



	public function getDrop(Request $request, $id)
	{
		$gettransactiondetail = Transactiondetail::find($id);

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
			$transactiondetails = Transactiondetail::where('transaction_id', '=', $gettransactiondetail->transaction_id)->get();
		}
		else
		{
			$transactiondetails = Transactiondetail::where('transaction_id', '=', $gettransactiondetail->transaction_id)->whereNotIn('id', $removedatas)->get();
		}

		$data['transactiondetails'] = $transactiondetails;

		return view('back.treturn.replace', $data);
	}

	public function getPrint(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		$treturn = Treturn::find($id);
		if($treturn != null)
		{
			$data['treturn'] = $treturn;

			$customer = Customer::find($treturn->customer_id);
			$data['customer'] = $customer;

			$treturndetails = Treturndetail::where('treturn_id', '=', $treturn->id)->get();
			$data['treturndetails'] = $treturndetails;

			return view('back.treturn.print', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/treturn')->with('error-message', 'Can not find Return with ID ' . $id);
		}
	}

	public function getPdf(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		$return = Treturn::find($id);
		if($return != null)
		{
			$data['return'] = $return;

			// $rak = Rak::find($return->ri->po->rak_id);
			// $data['rak'] = $rak;

			$customer = Customer::find($return->ri->po->customer_id);
			$data['customer'] = $customer;

			$html = \view('back.treturn.print', $data);
		
			// $pdf = App::make('dompdf.wrapper');
			$pdf = PDF::loadHTML($html);
			return $pdf->setPaper('a4', 'portrait')->stream();

			// return view('back.treturn.print', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/treturn')->with('error-message', 'Can not find Return with ID ' . $id);
		}
	}
}