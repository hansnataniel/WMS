<?php
    use Illuminate\Support\Str;

    use App\Models\Productphoto;
?>

@extends('front.template.master')

@section('title')
	Home
@endsection

@section('meta')
    <meta name="description" content="Remax's Latest Designs &amp; Technologies">
@endsection

@section('head_additional')
    {!!HTML::style('css/responsiveslides.css')!!}
    {!!HTML::script('js/responsiveslides.min.js')!!}
    <script type="text/javascript">
        $(document).ready(function(){
            
            $("#slides").responsiveSlides({
                speed: 400,
                pager: true
            });

            $('.home-buy').click(function(){
                // $(this).parent().parent().parent().find('.loading-order').show();
                var id = $(this).attr('dataId');
                var qty = $(this).parent().find('.home-qty').val();
                $.ajax({
                    type: "GET",
                    url: "{{URL::to('small-cart/buy')}}/" + id + "/" + qty,
                    success: function(msg){
                        // $('.loading-order').hide();
                        $('.alert-message .success-message-border').html(msg);
                        $('.home-qty').val('1');
                        $('.alert-message').fadeIn(400);
                        $('.alert-message').delay(3000).fadeOut(700);
                    }
                });
            });

            $('.home-notify').click(function(){
                $('.notification').fadeIn(400);
                var product_id = $(this).attr('dataId');
                $('.notification-product-id').val(product_id);
            });

        });
    </script>
    <style type="text/css">
       .alert-message{
            display: none;
       }

       .warning {
            display: block;
       }
    </style>
@endsection

