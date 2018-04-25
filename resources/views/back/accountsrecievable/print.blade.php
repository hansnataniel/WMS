<?php
	use App\Models\Accountdetail;
	use App\Models\Acc;
	use App\Models\Transaction;
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
				Accounts Recievable Report
				<span style="font-size: 16px;">
					{{$customer->name}}
				</span>
			</h1>
		</div>
		<table class="report-table">
			<tr class="tr-title">
				<th style="border-bottom: 1px solid #d2d2d2;">
					#
				</th>
				<th style="border-bottom: 1px solid #d2d2d2;">
					Date
				</th>
				<th style="border-bottom: 1px solid #d2d2d2;">
					Transaction ID
				</th>
				<th style="text-align: right; border-bottom: 1px solid #d2d2d2;">
					Total
				</th>
			</tr>
			<?php 
				$counter = 0;

				$transactions = Transaction::where('customer_id', '=', $customer->id)->where('status', '=', 'Waiting for Payment')->orderBy('id', 'desc')->get();

				$total = 0;
				foreach ($transactions as $transaction) {
					$total += $transaction->amount_to_pay;
				}

				$gettrans = Transaction::where('customer_id', '=', $customer->id)->orderBy('id', 'desc')->first();
			?>
			@foreach($transactions as $transaction)
				<?php 
					$counter++;
				?>
				<tr>
					<td>
						{{$counter}}
					</td>
					<td>
						{!!date('d/m/Y', strtotime($transaction->date))!!}
					</td>
					<td>
						{{$transaction->trans_id}}
					</td>
					<td style="text-align: right;">
						Rp {{number_format($transaction->amount_to_pay)}}
					</td>
				</tr>
			@endforeach
			<tr>
				<td colspan="3" style="text-align: right; font-size: 18px; border-top: 1px dashed #d2d2d2;">
					Total
				</td>
				<td style="text-align: right; font-size: 18px; border-top: 1px dashed #d2d2d2;">
					Rp {{number_format($total)}}
				</td>
			</tr>
		</table>
	</div>
</body>
</html>