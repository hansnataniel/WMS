<?php
	use Illuminate\Support\Str;

	use App\Models\Admin;
	use App\Models\Rate;
	use App\Models\Voucher;
	use App\Models\Product;
	use App\Models\Transaction;
	use App\Models\Transactiondetail;
	use App\Models\Hbt;
?>

@extends('back.template.master')

@section('title')
	Accounts Recievable View
@endsection

@section('head_additional')
	{!!HTML::style('css/back/detail.css')!!}
	{!!HTML::style('css/back/index.css')!!}

	<style>
		.page-item-title span {
			position: relative;
			display: block;
			font-size: 14px;
			padding-top: 5px;
		}
	</style>
@endsection

@section('js_additional')
	<script type="text/javascript">
		$(document).ready(function(){
			
		});
	</script>
@endsection

@section('page_title')
	Accounts Recievable View
	<span>
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/dashboard')}}">Dashboard</a> / <a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/accounts-recievable')}}">Accounts Recievable</a> / <span>Accounts Recievable View</span>
	</span>
@endsection

@section('help')
	<ul class="menu-help-list-container">
		<li>
			Anda dapat melihat data Accounts Recievable secara keseluruhan
		</li>
	</ul>
@endsection

@section('content')
	<div class="page-group">
		<div class="page-item col-1">
			<div class="page-item-content">
				<h1 class="view-title">
					@if($request->session()->has('last_url'))
						<a class="view-button-item view-button-back" href="{{URL::to($request->session()->get('last_url'))}}"></a>
					@else
						<a class="view-button-item view-button-back" href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/accounts-recievable')}}"></a>
					@endif
					
				</h1>
				
				<div class="page-group">
					<div class="page-item col-1">
						<div class="page-item-title">
							Customer Information
						</div>
						<div class="page-item-content view-item-content">
							<table class="view-detail-table">
								<tr>
									<td>
										Customer Name
									</td>
									<td class="view-info-mid">
										:
									</td>
									<td>
										<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/customer/' . $customer->id)}}" style="color: blue;">
											{{$customer->name}}
										</a>
									</td>
								</tr>
								<tr>
									<td>
										Code
									</td>
									<td class="view-info-mid">
										:
									</td>
									<td>
										{{$customer->code}}
									</td>
								</tr>
								<tr>
									<td>
										Email
									</td>
									<td class="view-info-mid">
										:
									</td>
									<td>
										{{$customer->email}}
									</td>
								</tr>
								<tr>
									<td>
										Phone
									</td>
									<td class="view-info-mid">
										:
									</td>
									<td>
										{{$customer->phone}}
									</td>
								</tr>
								<tr>
									<td>
										Mobile
									</td>
									<td class="view-info-mid">
										:
									</td>
									<td>
										{{$customer->mobile}}
									</td>
								</tr>
								<tr>
									<td>
										Fax
									</td>
									<td class="view-info-mid">
										:
									</td>
									<td>
										{{$customer->fax}}
									</td>
								</tr>
								<tr>
									<td>
										Address
									</td>
									<td class="view-info-mid">
										:
									</td>
									<td>
										{{$customer->address}}
									</td>
								</tr>
								<tr>
									<td>
										Total
									</td>
									<td class="view-info-mid">
										:
									</td>
									<td>
										<?php
											$total = 0;
											foreach ($transactions as $transaction) {
												$total = $total + $transaction->amount_to_pay;
											}
										?>
										Rp {{number_format($total)}}
									</td>
								</tr>
							</table>
						</div>
					</div>
				</div>
				
				@foreach($transactions as $transaction)
					<div class="page-group">
						<div class="page-item col-1">
							<div class="page-item-title">
								<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/transaction/' . $transaction->id)}}" style="color: blue;">
									Transaction ID {{$transaction->trans_id}}
								</a>
								<span>
									Date: {{date('d F Y', strtotime($transaction->date))}}
								</span>
							</div>
							<div class="page-item-content view-item-content">
								<table class="index-table">
									<tr class="index-tr-title">
										<th>
											#
										</th>
										<th>
											Product
										</th>
										<th>
											Rak
										</th>
										<th>
											Qty
										</th>
										<th width="200" style="text-align: right;">
											Price
										</th>
										<th width="200" style="text-align: right;">
											Discount
										</th>
										<th width="200" style="text-align: right;">
											Subtotal
										</th>
									</tr>
									<?php
										$counter = 0;
										$subtotal = 0;
										$transactiondetails = Transactiondetail::where('transaction_id', '=', $transaction->id)->get();
									?>
									@foreach ($transactiondetails as $transactiondetail)
										<?php 
											$counter++; 
											$subtotal = $subtotal + ($transactiondetail->price * $transactiondetail->qty);
										?>

										<tr>
											<td>
												{{$counter}}
											</td>
											<td>
												<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/product/' . $transactiondetail->product_id)}}" style="color: blue;">
													{{$transactiondetail->product->name}}
												</a>
											</td>
											<td>
												<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/rak/' . $transactiondetail->rak_id)}}" style="color: blue;">
													{{$transactiondetail->rak->name}}
												</a>
											</td>
											<td>
												{{$transactiondetail->qty}}
											</td>
											<td style="text-align: right;">
												Rp {{number_format($transactiondetail->price)}}
											</td>
											<td style="text-align: right;">
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
										<td colspan="6" style="font-size: 16px; text-align: right; border-top: 1px solid #535353; font-weight: bold;">
											Discount
										</td>
										<td style="font-size: 16px; text-align: right; border-top: 1px solid #535353; font-weight: bold;">
											<?php
												if($transaction->discounttype == 0)
												{
													$total = $total - $transaction->discount;
												}
												else
												{
													$total = $total - (($total * $transaction->discount) / 100);
												}
											?>
											@if($transaction->discounttype == 0)
												Rp {{number_format($transaction->discount)}}
											@else
												{{$transaction->discount}} %
											@endif
										</td>
									</tr>
									<tr>
										<td colspan="6" style="font-size: 20px; text-align: right; border-top: 1px solid #535353; font-weight: bold;">
											Total
										</td>
										<td style="font-size: 20px; text-align: right; border-top: 1px solid #535353; font-weight: bold;">
											Rp {{number_format($transaction->total)}}
										</td>
									</tr>	
									<tr>
										<td colspan="6" style="font-size: 20px; text-align: right; border-top: 1px solid #535353; font-weight: bold;">
											Amount to Pay
										</td>
										<td style="font-size: 20px; text-align: right; border-top: 1px solid #535353; font-weight: bold;">
											Rp {{number_format($transaction->amount_to_pay)}}
										</td>
									</tr>
								</table>
							</div>
						</div>
					</div>
				@endforeach
			</div>
		</div>
	</div>
@endsection