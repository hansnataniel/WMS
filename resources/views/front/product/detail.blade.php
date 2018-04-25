<?php
    use Illuminate\Support\Str;
?>

@extends('front.template.master')

@section('title')
    {{$product->name}}
@endsection

@section('meta')
    <meta name="description" content="{{strip_tags(karakter($product->description, 70))}}">

    {{-- For Facebook --}}
    <meta property="og:site_name" content="iRemax.id"/>
    <meta property="og:title" content="{{$product->name}}" />
    <meta property="og:description" content="{{strip_tags(karakter($product->description, 70))}}" />
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{URL::current()}}"/>
    @if($default_photo != null)
        <meta property="og:image" content="{{URL::to('/')}}{{'/usr/img/product/' . $default_photo->gambar}}"/>
    @endif

    {{-- For Twitter --}}
    <meta name="twitter:site" content="iRemax">
    <meta name="twitter:title" content="{{$product->name}}">
    <meta name="twitter:description" content="{{strip_tags(karakter($product->description, 70))}}">
    <meta name="twitter:creator" content="@iremax">
    @if($default_photo != null)
        <meta name="twitter:image" content="{{URL::to('/')}}{{'/usr/img/product/' . $default_photo->gambar}}">
    @endif
    <meta name="twitter:domain" content="iRemax.id">
@endsection

@section('head_additional')
    {{HTML::style('css/jquery.fancybox.css')}}
    {{HTML::script('js/jquery.fancybox.js')}}
    {{HTML::style('css/slick.css')}}
    {{HTML::script('js/slick.js')}}

    <style type="text/css">
        @if($parent_category != null)
            .nav-item.nav-item-{{$parent_category->id}} {
                border-bottom: solid 5px #000;
            }
        @endif

        .nav-item.nav-item-{{$category->id}} {
            border-bottom: solid 5px #000;
        }

        .fancybox-overlay {
            background-color: rgba(255, 255, 255, 0.8) !important;
        }

        .success-message {
            /*display: none;*/
        }

    </style>


    <script type="text/javascript">
        $(document).ready(function(){
            $('.detail-button').click(function(){
                // $(this).parent().parent().parent().find('.loading-order').show();
                var id = $(this).attr('dataId');
                var qty = $(this).parent().find('.detail-qty').val();
                $.ajax({
                    type: "GET",
                    url: "{{URL::to('small-cart/buy')}}/" + id + "/" + qty,
                    success: function(msg){
                        // $('.loading-order').hide();
                        $('.success-message .success-message-border').html(msg);
                        $('.detail-qty').val('1');
                        $('.success-message').fadeIn(400);
                        $('.success-message').delay(3000).fadeOut(700);
                    }
                });
            });

            $('.detail-qtydiscount-item').click(function(){
                var qty_total = $(this).attr('qtyTotal');
                $('.detail-qty').val(qty_total);
            });

            $('.detail-item-small').click(function(){
                var url_img = $(this).attr('urlImg');
                $('.detail-default').attr('src', '{{URL::to("/")}}/usr/img/product/thumbnail/' + url_img);
                $('.fancybox').attr('href', '{{URL::to("/")}}/usr/img/product/' + url_img);
            });

            $(".fancybox").fancybox({
                openEffect  : 'none',
                closeEffect : 'none'
            });

            $('.detail-notify').click(function(){
                $('.notification').fadeIn(400);
                var product_id = $(this).attr('dataId');
                $('.notification-product-id').val(product_id);
            });

            $('.detail-large').slick({
                slidesToShow: 5,
                slidesToScroll: 1,
                autoplay: false,
            });

            $('.detail-next').click(function(){
                $('button.slick-next.slick-arrow').click();
            });
        });
    </script>
@endsection

@section('content')
    <div class='validation-message success-message' style="display: none;">
        <div class="success-message-border">
            This product has been succesfully added to your cart
        </div>
    </div>
    @if (Session::has('success-message'))
         <div class='validation-message success-message'>
            <div class="success-message-border">
                {{Session::get('success-message')}}
            </div>
        </div>
    @endif
    <section class="detail">
        <div class="detail-content">
            <h1 class="small">{{$product->name}}</h1>
            <div class="detail-left">
                <div style="position: relative;">
                    @if($product->discount != 0)
                        <div class="detail-sale">SALE!</div>
                    @endif
                    @if($default_photo != null)
                        <a href="{{URL::to('/')}}{{'/usr/img/product/' . $default_photo->gambar}}" class="fancybox" style="display: block;">
                            {{HTML::image('usr/img/product/thumbnail/' . $default_photo->gambar, '', array('class'=>'detail-default'))}}
                            {{HTML::image('usr/img/front/icon-glass-yellow.png', '', array('class'=>'detail-glass'))}}
                        </a>
                    @endif
                </div>
                <div class="detail-small-image">
                    @if(!$productphotos->isEmpty())
                        @if(count($productphotos) >= 6)
                            {{HTML::image('img/front/detail-previous.jpg', '', array('class'=>'detail-previous'))}}
                        @endif
                        <div class="detail-small-content">
                            <div class="detail-large">
                                @foreach($productphotos as $productphoto)
                                    <div>
                                        {{HTML::image('usr/img/product/small/' . $productphoto->gambar, '', array('class'=>'detail-item-small', 'urlImg'=>$productphoto->gambar))}}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @if(count($productphotos) >= 6)
                            {{HTML::image('img/front/detail-previous.jpg', '', array('class'=>'detail-next'))}}
                        @endif
                    @endif
                </div>

                <div class="detail-share">
                    <span>Share this product: </span>
                    <a href="#" onclick="window.open('https://www.facebook.com/sharer/sharer.php?u='+encodeURIComponent(location.href), 'facebook-share-dialog', 'width=626,height=436'); return false;">
                        <span class="footer-facebook"></span>
                    </a>
                    <a href = "http://twitter.com/home?status={{$product->name}}, {{URL::current()}}" target = "_blank">
                        <span class="footer-twitter">
                        </span></a>
                </div>
                <a href="{{URL::to('product/category/' . $category->id . '/' . Str::slug($category->name, '-'))}}">
                    <div class="detail-back">
                        {{HTML::image('img/front/row-left.png')}} Back
                    </div>
                </a>
            </div>
            <div class="detail-right">
                <h1 class="desktop">{{$product->name}}</h1>
                @if($product->discount == 0)
                    <?php 
                        $product_price = $product->price;
                    ?>
                    <div class="detail-price2">{{rupiah2($product_price)}}</div>
                @else
                    <?php 
                        $product_price = $product->price - ($product->price * $product->discount / 100);
                    ?>
                    <div class="detail-price1">{{rupiah2($product->price)}}</div>
                    <div class="detail-price2">{{rupiah2($product_price)}}</div>
                @endif
                
                <div class="detail-form">
                    @if($product->stock != 0)
                    <span>QUANTITY: </span>
                    {{Form::text('qty', '1', array('class'=>'detail-qty'))}}
                    {{Form::button('BUY', array('class'=>'detail-button', 'dataId'=>$product->id))}}
                    @else
                        {{Form::button('NOTIFY ME', array('class'=>'detail-button detail-notify', 'dataId'=>$product->id))}}
                    @endif
                </div>
                <div class="detail-desc">
                    {!!$product->description!!}
                </div>
            </div>
        </div>
    </section>
@endsection