<?php
	use Illuminate\Support\Str;

	use App\Models\User;
	use App\Models\Invoice;
	use App\Models\Ridetail;
?>

@extends('back.template.master')

@section('title')
	New Invoice
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

			$('.supplier-select').live('change', function(){
				var data = $(this).val();

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

			if($('.supplier-select').val() != '')
			{
				$('.supplier-select').change();
			}

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
		        			$('.ri-result').prepend(msg);
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

				// alert(ids + " - " + supplier);

				if($(this).hasClass('close-active'))
				{

				}
				else
				{
					$(this).text('Loading...');

					$.ajax({
						type: "GET",
						url: "{{URL::to(Crypt::decrypt($setting->admin_url) . '/invoice/drop')}}/"+ids+"/"+supplier,
						success:function(msg) {
							$('.ri-parent').html(msg);
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
	New Invoice
	<span>
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/dashboard')}}">Dashboard</a> / <a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/invoice')}}">Invoice</a> / <span>New Invoice</span>
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
				{!!Form::open(['url' => URL::to(Crypt::decrypt($setting->admin_url) . '/invoice'), 'files' => true])!!}
					<div class="page-group">
						<div class="page-item col-1">
							<div class="page-item-title">
								Detail Information
							</div>
							<div class="page-item-content edit-item-content">
								<div class="edit-form-group">
									<?php
										$lastinvoice = Invoice::orderBy('id', 'desc')->first();
										if($lastinvoice == null)
										{
											$no_nota = 'INV/' . date('ymd') . '/1001';
										}
										else
										{
											$no_nota = 'INV/' . date('ymd') . '/' . ($lastinvoice->id + 1001);
										}
									?>
									{!!Form::label('no_nota', 'No Nota', ['class'=>'edit-form-label'])!!}
									{!!Form::text('no_nota', $no_nota, ['class'=>'edit-form-text large', 'style'=>"border: none; padding: 0px; font-size: 18px; font-weight: bold;", "readonly"])!!}
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
									{!!Form::select('supplier', $supplier_options, null, ['class'=>'edit-form-text supplier-select select large'])!!}
									<span class="edit-form-note">
										*Required
									</span>
								</div>
								<div class="edit-form-group ri-parent">
									{!!Form::label('recieve_item', 'Recieve Item', ['class'=>'edit-form-label'])!!}
									{!!Form::select('recieve_item', [''=>'Select Supplier First'], null, ['class'=>'edit-form-text select large'])!!}
									<span class="edit-form-note">
										
									</span>
								</div>
								<div class="edit-form-group ri-result" style="padding-top: 40px;">
									
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