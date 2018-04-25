<?php
	use App\Models\Accountdetail;
	use App\Models\Acc;
	use App\Models\Pricegap;
	use App\Models\Retur;
	use App\Models\Returndetail;
	use App\Models\Ri;
	use App\Models\Ridetail;
	use App\Models\Invoice;
	use App\Models\Invoicedetail;
?>

<html>
<head>
	<title></title>

	<script type="text/javascript">
        $(function(){
            var myWindow = window.open('', 'Transaction');
            myWindow.document.write('<html><head><title>Return</title>');
            myWindow.document.write('<link href="{{URL::to('/')}}/css/print/stock.css"rel="stylesheet" type="text/css" media="all">');
            myWindow.document.write("<style> td a {color: #f7961e; } .report-table tr:nth-child(odd) {background: transparent; }  </style>");
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
				Invoice
			</h1>
		</div>
		<div class="print-desc">
			<div class="print-desc-item">
				Dear.<br>
				<strong>{{$invoice->supplier->name}}</strong><br>
				@if($invoice->supplier->phone != null)
					Phone : {{$invoice->supplier->phone}}<br>
				@endif
				@if($invoice->supplier->address != null)
					Address : {{$invoice->supplier->address}}<br>
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
							{{$invoice->no_nota}}
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
							{{date('d F Y', strtotime($invoice->date))}}
						</td>
					</tr>
					<tr>
						<td>
							Total
						</td>
						<td class="print-desc-separator">
							:
						</td>
						<td>
							<?php
								$total = 0;
								foreach ($getinvoicedetails as $getinvoicedetail) {
									$total = $total + ($getinvoicedetail->qty * $getinvoicedetail->price);
								}
							?>
							Rp {{number_format($total)}}
						</td>
					</tr>
				</table>
			</div>
		</div>

		<?php
			$ris = Ri::whereIn('id', $getridetailids)->get();
		?>
		@foreach($ris as $ri)
			<span style="position: relative; display: block; font-size: 16px; padding-top: 20px; padding-left: 20px; margin-bottom: 10px;">
				{{$ri->no_nota}}
			</span>
			<table class="report-table" style="margin-bottom: 30px;">
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
						Subtotal
					</th>
				</tr>
				<?php
					$counter = 0;
					$subtotal = 0;
					$ridetailids = array();
					$ridetails = Ridetail::where('ri_id', '=', $ri->id)->get();
					foreach ($ridetails as $ridetail) {
						$ridetailids[] = $ridetail->id;
					}
					$invoicedetails = Invoicedetail::where('invoice_id', '=', $invoice->id)->whereIn('ridetail_id', $ridetailids)->where('price', '!=', 0)->get();
					$frees = Invoicedetail::where('invoice_id', '=', $invoice->id)->whereIn('ridetail_id', $ridetailids)->where('price', '=', 0)->get();
				?>
				@foreach ($invoicedetails as $invoicedetail)
					<?php
						$counter++;
						$subtotal = $subtotal + ($invoicedetail->price * $invoicedetail->qty);
					?>
					<tr>
						<td>
							{{$counter}}
						</td>
						<td>
							{{$invoicedetail->ridetail->podetail->product->name}}
						</td>
						<td>
							{{$invoicedetail->qty}}
						</td>
						<td style="text-align: right;">
							Rp {{number_format($invoicedetail->price)}}
						</td>
						<td style="text-align: right;">
							Rp {{number_format($invoicedetail->price * $invoicedetail->qty)}}
						</td>
					</tr>
				@endforeach

				@if(!$frees->isEmpty())
					<tr>
						<td colspan="5" style="border-top: 1px dashed #d2d2d2; border-bottom: 1px dashed #d2d2d2;">
							Free Item
						</td>
					</tr>
					@foreach($frees as $free)
						<?php 
							$counter++; 
						?>
						<tr>
							<td>
								{{$counter}}
							</td>
							<td>
								{{$free->ridetail->product->name}}
							</td>
							<td>
								{{$free->qty}}
							</td>
							<td style="text-align: right;">
								Rp 0
							</td>
							<td style="text-align: right;">
								Rp 0
							</td>
						</tr>
					@endforeach
				@endif
				<tr>
					<td colspan="4" style="text-align: right; border-top: 1px solid #d2d2d2; font-size: 18px;">
						Total
					</td>
					<td style="text-align: right; border-top: 1px solid #d2d2d2; font-size: 18px;">
						Rp {{number_format($subtotal)}}
					</td>
				</tr>
			@endforeach
		</table>
	</div>
</body>
</html>