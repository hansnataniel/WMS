<?php
	use Illuminate\Support\Str;

	use App\Models\User;
	use App\Models\Category;
?>

@extends('back.template.master')

@section('title')
	Product Edit
@endsection

@section('head_additional')
	{!!HTML::style('css/back/edit.css')!!}

	<style type="text/css">
		div.rak-container {
			position: relative;
			border: 1px solid #0d0f3b !important;
			padding: 20px !important;
			max-width: 500px;
		}

		div.rak-container table {
			width: 100%;
		}

		div.rak-container table .tr-title {
			border-bottom: double #0d0f3b;
		}

		div.rak-container table th {
			text-align: left;
			padding: 0px 10px 10px 10px;
		}

		div.rak-container table td {
			padding: 5px 10px;
		}

		div.rak-container table tr:nth-child(2) td {
			padding: 10px 10px 5px 10px;
		}

		.rak-delete {
			color: red;
			cursor: pointer;
		}
	</style>
@endsection

@section('js_additional')
	<script type="text/javascript">
		$(function(){
			$('.edit-form-image-delete').click(function(){
		    	var value = $(this).attr('value');
		    	$('.edit-form-image-loading').fadeIn();
		    	$.ajax({
		    		type: 'GET',
		    		url: "{{URL::to(Crypt::decrypt($setting->admin_url) . '/product/delete-image')}}/"+value,
		    		success: function(msg){
		    			$('.edit-form-image-success').fadeIn().delay(1000).fadeOut(1000);
		    			$('.edit-form-image').delay(2000).animate({'width': '0px', 'opacity': '0'}, 500, 'easeInExpo').slideUp();
		    		},
		    		error: function(msg) {
		    			$('body').html(msg.responseText);
		    		}
		    	});
		    });

		    $('.rak-select').live('change', function(){
				var data = $(this).val();
				$(this).parent().find('.edit-form-note').text('Loading...');

				$.ajax({
					type: "GET",
					url: "{{URL::to(Crypt::decrypt($setting->admin_url) . '/product/rak')}}/"+data,
					success:function(msg) {
						$('.rak-select').parent().find('.edit-form-note').text('Loading...');
						$('.rak-container table tbody').append(msg);

						$.ajax({
							type: "GET",
							url: "{{URL::to(Crypt::decrypt($setting->admin_url) . '/product/replace')}}",
							success:function(msg) {
								$('.rak-parent').html(msg);
							},
							error:function(msg) {
								$('body').html(msg.responseText);
							}
						});
					},
					error:function(msg) {
						$('body').html(msg.responseText);
					}
				});
			});

			$('.rak-delete').live('click', function(){
				$(this).text('Loading...');
				var data = $(this).attr('dataid');

				$.ajax({
					type: "GET",
					url: "{{URL::to(Crypt::decrypt($setting->admin_url) . '/product/drop')}}/"+data,
					success:function(msg) {
						$('.rak'+data).parent().remove();
						$('.rak-parent').html(msg);
					},
					error:function(msg) {
						$('body').html(msg.responseText);
					}
				});
			});
		});
	</script>
@endsection

@section('page_title')
	Product Edit
	<span>
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/dashboard')}}">Dashboard</a> / <a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/product')}}">Product</a> / <span>Product Edit</span>
	</span>
@endsection

