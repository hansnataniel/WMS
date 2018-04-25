<?php
    use Illuminate\Support\Str;
    
    use App\Models\Product;
    use App\Models\Productphoto;
?>

@extends('front.template.master')

@section('title')
    My Shopping Cart
@endsection

@section('head_additional')
    <script type="text/javascript">
         $(document).ready(function() {
            $('.alert-message').hide();
            
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

            $('.cart-qty').live('change', function(){
                $('.cart-loader').show();
                var id = $(this).attr('data_id');
                var qty = $(this).val();
                $.ajax({
                    type: "GET",
                    url: "{{URL::to('cart/edit')}}/" + id + "/" + qty,
                    success: function(msg){
                        $('.cart-ajax').html(msg);
                        $('.cart-loader').hide();
                    }
                });
            });
        });
    </script>
@endsection

@section('content')
    <div class='validation-message success-message alert-message'>
        <div class="success-message-border">
            The amount of desired item is exceeding the stock
        </div>
    </div>
    <section class="cart">
        @if (Session::has('success-message'))
            <div class='validation-message success-message'><div class="success-message-border">{{Session::get('success-message')}}</div></div>
        @endif
        <h1>MY SHOPPING CART</h1>
        <div class="cart-ajax">
            <table class="cart-table">
                <tr class='cart-table-header'>
                    <td colspan="2">Product</td>
                    <td style="text-align: right;">Qty</td>
                    <td style="text-align: right;">Price Each (Rp)</td>
                    <td style="text-align: right;">Subtotal (Rp)</td>
                </tr>
                <?php $total = 0; ?>
                @if(count($carts) != 0)
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
                        <td colspan="5">Shopping cart is empty</td>
                    </tr>
                @endif

                <tr class='cart-table-total'>
                    <td colspan="5" style="padding: 0;"><div class="cart-line"></div></td>
                </tr>
                <tr class='cart-table-total'>
                    <td colspan="2" style="padding-left: 0;">
                        @if($setting->is_free == 1)
                            {{HTML::image('img/front/free-delivery2.jpg')}}
                        @endif
                    </td>
                    <td colspan="2" style="text-align: right;">Total (Rp):</td>
                    <td style="text-align: right; font-size: 24px; font-weight: bold;">{{rupiah3($total)}}</td>
                </tr>
            </table>
        </div>

        <a href="{{URL::to('/')}}">
            {{HTML::image('img/front/continue-shop.jpg', '', array('class'=>'cart-continue'))}}
        </a>
        <a href="{{URL::to('checkout/step1')}}">
            {{HTML::image('img/front/checkout.jpg', '', array('class'=>'cart-checkout'))}}
        </a>
    </section>
@endsection