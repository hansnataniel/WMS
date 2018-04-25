<?php
	use App\Models\Visitorcounter;
	use App\Models\Product;
	use App\Models\Productphoto;
?>


@extends('back.template.master')

@section('title')
	Dashboard
@endsection

@section('head_additional')
	{!!HTML::style('css/back/dashboard.css')!!}

	<style>
		.custom-dash-product {
			margin: 0px 5px 10px;
			height: auto;
			border: 1px solid #d2d2d2;
			padding: 5px;
		}

		.custom-dash-product .dash-short-title {
			padding: 15px 0px 10px;
			font-size: 12px;
			position: relative;
			display: block;
		}
	</style>
@endsection

@section('js_additional')
	<script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
		google.load("visualization", "1", {packages:["corechart"]});
		google.setOnLoadCallback(drawChart);
		function drawChart() {

			var data = google.visualization.arrayToDataTable([
				['Month', 'Visitor(s)', 'Page Loaded'],
				<?php

					$getfirstdate = Visitorcounter::first();

					if($getfirstdate != null)
					{
						$getmonth = date('m');
						$getyear = date('Y');
						$getlastmonth = $getmonth - 11;

						// $getstartmonth = 12;
						// $getstartmonth = $getstartmonth - $getmonth;
						if($getmonth == 12)
						{
							$getstartmonth = 1;
						}
						else
						{
							$getstartmonth = intval(date('m')) + 1;
						}
						$getstartyear = intval(date('Y')) - 1;

						if($getlastmonth < 1)
						{
							$difyear = true;
							$visitorcounters = Visitorcounter::groupBy('year')->groupBy('month')->where('year', '>=', $getstartyear)->where('month', '>=', $getstartmonth)->get();
							$visitorcounter1s = Visitorcounter::groupBy('year')->groupBy('month')->where('year', '=', intval(date('Y')))->where('month', '<=', intval(date('m')))->get();
							// dd('done');
						}
						else
						{
							$difyear = false;
							$visitorcounters = Visitorcounter::groupBy('year')->groupBy('month')->where('month', '>=', $getlastmonth)->where('month', '<=', $getmonth)->where('year', '=', $getyear)->get();
						}
					}


					if(isset($difyear))
					{
						if($difyear == true)
						{
							if(!$visitorcounter1s->isEmpty())
							{
								$getvisitorcounters = array();

								foreach ($visitorcounters as $visitorcounter) {
									$getvisitorcounters[$visitorcounter->month] = $visitorcounter->year;
								}

								foreach ($visitorcounter1s as $visitorcounter1) {
									$getvisitorcounters[$visitorcounter1->month] = $visitorcounter1->year;
								}

								foreach($getvisitorcounters as $key => $value)
								{
									/*
										Change month to alphabet
									*/

										$month = intval($key);

										if ($month == 1) {
											$month_name1 = 'Jan';
										}
										if ($month == 2) {
											$month_name1 = 'Feb';
										}
										if ($month == 3) {
											$month_name1 = 'Mar';
										}
										if ($month == 4) {
											$month_name1 = 'Apr';
										}
										if ($month == 5) {
											$month_name1 = 'May';
										}
										if ($month == 6) {
											$month_name1 = 'Jun';
										}
										if ($month == 7) {
											$month_name1 = 'Jul';
										}
										if ($month == 8) {
											$month_name1 = 'Aug';
										}
										if ($month == 9) {
											$month_name1 = 'Sep';
										}
										if ($month == 10) {
											$month_name1 = 'Oct';
										}
										if ($month == 11) {
											$month_name1 = 'Nov';
										}
										if ($month == 12) {
											$month_name1 = 'Dec';
										}	

									$counter = 0;
									$pagecounter = 0;

									$countvisitors = Visitorcounter::where('month', '=', $key)->where('year', '=', $value)->get();
									foreach ($countvisitors as $countvisitor) {
										$counter = $counter + $countvisitor->count;
										$pagecounter = $pagecounter + $countvisitor->pageload;
									}

									echo "['".$month_name1."', ".intval($counter).", ".intval($pagecounter)."],";
								}

							}
						}
						else
						{
							if(!$visitorcounters->isEmpty())
							{
								foreach ($visitorcounters as $visitorcounter) 
								{
									/*
										Change month to alphabet
									*/

										if ($month == 1) {
											$month_name1 = 'Jan';
										}
										if ($month == 2) {
											$month_name1 = 'Feb';
										}
										if ($month == 3) {
											$month_name1 = 'Mar';
										}
										if ($month == 4) {
											$month_name1 = 'Apr';
										}
										if ($month == 5) {
											$month_name1 = 'May';
										}
										if ($month == 6) {
											$month_name1 = 'Jun';
										}
										if ($month == 7) {
											$month_name1 = 'Jul';
										}
										if ($month == 8) {
											$month_name1 = 'Aug';
										}
										if ($month == 9) {
											$month_name1 = 'Sep';
										}
										if ($month == 10) {
											$month_name1 = 'Oct';
										}
										if ($month == 11) {
											$month_name1 = 'Nov';
										}
										if ($month == 12) {
											$month_name1 = 'Dec';
										}	

									$month = intval($visitorcounter->month);

									$counter = 0;
									$pagecounter = 0;

									$countvisitors = Visitorcounter::where('month', '=', $visitorcounter->month)->where('year', '=', $visitorcounter->year)->get();
									foreach ($countvisitors as $countvisitor) {
										$counter = $counter + $countvisitor->count;
										$pagecounter = $pagecounter + $countvisitor->pageload;
									}

									echo "['".$month_name1."', ".intval($counter).", ".intval($pagecounter)."],";
								}
							}
						}
					}
				?>
			]);

			var options = {
				title: 'Visitor counter',
				hAxis: {title: 'Month', titleTextStyle: {color: '#0d0f3b'}},
				backgroundColor : {fill : 'none'},
				colors: ['f7961e', '0d0f3b']
			};

			

	        var chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));

	        chart.draw(data, options);
	    }
    </script>
