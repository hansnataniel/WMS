<?php
	use App\Models\Setting;

	$setting = Setting::first();
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
						<table border="0" style="font-size: 14px; color: #595959; font-family: arial;">
							<tr>
								<td style="padding: 5px 10px; vertical-align: top;">Name</td>
								<td style="padding: 5px 1px; width: 5px; vertical-align: top;">:</td>
								<td style="padding: 5px 2px;">{{$contact->name}}</td>
							</tr>
							<tr>
								<td style="padding: 5px 10px; vertical-align: top;">Email</td>
								<td style="padding: 5px 1px; width: 5px; vertical-align: top;">:</td>
								<td style="padding: 5px 2px;">{{$contact->email}}</td>
							</tr>
							<tr>
								<td style="padding: 5px 10px; vertical-align: top;">Phone</td>
								<td style="padding: 5px 1px; width: 5px; vertical-align: top;">:</td>
								@if($contact->phone != null)
									<td style="padding: 5px 2px;">{{$contact->phone}}</td>
								@else
									<td style="padding: 5px 2px;">-</td>
								@endif
							</tr>
							<tr>
								<td style="padding: 5px 10px; vertical-align: top;">Whatsapp</td>
								<td style="padding: 5px 1px; width: 5px; vertical-align: top;">:</td>
								@if($contact->whatsapp != null)
									<td style="padding: 5px 2px;">{{$contact->whatsapp}}</td>
								@else
									<td style="padding: 5px 2px;">-</td>
								@endif
							</tr>
							<tr>
								<td style="padding: 5px 10px; vertical-align: top;">Line</td>
								<td style="padding: 5px 1px; width: 5px; vertical-align: top;">:</td>
								@if($contact->line != null)
									<td style="padding: 5px 2px;">{{$contact->line}}</td>
								@else
									<td style="padding: 5px 2px;">-</td>
								@endif
							</tr>
							<tr>
								<td style="padding: 5px 10px; vertical-align: top;">Bbm</td>
								<td style="padding: 5px 1px; width: 5px; vertical-align: top;">:</td>
								@if($contact->bbm != null)
									<td style="padding: 5px 2px;">{{$contact->bbm}}</td>
								@else
									<td style="padding: 5px 2px;">-</td>
								@endif
							</tr>
							<tr>
								<td style="padding: 5px 10px; vertical-align: top;">Message</td>
								<td style="padding: 5px 1px; width: 5px; vertical-align: top;">:</td>
								<td style="padding: 5px 2px;">{{nl2br($contact->message)}}</td>
							</tr>
						</table>
					<br><br>

					Best regards, <br>
						
					{{$setting->name}}
					<br><br>
					
					<p>
						
						If you’re having trouble clicking the "Reset Password" button, copy and paste the URL below into your web browser.
						<br><br>
						<span style="color: blue;">
							{{ URL::to(Crypt::decrypt($setting->admin_url) . '/password/reset/' . $token) }}
						</span>
					</p>

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