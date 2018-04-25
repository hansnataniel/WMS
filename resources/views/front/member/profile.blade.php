<?php
    use Illuminate\Support\Str;

    use App\Models\Area;
?>

@extends('front.template.master')

@section('title')
    Member Profile
@endsection

@section('head_additional')
    {{HTML::script('js/select2.js')}}

    {{HTML::style('css/jquery-ui-back.css')}}
    {{HTML::style('css/select2.css')}}

    {!!HTML::style('css/jquery.datetimepicker.css')!!}
    {!!HTML::script('js/jquery.datetimepicker.js')!!}
    
    
    <script type="text/javascript">
        $(document).ready(function(){
            $('.datetimepicker').datetimepicker({
                timepicker: false,
                format: 'Y-m-d',
                maxDate: 0
            });

            $('.sign-up-terms').attr('checked', false);

            var cek = 0;
            $('.sign-up-terms').click(function(){
                if(cek == 0)
                {
                    $('.sign-up-submit.button').css({'display': 'none'});
                    cek = 1;
                }
                else
                {
                    $('.sign-up-submit.button').css({'display': 'inline-block'});
                    cek = 0;
                }
            });

            $(".select").select2();

            $('.province').on('change',function(){
                var selection = $('.province option:selected').val();
                $.ajax({
                    type: "GET",
                    url: "{{URL::to('sign-up/ajax-city')}}/"+selection,
                    success: function(msg){
                        $('.sign-province').html(msg);
                    }
                });
            });
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

    <style>
        span.select2-dropdown.select2-dropdown--above, span.select2-dropdown.select2-dropdown--below {
            left: 0px !important;
        }

        span#select2-province-container {
            margin-top: 0px;
        }

        .select2.select2-container {
            left: 0px !important;
            display: inline-block;
            vertical-align: top;
            margin-left: 0px !important;
            margin-bottom: 0px !important;
        }

        .sign-up-list span {
            margin-left: 0px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            top: 0px !important;
        }

        span#select2-city-container {
            margin-top: 0px;
        }
    </style>
@endsection

@section('content')
    <section class="profile">
        @if (Session::has('success-message'))
             <div class='validation-message success-message'>
                <div class="success-message-border">
                    {{Session::get('success-message')}}
                    <?php Session::forget('success-message'); ?>
                </div>
            </div>
        @endif

        <div class="profile-button">
            <a href="{{URL::to('member/profile')}}">
                <span class="active">MY PROFILE</span>
            </a>
            <a href="{{URL::to('member/my-transaction')}}">
                <span class="center">MY TRANSACTION</span>
            </a>
            <a href="{{URL::to('member/change-password')}}">
                <span>CHANGE PASSWORD</span>
            </a>
        </div>
        <div class="profile-content">
            <h1>Hi, {{$user->name}}</h1>
            <span>Selamat datang di <a href="#">iRemax.id</a>, saat ini Anda berada di halaman profile Anda. Di sini Anda dapat melihat semua informasi<br> profile atau transaction history Anda, serta memperbarui data diri / password Anda.</span>
            {{Form::open(array('url'=>URL::current(), 'method'=>'POST', 'files'=>true))}}

                @if(count($errors) != 0)
                    <div class="validation">
                        @foreach ($errors->all("<div class='validation-message error-message'>:message<div class='error-close-class'></div></div>") as $error)
                            {{$error}}
                        @endforeach
                    </div>
                @endif

                <div class="sign-up-list">
                    {{Form::label('email', 'Email*', array('class'=>'sign-up-label'))}}
                    {{Form::email('email', $user->email, array('class'=>'sign-up-textfield', 'required'))}}
                </div>
                
                <div class="sign-up-list">
                    {{Form::label('name', 'Name*', array('class'=>'sign-up-label'))}}
                    {{Form::text('name', $user->name, array('class'=>'sign-up-textfield', 'required'))}}
                </div>

                <div class="sign-up-list">
                    {{Form::label('phone', 'Phone*', array('class'=>'sign-up-label'))}}
                    {{Form::text('phone', $user->phone, array('class'=>'sign-up-textfield', 'required'))}}
                </div>

                <div class="sign-up-list">
                    {{Form::label('date_of_birth', 'Date of Birth', array('class'=>'sign-up-label'))}}
                    {{Form::text('date_of_birth', $user->birthdate, array('class'=>'sign-up-textfield datetimepicker', 'readonly'))}}
                </div>

                <div class="sign-up-list">
                    <?php
                        $area = Area::find($user->area_id);
                     ?>
                    {{Form::label('province', 'Province*', array('class'=>'sign-up-label'))}}
                    {{Form::select('province', $province_options, $area->province_id, array('class'=>'sign-up-textfield select province', 'required'))}}
                </div>

                <div class="sign-up-list sign-province">
                    {{Form::label('city', 'City*', array('class'=>'sign-up-label'))}}
                    {{Form::select('city', $area_options, Auth::user()->area_id, array('class'=>'sign-up-textfield select', 'required'))}}
                </div>

                <div class="sign-up-list">
                    {{Form::label('address', 'Address*', array('class'=>'sign-up-label'))}}
                    {{Form::text('address', $user->address, array('class'=>'sign-up-textfield', 'required'))}}
                </div>

                <div class="sign-up-list">
                    {{Form::label('zip_code', 'Zip Code', array('class'=>'sign-up-label'))}}
                    {{Form::text('zip_code', $user->zip_code, array('class'=>'sign-up-textfield'))}}
                </div>

                {{Form::submit('SAVE', array('class'=>'sign-up-submit'))}}
                
            {{Form::close()}}
        </div>
    </section>
@endsection