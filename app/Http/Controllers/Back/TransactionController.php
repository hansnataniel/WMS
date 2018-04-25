<?php

/*
	Use Customer_id Space Here
*/
namespace App\Http\Controllers\Back;

/*
	Call Model Here
*/
use App\Models\Setting;
use App\Models\Admingroup;
use App\Models\Que;
use App\Models\Customer;

use App\Models\Rak;
use App\Models\Transaction;
use App\Models\Transactiondetail;
use App\Models\Product;
use App\Models\Productstock;
use App\Models\Inventory;


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


class TransactionController extends Controller
{
	/* Get the list of the resource*/
	public function index(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/
		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->transaction_r != true)
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
		
		$query = Transaction::query();

		$trans_id = htmlspecialchars($request->input('src_trans_id'));
		if ($trans_id != null)
		{
			$query->where('trans_id', 'LIKE', '%' . $trans_id . '%');
			$data['criteria']['src_trans_id'] = $trans_id;
		}

		$customer_id = htmlspecialchars($request->input('src_customer_id'));
		if ($customer_id != null)
		{
			$query->where('customer_id', '=', $customer_id);
			$data['criteria']['src_customer_id'] = $customer_id;
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
		$transactions = $query->paginate($per_page);
		$data['transactions'] = $transactions;

		$request->flash();

		$customers = Customer::where('is_active', '=', true)->orderBy('customer_id')->get();
		if($customers->isEmpty())
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/customer/create')->with('error-message', "Your customer is Empty, Please create it first");
		}

		$customer_options[''] = "Select Customer";
		foreach ($customers as $customer) {
			$customer_options[$customer->id] = $customer->customer_id;
		}
		$data['customer_options'] = $customer_options;

		$request->session()->put('last_url', URL::full());

		$data['request'] = $request;

