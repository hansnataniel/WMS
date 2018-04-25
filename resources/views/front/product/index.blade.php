<?php
    use Illuminate\Support\Str;

    use App\Models\Productphoto;
?>

@extends('front.template.master')

@section('title')
    @if($parent_category == 'Sale')
        SALE PRODUCT
    @else
        {{$category->name}}
    @endif
@endsection

@section('meta')
    @if($parent_category == 'Sale')
        <meta name="description" content="Remax, Sale Product">
    @else
        <meta name="description" content="Remax, {{$category->name}}">
    @endif
@endsection

@section('head_additional')
    <style type="text/css">
        @if(($parent_category != null) AND ($parent_category != 'Sale'))
            .nav-item.nav-item-{{$parent_category->id}} {
                border-bottom: solid 5px #000;
            }

        @endif


        @if($parent_category == 'Sale')
            .nav-item.nav-item-sale {
                border-bottom: solid 5px #000;
            }
        @else
            .nav-item.nav-item-{{$category->id}} {
                border-bottom: solid 5px #000;
            }
        @endif

        .product {
            position: relative;
        }

        /*.success-message {*/
            /*display: none;*/
        /*}*/

    </style>

    <script type="text/javascript">
        $(document).ready(function(){
            // $('.success-message').hide();

            $('.home-buy').click(function(){
                // $(this).parent().parent().parent().find('.loading-order').show();
                var id = $(this).attr('dataId');
                var qty = $(this).parent().find('.home-qty').val();
                $.ajax({
                    type: "GET",
                    url: "{{URL::to('small-cart/buy')}}/" + id + "/" + qty,
                    success: function(msg){
                        // $('.loading-order').hide();
                        $('.success-message .success-message-border').html(msg);
                        $('.home-qty').val('1');
                        $('.success-message').fadeIn(400);
                        $('.success-message').delay(3000).fadeOut(700);
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
    <section class="product">
        @if($parent_category != 'Sale')
            <div class="product-category-image">
                @if (file_exists(public_path() . '/img/category/' . $category->id . '_' . Str::slug($category->name, '_') . '.jpg'))
                    {{HTML::image('img/category/' . $category->id . '_' . Str::slug($category->name, '_') . '.jpg?lastmod=' . Str::random(5))}}
                @endif
            </div>
        @endif
        @if($parent_category == 'Sale')
            <h1>SALE PRODUCT</h1>
        @else
            <h1>{{$category->name}}</h1>
        @endif

        @if(count($products) != 0)
            <div class="home-content-item home-sale">
                @foreach($products as $product)
                    <?php $productphoto = Productphoto::where('product_id', '=', $product->id)->where('default', '=', 1)->first(); ?>
                    <div class="home-item">
                        <a href="{{URL::to('product/detail/' . $product->id . '/' . Str::slug($product->name, '-'))}}">
                            @if($productphoto != null)
                                {{HTML::image('usr/img/product/thumbnail/' . $productphoto->gambar, '', array('class'=>'home-item-image'))}}
                            @else
                                {!!HTML::image('img/front/no-image.jpg', '', ['class'=>'home-item-image'])!!}
                            @endif
                        </a>
                        @if($product->discount != 0)
                            <div class="home-item-ket">SALE!</div>
                        @endif
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

        <div class="product-paginate">
            {{$products->links()}}
        </div>
    </section>
@endsection