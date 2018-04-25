<?php
    use Illuminate\Support\Str;
?>

@extends('front.template.master')

@section('title')
	{{$shipping_content->title}}
@endsection

@section('meta')
    <meta name="description" content="{{$shipping_content->meta_desc}}">
@endsection

@section('head_additional')

@endsection

@section('content')
     <section class="sign-in terms">
        <h1>SHIPPING &amp; POLICIES</h1>

        <div class="terms-content">
            <div class="terms-left">
                <?php $no = 1; ?>
                @foreach($shippings as $shipping)
                    <a href="{{URL::to('shipping-and-policies/' . $shipping->id . '/' . Str::slug($shipping->title, '-'))}}">
                        @if($shipping->id == $shipping_content->id)
                            @if($no++ == count($shippings))
                                <span class="active" style="border: none;">
                            @else
                                <span class="active">
                            @endif
                        @else
                            @if($no++ == count($shippings))
                                <span style="border: none;">
                            @else
                                <span>
                            @endif
                        @endif
                            {{$shipping->title }}
                        </span>
                    </a>
                @endforeach
            </div><!--
         --><div class="terms-right">
                <h2>{{$shipping_content->title}}</h2>
                <div class="terms-ket">
                    {!!$shipping_content->description!!}
                </div>
            </div>
        </div>

    </section>
@endsection