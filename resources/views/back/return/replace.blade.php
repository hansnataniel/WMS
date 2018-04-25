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
	@if(!$ridetails->isEmpty())
		@foreach($ridetails as $ridetail)
			@if($ridetail->qty > 0)
				<option value="{{$ridetail->id}}">{{$ridetail->podetail->product->name}}</option>
			@endif
		@endforeach
	@endif
	@if(!$frees->isEmpty())
		<optgroup label="-- Free Product --">
			@foreach($frees as $free)
				@if($free->qty > 0)
					<option value="{{$free->id}}">{{$free->product->name}}</option>
				@endif
			@endforeach
		</optgroup>
	@endif
</select>

<span class="edit-form-note">
	
</span>