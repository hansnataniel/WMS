<?php
	use Illuminate\Support\Str;

	use App\Models\User;
	use App\Models\tpayment;
?>

@extends('back.template.master')

@section('title')
	New Transaction Payment
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
			// $('.datetimepicker').datetimepicker({
			// 	timepicker: false,
			// 	format: 'Y-m-d',
			// 	maxDate: 0
			// });

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

			if ($('.transaction-select').val() != '')
			{
				$('.transaction-select').change();
			}
		});
	</script>
@endsection

@section('page_title')
	New Transaction Payment
	<span>
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/dashboard')}}">Dashboard</a> / <a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/tpayment')}}">Transaction Payment</a> / <span>New Transaction Payment</span>
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
				{!!Form::open(['url' => URL::to(Crypt::decrypt($setting->admin_url) . '/tpayment'), 'files' => true])!!}
					<div class="page-group">
						<div class="page-item col-1">
							<div class="page-item-title">
								Detail Information
							</div>
							<div class="page-item-content edit-item-content">
								<div class="edit-form-group">
									<?php
										$lasttpayment = tpayment::orderBy('id', 'desc')->first();
										if($lasttpayment == null)
										{
											$no_nota = 'TPAY/' . date('ymd') . '/1001';
										}
										else
										{
											$no_nota = 'TPAY/' . date('ymd') . '/' . ($lasttpayment->id + 1001);
										}
									?>
									{!!Form::label('no_nota', 'No Nota', ['class'=>'edit-form-label'])!!}
									{!!Form::text('no_nota', $no_nota, ['class'=>'edit-form-text large', 'style'=>"border: none; padding: 0px; font-size: 18px; font-weight: bold;", "readonly"])!!}
								</div>
								<div class="edit-form-group">
									{!!Form::label('payment_method', 'Payment Method', ['class'=>'edit-form-label'])!!}
									{!!Form::select('payment_method', $bank_options, null, ['class'=>'edit-form-text select large'])!!}
									<span class="edit-form-note">
										*Required
									</span>
								</div>
								<div class="edit-form-group">
									{!!Form::label('transaction', 'Transaction', ['class'=>'edit-form-label'])!!}
									@if(Session::has('save-pay'))
										{!!Form::select('transaction', $transaction_options, Session::get('save-pay'), ['class'=>'edit-form-text transaction-select select large'])!!}
									@else
										{!!Form::select('transaction', $transaction_options, null, ['class'=>'edit-form-text transaction-select select large'])!!}
									@endif
									<span class="edit-form-note">
										*Required
									</span>
								</div>
								<div class="transaction-result">
									<div class="edit-form-group">
										{!!Form::label('date', 'Date', ['class'=>'edit-form-label'])!!}
										{!!Form::text('date', null, ['class'=>'edit-form-text datetimepicker large', 'readonly', 'required', 'placeholder'=>'Select Transaction First'])!!}
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