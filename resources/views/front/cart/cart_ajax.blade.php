<?php
    use App\Models\Setting;
    use App\Models\Productphoto;
?>

<script type="text/javascript">
    @if($stok == false)
        $('.alert-message').show();
        $('.alert-message').delay(3000).fadeOut(700);
    @endif

    $('.cart-remove').click(function(){
        $('.cart-loader').show();
        var delId = $(this).attr('dataid');
        $.ajax({
            type: "GET",
            url: "{{URL::to('cart/edit')}}/" + delId + "/0",
            success: function(msg){
                $('.cart-ajax').html(msg);
                $('.cart-loader').hide();
            }
        });
    });

    @if(count($carts) == 0)
        $('.cart-checkout').hide();
    @else
        $('.cart-checkout').show();
    @endif

</script>

<?php $setting = Setting::first(); ?>

<table class="cart-table">
    <tr class='cart-table-header'>
        <td colspan="2">Product</td>
        <td style="text-align: right;">Qty</td>
        <td style="text-align: right;">Price Each (Rp)</td>
        <td style="text-align: right;">Subtotal (Rp)</td>
    </tr>
    @if(count($carts) != 0)
        <?php $total = 0; ?>
        @foreach($carts as $cart)
            <?php $productphoto = Productphoto::where('product_id', '=', $cart->id)->where('default', '=', 1)->first(); ?>
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
                    <div class="cart-remove" dataid="{{$cart->id}}">{{HTML::image('img/front/cart-delete.png')}} Remove</div>
                </td>
                <td style="text-align: right;">
                    {{Form::input('number', 'qty', $cart->quantity, array('class'=>'cart-qty', 'data_id'=>$cart->id))}}
                </td>
                <td style="text-align: right;">
                    @if($cart->product_price != $cart->price)
                        <span style="font-size: 14px; text-decoration: line-through; color: red;">{{rupiah3($cart->product_price)}}</span>
                    @endif
                    <span>{{rupiah3($cart->price)}}</span>
                </td>
                <td style="text-align: right;"><span>{{rupiah3($cart->price_total)}}</span></td>
            </tr>
            <?php $total = $total + $cart->price_total; ?>
        @endforeach
    @else
         <tr class='cart-table-item'>
            <td colspan="5" style="text-align: center;">Your shopping cart is empty</td>
        </tr>
    @endif
    @if(count($carts) != 0)
        <tr class='cart-table-total' >
            <td colspan="2" style="padding-left: 0;">
                @if($setting->is_free == 1)
                    {{HTML::image('img/front/free-delivery2.jpg')}}
                @endif
            </td>
            <td colspan="2" style="text-align: right;">Total (Rp):</td>
            <td style="text-align: right; font-size: 24px; font-weight: bold;">{{rupiah3($total)}}</td>
        </tr>
    @endif
</table>