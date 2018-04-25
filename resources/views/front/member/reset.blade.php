@extends('front.template.master')

@section('title')
    Forgot Your Password?
@endsection

@section('head_additional')
    
@endsection

@section('content')
    <section class="sign-up forgot-password">
        @if (Session::has('success-message'))
             <div class='validation-message success-message'>
                <div class="success-message-border">
                    {{Session::get('success-message')}}
                </div>
            </div>
        @endif
        <h1>FORGOT YOUR PASSWORD?</h1>

        {{Form::open(array('url'=>URL::current(), 'method'=>'POST', 'files'=>true, 'class'=>'sign-up-form', 'style'=>'border-top: solid 1px #dbdbdb;'))}}
            @if(count($errors) != 0)
                <div class="validation">
                    @foreach ($errors->all("<div class='validation-message error-message'>:message<div class='error-close-class'></div></div>") as $error)
                        {!!$error!!}
                    @endforeach
                </div>
            @endif

            <p>Masukkan alamat email Anda yang terdaftar, dan masukkan password baru Anda.</p>

            <div class="sign-up-list">
                {{Form::label('email', 'Email*', array('class'=>'sign-up-label'))}}
                {{Form::text('email', '', array('class'=>'sign-up-textfield', 'required'))}}
            </div>
            <div class="sign-up-list">
                {{Form::label('new_password', 'New Password*', array('class'=>'sign-up-label'))}}
                {{Form::password('new_password', array('class'=>'sign-up-textfield', 'required'))}}
            </div>
            <div class="sign-up-list">
                {{Form::label('new_password_confirmation', 'New Password Confirmation*', array('class'=>'sign-up-label'))}}
                {{Form::password('new_password_confirmation', array('class'=>'sign-up-textfield', 'required'))}}
            </div>

            
            {{Form::submit('SEND', array('class'=>'sign-up-submit submit'))}}
            
        {{Form::close()}}
    </section>
@endsection