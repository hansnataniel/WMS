<?php
    use App\Models\Productphoto;
?>

<script type="text/javascript">
    @if($cek_vouceher != null)
        @if($min_transaction == false)
            $('.checkout-ajax-voucher span').hide();
            $('.check-voucher, .checkout-use').hide();
            $('.checkout-textfield.checkout-voucher-val, .checkout-cencel').css({'display':'inline-block'});
        @else
            $('.checkout-ajax-voucher span').text('Minimum transaction for this voucher is Rp. {{$minimal_transaksi}}');
            $('.checkout-ajax-voucher span').css({'display':'block'});
        @endif
    @else
        $('.checkout-ajax-voucher span').text('Invalid code or voucher is terminated, please re-check your voucher.');
        $('.checkout-ajax-voucher span').css({'display':'block'});
        $('.check-voucher, .checkout-use').css({'display':'inline-block'});
    @endif

    @if($voucher == '0')
        $('.checkout-ajax-voucher span').css({'display':'block'});
    @endif
    
</script>

<style type="text/css">
     
</style>
<table class="cart-table">
    <tr class='cart-table-header'>
        <td colspan="2">Product</td>
        <td style="text-align: right;">Qty</td>
        <td style="text-align: right;">Price Each (Rp)</td>
        {{-- <td style="text-align: right;">Qty Discount (Rp)</td> --}}
        <td style="text-align: right;">Subtotal (Rp)</td>
    </tr>
    <?php $total = 0; ?>
    @foreach($carts as $cart)
        <?php $productphoto = Productphoto::where('product_id', '=', $cart->id)->first(); ?>
        <tr class='cart-table-item'>
            <td style="max-width: 50px; min-width: 80px;">
                @if($productphoto != null)
                    {{HTML::image('usr/img/product/small/' . $productphoto->gambar, '', array('class'=>'cart-img'))}}
                @else
                    {!!HTML::image('img/front/no-image.jpg', '', ['class'=>'cart-img'])!!}
                @endif
            </td>
            <td>
                <span>{{$cart->name}}</span>
            </td>
            <td style="text-align: right;">
                {{$cart->quantity}}
            </td>
            <td style="text-align: right;">
                @if($cart->product_price != $cart->price)
                    <span style="font-size: 14px; text-decoration: line-through; color: red;">{{rupiah3($cart->product_price)}}</span>
                @endif
                <span>{{rupiah3($cart->price)}}</span>
            </td>
            {{-- <td style="text-align: right;"><span>{{rupiah3($cart->quantity_discount)}}</span></td> --}}
            <td style="text-align: right;"><span>{{rupiah3($cart->price_total)}}</span></td>
        </tr>
        <?php $total = $total + $cart->price_total; ?>
    @endforeach

    <tr class='cart-table-total'>
        <td colspan="5" style="padding: 0;"><div class="cart-line"></div></td>
    </tr>
    <tr class='cart-table-total cart-table-total2'>
        <td style="padding-left: 0;"></td>
        <td colspan="2" style="text-align: right;">Total (Rp) :</td>
        <td colspan="2" style="text-align: right; font-weight: bold;">{{rupiah3($total)}}</td>
        {{Form::hidden('total', $total)}}
    </tr>
    <tr class='cart-table-total2'>
        <td style="padding-left: 0;"></td>
        <td colspan="2" style="text-align: right;">Voucher (Rp) :</td>
        @if(($cek_vouceher != null) AND ($min_transaction == false))
            {{Form::hidden('voucher_code', $cek_vouceher->code)}}
            @if($cek_vouceher->type != 0)
                {{Form::hidden('voucher_val_ajax', $cek_vouceher->code . ' - Rp ' .  rupiah3($cek_vouceher->value), array('class'=>'voucher-val-hasil'))}}
                <td colspan="2" style="text-align: right; font-weight: bold;">{{rupiah3($cek_vouceher->value)}}</td>
            @else
                {{Form::hidden('voucher_val_ajax', $cek_vouceher->code . ' - ' . $cek_vouceher->value . ' %', array('class'=>'voucher-val-hasil'))}}
                <td colspan="2" style="text-align: right; font-weight: bold;">{{rupiah3($total * $cek_vouceher->value / 100)}}</td>
            @endif
        @else
            {{Form::hidden('voucher_code', '')}}
            {{Form::hidden('voucher_val_ajax', '', array('class'=>'voucher-val-hasil'))}}
            <td colspan="2" style="text-align: right; font-weight: bold;">{{rupiah3(0)}}</td>
        @endif
    </tr>
    <tr class='cart-table-total2'>
        <td style="padding-left: 0;"></td>
        <td colspan="2" style="text-align: right;">Delivery Cost (Rp) :</td>
        <td colspan="2" style="text-align: right; font-weight: bold;">{{rupiah3($rate_price * $weight_tolerance)}}</td>
        {{Form::hidden('rate_price', $rate_price)}}
    </tr>
    <tr class='cart-table-total2'>
        <td style="padding-left: 0;"></td>
        <td colspan="2" style="text-align: right; border-top: solid 1px #dbdbdb">Total Payment (Rp) :</td>
        <td colspan="2" style="text-align: right; font-size: 24px; font-weight: bold; border-top: solid 1px #dbdbdb">{{rupiah3($price_total)}}</td>
        {{Form::hidden('amount_to_pay', $price_total)}}
    </tr>
</table>