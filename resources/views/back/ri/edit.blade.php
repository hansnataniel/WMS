<?php
	use Illuminate\Support\Str;

	use App\Models\User;
	use App\Models\Po;
	use App\Models\Supprice;
	use App\Models\Ridetail;
	use App\Models\Productstock;
?>

@extends('back.template.master')

@section('title')
	Recieve Item Edit
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
			$('.product-select').live('change', function(){
				var data = $(this).val();

				if(data != '')
				{
					$(this).parent().find('.edit-form-note').text('Loading...');

					$.ajax({
						type: "GET",
						url: "{{URL::to(Crypt::decrypt($setting->admin_url) . '/ri/add')}}/"+data,
						success:function(msg) {
							$('.free-result .tr-title').after(msg);
							// $('.product-result .edit-form-text').prepend(msg);
						},
						error:function(msg) {
							$('body').html(msg.responseText);
							// alert('done');
						}
					});
				}
			});

			$('.edit-product-close').live('click', function(){
				var ids = $(this).attr('data');

				$(this).text('Loading...');

				if($(this).hasClass('close-active'))
				{

				}
				else
				{
					$.ajax({
						type: "GET",
						url: "{{URL::to(Crypt::decrypt($setting->admin_url) . '/ri/drop')}}/"+ids,
						success:function(msg) {
							$('.product-parent').html(msg);
							$('.close'+ids).parent().parent().remove();
						},
						error:function(msg) {
							$('body').html(msg.responseText);
							// alert('done');
						}
					});
				}
			});

			$('.rec-switch').live('click', function(){
				if($(this).hasClass('active'))
				{
					$(this).parent().find('.rec-container').slideUp();
					$(this).removeClass('active');
				}
				else
				{
					$(this).parent().find('.rec-container').slideDown();
					$(this).addClass('active');
				}
			});
		});
	</script>
@endsection

@section('page_title')
	Recieve Item Edit
	<span>
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/dashboard')}}">Dashboard</a> / <a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/ri')}}">Recieve Item</a> / <span>Recieve Item Edit</span>
	</span>
@endsection

