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

use App\Models\Area;
use App\Models\Province;
use App\Models\Rate;
use App\Models\Voucher;
use App\Models\Transaction;
use App\Models\Transactionitem;
use App\Models\Product;
use App\Models\History;
use App\Models\Bank;
use App\Models\Payment;
use App\Models\Inventory;



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
use Cart;
use DB;


class CheckoutController extends Controller
{
	/* Get the list of the resource*/
	public function getStep1(Request $request)
	{
		if($request->session()->get('member_email') == null)
		{
			return redirect('sign-in');
		}

		$setting = Setting::first();
		$data['setting'] = $setting;

		$carts = Cart::contents();
		if(count($carts) == 0)
		{
			return redirect('/');
		}
		$data['carts'] = $carts;

		$areas = Area::where('is_active', '=', 1)->orderBy('name', 'asc')->get();
		$area_options[''] = 'Pilih area (kecamatan, kota)';
		if(count($areas) != 0)
		{
			foreach ($areas as $area) 
			{
				$area_options[$area->id] = $area->name;
			}
		}
		$data['area_options'] = $area_options;

		$provinces = Province::where('is_active', '=', 1)->orderBy('name', 'asc')->get();
		$province_options[''] = 'Pilih provinsi';
		if(count($provinces) != 0)
		{
			foreach ($provinces as $province) 
			{
				$province_options["$province->id"] = "$province->name";
			}
		}
		$data['province_options'] = $province_options;
		
		return view('front.cart.checkout_step1', $data);
	}