        return view('back.transaction.index', $data);
	}

	/* Create a po resource*/
	public function create(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->transaction_c != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/transaction')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		/*Menu Authentication*/

		$data['navModul'] = true;
		$data['helpModul'] = true;
		$data['searchModul'] = false;
		$data['alertModul'] = true;
		$data['messageModul'] = true;
		
		$data['request'] = $request;
		
		$transaction = new Transaction;
		$data['transaction'] = $transaction;

		$customers = Customer::where('is_active', '=', true)->orderBy('name')->get();
		if($customers->isEmpty())
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/customer/create')->with('error-message', "Your customer is Empty, Please create it first");
		}

		$customer_options[''] = "Select customer";
		foreach ($customers as $customer) {
			$customer_options[$customer->id] = $customer->name;
		}
		$data['customer_options'] = $customer_options;

		$products = Product::where('is_active', '=', true)->orderBy('name')->get();
		if($products->isEmpty())
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/product/create')->with('error-message', "Your product is Empty, Please create it first");
		}

		$data['products'] = $products;

		$product_options[''] = "Select Product";
		foreach ($products as $product) {
			$product_options[$product->id] = $product->name;
		}
		$data['product_options'] = $product_options;

		Session::forget('add_product');
		Session::forget('save-pay');

        return view('back.transaction.create', $data);
	}

	public function store(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;
		
		$inputs = $request->all();
		$rules = array(
			'trans_id'				=> 'required|unique:transactions,trans_id',
			'customer'				=> 'required',
			'product'				=> 'required',
			'date'				=> 'required',
		);

		$validator = Validator::make($inputs, $rules);
		if ($validator->passes())
		{
			$products = $request->input('product');

			if($products == 0)
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/transaction/create')->withInput()->with('error-message', "Please select the product first");
			}

			DB::transaction(function() use ($products, $request) {
				global $transaction;

				$transaction = new Transaction;
				$transaction->trans_id = htmlspecialchars($request->input('trans_id'));
				$transaction->customer_id = htmlspecialchars($request->input('customer'));
				$transaction->date = htmlspecialchars($request->input('date'));
				$transaction->discounttype = htmlspecialchars($request->input('globaldiscounttype'));
				$transaction->discount = htmlspecialchars($request->input('globaldiscount'));
				
				$transaction->total = htmlspecialchars($request->input('total'));
				$transaction->amount_to_pay = htmlspecialchars($request->input('amount_to_pay'));
				$transaction->message = htmlspecialchars($request->input('msg'));

				$transaction->status = 'Waiting for Payment';

				$transaction->create_id = Auth::user()->id;
				$transaction->update_id = Auth::user()->id;
				
				$transaction->save();

				foreach ($products as $id => $item) {
					$product = Product::find($id);

					if(($item['qty'] != null) AND ($item['qty'] != 0) AND ($item['qty'] > 0))
					{
						$transactiondetail = new Transactiondetail;
						$transactiondetail->transaction_id = $transaction->id;
						$transactiondetail->product_id = $id;
						$transactiondetail->rak_id = $item['rak'];
						$transactiondetail->qty = $item['qty'];
						$transactiondetail->price = $item['price'];
						$transactiondetail->discounttype = $item['discounttype'];
						$transactiondetail->discount = $item['discount'];
						$transactiondetail->save();


						/*Adding stock on product stock*/
						$productstock = Productstock::where('product_id', '=', $transactiondetail->product_id)->where('rak_id', '=', $transactiondetail->rak_id)->first();
						$productstock->stock = $productstock->stock - $transactiondetail->qty;
						$productstock->save();


						/*Adding Inventory Data*/
						$inventory = new Inventory;
						$inventory->date = $transaction->date;							
						$inventory->productstock_id = $productstock->id;							
						$inventory->type = 'S';
						$inventory->type_id = $transactiondetail->id;

						$getlastinv = Inventory::where('productstock_id', '=', $productstock->id)->where('date', '<=', $transaction->date)->orderBy('date', 'desc')->orderBy('id', 'desc')->first();

						$inventory->qty_last = $getlastinv->qty_z;
						$inventory->price_last = $getlastinv->price_z;

						$inventory->qty_z = $getlastinv->qty_z - $transactiondetail->qty;
						if($getlastinv->qty_z - $transactiondetail->qty == 0)
						{
							$inventory->price_z = 0;
						}
						else
						{
							$inventory->price_z = $getlastinv->price_z;
						}
					
						$inventory->real_price = $getlastinv->price_z;

						$inventory->qty_out = $transactiondetail->qty;
						$inventory->price_out = $getlastinv->price_z;

						$inventory->qty_in = 0;
						$inventory->price_in = 0;
						$inventory->save();

						$lastinventory = Inventory::where('productstock_id', '=', $inventory->productstock_id)->orderBy('date', 'desc')->orderBy('id', 'desc')->first();

						$productstock = Productstock::find($lastinventory->productstock_id);
						$productstock->stock = $lastinventory->qty_z;
						$productstock->save();

						update_inventory($inventory->productstock_id, $inventory->date, $inventory->id);
					}
				}
			});

			global $transaction;


			if (Session::has('save-pay'))
			{
				Session::put('save-pay', $transaction->id);
				
				return redirect(Crypt::decrypt($setting->admin_url) . '/tpayment/create')->with('success-message', "Transaction <strong>$transaction->trans_id</strong> has been created");
			}
			else
			{
				$admingroup = Admingroup::find(Auth::user()->admingroup_id);
				if ($admingroup->transaction_r != true)
				{
					return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('success-message', "Transaction <strong>$transaction->trans_id</strong> has been created");
				}
				else
				{
					return redirect(Crypt::decrypt($setting->admin_url) . '/transaction')->with('success-message', "Transaction <strong>$transaction->trans_id</strong> has been created");
				}
			}
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/transaction/create')->withInput()->withErrors($validator);
		}
	}

	/* Show a resource*/
	public function show(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->transaction_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/transaction')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		/*Menu Authentication*/

		$data['navModul'] = true;
		$data['helpModul'] = true;
		$data['searchModul'] = false;
		$data['alertModul'] = true;
		$data['messageModul'] = true;
		
		
		$transaction = Transaction::find($id);
		if ($transaction != null)
		{
			$data['transaction'] = $transaction;
			$data['request'] = $request;
			
	        return view('back.transaction.view', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/transaction')->with('error-message', 'Can not find po with ID ' . $id);
		}
	}

	/* Edit a resource*/
	public function edit(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/
		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->transaction_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}
		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->transaction_u != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/transaction')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		/*Menu Authentication*/

		$data['navModul'] = true;
		$data['helpModul'] = true;
		$data['searchModul'] = false;
		$data['alertModul'] = true;
		$data['messageModul'] = true;
		
		
		$transaction = Transaction::find($id);
		
		if ($transaction != null)
		{
			if($transaction->status != 'Waiting for Payment')
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/transaction')->with('error-message', "You can only edit data when the status is 'Waiting for Payment'");
			}

			Session::forget('add_product');

			$data['transaction'] = $transaction;

			$customers = Customer::where('is_active', '=', true)->orderBy('name')->get();
			if($customers->isEmpty())
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/customer/create')->with('error-message', "Your customer is Empty, Please create it first");
			}

			$customer_options[''] = "Select customer";
			foreach ($customers as $customer) {
				$customer_options[$customer->id] = $customer->name;
			}
			$data['customer_options'] = $customer_options;

			$getproducts = Transactiondetail::where('transaction_id', '=', $id)->get();
			$data['transactiondetails'] = $getproducts;

			foreach ($getproducts as $getproduct) {
				Session::push('add_product', $getproduct->product_id);
			}

			$createsessions = Session::get('add_product');
			// dd($createsessions);

			$products = Product::whereNotIn('id', $createsessions)->where('is_active', '=', true)->orderBy('name')->get();
			$data['products'] = $products;

			$product_options[''] = 'Select Product';
			foreach ($products as $product) {
				$product_options[$product->id] = $product->name;
			}
			$data['product_options'] = $product_options;

			$transactiondetails = Transactiondetail::where('transaction_id', '=', $id)->get();
			$data['transactiondetails'] = $transactiondetails;

			$data['request'] = $request;

			Session::forget('save-pay');

	        return view('back.transaction.edit', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/transaction')->with('error-message', 'Can not find image with ID ' . $id);
		}
	}

	public function update(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;
		
		$inputs = $request->all();
		$rules = array(
			// 'trans_id'				=> 'required|unique:transactions,trans_id',
			'customer'				=> 'required',
			'product'				=> 'required',
			'date'				=> 'required',
		);

		$validator = Validator::make($inputs, $rules);
		if ($validator->passes())
		{
			$transaction = Transaction::find($id);
			if ($transaction != null)
			{
				$products = $request->input('product');

				if($products == 0)
				{
					return redirect(Crypt::decrypt($setting->admin_url) . '/transaction/create')->withInput()->with('error-message', "Please select the product first");
				}


				DB::transaction(function() use ($products, $transaction, $request) {
					// $transaction->trans_id = htmlspecialchars($request->input('trans_id'));
					$transaction->customer_id = htmlspecialchars($request->input('customer'));
					$transaction->date = htmlspecialchars($request->input('date'));
					$transaction->discounttype = htmlspecialchars($request->input('globaldiscounttype'));
					$transaction->discount = htmlspecialchars($request->input('globaldiscount'));
					
					$transaction->total = htmlspecialchars($request->input('total'));
					$transaction->amount_to_pay = htmlspecialchars($request->input('amount_to_pay'));
					$transaction->message = htmlspecialchars($request->input('msg'));

					// $transaction->status = 'Waiting for Payment';

					// $transaction->create_id = Auth::user()->id;
					$transaction->update_id = Auth::user()->id;
					$transaction->save();


					/*Delete Transaction Detail*/
					$gettransactiondetails = Transactiondetail::where('transaction_id', '=', $transaction->id)->get();
					foreach ($gettransactiondetails as $gettransactiondetail) 
					{
						/*Delete Inventory then recalculate it*/
						$getinventory = Inventory::where('type', '=', 'S')->where('type_id', '=', $gettransactiondetail->id)->first();
						$inventoryproductstockid = $getinventory->productstock_id;
						$inventorydate = $getinventory->date;
						$inventoryid = $getinventory->id;
						$getinventory->delete();

						update_inventory($inventoryproductstockid, $inventorydate, $inventoryid);
						
						$gettransactiondetail->delete();
					}
					

					// $gettransactiondetails = Transactiondetail::where('transaction_id', '=', $transaction->id)->get();
					// foreach ($gettransactiondetails as $gettransactiondetail) {
					// 	$gettransactiondetail->delete();
					// }

					foreach ($products as $key => $item) {
						$product = Product::find($key);

						if(($item['qty'] != null) AND ($item['qty'] != 0) AND ($item['qty'] > 0))
						{
							$transactiondetail = new Transactiondetail;
							$transactiondetail->transaction_id = $transaction->id;
							$transactiondetail->product_id = $key;
							$transactiondetail->rak_id = $item['rak'];
							$transactiondetail->qty = $item['qty'];
							$transactiondetail->price = $item['price'];
							$transactiondetail->discounttype = $item['discounttype'];
							$transactiondetail->discount = $item['discount'];
							$transactiondetail->save();


							$checkproductstock = Productstock::where('product_id', '=', $transactiondetail->product_id)->where('rak_id', '=', $transactiondetail->rak_id)->first();

							/*Adding Inventory Data*/
							if($transactiondetail->qty != 0)
							{
								/*Adding Inventory Data*/
								$inventory = new Inventory;
								$inventory->date = $transaction->date;
								$inventory->productstock_id = $checkproductstock->id;
								$inventory->type = 'S';
								$inventory->type_id = $transactiondetail->id;

								$getlastinv = Inventory::where('productstock_id', '=', $checkproductstock->id)->where('date', '<=', $transaction->date)->orderBy('date', 'desc')->orderBy('id', 'desc')->first();

								$inventory->qty_last = $getlastinv->qty_z;
								$inventory->price_last = $getlastinv->price_z;

								$inventory->qty_z = $getlastinv->qty_z - $transactiondetail->qty;
								if($getlastinv->qty_z - $transactiondetail->qty == 0)
								{
									$inventory->price_z = 0;
								}
								else
								{
									$inventory->price_z = $getlastinv->price_z;
								}
							
								$inventory->real_price = $getlastinv->price_z;

								$inventory->qty_out = $transactiondetail->qty;
								$inventory->price_out = $getlastinv->price_z;

								$inventory->qty_in = 0;
								$inventory->price_in = 0;
								$inventory->save();

								$productstock = Productstock::find($inventory->productstock_id);
								$productstock->stock = $inventory->qty_z;
								$productstock->save();

								update_inventory($inventory->productstock_id, $inventory->date, $inventory->id);
							}
						}
					}
				});

				if (Session::has('save-pay'))
				{
					Session::put('save-pay', $transaction->id);
				
					return redirect(Crypt::decrypt($setting->admin_url) . '/tpayment/create')->with('success-message', "Transaction <strong>$transaction->trans_id</strong> has been updated");
				}
				else
				{
					return redirect(Crypt::decrypt($setting->admin_url) . '/transaction')->with('success-message', "Transaction <strong>$transaction->trans_id</strong> has been updated");
				}
			}
			else
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/transaction')->with('error-message', 'Can not find image with ID ' . $id);
			}
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/transaction/' . $id . '/edit')->withInput()->withErrors($validator);
		}
	}


	/* Delete a resource*/
	public function destroy(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->transaction_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}
		if ($admingroup->transaction_d != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}
		
		$transaction = Transaction::find($id);
		if ($transaction != null)
		{
			if($transaction->status != 'Waiting for Payment')
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/transaction')->with('error-message', 'You can not deleted this Transaction');
			}

			DB::transaction(function() use ($transaction, $setting) 
			{
				$transactiondetails = Transactiondetail::where('transaction_id', '=', $transaction->id)->get();
				if (!$transactiondetails->isEmpty())
				{
					foreach ($transactiondetails as $transactiondetail) 
					{
						$transactiondetailids[] = $transactiondetail->id;

						$getinventory = Inventory::where('type', '=', 'S')->where('type_id', '=', $transactiondetail->id)->first();
						$inventoryproductstockid = $getinventory->productstock_id;
						$inventorydate = $getinventory->date;
						$inventoryid = $getinventory->id;
						$getinventory->delete();

						$lastinventory = Inventory::where('productstock_id', '=', $inventoryproductstockid)->orderBy('date', 'desc')->orderBy('id', 'desc')->first();

						$productstock = Productstock::find($lastinventory->productstock_id);
						$productstock->stock = $lastinventory->qty_z;
						$productstock->save();

						update_inventory($inventoryproductstockid, $inventorydate, $inventoryid);

						$transactiondetail->delete();
					}
				}

				$requestid = $transaction->request_id;
				$transaction->delete();
			});

 			return redirect(Crypt::decrypt($setting->admin_url) . '/transaction')->with('success-message', "Transaction <strong>$transaction->trans_id</strong> has been deleted");
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/transaction')->with('error-message', 'Can not find po with ID ' . $id);
		}
	}




	public function getSend($id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		$transaction = Transaction::find($id);
		if($transaction != null)
		{
			$transaction->status = 'Dikirim';
			$transaction->save();

			$data['transaction'] = $transaction;

			return redirect(Crypt::decrypt($setting->admin_url) . '/transaction')->with('success-message', "Transaction $transaction->trans_id has been sent");
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/transaction')->with('error-message', 'Can not find Transaction with ID ' . $id);
		}
	}

	public function getAbort($id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		$transaction = Transaction::find($id);
		if($transaction != null)
		{
			$transaction->status = 'Dibatalkan';
			$transaction->save();

			$data['transaction'] = $transaction;

			return redirect(Crypt::decrypt($setting->admin_url) . '/transaction')->with('success-message', "Transaction $transaction->trans_id has been aborted");
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/transaction')->with('error-message', 'Can not find Transaction with ID ' . $id);
		}
	}

	// public function getPrint($id)
	// {
	// 	$setting = Setting::first();
	// 	$data['setting'] = $setting;

	// 	$transaction = Transaction::find($id);
	// 	if($transaction != null)
	// 	{
	// 		$data['transaction'] = $transaction;

	// 		$transactiondetails = Transactiondetail::where('transaction_id', '=', $transaction->id)->get();
	// 		$data['transactiondetails'] = $transactiondetails;

	// 		$customer = Customer::find($transaction->customer_id);
	// 		$data['customer'] = $customer;

	// 		return view('back.transaction.print', $data);
	// 	}
	// 	else
	// 	{
	// 		return redirect(Crypt::decrypt($setting->admin_url) . '/transaction')->with('error-message', 'Can not find Transaction with ID ' . $id);
	// 	}
	// }

	public function getReplace()
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		$products = Product::whereNotIn('id', Session::get('add_product'))->where('is_active', '=', true)->orderBy('name')->get();

		$data['products'] = $products;

		$product_options[] = 'Select Product';
		foreach ($products as $product) {
			$product_options[$product->id] = $product->name;
		}
		$data['product_options'] = $product_options;

		return view('back.transaction.replace', $data);
	}

	public function getAdd($productid = null, $rakid = null, $qty = null, $price = null, $discounttype = null, $discount = null)
	{
		if($productid != null)
		{
			$data['productid'] = $productid;
		}
		if($rakid != null)
		{
			$data['rakid'] = $rakid;
		}
		if($qty != null)
		{
			$data['qty'] = $qty;
		}
		if($price != null)
		{
			$data['price'] = $price;
		}
		if($discounttype != null)
		{
			$data['discounttype'] = $discounttype;
		}
		if($discount != null)
		{
			$data['discount'] = $discount;
		}

		$setting = Setting::first();
		$data['setting'] = $setting;

		// dd(Session::get('add_product'));

		// $product = Product::find($id);
		// $data['product'] = $product;

		if(Session::has('add_product'))
		{
			if($productid != null)
			{
				// $addproducts = Session::get('add_product');
				// dd(Session::get('add_product'));
				$addproducts = array_diff(Session::get('add_product'), array($productid));
				// dd($addproducts);
				Session::put('add_product', $addproducts);

				$products = Product::whereNotIn('id', $addproducts)->where('is_active', '=', true)->orderBy('name')->get();
			}
			else
			{
				$products = Product::whereNotIn('id', Session::get('add_product'))->where('is_active', '=', true)->orderBy('name')->get();
			}
		}
		else
		{
			$products = Product::where('is_active', '=', true)->orderBy('name')->get();
		}
		$data['products'] = $products;

		$product_options[] = 'Select Product';
		foreach ($products as $product) {
			$product_options[$product->id] = $product->name;
		}
		$data['product_options'] = $product_options;

		return view('back.transaction.form', $data);
	}

	public function getForm($productid, $rak_id, $qty, $price, $discounttype, $discount)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		Session::push('add_product', $productid);

		$product = Product::find($productid);
		$rak = Rak::find($rak_id);
		$data['product'] = $product;
		$data['rak_id'] = $rak_id;
		$data['rak'] = $rak;
		$data['qty'] = $qty;
		$data['price'] = $price;
		$data['discounttype'] = $discounttype;
		$data['discount'] = $discount;

		if($discounttype == '0')
		{
			$subtotal = ($qty * $price) - $discount;
			// $subtotal = 0000000;
		}

		if($discounttype == '1')
		{
			$subtotal = ($qty * $price) - ((($qty * $price) * $discount) / 100);
			// $subtotal = 1111111;
		}
		$data['subtotal'] = $subtotal;

		$subprice = $qty * $price;
		$data['subprice'] = $subprice;

		return view('back.transaction.search', $data);
	}

	public function getDrop($id)
	{
		$getsessions = Session::get('add_product');
		foreach ($getsessions as $getsession) {
			$getsessionids[] = $getsession;
		}
		$removedatas = array_diff($getsessionids, array($id));
		Session::forget('add_product');
		foreach($removedatas as $removedata)
		{
			Session::push('add_product', $removedata);
		}

		echo "sukses";

		// if(count($removedatas) == 0)
		// {
		// 	$products = Product::where('is_active', '=', true)->orderBy('name')->get();
		// }
		// else
		// {
		// 	$products = Product::whereNotIn('id', $removedatas)->where('is_active', '=', true)->orderBy('name')->get();
		// }
		// $product_options[] = 'Select Product';
		// foreach ($products as $product) {
		// 	$product_options[$product->id] = $product->name;
		// }
		// $data['product_options'] = $product_options;

		// return view('back.transaction.replace', $data);
	}

	public function getTprint($id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		$transaction = Transaction::find($id);
		if($transaction != null)
		{
			$data['transaction'] = $transaction;

			$transactiondetails = Transactiondetail::where('transaction_id', '=', $transaction->id)->get();
			$data['transactiondetails'] = $transactiondetails;

			$customer = Customer::find($transaction->customer_id);
			$data['customer'] = $customer;

			return view('back.transaction.print', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/transaction')->with('error-message', 'Can not find Transaction with ID ' . $id);
		}
	}

	public function getReport(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/
		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->transaction_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		/*Menu Authentication*/

		$data['navModul'] = true;
		$data['helpModul'] = true;
		$data['searchModul'] = false;
		$data['alertModul'] = true;
		$data['messageModul'] = true;

		$data['request'] = $request;

        return view('back.transactionreport.report', $data);
	}

	public function postReport(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;
		
		$inputs = $request->all();
		$rules = array(
			'date_start'			=> 'required',
			'date_end'				=> 'required',
		);

		$validator = Validator::make($inputs, $rules);
		if ($validator->passes())
		{
			$datestart = htmlspecialchars($request->get('date_start'));
			$dateend = htmlspecialchars($request->get('date_end'));

			$data['datestart'] = $datestart;
			$data['dateend'] = $dateend;

			return view('back.transactionreport.showreport', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/transaction/report')->withInput()->withErrors($validator);
		}
	}

	public function getPrint($datestart, $dateend)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;
		
		$data['datestart'] = $datestart;
		$data['dateend'] = $dateend;
			
		return view('back.transactionreport.print', $data);
	}
}