@section('content')
    <div class='validation-message success-message alert-message'>
        <div class="success-message-border">
            This product has been succesfully added to your cart
        </div>
    </div>
    @if (Session::has('success-message'))
         <div class='validation-message success-message'>
            <div class="success-message-border">
                {!!Session::get('success-message')!!}
            </div>
        </div>
    @endif
    <section class="home-content">
        <div class="home-slideshow">
            <ul id="slides">
                @foreach ($slideshows as $slideshow)
                    <li>
                        @if ($slideshow->url != null)
                            <a href="{{$slideshow->url}}" title="{{$slideshow->name}}" target="_blank">
                                {{HTML::image('usr/img/slideshow/' . $slideshow->id . '_' . Str::slug($slideshow->name, '_') . '.jpg', $slideshow->name, array('title'=> $slideshow->name))}}
                            </a>
                        @else
                            {{HTML::image('usr/img/slideshow/' . $slideshow->id . '_' . Str::slug($slideshow->name, '_') . '.jpg', $slideshow->name, array('title'=> $slideshow->name))}}
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="home-bottom">
            @if(count($products) != 0)
                <div class="home-content-item home-latest">
                    <h3>LATEST PRODUCT</h3>
                    <h5>Remax's Latest Designs &amp; Technologies</h5>
                    @foreach($products as $product)
                        <?php $productphoto = Productphoto::where('product_id', '=', $product->id)->where('default', '=', 1)->first(); ?>
                        <div class="home-item">
                            <a href="{{URL::to('product/detail/' . $product->id . '/' . Str::slug($product->name, '-'))}}">
                                @if($productphoto != null)
                                    {!!HTML::image('usr/img/product/thumbnail/' . $productphoto->gambar, '', array('class'=>'home-item-image'))!!}
                                @else
                                    {!!HTML::image('img/front/no-image.jpg', '', ['class'=>'home-item-image'])!!}
                                @endif
                            </a>
                            <div class="home-item-ket">New</div>
                            <div class="home-item-desc">
                                <a href="{{URL::to('product/detail/' . $product->id . '/' . Str::slug($product->name, '-'))}}">
                                    <div class="home-item-name">{{karakter($product->name, 40)}}</div>
                                </a>
                                <div class="home-item-price">
                                    @if($product->discount != 0)
                                        <span class="home-item-small">{{rupiah2($product->price)}}</span>
                                        <span>{{rupiah2($product->price - ($product->price * $product->discount / 100))}}</span>
                                    @else
                                        <span>{{rupiah2($product->price)}}</span>
                                    @endif
                                    @if($product->stock != 0)
                                        {{Form::text('qty', '1', array('class'=>'home-qty'))}}
                                        {{Form::button('BUY', array('class'=>'home-buy', 'dataId'=>$product->id))}}
                                    @else
                                        {{Form::button('NOTIFY ME', array('class'=>'home-notify', 'dataId'=>$product->id))}}
                                    @endif
                                </div>
                            </div>
                        </div>                
                    @endforeach
                </div>
            @endif

            @if(count($hot_products) != 0)
                <div class="home-content-item home-hot">
                    <h3>HOT PRODUCT!</h3>
                    <h5>Remax's Most Favourite Device</h5>
                    @foreach($hot_products as $product)
                        <?php $productphoto = Productphoto::where('product_id', '=', $product->id)->where('default', '=', 1)->first(); ?>
                        <div class="home-item">
                            <a href="{{URL::to('product/detail/' . $product->id . '/' . Str::slug($product->name, '-'))}}">
                                @if($productphoto != null)
                                    {!!HTML::image('usr/img/product/thumbnail/' . $productphoto->gambar, '', array('class'=>'home-item-image'))!!}
                                @else
                                    {!!HTML::image('img/front/no-image.jpg', '', ['class'=>'home-item-image'])!!}
                                @endif
                            </a>
                            <div class="home-item-ket">HOT!</div>
                            <div class="home-item-desc">
                                <a href="{{URL::to('product/detail/' . $product->id . '/' . Str::slug($product->name, '-'))}}">
                                    <div class="home-item-name">{{karakter($product->name, 40)}}</div>
                                </a>
                                <div class="home-item-price">
                                    @if($product->discount != 0)
                                        <span class="home-item-small">{{rupiah2($product->price)}}</span>
                                        <span>{{rupiah2($product->price - ($product->price * $product->discount / 100))}}</span>
                                    @else
                                        <span>{{rupiah2($product->price)}}</span>
                                    @endif
                                    @if($product->stock != 0)
                                        {{Form::text('qty', '1', array('class'=>'home-qty'))}}
                                        {{Form::button('BUY', array('class'=>'home-buy', 'dataId'=>$product->id))}}
                                    @else
                                        {{Form::button('NOTIFY ME', array('class'=>'home-notify', 'dataId'=>$product->id))}}
                                    @endif
                                </div>
                            </div>
                        </div>                
                    @endforeach
                    <div class="home-line"></div>
                </div>
            @endif

            @if(count($sale_products) != 0)
                <div class="home-content-item home-sale">
                    <h3>WHAT'S ON SALE?</h3>
                    <h5>Remax's Special Offer for You</h5>
                    @foreach($sale_products as $product)
                        <?php $productphoto = Productphoto::where('product_id', '=', $product->id)->where('default', '=', 1)->first(); ?>
                        <div class="home-item">
                            <a href="{{URL::to('product/detail/' . $product->id . '/' . Str::slug($product->name, '-'))}}">
                                @if($productphoto != null)
                                    {!!HTML::image('usr/img/product/thumbnail/' . $productphoto->gambar, '', array('class'=>'home-item-image'))!!}
                                @else
                                    {!!HTML::image('img/front/no-image.jpg', '', ['class'=>'home-item-image'])!!}
                                @endif
                            </a>
                            <div class="home-item-ket">SALE!</div>
                            <div class="home-item-desc">
                                <a href="{{URL::to('product/detail/' . $product->id . '/' . Str::slug($product->name, '-'))}}">
                                    <div class="home-item-name">{{karakter($product->name, 40)}}</div>
                                </a>
                                <div class="home-item-price">
                                    <span class="home-item-small">{{rupiah2($product->price)}}</span>
                                    <span>{{rupiah2($product->price - ($product->price * $product->discount / 100))}}</span>
                                    @if($product->stock != 0)
                                        {{Form::text('qty', '1', array('class'=>'home-qty'))}}
                                        {{Form::button('BUY', array('class'=>'home-buy', 'dataId'=>$product->id))}}
                                    @else
                                        {{Form::button('NOTIFY ME', array('class'=>'home-notify', 'dataId'=>$product->id))}}
                                    @endif
                                </div>
                            </div>
                        </div>                
                    @endforeach
                    <div class="home-line"></div>
                </div>
            @endif
        </div>

        <div class="home-delivery">
            @if($setting->is_free == 1)
                {!!HTML::image('usr/img/front/free-delivery.jpg')!!}
            @endif
            <div class="home-register">
                <div class="home-register-left">
                    {{Form::open(array('url'=>URL::to('register-newsletter'), 'method'=>'POST'))}}
                        {{Form::email('email', '', array('class'=>'home-register-textfield', 'placeholder'=>'Enter your email address', 'required'))}}
                        {{Form::submit('Register', array('class'=>'home-register-submit'))}}
                    {{Form::close()}}
                </div>
                <div class="home-register-right">
                    <h3>REGISTER YOUR EMAIL</h3>
                    <span>untuk mendapatkan info &amp; promo terbaru dari kami</span>
                </div>
            </div>
        </div>
    </section>
@endsection