<?php
    use App\Models\Productphoto;
    use App\Models\Area;
?>

@extends('front.template.master')

@section('title')
    Checkout | Voucher & Delivery
@endsection

@section('head_additional')
    {{HTML::script('js/select2.js')}}

    {{HTML::style('css/select2.css')}}

    <script type="text/javascript">
        $(document).ready(function(){
            @if(Auth::guest() == null)
                $.ajax({
                    type: "GET",
                    url: "{{URL::to('checkout/ajax-rate')}}/{{Auth::user()->area_id}}",
                    success: function(msg){
                        $('.ajax-layanan').html(msg);
                    }
                });
            @endif

            $('.checkout-use').live('click', function(){
                var voucher = $('.check-voucher').val();
                var layanan = $('.checkout-radio:checked').val();
                if(layanan == '')
                {
                    layanan = 0;
                }

                $.ajax({
                    type: "GET",
                    url: "{{URL::to('checkout/ajax-checkout')}}/" + voucher + '/' + layanan,
                    success: function(msg){
                        $('.checkout-ajax').html(msg);
                        var voucher_val = $('.voucher-val-hasil').val();
                        $('.checkout-voucher-val').val(voucher_val);
                    },
                    error: function(msg) {
                        $('body').html(msg.responseText);
                    }
                });
            });

            $('.checkout-cencel').live('click', function(){
                $('.check-voucher').val('');
                var voucher = '0';
                var layanan = $('.checkout-radio:checked').val();
                if(layanan == '')
                {
                    layanan = 0;
                }
                $.ajax({
                    type: "GET",
                    url: "{{URL::to('checkout/ajax-checkout')}}/" + voucher + '/' + layanan,
                    success: function(msg){
                        $('.checkout-ajax').html(msg);
                        $('.voucher-val-hasil').val('');
                        $('.checkout-voucher-val').val('');
                        $('.checkout-ajax-voucher span').hide();
                        $('.checkout-cencel, .checkout-voucher-val').hide();
                        // $('.check-voucher, .checkout-use').css({'display':'inline-block'});
                    },
                    error: function(msg) {
                        $('body').html(msg.responseText);
                    }
                });
            });

            $('.area-penerima').live('change',function(){
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
                    },
                    error: function(msg) {
                        $('body').html(msg.responseText);
                    }
                });
            });

            $('.province').live('change',function(){
                var selection = $('.province option:selected').val();
                if(selection == '')
                {
                    selection = 0;
                }

                $.ajax({
                    type: "GET",
                    url: "{{URL::to('checkout/ajax-city')}}/"+selection,
                    success: function(msg){
                        $('.ajax-province').html(msg);
                    }
                });
            });

            $(".select").select2();
        });
    </script>

    <style type="text/css">
        .select2.select2-container {
            left: 0px !important;
        }

        span.select2-selection.select2-selection--single {
            color: #5c5c5c;
            font-size: 14px;
        }

        span.select2-dropdown.select2-dropdown--above, span.select2-dropdown.select2-dropdown--below {
            left: 0px !important;
        }

        span.select2-selection__rendered {
            margin-top: 0px;
        }

        .select2.select2-container {
            left: 0px !important;
            margin-left: 0px !important;
        }

        .sign-up-list span {
            margin-left: 0px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            top: 0px !important;
        }
    </style>
@endsection

