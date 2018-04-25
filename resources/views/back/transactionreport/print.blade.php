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

	<script type="text/javascript">
        $(function(){
            var myWindow = window.open('', 'Transaction Report');
            myWindow.document.write('<html><head><title>Transaction Report</title>');
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