	public function postStep1(Request $request)
	{
		$setting = Setting::first();
		$inputs = $request->all();
		$rules = array(
			'nama_pengirim'			=> 'required',
			'alamat_pengirim'		=> 'required',
			'nama_penerima'			=> 'required',
			'alamat_penerima'		=> 'required',
			'area_alamat_penerima'	=> 'required',
			'layanan_pengiriman' 	=> 'required',
			'telepon'			 	=> 'required',
		);

		$validator = Validator::make($inputs, $rules);
		if ($validator->passes())
		{
			DB::transaction(function () use ($request, $setting) {
				global $transaction;
				global $product;
			    
				$transaction = new Transaction;

				$lasttransaction = Transaction::orderBy('id', 'desc')->first();
				if($lasttransaction == null)
				{
					$no_nota = 'S/' . date('ymd') . '/1001';
				}
				else
				{
					$no_nota = 'S/' . date('ymd') . '/' . ($lasttransaction->id + 1001);
				}

				$transaction->no_nota = $no_nota;

				if(Auth::guest() == null)
				{
					$transaction->user_id = Auth::user()->id;
					$transaction->type = 'Member';
				}
				else
				{
					$transaction->user_id = 0;
					$transaction->type = 'Not Member';
				}

				$transaction->date = date('Y-m-d');

				$transaction->area_id = htmlspecialchars($request->input('area_alamat_penerima'));
				$transaction->rate_id = htmlspecialchars($request->input('layanan_pengiriman'));
				$transaction->name = htmlspecialchars($request->input('nama_penerima'));
				$transaction->phone = htmlspecialchars($request->input('telepon'));
				$transaction->address = htmlspecialchars($request->input('alamat_penerima'));
				$transaction->email = $request->session()->get('member_email');
				$transaction->total = htmlspecialchars($request->input('total'));
				$transaction->amount_to_pay = htmlspecialchars($request->input('amount_to_pay'));
				$transaction->voucher = htmlspecialchars($request->input('voucher_code'));
				$transaction->rate = 0;
				$transaction->rate_price = htmlspecialchars($request->input('rate_price'));
				$transaction->weight_total = htmlspecialchars($request->input('weight_total'));
				$transaction->delivery_cost = $transaction->rate_price * $transaction->weight_total;
				$transaction->sender = htmlspecialchars($request->input('nama_pengirim'));
				$transaction->sender_address = htmlspecialchars($request->input('alamat_pengirim'));
				$transaction->message = htmlspecialchars($request->input('pesan'));
				$transaction->status = 'Waiting for payment';
				$transaction->save();

				if($transaction->voucher != null)
				{
					$voucher = Voucher::where('code', '=', $transaction->voucher)->first();
					$voucher->used = $voucher->used + 1;
					if($voucher->used == $voucher->available)
					{
						$voucher->status = 'Used';
					}
					$voucher->save();
				}

				$transaction_code = $transaction->no_nota;

				$carts = Cart::contents();
				foreach ($carts as $cart) 
				{
					$transactionitem = new Transactionitem;
					$transactionitem->transaction_id = $transaction->id;
					$transactionitem->product_id = $cart->id;
					$transactionitem->price = $cart->product_price;
					$transactionitem->price_afterdiscount = $cart->price;
					$transactionitem->qty = $cart->quantity;
					$transactionitem->save();

					/*edit stock product*/
					$product = Product::find($cart->id);
					/*create history*/
					$history = new History;
					$history->history_id = $transaction->no_nota;
					$history->product_id = $product->id;
					$history->amount = $transactionitem->qty;
					$history->last_stock = $product->stock;
					$history->final_stock = $product->stock - $cart->quantity;
					$history->status = 'Sale';
					$history->note = '';
					$history->save();

					/*
						Insert into Inventory
					*/
					$inventory = new Inventory;
					$inventory->date = date('Y-m-d');
					$inventory->product_id = $cart->id;
					$inventory->type = 'S';
					$inventory->type_id = $transactionitem->id;

					$getlastinv = Inventory::where('product_id', '=', $cart->id)->where('date', '<=', date('Y-m-d'))->orderBy('date', 'desc')->orderBy('id', 'desc')->first();

					if($getlastinv == null)
					{
						$inventory->qty_last = 0;

						$inventory->qty_z = -$cart->quantity;
					}
					else
					{
						$inventory->qty_last = $getlastinv->qty_z;

						$inventory->qty_z = $getlastinv->qty_z - $cart->quantity;
					}

					$inventory->qty_out = $cart->quantity;
					$inventory->qty_in = 0;
					$inventory->save();
				

					$product->stock = $product->stock - $cart->quantity;
					$product->save();

					update_inventory($cart->id, date('Y-m-d'), $inventory->id);
				}

				$subject = "Data dan Konfirmasi Pembayaran Pesanan Anda " . $setting->name . ".";
 				$transaction = $transaction;
 				$product = $product;

				Mail::to($transaction->email)
				    ->send(new transactiontouser($transaction->email, $subject, $product, $transaction));


				$subject2 = "Ada pesanan baru dari " . $transaction->name;
				Mail::to($setting->receiver_email)
				    ->send(new transactiontoadmin($setting->receiver_email, $subject2, $product, $transaction));

			});

			global $transaction;
			global $product;

			if(Auth::guest() != null)
			{
				$request->session()->forget('member_email');
			}

			Cart::destroy();

			$convertnonota = str_replace('/', '-', $transaction->no_nota);
			
			return redirect('checkout/step2/' . $convertnonota);
		}
		else
		{
			return redirect('checkout/step1')->withInput()->withErrors($validator);
		}
	}

	public function getCekemail(Request $request, $product_id)
	{
		$data['product_id'] = $product_id;
		return view('emails.to_member.notification', $data);
	}

