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
use App\Models\Inventory;
use App\Models\Pricegap;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Ri;
use App\Models\Ridetail;
use App\Models\Hbt;
use App\Models\Po;
use App\Models\Podetail;
use App\Models\Returndetail;
use App\Models\Retur;
use App\Models\Supprice;


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


class InvoiceController extends Controller
{
	/* Get the list of the resource*/
	public function index(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/
		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->invoice_r != true)
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
		
		$query = Invoice::query();

		$date = htmlspecialchars($request->input('src_date'));
		if ($date != null)
		{
			$query->where('date', 'LIKE', '%' . $date . '%');
			$data['criteria']['src_date'] = $date;
		}

		$no_nota = htmlspecialchars($request->input('src_no_nota'));
		if ($no_nota != null)
		{
			$query->where('no_nota', 'LIKE', '%' . $no_nota . '%');
			$data['criteria']['src_no_nota'] = $no_nota;
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
		$invoices = $query->paginate($per_page);
		$data['invoices'] = $invoices;

		$request->flash();

		$suppliers = Supplier::where('is_active', '=', true)->orderBy('name')->get();
		if($suppliers->isEmpty())
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/supplier/create')->with('error-message', "Your supplier is Empty, Please create it first");
		}

		$supplier_options[''] = "Select Supplier";
		foreach ($suppliers as $supplier) {
			$supplier_options[$supplier->id] = $supplier->name;
		}
		$data['supplier_options'] = $supplier_options;

		$request->session()->put('last_url', URL::full());

		$data['request'] = $request;

