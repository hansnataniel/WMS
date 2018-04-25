<?php
	use App\Models\Supprice;
	use App\Models\Productstock;
	use App\Models\Rak;
?>

<style type="text/css">
	.select2-container {
		z-index: 9999 !important;
	}

	.select2-container--default .select2-selection--single .select2-selection__rendered {
		text-align: left;
	}

	.rak-group {
		position: absolute;
		display: none;
	}

	.price-group {
		position: absolute;
		display: none;
	}

	.price {
		padding-left: 25px !important;
		border: 0px !important;
	}

	.price-label:after {
		/*content: 'Rp ';*/
		position: absolute;
		display: block;
		z-index: 1;
		right: -18px;
		top: 0px;
	}

	.form-select.rak {
		padding: 0px 5px;
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

				$('.rak-result').html($('.rak'+dataids).html());
				$('.price-result').html($('.price'+dataids).html());

				$('.rak-result').find('.form-select').attr('required', 'required').attr('name', 'rak_id');
				$('.price-result').find('.get-price').attr('name', 'price');

				$('.form-text.qty').attr('max', $('.price'+dataids+' .get-price').attr('max'));
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

				var datarak = $('.rak-result .rak').val();
				var dataqty = $('.qty').val();
				var dataprice = $('.price-result .get-price').val();
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
					url: "{{URL::to(Crypt::decrypt($setting->admin_url) . '/transaction/form')}}/"+dataproduct+"/"+datarak+"/"+dataqty+"/"+dataprice+"/"+datadiscounttype+"/"+datadiscount,
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
	<div class="form-group index-del-item rak-result">
		@if(isset($productid))
			<?php
				$productstocks = Productstock::where('product_id', '=', $productid)->where('is_active', '=', true)->get();
				$rakids = [];
				foreach ($productstocks as $productstock) {
					$rakids[] = $productstock->rak_id;
				}
			?>

			@if(!$productstocks->isEmpty())
				<?php
					$rak_options = [];
					$rak_options[''] = "Select Rak";
					$raks = Rak::whereIn('id', $rakids)->get();
					foreach ($raks as $rak) {
						$rak_options[$rak->id] = $rak->name;
					}
				?>

				{!! Form::label('rak_id', 'Rak', ['class'=>'form-label']) !!}
				{!! Form::select('raks', $rak_options, $rakid, ['class'=>'form-select rak']) !!}
			@endif
		@endif
	</div>
	<div class="form-group index-del-item">
		{!! Form::label('qty', 'Quantity', ['class'=>'form-label']) !!}
		@if(isset($productid))
			<?php
				$productstocks = Productstock::where('product_id', '=', $productid)->where('is_active', '=', true)->get();
				$totalstock = 0;
				foreach ($productstocks as $productstock) {
					$totalstock += $productstock->stock;
				}
			?>
			@if(isset($qty))
				{!! Form::input('number', 'qty', $qty, ['class'=>'form-text qty', 'min'=>'1', 'max'=>$totalstock - $qty, 'required']) !!}
			@else
				{!! Form::input('number', 'qty', 1, ['class'=>'form-text qty', 'min'=>'1', 'max'=>$totalstock, 'required']) !!}
			@endif
		@else
			@if(isset($qty))
				{!! Form::input('number', 'qty', $qty, ['class'=>'form-text qty', 'min'=>'1', 'required']) !!}
			@else
				{!! Form::input('number', 'qty', 1, ['class'=>'form-text qty', 'min'=>'1', 'required']) !!}
			@endif
		@endif
	</div>
	<div class="form-group index-del-item price-result">
		@if(isset($productid))
			{!! Form::label('price', 'Price', ['class'=>'form-label price-label']) !!}
			{!! Form::text('prices', number_format($product->price), ['class'=>'form-text price', 'min'=>'0', 'required', 'readonly']) !!}
			{!! Form::hidden('prices', $product->price, ['class'=>'form-text get-price', 'min'=>'0', 'required', 'readonly']) !!}
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

@foreach($products as $product)
	<div class="rak-group rak{{$product->id}}">
		<?php
			$productstocks = Productstock::where('product_id', '=', $product->id)->where('stock', '>', 0)->where('is_active', '=', true)->get();
			$rakids = [];
			foreach ($productstocks as $productstock) {
				$rakids[] = $productstock->rak_id;
			}
		?>

		@if(!$productstocks->isEmpty())
			<?php
				$rak_options = [];
				$rak_options[''] = "Select Rak";
				$raks = Rak::whereIn('id', $rakids)->get();
				foreach ($raks as $rak) {
					$rak_options[$rak->id] = $rak->name;
				}
			?>

			{!! Form::label('rak_id', 'Rak', ['class'=>'form-label']) !!}
			{!! Form::select('raks', $rak_options, null, ['class'=>'form-select rak']) !!}
		@endif
	</div>

	<div class="price-group price{{$product->id}}">
		<?php
			$totalstock = 0;
			foreach ($productstocks as $productstock) {
				$totalstock += $productstock->stock;
			}
		?>

		{!! Form::label('price', 'Price', ['class'=>'form-label price-label']) !!}
		{!! Form::text('prices', ceil($product->price), ['class'=>'form-text get-price', 'min'=>'0', 'max'=>$totalstock, 'required']) !!}
		{{-- {!! Form::hidden('prices', $product->price, ['class'=>'form-text get-price', 'min'=>'0', 'max'=>$totalstock, 'required', 'readonly']) !!} --}}
	</div>
@endforeach