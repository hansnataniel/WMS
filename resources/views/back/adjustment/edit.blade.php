<?php
	use Illuminate\Support\Str;

	use App\Models\Adjustment;
	use App\Models\Productstock;
?>

@extends('back.template.master')

@section('title')
	Stock Adjustment Edit
@endsection

@section('head_additional')
	{!!HTML::style('css/back/edit.css')!!}
	{!!HTML::style('css/jquery.datetimepicker.css')!!}
@endsection

@section('js_additional')
	{!!HTML::script('js/jquery.datetimepicker.js')!!}
	
	<script>
		$(function(){
		    $('.datetimepicker').datetimepicker({
				timepicker: false,
				format: 'Y-m-d',
				maxDate: 0
			});

			$('.product-select').change(function(){
				var dataid = $(this).val();

				$('.product-result').html($('.rak'+dataid).html());
			});
		});
	</script>
@endsection

@section('page_title')
	Stock Adjustment Edit
	<span>
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/dashboard')}}">Dashboard</a> / <a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/adjustment')}}">Stock Adjustment</a> / <span>Stock Adjustment Edit</span>
	</span>
@endsection

@section('help')
	<ul class="menu-help-list-container">
		<li>
			Stock Adjustment digunakan untuk membuat stok masuk dan juga keluar (selain dari transaction dan order)
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
					<a class="edit-button-item edit-button-back" href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/adjustment')}}">
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
				{!!Form::model($adjustment, ['url' => URL::to(Crypt::decrypt($setting->admin_url) . '/adjustment/' . $adjustment->id), 'method' => 'PUT', 'files' => true])!!}
					<div class="page-group">
						<div class="page-item col-1">
							<div class="page-item-title">
								Detail Information
							</div>
							<div class="page-item-content edit-item-content">
								<div class="edit-form-group">
									{!!Form::label('no_nota', 'No. Nota', ['class'=>'edit-form-label'])!!}
									{!!Form::text('no_nota', $adjustment->no_nota, ['class'=>'edit-form-text large', 'readonly', 'style="padding: 0px; border: 0px !important; background: transparent; font-size: 20px;'])!!}
								</div>
								<div class="edit-form-group">
									{!!Form::label('product', 'Product', ['class'=>'edit-form-label'])!!}
									{!!Form::select('product', $product_options, $adjustment->product_id, ['class'=>'edit-form-text product-select select large'])!!}
									<span class="edit-form-note">
										*Required
									</span>
								</div>
								<div class="edit-form-group product-result">
									<?php
										$getproductstocks = Productstock::where('product_id', '=', $adjustment->product_id)->where('is_active', '=', true)->get();

										$rak_options[''] = "Select Rak";
										foreach ($getproductstocks as $getproductstock) {
											$rak_options[$getproductstock->rak_id] = $getproductstock->rak->name;
										}
									?>

									<div class="rak{{$adjustment->product_id}}">
										{!!Form::label('rak', 'Rak', ['class'=>'edit-form-label'])!!}
										{!!Form::select('rak', $rak_options, $adjustment->rak_id, ['class'=>'edit-form-text select large'])!!}
										<span class="edit-form-note">
											*Required
										</span>
									</div>
								</div>
								<div class="edit-form-group">
									{!!Form::label('quantity', 'Quantity', ['class'=>'edit-form-label'])!!}
									{!!Form::input('number', 'quantity', null, ['class'=>'edit-form-text large', 'required'])!!}
									<span class="edit-form-note">
										*Required
									</span>
								</div>
								<div class="edit-form-group">
									{!!Form::label('date', 'Date', ['class'=>'edit-form-label'])!!}
									{!!Form::text('date', null, ['class'=>'edit-form-text large datetimepicker', 'required', 'readonly'])!!}
									<span class="edit-form-note">
										*Required
									</span>
								</div>
								<div class="edit-form-group">
									{!!Form::label('status', 'Status', ['class'=>'edit-form-label'])!!}
									<div class="edit-form-radio-group">
										<div class="edit-form-radio-item">
											{!!Form::radio('status', 'In', true, ['class'=>'edit-form-radio', 'id'=>'true'])!!} 
											{!!Form::label('true', 'Stock In', ['class'=>'edit-form-radio-label'])!!}
										</div>
										<div class="edit-form-radio-item">
											{!!Form::radio('status', 'Out', false, ['class'=>'edit-form-radio', 'id'=>'false'])!!} 
											{!!Form::label('false', 'Stock Out', ['class'=>'edit-form-radio-label'])!!}
										</div>
									</div>
								</div>
								<div class="edit-form-group">
									{!!Form::label('note', 'Note', ['class'=>'edit-form-label'])!!}
									{!!Form::textarea('note', null, ['class'=>'edit-form-text large area'])!!}
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

	<div class="product-get" style="position: absolute; z-index: -1; opacity: 0;">
		@foreach($products as $product)
			<?php
				$productstocks = Productstock::where('product_id', '=', $product->id)->where('is_active', '=', true)->get();

				$rak_options = [];
				$rak_options[''] = "Select Rak";
				foreach ($productstocks as $productstock) {
					$rak_options[$productstock->rak_id] = $productstock->rak->name;
				}
			?>

			<div class="rak{{$product->id}}">
				{!!Form::label('rak', 'Rak', ['class'=>'edit-form-label'])!!}
				{!!Form::select('rak', $rak_options, null, ['class'=>'edit-form-text large'])!!}
				<span class="edit-form-note">
					*Required
				</span>
			</div>
		@endforeach
	</div>
@endsection