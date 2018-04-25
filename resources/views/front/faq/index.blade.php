<?php
    use Illuminate\Support\Str;
?>

@extends('front.template.master')

@section('title')
	{{$faq_content->title}}
@endsection

@section('meta')
    <meta name="description" content="{{$faq_content->meta_desc}}">
@endsection

@section('head_additional')

@endsection

@section('content')
     <section class="sign-in terms">
        <h1>FREQUENTLY ASKED QUESTIONS</h1>

        <div class="terms-content">
            <div class="terms-left">
                <?php $no = 1; ?>
                @foreach($faqs as $faq)
                    <a href="{{URL::to('faq/' . $faq->id . '/' . Str::slug($faq->title, '-'))}}">
                        @if($faq->id == $faq_content->id)
                            @if($no++ == count($faqs))
                                <span class="active" style="border: none;">
                            @else
                                <span class="active">
                            @endif
                        @else
                            @if($no++ == count($faqs))
                                <span style="border: none;">
                            @else
                                <span>
                            @endif
                        @endif
                            {{$faq->title}}
                        </span>
                    </a>
                @endforeach
            </div><!--
         --><div class="terms-right">
                <h2>{{$faq_content->title}}</h2>
                <div class="terms-ket">
                    {!!$faq_content->description!!}
                </div>
            </div>
        </div>

    </section>
@endsection