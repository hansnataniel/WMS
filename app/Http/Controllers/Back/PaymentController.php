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
use App\Models\Payment;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Bank;


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


class PaymentController extends Controller
{
	/* Get the list of the resource*/
	public function index(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/
		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->payment_r != true)
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
		
		$query = Payment::query();

		$no_nota = htmlspecialchars($request->input('src_no_nota'));
		if ($no_nota != null)
		{
			$query->where('no_nota', 'LIKE', '%' . $no_nota . '%');
			$data['criteria']['src_no_nota'] = $no_nota;
		}

		$bank_id = htmlspecialchars($request->input('src_bank_id'));
		if ($bank_id != null)
		{
			$query->where('bank_id', '=', $bank_id);
			$data['criteria']['src_bank_id'] = $bank_id;
		}

		$invoice_id = htmlspecialchars($request->input('src_invoice_id'));
		if ($invoice_id != null)
		{
			$query->where('invoice_id', '=', $invoice_id);
			$data['criteria']['src_invoice_id'] = $invoice_id;
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
		$payments = $query->paginate($per_page);
		$data['payments'] = $payments;

		$request->flash();

		$request->session()->put('last_url', URL::full());

		$data['request'] = $request;

		$invoices = Invoice::where('status', '=', 'Paid')->get();
		$invoice_options[''] = 'Select Invoice';
		foreach ($invoices as $invoice) {
			$invoice_options[$invoice->id] = $invoice->no_nota;
		}
		$data['invoice_options'] = $invoice_options;

		$banks = Bank::where('is_active', '=', true)->orderBy('name')->get();
		$bank_options[''] = 'Select Bank';
		foreach ($banks as $bank) {
			$bank_options[$bank->id] = $bank->name . ' - ' . $bank->account_number;
		}
		$data['bank_options'] = $bank_options;

        return view('back.payment.index', $data);
	}

	/* Create a payment resource*/
	public function create(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->payment_c != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/payment')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		/*Menu Authentication*/

		$data['navModul'] = true;
		$data['helpModul'] = false;
		$data['searchModul'] = false;
		$data['alertModul'] = true;
		$data['messageModul'] = true;
		
		$data['request'] = $request;
		
		$payment = new Payment;
		$data['payment'] = $payment;

		$invoices = Invoice::where('status', '=', 'Pending')->get();
		$invoice_options[''] = 'Select Invoice';
		foreach ($invoices as $invoice) {
			$invoice_options[$invoice->id] = $invoice->no_nota;
		}
		$data['invoice_options'] = $invoice_options;

		$banks = Bank::where('is_active', '=', true)->orderBy('name')->get();
		$bank_options[''] = 'Select Bank';
		foreach ($banks as $bank) {
			$bank_options[$bank->id] = $bank->name . ' - ' . $bank->account_number;
		}
		$data['bank_options'] = $bank_options;

        return view('back.payment.create', $data);
	}