@section('help')
	<ul class="menu-help-list-container">
		<li>
			Recieve Item dijadikan sebagai pengelompok produk
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
					<a class="edit-button-item edit-button-back" href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/ri')}}">
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
				{!!Form::model($ri, ['url' => URL::to(Crypt::decrypt($setting->admin_url) . '/ri/' . $ri->id), 'method' => 'PUT', 'files' => true])!!}
					<div class="page-group">
						<div class="page-item col-1">
							<div class="page-item-title">
								Detail Information
							</div>
							<div class="page-item-content edit-item-content">
								<div class="edit-form-group">
									{!!Form::label('no_nota', 'No Nota', ['class'=>'edit-form-label'])!!}
									{!!Form::text('no_nota', $ri->no_nota, ['class'=>'edit-form-text large', 'style'=>"border: none; padding: 0px; font-size: 18px; font-weight: bold;", "readonly"])!!}
								</div>
								<div class="edit-form-group">
									{!!Form::label('date', 'Date', ['class'=>'edit-form-label'])!!}
									{!!Form::text('date', null, ['class'=>'edit-form-text datetimepicker large', 'readonly', 'required'])!!}
								</div>
								<div class="edit-form-group">
									{!!Form::label('supplier', 'Supplier', ['class'=>'edit-form-label'])!!}
									@if($ri->po_id != 0)
										{!!Form::select('supplier', $supplier_options, $ri->po->supplier_id, ['class'=>'edit-form-text select large'])!!}
									@else
										{!!Form::select('purchase_order', $supplier_options, $ri->supplier_id, ['class'=>'edit-form-text select large'])!!}
									@endif
									<span class="edit-form-note">
										*Required
									</span>
								</div>
								<div class="edit-form-group po-result">
									{!!Form::label('purchase_order', 'Purchase Order', ['class'=>'edit-form-label'])!!}
									@if($ri->po_id != 0)
										{!!Form::select('purchase_order', $po_options, $ri->po_id, ['class'=>'edit-form-text select large'])!!}
									@else
										{!!Form::select('purchase_order', $po_options, null, ['class'=>'edit-form-text select large'])!!}
									@endif
									<span class="edit-form-note">
										*Required
									</span>
								</div>

								<div class="edit-form-group product-result" style="padding-top: 40px;">
									<div class="edit-form-label"></div>
									<span style="position: absolute; font-size: 18px; left: 160px; top: 10px;">
										Product List
									</span>
									<div class="edit-form-text product-container">
										@if($ri->po_id != 0)
											{!!Form::hidden('po', $po->id)!!}
											<table>
												<tr class="tr-title">
													<th>
														Product
													</th>
													<th>
														Qty
													</th>
													<th>
														Rak
													</th>
													<th>
														Order
													</th>
													<th>
														Recieve
													</th>
													<th>
														Leftover
													</th>
												</tr>

												@foreach($podetails as $podetail)
													<?php
														$ridetail = Ridetail::where('podetail_id', '=', $podetail->id)->where('ri_id', '=', $ri->id)->where(function($qr){
															$qr->where('product_id', '=', 0);
															$qr->orWhere('product_id', '=', '0');
															$qr->orWhere('product_id', '=', null);
														})->first();
														
														$getridetails = Ridetail::where('podetail_id', '=', $podetail->id)->where(function($qr){
															$qr->where('product_id', '=', 0);
															$qr->orWhere('product_id', '=', '0');
															$qr->orWhere('product_id', '=', null);
														})->get();

														$checkriqty = 0;

														foreach ($getridetails as $getridetail) {
															$checkriqty = $checkriqty + $getridetail->qty;
														}

														$checkmax = $podetail->qty - $checkriqty;

														$productstocks = Productstock::where('product_id', '=', $podetail->product_id)->where('is_active', '=', true)->get();

														$rak_options = array();
														$rak_options[''] = "Select Rak";
														foreach($productstocks as $productstock) {
															$rak_options[$productstock->rak_id] = $productstock->rak->name;
														}
													?>

													<tr>
														<td>
															{{$podetail->product->name}}
														</td>
														<td>
															{!!Form::hidden('podetail[' . $podetail->id . '][product]', 0)!!}
															{!!Form::hidden('podetail[' . $podetail->id . '][bahan]', $podetail->product_id)!!}
															
															@if($ridetail != null)
																{!!Form::hidden('podetail[' . $podetail->id . '][max]', $checkmax + $ridetail->qty)!!}
																{!!Form::input('number', 'podetail[' . $podetail->id . '][qty]', $ridetail->qty, array('class'=>'edit-product-text', 'placeholder'=>'Qty', 'required', 'min'=>'0', 'max'=>$checkmax + $ridetail->qty))!!}
															@else
																{!!Form::hidden('podetail[' . $podetail->id . '][max]', $checkmax)!!}
																{!!Form::input('number', 'podetail[' . $podetail->id . '][qty]', 0, array('class'=>'edit-product-text', 'placeholder'=>'Qty', 'required', 'min'=>'0', 'max'=>$checkmax))!!}
															@endif
														</td>
														<td>
															@if($ridetail != null)
																{!! Form::select('podetail[' . $podetail->id . '][rak]', $rak_options, $ridetail->rak_id, ['class'=>'edit-product-text select']) !!}
															@else
																{!! Form::select('podetail[' . $podetail->id . '][rak]', $rak_options, null, ['class'=>'edit-product-text select']) !!}
															@endif
														</td>
														<td>
															{{$podetail->qty}}
														</td>
														<td>
															{{$checkriqty}}
														</td>
														<td>
															{{$checkmax}}
														</td>
													</tr>
													
													<div class="edit-product-item">
														<div class="edit-product-td product-name">
														</div>
														<div class="edit-product-td">
														</div>
													</div>
												@endforeach
											</table>
										@endif
									</div>
								</div>

								<div class="edit-form-group nota-result">
									<div class="edit-form-label"></div>
									<div class="edit-form-text">
										
									</div>
								</div>
								<div class="edit-form-group">
									<div class="edit-form-label"></div>
									<div class="edit-form-text">
										<span class="edit-form-note" style="position: relative; display: block; width: 100%; padding-top: 20px; color: #525252; font-style: italic;">
											* Pilih dan tambahkan product yang Anda pesan dengan mencarinya dikolom bawah ini<br>
											* Jika kolom Qty dari product yang Anda pesan kosong atau 0, maka data product tersebut tidak akan dimasukkan kedalam Recieve Item<br>
											* Qty tidak boleh berisi minus
										</span>
									</div>
								</div>
								<div class="edit-form-group product-parent">
									{!!Form::label('product', 'Bonus Item', ['class'=>'edit-form-label'])!!}
									{!!Form::select('product', $product_options, null, ['class'=>'edit-form-text select large product-select'])!!}
									<span class="edit-form-note">
										*Required
									</span>
								</div>
								<div class="edit-form-group free-result" style="padding-top: 40px;">
									<div class="edit-form-label"></div>
									<span style="position: absolute; font-size: 18px; left: 160px; top: 10px;">
										Bonus Item List
									</span>
									<div class="edit-form-text product-container" style="max-width: 500px;">
										<table>
											<tr class="tr-title">
												<th>
													Product
												</th>
												<th>
													Qty
												</th>
												<th>
													Rak
												</th>
												<th>
												</th>
											</tr>

											@foreach($ridetails as $ridetail)
												<?php
													$productstocks = Productstock::where('product_id', '=', $ridetail->product_id)->where('is_active', '=', true)->get();

													$rak_options = array();
													$rak_options[''] = "Select Rak";
													foreach($productstocks as $productstock) {
														$rak_options[$productstock->rak_id] = $productstock->rak->name;
													}
												?>
												<tr>
													<td>
														{{$ridetail->product->name}}
														{!!Form::hidden('freedetail[' . $ridetail->product_id . '][product]', $ridetail->product_id)!!}
													</td>
													<td>
														{!!Form::input('number', 'freedetail[' . $ridetail->product_id . '][qty]', $ridetail->qty, ['class'=>'edit-product-text', 'placeholder'=>'Qty', 'required', 'min'=>'1'])!!}
													</td>
													<td>
														{!! Form::select('freedetail[' . $ridetail->product_id . '][rak]', $rak_options, $ridetail->rak_id, ['class'=>'edit-product-text select']) !!}
													</td>
													<td>
														<div class="edit-product-close close{{$ridetail->product_id}}" data="{{$ridetail->product_id}}">
															Delete
														</div>
													</td>
												</tr>
											@endforeach
										</table>
									</div>
								</div>

								<div class="edit-form-group">
									{!!Form::label('msg', 'Message', ['class'=>'edit-form-label'])!!}
									{!!Form::textarea('msg', $ri->message, ['class'=>'edit-form-text large area'])!!}
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