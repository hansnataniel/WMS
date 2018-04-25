<?php
	use App\Models\Accountdetail;
	use App\Models\Acc;
?>

<html>
<head>
	<title></title>

	{!!HTML::style('css/back/style.css')!!}
	{!!HTML::style('css/print/stock.css')!!}
	{!!HTML::script('js/jquery-1.8.3.min.js')!!}

	<script>
		$(document).ready(function(){
			$('.print').click(function(e){
				e.preventDefault();
				var data = $(this).attr('href');
				$(this).text('Loading...');

				$.ajax({
					type: "GET",
					url: data,
					success:function(msg) {
						$('.result').html(msg);

						$('.print').text('Print');
					},
					error:function(msg) {
						$('body').html(msg.responseText);
					}
				});
			});
		});
	</script>

	<style>
		td a {
			color: #f7961e;
		}

		.report-table tr:nth-child(odd) {
			background: transparent;
		}
	</style>
</head>
<body style="background: #d2d2d2; margin: 0px; padding: 0px;">
	<div class="result"></div>
	<div class="print-container" style="width: 210mm; box-shadow: 0px 0px 10px #000; border: 1px solid #d2d2d2; background: #fff; display: table; margin: auto; padding-bottom: 50px;">
		<div class="report-header">
			<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/other-expense-revenue/print/' . $datestart . '/' . $dateend)}}" class="print" style="background: url('{{URL::to('img/admin/index/print_icon.png')}}');">
				Print
			</a>
			<div class="logo" style="background: url('{{URL::to('img/admin/remax_logo.png')}}');"></div>
			<h1 class="report-h1">
				Laporan Pendapatan / Beban Diluar Usaha
				<span>
					{{date('d F Y', strtotime($datestart))}} - {{date('d F Y', strtotime($dateend))}}
				</span>
			</h1>
		</div>
		<table class="report-table">
			<tr>
				<td colspan="5">
					<strong style="text-decoration: underline; color: #0d0f3b;">
						Pendapatan Diluar Usaha
					</strong>
				</td>
			</tr>
			<?php
				$getpendapatanlain = 0;
				$pendapatanlains = Acc::where('type', '=', 'Income')->where('is_active', '=', true)->get();

				$countpendapatanlains = count($pendapatanlains);
				$numb = 0;
			?>
			@foreach($pendapatanlains as $pendapatanlain)
				<?php
					$numb++;

					$pendapatanlaindetails = Accountdetail::where('account_id', '=', $pendapatanlain->id)->where('date', '>=', $datestart)->where('date', '<=', $dateend)->get();

					$totalpendapatanlain = 0;
					foreach ($pendapatanlaindetails as $pendapatanlaindetail) {
						$totalpendapatanlain = $totalpendapatanlain + $pendapatanlaindetail->amount;
					}

					$getpendapatanlain = $getpendapatanlain + $totalpendapatanlain;
				?>
				<tr>
					<td style="padding-left: 40px;">
						{{$pendapatanlain->name}}
					</td>
					@if($numb == $countpendapatanlains)
						<td style="text-align: right; border-bottom: 1px solid #0d0f3b;">
					@else
						<td style="text-align: right;">
					@endif
						{{number_format($totalpendapatanlain)}}
					</td>
					<td colspan="3"></td>
				</tr>
			@endforeach
			<tr>
				<td style="padding-left: 80px; font-weight: bold; color: #0d0f3b;">
					Total Pendapatan Diluar Usaha
				</td>
				<td></td>
				<td style="text-align: right; font-weight: bold; color: #0d0f3b;">
					<?php
						$totalpendapatan = $getpendapatanlain;
					?>
					Rp {{number_format($totalpendapatan)}}
				</td>
				<td colspan="2"></td>
			</tr>
			<tr>
				<td colspan="5"></td>
			</tr>
			<tr>
				<td colspan="5">
					<strong style="text-decoration: underline; color: #0d0f3b;">
						Beban Diluar Usaha
					</strong>
				</td>
			</tr>
			<?php
				$getbebanlain = 0;
				$bebanlains = Acc::where('type', '=', 'Expense')->where('is_active', '=', true)->get();

				$countbebanlains = count($bebanlains);
				$numb1 = 0;
			?>
			@foreach($bebanlains as $bebanlain)
				<?php
					$numb1++;

					$bebanlaindetails = Accountdetail::where('account_id', '=', $bebanlain->id)->where('date', '>=', $datestart)->where('date', '<=', $dateend)->get();

					$totalbebanlain = 0;
					foreach ($bebanlaindetails as $bebanlaindetail) {
						$totalbebanlain = $totalbebanlain + $bebanlaindetail->amount;
					}

					$getbebanlain = $getbebanlain + $totalbebanlain;
				?>
				<tr>
					<td style="padding-left: 40px;">
						{{$bebanlain->name}}
					</td>
					@if($numb1 == $countbebanlains)
						<td style="text-align: right; border-bottom: 1px solid #0d0f3b;">
					@else
						<td style="text-align: right;">
					@endif
						{{number_format($totalbebanlain)}}
					</td>
					<td colspan="3"></td>
				</tr>
			@endforeach
			<tr>
				<td style="padding-left: 80px; font-weight: bold; color: #0d0f3b;">
					Total Beban Diluar Usaha
				</td>
				<td></td>
				<td style="text-align: right; border-bottom: 1px solid #0d0f3b; font-weight: bold; color: #0d0f3b;">
					<?php
						$totalbeban = $getbebanlain;
					?>
					(Rp {{number_format($totalbeban)}})
				</td>
				<td colspan="2"></td>
			</tr>
			<tr>
				<td style="padding-left: 120px; font-weight: bold; color: #0d0f3b;">
					Pendapatan / Biaya Diluar Usaha
				</td>
				<td></td>
				<td></td>
				<td style="text-align: right; border-bottom: 1px solid #0d0f3b; font-weight: bold; color: #0d0f3b;">
					<?php
						$totalpbdu = $totalpendapatan - $totalbeban;
					?>
					Rp {{number_format($totalpbdu)}}
				</td>
				<td></td>
			</tr>
		</table>
	</div>
</body>
</html>