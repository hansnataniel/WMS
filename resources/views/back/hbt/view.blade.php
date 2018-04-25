<?php
	use Illuminate\Support\Str;

	use App\Models\Admin;
	use App\Models\Rate;
	use App\Models\Voucher;
	use App\Models\Product;
	use App\Models\Po;
	use App\Models\Ri;
	use App\Models\Ridetail;
	use App\Models\Hbt;
?>

@extends('back.template.master')

@section('title')
	Uninvoiced Debt View
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
	Uninvoiced Debt View
	<span>
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/dashboard')}}">Dashboard</a> / <a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/uninvoiced-debt')}}">Uninvoiced Debt</a> / <span>Uninvoiced Debt View</span>
	</span>
@endsection

@section('help')
	<ul class="menu-help-list-container">
		<li>
			Anda dapat melihat data Uninvoiced Debt secara keseluruhan
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
						<a class="view-button-item view-button-back" href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/uninvoiced-debt')}}"></a>
					@endif
					
				</h1>
				
				<div class="page-group">
					<div class="page-item col-1">
						<div class="page-item-title">
							Supplier Information
						</div>
						<div class="page-item-content view-item-content">
							<table class="view-detail-table">
								<tr>
									<td>
										Supplier Name
									</td>
									<td class="view-info-mid">
										:
									</td>
									<td>
										<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/supplier/' . $supplier->id)}}" style="color: blue;">
											{{$supplier->name}}
										</a>
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
										{{$supplier->phone}}
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
										{{$supplier->email}}
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
										{{$supplier->fax}}
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
										{{$supplier->address}}
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
											foreach ($hbts as $hbt) {
												$total = $total + $hbt->amount;
											}
										?>
										Rp {{number_format($total)}}
									</td>
								</tr>
							</table>
						</div>
					</div>
				</div>
				
				@foreach($hbts as $hbt)
					<div class="page-group">
						<div class="page-item col-1">
							<div class="page-item-title">
								<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/ri/' . $hbt->ri_id)}}" style="color: blue;">
									Recieve Item No. {{$hbt->ri->no_nota}}
								</a>
								<span>
									Date: {{date('d F Y', strtotime($hbt->ri->date))}}
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
											Purchase Order Qty
										</th>
										<th>
											Recieve Item Qty
										</th>
										<th width="200" style="text-align: right;">
											Price
										</th>
										<th width="200" style="text-align: right;">
											Subtotal
										</th>
									</tr>
									<?php
										$counter = 0;
										$ridetails = Ridetail::where('ri_id', '=', $hbt->ri_id)->where('product_id', '=', 0)->get();
										$frees = Ridetail::where('ri_id', '=', $hbt->ri_id)->where('product_id', '!=', 0)->get();
										$subtotal = 0;
									?>
									@foreach ($ridetails as $ridetail)
										<?php 
											$counter++; 
											$subtotal = $subtotal + ($ridetail->podetail->price * $ridetail->qty);
										?>

										<tr>
											<td>
												{{$counter}}
											</td>
											<td>
												<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/product/' . $ridetail->podetail->product_id)}}" style="color: blue;">
													{{$ridetail->podetail->product->name}}
												</a>
											</td>
											<td>
												{{$ridetail->podetail->qty}}
											</td>
											<td>
												{{$ridetail->qty}}
											</td>
											<td style="text-align: right;">
												Rp {{number_format($ridetail->podetail->price)}}
											</td>
											<td style="text-align: right;">
												Rp {{number_format($ridetail->podetail->price * $ridetail->qty)}}
											</td>
										</tr>
									@endforeach
									@if(!$frees->isEmpty())
										<tr style="border-top: 1px solid #535353;">
											<td colspan="6" style="font-weight: bold;">
												Free Product
											</td>
										</tr>
										<?php
											$counter = 0;
										?>
										@foreach($frees as $free)
											<?php
												$counter++;
											?>
											<tr style="border-top: 1px solid #535353;">
												<td>
													{{$counter}}
												</td>
												<td>
													<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/product/' . $ridetail->podetail->product_id)}}" style="color: blue;">
														{{$free->product->name}}
													</a>
												</td>
												<td>
													-
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
									<tr style="border-top: double #535353;">
										<td colspan="4">
										</td>
										<td style="text-align: right; font-size: 18px;">
											Total
										</td>
										<td style="text-align: right; font-size: 18px;">
											Rp {{number_format($subtotal)}}
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