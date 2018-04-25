<?php
	use Illuminate\Support\Str;

	use App\Models\User;
	use App\Models\Treturn;
?>

@extends('back.template.master')

@section('title')
	New Return
@endsection

@section('head_additional')
	{!!HTML::style('css/back/edit.css')!!}
	{!!HTML::style('css/back/po.css')!!}
	{!!HTML::style('css/back/index.css')!!}
	{!!HTML::style('css/jquery.datetimepicker.css')!!}

	<style type="text/css">
		.index-table th, td {
			vertical-align: middle;
		}

		.add-new-span {
			position: absolute;
			display: none;
			left: 100px;
			top: -33px;
			font-size: 12px;
			font-style: italic;
			color: red;
		}
	</style>
@endsection

@section('js_additional')
	{!!HTML::script('js/jquery.datetimepicker.js')!!}

	<script>
		$(document).ready(function(){
			$('.customer-select').live('change', function(){
	        	var data = $(this).val();

	        	if(data != '')
	        	{
	        		$('.customer-result .edit-form-note').text('Loading...');
	        		$.ajax({
	        			type: "GET",
	        			url: "{{URL::to(Crypt::decrypt($setting->admin_url) . '/treturn/ri')}}/"+data,
	        			success: function(msg) {
	        				$('.customer-result').html(msg);
	        			},
	        			error: function(msg) {
	        				$('body').html(msg.responseText);
	        			}
	        		});
	        	}
	        });

	        $('.ri-select').live('change', function(){
	        	var data = $(this).val();

	        	if(data != '')
	        	{
	        		$('.ri-result .edit-form-note').text('Loading...');
	        		$.ajax({
	        			type: "GET",
	        			url: "{{URL::to(Crypt::decrypt($setting->admin_url) . '/treturn/product')}}/"+data,
	        			success: function(msg) {
	        				$('.ri-result').html(msg);
	        			},
	        			error: function(msg) {
	        				$('body').html(msg.responseText);
	        			}
	        		});
	        	}
	        });


			$('.add-new').live('click', function(){
				if($('.ri-select').val() != '')
				{
					$('.pop-result').html($('.loading').html());
					$('.pop-container').fadeIn();

					var data = $('.ri-select').val();

					$.ajax({
						type: "GET",
						url: "{{URL::to(Crypt::decrypt($setting->admin_url) . '/treturn/add')}}/"+data,
						success:function(msg) {
							$('.pop-result').html(msg);

							$('.pop-container').find('.index-del-item').each(function(e){
								$(this).delay(70*e).animate({
				                    opacity: 1,
				                    top: 0
				                }, 300);
							});
						},
						error:function(msg) {
							$('body').html(msg.responseText);
						}
					});
				}
				else
				{
					$('.add-new-span').stop().fadeIn().delay(3000).fadeOut();
				}	

			});

			if($('.customer-select').val() != '')
			{
				$('.customer-select').change();
			}
		});
	</script>
@endsection

@section('page_title')
	New Return
	<span>
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/dashboard')}}">Dashboard</a> / <a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/treturn')}}">Return</a> / <span>New Return</span>
	</span>
@endsection

@section('help')
	<ul class="menu-help-list-container">
		<li>
			Isi data Customer dan pilih Transaction yang ingin Anda Buat Return
		</li>
		<li>
			Isi Qty dan Price di setiap data yang akan Anda masukkan pada Return
		</li>
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
					<a class="edit-button-item edit-button-back" href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/treturn')}}">
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
				{!!Form::open(['url' => URL::to(Crypt::decrypt($setting->admin_url) . '/treturn'), 'files' => true])!!}
					<div class="page-group">
						<div class="page-item col-1">
							<div class="page-item-title">
								Detail Information
							</div>
							<div class="page-item-content edit-item-content">
								<div class="edit-form-group">
									<?php
										$lastpo = Treturn::orderBy('id', 'desc')->first();
										if($lastpo == null)
										{
											$no_nota = 'TRET/' . date('ymd') . '/1001';
										}
										else
										{
											$no_nota = 'TRET/' . date('ymd') . '/' . ($lastpo->id + 1001);
										}
									?>
									{!!Form::label('no_nota', 'No Nota', ['class'=>'edit-form-label'])!!}
									{!!Form::text('no_nota', $no_nota, ['class'=>'edit-form-text large', 'style'=>"border: none; padding: 0px; font-size: 18px; font-weight: bold;", "readonly"])!!}
								</div>
								<div class="edit-form-group">
									{!!Form::label('customer', 'Customer', ['class'=>'edit-form-label'])!!}
									{!!Form::select('customer', $customer_options, null, ['class'=>'edit-form-text customer-select select large'])!!}
									<span class="edit-form-note">
										*Required
									</span>
								</div>
								<div class="customer-result" style="margin-bottom: 15px;">
									<div class="edit-form-group">
										{!!Form::label('transaction', 'Transaction', ['class'=>'edit-form-label'])!!}
										{!!Form::select('transaction', [''=>'Select Customer First'], null, ['class'=>'edit-form-text ri-select select large'])!!}
										<span class="edit-form-note">
											*Required
										</span>
									</div>
									<div class="ri-result">
										<div class="edit-form-group">
											{!!Form::label('date', 'Date', ['class'=>'edit-form-label'])!!}
											{!!Form::text('date', null, ['class'=>'edit-form-text datetimepicker large', 'readonly', 'required', 'placeholder'=>'Select Transaction First'])!!}
										</div>
										
										<div class="edit-form-group product-result" style="padding-top: 40px;">
											<div class="edit-form-label"></div>
											<div class="edit-form-text product-container">
												<div class="add-new">
													Add New
												</div>
												<span class="add-new-span">
													*Please Select Transaction First
												</span>
												<table>
													<tr class="tr-title">
														<th style="width: 300px;">
															Product
														</th>
														<th style="text-align: right;">
															Qty
														</th>
														<th style="text-align: right;">
															Price (Rp)
														</th>
														<th style="text-align: right;">
															Subtotal (Rp)
														</th>
														<th>
														</th>
													</tr>

													<tr class="global-discount" style="border-top: double #d2d2d2; border-bottom: 0px;">
														<td colspan="4" style="font-size: 18px; text-align: right;">
															Total (Rp)
														</td>
														<td colspan="2" style="font-size: 18px;" class="total" totaldata="0">
															0
														</td>
													</tr>
												</table>
											</div>
										</div>
									</div>
								</div>
								<div class="edit-form-group">
									{!!Form::label('msg', 'Message', ['class'=>'edit-form-label'])!!}
									{!!Form::textarea('msg', null, ['class'=>'edit-form-text large area'])!!}
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

	<div class="loading">
		<span>
			Loading...
		</span>
	</div>

	<script type="text/javascript">
		function propChange() {
			$('.edit-product-text').change();
		}
	</script>
@endsection