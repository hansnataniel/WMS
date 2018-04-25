<script type="text/javascript">
	$(function(){
		$('.select').each(function(){
			var data = $(this).attr('placeholder-data');

			$(this).select2({
				placeholder: data
			});
		});
	});
</script>

{!!Form::label('product', 'Product', ['class'=>'edit-form-label'])!!}
<select class="edit-form-text select large product-select">
	<option value="">Select Product</option>
	@foreach($products as $product)
		@if($product->stock >= $product->max_stock)
			<option value="{{$product->id}}" disabled="disabled">{{$product->name}}</option>
		@else
			<option value="{{$product->id}}">{{$product->name}}</option>
		@endif
	@endforeach
</select>
<span class="edit-form-note">
	*Required
</span>