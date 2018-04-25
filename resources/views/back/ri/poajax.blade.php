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

{!!Form::label('purchase_order', 'Purchase Order', ['class'=>'edit-form-label'])!!}
{!!Form::select('purchase_order', $po_options, null, ['class'=>'edit-form-text nota select large'])!!}
<span class="edit-form-note">
	*Required
</span>