<script type="text/javascript">
    $('.checkout-radio').click(function(){
        var voucher = $('.check-voucher').val();
        if(voucher == '')
        {
            voucher = 0;
        }
        var layanan = $(this).val();
        $.ajax({
            type: "GET",
            url: "{{URL::to('checkout/ajax-checkout')}}/" + voucher + '/' + layanan,
            success: function(msg){
                $('.checkout-ajax').html(msg);
                $('.checkout-ajax-voucher span').hide();
            },
            error: function(msg) {
                $('body').html(msg.responseText);
            }
        });
    });
</script>
@if(count($rates) != 0)
    <div class="checkout-text">
    {{Form::label('pilih_layanan', 'Pilih Layanan Pengiriman', array('class'=>'checkout-label'))}}
    @foreach($rates as $rate)
        <div>
            {{Form::radio('layanan_pengiriman', $rate->id, false, array('class'=>'checkout-radio'))}}
            <span>{{$rate->service->expedition->name}} - {{$rate->service->name . ' (Rp ' . rupiah3($rate->price * $weight_tolerance) . ')'}}</span>
        </div>
    @endforeach
    </div>
    <br>
@endif
<div class="checkout-text">
    {{Form::label('pesan', 'Pesan untuk kami (Contoh: dikirim sore hari)', array('class'=>'checkout-label'))}}
    {{Form::textarea('pesan', '', array('class'=>'checkout-textfield area'))}}
</div>
@if(count($rates) != 0)
    {{Form::hidden('weight_total', $weight_tolerance)}}
@else
    {{Form::hidden('weight_total', null)}}
@endif
