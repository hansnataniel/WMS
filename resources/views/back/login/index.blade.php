<?php
	use App\Models\Setting;
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">

	<link rel="shortcut icon" href="{{URL::to('img/admin/favicon.jpg')}}" />

	<title>
		REMAX Indonesia | Login Page
	</title>

	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	{!!HTML::style('css/back/style.css')!!}
	{!!HTML::style('css/back/login.css')!!}

	{!!HTML::script('js/jquery-1.8.3.min.js')!!}

	<style type="text/css">
		.classsatu {
			position: absolute;
			display: none;
			width: 100px;
			height: 100px;
			background: green;
			top: 0px;
			left: 0px;
			z-index: 9999;
		}
	</style>

	<script type="text/javascript">
		$(document).ready(function(){
			$('body').click(function(){
				$('.classsatu').css({
					'background': "#ff0000"
				});
			});
		});
	</script>
</head>
<body>
	<div class="classsatu"></div>

	<div class="container login-container">
		<div class="mid">
			<div class="login-content">
				{!!HTML::image('img/admin/remax_logo.png', 'REMAX Indonesia', ['class'=>'login-logo', 'title'=>'REMAX Indonesia'])!!}

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

				{!!Form::open(['url' => URL::current()])!!}
					<div class="login-group">
						{!!Form::email('email', null, ['class'=>'login-text', 'placeholder'=>'Username', 'required', 'autofocus'])!!}
						<div class="login-prepend username"></div>
					</div>
					<div class="login-group">
						{!!Form::password('password', ['class'=>'login-text', 'placeholder'=>'Password', 'required'])!!}
						<div class="login-prepend password"></div>
					</div>
					<div class="login-group login-group-signin">
						{!!Form::submit('Sign in', ['class'=>'login-button', 'title'=>'Sign in'])!!}
						<?php
							$setting = Setting::first();
						?>
						<a class="login-link" href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/password/remind')}}">
							Forgot your password?
						</a>
					</div>
					<div class="login-group">
						<span class="login-span">
							<span>
								Backend system version 3.0
							</span>
							Powered by <a href="http://www.creids.net" class="login-powered" title="CREIDS" target="_blank">CREIDS</a>
						</span>
					</div>
				{!!Form::close()!!}
			</div>
		</div>
	</div>
</body>
</html>