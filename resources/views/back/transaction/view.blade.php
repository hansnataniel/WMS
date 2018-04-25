<?php
	use Illuminate\Support\Str;

	use App\Models\Admin;
	use App\Models\Transaction;
	use App\Models\Transactiondetail;
?>

@extends('back.template.master')

@section('title')
	Transaction View
@endsection

@section('head_additional')
	{!!HTML::style('css/back/detail.css')!!}
	{!!HTML::style('css/back/index.css')!!}
@endsection

@section('js_additional')
	<script type="text/javascript">
		$(document).ready(function(){
			
		});
	</script>
@endsection

@section('page_title')
	Transaction View
	<span>
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/dashboard')}}">Dashboard</a> / <a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/transaction')}}">Transaction</a> / <span>Transaction View</span>
	</span>
@endsection

@section('help')
	<ul class="menu-help-list-container">
		<li>
			Gunakan tombol Edit untuk mengedit Transaction
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
						<a class="view-button-item view-button-back" href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/transaction')}}"></a>
					@endif
					{{$transaction->trans_id}}
					<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/transaction/' . $transaction->id . '/edit')}}" class="view-button-item view-button-edit">
						Edit
					</a>
				</h1>

				<?php
					if($transaction->code_id != null)
					{
						$generator1 = new Picqer\Barcode\BarcodeGeneratorPNG();
						echo '<img style="margin-bottom: 20px; position: relative; display: block;" src="data:image/png;base64,' . base64_encode($generator1->getBarcode("$transaction->code_id", $generator1::TYPE_CODE_128)) . '">';
					}
				?>
				
				@if (file_exists(public_path() . '/usr/img/transaction/' . $transaction->id . '_' . Str::slug($transaction->name, '_') . '_thumb.jpg'))
					{!!HTML::image('usr/img/transaction/' . $transaction->id . '_' . Str::slug($transaction->name, '_') . '_thumb.jpg?lastmod=' . Str::random(5), '', ['class'=>'view-photo'])!!}
				@endif
				<div class="page-group">
					<div class="page-item col-1">
						<div class="page-item-title">
							Detail Information
						</div>
						<div class="page-item-content view-item-content">
							<table class="view-detail-table">
								<tr>
									<td>
										Customer
									</td>
									<td class="view-info-mid">
										:
									</td>
									<td>
										<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/customer/' . $transaction->customer_id)}}" style="color: blue;">
											{{$transaction->customer->name}}
										</a>
									</td>
								</tr>
								<tr>
									<td>
										Date
									</td>
									<td class="view-info-mid">
										:
									</td>
									<td>
										{{date('d F Y', strtotime($transaction->date))}}
									</td>
								</tr>
								<tr>
									<td>
										Status
									</td>
									<td class="view-info-mid">
										:
									</td>
									<td>
										{!!$transaction->status == 'Waiting for Payment' ? "<span class='text-orange'>Waiting for Payment</span>":"<span class='text-green'>Paid</span>"!!}
									</td>
								</tr>
							</table>
						</div>
					</div>
				</div>
				<div class="page-group">
					<div class="page-item col-1">
						<div class="page-item-title">
							Product Detail
						</div>
						<div class="page-item-content view-item-content">
							<?php
								$transactiondetails = Transactiondetail::where('transaction_id', '=', $transaction->id)->get();
							?>
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
									<th style="text-align: right;">
										Price
									</th>
									<th style="text-align: right;">
										Discount
									</th>
									<th style="text-align: right;">
										Sub Price
									</th>
								</tr>
								<?php
									$counter = 0;
									$total = 0;
								?>
								@foreach ($transactiondetails as $transactiondetail)
									<?php 
										$counter++; 
										if($transactiondetail->discounttype == 0)
										{
											$total += ($transactiondetail->price * $transactiondetail->qty) - $transactiondetail->discount;
										}
										else
										{
											$total += ($transactiondetail->price * $transactiondetail->qty) - ((($transactiondetail->price * $transactiondetail->qty) * $transactiondetail->discount) / 100);
										}
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
				<div class="view-last-edit">
					<?php
						$createuser = Admin::find($transaction->create_id);
						$updateuser = Admin::find($transaction->update_id);
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
								{{date('l, d F Y G:i:s', strtotime($transaction->created_at))}}
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
								{{date('l, d F Y G:i:s', strtotime($transaction->updated_at))}}
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