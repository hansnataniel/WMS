<?php
	use App\Models\Adjustment;
	use App\Models\Transaction;
	use App\Models\Transactionitem;
?>

<html>
<head>
	<title></title>

	{{-- {{HTML::style('css/print/stock.css')}} --}}
	{{-- {{HTML::script('js/jquery-1.8.3.min.js')}} --}}

	<script type="text/javascript">
        $(function(){
            var myWindow = window.open('', 'Stock Card');
            myWindow.document.write('<html><head><title>Stock Card</title>');
            myWindow.document.write('<link href="{{URL::to('/')}}/css/back/style.css"rel="stylesheet" type="text/css" media="all">');
            myWindow.document.write('<link href="{{URL::to('/')}}/css/print/stock.css"rel="stylesheet" type="text/css" media="all">');
            myWindow.document.write('<link href="{{URL::to('/')}}/css/back/index.css"rel="stylesheet" type="text/css" media="all">');
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
	{{-- <div class="print-container" style="width: 297mm; box-shadow: 0px 0px 10px #000; border: 1px solid #d2d2d2; background: #fff; display: table; margin: auto;"> --}}
		<div class="report-header">
			<div class="logo" style="background: url('{{URL::to('img/admin/remax_logo.png')}}');"></div>
			<h1 class="report-h1">
				Stock Card Report
				<span>
					{{date('d F Y', strtotime($datestart))}} - {{date('d F Y', strtotime($dateend))}}
				</span>
			</h1>
		</div>
		<div class="info-before-table">
			<table>
				<tr>
					<td>
						Product
					</td>
					<td class="info-td-mid">
						:
					</td>
					<td>
						<strong>{{$product->name}}</strong>
					</td>
				</tr>
			</table>
		</div>
		<table class="index-table">
			<tr class="index-tr-title">
				<th>
					#
				</th>
				<th>
					Date
				</th>
				<th>
					Information
				</th>
				<th>
					Last Qty
				</th>
				<th>
					Input Qty
				</th>
				<th>
					Out Qty
				</th>
				<th>
					Final Qty
				</th>
			</tr>
			<?php
				$no = 0;
			?>
			@foreach($inventories as $inventory)
				<?php
					$no++;
				?>
				<tr>
					<td>
						{{$no}}
					</td>
					<td>
						{{date('d/m/Y', strtotime($inventory->date))}}
					</td>

					<td>
						@if($inventory->type == 'S')
							<?php
								$transactionitem = Transactionitem::find($inventory->type_id);
								$transaction = Transaction::find($transactionitem->transaction_id);
							?>
							Transaction - 
							<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/transaction/' . $transaction->id)}}" target="_blank">
								{{$transaction->no_nota}}
							</a>
						@elseif($inventory->type == 'Adj')
							<?php
								$adjustment = Adjustment::find($inventory->type_id);
							?>
							Stock Adjustment - 
							<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/adjustment/' . $adjustment->id)}}" target="_blank">
								{{$adjustment->no_nota}}
							</a>
						@endif
					</td>

					<td style="text-align: center;">
						{{$inventory->qty_last}}
					</td>
					<td style="text-align: center;">
						@if($inventory->qty_in == 0)
							-
						@else
							{{$inventory->qty_in}}
						@endif
					</td>
					<td style="text-align: center;">
						@if($inventory->qty_out == 0)
							-
						@else
							{{$inventory->qty_out}}
						@endif
					</td>
					<td style="text-align: center;">
						{{$inventory->qty_z}}
					</td>
				</tr>
			@endforeach
		</table>
	</div>
</body>
</html>