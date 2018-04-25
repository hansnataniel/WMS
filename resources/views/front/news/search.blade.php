<?php
    use Illuminate\Support\Str;
?>

@extends('front.template.master')

@section('title')
	News
@endsection

@section('head_additional')
    
@endsection

@section('content')
    <section class="news">
        <div class="news-left">
             @if(count($newses) != null)
                @foreach($newses as $news)
                    <span class="news-date">{{date('d F Y', strtotime($news->created_at))}}</span>
                    <a href="{{URL::to('news/detail/' . $news->id . '/' . Str::slug($news->title, '-'))}}"><h2>{{$news->title}}</h2></a>
                    @if (file_exists(public_path() . '/img/news/' . $news->id . '_' . Str::slug($news->title, '_') . '.jpg'))
                        {{HTML::image('img/news/' . $news->id . '_' . Str::slug($news->title, '_') . '.jpg?lastmod=' . Str::random(5))}}
                    @endif
                    <span class="news-desc">
                        {{$news->short_desc}} <a href="{{URL::to('news/detail/' . $news->id . '/' . Str::slug($news->title, '-'))}}">Read More</a>
                    </span>
                @endforeach
                <div class="product-paginate">
                    {{$newses->links()}}
                </div>
            @else
                <div class="news-not-found">0 article(s) found for "{{$src_title}}" in News.</div>
            @endif
        </div><!--
     --><div class="news-right">
            {{Form::open(array('url'=>URL::to('news/search'), 'method'=>'GET', 'files'=>true, 'class'=>'news-form'))}}
                {{Form::text('src_title', $src_title, array('class'=>'news-textfield'))}}
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