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
            var myWindow = window.open('', 'Transaction Return');
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
				Transaction Return
			</h1>
		</div>
		<div class="print-desc">
			<div class="print-desc-item">
				From.<br>
				<strong>{{$treturn->customer->name}}</strong><br>
				@if($treturn->customer->email != null)
					Email : {{$treturn->customer->email}}<br>
				@endif
				@if($treturn->customer->phone != null)
					Phone : {{$treturn->customer->phone}}<br>
				@endif
				@if($treturn->customer->mobile != null)
					Mobile : {{$treturn->customer->mobile}}<br>
				@endif
				@if($treturn->customer->fax != null)
					Fax : {{$treturn->customer->fax}}<br>
				@endif
				@if($treturn->customer->address != null)
					Address : {{$treturn->customer->address}}<br>
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
							{{$treturn->no_nota}}
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
							{{date('d F Y', strtotime($treturn->date))}}
						</td>
					</tr>
					<tr>
						<td colspan="3" style="padding: 10px;">
						</td>
					</tr>
					<tr>
						<td>
							Transaction ID
						</td>
						<td class="print-desc-separator">
							:
						</td>
						<td>
							{{$treturn->transaction->trans_id}}
						</td>
					</tr>
					<tr>
						<td>
							Transaction Date
						</td>
						<td class="print-desc-separator">
							:
						</td>
						<td>
							{{date('d F Y', strtotime($treturn->transaction->date))}}
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
					Transaction
				</th>
				<th style="border-bottom: 1px solid #d2d2d2;">
					Return
				</th>
				<th style="text-align: right; border-bottom: 1px solid #d2d2d2;">
					Price
				</th>
				<th style="text-align: right; border-bottom: 1px solid #d2d2d2;">
					Subtotal
				</th>
			</tr>
			<?php
				$count = 0;
				$total = 0;
			?>
			@foreach($treturndetails as $treturndetail)
				<?php
					$count++;
					$total += $treturndetail->price * $treturndetail->qty;
				?>
				<tr>
					<td>
						{{$count}}
					</td>
					<td>
						{{$treturndetail->transactiondetail->product->name}}
					</td>
					<td>
						{{$treturndetail->transactiondetail->rak->name}}
					</td>
					<td>
						{{$treturndetail->transactiondetail->qty}}
					</td>
					<td>
						{{$treturndetail->qty}}
					</td>
					<td style="text-align: right;">
						Rp {!!number_format($treturndetail->price)!!}
					</td>
					<td style="text-align: right;">
						Rp {{number_format($treturndetail->price * $treturndetail->qty)}}
					</td>
				</tr>
			@endforeach
			<tr>
				<td colspan="6" style="text-align: right; border-top: 1px solid #d2d2d2; font-size: 18px;">
					Total
				</td>
				<td style="text-align: right; border-top: 1px solid #d2d2d2; font-size: 18px;">
					Rp {{number_format($total)}}
				</td>
			</tr>
			<tr>
				<td colspan="7">
					Catatan : <br>
					{{nl2br($treturn->message)}}
				</td>
			</tr>
		</table>
	</div>
</body>
</html>