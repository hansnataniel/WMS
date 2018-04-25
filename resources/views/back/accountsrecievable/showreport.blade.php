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
			<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/accounts-recievable/print/' . $customer->id)}}" class="print" style="background: url('{{URL::to('img/admin/index/print_icon.png')}}');">
				Print
			</a>
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