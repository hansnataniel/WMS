<script type="text/javascript">
	$('.area-penerima').on('change',function(){
        var selection = $('.area-penerima option:selected').val();
        if(selection == '')
        {
            selection = 0;
        }
        $.ajax({
            type: "GET",
            url: "{{URL::to('checkout/ajax-rate')}}/"+selection,
            success: function(msg){
                $('.ajax-layanan').html(msg);
            }
        });
    });

    $(".select").select2();
</script>

{{Form::select('area_alamat_penerima', $area_options, '', array('class'=>'checkout-textfield select area-penerima', 'required'))}}