@endsection

@section('page_title')
	Dashboard
	<span>
		Statistic
	</span>
@endsection

@section('help')
	<ul class="menu-help-list-container">
		<li>
			Klik Shortcut yang ada di halaman ini untuk mempercepat menuju halaman yang dituju
		</li>
		<li>
			Gunakan tombol Sign Out di dalam menu atau di halaman dashboard bagian shortcut untuk keluar dari halaman Admin
		</li>
	</ul>
@endsection

@section('content')
	<div class="page-group">
		<div class="page-item col-2-4">
			<div class="page-item-title">
				Navigation Shortcut
			</div>
			<div class="page-item-content dash-short-container">
				<a class="dash-short-item" title="User" href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/user')}}">
					<div class="mid">
						{!!HTML::image('img/admin/dashboard/member.png', 'User', ['class'=>'dash-short-image'])!!}
					</div>
					<div class="dash-short-title">
						User
					</div>
				</a>
				<a class="dash-short-item" title="Product" href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/product')}}">
					<div class="mid">
						{!!HTML::image('img/admin/dashboard/product.png', 'Product', ['class'=>'dash-short-image'])!!}
					</div>
					<div class="dash-short-title">
						Product
					</div>
				</a>
			</div>
		</div>
		<div class="page-item col-2-4">
			<div class="page-item-title">
				Master Shortcut
			</div>
			<div class="page-item-content dash-short-container">
				<a class="dash-short-item" title="Edit Profile" href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/user/edit-profile')}}">
					<div class="mid">
						{!!HTML::image('img/admin/dashboard/edit_profile.png', 'Member', ['class'=>'dash-short-image'])!!}
					</div>
					<div class="dash-short-title">
						Edit Profile
					</div>
				</a>
				<a class="dash-short-item" title="Setting" href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/setting/edit')}}">
					<div class="mid">
						{!!HTML::image('img/admin/dashboard/setting.png', 'Setting', ['class'=>'dash-short-image'])!!}
					</div>
					<div class="dash-short-title">
						Setting
					</div>
				</a>
				<a class="dash-short-item" title="Sign out" href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/logout')}}">
					<div class="mid">
						{!!HTML::image('img/admin/dashboard/logout.png', 'Logout', ['class'=>'dash-short-image'])!!}
					</div>
					<div class="dash-short-title">
						Sign out
					</div>
				</a>
			</div>
		</div>
	</div>
	<div class="page-group">
		<div class="page-item col-1">
			<div class="page-item-title">
				Visitor Counter
			</div>
			<div class="page-item-content">
				<div class="chart" id="chart_div" style=""></div>
			</div>
		</div>
	</div>
@endsection