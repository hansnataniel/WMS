<?php
	use App\Models\Accountdetail;
	use App\Models\Acc;
?>

<html>
<head>
	<title></title>

	<script type="text/javascript">
        $(function(){
            var myWindow = window.open('', 'Laporan Pendapatan / Beban Diluar Usaha');
            myWindow.document.write('<html><head><title>Laporan Pendapatan / Beban Diluar Usaha</title>');
            myWindow.document.write('<link href="{{URL::to('/')}}/css/print/stock.css"rel="stylesheet" type="text/css" media="all">');
            myWindow.document.write("<style> td a {color: #f7961e; } .report-table tr:nth-child(odd) {background: transparent; } </style>");
            myWindow.document.write('</head><body>');
            myWindow.document.write($('#container').html());
            myWindow.document.write('</body></html>');
            myWindow.document.close(); // necessary for IE >= 10

            myWindow.onload=function(){ // necessary if the div contain images

                myWindow.focus(); // necessary for IE >= 10
                myWindow.print();
                myWindow.close();
            };
        });
	</script>
</head>
<body>
	<div id="container" class="print-container">
		<div class="report-header">
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