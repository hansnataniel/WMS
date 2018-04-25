<?php
	use App\Models\Accountdetail;
	use App\Models\Acc;
	use App\Models\Pricegap;
	use App\Models\Transaction;
	use App\Models\Transactiondetail;
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
			<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/transaction/print/' . $datestart . '/' . $dateend)}}" class="print" style="background: url('{{URL::to('img/admin/index/print_icon.png')}}');">
				Print
			</a>
			<div class="logo" style="background: url('{{URL::to('img/admin/remax_logo.png')}}');"></div>
			<h1 class="report-h1">
				Transaction Report
				<span>
					{{date('d F Y', strtotime($datestart))}} - {{date('d F Y', strtotime($dateend))}}
				</span>
			</h1>
		</div>
		<?php
			$transactions = Transaction::where('date', '>=', $datestart)->where('date', '<=', $dateend)->get();
		?>
		<table class="report-table">
			<tr class="tr-title">
				<th style="border-bottom: 1px solid #d2d2d2;">
					Date
				</th>
				<th style="border-bottom: 1px solid #d2d2d2;">
					Transaction ID
				</th>
				<th style="border-bottom: 1px solid #d2d2d2;">
					Product
				</th>
				<th style="border-bottom: 1px solid #d2d2d2;">
					Rak
				</th>
				<th style="border-bottom: 1px solid #d2d2d2;">
					Qty
				</th>
				<th style="text-align: right; border-bottom: 1px solid #d2d2d2;">
					Price
				</th>
				<th style="text-align: right; border-bottom: 1px solid #d2d2d2;">
					Discount
				</th>
				<th style="text-align: right; border-bottom: 1px solid #d2d2d2;">
					Subtotal
				</th>
			</tr>
			<?php
				$counter = 0;
				$date = 0;
				$transid = 0;
			?>
			@foreach($transactions as $transaction)
				<?php
					$transactiondetails = Transactiondetail::where('transaction_id', '=', $transaction->id)->get();

					$count = 0;
					$counter = count($transactiondetails);
				?>
				@foreach($transactiondetails as $transactiondetail)
					<?php
						$count++;
					?>
					<tr>
						<td>
							@if($date != $transaction->date)
								<?php
									$date = $transaction->date;
								?>
								{!!date('d/m/Y', strtotime($date))!!}
							@endif
						</td>
						<td>
							@if($transid !== $transaction->trans_id)
								<?php
									$transid = $transaction->trans_id;
								?>
								{{$transid}}
							@endif
						</td>
						<td>
							{{$transactiondetail->product->name}}
						</td>
						<td>
							{{$transactiondetail->rak->name}}
						</td>
						<td>
							{{$transactiondetail->qty}}
						</td>
						<td style="text-align: right;">
							Rp {!!number_format($transactiondetail->price)!!}
						</td>
						<td style="text-align: right;">
							@if($transactiondetail->discounttype == 0)
								Rp {{number_format($transactiondetail->discount)}}
							@else
								{{$transactiondetail->discount}} %
							@endif
						</td>
						<td style="text-align: right;">
							@if($transactiondetail->discounttype == 0)
								Rp {{number_format(($transactiondetail->price * $transactiondetail->qty) - $transactiondetail->discount)}}
							@else
								Rp {{number_format(($transactiondetail->price * $transactiondetail->qty) - ((($transactiondetail->price * $transactiondetail->qty) * $transactiondetail->discount) / 100))}}
							@endif
						</td>
					</tr>
					@if($counter == $count)
						<tr>
							<td colspan="6" style="text-align: right; font-weight: bold;">
								Total
							</td>
							<td colspan="2" style="text-align: right; font-weight: bold;">
								Rp {{number_format($transaction->total)}}
							</td>
						</tr>
						<tr>
							<td colspan="6" style="text-align: right; font-weight: bold;">
								Discount
							</td>
							<td colspan="2" style="text-align: right; font-weight: bold;">
								@if($transaction->discounttype == 0)
									Rp {{number_format($transaction->discount)}}
								@else
									{{$transaction->discount}} %
								@endif
							</td>
						</tr>
						<tr>
							<td colspan="6" style="text-align: right; font-weight: bold;">
								Amount to Pay
							</td>
							<td colspan="2" style="text-align: right; font-weight: bold;">
								Rp {{number_format($transaction->amount_to_pay)}}
							</td>
						</tr>
					@endif
				@endforeach
			@endforeach
		</table>
	</div>
</body>
</html>