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
	<option value="">
		Select Product
	</option>
	@if(!$transactiondetails->isEmpty())
		@foreach($transactiondetails as $transactiondetail)
			@if($transactiondetail->qty > 0)
				<option value="{{$transactiondetail->id}}">{{$transactiondetail->product->name}}</option>
			@endif
		@endforeach
	@endif
</select>

<span class="edit-form-note">
	
</span>