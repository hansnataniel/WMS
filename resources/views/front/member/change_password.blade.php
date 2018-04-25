@extends('front.template.master')

@section('title')
    Member Profile
@endsection

@section('head_additional')
    {{HTML::script('js/select2.js')}}

    {{HTML::style('css/jquery-ui-back.css')}}
    {{HTML::style('css/select2.css')}}
    
    <script type="text/javascript">
        $(document).ready(function(){
            $(".select").select2();
        });

        $(function() {
            $(".datepicker").datepicker({
                changeMonth: true,
                changeYear: true,
                yearRange: "-70",
                dateFormat: 'yy-mm-dd'
            });
        });
    </script>

    <style type="text/css">
        .select2.select2-container {
            height: 40px !important;
            position: relative !important;
            display: inline-block;
            left: -8px !important;
            margin-bottom: 0px;
        }
    </style>
@endsection

@section('content')
    <section class="profile">
        @if (Session::has('success-message'))
             <div class='validation-message success-message'>
                <div class="success-message-border">
                    {{Session::get('success-message')}}
                </div>
            </div>
        @endif

        <div class="profile-button">
            <a href="{{URL::to('member/profile')}}">
                <span>MY PROFILE</span>
            </a>
            <a href="{{URL::to('member/my-transaction')}}">
                <span class="center">MY TRANSACTION</span>
            </a>
            <a href="{{URL::to('member/change-password')}}">
                <span class="active">CHANGE PASSWORD</span>
            </a>
        </div>
        <div class="profile-content">
            <h1>Change Password</h1>
            <br>
            {{Form::open(array('url'=>URL::current(), 'method'=>'POST', 'files'=>true))}}

                @if(count($errors) != 0)
                    <div class="validation">
                        @foreach ($errors->all("<div class='validation-message error-message'>:message<div class='error-close-class'></div></div>") as $error)
                            {{$error}}
                        @endforeach
                    </div>
                @endif

            <div class="sign-up-list">
                {{Form::label('old_password', 'Old Password*', array('class'=>'sign-up-label'))}}
                {{Form::password('old_password', array('class'=>'sign-up-textfield', 'required'))}}
            </div>

            <div class="sign-up-list">
                {{Form::label('new_password', 'New Password*', array('class'=>'sign-up-label'))}}
                {{Form::password('new_password', array('class'=>'sign-up-textfield', 'required'))}}
            </div>
            
            <div class="sign-up-list">
                {{Form::label('new_password_confirmation', 'New Password Confirmation*', array('class'=>'sign-up-label'))}}
                {{Form::password('new_password_confirmation', array('class'=>'sign-up-textfield', 'required'))}}
            </div>

                {{Form::submit('SAVE', array('class'=>'sign-up-submit'))}}
                
            {{Form::close()}}
        </div>
    </section>
@endsection