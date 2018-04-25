<?php
    use Illuminate\Support\Str;
?>

@extends('front.template.master')

@section('title')
	{{$news->title}}
@endsection

@section('meta')
    <meta name="description" content="{{$news->meta_desc}}">
    <meta name="keyword" content="{{$news->meta_key}}">

     {{-- For Facebook --}}
    <meta property="og:site_name" content="iRemax.id"/>
    <meta property="og:title" content="{{$news->title}}" />
    <meta property="og:description" content="{{strip_tags(karakter($news->short_desc, 70))}}" />
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{URL::current()}}"/>
    <meta property="og:image" content="{{URL::to('/')}}{{'/img/news/' . $news->id . '_' . Str::slug($news->title, '_') . '.jpg'}}"/>

    {{-- For Twitter --}}
    <meta name="twitter:site" content="iRemax">
    <meta name="twitter:title" content="{{$news->title}}">
    <meta name="twitter:description" content="{{strip_tags(karakter($news->short_desc, 70))}}">
    <meta name="twitter:creator" content="@iremax">
    <meta name="twitter:image" content="{{URL::to('/')}}{{'/img/news/' . $news->id . '_' . Str::slug($news->title, '_') . '.jpg'}}">
    <meta name="twitter:domain" content="iRemax.id">
@endsection


@section('head_additional')
    
@endsection

@section('content')
    <section class="news">
        <div class="news-left">
            <span class="news-date">{{date('d F Y', strtotime($news->created_at))}}</span>
            <h2>{{$news->title}}</h2>
            @if (file_exists(public_path() . '/img/news/' . $news->id . '_' . Str::slug($news->title, '_') . '.jpg'))
                {{HTML::image('img/news/' . $news->id . '_' . Str::slug($news->title, '_') . '.jpg?lastmod=' . Str::random(5))}}
            @endif
            <span class="news-desc">
                {!!$news->description!!}
            </span>

            <div class="detail-share">
                <span>Share this news: </span>
                <a href="#" onclick="window.open('https://www.facebook.com/sharer/sharer.php?u='+encodeURIComponent(location.href), 'facebook-share-dialog', 'width=626,height=436'); return false;">
                    <span class="footer-facebook"></span>
                </a>
                <a href = "http://twitter.com/home?status={{$news->name}}, {{URL::current()}}" target = "_blank">
                    <span class="footer-twitter">
                    </span></a>
            </div>
            
            <a href="{{URL::to('news')}}">{{HTML::image('img/front/back.jpg', '', array('class'=>'history-back'))}}</a>
        </div><!--
     --><div class="news-right">
            {{Form::open(array('url'=>URL::to('news/search'), 'method'=>'GET', 'files'=>true, 'class'=>'news-form'))}}
                {{Form::text('src_title', '', array('class'=>'news-textfield'))}}
                {{Form::image('img/front/icon-glass-yellow.png', '', array('class'=>'news-submit'))}}
            {{Form::close()}}
            <h2>RECENT NEWS</h2>
            <ul>
                @foreach($recent_newses as $recent)
                    <a href="{{URL::to('news/detail/' . $recent->id . '/' . Str::slug($recent->title, '-'))}}">
                        <li>{{$recent->title}}</li>
                    </a>
                @endforeach
            </ul>
        </div>
    </section>
@endsection