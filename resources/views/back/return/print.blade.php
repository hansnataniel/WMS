<?php
	use App\Models\Accountdetail;
	use App\Models\Acc;
	use App\Models\Pricegap;
	use App\Models\Retur;
	use App\Models\Returndetail;
	use App\Models\Ri;
	use App\Models\Ridetail;
?>

<html>
<head>
	<title></title>

	<script type="text/javascript">
        $(function(){
            var myWindow = window.open('', 'Transaction');
            myWindow.document.write('<html><head><title>Return</title>');
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
				Return
			</h1>
		</div>
		<div class="print-desc">
			<div class="print-desc-item">
				Dear.<br>
				<strong>{{$return->supplier->name}}</strong><br>
				@if($return->supplier->phone != null)
					Phone : {{$return->supplier->phone}}<br>
				@endif
				@if($return->supplier->address != null)
					Address : {{$return->supplier->address}}<br>
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
							{{$return->no_nota}}
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
							{{date('d F Y', strtotime($return->date))}}
						</td>
					</tr>
					<tr>
						<td colspan="3" style="padding: 10px;">
						</td>
					</tr>
					<tr>
						<td>
							Recieve Item
						</td>
						<td class="print-desc-separator">
							:
						</td>
						<td>
							{{$return->ri->no_nota}}
						</td>
					</tr>
					<tr>
						<td>
							Recieve Item Date
						</td>
						<td class="print-desc-separator">
							:
						</td>
						<td>
							{{date('d F Y', strtotime($return->ri->date))}}
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
					Subtotal
				</th>
			</tr>
			<?php
				$counter = 0;
				$total = 0;

				$getridetails = Ridetail::where('ri_id', '=', $return->ri_id)->where('product_id', '=', 0)->get();
				foreach ($getridetails as $getridetail) {
					$getridetailids[] = $getridetail->id;
				}

				$frees = Ridetail::where('ri_id', '=', $return->ri_id)->where('product_id', '!=', 0)->get();
				foreach ($frees as $free) {
					$freeids[] = $free->id;
				}
			?>
			@if(isset($getridetailids))
				<?php
					$returndetails = Returndetail::where('return_id', '=', $return->id)->whereIn('ridetail_id', $getridetailids)->get();
				?>
				@foreach ($returndetails as $returndetail)
					<?php 
						$counter++; 
						$total += $returndetail->price * $returndetail->qty;
					?>
					<tr>
						<td>
							{{$counter}}
						</td>
						<td>
							{{$returndetail->ridetail->podetail->product->name}}
						</td>
						<td>
							{{$returndetail->qty}}
						</td>
						<td style="text-align: right;">
							Rp {!!number_format($returndetail->price)!!}
						</td>
						<td style="text-align: right;">
							Rp {!!number_format($returndetail->price * $returndetail->qty)!!}
						</td>
					</tr>
				@endforeach
			@endif

			@if(isset($freeids))
				<?php
					$freereturndetails = Returndetail::where('return_id', '=', $return->id)->whereIn('ridetail_id', $freeids)->get();
				?>
				<tr>
					<td colspan="5" style="border-top: 1px dashed #d2d2d2; border-bottom: 1px dashed #d2d2d2;">
						Free Item
					</td>
				</tr>
				@foreach ($freereturndetails as $freereturndetail)
					<?php 
						$counter++; 
						$total += $freereturndetail->price * $freereturndetail->qty;
					?>
					<tr>
						<td>
							{{$counter}}
						</td>
						<td>
							{{$freereturndetail->ridetail->product->name}}
						</td>
						<td>
							{{$freereturndetail->qty}}
						</td>
						<td style="text-align: right;">
							Rp {{number_format($freereturndetail->price)}}
						</td>
						<td style="text-align: right;">
							Rp {!!number_format($freereturndetail->price * $freereturndetail->qty)!!}
						</td>
					</tr>
				@endforeach
			@endif
			<tr>
				<td colspan="4" style="text-align: right; border-top: 1px solid #d2d2d2; font-size: 18px;">
					Total
				</td>
				<td style="text-align: right; border-top: 1px solid #d2d2d2; font-size: 18px;">
					Rp {{number_format($total)}}
				</td>
			</tr>
			<tr>
				<td colspan="7">
					Catatan : <br>
					{!!nl2br($return->msg)!!}
				</td>
			</tr>
		</table>
	</div>
</body>
</html>