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

use App\Models\Transaction;
use App\Models\Transactiondetail;
use App\Models\Tpayment;
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


class PaymentconfirmationController extends Controller
{
	/* Get the list of the resource*/
	public function index(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/
		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->tpayment_r != true)
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
		
		$query = Tpayment::query();

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

		$transaction_id = htmlspecialchars($request->input('src_transaction_id'));
		if ($transaction_id != null)
		{
			$query->where('transaction_id', '=', $transaction_id);
			$data['criteria']['src_transaction_id'] = $transaction_id;
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
		$tpayments = $query->paginate($per_page);
		$data['tpayments'] = $tpayments;

		$request->flash();

		$request->session()->put('last_url', URL::full());

		$data['request'] = $request;

		$transactions = Transaction::where('status', '=', 'Waiting for Payment')->get();
		$transaction_options[''] = 'Select Transaction';
		foreach ($transactions as $transaction) {
			$transaction_options[$transaction->id] = $transaction->trans_id;
		}
		$data['transaction_options'] = $transaction_options;

		$banks = Bank::where('is_active', '=', true)->orderBy('name')->get();
		$bank_options[''] = 'Select Payment Method';
		$bank_options['0'] = 'Cash';
		foreach ($banks as $bank) {
			$bank_options[$bank->id] = $bank->name . ' - ' . $bank->account_number;
		}
		$data['bank_options'] = $bank_options;

        return view('back.tpayment.index', $data);
	}

	/* Create a payment resource*/
	public function create(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->tpayment_c != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/tpayment')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		/*Menu Authentication*/

		$data['navModul'] = true;
		$data['helpModul'] = false;
		$data['searchModul'] = false;
		$data['alertModul'] = true;
		$data['messageModul'] = true;
		
		$data['request'] = $request;
		
		$tpayment = new Tpayment;
		$data['tpayment'] = $tpayment;

		$transactions = Transaction::where('status', '=', 'Waiting for Payment')->get();
		$transaction_options[''] = 'Select Transaction';
		foreach ($transactions as $transaction) {
			$transaction_options[$transaction->id] = $transaction->trans_id;
		}
		$data['transaction_options'] = $transaction_options;

		$banks = Bank::where('is_active', '=', true)->orderBy('name')->get();
		$bank_options[''] = 'Select Payment Method';
		$bank_options['0'] = 'Cash';
		foreach ($banks as $bank) {
			$bank_options[$bank->id] = $bank->name . ' - ' . $bank->account_number;
		}
		$data['bank_options'] = $bank_options;

        return view('back.tpayment.create', $data);
	}

