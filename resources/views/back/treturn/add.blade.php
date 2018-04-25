<?php
	use App\Models\Transaction;
	use App\Models\Transactiondetail;
?>

<script>
	$(function(){
		$.ajax({
			type: "GET",
			url: "{{URL::to(Crypt::decrypt($setting->admin_url) . '/treturn/replace')}}/"+{{$transactiondetail->transaction_id}},
			success:function(msg) {
				$('.item'+{{$transactiondetail->id}}).show();
				$('.product-parent').html(msg);
			},
			error:function(msg) {
				$('body').html(msg.responseText);
			}
		});
	});
</script>

<div class="edit-product-item item{{$transactiondetail->id}}" style="display: none;">
	<div class="edit-product-td">
		<div class="edit-product-close close{{$transactiondetail->id}}" data="{{$transactiondetail->id}}">
			<span>Loading...</span>
		</div>
	</div>
	<div class="edit-product-td product-name">
		{{$transactiondetail->product->name}}
	</div>
	<div class="edit-product-td">
		{{Form::hidden('transactiondetail[' . $transactiondetail->id . '][bahan]', $transactiondetail->product_id)}}
		{!!Form::input('number', 'transactiondetail[' . $transactiondetail->id . '][qty]', null, ['class'=>'edit-product-text', 'placeholder'=>'Qty', 'required', 'min'=>'1'])!!}
	</div>
	<div class="edit-product-td" style="font-size: 0px; border-right: 0px; padding-right: 0px; margin-right: 0px;">
		<div class="edit-product-prepend">
			Rp
		</div>
		{!!Form::input('number', 'transactiondetail[' . $transactiondetail->id . '][price]', $transactiondetail->price, ['class'=>'edit-product-text edit-product-text1', 'placeholder'=>'Price', 'required', 'min'=>'0'])!!}
	</div>
</div>