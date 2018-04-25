<?php
	use Illuminate\Support\Str;
	use App\Models\Setting;
	use App\Models\Productphoto;
	use App\Models\Product;
	use App\Models\Bank;
	use App\Models\Transaction;
	use App\Models\Transactionitem;
	use App\Models\Voucher;
	use App\Models\Rate;
	use App\Models\Service;
	use App\Models\Expedition;

	$setting = Setting::first();

	$transaction = Transaction::where('no_nota', '=', $payment->transaction_number)->first();
	$transactionitems = Transactionitem::where('transaction_id', '=', $transaction->id)->get();

	$bank = Bank::find($payment->bank_id);
?>

<html>
	<head>
		<title>{{$setting->name}}</title>
	</head>
	<body>
		<table id="wrapper" style="font-size: 14px; color: #0d0f3b; font-family: arial; width: 100%; line-height: 20px;">
			<tr>
				<td id="header-container" style="padding: 10px 20px; text-align: center;">
					{{HTML::image('img/admin/creids_logo.png', '', array('style'=>'width: 200px;'))}}
				</td>
			</tr>
			<tr>
				<td id="section-container" style="padding: 20px;">
					Halo {{$setting->receiver_email_name}},<br>

					<br>
					Ada konfirmasi pembayaran baru yang masuk dengan data sebagai berikut:<br>

					<table border="0" style="font-size: 14px; color: #595959; font-family: arial;">
						<tr>
							<td style="padding: 5px 10px; padding-left: 0px;">Transaction ID</td>
							<td style="padding: 5px 10px;">{{$payment->transaction_number}}</td>
						</tr>
						<tr>
							<td style="padding: 5px 10px; padding-left: 0px;">Name</td>
							<td style="padding: 5px 10px;">{{$payment->name}}</td>
						</tr>
						<tr>
							<td style="padding: 5px 10px; padding-left: 0px;">Amount To Pay (Rp)</td>
							<td style="padding: 5px 10px;">{{rupiah3($payment->amount)}}</td>
						</tr>
						<tr>
							<td style="padding: 5px 10px; padding-left: 0px;">Transfer To</td>

							<td style="padding: 5px 10px;">{{$bank->name . ' | ' . $bank->account_number . ' | ' . $bank->account_name}}</td>
						</tr>
						<tr>
							<td style="padding: 5px 10px; padding-left: 0px;">Transfer From</td>
							<td style="padding: 5px 10px;">{{$payment->bank . ' | ' . $payment->account_number . ' | ' . $payment->account_name}}</td>
						</tr>
						<tr>
							<td style="padding: 5px 10px; padding-left: 0px;">Transfer Date</td>
							<td style="padding: 5px 10px;">{{tanggal2($payment->date_transfer)}}</td>
						</tr>
						
					</table><br>
					<p>
						Dengan data transaksi sebagai berikut:
					</p>
					<table border="0" style="font-size: 14px; color: #595959; font-family: arial; border-spacing: 0px; border: solid 1px #dbdbdb; border-top: none;">
						<tr>
							<td style="padding: 10px; background: #FDD000; color: #000;">
								Date
							</td>
							<td style="padding: 10px; background: #FDD000; color: #000;">
								Transaction ID
							</td>
							<td style="padding: 10px; background: #FDD000; color: #000;">
								Amount To Pay
							</td>
						</tr>
						<tr>
							<td style="padding: 10px">{{date('d F Y', strtotime($transaction->created_at))}}</td>
							<td style="padding: 10px">{{$transaction->id + 1000}}</td>
							<td style="padding: 10px">Rp {{rupiah3($transaction->amount_to_pay)}}</td>
						</tr>
						<tr>
							<td colspan="3" style="padding: 10px; background: #FDD000; color: #000;">
								Order Summary
							</td>
						</tr>
						<?php
							$counter = 0;
						?>
						@foreach ($transactionitems as $transactionitem)
							<?php 
								$counter++; 
								$product = Product::find($transactionitem->product_id);
							?>
							<tr>
								@if($counter == 1)
									<td colspan="3" style="padding: 10px; line-height: 18px;">
								@else
									<td colspan="3" style="padding: 10px; padding-top: 0; line-height: 18px;">
								@endif
									{{$product->name}}<br>
		                            <span style="font-size: 12px;">Price: Rp {{rupiah3($transactionitem->price_afterdiscount)}} . Quantity: {{$transactionitem->qty}}</span>
								</td>
							</tr>
						@endforeach
						@if($transaction->voucher != null)
							<tr>
	                            <?php $voucher = Voucher::where('code', '=', $transaction->voucher)->first(); ?>
	                            <td colspan="3" style="padding: 10px; padding-top: 0; line-height: 18px;">
                            		VOUCHER<br>
	                            	<span style="font-size: 12px;">{{$transaction->voucher}} - 
	                            		@if($voucher->type == false)
		                                    Rp {{rupiah3($voucher->value)}}
		                                @else
		                                    {{rupiah3($voucher->value)}}% (Rp {{rupiah3($transaction->total * $voucher->value / 100)}})
		                                @endif
	                            	</span>
		                        </td>
							</tr>
                        @endif
                        @if($transaction->rate_price != 0)
                            <tr>
	                            <td colspan="3" style="padding: 10px; padding-top: 0; line-height: 18px;">
	                            	DELIVERY COST<br>
		                            <span style="font-size: 12px;">Rp {{rupiah3($transaction->delivery_cost)}}</span>
	                            </td>
                            </tr>
                        @endif
					</table><br>

					Untuk melihat detail konfirmasi pembayaran silahkan klik <a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/payment/view/' . $payment->id)}}" target="_blank" style="color: #FDD000; text-decoration: none;">disini</a>.
					<br><br>

					Best regards, <br>
						
					{{$setting->name}}
					<br>

					<br>
				</td>
			</tr>
			<tr>
				<td class="not-reply" style="font-size: 11px; line-height: 13px;padding-left: 20px;">
					<i>
						*This email was sent from a notification-only address that cannot accept incoming emails. Please do not reply to this email.
					</i>
					<br><br>
				</td>
			</tr>
			<tr>
				<td  id="footer-container" style="padding: 10px 20px; color: #fff; background: #f7961e; text-align: center">
					<span>
						© {{date('Y')}} - {{$setting->name}}
					</span>
				</td>
			</tr>
		</table>
	</body>
</html>