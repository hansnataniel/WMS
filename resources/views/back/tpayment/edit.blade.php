<?php
	use Illuminate\Support\Str;

	use App\Models\User;
	use App\Models\tpayment;
	use App\Models\Ri;
	use App\Models\Ridetail;
	use App\Models\Transaction;
	use App\Models\Transactiondetail;
?>

@extends('back.template.master')

@section('title')
	Transaction Payment Edit
@endsection

@section('head_additional')
	{!!HTML::style('css/back/edit.css')!!}
	{!!HTML::style('css/back/index.css')!!}
	{!!HTML::style('css/jquery.datetimepicker.css')!!}

	<style>
		
		.transaction-nota {
			position: relative;
			display: block;
			font-size: 16px;
			font-weight: bold;
			color: #0d0f3b;
			/*border-bottom: 1px solid #535353;*/
			margin-bottom: 10px;
			padding: 0px 0px 5px;
		}
	</style>
@endsection

@section('js_additional')
	{!!HTML::script('js/jquery.datetimepicker.js')!!}

	<script type="text/javascript">
		$(document).ready(function(){
			$('.datetimepicker').datetimepicker({
				timepicker: false,
				format: 'Y-m-d',
				minDate: "{{date('Y/m/d', strtotime($tpayment->transaction->date))}}",
				maxDate: 0
			});

			$('.transaction-select').live('change', function(){
				var data = $(this).val();

				if(data != '')
				{
					$('.transaction-select').parent().find('.edit-form-note').text('Loading...');
					$.ajax({
						type: "GET",
						url: "{{URL::to(Crypt::decrypt($setting->admin_url) . '/tpayment/transaction')}}/"+data,
						success:function(msg) {
							$('.transaction-select').parent().find('.edit-form-note').text('*Required');
							$('.transaction-result').html(msg);
						},
						error:function(msg) {
							$('body').html(msg.responseText);
						}
					});
				}
			});
		});
	</script>
@endsection

@section('page_title')
	Transaction Payment Edit
	<span>
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/dashboard')}}">Dashboard</a> / <a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/tpayment')}}">Transaction Payment</a> / <span>Transaction Payment Edit</span>
	</span>
@endsection

@section('help')
	<ul class="menu-help-list-container">
		
	</ul>
@endsection

