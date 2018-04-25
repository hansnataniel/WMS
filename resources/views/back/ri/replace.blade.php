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
{!!Form::select('product', $product_options, null, ['class'=>'edit-form-text select large product-select'])!!}
<span class="edit-form-note">
	*Required
</span>