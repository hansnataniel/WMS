<?php
	use Illuminate\Support\Str;
	use App\Models\Setting;
	use App\Models\Productphoto;
	use App\Models\Transactionitem;
	use App\Models\Bank;
	use App\Models\Product;
	use App\Models\Voucher;

	$setting = Setting::first();

	$productphoto = Productphoto::where('product_id', '=', $product->id)->where('default', '=', 1)->first();
	$transactionitems = Transactionitem::where('transaction_id', '=', $transaction_id)->get();
	$banks = Bank::where('is_active', '=', true)->get();
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
					<br>
					Halo {{$transaction->name}},<br>

					<p>
		               	Maaf pesanan Anda telah kami batalkan.
		               	{{$reason}}<br>
		            </p>

		            <p>
		               	Berikut adalah data transaksi Anda:
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
							<td style="padding: 10px">{{$transaction->no_nota}}</td>
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

					<span>
			            <p>
			                Jika Anda mempunyai pertanyaan atau membutuhkan informasi tambahan silahkan hubungi kami di <a href="{{URL::to('contact-us')}}" target="_blank" style="color: #FDD000; text-decoration: none;">Contact Us</a>
			            </p>
			        </span>
			        <br><br>

					<p>
		                <strong>Remax Customer Service:</strong>
		                <table  border="0" style="font-size: 14px; color: #595959; font-family: arial; border-spacing: 0px;">
		                	@if($setting->email != null)
			                	<tr>
			                		<td style="padding: 0; padding-top:5px;">Email</td>
			                		<td style="padding-top:5px; padding-left: 3px;">:</td>
			                		<td style="padding-top:5px; padding-left: 5px;">{{$setting->email}}</td>
			                	</tr>
		                	@endif
		                	@if($setting->phone != null)
			                	<tr>
			                		<td style="padding: 0; padding-top:5px;">Phone</td>
			                		<td style="padding-top:5px; padding-left: 3px;">:</td>
			                		<td style="padding-top:5px; padding-left: 5px;">{{$setting->phone}}</td>
			                	</tr>
		                	@endif
		                	@if($setting->bbm != null)
			                	<tr>
			                		<td style="padding: 0; padding-top:5px;">BBM</td>
			                		<td style="padding-top:5px; padding-left: 3px;">:</td>
			                		<td style="padding-top:5px; padding-left: 5px;">{{$setting->bbm}}</td>
			                	</tr>
		                	@endif
		                	@if($setting->whatsapp != null)
			                	<tr>
			                		<td style="padding: 0; padding-top:5px;">Whatsapp</td>
			                		<td style="padding-top:5px; padding-left: 3px;">:</td>
			                		<td style="padding-top:5px; padding-left: 5px;">{{$setting->whatsapp}}</td>
			                	</tr>
		                	@endif
		                	<tr>
		                		<td style="padding: 0; padding-top:5px;">Senin - Jumat</td>
		                		<td style="padding-top:5px; padding-left: 3px;">:</td>
		                		<td style="padding-top:5px; padding-left: 5px;">09:00 - 17:00 WIB</td>
		                	</tr>
		                	<tr>
		                		<td style="padding: 0; padding-top:5px;">Sabtu</td>
		                		<td style="padding-top:5px; padding-left: 3px;">:</td>
		                		<td style="padding-top:5px; padding-left: 5px;">09:00 - 16:00 WIB</td>
		                	</tr>
		                </table>
					</p>

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