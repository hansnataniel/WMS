<?php
	use Illuminate\Support\Str;

	use App\Models\User;
	use App\Models\Invoice;
	use App\Models\Invoicedetail;
	use App\Models\Hbt;
	use App\Models\Po;
	use App\Models\Ri;
	use App\Models\Ridetail;
?>

@extends('back.template.master')

@section('title')
	Invoice Edit
@endsection

@section('head_additional')
	{!!HTML::style('css/back/edit.css')!!}
	{!!HTML::style('css/jquery.datetimepicker.css')!!}

	<style>
		.edit-product-item {
			position: relative;
			display: table;
			padding: 10px;
			margin: 0px 0px 10px 0px;
			border: 1px solid #d2d2d2;
			font-size: 0px;
		}

		.edit-product-td {
			position: relative;
			display: inline-block;
			vertical-align: top;
			font-size: 14px;
			padding-right: 10px;
			margin-right: 10px;
			border-right: 1px solid #d2d2d2;
			line-height: 40px;
		}

		.product-name {
			width: 200px;
		}

		.edit-product-td:last-child {
			margin-right: 0px;
			padding-right: 0px;
			border-right: 0px;
		}

		.edit-product-text {
			position: relative;
			display: inline-block;
			vertical-align: top;
			border: 1px solid #d2d2d2;
			width: 150px;
			height: 40px;
			padding: 0px 10px;
			font-size: 14px;
		}

		.edit-product-close {
			position: relative;
			display: inline-block;
			vertical-align: top;
			margin: 3px 0px;
			cursor: pointer;
			width: 24px;
			height: 34px;
			margin-right: 10px;
		}

		.edit-product-close span {
			display: none;
		}

		.edit-product-close:before {
			content: '';
			position: absolute;
			left: 0px;
			top: 16px;
			width: 24px;
			height: 1px;
			background: #535353;
			-webkit-transform: rotate(45deg);
			-moz-transform: rotate(45deg);
			-ms-transform: rotate(45deg);
			transform: rotate(45deg);

			-webkit-transition: background 0.3s, -webkit-transform 0.4s;
			-moz-transition: background 0.3s, -moz-transform 0.4s;
			-ms-transition: background 0.3s, -ms-transform 0.4s;
			transition: background 0.3s, transform 0.4s;
		}

		.edit-product-close:after {
			content: '';
			position: absolute;
			left: 0px;
			top: 16px;
			width: 24px;
			height: 1px;
			background: #535353;
			-webkit-transform: rotate(-45deg);
			-moz-transform: rotate(-45deg);
			-ms-transform: rotate(-45deg);
			transform: rotate(-45deg);

			-webkit-transition: background 0.3s, -webkit-transform 0.4s;
			-moz-transition: background 0.3s, -moz-transform 0.4s;
			-ms-transition: background 0.3s, -ms-transform 0.4s;
			transition: background 0.3s, transform 0.4s;
		}

		.edit-product-close:hover:before {
			background: #000;
			-webkit-transition: background 0.3s;
			-moz-transition: background 0.3s;
			-ms-transition: background 0.3s;
			transition: background 0.3s;
		}

		.edit-product-close:hover:after {
			background: #000;
			-webkit-transition: background 0.3s;
			-moz-transition: background 0.3s;
			-ms-transition: background 0.3s;
			transition: background 0.3s;

			/*-webkit-transform: rotate(0deg);*/
			/*-moz-transform: rotate(0deg);*/
			/*-ms-transform: rotate(0deg);*/
			/*transform: rotate(0deg);*/

			/*-webkit-transition: -webkit-transform 0.4s;*/
			/*-moz-transition: -moz-transform 0.4s;*/
			/*-ms-transition: -ms-transform 0.4s;*/
			/*transition: transform 0.4s;*/
		}

		.close-active:before {
			opacity: 0;
			-webkit-transform: rotate(90deg);
			-moz-transform: rotate(90deg);
			-ms-transform: rotate(90deg);
			transform: rotate(90deg);

			-webkit-transition: -webkit-transform 0.4s, opacity 0.4s;
			-webkit-transition: -moz-transform 0.4s, opacity 0.4s;
			-webkit-transition: -ms-transform 0.4s, opacity 0.4s;
			-webkit-transition: transform 0.4s, opacity 0.4s;
		}

		.close-active:after {
			right: 0px;
			left: auto;
			opacity: 0;
			-webkit-transform: rotate(90deg);
			-moz-transform: rotate(90deg);
			-ms-transform: rotate(90deg);
			transform: rotate(90deg);

			-webkit-transition: -webkit-transform 0.4s, opacity 0.4s;
			-webkit-transition: -moz-transform 0.4s, opacity 0.4s;
			-webkit-transition: -ms-transform 0.4s, opacity 0.4s;
			-webkit-transition: transform 0.4s, opacity 0.4s;
		}

		.edit-product-prepend {
			position: relative;
			display: inline-block;
			vertical-align: top;
			background: #f7961e;
			color: #fff;
			font-size: 12px;
			line-height: 40px;
			height: 40px;
			width: 35px;
			text-align: center;
			border: 1px solid #d2d2d2;
			border-right: 0px;
		}

		.edit-product-text1 {
			position: relative;
			display: inline-block;
			vertical-align: top;
			height: 40px;
			width: 150px;
			padding: 0px 10px;
			border: 1px solid #d2d2d2;
			border-left: 0px;
			font-size: 14px;
		}

		.rec-container {
			position: relative;
			display: block;
			width: 100%;
			font-size: 0px;
			padding-left: 35px;
		}

		.rec-group {
			position: relative;
			display: block;
		}

		.rec-group.rec-title {
			font-weight: bold;
			/*border-top: double #d2d2d2;*/
			border-bottom: double #d2d2d2;
		}

		.rec-item {
			position: relative;
			display: inline-block;
			vertical-align: top;
			padding: 5px 10px;
			font-size: 14px;
			width: 50%;
		}

		.rec-group .rec-title.rec-title {
			padding: 10px;
			border-bottom: 1px solid #d2d2d2;
		}

		.rec-switch {
			position: relative;
			display: block;
			padding-left: 45px;
			font-size: 12px;
			font-style: italic;
			cursor: pointer;
			padding-top: 20px;
			padding-bottom: 10px;
			color: #f7961e;
		}

		.rec-empty {
			position: relative;
			display: block;
			padding-left: 45px;
			font-size: 12px;
			color: red;
			font-style: italic;
			padding-top: 10px;
		}

		.edit-supplier-name {
			position: relative;
			display: table;
			font-size: 20px;
			border-bottom: 1px solid #0d0f3b;
			padding-bottom: 5px;
			margin-bottom: 15px;
			width: 487px;
		}

		.edit-supplier-name span {
			line-height: 40px;
			padding-left: 5px;
			border-left: 1px solid #0d0f3b;
		}

		.edit-free-container {
			position: relative;
			display: block;
			padding-left: 15px;
			border-left: 5px solid #0d0f3b;
		}

		.edit-free-title {
			position: relative;
			display: block;
			font-size: 18px;
			border-bottom: 1px solid #d2d2d2;
			padding-bottom: 5px;
			margin-bottom: 10px;
			width: 467px;
		}

		.edit-supplier-group {
			position: relative;
			display: block;
			margin-bottom: 30px;
		}
	</style>
