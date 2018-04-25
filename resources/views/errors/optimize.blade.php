<html>
<head>
	<title>
		Something went wrong on this site
	</title>

	{!!HTML::style('css/front/style.css')!!}
	{!!HTML::style('css/front/error.css')!!}
</head>
<body style="background: #f5f5f5;">
	<div class="error-container" style="color: #0d0f3b;">
		<div class="error-content">
			<div class="error-group" style="max-width: none;">
				<a href="http://creids.net" target="_blank" style="display: table; margin: auto;">
					{!!HTML::image('img/admin/creids_logo.png', '', array('id'=>'error-img'))!!}
				</a>
				<h1>
					Oops
				</h1>
				<span>
					Something went wrong on this site,<br>
					Please <a href="http://creids.net/contact_us" target="_blank">Contact Us</a> to fix this site
				</span>
			</div>
		</div>
	</div>
</body>
</html>