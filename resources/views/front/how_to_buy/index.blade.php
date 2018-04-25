@extends('front.template.master')

@section('title')
	How To Order
@endsection

@section('meta')
    <meta name="description" content="{{$setting->how_to_buy_meta_desc}}">
@endsection

@section('head_additional')

@endsection

@section('content')
    <section class="sign-in how">
        <h1>HOW TO BUY</h1>

        <div class="how-content">
            {!!$setting->how_to_buy!!}
        </div>
    </section>
@endsection