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

{!!Form::label('rak', 'Rak', ['class'=>'edit-form-label'])!!}
{!!Form::select('rak', $rak_options, null, ['class'=>'edit-form-text rak-select select medium'])!!}
<span class="edit-form-note">
	
</span>