<?php
	use Illuminate\Support\Str;

	use App\Models\User;
	use App\Models\Transaction;
	use App\Models\Supprice;
?>

@extends('back.template.master')

@section('title')
	Transaction Edit
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

			$('.add-new').live('click', function(){
				$('.pop-result').html($('.loading').html());
				$('.pop-container').fadeIn();

				$.ajax({
					type: "GET",
					url: "{{URL::to(Crypt::decrypt($setting->admin_url) . '/transaction/add')}}",
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

			$('.global-discount').keyup(function(){
				var globaldiscount = $(this).val();

				var count = 0;
				$('.subtotal').each(function(){
					count += parseInt($(this).val());
				});

				if ($('.global-type').val() == '0')
				{
					$('.total').text(number_format(count - globaldiscount));
				}
				if ($('.global-type').val() == '1')
				{
					$('.total').text(number_format(count - ((count * globaldiscount) / 100)));
				}
			});

			$('.global-discount').change(function(){
				var globaldiscount = $(this).val();

				var count = 0;
				$('.subtotal').each(function(){
					count += parseInt($(this).val());
				});

				var pricedatas = 0;
				$('.price').each(function(){
					pricedatas += parseInt($(this).val());
				});
				$('.form-total').val(pricedatas);

				if ($('.global-type').val() == '0')
				{
					$('.total').text(number_format(count - globaldiscount));
					$('.form-amount-to-pay').val(count - globaldiscount);
				}
				if ($('.global-type').val() == '1')
				{
					$('.total').text(number_format(count - ((count * globaldiscount) / 100)));
					$('.form-amount-to-pay').val(count - ((count * globaldiscount) / 100));
				}
			});

			$('.global-type').live('change', function(){
				$('.global-discount').change();
			});

			$('.edit-product-close').live('click', function(){
				$(this).text('Loading...');
				var dataids = $(this).attr('data');

				$.ajax({
					type: "GET",
					url: "{{URL::to(Crypt::decrypt($setting->admin_url) . '/transaction/drop')}}/"+dataids,
					success:function(msg) {
						$('.tr'+dataids).remove();
					},
					error:function(msg) {
						$('body').html(msg.responseText);
					}
				});
			});

			@foreach($transactiondetails as $transactiondetail)
				$('.index-action-switch'+{{$transactiondetail->product->id}}+' .delete').click(function(e){
					e.preventDefault();

					$(this).parent().parent().parent().find('span').text('Loading...');
					var dataids = $(this).attr('data');

					$.ajax({
						type: "GET",
						url: "{{URL::to(Crypt::decrypt($setting->admin_url) . '/transaction/drop')}}/"+dataids,
						success:function(msg) {
							$('.tr'+dataids).remove();
						},
						error:function(msg) {
							$('body').html(msg.responseText);
						}
					});
				});
			@endforeach

			$('.global-discount').change();

			@foreach($transactiondetails as $transactiondetail)
				$('.index-action-switch{{$transactiondetail->product_id}}').click(function(e){
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

				$('.index-action-switch{{$transactiondetail->product_id}} .edit').click(function(e){
					e.preventDefault();

					var dataid = $(this).attr('data');
					var dataproduct = $('.tr'+dataid).attr('dataproduct');
					var datarak = $('.tr'+dataid).attr('datarak');
					var dataquantity = $('.tr'+dataid).attr('dataquantity');
					var dataprice = $('.tr'+dataid).attr('dataprice');
					var datadiscounttype = $('.tr'+dataid).attr('datadiscounttype');
					var datadiscount = $('.tr'+dataid).attr('datadiscount');

					$('.pop-result').html($('.loading').html());
					$('.pop-container').fadeIn();

					$.ajax({
						type: "GET",
						url: "{{URL::to(Crypt::decrypt($setting->admin_url) . '/transaction/add')}}/"+dataproduct+"/"+datarak+"/"+dataquantity+"/"+dataprice+"/"+datadiscounttype+"/"+datadiscount,
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
			@endforeach

			$('.save-pay').click(function(){
				<?php
					Session::put('save-pay', true);
				?>

				$('.form-open').submit();
			});
		});
	</script>
@endsection

@section('page_title')
	Transaction Edit
	<span>
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/dashboard')}}">Dashboard</a> / <a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/transaction')}}">Transaction</a> / <span>Transaction Edit</span>
	</span>
@endsection

@section('help')
	<ul class="menu-help-list-container">
		<li>
			Tambahkan product yang Anda pesan dengan klik tombol Add New
		</li>
		<li>
			Diskon yang ada di masing-masing product digunakan untuk menambahkan diskon per-product
		</li>
		<li>
			Diskon yang ada di dipaling bawah digunakan untuk menambahkan diskon untuk keseluruhan product
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
					<a class="edit-button-item edit-button-back" href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/transaction')}}">
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
				{!!Form::model($transaction, ['url' => URL::to(Crypt::decrypt($setting->admin_url) . '/transaction/' . $transaction->id), 'method' => 'PUT', 'files' => true, 'class'=>'form-open'])!!}
					<div class="page-group">
						<div class="page-item col-1">
							<div class="page-item-title">
								Detail Information
							</div>
							<div class="page-item-content edit-item-content">
								<div class="edit-form-group">
									{!!Form::label('trans_id', 'Transaction ID', ['class'=>'edit-form-label'])!!}
									{!!Form::text('trans_id', $transaction->trans_id, ['class'=>'edit-form-text large', 'style'=>"border: none; padding: 0px; font-size: 18px; font-weight: bold;", "readonly"])!!}
								</div>
								<div class="edit-form-group">
									{!!Form::label('date', 'Date', ['class'=>'edit-form-label'])!!}
									{!!Form::text('date', $transaction->date, ['class'=>'edit-form-text large datetimepicker', 'readonly'])!!}
								</div>
								<div class="edit-form-group">
									{!!Form::label('customer', 'Customer', ['class'=>'edit-form-label'])!!}
									{!!Form::select('customer', $customer_options, $transaction->customer_id, ['class'=>'edit-form-text select large'])!!}
									<span class="edit-form-note">
										*Required
									</span>
								</div>
								<div class="edit-form-group">
									{!!Form::label('msg', 'Message', ['class'=>'edit-form-label'])!!}
									{!!Form::textarea('msg', $transaction->message, ['class'=>'edit-form-text large area'])!!}
								</div>
								<div class="edit-form-group">
									<div class="edit-form-label"></div>
									<div class="edit-form-text">
										<span class="edit-form-note" style="position: relative; display: block; width: 100%; padding-top: 20px; color: #525252; font-style: italic;">
											* Pilih dan tambahkan product yang Anda pesan dengan menekan tombol Add New<br>
											* Hanya Product yang memiliki stock diatas minimum stock yang bisa Anda tambahkan<br>
											* Jika kolom Qty dari product yang Anda pesan kosong atau 0, maka data product tersebut tidak akan dimasukkan kedalam Transaction<br>
											* Qty tidak boleh berisi minus
										</span>
									</div>
								</div>
								<div class="edit-form-group product-result" style="padding-top: 40px;">
									<div class="edit-form-label"></div>
									<div class="edit-form-text product-container">
										<div class="add-new">
											Add New
										</div>
										<table>
											<tr class="tr-title">
												<th style="width: 300px;">
													Product
												</th>
												<th>
													Rak
												</th>
												<th style="text-align: right;">
													Qty
												</th>
												<th style="text-align: right;">
													Price (Rp)
												</th>
												<th style="text-align: right;">
													Discount
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
											@foreach($transactiondetails as $transactiondetail)
												<tr class="tr{{$transactiondetail->product_id}}" dataproduct="{{$transactiondetail->product_id}}" datarak="{{$transactiondetail->rak_id}}" dataquantity="{{$transactiondetail->qty}}" dataprice="{{$transactiondetail->price}}" datadiscounttype="{{$transactiondetail->discounttype}}" datadiscount="{{$transactiondetail->discount}}">
													<td>
														<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/product/' . $transactiondetail->product_id)}}" style="color: blue;">
															{{$transactiondetail->product->name}}
															{!!Form::hidden('product[' . $transactiondetail->product_id . '][product]', $transactiondetail->product_id)!!}
														</a>
													</td>
													<td>
														<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/rak/' . $transactiondetail->rak_id)}}" style="color: blue;">
															{{$transactiondetail->rak->name}}
															{!!Form::hidden('product[' . $transactiondetail->product_id . '][rak]', $transactiondetail->rak_id)!!}
														</a>
													</td>
													<td style="text-align: right;">
														{{$transactiondetail->qty}}
														{!!Form::hidden('product[' . $transactiondetail->product_id . '][qty]', $transactiondetail->qty)!!}
													</td>
													<td style="text-align: right;">
														{{number_format($transactiondetail->price)}}
														{!!Form::hidden('product[' . $transactiondetail->product_id . '][price]', $transactiondetail->price)!!}
														{!!Form::hidden('subprice', $transactiondetail->price * $transactiondetail->qty, ['class'=>'price'])!!}
													</td>
													<td style="text-align: right;">
														@if($transactiondetail->discounttype == '0')
															Rp {{number_format($transactiondetail->discount)}}
															{!!Form::hidden('product[' . $transactiondetail->product_id . '][discounttype]', '0')!!}
														@else
															{{$transactiondetail->discount}} %
															{!!Form::hidden('product[' . $transactiondetail->product_id . '][discounttype]', '1')!!}
														@endif
														{!!Form::hidden('product[' . $transactiondetail->product_id . '][discount]', $transactiondetail->discount)!!}
													</td>
													<td style="text-align: right;">
														<?php
															if($transactiondetail->discounttype == '0')
															{
																$subtotal = ($transactiondetail->qty * $transactiondetail->price) - $transactiondetail->discount;
															}

															if($transactiondetail->discounttype == '1')
															{
																$subtotal = ($transactiondetail->qty * $transactiondetail->price) - ((($transactiondetail->qty * $transactiondetail->price) * $transactiondetail->discount) / 100);
																// $subtotal = 1111111;
															}

															$total += $subtotal;
														?>
														{{number_format($subtotal)}}
														{!!Form::hidden('subtotal', $subtotal, ['class'=>'subtotal'])!!}
													</td>

													<td class="index-td-icon">
														{{-- <div class="edit-product-close close{{$transactiondetail->product_id}}" data="{{$transactiondetail->product_id}}"> --}}
															{{-- Delete --}}
														{{-- </div> --}}
														<div class="index-action-switch index-action-switch{{$transactiondetail->product_id}}">
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
																<li data="{{$transactiondetail->product->id}}">
																	<a href="#" data="{{$transactiondetail->product->id}}" class="edit">
																		{!!HTML::image('img/admin/index/edit_icon.png')!!}
																		<span>
																			Edit
																		</span>
																	</a>
																</li>
																<li data="{{$transactiondetail->product->id}}">
																	<a href="#" data="{{$transactiondetail->product->id}}" class="delete">
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
											@endforeach
											
											<tr class="global-discount" style="border-top: double #d2d2d2; border-bottom: 0px;">
												<td colspan="4" style="font-size: 18px; text-align: right; line-height: 40px;">
													Discount Type
												</td>
												<td colspan="2" style="font-size: 18px;">
													{!! Form::select('globaldiscounttype', ['0'=>'Rp', '1'=>'%'], $transaction->discounttype, ['class'=>'edit-product-text global-text global-type select']) !!}
												</td>
											</tr>
											<tr style="border-bottom: 0px;">
												<td colspan="4" style="font-size: 18px; text-align: right;">
													Discount
												</td>
												<td colspan="2" style="font-size: 18px;">
													{!!Form::input('number', 'globaldiscount', $transaction->discount, ['class'=>'edit-product-text global-text global-discount', 'required', 'min'=>'0'])!!}

													{!! Form::hidden('total', $transaction->total, ['class'=>'form-total']) !!}
													{!! Form::hidden('amount_to_pay', $transaction->amount_to_pay, ['class'=>'form-amount-to-pay']) !!}
												</td>
											</tr>
											<tr style="border-bottom: 0px;">
												<td colspan="4" style="font-size: 18px; text-align: right;">
													Total (Rp)
												</td>
												<td colspan="2" style="font-size: 18px;" class="total">
													{{number_format($total)}}
												</td>
											</tr>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="page-group">
						<div class="edit-button-group">
							{{Form::submit('Save', ['class'=>'edit-button-item'])}}
							{{Form::button('Save and Pay', ['class'=>'edit-button-item save-pay'])}}
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
@endsection