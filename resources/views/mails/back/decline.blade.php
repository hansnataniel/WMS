<?php
	use Illuminate\Support\Str;
	use App\Models\Setting;
	use App\Models\Transaction;
	use App\Models\Bank;
	use App\Models\Voucher;

	$setting = Setting::first();

	$transaction = Transaction::where('no_nota', '=', $payment->transaction_number)->first();
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
					<p>Halo {{$transaction->name}},</p>

					<p>
						Confirmasi pembayaran Anda telah kami tolak, dengan data sebagai berikut:
		            </p>

					<br>

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
						
					</table>
					<p>
						Silahkan hubungi kami untuk informasi lebih lanjut.
					</p>

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