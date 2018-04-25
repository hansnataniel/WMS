@extends('front.template.master')

@section('title')
   Payment Confirmation
@endsection

@section('head_additional')
    {{HTML::script('js/select2.js')}}
    {{HTML::style('css/select2.css')}}


    {!!HTML::style('css/jquery.datetimepicker.css')!!}
    {!!HTML::script('js/jquery.datetimepicker.js')!!}
    
    <script>
        $(function(){
            $('.datetimepicker').datetimepicker({
                timepicker: false,
                format: 'Y-m-d',
                maxDate: 0
            });

            $(".select").select2();
        });
    </script>

    <style type="text/css">
        .select2.select2-container {
            left: 0px !important;
            display: inline-block;
        }

        span.select2-selection.select2-selection--single {
            color: #5c5c5c;
            font-size: 14px;
        }

        span.select2-dropdown.select2-dropdown--above, span.select2-dropdown.select2-dropdown--below {
            left: 0px !important;
        }

        span.select2-selection__rendered {
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

        span.selection {
            text-align: left;
        }
    </style>
@endsection

@section('content')
    <section class="checkout">
        @if (Session::has('success-message'))
            <div class='validation-message success-message'><div class="success-message-border">{{Session::get('success-message')}}</div></div>
        @endif
        <div class="checkout-payment-confirmation">
            <h2>PAYMENT CONFIRMATION</h2>
            @if(count($errors) != 0)
                <br><br>
                <div class="validation">
                    @foreach ($errors->all("<div class='validation-message error-message'>:message<div class='error-close-class'></div></div>") as $error)
                        {{$error}}
                    @endforeach
                </div>
            @endif
            {{Form::open(array('url'=>URL::to('payment-confirmation'), 'method'=>'POST', 'files'=>TRUE, 'class'=>'checkout-payment-form'))}}

                {{Form::label('transaction_id', 'Transaction ID', array('class'=>'checkout-payment-label'))}}
                {{Form::text('transaction_id', $transaction_code, array('class'=>'checkout-payment-textfield', 'required'))}}

                {{Form::label('amount_transfered', 'Amount Transfered (Rp)', array('class'=>'checkout-payment-label'))}}
                {{Form::input('number','amount_transfered', '', array('class'=>'checkout-payment-textfield', 'required'))}}

                {{Form::label('transfer_to', 'Transfer To', array('class'=>'checkout-payment-label'))}}
                {{Form::select('transfer_to', $bank_options, '', array('class'=>'checkout-payment-textfield select', 'required'))}}

                {{Form::label('your_bank', 'Your Bank', array('class'=>'checkout-payment-label'))}}
                {{Form::text('your_bank', '', array('class'=>'checkout-payment-textfield', 'required'))}}

                {{Form::label('your_account_number', 'Your Account Number', array('class'=>'checkout-payment-label'))}}
                {{Form::text('your_account_number', '', array('class'=>'checkout-payment-textfield', 'required'))}}

                {{Form::label('your_account_name', 'Your Account Name', array('class'=>'checkout-payment-label'))}}
                {{Form::text('your_account_name', '', array('class'=>'checkout-payment-textfield', 'required'))}}

                {{Form::label('transfer_date', 'Transfer Date', array('class'=>'checkout-payment-label'))}}
                {{Form::text('transfer_date', '', array('class'=>'checkout-payment-textfield datetimepicker', 'readonly', 'required'))}}

                {{Form::submit('CONFIRM', array('class'=>'checkout-payment-submit'))}}
            {{Form::close()}}
        </div>
    </section>
@endsection