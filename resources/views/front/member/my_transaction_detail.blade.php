<?php
    use Illuminate\Support\Str;

    use App\Models\Product;
    use App\Models\Productphoto;
    use App\Models\Voucher;
?>

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
    <section class="profile history history-detail">
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
            <a href="{{URL::to('member/my-transaction')}}">{{HTML::image('img/front/back.jpg', '', array('class'=>'history-back'))}}</a>

            <div class="history-ket-right">
                Transaction Date: {{date('d/m/Y', strtotime($transaction->created_at))}}<br>
                Transaction ID: {{$transaction->no_nota}}
            </div>

            <table class="cart-table">
                <tr class='cart-table-header'>
                    <td colspan="2">Product</td>
                    <td style="text-align: right;">Price Each (Rp)</td>
                    <td style="text-align: center;">Disc.</td>
                    {{-- <td style="text-align: right;">Qty Discount (Rp)</td> --}}
                    <td style="text-align: right;">Qty</td>
                    <td style="text-align: right;">Subtotal (Rp)</td>
                </tr>
                @foreach($transactionitems as $transactionitem)
                    <?php
                        $product = Product::find($transactionitem->product_id);
                        $productphoto = Productphoto::where('product_id', '=', $transactionitem->product_id)->where('default', '=', 1)->first();
                        $disc = ($transactionitem->price - $transactionitem->price_afterdiscount) / $transactionitem->price * 100;
                    ?>
                    <tr class='cart-table-item'>
                        <td style="max-width: 85px; min-width: 80px;">
                            @if($productphoto != null)
                                {{HTML::image('usr/img/product/small/' . $productphoto->gambar, '', array('class'=>'cart-img'))}}
                            @else
                                {!!HTML::image('img/front/no-image.jpg', '', ['class'=>'cart-img'])!!}
                            @endif
                        </td>
                        <td>
                            <span>{{$product->name}}</span>
                        </td>
                        <td style="text-align: right;"><span style="text-align: right;">{{rupiah3($transactionitem->price)}}</span></td>
                        <td style="text-align: right;"><span style="text-align: center;">{{$disc . '%'}}</span></td>
                        <td style="text-align: right;">
                            <span style="text-align: right;">{{$transactionitem->qty}}</span>
                        </td>
                        {{-- <td style="text-align: right;"><span>{{rupiah3($transactionitem->quantity_discount)}}</span></td> --}}
                        <td style="text-align: right;"><span style="text-align: right;">{{rupiah3($transactionitem->price_afterdiscount * $transactionitem->qty)}}</span></td>
                    </tr>
                @endforeach

                <tr class='cart-table-total'>
                    <td colspan="6" style="padding: 0;"><div class="cart-line"></div></td>
                </tr>
                <tr class='cart-table-total'>
                    <td style="padding-left: 0;"></td>
                    <td colspan="3" style="text-align: right;">Total (Rp) :</td>
                    <td colspan="2" style="text-align: right; font-weight: bold;">{{rupiah3($transaction->total)}}</td>
                </tr>
                <tr class='cart-table-total2'>
                    <td style="padding-left: 0;"></td>
                    <td colspan="3" style="text-align: right;">Voucher (Rp) :</td>
                    <td colspan="2" style="text-align: right; font-weight: bold;">
                        <?php $voucher = Voucher::where('code', '=', $transaction->voucher)->first(); ?>
                        @if($voucher != null)
                            @if($voucher->type == false)
                                Rp {{rupiah3($voucher->value)}}
                            @else
                                {{rupiah3($voucher->value)}}% (Rp {{rupiah3($transaction->total * $voucher->value / 100)}})
                            @endif
                        @else
                            {{rupiah3(0)}}
                        @endif
                    </td>
                </tr>
                <tr class='cart-table-total2'>
                    <td style="padding-left: 0;"></td>
                    <td colspan="3" style="text-align: right;">Delivery Cost (Rp) :</td>
                    <td colspan="2" style="text-align: right; font-weight: bold;">{{rupiah3($transaction->rate_price)}}</td>
                </tr>
                <tr class='cart-table-total2'>
                    <td style="padding-left: 0;"></td>
                    <td colspan="3" style="text-align: right; border-top: solid 1px #dbdbdb">Total Payment (Rp) :</td>
                    <td colspan="2" style="text-align: right; font-size: 24px; font-weight: bold; border-top: solid 1px #dbdbdb">{{rupiah3($transaction->amount_to_pay)}}</td>
                </tr>
            </table>
        </div>
    </section>
@endsection