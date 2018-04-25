<?php
    use Illuminate\Support\Str;
    
    use App\Models\Product;
    use App\Models\Productphoto;
?>

<script type="text/javascript">
    $('.cart-small-delete').click(function(){
        $(this).parent().find('.cart-loader').show();
        var id = $(this).attr('dataid');
        $.ajax({
            type: "GET",
            url: "{{URL::to('small-cart/delete')}}/" + id,
            success: function(msg){
                // location.reload(true);
                $('.cart-small').html(msg);
                $(this).parent().find('.cart-loader').hide();
            }
        });
    });
</script>

@if(count($carts) != 0)
     <?php 
        $total = 0;
     ?>
    @foreach($carts as $cart)
        <?php 
            $product = Product::find($cart->id); 
            $productphoto = Productphoto::where('product_id', '=', $product->id)->where('default', '=', 1)->first();
            $total = $total + $cart->price_total;
        ?>
        <div class="cart-small-item">
            <div class="cart-small-left">
                @if($productphoto != null)
                    {{HTML::image('usr/img/product/small/' . $productphoto->gambar, '', array('class'=>'cart-small-img'))}}
                @else
                    {!!HTML::image('img/front/no-image.jpg', '', ['class'=>'cart-small-img', 'style'=>'border: 1px solid #d2d2d2;'])!!}
                @endif
            </div><!--
         --><div class="cart-small-right">
                <span>{{karakter($cart->name, 20)}}</span>
                @if($cart->quantity_discount == 0)
                    <span>{{rupiah3($cart->price)}}</span>
                @else
                    <span>{{rupiah3($cart->price - ($cart->quantity_discount / $cart->quantity))}}</span>
                @endif
                <span class="cart-small-qty">Qty: {{$cart->quantity}}</span>
            </div>
            {{HTML::image('img/front/cart-delete.png', '', array('class'=>'cart-small-delete', 'dataid'=>$cart->id))}}
            {{HTML::image('img/loading.gif', '', array('class'=>'cart-loader'))}}
        </div>
    @endforeach

    <div class="cart-small-total">
       Total: {{rupiah3($total)}}
    </div>
    <a href="{{URL::to('cart')}}">{{Form::button('VIEW CART', array('class'=>'button left'))}}</a>
    <a href="{{URL::to('checkout/step1')}}">{{Form::button('CHECKOUT', array('class'=>'button'))}}</a>
@else
    <div class="cart-small-null">Your cart 0 item</div>
@endif