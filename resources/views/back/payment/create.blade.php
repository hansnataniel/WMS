<?php
	use Illuminate\Support\Str;

	use App\Models\User;
	use App\Models\Payment;
?>

@extends('back.template.master')

@section('title')
	New Payment
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
			// $('.datetimepicker').datetimepicker({
			// 	timepicker: false,
			// 	format: 'Y-m-d',
			// 	maxDate: 0
			// });

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

			if ($('.invoice-select').val() != '')
			{
				$('.invoice-select').change();
			}
		});
	</script>
@endsection

@section('page_title')
	New Payment
	<span>
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/dashboard')}}">Dashboard</a> / <a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/payment')}}">Payment</a> / <span>New Payment</span>
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
				{!!Form::open(['url' => URL::to(Crypt::decrypt($setting->admin_url) . '/payment'), 'files' => true])!!}
					<div class="page-group">
						<div class="page-item col-1">
							<div class="page-item-title">
								Detail Information
							</div>
							<div class="page-item-content edit-item-content">
								<div class="edit-form-group">
									<?php
										$lastpayment = Payment::orderBy('id', 'desc')->first();
										if($lastpayment == null)
										{
											$no_nota = 'PAY/' . date('ymd') . '/1001';
										}
										else
										{
											$no_nota = 'PAY/' . date('ymd') . '/' . ($lastpayment->id + 1001);
										}
									?>
									{!!Form::label('no_nota', 'No Nota', ['class'=>'edit-form-label'])!!}
									{!!Form::text('no_nota', $no_nota, ['class'=>'edit-form-text large', 'style'=>"border: none; padding: 0px; font-size: 18px; font-weight: bold;", "readonly"])!!}
								</div>
								<div class="edit-form-group">
									{!!Form::label('bank', 'Bank', ['class'=>'edit-form-label'])!!}
									{!!Form::select('bank', $bank_options, null, ['class'=>'edit-form-text select large'])!!}
									<span class="edit-form-note">
										*Required
									</span>
								</div>
								<div class="edit-form-group">
									{!!Form::label('invoice', 'Invoice', ['class'=>'edit-form-label'])!!}
									{!!Form::select('invoice', $invoice_options, null, ['class'=>'edit-form-text invoice-select select large'])!!}
									<span class="edit-form-note">
										*Required
									</span>
								</div>
								<div class="invoice-result">
									<div class="edit-form-group">
										{!!Form::label('date', 'Date', ['class'=>'edit-form-label'])!!}
										{!!Form::text('date', null, ['class'=>'edit-form-text datetimepicker large', 'readonly', 'required', 'placeholder'=>'Select Invoice First'])!!}
									</div>
									<div class="edit-form-group nota-result">
										<div class="edit-form-label"></div>
										<div class="edit-form-text">
											
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