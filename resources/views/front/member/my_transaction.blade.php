@extends('front.template.master')

@section('title')
    Member My Transaction
@endsection

@section('head_additional')
    <script type="text/javascript">
        $(document).ready(function() {
            var cek = 0;
            $('.history-drop-down').click(function(){
                $('.history-drop-content').slideUp();

                if(cek == 0)
                {
                    $(this).parent().find('.history-drop-content').slideDown();
                    cek = 1;
                }
                else
                {
                    $(this).parent().find('.history-drop-content').slideUp();
                    cek = 0; 
                }
            });
        });
    </script>
@endsection

@section('content')
    <section class="profile history">
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
                <span class="active center">MY TRANSACTION</span>
            </a>
            <a href="{{URL::to('member/change-password')}}">
                <span>CHANGE PASSWORD</span>
            </a>
        </div>
        <div class="profile-content">
            <h1>Transaction History</h1>
            <table class="cart-table">
                <tr class='cart-table-header'>
                    <td>No</td>
                    <td>Date</td>
                    <td>Transaction ID</td>
                    <td>Amount to Pay</td>
                    <td>Status</td>
                    <td></td>
                </tr>
                <?php $no = 0; ?>
                @if(count($transactions) != 0)
                    @foreach($transactions as $transaction)
                        <?php 
                            $no++; 
                            $transaction_code = $transaction->no_nota;
                        ?>
                        <tr class='cart-table-item'>
                            <td>{{$no}}</td>
                            <td>{{date('d/m/Y', strtotime($transaction->created_at))}}</td>
                            <td>{{$transaction->no_nota}}</td>
                            <td>Rp {{rupiah3($transaction->amount_to_pay)}}</td>
                            @if($transaction->status == 'Waiting for payment')
                                <td style="color: orange;">{{$transaction->status}}</td>
                            @elseif($transaction->status == 'Paid')
                                <td style="color: green;">{{$transaction->status}}</td>
                            @elseif($transaction->status == 'Delivered')
                                <td style="color: blue;">{{$transaction->status}}</td>
                            @else
                                <td style="color: red;">{{$transaction->status}}</td>
                            @endif
                            <td style="padding-right: 0;">
                                <span class="history-drop-down">Action</span>
                                <div class="history-drop-content">
                                    <?php
                                        $convertnonota = str_replace('/', '-', $transaction->no_nota);
                                    ?>
                                    <a href="{{URL::to('member/my-transaction-detail/' . $convertnonota)}}"><span>Detail</span></a>
                                    @if($transaction->status == 'Waiting for payment')
                                        <a href="{{URL::to('payment-confirmation/pay/' . $convertnonota)}}"><span>Payment Confirmation</span></a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr class='cart-table-item'>
                        <td colspan="6" style="text-align: center;">
                            -- Belum ada transaksi --
                        </td>
                    </tr>
                @endif
            </table>
        </div>
    </section>
@endsection