@endsection

@section('js_additional')
	{!!HTML::script('js/jquery.datetimepicker.js')!!}

	<script>
		$(document).ready(function(){
			$('.datetimepicker').datetimepicker({
				timepicker: false,
				format: 'Y-m-d',
				maxDate: 0
			});

			$('.supplier-select').live('change', function(){
				var data = $(this).val();
				alert('done');

				if(data != '')
				{
	    			$('.ri-parent .edit-form-note').text('Loading...');

					$.ajax({
						type: "GET",
						url: "{{URL::to(Crypt::decrypt($setting->admin_url) . '/invoice/supplier')}}/"+data,
						success:function(msg) {
							$('.ri-parent').html(msg);
						},
						error:function(msg) {
							$('body').html(msg.responseText);
						}
					});
				}
			});

			// if($('.supplier-select').val() != '')
			// {
				// $('.supplier-select').change();
			// }

	        $('.ri-select').live('change', function(){
	        	var data = $(this).val();

	        	if(data != '')
	        	{
					$(this).parent().find('.edit-form-note').text('Loading...');
	        		$.ajax({
			        	type: "GET",
		        		url: "{{URL::to(Crypt::decrypt($setting->admin_url) . '/invoice/ri')}}/"+data,
		        		success:function(msg) {
			    			$('.ri-parent .edit-form-note').text('');
		        			$('.ri-result .edit-form-text').prepend(msg);
		        		},
		        		error:function(msg) {
		        			$('body').html(msg.responseText);
		        		}
	        		});
	        	}
	        });

			$('.edit-product-close').live('click', function(){
				var ids = $(this).attr('data');
				var supplier = $(this).attr('supplier');

				if($(this).hasClass('close-active'))
				{

				}
				else
				{
					$(this).addClass('close-active');
					$(this).find('span').delay(100).fadeIn();
					$(this).delay(100).animate({
						'width': 85
					});

					$.ajax({
						type: "GET",
						url: "{{URL::to(Crypt::decrypt($setting->admin_url) . '/invoice/drop')}}/"+ids+"/"+supplier,
						success:function(msg) {
							$('.ri-parent .edit-right').html(msg);
							$('.group'+ids).remove();
						},
						error:function(msg) {
							$('body').html(msg.responseText);
							// alert('done');
						}
					});
				}
			});
		});
	</script>
@endsection

@section('page_title')
	Invoice Edit
	<span>
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/dashboard')}}">Dashboard</a> / <a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/invoice')}}">Invoice</a> / <span>Invoice Edit</span>
	</span>
@endsection

@section('help')
	<ul class="menu-help-list-container">
		<li>
			Isi date dengan tanggal Anda membuat invoice
		</li>
		<li>
			Setelah mengisi date, cari Recieve Item menggunakan No Nota dari recieve item yang Anda buat
		</li>
		<li>
			Setelah Anda memilih recieve item, maka akan muncul data-data yang sudah Anda terima
		</li>
		<li>
			Isi Price berdasarkan Invoice yang Anda terima di setiap data yang ada di Purchase Order
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
					<a class="edit-button-item edit-button-back" href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/invoice')}}">
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
				{!!Form::model($invoice, ['url' => URL::to(Crypt::decrypt($setting->admin_url) . '/invoice/' . $invoice->id), 'method' => 'PUT', 'files' => true])!!}
					<div class="page-group">
						<div class="page-item col-1">
							<div class="page-item-title">
								Detail Information
							</div>
							<div class="page-item-content edit-item-content">
								<div class="edit-form-group">
									{!!Form::label('no_nota', 'No Nota', ['class'=>'edit-form-label'])!!}
									{!!Form::text('no_nota', $invoice->no_nota, ['class'=>'edit-form-text large', 'style'=>"border: none; padding: 0px; font-size: 18px; font-weight: bold;", "readonly"])!!}
								</div>
								<div class="edit-form-group">
									{!!Form::label('date', 'Date', ['class'=>'edit-form-label'])!!}
									{!!Form::text('date', null, ['class'=>'edit-form-text datetimepicker large', 'readonly', 'required'])!!}
									<span class="edit-form-note">
										*Required
									</span>
								</div>
								<div class="edit-form-group supplier-parent">
									{!!Form::label('supplier', 'Supplier', ['class'=>'edit-form-label'])!!}
									{!!Form::select('supplier', $supplier_options, $invoice->supplier_id, ['class'=>'edit-form-text supplier-select select large'])!!}
									<span class="edit-form-note">
										*Required
									</span>
								</div>
								<div class="edit-form-group ri-parent">
									<?php
										$hbts = Hbt::where('status', '=', false)->get();
										foreach ($hbts as $hbt) {
											$hbtids[] = $hbt->ri_id;
										}

										$pos = Po::where('supplier_id', '=', $invoice->supplier_id)->get();
										foreach ($pos as $po) {
											$poids[] = $po->id;
										}

										$getris = Ri::whereIn('po_id', $poids)->whereNotIn('id', Session::get('add_product'))->orderBy('id', 'desc')->get();
										$ri_options[''] = 'Select Recieve Item';
										foreach ($getris as $getri) {
											$ri_options[$getri->id] = $getri->no_nota;
										}
									?>
									{!!Form::label('recieve_item', 'Recieve Item', ['class'=>'edit-form-label'])!!}
									{!!Form::select('recieve_item', $ri_options, null, ['class'=>'edit-form-text ri-select select large'])!!}
									<span class="edit-form-note">
										
									</span>
								</div>
								<div class="edit-form-group ri-result">
									<div class="edit-form-label"></div>
									<div class="edit-form-text">
										@foreach($ris as $ri)
											<?php
												$ridetails = Ridetail::where('ri_id', '=', $ri->id)->where('product_id', '=', 0)->where('qty', '>', 0)->get();
												$frees = Ridetail::where('ri_id', '=', $ri->id)->where('product_id', '!=', 0)->get();
											?>

											<div class="edit-supplier-group group{{$ri->id}}">
												<div class="edit-supplier-name">
													@if($ri->po_id != 0)
														<div class="edit-product-close" supplier="{{$ri->po->supplier_id}}" data="{{$ri->id}}">
													@else
														<div class="edit-product-close" supplier="{{$ri->supplier_id}}" data="{{$ri->id}}">
													@endif
														<span>Loading...</span>
													</div>
													<span>
														{{$ri->no_nota}}
													</span>
												</div>
												@if($ri->po_id != 0)
													@foreach($ridetails as $ridetail)
														<?php
															$invoicedetail = Invoicedetail::where('ridetail_id', $ridetail->id)->first();
														?>
														<div class="edit-product-item">
															<div class="edit-product-td product-name">
																{{$ridetail->podetail->product->name}}
															</div>
															<div class="edit-product-td" style="width: 60px;">
																Qty : {{$ridetail->qty}}
															</div>
															<div class="edit-product-td" style="font-size: 0px;">
																{!!Form::hidden('ridetail[' . $ridetail->id . '][qty]', $ridetail->qty)!!}
																<div class="edit-product-prepend">
																	Rp
																</div>
																{!!Form::input('number', 'ridetail[' . $ridetail->id . '][price]', $invoicedetail->price, array('class'=>'edit-product-text', 'placeholder'=>'Price', 'required', 'min'=>'0'))!!}
															</div>
														</div>
													@endforeach
												@endif
												<?php
													$frees = Ridetail::where('ri_id', '=', $ri->id)->where('product_id', '!=', 0)->get();
												?>
												@if(!$frees->isEmpty())
													<div class="edit-free-container">
														<div class="edit-free-title">
															Free Product
														</div>
														@foreach($frees as $free)
															<div class="edit-product-item">
																<div class="edit-product-td product-name">
																	{{$free->product->name}}
																</div>
																<div class="edit-product-td" style="width: 60px;">
																	Qty : {{$free->qty}}
																</div>
																<div class="edit-product-td">
																	Rp 0
																	{!!Form::hidden('ridetail[' . $free->id . '][qty]', $free->qty)!!}
																	{!!Form::hidden('ridetail[' . $free->id . '][price]', '0')!!}
																</div>
															</div>
														@endforeach
													</div>
												@endif
											</div>
										@endforeach
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