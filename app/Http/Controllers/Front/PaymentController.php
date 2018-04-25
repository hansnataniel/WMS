<?php

/*
	Use Name Space Here
*/
namespace App\Http\Controllers\Front;

/*
	Call Model Here
*/
use App\Models\Setting;
use App\Models\Admingroup;
use App\Models\Que;

use App\Models\Bank;
use App\Models\Payment;
use App\Models\Transaction;



/*
	Call Mail file & mail facades
*/
use App\Mail\Front\transactiontouser;
use App\Mail\Front\transactiontoadmin;
use App\Mail\Front\paymenttoadmin;

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


class PaymentController extends Controller
{
	/* Get the list of the resource*/
	public function index(Request $request)
	{
		$request->session()->forget('pay_code');
		
		$banks = Bank::where('is_active', '=', 1)->orderBy('name', 'asc')->get();
		if(count($banks) != 0)
		{
			foreach ($banks as $bank) 
			{
				$bank_options[$bank->id] = $bank->name . ' - ' . $bank->account_number;
			}
		}
		else
		{
			$bank_options[''] = 'Bank not found';
		}
		$data['bank_options'] = $bank_options;

		$data['transaction_code'] = '';
		
		return view('front.payment.index', $data);
	}

	public function store(Request $request)
	{
		$setting = Setting::first();
		$transaction_code = $request->input('transaction_id');
		$inputs = $request->all();
		$rules = array(
			'transaction_id'		=> 'required',
			'amount_transfered'		=> 'required',
			'transfer_to' 			=> 'required',
			'your_bank'				=> 'required',
			'your_account_number'	=> 'required',
			'your_account_name'		=> 'required',
			'transfer_date'		 	=> 'required',
		);

		$validator = Validator::make($inputs, $rules);
		if ($validator->passes())
		{
			$transaction = Transaction::where('no_nota', '=', $request->input('transaction_id'))->where('status', '=', 'Waiting for payment')->first();
			if($transaction == null)
			{
				if($request->session()->get('pay_code') == null)
				{
					return redirect('payment-confirmation')->withInput()->with('success-message', "Your Transaction ID invalid.");
				}
				else
				{
					$convertcode = str_replace('/', '-', $request->session()->get('pay_code'));
					return redirect('payment-confirmation/pay/' . $convertcode)->withInput()->with('success-message', "Your Transaction ID invalid.");
				}
			}

			$payment = new Payment;
			$payment->transaction_number = htmlspecialchars($request->input('transaction_id'));
			$payment->bank_id = htmlspecialchars($request->input('transfer_to'));
			$payment->name = $transaction->name;
			$payment->email = $transaction->email;
			$payment->bank = htmlspecialchars($request->input('your_bank'));
			$payment->account_number = htmlspecialchars($request->input('your_account_number'));
			$payment->account_name = htmlspecialchars($request->input('your_account_name'));
			$payment->amount = htmlspecialchars($request->input('amount_transfered'));
			$payment->date_transfer = htmlspecialchars($request->input('transfer_date'));
			$payment->status = 'Waiting for confirmation';
			
			$payment->confirm_at = date('Y-m-d H:i:s');
			$payment->confirm_id = 0;
			$payment->decline_at = date('Y-m-d H:i:s');
			$payment->decline_id = 0;
			$payment->save();

			$subject = "Ada konfirmasi pembayaran baru dari " . $transaction->name;

			Mail::to($setting->receiver_email)
			    ->send(new paymenttoadmin($subject, $payment));

			if($request->session()->get('pay_code') == null)
			{
				return redirect('payment-confirmation')->with('success-message', "Your payment confirmation has been sent");
			}
			else
			{
				$convertcode = str_replace('/', '-', $request->session()->get('pay_code'));
				return redirect('payment-confirmation/pay/' . $convertcode)->with('success-message', "Your payment confirmation has been sent");
			}
		}
		else
		{
			if($request->session()->get('pay_code') == null)
			{
				return redirect('payment-confirmation')->withInput()->withErrors($validator);
			}
			else
			{
				return redirect('payment-confirmation/pay/' . $request->session()->get('pay_code'))->withInput()->withErrors($validator);
			}
		}
	}

	public function getPay(Request $request, $transaction_code)
	{
		$transaction_code = str_replace('-', '/', $transaction_code);
		$banks = Bank::where('is_active', '=', 1)->orderBy('name', 'asc')->get();
		if(count($banks) != 0)
		{
			foreach ($banks as $bank) 
			{
				$bank_options[$bank->id] = $bank->name . ' - ' . $bank->account_number;
			}
		}
		$data['bank_options'] = $bank_options;

		$data['transaction_code'] = $transaction_code;
		$request->session()->put('pay_code', $transaction_code);
		
		return view('front.payment.index', $data);
	}
}