	public function store(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;
		
		$inputs = $request->all();
		$rules = array(
			'date'				=> 'required',
			'invoice'			=> 'required',
			'bank'				=> 'required',
		);

		$validator = Validator::make($inputs, $rules);
		if ($validator->passes())
		{
			DB::transaction(function() use ($request) {
				global $payment;
				
				$payment = new Payment;
				$payment->no_nota = htmlspecialchars($request->input('no_nota'));
				$payment->date = htmlspecialchars($request->input('date'));
				$payment->bank_id = htmlspecialchars($request->input('bank'));
				$payment->invoice_id = htmlspecialchars($request->input('invoice'));

				$payment->create_id = Auth::user()->id;
				$payment->update_id = Auth::user()->id;
				
				$payment->save();

				$invoice = Invoice::find($payment->invoice_id);
				$invoice->status = 'Paid';

				$invoice->save();
			});
			global $payment;
			
			$admingroup = Admingroup::find(Auth::user()->admingroup_id);
			if ($admingroup->payment_r != true)
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('success-message', "Payment <strong>$payment->name</strong> has been created");
			}
			return redirect(Crypt::decrypt($setting->admin_url) . '/payment')->with('success-message', "Payment <strong>$payment->name</strong> has been created");
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/payment/create')->withInput()->withErrors($validator);
		}
	}

	/* Show a resource*/
	public function show(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->payment_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/payment')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		/*Menu Authentication*/

		$data['navModul'] = true;
		$data['helpModul'] = true;
		$data['searchModul'] = false;
		$data['alertModul'] = true;
		$data['messageModul'] = true;
		
		
		$payment = Payment::find($id);
		if ($payment != null)
		{
			$data['payment'] = $payment;
			$data['request'] = $request;

			$invoicedetails = Invoicedetail::where('invoice_id', '=', $payment->invoice_id)->where('price', '!=', 0)->get();
			foreach ($invoicedetails as $invoicedetail) {
				$invoiceids[] = $invoicedetail->ridetail_id;
			}

			$ridetails = Ridetail::whereIn('id', $invoiceids)->get();
			foreach ($ridetails as $ridetail) {
				$ridetailids[] = $ridetail->ri_id;
			}

			$data['getinvoicedetails'] = $invoicedetails;
			$data['getridetailids'] = $ridetailids;
			
	        return view('back.payment.view', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/payment')->with('error-message', 'Can not find payment with ID ' . $id);
		}
	}

	/* Edit a resource*/
	public function edit(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/
		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->payment_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}
		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->payment_u != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/payment')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		/*Menu Authentication*/

		$data['navModul'] = true;
		$data['helpModul'] = false;
		$data['searchModul'] = false;
		$data['alertModul'] = true;
		$data['messageModul'] = true;
		
		
		$payment = Payment::find($id);
		
		if ($payment != null)
		{
			$data['payment'] = $payment;

			$invoices = Invoice::where(function($qr) use ($payment) {
				$qr->where('status', '=', 'Pending');
				$qr->orWhere('id', '=', $payment->invoice_id);
			})->get();
			$invoice_options[''] = 'Select Invoice';
			foreach ($invoices as $invoice) {
				$invoice_options[$invoice->id] = $invoice->no_nota;
			}
			$data['invoice_options'] = $invoice_options;

			$banks = Bank::where('is_active', '=', true)->orderBy('name')->get();
			$bank_options[''] = 'Select Bank';
			foreach ($banks as $bank) {
				$bank_options[$bank->id] = $bank->name . ' - ' . $bank->account_number;
			}
			$data['bank_options'] = $bank_options;

			$invoicedetails = Invoicedetail::where('invoice_id', '=', $payment->invoice_id)->where('price', '!=', 0)->get();
			foreach ($invoicedetails as $invoicedetail) {
				$invoiceids[] = $invoicedetail->ridetail_id;
			}

			$ridetails = Ridetail::whereIn('id', $invoiceids)->get();
			foreach ($ridetails as $ridetail) {
				$ridetailids[] = $ridetail->ri_id;
			}

			$data['getinvoicedetails'] = $invoicedetails;
			$data['getridetailids'] = $ridetailids;

			$data['request'] = $request;

	        return view('back.payment.edit', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/payment')->with('error-message', 'Can not find image with ID ' . $id);
		}
	}

	public function update(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;
		
		$inputs = $request->all();
		$rules = array(
			'date'				=> 'required',
			'bank'				=> 'required',
			'invoice'				=> 'required',
		);

		$validator = Validator::make($inputs, $rules);
		if ($validator->passes())
		{
			$payment = Payment::find($id);
			if ($payment != null)
			{
				DB::transaction(function() use ($payment, $request) {
					if(htmlspecialchars($request->input('invoice')) != null)
					{
						$oldinvoice = Invoice::find($payment->invoice_id);
						$oldinvoice->status = 'Pending';
						$oldinvoice->save();

						$payment->date = htmlspecialchars($request->input('date'));
						$payment->bank_id = htmlspecialchars($request->input('bank'));
						$payment->invoice_id = htmlspecialchars($request->input('invoice'));

						$payment->update_id = Auth::user()->id;
						
						$payment->save();

						$invoice = Invoice::find($payment->invoice_id);
						$invoice->status = 'Paid';
						$invoice->save();
					}
					else
					{
						$payment->date = htmlspecialchars($request->input('date'));
						$payment->bank_id = htmlspecialchars($request->input('bank'));
						// $payment->invoice_id = htmlspecialchars($request->input('invoice'));
						
						$payment->update_id = Auth::user()->id;

						$payment->save();
					}
				});

				return redirect(Crypt::decrypt($setting->admin_url) . '/payment')->with('success-message', "Payment <strong>$payment->name</strong> has been updated");
			}
			else
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/payment')->with('error-message', 'Can not find image with ID ' . $id);
			}
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/payment/' . $id . '/edit')->withInput()->withErrors($validator);
		}
	}


	/* Delete a resource*/
	public function destroy(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->payment_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}
		if ($admingroup->payment_d != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}
		
		$payment = Payment::find($id);
		if ($payment != null)
		{
			DB::transaction(function() use ($payment){
				$invoice = Invoice::find($payment->invoice_id);
				$invoice->status = 'Pending';
				$invoice->save();

				$payment_name = Str::words($payment->no_nota, 5);
				$payment->delete();
			});

 			return redirect(Crypt::decrypt($setting->admin_url) . '/payment')->with('success-message', "Payment <strong>$payment->name</strong> has been deleted");
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/payment')->with('error-message', 'Can not find payment with ID ' . $id);
		}
	}

	public function getInvoice(Request $request, $id)
	{
		$invoice = Invoice::find($id);
		$data['invoice'] = $invoice;

		$invoicedetails = Invoicedetail::where('invoice_id', '=', $id)->where('price', '!=', 0)->orderBy('id', 'desc')->get();
		foreach ($invoicedetails as $invoicedetail) {
			$invoiceids[] = $invoicedetail->ridetail_id;
		}

		$ridetails = Ridetail::whereIn('id', $invoiceids)->orderBy('id', 'desc')->get();
		foreach ($ridetails as $ridetail) {
			$ridetailids[] = $ridetail->ri_id;
		}

		$data['getinvoicedetails'] = $invoicedetails;
		$data['getridetailids'] = $ridetailids;

		return view('back.payment.invoice', $data);
	}

	public function getPrint(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		$payment = Payment::find($id);
		if($payment != null)
		{
			$data['payment'] = $payment;

			$invoicedetails = Invoicedetail::where('invoice_id', '=', $payment->invoice_id)->where('price', '!=', 0)->get();
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

			$getdetail = Invoicedetail::where('invoice_id', '=', $payment->invoice_id)->where('price', '!=', 0)->first();

			$supplier = Supplier::find($payment->invoice->supplier_id);
			$data['supplier'] = $supplier;

			return view('back.payment.print', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/payment')->with('error-message', 'Can not find Payment with ID ' . $id);
		}
	}

	public function getPdf(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		$payment = Payment::find($id);
		if($payment != null)
		{
			$data['payment'] = $payment;

			$invoicedetails = Invoicedetail::where('invoice_id', '=', $payment->invoice_id)->where('price', '!=', 0)->get();
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

			$getdetail = Invoicedetail::where('invoice_id', '=', $payment->invoice_id)->where('price', '!=', 0)->first();

			$supplier = Supplier::find($payment->invoice->supplier_id);
			$data['supplier'] = $supplier;

			$html = \view('back.payment.print', $data);
		
			// $pdf = App::make('dompdf.wrapper');
			$pdf = PDF::loadHTML($html);
			return $pdf->setPaper('a4', 'portrait')->stream();

			// return View::make('back.payment.print', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/payment')->with('error-message', 'Can not find Payment with ID ' . $id);
		}
	}
}