@section('content')
    <section class="checkout">
        @if (Session::has('success-message'))
            <div class='validation-message success-message'><div class="success-message-border">{{Session::get('success-message')}}</div></div>
        @endif
        <div class="checkout-header">
            <h1>CHECKOUT</h1>
            <span class="checkout-active">STEP 1: VOUCHER &amp; DELIVERY</span>
            <span>STEP 2: PAYMENT</span>
            <span>STEP 3: PAYMENT CONFIRMATION</span>
        </div>
        @if(count($errors) != 0)
            <br><br>
            <div class="validation">
                @foreach ($errors->all("<div class='validation-message error-message'>:message<div class='error-close-class'></div></div>") as $error)
                    {!!$error!!}
                @endforeach
            </div>
        @endif
        {{Form::open(array('url'=>URL::current(), 'method'=>'POST', 'files'=>TRUE))}}
            <h3>VOUCHER</h3>
            <div class="checkout-ajax-voucher">

                <span>Invalid code, please re-check your voucher.</span>
                {{Form::text('voucher', '', array('class'=>'checkout-textfield check-voucher', 'placeholder'=>'Have a voucher code?'))}}
                {{Form::button('USE VOUCHER', array('class'=>'checkout-use'))}}

                {{Form::text('voucher_val', '', array('class'=>'checkout-textfield checkout-voucher-val'))}}  
                {{Form::button('CANCEL VOUCHER', array('class'=>'checkout-cencel'))}}

            </div>

            <div class="cart-line checkout-line"></div>
            <div class="cart-line checkout-line"></div>

            <h3>DELIVERY</h3>
            <div class="checkout-left">
                <h5>Pengirim</h5>

                <span class="checkout-ket">*Dapat Anda rubah dengan nama dan alamat Anda sendiri atau lainnya (send as gift)</span>

                <div class="checkout-text">
                    {{Form::label('nama_pengirim', 'Nama Pengirim', array('class'=>'checkout-label'))}}
                    <span>*</span>
                </div>
                {{Form::text('nama_pengirim', $setting->name, array('class'=>'checkout-textfield', 'required'))}}

                <div class="checkout-text">
                    {{Form::label('alamat_pengirim', 'Alamat Pengirim', array('class'=>'checkout-label'))}}
                    <span>*</span>
                </div>
                @if($setting->address == null)
                    {{Form::text('alamat_pengirim', 'www.iremax.id', array('class'=>'checkout-textfield', 'required'))}}
                @else
                    {{Form::text('alamat_pengirim', $setting->address, array('class'=>'checkout-textfield', 'required'))}}
                @endif

            </div><!--
         --><div class="checkout-right">
                <h5>Penerima</h5>

                <span class="checkout-ket">*Dapat Anda rubah dengan nama dan alamat yang ingin Anda tuju</span>

                @if(Auth::guest() != null)
                    <div class="checkout-text">
                        {{Form::label('nama_penerima', 'Nama Penerima', array('class'=>'checkout-label'))}}
                    </div>
                    {{Form::text('nama_penerima', '', array('class'=>'checkout-textfield', 'required'))}}

                    <div class="checkout-text">
                        {{Form::label('telepon', 'Telepon', array('class'=>'checkout-label'))}}
                    </div>
                    {{Form::text('telepon', '', array('class'=>'checkout-textfield', 'required'))}}

                    <div class="checkout-text">
                        {{Form::label('alamat_penerima', 'Alamat Penerima', array('class'=>'checkout-label'))}}
                    </div>
                    {{Form::select('provinsi_penerima', $province_options, '', array('class'=>'checkout-textfield select province', 'required'))}}
                    <div class="ajax-province">
                        
                    </div>
                    {{Form::text('alamat_penerima', '', array('class'=>'checkout-textfield textfield2', 'required', 'placeholder'=>'Alamat'))}}
                @else
                    <div class="checkout-text">
                        {{Form::label('nama_penerima', 'Nama Penerima', array('class'=>'checkout-label'))}}
                    </div>
                    {{Form::text('nama_penerima', Auth::user()->name, array('class'=>'checkout-textfield', 'required'))}}

                    <div class="checkout-text">
                        {{Form::label('telepon', 'Telepon', array('class'=>'checkout-label'))}}
                    </div>
                    {{Form::text('telepon', Auth::user()->phone, array('class'=>'checkout-textfield', 'required'))}}

                    <div class="checkout-text">
                        {{Form::label('alamat_penerima', 'Alamat Penerima', array('class'=>'checkout-label'))}}
                    </div>
                    <?php $area = Area::find(Auth::user()->area_id); ?>
                    {{Form::select('provinsi_penerima', $province_options, $area->province_id, array('class'=>'checkout-textfield select province', 'required'))}}
                    <div class="ajax-province">
                        {{Form::select('area_alamat_penerima', $area_options, Auth::user()->area_id, array('class'=>'checkout-textfield select area-penerima', 'required'))}}
                    </div>
                    {{Form::text('alamat_penerima', Auth::user()->address, array('class'=>'checkout-textfield textfield2', 'required'))}}
                @endif
            </div>

            <div class="cart-line checkout-line"></div>
            <div class="cart-line checkout-line"></div>

            <h3>DELIVERY WITH</h3>

            <div class="checkout-text ajax-layanan">
                <div class="checkout-text">
                    {{Form::label('pesan', 'Pesan untuk kami (Contoh: dikirim sore hari)', array('class'=>'checkout-label'))}}
                    {{Form::textarea('pesan', '', array('class'=>'checkout-textfield area'))}}
                </div>
                {{Form::hidden('weight_total', '')}}
            </div>

            <div class="cart-line checkout-line "></div>

            <br>
            <br>
            <div class="checkout-ajax">
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
                           {{--  <td style="text-align: right;"><span>{{rupiah3($cart->quantity_discount)}}</span></td> --}}
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
                        <td colspan="2" style="text-align: right; font-weight: bold;">{{rupiah3(0)}}</td>
                        {{Form::hidden('voucher_code', '')}}
                    </tr>
                    <tr class='cart-table-total2'>
                        <td style="padding-left: 0;"></td>
                        <td colspan="2" style="text-align: right;">Delivery Cost (Rp) :</td>
                        <td colspan="2" style="text-align: right; font-weight: bold;">{{rupiah3(0)}}</td>
                        {{Form::hidden('rate_price', '')}}
                    </tr>
                    <tr class='cart-table-total2'>
                        <td style="padding-left: 0;"></td>
                        <td colspan="2" style="text-align: right; border-top: solid 1px #dbdbdb">Total Payment (Rp) :</td>
                        <td colspan="2" style="text-align: right; font-size: 24px; font-weight: bold; border-top: solid 1px #dbdbdb">{{rupiah3($total)}}</td>
                        {{Form::hidden('amount_to_pay', $total)}}
                    </tr>
                </table>
            </div>
            <a href="{{URL::to('cart')}}">
                {{HTML::image('img/front/edit-cart.jpg', '', array('class'=>'cart-continue'))}}
            </a>

            {{Form::image('img/front/payment.jpg', '', array('class'=>'cart-checkout'))}}
        {{Form::close()}}
    </section>
@endsection