@section('content')
	<div class="page-group">
		<div class="page-item col-1">
			<div class="page-item-content">
				@if($request->session()->has('last_url'))
					<a class="edit-button-item edit-button-back" href="{{URL::to($request->session()->get('last_url'))}}">
						Back
					</a>
				@else
					<a class="edit-button-item edit-button-back" href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/tpayment')}}">
						Back
					</a>
				@endif

				<div class="page-item-error-container">
					@foreach ($errors->all() as $error)
						<div class='page-item-error-item'>
							{{$error}}
						</div>
					@endforeach
				</div>
				{!!Form::model($tpayment, ['url' => URL::to(Crypt::decrypt($setting->admin_url) . '/tpayment/' . $tpayment->id), 'method' => 'PUT', 'files' => true])!!}
					<div class="page-group">
						<div class="page-item col-1">
							<div class="page-item-title">
								Detail Information
							</div>
							<div class="page-item-content edit-item-content">
								<div class="edit-form-group">
									{!!Form::label('no_nota', 'No Nota', ['class'=>'edit-form-label'])!!}
									{!!Form::text('no_nota', $tpayment->no_nota, ['class'=>'edit-form-text large', 'style'=>"border: none; padding: 0px; font-size: 18px; font-weight: bold;", "readonly"])!!}
								</div>
								<div class="edit-form-group">
									{!!Form::label('payment_method', 'Payment Method', ['class'=>'edit-form-label'])!!}
									{!!Form::select('payment_method', $bank_options, $tpayment->bank_id, ['class'=>'edit-form-text select large'])!!}
									<span class="edit-form-note">
										*Required
									</span>
								</div>
								<div class="edit-form-group">
									{!!Form::label('transaction', 'Transaction', ['class'=>'edit-form-label'])!!}
									{!!Form::select('transaction', $transaction_options, $tpayment->transaction_id, ['class'=>'edit-form-text transaction-select select large'])!!}
									<span class="edit-form-note">
										*Required
									</span>
								</div>
								<div class="transaction-result">
									<div class="edit-form-group">
										{!!Form::label('date', 'Date', ['class'=>'edit-form-label'])!!}
										{!!Form::text('date', $tpayment->date, ['class'=>'edit-form-text datetimepicker large', 'readonly', 'required', 'placeholder'=>'Select Transaction First'])!!}
									</div>
									<div class="edit-form-group nota-result">
										<?php
											$transaction = Transaction::find($tpayment->transaction_id);
										?>
										<div class="edit-form-label"></div>
										<div class="edit-form-text">
											<span class="transaction-nota">
												Transaction ID : 
												<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/transaction/view/' . $transaction->id)}}" style="text-decoration: none;" target="_blank">
													{{$transaction->trans_id}}
												</a>
											</span>
											<table class="index-table">
												<tr class="index-tr-title">
													<td>
														#
													</td>
													<td>
														Product
													</td>
													<td>
														Rak
													</td>
													<td>
														Qty
													</td>
													<td>
														Price
													</td>
													<td>
														Discount
													</td>
													<td>
														Sub Total
													</td>
												</tr>

												<?php
													$transactiondetails = Transactiondetail::where('transaction_id', '=', $transaction->id)->get();
													$total = 0;
													$counter = 0;
												?>
												@foreach($transactiondetails as $transactiondetail)
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
															<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/product/view/' . $transaction->product_id)}}" style="color: blue;" target="_blank">
																{{$transactiondetail->product->name}}
															</a>
														</td>
														<td>
															<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/rak/view/' . $transaction->rak_id)}}" style="color: blue;" target="_blank">
																{{$transactiondetail->rak->name}}
															</a>
														</td>
														<td>
															{{$transactiondetail->qty}}
														</td>
														<td style="text-align: right;">
															Rp {{number_format($transactiondetail->price)}}
														</td>
														<td>
															@if($transactiondetail->discounttype == 0)
																Rp {{number_format($transactiondetail->discount)}}
															@else
																{{$transactiondetail->discount}} %
															@endif
														</td>
														<td>
															@if($transactiondetail->discounttype == 0)
																Rp {{number_format(($transactiondetail->price * $transactiondetail->qty) - $transactiondetail->discount)}}
															@else
																Rp {{number_format(($transactiondetail->price * $transactiondetail->qty) - ((($transactiondetail->price * $transactiondetail->qty) * $transactiondetail->discount) / 100))}}
															@endif
														</td>
													</tr>
													<?php
														$transactiondetailids = array();
													?>
												@endforeach
												<tr class="transaction-total">
													<td colspan="6" style="text-align: right; font-size: 18px;">
														Discount
													</td>
													<td style="font-size: 18px;">
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
												<tr class="transaction-total">
													<td colspan="6" style="text-align: right; font-size: 18px;">
														Total
													</td>
													<td style="font-size: 18px;">
														Rp {{number_format($transaction->total)}}
													</td>
												</tr>
												<tr class="transaction-total">
													<td colspan="6" style="text-align: right; font-size: 18px;">
														Amount to Pay
													</td>
													<td style="font-size: 18px;">
														Rp {{number_format($transaction->amount_to_pay)}}
													</td>
												</tr>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="page-group">
						<div class="edit-button-group">
							{{Form::submit('Save', ['class'=>'edit-button-item'])}}
							{{Form::reset('Reset', ['class'=>'edit-button-item reset'])}}
						</div>
					</div>
				{!!Form::close()!!}
			</div>
		</div>
	</div>
@endsection