<?php
	use App\Models\Supprice;
?>

<style type="text/css">
	.select2-container {
		z-index: 9999 !important;
	}

	.select2-container--default .select2-selection--single .select2-selection__rendered {
		text-align: left;
	}
</style>

<script type="text/javascript">
	$(function(){
		$('.select').each(function(){
			var data = $(this).attr('placeholder-data');

			$(this).select2({
				placeholder: data
			});
		});

		$('.product-select').change(function(){
			$('.rec-group').hide();

			if($(this).val() != '')
			{
				var dataids = $(this).val();

				$('.rec'+dataids).show();
			}
		});

		$('.radio-type').live('change', function(e){
			if($(this).hasClass('cash'))
			{
				$('.form-discount').removeAttr('max');
			}
			else
			{
				$('.form-discount').attr('max', '100');
			}
		});

		$('.form-open').submit(function(e){
			e.preventDefault();

			var dataproduct = $('.product-select').val();
			if(dataproduct == '')
			{
				$('.form-alert').show();
			}
			else
			{
				$('.form-submit').val('Loading...');

				$('.form-alert').hide();

				var dataqty = $('.qty').val();
				var dataprice = $('.price').val();
				if ($('.radio-type.cash').is(':checked'))
				{
					var datadiscounttype = '0';
				}
				if ($('.radio-type.percent').is(':checked'))
				{
					var datadiscounttype = '1';
				}
				var datadiscount = $('.form-discount').val();

				$.ajax({
					type: "GET",
					url: "{{URL::to(Crypt::decrypt($setting->admin_url) . '/po/form')}}/"+dataproduct+"/"+dataqty+"/"+dataprice+"/"+datadiscounttype+"/"+datadiscount,
					success:function(msg) {
						@if(isset($productid))
							$('.tr{{$productid}}').remove();
						@endif
						$('tr.global-discount').before(msg);

						$('.edit-product-text').change();

						$('.pop-container').click();
					},
					error:function(msg) {
						$('body').html(msg.responseText);
					}
				});
			}
		});
	});
</script>

{!! Form::open(['class'=>'form-open']) !!}
	<div class="form-alert">
		Please select product first
	</div>
	<div class="form-group index-del-item">
		{!! Form::label('product', 'Product', ['class'=>'form-label']) !!}
		<select class="form-select select product-select">
			<option value="">Select Product</option>
			@foreach($products as $product)
				@if($product->stock >= $product->max_stock)
					@if(isset($productid))
						@if($productid == $product->id)
							<option value="{{$product->id}}" disabled="disabled" selected>
						@else
							<option value="{{$product->id}}" disabled="disabled">
						@endif
					@else
						<option value="{{$product->id}}" disabled="disabled">
					@endif
						{{$product->name}}
					</option>
				@else
					@if(isset($productid))
						@if($productid == $product->id)
							<option value="{{$product->id}}" selected>
						@else
							<option value="{{$product->id}}">
						@endif
					@else
						<option value="{{$product->id}}">
					@endif
						{{$product->name}}
					</option>
				@endif
			@endforeach
		</select>
	</div>
	<div class="form-recomendation">
		@foreach($products as $product)
			<div class="rec-group rec{{$product->id}}">
				<?php
					$recomendations = Supprice::where('product_id', '=', $product->id)->orderBy('price', 'asc')->take(3)->get();
				?>

				@if(!$recomendations->isEmpty())
					<table class="recomendation-table">
						<tr class="recomendation-title">
							<th style="width: 150px;">
								Supplier
							</th>
							<th>
								Price
							</th>
							<th>
								Last Purchase
							</th>
						</tr>
						@foreach($recomendations as $recomendation)
							<tr>
								<td style="width: 150px;">
									{{$recomendation->supplier->name}}
								</td>
								<td>
									Rp {{number_format($recomendation->price)}}
								</td>
								<td>
									{{date('d/m/Y', strtotime($recomendation->updated_at))}}
								</td>
							</tr>
						@endforeach
					</table>
				@else
					<span class="no-recomendation">
						You don't have price recomendation for this product
					</span>
				@endif
			</div>
		@endforeach
	</div>
	<div class="form-group index-del-item">
		{!! Form::label('qty', 'Quantity', ['class'=>'form-label']) !!}
		@if(isset($qty))
			{!! Form::input('number', 'qty', $qty, ['class'=>'form-text qty', 'min'=>'1', 'required']) !!}
		@else
			{!! Form::input('number', 'qty', 1, ['class'=>'form-text qty', 'min'=>'1', 'required']) !!}
		@endif
	</div>
	<div class="form-group index-del-item">
		{!! Form::label('price', 'Price', ['class'=>'form-label']) !!}
		@if(isset($price))
			{!! Form::input('number', 'price', $price, ['class'=>'form-text price', 'min'=>'0', 'required']) !!}
		@else
			{!! Form::input('number', 'price', 0, ['class'=>'form-text price', 'min'=>'0', 'required']) !!}
		@endif
	</div>
	<div class="form-group index-del-item">
		{!! Form::label('discount-type', 'Discount Type', ['class'=>'form-label']) !!}
		<div class="form-text">
			<div class="type-container">
				@if(isset($discounttype))
					@if($discounttype == 0)
						<div class="radio-group">
							{!! Form::radio('discounttype', 0, true, ['class'=>'radio-type cash', 'id'=>'cash-product']) !!}
							{!! Form::label('cash-product', 'Rp', ['class'=>'radio-label']) !!}
						</div>
						<div class="radio-group">
							{!! Form::radio('discounttype', 1, false, ['class'=>'radio-type percent', 'id'=>'cash-percent']) !!}
							{!! Form::label('cash-percent', '%', ['class'=>'radio-label']) !!}
						</div>
					@else
						<div class="radio-group">
							{!! Form::radio('discounttype', 0, false, ['class'=>'radio-type cash', 'id'=>'cash-product']) !!}
							{!! Form::label('cash-product', 'Rp', ['class'=>'radio-label']) !!}
						</div>
						<div class="radio-group">
							{!! Form::radio('discounttype', 1, true, ['class'=>'radio-type percent', 'id'=>'cash-percent']) !!}
							{!! Form::label('cash-percent', '%', ['class'=>'radio-label']) !!}
						</div>
					@endif
				@else
					<div class="radio-group">
						{!! Form::radio('discounttype', 0, true, ['class'=>'radio-type cash', 'id'=>'cash-product']) !!}
						{!! Form::label('cash-product', 'Rp', ['class'=>'radio-label']) !!}
					</div>
					<div class="radio-group">
						{!! Form::radio('discounttype', 1, false, ['class'=>'radio-type percent', 'id'=>'cash-percent']) !!}
						{!! Form::label('cash-percent', '%', ['class'=>'radio-label']) !!}
					</div>
				@endif
			</div>
		</div>
	</div>
	<div class="form-group index-del-item">
		{!! Form::label('discount', 'Discount', ['class'=>'form-label']) !!}
		@if(isset($discount))
			{!! Form::input('number', 'discount', $discount, ['class'=>'form-text form-discount', 'min'=>'0', 'required']) !!}
		@else
			{!! Form::input('number', 'discount', 0, ['class'=>'form-text form-discount', 'min'=>'0', 'required']) !!}
		@endif
	</div>
	{!! Form::submit('Add Product', ['class'=>'form-submit']) !!}
{!! Form::close() !!}