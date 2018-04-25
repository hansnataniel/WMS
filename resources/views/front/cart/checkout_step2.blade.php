<?php
    use App\Models\Product;
    use App\Models\Productphoto;
    use App\Models\Rate;
    use App\Models\Service;
    use App\Models\Expedition;
    use App\Models\Voucher;
?>

@extends('front.template.master')

@section('title')
    Checkout | Payment
@endsection

@section('head_additional')
    
@endsection

@section('content')
    <section class="checkout">
        @if (Session::has('success-message'))
            <div class='validation-message success-message'><div class="success-message-border">{{Session::get('success-message')}}</div></div>
        @endif
        <div class="checkout-header">
            <h1>CHECKOUT</h1>
            <span>STEP 1: VOUCHER &amp; DELIVERY</span>
            <span class="checkout-active">STEP 2: PAYMENT</span>
            <span>STEP 3: PAYMENT CONFIRMATION</span>
        </div>
        <p>
            <br>
            <span class="checkout-step2-ket"> 
                Terima kasih telah berbelanja di REMAX INDONESIA.<br>
                Setelah melakukan pembayaran melalui Bank Transfer, segera lakukan
                konfirmasi pembayaran agar kami dapat memproses pesanan Anda secepatnya
            </span>
        </p>

        <div class="chekout-step2-left">
            <p>
                <span>Data transaksi dan Pengiriman Anda:</span>
            </p>
            <table class="checkout-table">
                <tr class="checkout-table-index">
                    <td>Date</td>
                    <td>Transaction ID</td>
                    <td>Amout to Pay</td>
                </tr>
                <tr class="checkout-table-item">
                    <td>{{date('d F Y', strtotime($transaction->created_at))}}</td>
                    <td>{{$transaction->no_nota}}</td>
                    <td>Rp {{rupiah3($transaction->amount_to_pay)}}</td>
                </tr>
                <tr class="checkout-table-index">
                    <td colspan="3">Order Summary</td>
                </tr>
                <tr class="checkout-table-item">
                    <td colspan="3">
                        @foreach($transactionitems as $transactionitem)
                            <?php $product = Product::find($transactionitem->product_id); ?>
                            
                            <div>{{$product->name}}</div>
                            <div class="chekout-step2-price">Price: Rp {{rupiah3($transactionitem->price_afterdiscount)}} . Quantity: {{$transactionitem->qty}}</div>
                        @endforeach

                        @if($transaction->voucher != null)
                            <?php $voucher = Voucher::where('code', '=', $transaction->voucher)->first(); ?>
                            <div>VOUCHER</div>
                            <div class="chekout-step2-price">{{$transaction->voucher}} - 
                                @if($voucher->type != false)
                                    Rp {{rupiah3($voucher->value)}}
                                @else
                                    {{rupiah3($voucher->value)}}% (Rp {{rupiah3($transaction->total * $voucher->value / 100)}})
                                @endif
                            </div>

                        @endif
                        @if($transaction->rate_price != 0)
                            <div>DELIVERY COST</div>
                            <div class="chekout-step2-price" style="margin-bottom: 0px;">Rp {{rupiah3($transaction->delivery_cost)}}</div>
                        @endif
                    </td>
                </tr>
                <tr class="checkout-table-index">
                    <td colspan="3">Delivery</td>
                </tr>
                <tr class="checkout-table-item no-bottom">
                    <td>Nama Pengirim</td>
                    <td colspan="2">: <span>{{$transaction->sender}}</span></td>
                </tr>
                <tr class="checkout-table-item no-bottom">
                    <td>Alamat Pengirim</td>
                    <td colspan="2">: <span>{{$transaction->sender_address}}</span></td>
                </tr>
                <tr class="checkout-table-item no-bottom">
                    <td>Nama Penerima</td>
                    <td colspan="2">: <span>{{$transaction->name}}</span></td>
                </tr>
                <tr class="checkout-table-item no-bottom">
                    <td>Alamat Penerima</td>
                    <td colspan="2">: <span>{{$transaction->address}}</span></td>
                </tr>
                <tr class="checkout-table-item no-bottom">
                    <td>Layanan Pengiriman</td>
                    <?php 
                        $rate = Rate::find($transaction->rate_id);
                        $service = Service::find($rate->service_id);
                        $expedition = Expedition::find($service->expedition_id);
                    ?>
                    <td colspan="2">: <span>{{$expedition->name}} ({{$service->name}})</span></td>
                </tr>
                <tr class="checkout-table-item yes-bottom">
                    <td>Pesan</td>
                    <td colspan="2">: <span>{{$transaction->message}}</span></td>
                </tr>
            </table>
        </div><!--
     --><div class="chekout-step2-right">
            <p>
                <span>Pembayaran dapat Anda lakukan secara transfer ke rekening bank kami:</span>
            </p>

            <table class="checkout-table">
                <tr class="checkout-table-index">
                    <td>Bank</td>
                    <td>Account Name</td>
                    <td>Account Number</td>
                </tr>
                @if(count($banks) != 0)
                    @foreach($banks as $bank)
                        <tr class="checkout-table-item">
                            <td>{{$bank->name}}</td>
                            <td>{{$bank->account_name}}</td>
                            <td>{{$bank->account_number}}</td>
                        </tr>
                    @endforeach
                @endif
            </table>

            <p>
                <h2>PLEASE NOTE THE FOLLOWING PAYMENT RESTRICTION:</h2>
                <ol>
                    <li>Kami harus menerima konfirmasi pembayaran Anda paling lambat 24 jam setelah pesanan dilakukan.</li>
                    <li>Setelah pesanan di terima, kami akan menyimpan stok barang tersebut hanya dalam waktu 24 jam.</li>
                    <li>Stok barang yang Anda pesan pasti terjamin, hanya setelah Anda melakukan konfirmasi pembayaran.</li>
                    <li>Setelah melakukan pembayaran, pastikan Anda melakukan konfirmasi pembayaran segera, agar kami dapat memproses secepatnya pengiriman Anda.</li>
                    <li>Untuk melakukan konfirmasi pembayaran, tekan tombol Payment Konfirmation di bawah, atau masuk ke halaman Payment Confirmation di footer website kami, atau Anda dapat melakukannya di halaman My Transaction setelah sign in.</li>
                </ol>
            </p>
        </div>

        <a href="{{URL::to('checkout/step3/' . $transaction_code)}}" style="display: table; margin: 0 auto;">{{HTML::image('img/front/payment-confirmation.jpg', '', array('class'=>'checkout-payment'))}}</a>
    </section>
@endsection