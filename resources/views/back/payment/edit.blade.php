<?php
	use Illuminate\Support\Str;

	use App\Models\User;
	use App\Models\Payment;
	use App\Models\Ri;
	use App\Models\Ridetail;
	use App\Models\Invoice;
	use App\Models\Invoicedetail;
?>

@extends('back.template.master')

@section('title')
	Payment Edit
@endsection

@section('head_additional')
	{!!HTML::style('css/back/edit.css')!!}
	{!!HTML::style('css/jquery.datetimepicker.css')!!}

	<style>
		.invoice-table {
			font-size: 12px;
			border: 1px solid #d2d2d2;
			border-collapse: collapse;
			width: 300px;
		}

		.invoice-title {
			background: #0d0f3b;
			color: #fff;
		}

		.invoice-title td {
			font-size: 14px;
			text-align: center !important;
		}

		.invoice-table td {
			padding: 10px;
		}

		.invoice-table td:last-child {
			text-align: right;
			border-left: 1px solid #d2d2d2;
		}

		.invoice-total {
			font-weight: bold;
			font-size: 14px;
			border-top: 1px dashed #535353;
			border-bottom: 1px dashed #535353;
		}

		.invoice-total td:first-child {
			text-align: center;
		}

		.invoice-nota {
			position: relative;
			display: block;
			font-size: 16px;
			font-weight: bold;
			color: #0d0f3b;
			border-bottom: 1px solid #535353;
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
				minDate: "{{date('Y/m/d', strtotime($payment->invoice->date))}}",
				maxDate: 0
			});

			$('.invoice-select').live('change', function(){
				var data = $(this).val();

				if(data != '')
				{
					$('.invoice-select').parent().find('.edit-form-note').text('Loading...');
					$.ajax({
						type: "GET",
						url: "{{URL::to(Crypt::decrypt($setting->admin_url) . '/payment/invoice')}}/"+data,
						success:function(msg) {
							$('.invoice-select').parent().find('.edit-form-note').text('*Required');
							$('.invoice-result').html(msg);
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
	Payment Edit
	<span>
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/dashboard')}}">Dashboard</a> / <a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/payment')}}">Payment</a> / <span>Payment Edit</span>
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
					<a class="edit-button-item edit-button-back" href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/payment')}}">
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
				{!!Form::model($payment, ['url' => URL::to(Crypt::decrypt($setting->admin_url) . '/payment/' . $payment->id), 'method' => 'PUT', 'files' => true])!!}
					<div class="page-group">
						<div class="page-item col-1">
							<div class="page-item-title">
								Detail Information
							</div>
							<div class="page-item-content edit-item-content">
								<div class="edit-form-group">
									{!!Form::label('no_nota', 'No Nota', ['class'=>'edit-form-label'])!!}
									{!!Form::text('no_nota', $payment->no_nota, ['class'=>'edit-form-text large', 'style'=>"border: none; padding: 0px; font-size: 18px; font-weight: bold;", "readonly"])!!}
								</div>
								<div class="edit-form-group">
									{!!Form::label('bank', 'Bank', ['class'=>'edit-form-label'])!!}
									{!!Form::select('bank', $bank_options, $payment->bank_id, ['class'=>'edit-form-text select large'])!!}
									<span class="edit-form-note">
										*Required
									</span>
								</div>
								<div class="edit-form-group">
									{!!Form::label('invoice', 'Invoice', ['class'=>'edit-form-label'])!!}
									{!!Form::select('invoice', $invoice_options, $payment->invoice_id, ['class'=>'edit-form-text invoice-select select large'])!!}
									<span class="edit-form-note">
										*Required
									</span>
								</div>
								<div class="invoice-result">
									<div class="edit-form-group">
										{!!Form::label('date', 'Date', ['class'=>'edit-form-label'])!!}
										{!!Form::text('date', $payment->date, ['class'=>'edit-form-text datetimepicker large', 'readonly', 'required', 'placeholder'=>'Select Invoice First'])!!}
									</div>
									<div class="edit-form-group nota-result">
										<div class="edit-form-label"></div>
										<div class="edit-form-text">
											<span class="invoice-nota">
												Invoice : 
												<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/invoice/view/' . $payment->invoice_id)}}" style="text-decoration: none;" target="_blank">
													{{$payment->invoice->no_nota}}
												</a>
											</span>
											<table class="invoice-table">
												<tr class="invoice-title">
													<td>
														Recieve Item
													</td>
													<td>
														Sub Total
													</td>
												</tr>

												<?php
													$ris = Ri::whereIn('id', $getridetailids)->get();
													// $total = 0;
												?>
												@foreach($ris as $ri)
													<?php
														$ridetails = Ridetail::where('ri_id', '=', $ri->id)->get();
														foreach ($ridetails as $ridetail) {
															$ridetailids[] = $ridetail->id;
														}
														$invoicedetails = Invoicedetail::where('invoice_id', '=', $payment->invoice_id)->whereIn('ridetail_id', $ridetailids)->where('price', '!=', 0)->get();
														$subtotal = 0;
														foreach ($invoicedetails as $invoicedetail) {
															$subtotal = $subtotal + ($invoicedetail->price * $invoicedetail->qty);
														}
														// $total = $total + $subtotal;
													?>
													<tr>
														<td>
															<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/ri/view/' . $ri->id)}}" style="text-decoration: none;" target="_blank">
																{{$ri->no_nota}}
															</a>
														</td>
														<td>
															Rp {{number_format($subtotal)}}
														</td>
													</tr>
													<?php
														$ridetailids = array();
													?>
												@endforeach
												<tr class="invoice-total">
													<td>
														Total
													</td>
													<td>
														<?php
															$total = 0;
															$getinvoicedetails = Invoicedetail::where('invoice_id', '=', $payment->invoice_id)->get();
															foreach ($getinvoicedetails as $getinvoicedetail) {
																$total = $total + ($getinvoicedetail->qty * $getinvoicedetail->price);
															}
														?>
														Rp {{number_format($total)}}
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