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
				Purchase Order
			</h1>
		</div>
		<div class="print-desc">
			<div class="print-desc-item">
				Dear.<br>
				<strong>{{$po->supplier->name}}</strong><br>
				@if($po->supplier->phone != null)
					Phone : {{$po->supplier->phone}}<br>
				@endif
				@if($po->supplier->address != null)
					Address : {{$po->supplier->address}}<br>
				@endif
			</div>
			<div class="print-desc-item">
				<table>
					<tr>
						<td>
							No. Nota
						</td>
						<td class="print-desc-separator">
							:
						</td>
						<td>
							{{$po->no_nota}}
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
							{{date('d F Y', strtotime($po->date))}}
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
			@foreach($podetails as $podetail)
				<?php
					$count++;
					if($podetail->discounttype == 0)
					{
						$total += ($podetail->price * $podetail->qty) - $podetail->discount;
					}
					else
					{
						$total += ($podetail->price * $podetail->qty) - ((($podetail->price * $podetail->qty) * $podetail->discount) / 100);
					}
				?>
				<tr>
					<td>
						{{$count}}
					</td>
					<td>
						{{$podetail->product->name}}
					</td>
					<td>
						{{$podetail->qty}}
					</td>
					<td style="text-align: right;">
						Rp {!!number_format($podetail->price)!!}
					</td>
					<td style="text-align: right;">
						@if($podetail->discounttype == 0)
							Rp {{number_format($podetail->discount)}}
						@else
							{{$podetail->discount}} %
						@endif
					</td>
					<td style="text-align: right;">
						@if($podetail->discounttype == 0)
							Rp {{number_format(($podetail->price * $podetail->qty) - $podetail->discount)}}
						@else
							Rp {{number_format(($podetail->price * $podetail->qty) - ((($podetail->price * $podetail->qty) * $podetail->discount) / 100))}}
						@endif
					</td>
				</tr>
			@endforeach
			<tr>
				<td colspan="5" style="text-align: right; border-top: 1px solid #d2d2d2; font-size: 18px;">
					Discount
				</td>
				<td style="text-align: right; border-top: 1px solid #d2d2d2; font-size: 18px;">
					<?php
						if($po->discounttype == 0)
						{
							$total = $total - $po->discount;
						}
						else
						{
							$total = $total - (($total * $po->discount) / 100);
						}
					?>
					@if($po->discounttype == 0)
						Rp {{number_format($po->discount)}}
					@else
						{{$po->discount}} %
					@endif
				</td>
			</tr>
			<tr>
				<td colspan="5" style="text-align: right; border-top: 1px solid #d2d2d2; font-size: 18px;">
					Total
				</td>
				<td style="text-align: right; border-top: 1px solid #d2d2d2; font-size: 18px;">
					Rp {{number_format($total)}}
				</td>
			</tr>
		</table>
	</div>
</body>
</html>