@section('help')
	<ul class="menu-help-list-container">
		<li>
			No Merk di isi dengan No Part dari merk product yang Anda buat
		</li>
		<li>
			Field <i>Reference</i> wajib di isi jika product yang Anda inputkan adalah product KW
		</li>
		<li>
			Kosongkan field <i>Reference</i> jika product yang Anda inputkan adalah product ORIGINAL
		</li>
		<li>
			Field <i>Rak</i> digunakan untuk mengetahui letak dari product yang Anda buat
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
					<a class="edit-button-item edit-button-back" href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/product')}}">
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
				{!!Form::model($product, ['url' => URL::to(Crypt::decrypt($setting->admin_url) . '/product/' . $product->id), 'method' => 'PUT', 'files' => true])!!}
					<div class="page-group">
						<div class="page-item col-1">
							<div class="page-item-title">
								Detail Information
							</div>
							<div class="page-item-content edit-item-content">
								<div class="edit-form-group">
									{!!Form::label('name', 'Name', ['class'=>'edit-form-label'])!!}
									{!!Form::text('name', null, ['class'=>'edit-form-text medium', 'required'])!!}
									<span class="edit-form-note">
										*Required
									</span>
								</div>
								<div class="edit-form-group">
									{!!Form::label('no_merk', 'No Merk', ['class'=>'edit-form-label'])!!}
									{!!Form::text('no_merk', null, ['class'=>'edit-form-text medium', 'required'])!!}
									<span class="edit-form-note">
										*Required
									</span>
								</div>
								<div class="edit-form-group">
									{!!Form::label('reference', 'OEM Part', ['class'=>'edit-form-label'])!!}
									{!!Form::select('reference', $reference_options, $product->reference_id, ['class'=>'edit-form-text select medium'])!!}
								</div>
								<div class="edit-form-group rak-parent">
									{!!Form::label('rak', 'Rak', ['class'=>'edit-form-label'])!!}
									{!!Form::select('rak', $rak_options, null, ['class'=>'edit-form-text rak-select select medium'])!!}
									<span class="edit-form-note">
										
									</span>
								</div>
								<div class="edit-form-group rak-result">
									<div class="edit-form-label"></div>
									<div class="edit-form-text rak-container">
										<table>
											<tr class="tr-title">
												<th>
													Rak
												</th>
												<th>
													Barcode
												</th>
												<th>
												</th>
											</tr>
											@foreach($productstocks as $productstock)
												<tr>
													<td>
														<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/rak/' . $productstock->rak->id)}}" style="color: blue;">
															{{$productstock->rak->name}}
														</a>
													</td>
													<td>
														{{$productstock->rak->code_id}}
														{!! Form::hidden('raks[]', $productstock->rak->id) !!}
													</td>
													<td class="rak-delete rak{{$productstock->rak->id}}" dataid="{{$productstock->rak->id}}">
														Delete
													</td>
												</tr>
											@endforeach
										</table>
									</div>
								</div>
								<div class="edit-form-group">
									{!!Form::label('kendaraan', 'Kendaraan', ['class'=>'edit-form-label'])!!}
									{!!Form::select('kendaraan', $kendaraan_options, $product->kendaraan_id, ['class'=>'edit-form-text select medium'])!!}
									<span class="edit-form-note">
										*Required
									</span>
								</div>
								<div class="edit-form-group">
									{!!Form::label('min_stock', 'Min Stock', ['class'=>'edit-form-label'])!!}
									{!!Form::input('number', 'min_stock', null, ['class'=>'edit-form-text medium', 'required'])!!}
									<span class="edit-form-note">
										*Required
									</span>
								</div>
								<div class="edit-form-group">
									{!!Form::label('max_stock', 'Max Stock', ['class'=>'edit-form-label'])!!}
									{!!Form::input('number', 'max_stock', null, ['class'=>'edit-form-text medium', 'required'])!!}
									<span class="edit-form-note">
										*Required
									</span>
								</div>
								<div class="edit-form-group">
									{!!Form::label('description', 'Description', ['class'=>'edit-form-label'])!!}
									{!!Form::textarea('description', null, ['class'=>'edit-form-text large ckeditor'])!!}
								</div>
								<div class="edit-form-group">
									{!!Form::label('is_active', 'Active Status', ['class'=>'edit-form-label'])!!}
									<div class="edit-form-radio-group">
										<div class="edit-form-radio-item">
											{!!Form::radio('is_active', 1, true, ['class'=>'edit-form-radio', 'id'=>'true'])!!} 
											{!!Form::label('true', 'Active', ['class'=>'edit-form-radio-label'])!!}
										</div>
										<div class="edit-form-radio-item">
											{!!Form::radio('is_active', 0, false, ['class'=>'edit-form-radio', 'id'=>'false'])!!} 
											{!!Form::label('false', 'Not Active', ['class'=>'edit-form-radio-label'])!!}
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