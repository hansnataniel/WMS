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
				Recieve Item
			</h1>
		</div>
		<div class="print-desc">
			<div class="print-desc-item">
				From.<br>
				<strong>{{$ri->supplier->name}}</strong><br>
				@if($ri->supplier->phone != null)
					Phone : {{$ri->supplier->phone}}<br>
				@endif
				@if($ri->supplier->address != null)
					Address : {{$ri->supplier->address}}<br>
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
							{{$ri->no_nota}}
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
							{{date('d F Y', strtotime($ri->date))}}
						</td>
					</tr>
					<tr>
						<td colspan="3" style="padding: 10px;">
						</td>
					</tr>
					<tr>
						<td>
							Purchase Order
						</td>
						<td class="print-desc-separator">
							:
						</td>
						<td>
							{{$ri->po->no_nota}}
						</td>
					</tr>
					<tr>
						<td>
							Purchase Order Date
						</td>
						<td class="print-desc-separator">
							:
						</td>
						<td>
							{{date('d F Y', strtotime($ri->po->date))}}
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
			</tr>
			<?php
				$count = 0;
			?>
			@foreach($ridetails as $ridetail)
				<?php
					$count++;
				?>
				<tr>
					<td>
						{{$count}}
					</td>
					<td>
						{{$ridetail->podetail->product->name}}
					</td>
					<td>
						{{$ridetail->rak->name}}
					</td>
					<td>
						{{$ridetail->qty}}
					</td>
				</tr>
			@endforeach
			@if(!$frees->isEmpty())
				<tr>
					<td colspan="4" style="border-top: 1px dashed #d2d2d2; border-bottom: 1px dashed #d2d2d2;">
						Free Item
					</td>
				</tr>
			@endif
			@foreach($frees as $free)
				<?php
					$count++;
				?>
				<tr>
					<td>
						{{$count}}
					</td>
					<td>
						{{$free->product->name}}
					</td>
					<td>
						{{$free->rak->name}}
					</td>
					<td>
						{{$free->qty}}
					</td>
				</tr>
			@endforeach
		</table>
	</div>
</body>
</html>