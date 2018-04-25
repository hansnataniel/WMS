<?php
	use Illuminate\Support\Str;
	use App\Models\Setting;
	use App\Models\User;


	$setting = Setting::first();

	$code = Crypt::encrypt($user->id);
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
					Halo {{$user->name}},<br><br>

					<p>
						Terima kasih sudah mendaftar untuk bergabung di Remax Indonesia. Untuk mengaktivasikan akun Anda silahkan klik <a href="{{URL::to('activation' . '/' . $code)}}" style="color: #FDD000; text-decoration: none; font-family: arial;">di sini.</a>
					</p>
					
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