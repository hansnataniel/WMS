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
            var myWindow = window.open('', 'Transaction');
            myWindow.document.write('<html><head><title>Transaction Return</title>');
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
		<div class="report-header print-header">
			<div class="logo" style="background: url('{{URL::to('img/admin/remax_logo.png')}}');"></div>
			<h1 class="report-h1">
				Transaction
			</h1>
		</div>
		<div class="print-desc">
			<div class="print-desc-item">
				Dear.<br>
				<strong>{{$transaction->customer->name}}</strong>
				@if($transaction->customer->email != null)
					Email : {{$transaction->customer->email}}<br>
				@endif
				@if($transaction->customer->phone != null)
					Phone : {{$transaction->customer->phone}}<br>
				@endif
				@if($transaction->customer->mobile != null)
					Mobile : {{$transaction->customer->mobile}}<br>
				@endif
				@if($transaction->customer->fax != null)
					Fax : {{$transaction->customer->fax}}<br>
				@endif
				@if($transaction->customer->address != null)
					Address : {{$transaction->customer->address}}<br>
				@endif
			</div>
			<div class="print-desc-item">
				<table>
					<tr>
						<td>
							Transaction ID
						</td>
						<td class="print-desc-separator">
							:
						</td>
						<td>
							{{$transaction->trans_id}}
						</td>
					</tr>
					<tr>
						<td>
							Date
						</td>
						<td class="print-desc-separator">
							:
						</td>
						<td>
							{{date('d F Y', strtotime($transaction->date))}}
						</td>
					</tr>
				</table>
			</div>
		</div>
		<table class="report-table">
			<tr class="tr-title">
				<th style="border-bottom: 1px solid #d2d2d2;">
					#
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
				$count = 0;
				$total = 0;
			?>
			@foreach($transactiondetails as $transactiondetail)
				<?php
					$count++;
					$total += $transactiondetail->price * $transactiondetail->qty;
				?>
				<tr>
					<td>
						{{$count}}
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
					<td>
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
			@endforeach
			<tr>
				<td colspan="6" style="text-align: right; border-top: 1px solid #d2d2d2; font-size: 18px;">
					Total
				</td>
				<td style="text-align: right; border-top: 1px solid #d2d2d2; font-size: 18px;">
					Rp {{number_format($transaction->total)}}
				</td>
			</tr>
			<tr>
				<td colspan="6" style="text-align: right; border-top: 1px solid #d2d2d2; font-size: 18px;">
					Discount
				</td>
				<td style="text-align: right; border-top: 1px solid #d2d2d2; font-size: 18px;">
					@if($transaction->discounttype == 0)
						Rp {{number_format($transaction->discount)}}
					@else
						{{$transaction->discount}} %
					@endif
				</td>
			</tr>
			<tr>
				<td colspan="6" style="text-align: right; border-top: 1px solid #d2d2d2; font-size: 18px;">
					Amount to Pay
				</td>
				<td style="text-align: right; border-top: 1px solid #d2d2d2; font-size: 18px;">
					Rp {{number_format($transaction->amount_to_pay)}}
				</td>
			</tr>
		</table>
	</div>
</body>
</html>