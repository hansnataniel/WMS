@extends('front.template.master')

@section('title')
    Contact Us
@endsection

@section('head_additional')
    
@endsection

@section('content')
    <section class="sign-in contact">
        @if (Session::has('success-message'))
             <div class='validation-message success-message'>
                <div class="success-message-border">
                    {{Session::get('success-message')}}
                </div>
            </div>
        @endif

        <h1>CONTACT US</h1>

        <div class="contact-content">
            <p>
                Anda dapat menghubungi kami pada jam operasional, yaitu setiap hari Senin - Jumat, jam 09:00 - 17:00 WIB, dan Sabtu jam 09:00 - 16:00 WIB, di:
            </p>

            <div class="contact-list">
                @if($setting->address != null)
                    <div class="contact-address">
                        <span class="left">Address</span>
                        <span>:</span>
                        <span class="right">{{$setting->address}}</span>
                    </div>
                @endif
                @if($setting->email != null)
                    <div class="contact-address">
                        <span class="left">Email</span>
                        <span>:</span>
                        <span class="right">{{$setting->email}}</span>
                    </div>
                @endif
                @if($setting->phone != null)
                    <div class="contact-address">
                        <span class="left">Phone</span>
                        <span>:</span>
                        <span class="right">{{$setting->phone}}</span>
                    </div>
                @endif
                @if($setting->fax != null)
                    <div class="contact-address">
                        <span class="left">Fax</span>
                        <span>:</span>
                        <span class="right">{{$setting->fax}}</span>
                    </div>
                @endif
                @if($setting->bbm != null)
                    <div class="contact-address">
                        <span class="left">BBM</span>
                        <span>:</span>
                        <span class="right">{{$setting->bbm}}</span>
                    </div>
                @endif
            </div>
            <div class="contact-list">
                @if($setting->whatsapp != null)
                    <div class="contact-address">
                        <span class="left">Whatsapp</span>
                        <span>:</span>
                        <span class="right">{{$setting->whatsapp}}</span>
                    </div>
                @endif
                @if($setting->line != null)
                    <div class="contact-address">
                        <span class="left">Line</span>
                        <span>:</span>
                        <span class="right">{{$setting->line}}</span>
                    </div>
                @endif
            </div>

            {{Form::open(array('url'=>URL::current(), 'method'=>'POST', 'class'=>'contact-form'))}}
                <span>Atau hubungi secara langsung customer servce kami melalui form email ini</span>

                @if(count($errors) != 0)
                    <div class="validation">
                        @foreach ($errors->all("<div class='validation-message error-message'>:message<div class='error-close-class'></div></div>") as $error)
                            {{$error}}
                        @endforeach
                    </div>
                @endif

                <div class="sign-up-list">
                    {{Form::label('name', 'Name*', array('class'=>'sign-up-label'))}}
                    {{Form::text('name', '', array('class'=>'sign-up-textfield', 'required'))}}
                </div>

                <div class="sign-up-list">
                    {{Form::label('email', 'Email*', array('class'=>'sign-up-label'))}}
                    {{Form::email('email', '', array('class'=>'sign-up-textfield', 'required'))}}
                </div>
                
                <div class="sign-up-list">
                    {{Form::label('phone', 'Phone', array('class'=>'sign-up-label'))}}
                    {{Form::text('phone', '', array('class'=>'sign-up-textfield'))}}
                </div>

                <div class="sign-up-list">
                    {{Form::label('whatsapp', 'Whatsapp', array('class'=>'sign-up-label'))}}
                    {{Form::text('whatsapp', '', array('class'=>'sign-up-textfield'))}}
                </div>

                <div class="sign-up-list">
                    {{Form::label('line', 'Line', array('class'=>'sign-up-label'))}}
                    {{Form::text('line', '', array('class'=>'sign-up-textfield'))}}
                </div>

                <div class="sign-up-list">
                    {{Form::label('bbm', 'Bbm', array('class'=>'sign-up-label'))}}
                    {{Form::text('bbm', '', array('class'=>'sign-up-textfield'))}}
                </div>

                <div class="sign-up-list">
                    {{Form::label('subject', 'Subject', array('class'=>'sign-up-label'))}}
                    {{Form::text('subject', '', array('class'=>'sign-up-textfield'))}}
                </div>

                <div class="sign-up-list">
                    {{Form::label('message', 'Message*', array('class'=>'sign-up-label'))}}
                    {{Form::textarea('message', '', array('class'=>'sign-up-textfield area', 'required'))}}
                </div>

                {{Form::submit('SEND', array('class'=>'contact-submit'))}}
            {{Form::close()}}
        </div>
    </section>
@endsection