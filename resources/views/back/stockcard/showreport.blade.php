<?php
	use App\Models\Adjustment;
	use App\Models\Transaction;
	use App\Models\Transactiondetail;
	use App\Models\Ridetail;
	use App\Models\Ri;
	use App\Models\Po;
	use App\Models\Podetail;
	use App\Models\Retur;
	use App\Models\Returndetail;
	use App\Models\Treturn;
	use App\Models\Treturndetail;
?>

<html>
<head>
	<title>
		Stock Card Report
	</title>

	{!!HTML::style('css/back/style.css')!!}
	{!!HTML::style('css/print/stock.css')!!}
	{!!HTML::style('css/back/index.css')!!}
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
	</style>
</head>
<body style="background: #d2d2d2; margin: 0px; padding: 0px;">
	<div class="result"></div>
	<div class="print-container" style="box-shadow: 0px 0px 10px #000; border: 1px solid #d2d2d2; background: #fff; display: table; margin: auto;">
	{{-- <div class="print-container" style="width: 297mm; box-shadow: 0px 0px 10px #000; border: 1px solid #d2d2d2; background: #fff; display: table; margin: auto;"> --}}
		<div class="report-header">
			<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/stock-card/print/' . $product->id . '/' . $datestart . '/' . $dateend)}}" class="print" style="background: url('{{URL::to('img/admin/index/print_icon.png')}}');">
				Print
			</a>
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
					Ref
				</th>
				<th>
					Last Qty
				</th>
				<th>
					Last Price
				</th>
				<th>
					Input Qty
				</th>
				<th>
					Input Price
				</th>
				<th>
					Out Qty
				</th>
				<th>
					Out Price
				</th>
				<th>
					Final Qty
				</th>
				<th>
					Final Price
				</th>
			</tr>
			<?php
				$counter = 0;
			?>
			@foreach($inventories as $inventory)
				<?php
					$counter++;
				?>
				@if($inventory->type == 'R')
					{{-- <tr class="return"> --}}
					<tr>
				@elseif($inventory->type == 'S')
					{{-- <tr class="transaction"> --}}
					<tr>
				@else
					<tr>
				@endif
					<td>
						{{$counter}}
					</td>
					<td>
						{{date('d/m/Y', strtotime($inventory->date))}}
					</td>

					<td>
						@if($inventory->type == 'Ri')
							<?php
								$ridetail = Ridetail::find($inventory->type_id);
								$ri = Ri::find($ridetail->ri_id);
								$po = Po::find($ridetail->ri->po_id);
							?>
							@if($po != null)
								Purchase Order - 
								<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/po/' . $po->id)}}" target="_blank">
									{{$po->no_nota}}
								</a>
							@endif
						@elseif($inventory->type == 'R')
							<?php
								$returndetail = Returndetail::find($inventory->type_id);
								$return = Retur::find($returndetail->return_id);
								$ri = Ri::find($return->ri_id);
							?>
							Return - 
							<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/ri/' . $ri->id)}}" target="_blank">
								{{$ri->no_nota}}
							</a>
						@elseif($inventory->type == 'S')
							<?php
								$transactiondetail = Transactiondetail::find($inventory->type_id);
								$transaction = Transaction::find($transactiondetail->transaction_id);
							?>
							Transaction - 
							<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/transaction/' . $transaction->id)}}" target="_blank">
								{{$transaction->trans_id}}
							</a>
						@elseif($inventory->type == 'Adj')
							<?php
								$adjustment = Adjustment::find($inventory->type_id);
							?>
							{{$adjustment->note}}
						@elseif($inventory->type == 'TR')
							<?php
								$treturndetail = Treturndetail::find($inventory->type_id);
							?>
							Transaction Return - 
							<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/transaction/' . $treturndetail->treturn->transaction->id)}}" target="_blank">
								{{$treturndetail->treturn->transaction->trans_id}}
							</a>
						@endif
					</td>

					<td>
						@if($inventory->type == 'Ri')
							<?php
								$ridetail = Ridetail::find($inventory->type_id);
								$ri = Ri::find($ridetail->ri_id);
								$po = Po::find($ri->po_id);
							?>
							<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/ri/' . $ri->id)}}" target="_blank">
								{{$ri->no_nota}}
							</a>
						@elseif($inventory->type == 'R')
							<?php
								$returndetail = Returndetail::find($inventory->type_id);
								$ridetail = Ridetail::find($returndetail->ridetail_id);
								$return = Retur::find($returndetail->return_id);
								$ri = Ri::find($return->ri_id);
							?>
							<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/return/' . $return->id)}}" target="_blank">
								{{$return->no_nota}}
							</a>
						@elseif($inventory->type == 'S')
							<?php
								$transactiondetail = Transactiondetail::find($inventory->type_id);
								$transaction = Transaction::find($transactiondetail->transaction_id);
							?>
							<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/transaction/fill-view/' . $transaction->id)}}" target="_blank">
								{{$transaction->trans_id}}
							</a>
						@elseif($inventory->type == 'Adj')
							<?php
								$adjustment = Adjustment::find($inventory->type_id);
							?>
							<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/stock-adjustment/' . $adjustment->id)}}" target="_blank">
								{{$adjustment->no_nota}}
							</a>
						@elseif($inventory->type == 'TR')
							<?php
								$treturndetail = Treturndetail::find($inventory->type_id);
								// dd($treturndetail);
							?>
							<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/treturn/' . $treturndetail->treturn->id)}}" target="_blank">
								{{$treturndetail->treturn->no_nota}}
							</a>
						@endif
					</td>

					<td style="text-align: center;">
						{{$inventory->qty_last}}
					</td>
					<td style="text-align: right;">
						Rp {{number_format($inventory->price_last)}}
					</td>
					<td style="text-align: center;">
						@if($inventory->qty_in == 0)
							-
						@else
							{{$inventory->qty_in}}
						@endif
					</td>
					<td style="text-align: right;">
						@if($inventory->price_in == 0.00)
							@if($inventory->type == 'Ri')
								Rp {{number_format($inventory->price_in)}}
							@else
								@if($inventory->type == 'Adj')
									@if($inventory->qty_in != 0)
										Rp {{number_format($inventory->price_in)}}
									@else
										-
									@endif
								@else
									-
								@endif
							@endif
						@else
							Rp {{number_format($inventory->price_in)}}
						@endif
					</td>
					<td style="text-align: center;">
						@if($inventory->qty_out == 0)
							-
						@else
							{{$inventory->qty_out}}
						@endif
					</td>
					<td style="text-align: right;">
						@if($inventory->price_out == 0.00)
							@if($inventory->type == 'R')
								Rp {{number_format($inventory->price_out)}}
							@else
								@if($inventory->type == 'Adj')
									@if($inventory->qty_out != 0)
										Rp {{number_format($inventory->price_out)}}
									@else
										-
									@endif
								@else
									-
								@endif
							@endif
						@else
							Rp {{number_format($inventory->price_out)}}
						@endif
					</td>
					<td style="text-align: center;">
						{{$inventory->qty_z}}
					</td>
					<td style="text-align: right;">
						Rp {{number_format($inventory->price_z)}}
					</td>
				</tr>
			@endforeach
		</table>
	</div>
</body>
</html>