<?php
	use App\Models\Supprice;
	use App\Models\Invoicedetail;
	use App\Models\Ridetail;
	use App\Models\Returndetail;
	use App\Models\Product;
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

			var pricedata = $('option:selected', this).attr('pricedata');
			var maxdata = $('option:selected', this).attr('maxdata');
			var ridetaildata = $('option:selected', this).attr('ridetaildata');

			if($(this).val() != '')
			{
				var dataids = $(this).val();
				$('.form-text.price').val(pricedata);
				$('.form-text.qty').attr('max', maxdata);
				$('.form-text.qty').attr('ridetaildata', ridetaildata);

				$('.rec'+dataids).show();
			}
		});

		if($('.product-select').val() != '')
		{
			var pricedata = $('option:selected', $('.product-select')).attr('pricedata');
			var maxdata = $('option:selected', $('.product-select')).attr('maxdata');
			var ridetaildata = $('option:selected', $('.product-select')).attr('ridetaildata');

			$('.form-text.price').val(pricedata);
			$('.form-text.qty').attr('max', maxdata);
			$('.form-text.qty').attr('ridetaildata', ridetaildata);
		}

		$('.form-open').submit(function(e){
			e.preventDefault();

			var dataproduct = $('.form-select.product-select').val();
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

				var ridetaildata = $('option:selected', $('.product-select')).attr('ridetaildata');

				$.ajax({
					type: "GET",
					url: "{{URL::to(Crypt::decrypt($setting->admin_url) . '/return/form')}}/"+ridetaildata+"/"+dataproduct+"/"+dataqty+"/"+dataprice,
					success:function(msg) {
						$('.tr'+ridetaildata).remove();
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
			@foreach($ridetails as $ridetail)
				@if($ridetail->qty > 0)
					<?php
						$invoice = Invoicedetail::where('ridetail_id', '=', $ridetail->id)->first();

						$total = 0;
						$max = 0;

						$ridetailget = Ridetail::find($ridetail->id);
						if(Session::has('returnid'))
						{
							$returndetails = Returndetail::where('ridetail_id', '=', $ridetail->id)->where('return_id', '!=', Session::get('returnid'))->get();
						}
						else
						{
							$returndetails = Returndetail::where('ridetail_id', '=', $ridetail->id)->get();
						}

						foreach ($returndetails as $returndetail) {
							$total = $total + $returndetail->qty;
						}

						if(!$returndetails->isEmpty())
						{
							$max = $ridetailget->qty - $total;
						}
						else
						{
							$max = $ridetailget->qty;
						}
					?>
					@if($invoice != null)
						@if(isset($ridetailid))
							@if($ridetailid == $ridetail->id)
								<option value="{{$ridetail->podetail->product_id}}" ridetaildata="{{$ridetail->id}}" pricedata="{{$invoice->price}}" maxdata="{{$max}}" selected>{{$ridetail->podetail->product->name}}</option>
							@else
								<option value="{{$ridetail->podetail->product_id}}" ridetaildata="{{$ridetail->id}}" pricedata="{{$invoice->price}}" maxdata="{{$max}}">{{$ridetail->podetail->product->name}}</option>
							@endif
						@else
							<option value="{{$ridetail->podetail->product_id}}" ridetaildata="{{$ridetail->id}}" pricedata="{{$invoice->price}}" maxdata="{{$max}}">{{$ridetail->podetail->product->name}}</option>
						@endif
					@else
						@if(isset($ridetailid))
							@if($ridetailid == $ridetail->id)
								<option value="{{$ridetail->podetail->product_id}}" ridetaildata="{{$ridetail->id}}" pricedata="{{$ridetail->podetail->price}}" maxdata="{{$max}}" selected>{{$ridetail->podetail->product->name}}</option>
							@else
								<option value="{{$ridetail->podetail->product_id}}" ridetaildata="{{$ridetail->id}}" pricedata="{{$ridetail->podetail->price}}" maxdata="{{$max}}">{{$ridetail->podetail->product->name}}</option>
							@endif
						@else
							<option value="{{$ridetail->podetail->product_id}}" ridetaildata="{{$ridetail->id}}" pricedata="{{$ridetail->podetail->price}}" maxdata="{{$max}}">{{$ridetail->podetail->product->name}}</option>
						@endif
					@endif
				@endif
			@endforeach
			@if(!$frees->isEmpty())
				<optgroup label="-- Free Product --">
					@foreach($frees as $free)
						<?php
							$total = 0;
							$max = 0;

							$ridetailget = Ridetail::find($free->id);
							$returndetails = Returndetail::where('ridetail_id', '=', $free->id)->get();
							foreach ($returndetails as $returndetail) {
								$total = $total + $returndetail->qty;
							}

							if(!$returndetails->isEmpty())
							{
								$max = $ridetailget->qty - $total;
							}
							else
							{
								$max = $ridetailget->qty;
							}
						?>
						@if($free->qty > 0)
							@if(isset($ridetailid))
								@if($ridetailid == $free->id)
									<option value="{{$free->product_id}}" ridetaildata="{{$free->id}}" pricedata="0" maxdata="{{$max}}" selected>{{$free->product->name}}</option>
								@else
									<option value="{{$free->product_id}}" ridetaildata="{{$free->id}}" pricedata="0" maxdata="{{$max}}">{{$free->product->name}}</option>
								@endif
							@else
								<option value="{{$free->product_id}}" ridetaildata="{{$free->id}}" pricedata="0" maxdata="{{$max}}">{{$free->product->name}}</option>
							@endif
						@endif
					@endforeach
				</optgroup>
			@endif
		</select>
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
	{!! Form::submit('Return Product', ['class'=>'form-submit']) !!}
{!! Form::close() !!}