<?php
	use App\Models\Setting;
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">

	<link rel="shortcut icon" href="{{URL::to('img/admin/favicon.jpg')}}" />

	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title>
		REMAX Indonesia | Forgot Password
	</title>

	{!!HTML::style('css/back/style.css')!!}
	{!!HTML::style('css/back/login.css')!!}
</head>
<body>
	<div class="container login-container">
		<div class="mid">
			<div class="login-content">
				<h1 class="login-header">
					RESET PASSWORD
					<span>
						Fill this form to reset your password
					</span>
				</h1>

				{{-- 
					Alert
				 --}}
				<div class="login-alert-container">
					@foreach ($errors->all() as $error)
						<div class="login-alert-item">
							{{$error}}
						</div>
					@endforeach
					@if ($request->session()->has('message'))
						<div class="login-alert-item">
							{!!$request->session()->get('message')!!}
						</div>
					@endif
					@if ($request->session()->has('success'))
						<div class="login-alert-item success">
							{!!$request->session()->get('success')!!}
						</div>
					@endif
				</div>

				{!!Form::open()!!}
					<div class="login-group">
						{!!Form::email('old_email', null, ['class'=>'login-text', 'placeholder'=>'Old Email', 'required', 'autofocus'])!!}
						<div class="login-prepend mail"></div>
					</div>
					<div class="login-group">
						{!!Form::password('new_password', ['class'=>'login-text', 'placeholder'=>'New Password', 'required'])!!}
						<div class="login-prepend password"></div>
					</div>
					<div class="login-group">
						{!!Form::password('new_password_confirmation', ['class'=>'login-text', 'placeholder'=>'New Password Confrimation', 'required'])!!}
						<div class="login-prepend password"></div>
					</div>
					<div class="login-group">
						{!!Form::submit('Reset', ['class'=>'login-button forgot-button', 'title'=>'Sign in'])!!}
					</div>
				{!!Form::close()!!}
			</div>
		</div>
	</div>
</body>
</html>