	public function store(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;
		
		$inputs = $request->all();
		$rules = array(
			'date'				=> 'required',
			'transaction'			=> 'required',
			'payment_method'				=> 'required',
		);

		$validator = Validator::make($inputs, $rules);
		if ($validator->passes())
		{
			$existcheck = Tpayment::where('transaction_id', '=', htmlspecialchars($request->input('transaction')))->first();
			if($existcheck != null)
			{
				$gettransaction = Transaction::find($existcheck->transaction_id);
				return redirect(Crypt::decrypt($setting->admin_url) . '/tpayment/create')->withInput()->withErrors("Payment Confirmation for Transaction $gettransaction->trans_id is already exist");
			}

			DB::transaction(function() use ($request) {
				global $tpayment;
				
				$tpayment = new Tpayment;
				$tpayment->no_nota = htmlspecialchars($request->input('no_nota'));
				$tpayment->date = htmlspecialchars($request->input('date'));
				$tpayment->bank_id = htmlspecialchars($request->input('payment_method'));
				$tpayment->transaction_id = htmlspecialchars($request->input('transaction'));

				$tpayment->create_id = Auth::user()->id;
				$tpayment->update_id = Auth::user()->id;
				
				$tpayment->save();

				$transaction = Transaction::find($tpayment->transaction_id);
				$transaction->status = 'Paid';

				$transaction->save();

				Session::forget('save-pay');
			});
			global $tpayment;
			
			$admingroup = Admingroup::find(Auth::user()->admingroup_id);
			if ($admingroup->tpayment_r != true)
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('success-message', "Payment <strong>$tpayment->name</strong> has been created");
			}
			return redirect(Crypt::decrypt($setting->admin_url) . '/tpayment')->with('success-message', "Payment <strong>$tpayment->name</strong> has been created");
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/tpayment/create')->withInput()->withErrors($validator);
		}
	}

	/* Show a resource*/
	public function show(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->tpayment_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/tpayment')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		/*Menu Authentication*/

		$data['navModul'] = true;
		$data['helpModul'] = true;
		$data['searchModul'] = false;
		$data['alertModul'] = true;
		$data['messageModul'] = true;
		
		
		$tpayment = Tpayment::find($id);
		if ($tpayment != null)
		{
			$data['tpayment'] = $tpayment;
			$data['request'] = $request;

			$transactiondetails = Transactiondetail::where('transaction_id', '=', $tpayment->transaction_id)->where('price', '!=', 0)->get();
			// foreach ($transactiondetails as $transactiondetail) {
				// $transactionids[] = $transactiondetail->id;
			// }

			$data['transactiondetails'] = $transactiondetails;
			// $data['transactionids'] = $transactionids;
			
	        return view('back.tpayment.view', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/tpayment')->with('error-message', 'Can not find payment with ID ' . $id);
		}
	}

	/* Edit a resource*/
	public function edit(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/
		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->tpayment_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}
		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->tpayment_u != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/tpayment')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		/*Menu Authentication*/

		$data['navModul'] = true;
		$data['helpModul'] = false;
		$data['searchModul'] = false;
		$data['alertModul'] = true;
		$data['messageModul'] = true;
		
		
		$tpayment = Tpayment::find($id);
		
		if ($tpayment != null)
		{
			$data['tpayment'] = $tpayment;

			$transactions = Transaction::where(function($qr) use ($tpayment) {
				$qr->where('status', '=', 'Waiting for Payment');
				$qr->orWhere('id', '=', $tpayment->transaction_id);
			})->get();
			$transaction_options[''] = 'Select Transaction';
			foreach ($transactions as $transaction) {
				$transaction_options[$transaction->id] = $transaction->trans_id;
			}
			$data['transaction_options'] = $transaction_options;

			$banks = Bank::where('is_active', '=', true)->orderBy('name')->get();
			$bank_options[''] = 'Select Payment Method';
			$bank_options['0'] = 'Cash';
			foreach ($banks as $bank) {
				$bank_options[$bank->id] = $bank->name . ' - ' . $bank->account_number;
			}
			$data['bank_options'] = $bank_options;

			// $transactiondetails = Transactiondetail::where('transaction_id', '=', $tpayment->transaction_id)->where('price', '!=', 0)->get();
			// foreach ($transactiondetails as $transactiondetail) {
				// $transactionids[] = $transactiondetail->ridetail_id;
			// }

			// $data['gettransactiondetails'] = $transactiondetails;

			$data['request'] = $request;

	        return view('back.tpayment.edit', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/tpayment')->with('error-message', 'Can not find image with ID ' . $id);
		}
	}

	public function update(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;
		
		$inputs = $request->all();
		$rules = array(
			'date'				=> 'required',
			'payment_method'				=> 'required',
			'transaction'				=> 'required',
		);

		$validator = Validator::make($inputs, $rules);
		if ($validator->passes())
		{
			$tpayment = Tpayment::find($id);
			if ($tpayment != null)
			{
				$existcheck = Tpayment::where('transaction_id', '=', htmlspecialchars($request->input('transaction')))->first();
				if($existcheck != null)
				{
					$gettransaction = Transaction::find($existcheck->transaction_id);
					return redirect(Crypt::decrypt($setting->admin_url) . '/tpayment/' . $id . '/edit')->withInput()->withErrors("Payment Confirmation for Transaction $gettransaction->trans_id is already exist");
				}
			
				DB::transaction(function() use ($tpayment, $request) {
					$oldtransaction = Transaction::find($tpayment->transaction_id);
					$oldtransaction->status = 'Waiting for Payment';
					$oldtransaction->save();

					$tpayment->date = htmlspecialchars($request->input('date'));
					$tpayment->bank_id = htmlspecialchars($request->input('payment_method'));
					$tpayment->transaction_id = htmlspecialchars($request->input('transaction'));

					$tpayment->update_id = Auth::user()->id;
					
					$tpayment->save();

					$transaction = Transaction::find($tpayment->transaction_id);
					$transaction->status = 'Paid';
					$transaction->save();
					
					Session::forget('save-pay');
				});

				return redirect(Crypt::decrypt($setting->admin_url) . '/tpayment')->with('success-message', "Payment <strong>$tpayment->name</strong> has been updated");
			}
			else
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/tpayment')->with('error-message', 'Can not find image with ID ' . $id);
			}
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/tpayment/' . $id . '/edit')->withInput()->withErrors($validator);
		}
	}


	/* Delete a resource*/
	public function destroy(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->tpayment_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}
		if ($admingroup->tpayment_d != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}
		
		$tpayment = Tpayment::find($id);
		if ($tpayment != null)
		{
			DB::transaction(function() use ($tpayment){
				$transaction = Transaction::find($tpayment->transaction_id);
				$transaction->status = 'Waiting for Payment';
				$transaction->save();

				$tpayment_name = Str::words($tpayment->no_nota, 5);
				$tpayment->delete();
			});

 			return redirect(Crypt::decrypt($setting->admin_url) . '/tpayment')->with('success-message', "Payment <strong>$tpayment->name</strong> has been deleted");
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/tpayment')->with('error-message', 'Can not find payment with ID ' . $id);
		}
	}

	public function getTransaction(Request $request, $id)
	{
		$transaction = Transaction::find($id);
		$data['transaction'] = $transaction;

		// $transactiondetails = Transactiondetail::where('transaction_id', '=', $id)->get();
		// foreach ($transactiondetails as $transactiondetail) {
			// $transactionids[] = $transactiondetail->ridetail_id;
		// }

		// $data['gettransactiondetails'] = $transactiondetails;
		// $data['transactionids'] = $transactionids;

		return view('back.tpayment.transaction', $data);
	}

	public function getPrint(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		$tpayment = Tpayment::find($id);
		if($tpayment != null)
		{
			$data['tpayment'] = $tpayment;

			// $transactiondetails = Transactiondetail::where('transaction_id', '=', $tpayment->transaction_id)->where('price', '!=', 0)->get();
			// $data['transactiondetails'] = $transactiondetails;
			// $data['gettransactiondetails'] = $transactiondetails;

			// foreach ($transactiondetails as $transactiondetail) {
				// $transactionids[] = $transactiondetail->ridetail_id;
			// }

			// $getdetail = Transactiondetail::where('transaction_id', '=', $tpayment->transaction_id)->where('price', '!=', 0)->first();

			$supplier = Supplier::find($tpayment->transaction->supplier_id);
			$data['supplier'] = $supplier;

			return view('back.tpayment.print', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/tpayment')->with('error-message', 'Can not find Payment with ID ' . $id);
		}
	}

	public function getPdf(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		$tpayment = Tpayment::find($id);
		if($tpayment != null)
		{
			$data['tpayment'] = $tpayment;

			$getdetail = Transactiondetail::where('transaction_id', '=', $tpayment->transaction_id)->where('price', '!=', 0)->first();

			$supplier = Supplier::find($tpayment->transaction->supplier_id);
			$data['supplier'] = $supplier;

			$html = \view('back.tpayment.print', $data);
		
			// $pdf = App::make('dompdf.wrapper');
			$pdf = PDF::loadHTML($html);
			return $pdf->setPaper('a4', 'portrait')->stream();

			// return View::make('back.tpayment.print', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/tpayment')->with('error-message', 'Can not find Payment with ID ' . $id);
		}
	}
}