	public function getAjaxCheckout(Request $request, $voucher, $layanan_id)
	{
		$carts = Cart::contents();
		$data['carts'] = $carts;

		$setting = Setting::first();

		$price_total = 0;
		$minimal_transaksi = 0;
		$weight_total = 0;
		$min_transaction = false;
		
		foreach ($carts as $cart) 
		{
			$price_total = $price_total + $cart->price_total;
			$weight_total = $weight_total + ($cart->weight * $cart->quantity);
		}
		$weight_tolerance = weight_total($weight_total);

		/*cek voucher*/
		$cek_vouceher = Voucher::where('code', '=', $voucher)->first();
		if(($cek_vouceher != null) AND ($cek_vouceher->available > $cek_vouceher->used) AND ($cek_vouceher->status == 'Unused'))
		{
			$minimal_transaksi = number_format($cek_vouceher->min_transaction, 0, '.', '.');
			if($cek_vouceher->available > $cek_vouceher->used)
			{
				if($cek_vouceher->min_transaction < $price_total)
				{
					if($cek_vouceher->type != 0)
					{
						$price_total = $price_total - $cek_vouceher->value;
					}
					else
					{
						$voucher_value = ($price_total * $cek_vouceher->value) / 100;
						$price_total = $price_total - $voucher_value;
					}

					if($price_total < 0)
					{
						$price_total = 0;
					}
				}
				else
				{
					$min_transaction = true;
				}
			}
		}
		else
		{
			$cek_vouceher = null;
		}

		/*cek shipping cost*/
		$rate_price = 0;
		if($setting->is_free == 0)
		{
			if($layanan_id != 0)
			{
				$rate = Rate::find($layanan_id);
				if($rate != null)
				{
					$rate_price = $rate->price;
					$price_total = $price_total + ($rate_price * $weight_tolerance);
				}
			}

			// dd('done');
		}
		else
		{
			if($price_total <= $setting->free_delivery)
			{
				if($layanan_id != 0)
				{
					$rate = Rate::find($layanan_id);
					if($rate != null)
					{
						$rate_price = $rate->price;
						$price_total = $price_total + ($rate_price * $weight_tolerance);
					}
				}
			}
		}

		$data['voucher'] = $voucher;
		$data['cek_vouceher'] = $cek_vouceher;
		$data['minimal_transaksi'] = $minimal_transaksi;
		$data['min_transaction'] = $min_transaction;
		$data['price_total'] = $price_total;
		$data['rate_price'] = $rate_price;

		$data['weight_tolerance'] = $weight_tolerance;
				
		return view('front.cart.checkout_ajax', $data);
	}

	public function getAjaxCity(Request $request, $province_id)
	{
		$areas = Area::where('province_id', '=', $province_id)->where('is_active', '=', 1)->orderBy('name', 'asc')->get();
		$area_options[''] = 'Pilih area (kecamatan, kota)';
		if(count($areas) != 0)
		{
			foreach ($areas as $area) 
			{
				$area_options["$area->id"] = "$area->name";
			}
		}
		$data['area_options'] = $area_options;

		return view('front.cart.area_ajax', $data);
	}

	public function getAjaxRate(Request $request, $area_id)
	{
		$area = Area::find($area_id);
		if($area != null)
		{
			$carts = Cart::contents();
			$weight_total = 0;
			if (!empty($carts)) {
				foreach ($carts as $cart)
				{
					$weight_total = $weight_total + ($cart->weight * $cart->quantity);
				}
				$weight_tolerance = weight_total($weight_total);
			}
			$data['weight_tolerance'] = $weight_tolerance;


			$rates = Rate::where('area_id', '=', $area->id)->orderBy('service_id', 'asc')->get();
			$data['rates'] = $rates;

			return view('front.cart.rate_ajax', $data);
		}
	}

	public function getStep2(Request $request, $transaction_code)
	{
		$convertnonota = str_replace('-', '/', $transaction_code);

		$transaction = Transaction::where('no_nota', '=', $convertnonota)->where('status', '=', 'Waiting for payment')->first();
		if($transaction != null)
		{
			$data['transaction'] = $transaction;
			$data['transaction_code'] = $transaction_code;

			$transactionitems = Transactionitem::where('transaction_id', '=', $transaction->id)->get();
			$data['transactionitems'] = $transactionitems;

			$banks = Bank::where('is_active', '=', 1)->get();
			$data['banks'] = $banks;
			
			return view('front.cart.checkout_step2', $data);
		}
		else
		{
			return redirect('/');
		}
	}

	public function getStep3(Request $request, $transaction_code)
	{
		$convertnonota = str_replace('-', '/', $transaction_code);
		$data['transaction_url'] = $transaction_code;
		$data['transaction_code'] = $convertnonota;

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
		
		return view('front.cart.checkout_step3', $data);
	}

	public function postStep3(Request $request, $transaction_code)
	{
		$setting = Setting::first();
		$transaction_code = $request->input('transaction_id');


		$transaction_url = str_replace('/', '-', $transaction_code);

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
			$transaction = Transaction::where('no_nota', '=', $transaction_code)->where('status', '=', 'Waiting for payment')->first();
			if($transaction == null)
			{
				return redirect('checkout/step3/' . $transaction_url)->withInput()->with('success-message', "Your Transaction ID invalid.");
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

			return redirect('/')->with('success-message', "Your payment confirmation has been sent");
		}
		else
		{
			return redirect('checkout/step3/' . $transaction_url)->withInput()->withErrors($validator);
		}
	}
}