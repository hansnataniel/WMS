@extends('front.template.master')

@section('title')
    SIGN IN / SIGN UP / GUEST CHECKOUT
@endsection

@section('head_additional')
    {{HTML::script('js/select2.js')}}

    {{HTML::style('css/jquery-ui-back.css')}}
    {{HTML::style('css/select2.css')}}
    
    <script type="text/javascript">
        $(document).ready(function(){
            $('.sign-up-terms').attr('checked', false);

            $('.sign-up-terms').click(function(){
                $('.sign-up-submit.button').toggle();
            });

            $(".select").select2();

            $('#sign-in-button1').click(function(){
                $('#sign-in-button2').removeClass('active');
                $(this).addClass('active');
                $('.sign-in-left1').show();
                $('.sign-in-left2').hide();
            });

            $('#sign-in-button2').click(function(){
                $('#sign-in-button1').removeClass('active');
                $(this).addClass('active');
                $('.sign-in-left2').show();
                $('.sign-in-left1').hide();
            });

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
@endsection

@section('content')
    <section class="sign-in">
        @if (Session::has('success-message'))
             <div class='validation-message success-message'>
                <div class="success-message-border">
                    {{Session::get('success-message')}}
                </div>
            </div>
        @endif

        <h1>SIGN IN / SIGN UP / GUEST CHECKOUT</h1>

        <div class="sign-in-left">
            <div class="sign-in-button active" id="sign-in-button1">SIGN IN</div><!--
         --><div class="sign-in-button" id="sign-in-button2">SIGN UP <span>(CREATE AN ACCOUNT)</span></div>
            @if(count($errors) != 0)
                <div class="validation">
                    @foreach ($errors->all("<div class='validation-message error-message'>:message<div class='error-close-class'></div></div>") as $error)
                        {!!$error!!}
                    @endforeach
                </div>
            @endif
            <div class="sign-in-left1">
                {{Form::open(array('url'=>URL::to('sign-in/login'), 'method'=>'POST', 'files'=>true, 'class'=>'sign-up-form'))}}
                    <div class="sign-up-list">
                        {{Form::label('email_sign_in', 'Email', array('class'=>'sign-up-label'))}}
                        {{Form::email('email_sign_in', '', array('class'=>'sign-up-textfield', 'required'))}}
                    </div>

                    <div class="sign-up-list">
                        {{Form::label('password', 'Password', array('class'=>'sign-up-label'))}}
                        {{Form::password('password', array('class'=>'sign-up-textfield', 'required'))}}
                    </div>

                    {{Form::label('', '', array('class'=>'sign-up-label label-submit'))}}
                    {{Form::submit('SIGN IN & CHECKOUT', array('class'=>'sign-up-submit submit'))}}
                {{Form::close()}}
            </div>
            <div class="sign-in-left2">
                {{Form::open(array('url'=>URL::to('sign-in/signup'), 'method'=>'POST', 'files'=>true, 'class'=>'sign-up-form'))}}
                    @if(count($errors) != 0)
                        <div class="validation">
                            @foreach ($errors->all("<div class='validation-message error-message'>:message<div class='error-close-class'></div></div>") as $error)
                                {!!$error!!}
                            @endforeach
                        </div>
                    @endif

                    <div class="sign-up-list">
                        {{Form::label('email_sign_up', 'Email*', array('class'=>'sign-up-label'))}}
                        {{Form::email('email_sign_up', '', array('class'=>'sign-up-textfield', 'required'))}}
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
                        {{Form::text('date_of_birth', '', array('class'=>'sign-up-textfield datepicker'))}}
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
                        <span> Saya telah membaca dan menyetujui <a href="#">Terms and Conditions</a> </span>
                    </div>

                    <div class="sign-up-list">
                        {{Form::checkbox('newsletter', true, false, array('class'=>'sign-up-checkbox'))}}
                        <span> Daftar Newsletter </span>
                    </div>
                    <br>
                    <div>
                        {{Form::label('', '', array('class'=>'sign-up-label label-submit'))}}
                        {{Form::submit('SIGN UP & CHECKOUT', array('class'=>'sign-up-submit submit'))}}
                    </div>
                    {{Form::label('', '', array('class'=>'sign-up-label label-submit'))}}
                    {{Form::button('SIGN UP & CHECKOUT', array('class'=>'sign-up-submit button'))}}
                    
                {{Form::close()}}
            </div>
        </div><!--
     --><div class="sign-in-right">
            <h2>GUEST CHECKOUT</h2>
            <span>Anda dapat melakukan checkout tanpa perlu membuat account.</span>
            {{Form::open(array('url'=>URL::to('sign-in/checkout'), 'method'=>'POST', 'files'=>true, 'class'=>'sign-up-form guest-checkout'))}}
                    <div class="sign-up-list">
                        {{Form::label('email', 'Email', array('class'=>'sign-up-label'))}}
                        {{Form::email('email', '', array('class'=>'sign-up-textfield', 'required'))}}
                    </div>
                    {{Form::label('', '', array('class'=>'sign-up-label label-submit'))}}
                    {{Form::submit('CHECKOUT', array('class'=>'sign-up-submit submit'))}}
                {{Form::close()}}
        </div>
    </section>
@endsection