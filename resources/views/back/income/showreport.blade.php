<?php
	use App\Models\Accountdetail;
	use App\Models\Acc;
	use App\Models\Pricegap;
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
			<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/income-statement/print/' . $datestart . '/' . $dateend)}}" class="print" style="background: url('{{URL::to('img/admin/index/print_icon.png')}}');">
				Print
			</a>
			<div class="logo" style="background: url('{{URL::to('img/admin/remax_logo.png')}}');"></div>
			<h1 class="report-h1">
				Laporan Laba / Rugi
				<span>
					{{date('d F Y', strtotime($datestart))}} - {{date('d F Y', strtotime($dateend))}}
				</span>
			</h1>
		</div>
		<?php
			/*Get Pricegap*/
			$pricegaps = Pricegap::where('date', '>=', $datestart)->where('date', '<=', $dateend)->get();

			$selisihbahan = 0;
			foreach ($pricegaps as $pricegap) {
				$selisihbahan = $selisihbahan + $pricegap->price;
			}
		?>
		<table class="report-table">
			<tr>
				<td colspan="5">
					<strong style="text-decoration: underline; color: #0d0f3b;">
						Pendapatan Usaha
					</strong>
				</td>
			</tr>
			<tr>
				<td style="padding-left: 40px;">
					Untung Penjualan
				</td>
				@if($selisihbahan < 0)
					<td style="text-align: right;">
				@else
					<td style="text-align: right; border-bottom: 1px solid #0d0f3b;">
				@endif
					<?php
						// if(Auth::user()->get()->is_tax == true)
						// {
							$getincometotal = 0;

							$transactions = Transaction::where('date', '>=', $datestart)->where('date', '<=', $dateend)->get();
							foreach ($transactions as $transaction) {
								$transactiondetails = Transactiondetail::where('transaction_id', '=', $transaction->id)->get();
								foreach ($transactiondetails as $transactiondetail) {
									$getproductstock = Productstock::where('product_id', '=', $transactiondetail->product_id)->where('rak_id', '=', $rak_id)->first();
									$getinventory = Inventory::where('type', '=', 'S')->where('productstock_id', '=', $getproductstock->id)->where('type_id', '=', $transactiondetail->id)->first();

									if($discounttype == '0')
									{
										$subtotal = ($transactiondetail->qty * $transactiondetail->price) - $transactiondetail->discount;
										$getgap = ($getinventory->price_out * $getinventory->qty_out) - $subtotal;
									}

									if($discounttype == '1')
									{
										$subtotal = ($transactiondetail->qty * $transactiondetail->price) - ((($transactiondetail->qty * $transactiondetail->price) * $transactiondetail->discount) / 100);
										$getgap = ($getinventory->price_out * $getinventory->qty_out) - $subtotal;
									}
									
									$getincometotal += $getgap;
								}
							}
						// }
						// else
						// {
							// $histories = History::where('date', '>=', $datestart)->where('date', '<=', $dateend)->where('status', '=', '3')->get();
						// }
					?>
					{{number_format($getincometotal)}}
				</td>
				<td colspan="3"></td>
			</tr>
			<?php
				$getaccountincomeusahas = Account::where('type', '=', 'Income')->where('is_active', '=', true)->get();
				$totalincome = 0;
			?>
				
			@foreach($getaccountincomeusahas as $getaccountincomeusaha)
				<?php
					$getincomeusahas = Accountdetail::where('account_id', '=', $getaccountincomeusaha->id)->where('date', '>=', $datestart)->where('date', '<=', $dateend)->get();

					$totalincomeusaha = 0;
					foreach ($getincomeusahas as $getincomeusaha) {
						$totalincomeusaha += $getincomeusaha->amount;
					}

					$totalincome += $totalincomeusaha;
				?>

				<tr>
					<td style="padding-left: 40px;">
						{{$getaccountincomeusaha->name}}
					</td>
					<td style="text-align: right;">
						{{number_format($totalincomeusaha)}}
					</td>
					<td colspan="3"></td>
				</tr>
			@endforeach

			@if($selisihbahan < 0)
				<tr>
					<td style="padding-left: 40px;">
						Selisih Pembelian
					</td>
					<td style="text-align: right; border-bottom: 1px solid #0d0f3b;">
						<?php
							$getselisihbahan = str_replace('-', '', $selisihbahan);
						?>
						{{number_format($getselisihbahan)}}
					</td>
					<td colspan="3"></td>
				</tr>
			@endif
			<tr>
				<td style="padding-left: 80px; font-weight: bold; color: #0d0f3b;">
					Total Pendapatan Usaha
				</td>
				<td></td>
				<td style="text-align: right; font-weight: bold; color: #0d0f3b;">
					<?php
						if($selisihbahan < 0)
						{
							$getselisihbahan = str_replace('-', '', $selisihbahan);
							$totalpendapatan = $getincometotal + $getselisihbahan + $totalincome;
						}
						else
						{
							$totalpendapatan = $getincometotal + $totalincome;
						}
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
						Beban Usaha
					</strong>
				</td>
			</tr>
			<?php
				$getaccountbebanusahas = Account::where('type', '=', 'Expense')->where('is_active', '=', true)->get();
				$totalbeban = 0;
			?>
				
			@foreach($getaccountbebanusahas as $getaccountbebanusaha)
				<?php
					$getbebanusahas = Accountdetail::where('account_id', '=', $getaccountbebanusaha->id)->where('date', '>=', $datestart)->where('date', '<=', $dateend)->get();

					$totalbebanusaha = 0;
					foreach ($getbebanusahas as $getbebanusaha) {
						$totalbebanusaha += $getbebanusaha->amount;
					}

					$totalbeban += $totalbebanusaha;
				?>

				<tr>
					<td style="padding-left: 40px;">
						{{$getaccountbebanusaha->name}}
					</td>
					<td style="text-align: right;">
						{{number_format($totalbebanusaha)}}
					</td>
					<td colspan="3"></td>
				</tr>
			@endforeach

			@if($selisihbahan > 0)
				<tr>
					<td style="padding-left: 40px;">
						Selisih Pembelian
					</td>
					<td style="text-align: right; border-bottom: 1px solid #0d0f3b;">
						{{number_format($selisihbahan)}}
					</td>
					<td colspan="3"></td>
				</tr>
			@endif
			<tr>
				<td style="padding-left: 80px; font-weight: bold;">
					Total Beban Usaha
				</td>
				<td></td>
				<td style="text-align: right; border-bottom: 1px solid #0d0f3b; font-weight: bold; color: #0d0f3b;">
					<?php
						if($selisihbahan > 0)
						{
							$totalbeban = $totalbebanusaha + $selisihbahan + $totalbeban;
						}
						else
						{
							$totalbeban = $totalbebanusaha + $totalbeban;
						}
					?>
					(Rp {{number_format($totalbeban)}})
				</td>
				<td colspan="2"></td>
			</tr>
			<tr>
				<td style="padding-left: 120px; font-weight: bold; color: #0d0f3b;">
					Laba Kotor
				</td>
				<td></td>
				<td></td>
				<td style="text-align: right; font-weight: bold; color: #0d0f3b;">
					<?php
						$labakotor = $totalpendapatan - $totalbeban;
					?>
					Rp {{number_format($labakotor)}}
				</td>
				<td></td>
			</tr>
			<tr>
				<td colspan="5"></td>
			</tr>
			<tr>
				<td colspan="5">
					<strong style="text-decoration: underline; color: #0d0f3b;">
						Pendapatan Diluar Usaha
					</strong>
				</td>
			</tr>
			<?php
				$getpendapatanlain = 0;
				$pendapatanlains = Acc::where('type', '=', 'Other Income')->where('is_active', '=', true)->get();

				$countpendapatanlains = count($pendapatanlains);
				$numb = 0;

				$totalout = 0;
				$outinventories = Inventory::where('date', '>=', $datestart)->where('date', '<=', $dateend)->where('type', '=', 'Adj')->where('qty_in', '=', 0)->where('qty_out', '!=', 0)->get();
				foreach ($outinventories as $outinventories) {
					$totalout = $totalout + ($outinventories->qty_out * $outinventories->price_out);
				}

				$totalin = 0;
				$ininventories = Inventory::where('date', '>=', $datestart)->where('date', '<=', $dateend)->where('type', '=', 'Adj')->where('qty_in', '!=', 0)->where('qty_out', '=', 0)->get();
				foreach ($ininventories as $ininventories) {
					$totalin = $totalin + ($ininventories->qty_in * $ininventories->price_in);
				}

				$gettotalselisihpenyesuaian = $totalin - $totalout;
			?>
			@if($gettotalselisihpenyesuaian > 0)
				<tr>
					<td style="padding-left: 40px;">
						Pendapatan Selisih Penyesuaian
					</td>
					<td style="text-align: right;">
						{{number_format($gettotalselisihpenyesuaian)}}
					</td>
					<td colspan="3"></td>
				</tr>
			@endif
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
						if($gettotalselisihpenyesuaian > 0)
						{
							$totalpendapatan = $getpendapatanlain + $gettotalselisihpenyesuaian;
							// $totalpendapatan = $getpendapatanlain;
						}
						else
						{
							$totalpendapatan = $getpendapatanlain;
						}
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
				$bebanlains = Acc::where('type', '=', 'Other Cost')->where('is_active', '=', true)->get();

				$countbebanlains = count($bebanlains);
				$numb1 = 0;
			?>
			@if($gettotalselisihpenyesuaian < 0)
				<tr>
					<td style="padding-left: 40px;">
						Beban Selisih Penyesuaian
					</td>
					<td style="text-align: right;">
						{{number_format(str_replace('-', '', $gettotalselisihpenyesuaian))}}
					</td>
					<td colspan="3"></td>
				</tr>
			@endif
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
						if($gettotalselisihpenyesuaian < 0)
						{
							$totalbeban = $getbebanlain + $gettotalselisihpenyesuaian;
							// $totalbeban = $getbebanlain;
						}
						else
						{
							$totalbeban = $getbebanlain;
						}
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
			<tr>
				<td style="padding-left: 150px; font-weight: bold; color: #0d0f3b;">
					Laba Bersih
				</td>
				<td></td>
				<td></td>
				<td></td>
				<td style="text-align: right; font-weight: bold; color: #0d0f3b;">
					<?php
						$lababersih = $labakotor + $totalpbdu;
					?>
					Rp {{number_format($lababersih)}}
				</td>
			</tr>
		</table>
	</div>
</body>
</html>