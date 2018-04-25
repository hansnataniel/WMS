<?php
	use App\Models\Invoice;
	use App\Models\Invoicedetail;
?>

<script>
	$(function(){
		$.ajax({
			type: "GET",
			url: "{{URL::to(Crypt::decrypt($setting->admin_url) . '/return/replace')}}/"+{{$ridetail->ri_id}},
			success:function(msg) {
				$('.item'+{{$ridetail->id}}).show();
				$('.product-parent').html(msg);
			},
			error:function(msg) {
				$('body').html(msg.responseText);
			}
		});
	});
</script>

@if($ridetail->product_id == 0)
	<div class="edit-product-item item{{$ridetail->id}}" style="display: none;">
		<div class="edit-product-td">
			<div class="edit-product-close close{{$ridetail->id}}" data="{{$ridetail->id}}">
				<span>Loading...</span>
			</div>
		</div>
		<div class="edit-product-td product-name">
			{{$ridetail->podetail->product->name}}
		</div>
		<div class="edit-product-td">
			{{Form::hidden('ridetail[' . $ridetail->id . '][bahan]', $ridetail->podetail->product_id)}}
			{!!Form::input('number', 'ridetail[' . $ridetail->id . '][qty]', null, ['class'=>'edit-product-text', 'placeholder'=>'Qty', 'required', 'min'=>'1'])!!}
		</div>
		<div class="edit-product-td" style="font-size: 0px; border-right: 0px; padding-right: 0px; margin-right: 0px;">
			<div class="edit-product-prepend">
				Rp
			</div>
			<?php
				$invoice = Invoicedetail::where('ridetail_id', '=', $ridetail->id)->first();
			?>
			@if($invoice != null)
				{!!Form::input('number', 'ridetail[' . $ridetail->id . '][price]', $invoice->price, ['class'=>'edit-product-text edit-product-text1', 'placeholder'=>'Price', 'required', 'min'=>'0'])!!}
			@else
				{!!Form::input('number', 'ridetail[' . $ridetail->id . '][price]', $ridetail->podetail->price, ['class'=>'edit-product-text edit-product-text1', 'placeholder'=>'Price', 'required', 'min'=>'0'])!!}
			@endif
		</div>
	</div>
@else
	<div class="edit-product-item item{{$ridetail->id}}" style="display: none;">
		<div class="edit-product-td">
			<div class="edit-product-close close{{$ridetail->id}}" data="{{$ridetail->id}}">
				<span>Loading...</span>
			</div>
		</div>
		<div class="edit-product-td product-name">
			{{$ridetail->product->name}}
		</div>
		<div class="edit-product-td">
			{{Form::hidden('ridetail[' . $ridetail->id . '][bahan]', $ridetail->product_id)}}
			{!!Form::input('number', 'product[' . $ridetail->id . '][qty]', null, ['class'=>'edit-product-text', 'placeholder'=>'Qty', 'required', 'min'=>'1'])!!}
		</div>
		<div class="edit-product-td" style="font-size: 0px; border-right: 0px; padding-right: 0px; margin-right: 0px;">
			<div class="edit-product-prepend">
				Rp
			</div>
			{!!Form::input('number', 'product[' . $ridetail->id . '][price]', '0', ['class'=>'edit-product-text edit-product-text1', 'placeholder'=>'Price', 'required', 'min'=>'0'])!!}
		</div>
	</div>
@endif