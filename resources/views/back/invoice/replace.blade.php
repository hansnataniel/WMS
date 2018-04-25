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

{!!Form::label('recieve_item', 'Recieve Item', ['class'=>'edit-form-label'])!!}
{!!Form::select('recieve_item', $ri_options, null, ['class'=>'edit-form-text ri-select select large'])!!}
<span class="edit-form-note">
	
</span>