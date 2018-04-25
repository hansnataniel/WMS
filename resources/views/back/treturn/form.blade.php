<?php
	use App\Models\Supprice;
	use App\Models\Invoicedetail;
	use App\Models\Transactiondetail;
	use App\Models\Treturndetail;
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
			var transactiondetaildata = $('option:selected', this).attr('transactiondetaildata');

			if($(this).val() != '')
			{
				var dataids = $(this).val();
				$('.form-text.price').val(pricedata);
				$('.form-text.qty').attr('max', maxdata);
				$('.form-text.qty').attr('transactiondetaildata', transactiondetaildata);

				$('.rec'+dataids).show();
			}
		});

		if($('.product-select').val() != '')
		{
			var pricedata = $('option:selected', $('.product-select')).attr('pricedata');
			var maxdata = $('option:selected', $('.product-select')).attr('maxdata');
			var transactiondetaildata = $('option:selected', $('.product-select')).attr('transactiondetaildata');

			$('.form-text.price').val(pricedata);
			$('.form-text.qty').attr('max', maxdata);
			$('.form-text.qty').attr('transactiondetaildata', transactiondetaildata);
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

				var transactiondetaildata = $('option:selected', $('.product-select')).attr('transactiondetaildata');

				$.ajax({
					type: "GET",
					url: "{{URL::to(Crypt::decrypt($setting->admin_url) . '/treturn/form')}}/"+transactiondetaildata+"/"+dataproduct+"/"+dataqty+"/"+dataprice,
					success:function(msg) {
						$('.tr'+transactiondetaildata).remove();
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
			@foreach($transactiondetails as $transactiondetail)
				@if($transactiondetail->qty > 0)
					<?php
						$total = 0;
						$max = 0;

						if(Session::has('returnid'))
						{
							$returndetails = Treturndetail::where('transactiondetail_id', '=', $transactiondetail->id)->where('treturn_id', '!=', Session::get('returnid'))->get();
						}
						else
						{
							$returndetails = Treturndetail::where('transactiondetail_id', '=', $transactiondetail->id)->get();
						}

						if(!$returndetails->isEmpty())
						{
							foreach ($returndetails as $returndetail) {
								$total = $total + $returndetail->qty;
							}

							$max = $transactiondetail->qty - $total;
						}
						else
						{
							$max = $transactiondetail->qty;
						}
					?>

					@if(isset($transactiondetailid))
						@if($transactiondetailid == $transactiondetail->id)
							<option value="{{$transactiondetail->product_id}}" transactiondetaildata="{{$transactiondetail->id}}" pricedata="{{ceil($transactiondetail->price)}}" maxdata="{{$max}}" selected>{{$transactiondetail->product->name}}</option>
						@else
							<option value="{{$transactiondetail->product_id}}" transactiondetaildata="{{$transactiondetail->id}}" pricedata="{{ceil($transactiondetail->price)}}" maxdata="{{$max}}">{{$transactiondetail->product->name}}</option>
						@endif
					@else
						<option value="{{$transactiondetail->product_id}}" transactiondetaildata="{{$transactiondetail->id}}" pricedata="{{ceil($transactiondetail->price)}}" maxdata="{{$max}}">{{$transactiondetail->product->name}}</option>
					@endif
				@endif
			@endforeach
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
			{!! Form::input('number', 'price', ceil($price), ['class'=>'form-text price', 'min'=>'0', 'required']) !!}
		@else
			{!! Form::input('number', 'price', 0, ['class'=>'form-text price', 'min'=>'0', 'required']) !!}
		@endif
	</div>
	{!! Form::submit('Return Product', ['class'=>'form-submit']) !!}
{!! Form::close() !!}