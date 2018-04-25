<?php
	use Illuminate\Support\Str;

	use App\Models\Admin;
	use App\Models\tpayment;
	use App\Models\Transactiondetail;
	use App\Models\Transaction;
?>

@extends('back.template.master')

@section('title')
	Transaction Payment View
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
	Transaction Payment View
	<span>
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/dashboard')}}">Dashboard</a> / <a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/tpayment')}}">Transaction Payment</a> / <span>Transaction Payment View</span>
	</span>
@endsection

@section('help')
	<ul class="menu-help-list-container">
		<li>
			Gunakan tombol Edit untuk mengedit tpayment
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
						<a class="view-button-item view-button-back" href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/tpayment')}}"></a>
					@endif
					{{$tpayment->no_nota}}
					<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/tpayment/' . $tpayment->id . '/edit')}}" class="view-button-item view-button-edit">
						Edit
					</a>
				</h1>

				<div class="page-group">
					<div class="page-item col-1">
						<div class="page-item-title">
							Detail Information
						</div>
						<div class="page-item-content view-item-content">
							<table class="view-detail-table">
								<tr>
									<td>
										Date
									</td>
									<td class="view-info-mid">
										:
									</td>
									<td>
										{{date('d F Y', strtotime($tpayment->date))}}
									</td>
								</tr>
								<tr>
									<td>
										Payment Method
									</td>
									<td class="view-info-mid">
										:
									</td>
									<td>
										@if($tpayment->bank_id != 0)
											<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/tpayment/' . $tpayment->bank_id)}}" style="color: blue;">
												{{$tpayment->bank->name}} - {{$tpayment->bank->account_number}} - {{$tpayment->bank->account_name}}
											</a>
										@else
											Cash
										@endif
									</td>
								</tr>
							</table>
						</div>
					</div>
				</div>

				<?php
					$transaction = Transaction::find($tpayment->transaction_id);
				?>
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
									$transactiondetails = Transactiondetail::where('transaction_id', '=', $transaction->id)->get();
								?>

								@foreach ($transactiondetails as $transactiondetail)
									<?php
										$counter++;
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

				<div class="view-last-edit">
					<?php
						$createuser = Admin::find($tpayment->create_id);
						$updateuser = Admin::find($tpayment->update_id);
					?>

					<div class="page-item-title" style="margin-bottom: 20px;">
						Basic Information
					</div>

					<div class="view-last-edit-group">
						<div class="view-last-edit-title">
							Create
						</div>
						<div class="view-last-edit-item">
							<span>
								Created at
							</span>
							<span>
								:
							</span>
							<span>
								{{date('l, d F Y G:i:s', strtotime($tpayment->created_at))}}
							</span>
						</div>
						<div class="view-last-edit-item">
							<span>
								Created by
							</span>
							<span>
								:
							</span>
							<span>
								{{$createuser->name}}
							</span>
						</div>
					</div>

					<div class="view-last-edit-group">
						<div class="view-last-edit-title">
							Update
						</div>
						<div class="view-last-edit-item">
							<span>
								Updated at
							</span>
							<span>
								:
							</span>
							<span>
								{{date('l, d F Y G:i:s', strtotime($tpayment->updated_at))}}
							</span>
						</div>
						<div class="view-last-edit-item">
							<span>
								Last Updated by
							</span>
							<span>
								:
							</span>
							<span>
								{{$updateuser->name}}
							</span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection