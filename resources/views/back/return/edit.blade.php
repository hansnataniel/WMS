<?php
	use Illuminate\Support\Str;

	use App\Models\User;
	use App\Models\Ridetail;
	use App\Models\Retur;
	use App\Models\Returndetail;
	use App\Models\Supprice;
	use App\Models\Invoicedetail;

	$returndetails = Returndetail::where('return_id', '=', $return->id)->get();
?>

@extends('back.template.master')

@section('title')
	Return Edit
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
			$('.datetimepicker').datetimepicker({
				timepicker: false,
				format: 'Y-m-d',
				minDate: "{{date('Y/m/d', strtotime($return->ri->date))}}",
				maxDate: 0
			});

			$('.supplier-select').live('change', function(){
	        	var data = $(this).val();

	        	if(data != '')
	        	{
	        		$('.supplier-result .edit-form-note').text('Loading...');
	        		$.ajax({
	        			type: "GET",
	        			url: "{{URL::to(Crypt::decrypt($setting->admin_url) . '/return/ri')}}/"+data,
	        			success: function(msg) {
	        				$('.supplier-result').html(msg);
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
	        			url: "{{URL::to(Crypt::decrypt($setting->admin_url) . '/return/product')}}/"+data,
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
						url: "{{URL::to(Crypt::decrypt($setting->admin_url) . '/return/add')}}/"+data,
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

			$('.index-action-switch .delete').click(function(e){
				e.preventDefault();

				$(this).parent().parent().parent().find('span').text('Loading...');
				var dataids = $(this).attr('data');

				$.ajax({
					type: "GET",
					url: "{{URL::to(Crypt::decrypt($setting->admin_url) . '/return/drop')}}/"+dataids,
					success:function(msg) {
						$('.tr'+dataids).remove();

						var gettotal = 0;

						$('.subtotal').each(function(){
							gettotal += parseInt($(this).val());
						});

						$('.total').attr('totaldata', gettotal);
						$('.total').text(number_format(gettotal));
					},
					error:function(msg) {
						$('body').html(msg.responseText);
					}
				});
			});

			if($('.supplier-select').val() != '')
			{
				$('.supplier-select').change();
			}

			@foreach ($returndetails as $returndetail)
				<?php
					$ridetail = Ridetail::find($returndetail->ridetail_id);
				?>

				$('.index-action-switch'+{{$ridetail->id}}).click(function(e){
					e.stopPropagation();
					
					if($(this).hasClass('active'))
					{
						indexSwitchOff();
					}
					else
					{
						indexSwitchOff();

						$(this).addClass('active');
						$(this).find($('.index-action-child-container')).fadeIn();

						$(this).find($('li')).each(function(e){
							$(this).delay(50*e).animate({
			                    opacity: 1,
			                    top: 0
			                }, 300);
						});
					}
				});

				$('.index-action-switch'+{{$ridetail->id}}+' a.edit').click(function(e){
					e.preventDefault();

					var dataid = $(this).attr('data');
					// var dataid = $(this).attr('data');
					var datari = $('.tr'+dataid).attr('datari');
					var dataproduct = $('.tr'+dataid).attr('dataproduct');
					var dataquantity = $('.tr'+dataid).attr('dataquantity');
					var dataprice = $('.tr'+dataid).attr('dataprice');

					$('.pop-result').html($('.loading').html());
					$('.pop-container').fadeIn();

					$.ajax({
						type: "GET",
						url: "{{URL::to(Crypt::decrypt($setting->admin_url) . '/return/add')}}/"+datari+"/"+dataid+"/"+dataquantity+"/"+dataprice,
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
				});

				var gettotal = 0;

				$('.subtotal').each(function(){
					gettotal += parseInt($(this).val());
				});

				$('.total').attr('totaldata', gettotal);
				$('.total').text(number_format(gettotal));
			@endforeach
		});
	</script>
@endsection

@section('page_title')
	Return Edit
	<span>
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/dashboard')}}">Dashboard</a> / <a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/return')}}">Return</a> / <span>Return Edit</span>
	</span>
@endsection

@section('help')
	<ul class="menu-help-list-container">
		<li>
			Isi data Supplier dan pilih Recieve Item yang ingin Anda Buat Return.
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
					<a class="edit-button-item edit-button-back" href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/return')}}">
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
				{!!Form::model($return, ['url' => URL::to(Crypt::decrypt($setting->admin_url) . '/return/' . $return->id), 'method' => 'PUT', 'files' => true])!!}
					<div class="page-group">
						<div class="page-item col-1">
							<div class="page-item-title">
								Detail Information
							</div>
							<div class="page-item-content edit-item-content">
								<div class="edit-form-group">
									{!!Form::label('no_nota', 'No Nota', ['class'=>'edit-form-label'])!!}
									{!!Form::text('no_nota', $return->no_nota, ['class'=>'edit-form-text large', 'style'=>"border: none; padding: 0px; font-size: 18px; font-weight: bold;", "readonly"])!!}
								</div>
								<div class="edit-form-group">
									{!!Form::label('supplier', 'Supplier', ['class'=>'edit-form-label'])!!}
									{!!Form::select('supplier', $supplier_options, $return->supplier_id, ['class'=>'edit-form-text select large'])!!}
									<span class="edit-form-note">
										*Required
									</span>
								</div>
								<div class="supplier-result">
									<div class="edit-form-group">
										{!!Form::label('receive_item', 'Receive Item', ['class'=>'edit-form-label'])!!}
										{!!Form::select('receive_item', $ri_options, $return->ri_id, ['class'=>'edit-form-text ri-select select large'])!!}
										<span class="edit-form-note">
											*Required
										</span>
									</div>
									<div class="ri-result" style="margin-bottom: 15px;">
										<div class="edit-form-group">
											{!!Form::label('date', 'Date', ['class'=>'edit-form-label'])!!}
											{!!Form::text('date', null, ['class'=>'edit-form-text datetimepicker large', 'readonly', 'required', 'placeholder'=>'Select Recieve Item First'])!!}
										</div>
										
										<div class="edit-form-group product-result" style="padding-top: 40px;">
											<div class="edit-form-label"></div>
											<div class="edit-form-text product-container">
												<div class="add-new">
													Add New
												</div>
												<span class="add-new-span">
													*Please Select Receive Item First
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

													<?php
														$total = 0;
													?>
													@foreach($returndetails as $returndetail)
														<?php
															$ridetail = Ridetail::find($returndetail->ridetail_id);
															$total += $returndetail->price * $returndetail->qty;
														?>
														@if($ridetail->product_id == 0)
															<tr class="tr{{$ridetail->id}}" datari="{{$ridetail->ri_id}}" dataproduct="{{$ridetail->podetail->product_id}}" dataquantity="{{$returndetail->qty}}" dataprice="{{$returndetail->price}}">
																<td>
																	<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/product/' . $ridetail->podetail->product_id)}}" style="color: blue;">
																		{{$ridetail->podetail->product->name}}
																		{!!Form::hidden('ridetail[' . $ridetail->id . '][product]', $ridetail->podetail->product_id)!!}
																	</a>
																</td>
																<td style="text-align: right;">
																	{{$returndetail->qty}}
																	{!!Form::hidden('ridetail[' . $ridetail->id . '][qty]', $returndetail->qty)!!}
																</td>
																<td style="text-align: right;">
																	{{number_format($returndetail->price)}}
																	{!!Form::hidden('ridetail[' . $ridetail->id . '][price]', $returndetail->price)!!}
																</td>
																<td style="text-align: right;">
																	{{number_format($returndetail->price * $returndetail->qty)}}
																	{!!Form::hidden('ridetail[' . $ridetail->id . '][subtotal]', $returndetail->price * $returndetail->qty, ['class'=>'subtotal'])!!}
																</td>

																<td class="index-td-icon">
																	{{-- <div class="edit-product-close close{{$product->id}}" data="{{$product->id}}"> --}}
																		{{-- Delete --}}
																	{{-- </div> --}}
																	<div class="index-action-switch index-action-switch{{$ridetail->id}}">
																		{{-- 
																			Switch of ACTION
																		 --}}
																		<span>
																			Action
																		</span>
																		<div class="index-action-arrow"></div>

																		{{-- 
																			List of ACTION
																		 --}}
																		<ul class="index-action-child-container" style="width: 110px">
																			<li data="{{$ridetail->id}}">
																				<a href="#" data="{{$ridetail->id}}" class="edit">
																					{!!HTML::image('img/admin/index/edit_icon.png')!!}
																					<span>
																						Edit
																					</span>
																				</a>
																			</li>
																			<li data="{{$ridetail->id}}">
																				<a href="#" data="{{$ridetail->id}}" class="delete">
																					{!!HTML::image('img/admin/index/trash_icon.png')!!}
																					<span>
																						Delete
																					</span>
																				</a>
																			</li>
																		</ul>
																	</div>
																</td>
															</tr>
														@else
															<tr class="tr{{$ridetail->id}}" datari="{{$ridetail->ri_id}}" dataproduct="{{$ridetail->product_id}}" dataquantity="{{$returndetail->qty}}" dataprice="{{$returndetail->price}}">
																<td>
																	<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/product/' . $ridetail->product_id)}}" style="color: blue;">
																		{{$ridetail->product->name}}
																		{!!Form::hidden('ridetail[' . $ridetail->id . '][product]', $ridetail->product_id)!!}
																	</a>
																</td>
																<td style="text-align: right;">
																	{{$returndetail->qty}}
																	{!!Form::hidden('ridetail[' . $ridetail->id . '][qty]', $returndetail->qty)!!}
																</td>
																<td style="text-align: right;">
																	{{number_format($returndetail->price)}}
																	{!!Form::hidden('ridetail[' . $ridetail->id . '][price]', $returndetail->price)!!}
																</td>
																<td style="text-align: right;">
																	{{number_format($returndetail->price * $returndetail->qty)}}
																	{!!Form::hidden('ridetail[' . $ridetail->id . '][subtotal]', $returndetail->price * $returndetail->qty, ['class'=>'subtotal'])!!}
																</td>

																<td class="index-td-icon">
																	{{-- <div class="edit-product-close close{{$product->id}}" data="{{$product->id}}"> --}}
																		{{-- Delete --}}
																	{{-- </div> --}}
																	<div class="index-action-switch index-action-switch{{$ridetail->id}}">
																		{{-- 
																			Switch of ACTION
																		 --}}
																		<span>
																			Action
																		</span>
																		<div class="index-action-arrow"></div>

																		{{-- 
																			List of ACTION
																		 --}}
																		<ul class="index-action-child-container" style="width: 110px">
																			<li data="{{$ridetail->id}}">
																				<a href="#" data="{{$ridetail->id}}" class="edit">
																					{!!HTML::image('img/admin/index/edit_icon.png')!!}
																					<span>
																						Edit
																					</span>
																				</a>
																			</li>
																			<li data="{{$ridetail->id}}">
																				<a href="#" data="{{$ridetail->id}}" class="delete">
																					{!!HTML::image('img/admin/index/trash_icon.png')!!}
																					<span>
																						Delete
																					</span>
																				</a>
																			</li>
																		</ul>
																	</div>
																</td>
															</tr>
														@endif
													@endforeach

													<tr class="global-discount" style="border-top: double #d2d2d2; border-bottom: 0px;">
														<td colspan="4" style="font-size: 18px; text-align: right;">
															Total (Rp)
														</td>
														<td colspan="2" style="font-size: 18px;" class="total" totaldata="0">
															{{number_format($total)}}
														</td>
													</tr>
												</table>
											</div>
										</div>
									</div>
								</div>

								
								<div class="edit-form-group">
									{!!Form::label('msg', 'Message', ['class'=>'edit-form-label'])!!}
									{!!Form::textarea('msg', $return->message, ['class'=>'edit-form-text large area'])!!}
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