        return view('back.invoice.index', $data);
	}

	/* Create a invoice resource*/
	public function create(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->invoice_c != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/invoice')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		/*Menu Authentication*/

		$data['navModul'] = true;
		$data['helpModul'] = true;
		$data['searchModul'] = false;
		$data['alertModul'] = true;
		$data['messageModul'] = true;
		
		$data['request'] = $request;
		
		$invoice = new Invoice;
		$data['invoice'] = $invoice;

		$hbts = Hbt::where('status', '=', false)->get();
		if(!$hbts->isEmpty())
		{
			foreach ($hbts as $hbt) {
				$hbtids[] = $hbt->ri_id;
			}

			$ris = Ri::whereIn('id', $hbtids)->where('is_invoice', '=', false)->get();
			if($ris->isEmpty())
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/ri/create')->with('error-message', "Sorry you don't have Recieve Item, please create it first.");
			}
			foreach ($ris as $ri) {
				$riids[] = $ri->po_id;
			}

			$pos = Po::whereIn('id', $riids)->get();
			if($pos->isEmpty())
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/po/create')->with('error-message', "Sorry you don't have Purchase Order, please create it first.");
			}
			foreach ($pos as $po) {
				$poids[] = $po->supplier_id;
			}

			$suppliers = Supplier::whereIn('id', $poids)->where('is_active', '=', true)->orderBy('name')->get();
			if($suppliers->isEmpty())
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/supplier/create')->with('error-message', "Sorry you don't have supplier, please create it first.");
			}
			$supplier_options[''] = 'Select Supplier';
			foreach ($suppliers as $supplier) {
				$supplier_options[$supplier->id] = $supplier->name;
			}
		}
		else
		{
			$supplier_options[''] = 'Select Supplier';
		}

		Session::forget('add_product');

		$data['supplier_options'] = $supplier_options;

        return view('back.invoice.create', $data);
	}

	public function store(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;
		
		$inputs = $request->all();
		$rules = array(
			'supplier'			=> 'required',
			'no_nota'			=> 'required',
			'date'				=> 'required',
		);

		$validator = Validator::make($inputs, $rules);
		if ($validator->passes())
		{
			$invoicedetails = $request->input('ridetail');

			if($invoicedetails == 0)
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/invoice/create')->withInput()->with('error-message', "Please select the recieve item first");
			}

			DB::transaction(function() use ($request, $invoicedetails) {
				global $invoice;

				$invoice = new Invoice;
				$invoice->no_nota = htmlspecialchars($request->input('no_nota'));
				$invoice->supplier_id = htmlspecialchars($request->input('supplier'));
				$invoice->date = htmlspecialchars($request->input('date'));

				$getsupplier = Supplier::find(htmlspecialchars($request->input('supplier')));

				$date = htmlspecialchars($request->input('date'));
				$duedate = date('Y-m-d', strtotime($date . "+" . $getsupplier->tempo . "days"));
				// dd($duedate);

				$invoice->due_date = $duedate;
				$invoice->status = 'Pending';

				$invoice->create_id = Auth::user()->id;
				$invoice->update_id = Auth::user()->id;
				$invoice->save();


				foreach ($invoicedetails as $id => $item) {
					if(($item['qty'] != null) AND ($item['qty'] >= 0))
					{
						$invoicedetail = new Invoicedetail;
						$invoicedetail->invoice_id = $invoice->id;
						$invoicedetail->ridetail_id = $id;
						$invoicedetail->price = $item['price'];
						$invoicedetail->qty = $item['qty'];
						// $invoicedetail->price = $product->price;
						$invoicedetail->save();

						if($invoicedetail->ridetail->product_id == 0)
						{
							$checksupprice = Supprice::where('supplier_id', '=', $invoice->supplier_id)->where('product_id', '=', $invoicedetail->ridetail->podetail->product_id)->first();
							if($checksupprice != null)
							{
								$supprice = $checksupprice;
							}
							else
							{
								$supprice = new Supprice;
							}
							$supprice->supplier_id = $invoice->supplier_id;
							$supprice->product_id = $invoicedetail->ridetail->podetail->product_id;
							$supprice->price = $invoicedetail->price;
							$supprice->save();
						}

						$hbt = Hbt::where('ri_id', '=', $invoicedetail->ridetail->ri_id)->first();
						$hbt->status = true;
						$hbt->save();

						$ri = Ri::find($invoicedetail->ridetail->ri_id);
						$ri->is_invoice = true;
						$ri->save();

						$inventory = Inventory::where('type', '=', 'Ri')->where('type_id', '=', $invoicedetail->ridetail_id)->first();
						$inventory->real_price = $invoicedetail->price;
						$inventory->save();

						if($inventory->price_in != $inventory->real_price)
						{
							$pricegap = new Pricegap;
							$pricegap->date = $invoicedetail->invoice->date;
							$pricegap->invoicedetail_id = $invoicedetail->id;
							$pricegap->price = $inventory->real_price - $inventory->price_in;
							$pricegap->save();
						}
					}
				}
			});

			global $invoice;

			$admingroup = Admingroup::find(Auth::user()->admingroup_id);
			if ($admingroup->invoice_r != true)
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('success-message', "Invoice <strong>$invoice->name</strong> has been created");
			}
			return redirect(Crypt::decrypt($setting->admin_url) . '/invoice')->with('success-message', "Invoice <strong>$invoice->name</strong> has been created");
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/invoice/create')->withInput()->withErrors($validator);
		}
	}

	/* Show a resource*/
	public function show(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->invoice_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/invoice')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		/*Menu Authentication*/

		$data['navModul'] = true;
		$data['helpModul'] = true;
		$data['searchModul'] = false;
		$data['alertModul'] = true;
		$data['messageModul'] = true;
		
		
		$invoice = Invoice::find($id);
		if ($invoice != null)
		{
			$data['invoice'] = $invoice;
			$data['request'] = $request;

			$supplier = Supplier::find($invoice->supplier_id);
			$data['supplier'] = $supplier;

			$invoicedetails = Invoicedetail::where('invoice_id', '=', $invoice->id)->where('price', '!=', 0)->get();
			foreach ($invoicedetails as $invoicedetail) {
				$invoiceids[] = $invoicedetail->ridetail_id;
			}

			$ridetails = Ridetail::whereIn('id', $invoiceids)->get();
			foreach ($ridetails as $ridetail) {
				$ridetailids[] = $ridetail->ri_id;
			}

			$data['getinvoicedetails'] = $invoicedetails;
			$data['getridetailids'] = $ridetailids;

	        return view('back.invoice.view', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/invoice')->with('error-message', 'Can not find invoice with ID ' . $id);
		}
	}

	/* Edit a resource*/
	public function edit(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/
		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->invoice_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}
		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->invoice_u != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/invoice')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		/*Menu Authentication*/

		$data['navModul'] = true;
		$data['helpModul'] = true;
		$data['searchModul'] = false;
		$data['alertModul'] = true;
		$data['messageModul'] = true;
		
		
		$invoice = Invoice::find($id);
		
		if ($invoice != null)
		{
			$data['invoice'] = $invoice;

			Session::forget('add_product');

			if($invoice->status == 'Paid')
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/invoice')->with('error-message', 'You can not edit this Invoice');
			}

			/*Get Supplier Options*/
			$suppliers = Supplier::where('is_active', '=', true)->orderBy('name')->get();
			if($suppliers->isEmpty())
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/supplier/create')->with('error-message', "Sorry you don't have supplier, please create it first.");
			}
			$supplier_options[''] = 'Select Supplier';
			foreach ($suppliers as $supplier) {
				$supplier_options[$supplier->id] = $supplier->name;
			}
			$data['supplier_options'] = $supplier_options;

			/*Make Session Add Product*/
			$invoicedetails = Invoicedetail::where('invoice_id', '=', $id)->get();
			foreach ($invoicedetails as $invoicedetail) {
				$invoicedetailids[] = $invoicedetail->ridetail_id;
			}

			$ridetails = Ridetail::whereIn('id', $invoicedetailids)->get();
			foreach ($ridetails as $ridetail) {
				$ridetailids[] = $ridetail->ri_id;
			}

			$ris = Ri::whereIn('id', $ridetailids)->get();
			foreach ($ris as $ri) {
				Session::push('add_product', $ri->id);
			}

			$data['ris'] = $ris;

			$data['request'] = $request;

	        return view('back.invoice.edit', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/invoice')->with('error-message', 'Can not find image with ID ' . $id);
		}
	}

	public function update(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;
		
		$inputs = $request->all();
		$rules = array(
			'supplier'			=> 'required',
			'no_nota'			=> 'required',
			'date'			=> 'required',
		);

		$validator = Validator::make($inputs, $rules);
		if ($validator->passes())
		{
			$invoice = Invoice::find($id);
			if ($invoice != null)
			{
				$invoicedetails = $request->input('ridetail');

				if($invoicedetails == 0)
				{
					return redirect(Crypt::decrypt($setting->admin_url) . '/invoice/create')->withInput()->with('error-message', "Please select the recieve item first");
				}

				DB::transaction(function() use ($invoicedetails, $invoice, $request) {
					$invoice->no_nota = htmlspecialchars($request->input('no_nota'));
					$invoice->supplier_id = htmlspecialchars($request->input('supplier'));
					$invoice->date = htmlspecialchars($request->input('date'));

					$getsupplier = Supplier::find(htmlspecialchars($request->input('supplier')));

					$date = htmlspecialchars($request->input('date'));
					$duedate = date('Y-m-d', strtotime($date . "+" . $getsupplier->tempo . "days"));
					// dd($duedate);

					$invoice->update_id = Auth::user()->id;

					$invoice->save();

					$getinvoicedetails = Invoicedetail::where('invoice_id', '=', $invoice->id)->get();
					foreach ($getinvoicedetails as $getinvoicedetail) {
						$hbt = Hbt::where('ri_id', '=', $getinvoicedetail->ridetail->ri_id)->first();
						$hbt->status = false;
						$hbt->save();

						$ri = Ri::find($getinvoicedetail->ridetail->ri_id);
						$ri->is_invoice = false;
						$ri->save();

						$inventory = Inventory::where('type', '=', 'Ri')->where('type_id', '=', $getinvoicedetail->ridetail_id)->first();
						$inventory->real_price = $inventory->price_in;
						$inventory->save();

						$pricegap = Pricegap::where('invoicedetail_id', '=', $getinvoicedetail->id)->first();
						if($pricegap != null)
						{
							$pricegap->delete();
						}

						$getinvoicedetail->delete();
					}

					foreach ($invoicedetails as $id => $item) {
						if(($item['qty'] != null) AND ($item['qty'] >= 0))
						{
							$invoicedetail = new Invoicedetail;
							$invoicedetail->invoice_id = $invoice->id;
							$invoicedetail->ridetail_id = $id;
							$invoicedetail->price = $item['price'];
							$invoicedetail->qty = $item['qty'];
							// $invoicedetail->price = $product->price;
							$invoicedetail->save();

							if($invoicedetail->ridetail->product_id == 0)
							{
								$checksupprice = Supprice::where('supplier_id', '=', $invoice->supplier_id)->where('product_id', '=', $invoicedetail->ridetail->podetail->product_id)->first();
								if($checksupprice != null)
								{
									$supprice = $checksupprice;
								}
								else
								{
									$supprice = new Supprice;
								}
								$supprice->supplier_id = $invoice->supplier_id;
								$supprice->product_id = $invoicedetail->ridetail->podetail->product_id;
								$supprice->price = $invoicedetail->price;
								$supprice->save();
							}

							$hbt = Hbt::where('ri_id', '=', $invoicedetail->ridetail->ri_id)->first();
							$hbt->status = true;
							$hbt->save();

							$ri = Ri::find($invoicedetail->ridetail->ri_id);
							$ri->is_invoice = true;
							$ri->save();

							$inventory = Inventory::where('type', '=', 'Ri')->where('type_id', '=', $invoicedetail->ridetail_id)->first();
							$inventory->real_price = $invoicedetail->price;
							$inventory->save();

							if($inventory->price_in != $inventory->real_price)
							{
								$pricegap = new Pricegap;
								$pricegap->date = $invoicedetail->invoice->date;
								$pricegap->invoicedetail_id = $invoicedetail->id;
								$pricegap->price = $inventory->real_price - $inventory->price_in;
								$pricegap->save();
							}

							/*Change Return if exist*/
							$returndetail = Returndetail::where('ridetail_id', '=', $invoicedetail->ridetail_id)->first();
							if($returndetail != null)
							{
								$return = Retur::find($returndetail->return_id);

								$returndetail->price = $invoicedetail->price;
								$returndetail->save();

								$rinventory = Inventory::where('type', '=', 'R')->where('type_id', '=', $returndetail->id)->first();
								$rinventory->real_price = $returndetail->price;
								$rinventory->save();

								$checkrpricegap = Pricegap::where('returndetail_id', '=', $returndetail->id)->first();
								if($checkrpricegap != null)
								{
									$rpricegap = $checkrpricegap;
								}
								else
								{
									$pricegap = new Pricegap;
								}
								$rpricegap->date = $return->date;
								$rpricegap->returndetail_id = $returndetail->id;
								$rpricegap->price = $rinventory->real_price - $rinventory->price_out;
								$rpricegap->save();
							}
						}
					}
				});

				return redirect(Crypt::decrypt($setting->admin_url) . '/invoice')->with('success-message', "Invoice <strong>$invoice->name</strong> has been updated");
			}
			else
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/invoice')->with('error-message', 'Can not find image with ID ' . $id);
			}
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/invoice/' . $id . '/edit')->withInput()->withErrors($validator);
		}
	}


	/* Delete a resource*/
	public function destroy(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->invoice_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}
		if ($admingroup->invoice_d != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}
		
		$invoice = Invoice::find($id);
		if ($invoice != null)
		{
			if($invoice->status == 'Paid')
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/invoice')->with('error-message', 'You can not deleted this Invoice');
			}

			$getreturns = Retur::get();
			$getreturnids = array();
			$ridetailids = array();
			if(!$getreturns->isEmpty())
			{
				foreach ($getreturns as $getreturn) {
					$getreturnids[] = $getreturn->ri_id;
				}
			// dd($getreturnids);

				$ridetails = Ridetail::whereIn('ri_id', $getreturnids)->get();
				foreach ($ridetails as $ridetail) {
					$ridetailids[] = $ridetail->id;
				}
			}
			// dd($ridetailids);

			if(!$getreturns->isEmpty())
			{
				$getinvoicedetail = Invoicedetail::whereIn('ridetail_id', $ridetailids)->where('invoice_id', '=', $invoice->id)->first();

				if($getinvoicedetail != null)
				{
					return redirect(Crypt::decrypt($setting->admin_url) . '/invoice')->with('error-message', 'You can not deleted this Invoice');
				}
			}
			// dd($getinvoicedetail->ridetail_id);


			$invoicedetails = Invoicedetail::where('invoice_id', '=', $id)->get();
			if (count($invoicedetails) != 0)
			{
				foreach ($invoicedetails as $invoicedetail) 
				{
					$hbt = Hbt::where('ri_id', '=', $invoicedetail->ridetail->ri_id)->first();
					$hbt->status = false;
					$hbt->save();

					$ri = Ri::find($invoicedetail->ridetail->ri_id);
					$ri->is_invoice = false;
					$ri->save();

					$inventory = Inventory::where('type', '=', 'Ri')->where('type_id', '=', $invoicedetail->ridetail_id)->first();
					$inventory->real_price = $inventory->price_in;
					$inventory->save();

					$pricegap = Pricegap::where('invoicedetail_id', '=', $invoicedetail->id)->first();
					if($pricegap != null)
					{
						$pricegap->delete();
					}

					$invoicedetail->delete();
				}
			}

			$invoice->delete();

 			return redirect(Crypt::decrypt($setting->admin_url) . '/invoice')->with('success-message', "Invoice <strong>$invoice->name</strong> has been deleted");
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/invoice')->with('error-message', 'Can not find invoice with ID ' . $id);
		}
	}

	public function getSupplier(Request $request, $id)
	{
		$hbts = Hbt::where('status', '=', false)->get();
		foreach ($hbts as $hbt) {
			$hbtids[] = $hbt->ri_id;
		}

		// $ris = Ri::whereIn('id', $hbtids)->get();
		// foreach ($ris as $ri) {
		// 	$riids[] = $ri->po_id;
		// }

		$pos = Po::where('supplier_id', '=', $id)->get();
		foreach ($pos as $po) {
			$poids[] = $po->id;
		}

		if($pos->isEmpty())
		{
			$ri_options[''] = 'Select Recieve Item';
		}
		else
		{
			$getris = Ri::whereIn('po_id', $poids)->whereIn('id', $hbtids)->orderBy('id', 'desc')->get();
			$ri_options[''] = 'Select Recieve Item';
			foreach ($getris as $getri) {
				$ri_options[$getri->id] = $getri->no_nota;
			}
		}
		$data['ri_options'] = $ri_options;

		return view('back.invoice.supplier', $data);
	}

	public function getReplace(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		$hbts = Hbt::where('status', '=', false)->get();
		foreach ($hbts as $hbt) {
			$hbtids[] = $hbt->ri_id;
		}

		$pos = Po::where('supplier_id', '=', $id)->get();
		foreach ($pos as $po) {
			$poids[] = $po->id;
		}

		if($pos->isEmpty())
		{
			$ri_options[''] = 'Select Recieve Item';
		}
		else
		{
			$getris = Ri::whereIn('po_id', $poids)->whereNotIn('id', Session::get('add_product'))->orderBy('id', 'desc')->where('is_invoice', '=', false)->get();
			$ri_options[''] = 'Select Recieve Item';
			foreach ($getris as $getri) {
				$ri_options[$getri->id] = $getri->no_nota;
			}
		}
		$data['ri_options'] = $ri_options;

		return view('back.invoice.replace', $data);
	}

	public function getRi(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;
		$data['dataid'] = $id;

		// dd(Session::get('add_product'));

		$ri = Ri::find($id);
		$data['ri'] = $ri;

		$ridetails = Ridetail::where('ri_id', '=', $id)->where('product_id', '=', 0)->where('qty', '>', 0)->get();
		$data['ridetails'] = $ridetails;

		Session::push('add_product', $id);

		return view('back.invoice.search', $data);
	}

	public function getDrop(Request $request, $id, $supplier)
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

		$pos = Po::where('supplier_id', '=', $supplier)->get();
		foreach ($pos as $po) {
			$poids[] = $po->id;
		}

		if(count($removedatas) == 0)
		{
			$getris = Ri::whereIn('po_id', $poids)->orderBy('id', 'desc')->where('is_invoice', '=', false)->get();
		}
		else
		{
			$getris = Ri::whereIn('po_id', $poids)->whereNotIn('id', Session::get('add_product'))->orderBy('id', 'desc')->where('is_invoice', '=', false)->get();
		}
			
		$ri_options[''] = 'Select Recieve Item';
		foreach ($getris as $getri) {
			$ri_options[$getri->id] = $getri->no_nota;
		}
		$data['ri_options'] = $ri_options;

		return view('back.invoice.replace', $data);
	}

	public function getPrint(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		$invoice = Invoice::find($id);
		if($invoice != null)
		{
			$data['invoice'] = $invoice;

			$invoicedetails = Invoicedetail::where('invoice_id', '=', $invoice->id)->where('price', '!=', 0)->get();
			$data['invoicedetails'] = $invoicedetails;
			$data['getinvoicedetails'] = $invoicedetails;

			foreach ($invoicedetails as $invoicedetail) {
				$invoiceids[] = $invoicedetail->ridetail_id;
			}

			$ridetails = Ridetail::whereIn('id', $invoiceids)->get();
			foreach ($ridetails as $ridetail) {
				$ridetailids[] = $ridetail->ri_id;
			}

			$data['getridetailids'] = $ridetailids;
			
			$supplier = Supplier::find($invoice->supplier_id);
			$data['supplier'] = $supplier;

			return view('back.invoice.print', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/invoice')->with('error-message', 'Can not find Invoice with ID ' . $id);
		}
	}

	public function getReport(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		$data['request'] = $request;

		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->invoice_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		/*Menu Authentication*/

		$data['navModul'] = true;
		$data['helpModul'] = true;
		$data['searchModul'] = false;
		$data['alertModul'] = true;
		$data['messageModul'] = true;

		$getinvoices = Invoice::where('status', '=', 'Pending')->get();
		$supids = [];
		foreach ($getinvoices as $getinvoice) {
			$supids[] = $getinvoice->supplier_id;
		}

		$suppliers = Supplier::whereIn('id', $supids)->get();
		$data['suppliers'] = $suppliers;

		$supplier_options[''] = "Select Supplier";
		foreach ($suppliers as $supplier) {
			$supplier_options[$supplier->id] = $supplier->name;
		}
		$data['supplier_options'] = $supplier_options;

		return view('back.invoice.report', $data);
	}

	public function postReport(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;
		
		$inputs = $request->all();
		$rules = array(
			'supplier'			=> 'required',
		);

		$validator = Validator::make($inputs, $rules);
		if ($validator->passes())
		{
			$supplier = Supplier::find(htmlspecialchars($request->get('supplier')));

			$data['supplier'] = $supplier;

			return view('back.invoice.showreport', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/invoice/report')->withInput()->withErrors($validator);
		}
	}

	public function getTprint($supplierid)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		$supplier = Supplier::find($supplierid);
		$data['supplier'] = $supplier;
			
		return view('back.invoice.tprint', $data);
	}
}