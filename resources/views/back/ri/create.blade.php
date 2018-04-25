<?php
	use Illuminate\Support\Str;

	use App\Models\User;
	use App\Models\Ri;
?>

@extends('back.template.master')

@section('title')
	New Recieve Item
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

	        $('.nota').live('change', function(){
	        	var data = $(this).val();

	        	if(data != '')
	        	{
	    			$(this).parent().find('.edit-form-note').text('Loading...');
	        		$.ajax({
			        	type: "GET",
		        		url: "{{URL::to(Crypt::decrypt($setting->admin_url) . '/ri/podetail')}}/"+data,
		        		success:function(msg) {
			    			$('.nota').parent().find('.edit-form-note').text('*Required');
		        			$('.product-result .tr-title').after(msg);
		        		},
		        		error:function(msg) {
		        			$('body').html(msg.responseText);
		        		}
	        		});
	        	}
	        });

	        $('.supplier').live('change', function(){
				var data = $(this).val();

				if(data != '')
				{
	    			$(this).parent().find('.edit-form-note').text('Loading...');

					$.ajax({
						type: "GET",
						url: "{{URL::to(Crypt::decrypt($setting->admin_url) . '/ri/find-po')}}/"+data,
						success:function(msg) {
			    			$('.po-switch .edit-form-note').text('*Required');
							$('.po-result').html(msg);
						},
						error:function(msg) {
							$('body').html(msg.responseText);
							// alert('done');
						}
					});
				}
			});

			if($('.supplier').val() != '')
	        {
	        	$('.supplier').change();
	        }

	        if($('.nota').val() != '')
	        {
	        	$('.nota').change();
	        }




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
	New Recieve Item
	<span>
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/dashboard')}}">Dashboard</a> / <a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/ri')}}">Recieve Item</a> / <span>New Recieve Item</span>
	</span>
@endsection

@section('help')
	<ul class="menu-help-list-container">
		<li>
			Isi Purchase Order date dengan tanggal Anda membuat purchase order
		</li>
		<li>
			Setelah mengisi Purchase Order date, cari Purchase Order menggunakan No Nota dari purchase order yang Anda buat
		</li>
		<li>
			Setelah Anda memilih Purchase Order, maka akan muncul data-data yang sudah Anda pesan
		</li>
		<li>
			Isi Qty dan Price berdasarkan Recieve Item yang Anda terima di setiap data yang ada di Purchase Order
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
				{!!Form::open(['url' => URL::to(Crypt::decrypt($setting->admin_url) . '/ri'), 'files' => true])!!}
					<div class="page-group">
						<div class="page-item col-1">
							<div class="page-item-title">
								Detail Information
							</div>
							<div class="page-item-content edit-item-content">
								<div class="edit-form-group">
									<?php
										$lastri = Ri::orderBy('id', 'desc')->first();
										if($lastri == null)
										{
											$no_nota = 'RI/' . date('ymd') . '/1001';
										}
										else
										{
											$no_nota = 'RI/' . date('ymd') . '/' . ($lastri->id + 1001);
										}
									?>
									{!!Form::label('no_nota', 'No Nota', ['class'=>'edit-form-label'])!!}
									{!!Form::text('no_nota', $no_nota, ['class'=>'edit-form-text large', 'style'=>"border: none; padding: 0px; font-size: 18px; font-weight: bold;", "readonly"])!!}
								</div>
								<div class="edit-form-group">
									{!!Form::label('date', 'Date', ['class'=>'edit-form-label'])!!}
									{!!Form::text('date', null, ['class'=>'edit-form-text datetimepicker large', 'readonly', 'required'])!!}
								</div>
								<div class="edit-form-group po-switch">
									{!!Form::label('supplier', 'Supplier', ['class'=>'edit-form-label'])!!}
									{!!Form::select('supplier', $supplier_options, null, ['class'=>'edit-form-text supplier select large'])!!}
									<span class="edit-form-note">
										*Required
									</span>
								</div>
								<div class="edit-form-group po-result">
									{!!Form::label('purchase_order', 'Purchase Order', ['class'=>'edit-form-label'])!!}
									{!!Form::select('purchase_order', [''=>'Select Supplier First'], null, ['class'=>'edit-form-text select large'])!!}
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

										</table>
									</div>
								</div>

								<div class="edit-form-group">
									<div class="edit-form-label"></div>
									<div class="edit-form-text">
										<span class="edit-form-note" style="position: relative; display: block; width: 100%; padding-top: 20px; color: #525252; font-style: italic;">
											* Pilih dan tambahkan product bonus dengan mencarinya dikolom bawah ini<br>
											* Jika kolom Qty dari product yang Anda pesan kosong atau 0, maka data product tersebut tidak akan dimasukkan kedalam Puschase Order<br>
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

										</table>
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
@endsection