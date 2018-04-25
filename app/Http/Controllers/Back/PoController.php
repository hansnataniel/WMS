<?php

/*
	Use Supplier_id Space Here
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
use App\Models\Supplier;
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
use DB;
use PDF;


class PoController extends Controller
{
	/* Get the list of the resource*/
	public function index(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/
		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->po_r != true)
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
		
		$query = Po::query();

		$no_nota = htmlspecialchars($request->input('src_no_nota'));
		if ($no_nota != null)
		{
			$query->where('no_nota', 'LIKE', '%' . $no_nota . '%');
			$data['criteria']['src_no_nota'] = $no_nota;
		}

		$supplier_id = htmlspecialchars($request->input('src_supplier_id'));
		if ($supplier_id != null)
		{
			$query->where('supplier_id', '=', $supplier_id);
			$data['criteria']['src_supplier_id'] = $supplier_id;
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
		$pos = $query->paginate($per_page);
		$data['pos'] = $pos;

		$request->flash();

		$gudangs = Gudang::where('is_active', '=', true)->orderBy('supplier_id')->get();
		if($gudangs->isEmpty())
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/gudang/create')->with('error-message', "Your gudang is Empty, Please create it first");
		}

		$gudang_options[''] = "Select Gudang";
		foreach ($gudangs as $gudang) {
			$gudang_options[$gudang->id] = $gudang->supplier_id;
		}
		$data['gudang_options'] = $gudang_options;

		$request->session()->put('last_url', URL::full());

		$data['request'] = $request;

        return view('back.po.index', $data);
	}

	/* Create a po resource*/
	public function create(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->po_c != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/po')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		/*Menu Authentication*/

		$data['navModul'] = true;
		$data['helpModul'] = true;
		$data['searchModul'] = false;
		$data['alertModul'] = true;
		$data['messageModul'] = true;
		
		$data['request'] = $request;
		
		$po = new Po;
		$data['po'] = $po;

		$suppliers = Supplier::where('is_active', '=', true)->orderBy('name')->get();
		if($suppliers->isEmpty())
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/supplier/create')->with('error-message', "Your supplier is Empty, Please create it first");
		}

		$supplier_options[''] = "Select supplier";
		foreach ($suppliers as $supplier) {
			$supplier_options[$supplier->id] = $supplier->name;
		}
		$data['supplier_options'] = $supplier_options;

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

        return view('back.po.create', $data);
	}

	public function store(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;
		
		$inputs = $request->all();
		$rules = array(
			'no_nota'				=> 'required|unique:pos,no_nota',
			'supplier'				=> 'required',
			'product'				=> 'required',
			'date'				=> 'required',
		);

		$validator = Validator::make($inputs, $rules);
		if ($validator->passes())
		{
			$products = $request->input('product');

			if($products == 0)
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/po/create')->withInput()->with('error-message', "Please select the product first");
			}

			DB::transaction(function() use ($products, $request) {
				global $po;

				$po = new Po;
				$po->no_nota = htmlspecialchars($request->input('no_nota'));
				$po->supplier_id = htmlspecialchars($request->input('supplier'));
				$po->date = htmlspecialchars($request->input('date'));
				$po->message = htmlspecialchars($request->input('msg'));
				$po->discounttype = htmlspecialchars($request->input('globaldiscounttype'));
				$po->discount = htmlspecialchars($request->input('globaldiscount'));
				$po->status = 'Belum Dikirim';
				$po->ri_status = 'Belum Diterima';

				$po->create_id = Auth::user()->id;
				$po->update_id = Auth::user()->id;
				
				$po->save();

				foreach ($products as $id => $item) {
					$product = Product::find($id);

					if(($item['qty'] != null) AND ($item['qty'] != 0) AND ($item['qty'] > 0))
					{
						$podetail = new Podetail;
						$podetail->po_id = $po->id;
						$podetail->product_id = $id;
						$podetail->qty = $item['qty'];
						$podetail->price = $item['price'];
						$podetail->discounttype = $item['discounttype'];
						$podetail->discount = $item['discount'];
						$podetail->status = 'Belum Diterima';
						$podetail->save();
					}
				}
			});

			global $po;

			$admingroup = Admingroup::find(Auth::user()->admingroup_id);
			if ($admingroup->po_r != true)
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('success-message', "Purchase Order <strong>$po->no_nota</strong> has been created");
			}
			return redirect(Crypt::decrypt($setting->admin_url) . '/po')->with('success-message', "Purchase Order <strong>$po->no_nota</strong> has been created");
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/po/create')->withInput()->withErrors($validator);
		}
	}

	/* Show a resource*/
	public function show(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->po_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/po')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		/*Menu Authentication*/

		$data['navModul'] = true;
		$data['helpModul'] = true;
		$data['searchModul'] = false;
		$data['alertModul'] = true;
		$data['messageModul'] = true;
		
		
		$po = Po::find($id);
		if ($po != null)
		{
			$data['po'] = $po;
			$data['request'] = $request;
			
	        return view('back.po.view', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/po')->with('error-message', 'Can not find po with ID ' . $id);
		}
	}

	/* Edit a resource*/
	public function edit(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/
		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->po_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}
		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->po_u != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/po')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		/*Menu Authentication*/

		$data['navModul'] = true;
		$data['helpModul'] = true;
		$data['searchModul'] = false;
		$data['alertModul'] = true;
		$data['messageModul'] = true;
		
		
		$po = Po::find($id);
		
		if ($po != null)
		{
			if($po->status == 'Dikirim')
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/po')->with('error-message', "You can only edit data when the status is 'Belum Dikirim'");
			}

			Session::forget('add_product');

			$data['po'] = $po;

			$suppliers = Supplier::where('is_active', '=', true)->orderBy('name')->get();
			if($suppliers->isEmpty())
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/supplier/create')->with('error-message', "Your supplier is Empty, Please create it first");
			}

			$supplier_options[''] = "Select supplier";
			foreach ($suppliers as $supplier) {
				$supplier_options[$supplier->id] = $supplier->name;
			}
			$data['supplier_options'] = $supplier_options;

			// $products = Product::where('is_active', '=', true)->orderBy('name')->get();
			// if($products->isEmpty())
			// {
			// 	return redirect(Crypt::decrypt($setting->admin_url) . '/product/create')->with('error-message', "Your product is Empty, Please create it first");
			// }

			// $product_options[''] = "Select product";
			// foreach ($products as $product) {
			// 	$product_options[$product->id] = $product->name;
			// }
			// $data['product_options'] = $product_options;

			$getproducts = Podetail::where('po_id', '=', $id)->get();
			$data['podetails'] = $getproducts;

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

			$podetails = Podetail::where('po_id', '=', $id)->get();
			$data['podetails'] = $podetails;

			$data['request'] = $request;

	        return view('back.po.edit', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/po')->with('error-message', 'Can not find image with ID ' . $id);
		}
	}

	public function update(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;
		
		$inputs = $request->all();
		$rules = array(
			'supplier'				=> 'required',
			'date'				=> 'required',
		);

		$validator = Validator::make($inputs, $rules);
		if ($validator->passes())
		{
			$po = Po::find($id);
			if ($po != null)
			{
				$products = $request->input('product');

				if($products == 0)
				{
					return redirect(Crypt::decrypt($setting->admin_url) . '/po/create')->withInput()->with('error-message', "Please select the product first");
				}

				DB::transaction(function() use ($products, $po, $request) {
					$po->supplier_id = htmlspecialchars($request->input('supplier'));
					$po->date = htmlspecialchars($request->input('date'));
					$po->discounttype = htmlspecialchars($request->input('globaldiscounttype'));
					$po->discount = htmlspecialchars($request->input('globaldiscount'));
					$po->message = htmlspecialchars($request->input('msg'));
					$po->save();

					$getpodetails = Podetail::where('po_id', '=', $po->id)->get();
					foreach ($getpodetails as $getpodetail) {
						$getpodetail->delete();
					}

					foreach ($products as $id => $item) {
						$product = Product::find($id);

						if(($item['qty'] != null) AND ($item['qty'] != 0) AND ($item['qty'] > 0))
						{
							$podetail = new Podetail;
							$podetail->po_id = $po->id;
							$podetail->product_id = $id;
							$podetail->qty = $item['qty'];
							$podetail->price = $item['price'];
							$podetail->discounttype = $item['discounttype'];
							$podetail->discount = $item['discount'];
							$podetail->status = 'Belum Diterima';
							$podetail->save();
						}
					}
				});

				return redirect(Crypt::decrypt($setting->admin_url) . '/po')->with('success-message', "Purchase Order <strong>$po->no_nota</strong> has been updated");
			}
			else
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/po')->with('error-message', 'Can not find image with ID ' . $id);
			}
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/po/' . $id . '/edit')->withInput()->withErrors($validator);
		}
	}


	/* Delete a resource*/
	public function destroy(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->po_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}
		if ($admingroup->po_d != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}
		
		$po = Po::find($id);
		if ($po != null)
		{
			if($po->status == 'Dikirim')
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/po')->with('error-message', 'You can not deleted this Purchase Order');
			}
			$podetails = Podetail::where('po_id', '=', $id)->get();
			if (count($podetails) != 0)
			{
				foreach ($podetails as $podetail) 
				{
					$podetail->delete();
				}
			}
			
			$po->delete();

 			return redirect(Crypt::decrypt($setting->admin_url) . '/po')->with('success-message', "Purchase Order <strong>$po->no_nota</strong> has been deleted");
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/po')->with('error-message', 'Can not find po with ID ' . $id);
		}
	}




	public function getSend($id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		$po = Po::find($id);
		if($po != null)
		{
			$po->status = 'Dikirim';
			$po->save();

			$data['po'] = $po;

			return redirect(Crypt::decrypt($setting->admin_url) . '/po')->with('success-message', "Purchase Order $po->no_nota has been sent");
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/po')->with('error-message', 'Can not find Purchase Order with ID ' . $id);
		}
	}

	public function getAbort($id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		$po = Po::find($id);
		if($po != null)
		{
			$po->status = 'Dibatalkan';
			$po->save();

			$data['po'] = $po;

			return redirect(Crypt::decrypt($setting->admin_url) . '/po')->with('success-message', "Purchase Order $po->no_nota has been aborted");
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/po')->with('error-message', 'Can not find Purchase Order with ID ' . $id);
		}
	}

	// public function getPrint($id)
	// {
	// 	$setting = Setting::first();
	// 	$data['setting'] = $setting;

	// 	$po = Po::find($id);
	// 	if($po != null)
	// 	{
	// 		$data['po'] = $po;

	// 		$podetails = Podetail::where('po_id', '=', $po->id)->get();
	// 		$data['podetails'] = $podetails;

	// 		$supplier = Supplier::find($po->supplier_id);
	// 		$data['supplier'] = $supplier;

	// 		return view('back.po.print', $data);
	// 	}
	// 	else
	// 	{
	// 		return redirect(Crypt::decrypt($setting->admin_url) . '/po')->with('error-message', 'Can not find Purchase Order with ID ' . $id);
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

		return view('back.po.replace', $data);
	}

	public function getAdd($productid = null, $qty = null, $price = null, $discounttype = null, $discount = null)
	{
		if($productid != null)
		{
			$data['productid'] = $productid;
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

		return view('back.po.form', $data);
	}

	public function getForm($productid, $qty, $price, $discounttype, $discount)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		Session::push('add_product', $productid);

		$product = Product::find($productid);
		$data['product'] = $product;
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

		return view('back.po.search', $data);
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

		// return view('back.po.replace', $data);
	}

	public function getPrint($id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		$po = Po::find($id);
		if($po != null)
		{
			$data['po'] = $po;

			$podetails = Podetail::where('po_id', '=', $po->id)->get();
			$data['podetails'] = $podetails;

			$supplier = Supplier::find($po->supplier_id);
			$data['supplier'] = $supplier;

			return view('back.po.print', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/po')->with('error-message', 'Can not find Purchase Order with ID ' . $id);
		}
	}
}