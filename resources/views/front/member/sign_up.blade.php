<?php
    use Illuminate\Support\Str;

    use App\Models\Shipping;
?>

@extends('front.template.master')

@section('title')
    New Member Registration
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
            margin-left: 0px !important;
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
    <section class="sign-up">
        @if (Session::has('success-message'))
             <div class='validation-message success-message'>
                <div class="success-message-border">
                    {{Session::get('success-message')}}
                </div>
            </div>
        @endif
        <h1>NEW MEMBER REGISTRATION</h1>
        {{HTML::image('img/front/signup.jpg')}}

        {{Form::open(array('url'=>URL::current(), 'method'=>'POST', 'files'=>true, 'class'=>'sign-up-form'))}}
            @if(count($errors) != 0)
                <div class="validation">
                    @foreach ($errors->all("<div class='validation-message error-message'>:message<div class='error-close-class'></div></div>") as $error)
                        {!!$error!!}
                    @endforeach
                </div>
            @endif

            <div class="sign-up-list">
                {{Form::label('email', 'Email*', array('class'=>'sign-up-label'))}}
                {{Form::email('email', '', array('class'=>'sign-up-textfield', 'required'))}}
            </div>

            <div class="sign-up-list">
                {{Form::label('password', 'Password*', array('class'=>'sign-up-label'))}}
                {{Form::password('password', array('class'=>'sign-up-textfield', 'required'))}}
            </div>
            
            <div class="sign-up-list">
                {{Form::label('password_confirmation', 'Confirm Password*', array('class'=>'sign-up-label'))}}
                {{Form::password('password_confirmation', array('class'=>'sign-up-textfield', 'required'))}}
            </div>
            
            <div class="sign-up-list">
                {{Form::label('name', 'Name*', array('class'=>'sign-up-label'))}}
                {{Form::text('name', '', array('class'=>'sign-up-textfield', 'required'))}}
            </div>

            <div class="sign-up-list">
                {{Form::label('phone', 'Phone*', array('class'=>'sign-up-label'))}}
                {{Form::text('phone', '', array('class'=>'sign-up-textfield', 'required'))}}
            </div>

            <div class="sign-up-list">
                {{Form::label('date_of_birth', 'Date of Birth', array('class'=>'sign-up-label'))}}
                {{Form::text('date_of_birth', '', array('class'=>'sign-up-textfield datetimepicker', 'readonly'))}}
            </div>

            <div class="sign-up-list">
                {{Form::label('province', 'Province*', array('class'=>'sign-up-label'))}}
                {{Form::select('province', $province_options, '', array('class'=>'sign-up-textfield select province', 'required'))}}
            </div>

            <div class="sign-up-list sign-province">
               
            </div>

            <div class="sign-up-list">
                {{Form::label('address', 'Address*', array('class'=>'sign-up-label'))}}
                {{Form::text('address', '', array('class'=>'sign-up-textfield', 'required'))}}
            </div>

            <div class="sign-up-list">
                {{Form::label('zip_code', 'Zip Code', array('class'=>'sign-up-label'))}}
                {{Form::text('zip_code', '', array('class'=>'sign-up-textfield'))}}
            </div>

            <div class="sign-up-list">
                <br>
                {{Form::checkbox('terms', true, false, array('class'=>'sign-up-checkbox sign-up-terms'))}}
                <?php $shipping = Shipping::first(); ?>
                <span> Saya telah membaca dan menyetujui <a href="{{URL::to('shipping-and-policies/' . $shipping->id . '/' . Str::slug($shipping->title, '-'))}}" target="_blank">Terms and Conditions</a> </span>
            </div>

            <div class="sign-up-list">
                {{Form::checkbox('newsletter', true, false, array('class'=>'sign-up-checkbox'))}}
                <span> Daftar Newsletter </span>
            </div>

            {{Form::submit('SIGN UP', array('class'=>'sign-up-submit submit'))}}
            {{Form::button('SIGN UP', array('class'=>'sign-up-submit button'))}}
            
        {{Form::close()}}
    </section>
@endsection