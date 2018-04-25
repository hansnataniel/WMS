<?php
	use App\Models\Payment;
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">

	<title>
		REMAX | @yield('title')
	</title>

	{!!HTML::style('css/back/style.css')!!}
	{!!HTML::style('css/back/general.css')!!}
	{!!HTML::style('css/back/menu.css')!!}

	<link rel="shortcut icon" href="{{URL::to('img/front/remax_fav_icon.png')}}" />

	{{-- META TAGS --}}
	<meta name="description" content="@yield('metadesc')">
    <meta name="keywords" content="@yield('metakey')">
	<meta name="robot" content="INDEX,FOLLOW">
	<meta name="copyright" content="CREIDS">
	<meta name="language" content="id">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	{{-- 
		CSS Additional
	 --}}

	{!!HTML::style('css/select2.css')!!}

	@yield('head_additional')
</head>
<body>
	
	<header class="header-container">
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/dashboard')}}" class="header-logo" title="REMAX"></a>
		<div class="header-icon-container">
			@if($alertModul == true)
				<?php
					$checkproducts = DB::table('products')->where('is_active', '=', true)->whereRaw('products.stock <= products.min_stock')->get();
					// $checkproducts = Product::where('is_active', '=', true)->where('stock', '<=', $)->get();

					$total = 0;
					if(!$checkproducts->isEmpty())
					{
						$total += count($checkproducts);
					}
				?>
				<div class="header-icon-item header-notification-icon">
					@if(!$checkproducts->isEmpty())
						<div class="header-icon-alert">
							@if($total <= 9)
								{{$total}}
							@else
								9+
							@endif
						</div>

						{{-- <div class="header-icon-drop-arrow"></div> --}}
						@if($searchModul == true)
							<ul class="header-icon-drop-container">
						@else
							<ul class="header-icon-drop-container five">
						@endif
							@if(!$checkproducts->isEmpty())
								@foreach($checkproducts as $checkproduct)
									<li>
										<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/po/create')}}">
											<strong>{{$checkproduct->name}}</strong> is out of stock
										</a>
									</li>
								@endforeach
							@endif
						</ul>
					@else
						@if($searchModul == true)
							<ul class="header-icon-drop-container">
						@else
							<ul class="header-icon-drop-container five">
						@endif
							<li>
								Your notification is empty
							</li>
						</ul>
					@endif
				</div>
			@endif
			@if($messageModul == true)
				
			@endif
			@if($searchModul == true)
				<div class="header-icon-item header-search-icon"></div>
			@endif
			@if($helpModul == true)
				<div class="header-icon-item header-hint-icon"></div>
			@endif
			@if($navModul == true)
				<div class="header-icon-item header-menu-icon">
					<div class="mid">
						<div class="header-menu-line-container">
							<div class="header-menu-line line1"></div>
							<div class="header-menu-line line2"></div>
							<div class="header-menu-line line3"></div>
						</div>
					</div>
				</div>
			@endif
		</div>
	</header>

	<nav class="menu-container navigation">
		@include('back.template.nav')
	</nav>

	<div class="menu-container help">
		<div class="menu-group">
			<div class="menu-title">
				Help
			</div>
			@yield('help')
		</div>
	</div>

	<div class="menu-container search">
		@yield('search')
	</div>

	<div class="page-title-container">
		<h1 class="page-title-h1">
			@yield('page_title')
		</h1>
	</div>

	<div class="page-content">
		@yield('content')
	</div>

	<footer class="footer-container">
		© 2017 Backend system version 3.0<span> . </span><br>Designed and developed by {!!HTML::link('http://www.creids.net', 'CREIDS', ['id'=>'footer-link', 'target'=>'_blank'])!!}
		<?php
			/*
				Menampilkan sebagai tag HTML
			*/
			$generator = new Picqer\Barcode\BarcodeGeneratorHTML();
			// echo $generator->getBarcode('123123098', $generator::TYPE_CODE_128);
			
			/*
				Menampilkan sebagai file gambar PNG
			*/
			$generator1 = new Picqer\Barcode\BarcodeGeneratorPNG();
			// echo '<img src="data:image/png;base64,' . base64_encode($generator1->getBarcode('dj-123', $generator1::TYPE_CODE_128)) . '">';

			/*
				Menampilkan sebagai file gambar JPG
			*/
			$generator2 = new Picqer\Barcode\BarcodeGeneratorJPG();
			// echo '<img src="data:image/jpg;base64,' . base64_encode($generator2->getBarcode('123123098', $generator2::TYPE_CODE_128)) . '">';
		?>
	</footer>

	{{-- 
		Alert full page with custom content
	 --}}

	<div class="pop-container">
		<div class="mid">
			<div class="pop-content">
				<div class="pop-result"></div>
			</div>
		</div>
	</div>

	@if ($request->session()->has('success-message'))
		<div class='message-fixed success-message'>
			{!!$request->session()->get('success-message')!!}
		</div>
	@endif
	@if ($request->session()->has('warning-message'))
		<div class='message-fixed warning-message'>
			{!!$request->session()->get('warning-message')!!}
		</div>
	@endif
	@if ($request->session()->has('error-message'))
		<div class='message-fixed error-message'>
			{!!$request->session()->get('error-message')!!}
		</div>
	@endif

	{{-- 
		JS Additional
	 --}}

	{!!HTML::script('js/jquery-1.8.3.min.js')!!}
	{!!HTML::script('js/jquery.easing.1.3.js')!!}
	{!!HTML::script('js/ckeditor/ckeditor.js')!!}
	{!!HTML::script('js/select2.js')!!}

	<script type="text/javascript">
		function indexSwitchOff () {
			$('.index-action-switch').removeClass('active');
			$('.index-action-child-container').fadeOut();
			$('.index-action-switch').find($('li')).delay(100).animate({
                opacity: 0,
                top: 30
            }, 0);
		}

		function menuContainerClose () {
			$('.header-icon-item').removeClass('active');
			$('.menu-container').stop().fadeOut(200);
			$('.menu-group').stop().delay(100).animate({
				'left': 50,
				'opacity': 0
			}, 0);
			$('.menu-link').stop().delay(100).animate({
				'left': 50,
				'opacity': 0
			}, 0);
			$('.menu-group li').stop().delay(100).animate({
				'left': 50,
				'opacity': 0
			}, 0);
			$('.menu-search-group').stop().delay(100).animate({
				'left': 50,
				'opacity': 0
			}, 0);

			$('.header-icon-drop-container').stop().fadeOut(200);
			$('.header-icon-drop-arrow').stop().fadeOut(200);

			$('.header-icon-drop-container li a').stop().animate({
				'opacity': 0,
				'top': 50
			}, 200);
		}

		function menuSwitchClose () {
			$('.menu-switch').removeClass('active');
			$('.menu-sub-menu-container').stop().slideUp(300);
			$('.menu-sub-menu-link').stop().animate({
				'opacity': 0,
				'top': 50
			}, 200);
		}

		function number_format (number, decimals, dec_point, thousands_sep) {
		    // Strip all characters but numerical ones.
		    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
		    var n = !isFinite(+number) ? 0 : +number,
		        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
		        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
		        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
		        s = '',
		        toFixedFix = function (n, prec) {
		            var k = Math.pow(10, prec);
		            return '' + Math.round(n * k) / k;
		        };
		    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
		    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
		    if (s[0].length > 3) {
		        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
		    }
		    if ((s[1] || '').length < prec) {
		        s[1] = s[1] || '';
		        s[1] += new Array(prec - s[1].length + 1).join('0');
		    }
		    return s.join(dec);
		}
	</script>

	<script type="text/javascript">
		$(document).ready(function(){
			$('body').click(function(){
				indexSwitchOff();
				menuContainerClose();
				headerIconClose();
			});

			$('.header-notification-icon').click(function(e){
				e.stopPropagation();

				if($(this).hasClass('active'))
				{
					// headerIconClose();
					menuContainerClose();
				}
				else
				{
					menuContainerClose();
					$(this).parent().find('.header-icon-drop-container').fadeIn();
					$(this).parent().find('.header-icon-drop-arrow').fadeIn();

					$(this).parent().find('li a').each(function(e){
						$(this).delay(e*50).animate({
							'opacity': 1,
							'top': 0
						}, 300);
					});

					$(this).addClass('active');
				}
			});

			$('.header-hint-icon').click(function(e){
				e.stopPropagation();

				if($(this).hasClass('active'))
				{
					menuContainerClose();
				}
				else
				{
					menuContainerClose();

					$(this).addClass('active');

					$('.help').stop().fadeIn();

					$('.help .menu-group').each(function(e){
						$(this).delay(e*70).animate({
							'opacity': 1,
							'left': 0
						}, 500, "easeInOutCubic");

						$(this).find('li').each(function(i){
							$(this).delay(i*100).animate({
								'opacity': 1,
								'left': 0
							}, 500, "easeInOutCubic");
						});
					});
				}
			});

			$('.header-menu-icon').click(function(e){
				e.stopPropagation();

				if($(this).hasClass('active'))
				{
					menuContainerClose();
				}
				else
				{
					menuContainerClose();

					$(this).addClass('active');

					$('.navigation').stop().fadeIn();

					$('.navigation .menu-group').each(function(e){
						$(this).delay(e*70).animate({
							'opacity': 1,
							'left': 0
						}, 500, "easeInOutCubic");

						$(this).find('.menu-link').each(function(i){
							$(this).delay(i*50).animate({
								'opacity': 1,
								'left': 0
							}, 500, "easeInOutCubic");
						});
					});
				}
			});

			$('.header-search-icon').click(function(e){
				e.stopPropagation();

				if($(this).hasClass('active'))
				{
					menuContainerClose();
				}
				else
				{
					menuContainerClose();

					$(this).addClass('active');

					$('.search').stop().fadeIn();

					$('.search .menu-group').each(function(e){
						$(this).delay(e*70).animate({
							'opacity': 1,
							'left': 0
						}, 500, "easeInOutCubic");

						$(this).find('.menu-search-group').each(function(i){
							$(this).delay(i*100).animate({
								'opacity': 1,
								'left': 0
							}, 500, "easeInOutCubic");
						});
					});
				}
			});

			$('.menu-switch').click(function(){
				if($(this).hasClass('active'))
				{
					menuSwitchClose();
				}
				else
				{
					menuSwitchClose();

					$(this).addClass('active');
					$(this).find('.menu-sub-menu-container').stop().slideDown();
					$(this).find('.menu-sub-menu-link').each(function(e){
						$(this).delay(e*60).animate({
							'opacity': 1,
							'top': 0
						}, 250);
					});
				}
			});

			$('.pop-container').click(function(){
				$('.pop-container').fadeOut();
				setTimeout(function(){
					$('.pop-result').html('');
					$('.index-del-item').animate({
						'opacity': 0,
						'top': 50
					}, 0);
				}, 300);
			});

			$('.pop-result').click(function(e){
				e.stopPropagation();
			});

			$('.menu-container').click(function(e){
				e.stopPropagation();
			});

			$('.select').each(function(){
				var data = $(this).attr('placeholder-data');

				$(this).select2({
					placeholder: data
				});
			});

			$('.message-fixed').click(function(){
				$(this).fadeOut();
				// $('body').fadeOut();
				// alert('done');
			});

			setTimeout(function(){
				$('.message-fixed').fadeOut();
			}, 4000);
		});
	</script>

	@yield('js_additional')
</body>
</html>