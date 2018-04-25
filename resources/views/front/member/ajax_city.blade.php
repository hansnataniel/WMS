    
<script type="text/javascript">
    $(".select").select2();

</script>

{{Form::label('city', 'City*', array('class'=>'sign-up-label'))}}
{{Form::select('city', $area_options, '', array('class'=>'sign-up-textfield select', 'required'))}}