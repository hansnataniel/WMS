<!DOCTYPE html>
<html>
<head>
	<title>
		Manage CREIDS DB Migrate / Fill / Rollback
	</title>

	<link rel="shortcut icon" href="{{URL::to('img/admin/favicon.jpg')}}" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	{!!HTML::style('css/back/style.css')!!}
</head>
<body>
	<div class="db-container">
		<div class="mid">
			<div class="db-content">
				{!!HTML::image('img/admin/creids_logo.png', '', ['class'=>'db-img'])!!}
				<div class="db-group">
					<div class="db-item migrate" url="{{URL::to('creidsdbmigrate')}}" datatext="Initializing migrate..." successtext="Migrate Success">
						Migrate
					</div>
					<div class="db-item fill" url="{{URL::to('creidsdbfill')}}" datatext="Initializing fill..." successtext="Fill Success">
						Fill
					</div>
					<div class="db-item rollback" url="{{URL::to('creidsdbrollback')}}" datatext="Initializing rollback..." successtext="Rollback Success">
						Rollback
					</div>
				</div>
				<div class="db-status">
					/* Choose action what you want to do */
				</div>
			</div>
		</div>
	</div>

	{!!HTML::script('js/jquery-1.8.3.min.js')!!}

	<script>
		$(document).ready(function(){
			$('.db-item').click(function(){
				if($(this).hasClass('pending'))
				{

				}
				else
				{
					var loading = $(this).attr('datatext');
					var success = $(this).attr('successtext');


					$('.db-item').addClass('pending');
					$('.db-status').removeClass('success').text(loading);

					$.ajax({
						type: "GET",
						url: $(this).attr('url'),
						success:function(msg){
							$('.db-item').removeClass('pending');
							$('.db-status').addClass('success').text(success);
						},
						error:function(msg) {
							$('.db-item').removeClass('pending');
							$('body').html(msg.responseText);
						}
					});
				}
			});
		});
	</script>
</body>
</html>