<html>
<head>
	<title>
		Maintenance Mode
	</title>

	{!!HTML::style('css/front/style.css')!!}
	{!!HTML::style('css/front/error.css')!!}
</head>
<body>
	<div class="error-container">
		<div class="error-content">
			<div class="error-group">
				{!!HTML::image('img/admin/creids_logo.png', '', array('id'=>'error-img'))!!}
				<h1>
					Maintenance Mode
				</h1>
				<span>
					Sorry, this site is under maintenance.<br>
					Please try again in a few minute.
				</span>
			</div>
		</div>
	</div>
</body>
</html>