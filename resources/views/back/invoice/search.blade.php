<?php
	use App\Models\Ridetail;
?>

<script>
	$(function(){
		if({{$ri->po_id}} != 0)
		{
			var dataLink = "{{URL::to(Crypt::decrypt($setting->admin_url) . '/invoice/replace')}}/"+{{$ri->po->supplier_id}};
		}
		else
		{
			var dataLink = "{{URL::to(Crypt::decrypt($setting->admin_url) . '/invoice/replace')}}/"+{{$ri->supplier_id}};
		}
		
		$.ajax({
			type: "GET",
			url: dataLink,
			success:function(msg) {
				$('.group'+{{$ri->id}}).show();
				$('.ri-parent').html(msg);
			},
			error:function(msg) {
				$('body').html(msg.responseText);
			}
		});
	});
</script>

<div class="edit-form-text product-container group{{$ri->id}}" style="margin-bottom: 80px; margin-left: 160px; max-width: 600px;">
	<span style="position: absolute; font-size: 18px; left: 0px; top: -30px; display: block; width: 100%;">
		Recieve Item : <a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/ri/' . $ri->id)}}" style="color: blue;">{{$ri->no_nota}}</a>
		@if($ri->po_id != 0)
			<span class="edit-product-close" style="position: absolute; right: -1px; top: -5px; cursor: pointer; padding: 5px 10px; font-size: 14px; color: #fff; background: red;" supplier="{{$ri->po->supplier_id}}" data="{{$ri->id}}">
		@else
			<span class="edit-product-close" style="position: absolute; right: -1px; top: -5px; cursor: pointer; padding: 5px 10px; font-size: 14px; color: #fff; background: red;" supplier="{{$ri->supplier_id}}" data="{{$ri->id}}">
		@endif
			Delete
		</span>
	</span>
	<table>
		<tr class="tr-title">
			<th>
				Product
			</th>
			<th>
				Qty
			</th>
			<th>
				Price
			</th>
		</tr>

		@if($ri->po_id != 0)
			@foreach($ridetails as $ridetail)
				<tr>
					<td>
						<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/product/' . $ridetail->podetail->product_id)}}" style="color: blue;">
							{{$ridetail->podetail->product->name}}
							{!!Form::hidden('ridetail[' . $ridetail->id . '][product]', $ridetail->podetail->product_id)!!}
						</a>
					</td>
					<td>
						{{$ridetail->qty}}
						{!!Form::hidden('ridetail[' . $ridetail->id . '][qty]', $ridetail->qty)!!}
					</td>
					<td>
						{!!Form::input('number', 'ridetail[' . $ridetail->id . '][price]', null, array('class'=>'edit-product-text', 'placeholder'=>'Price', 'required', 'min'=>'0'))!!}
					</td>
				</tr>
			@endforeach
		@endif
		<?php
			$frees = Ridetail::where('ri_id', '=', $ri->id)->where('product_id', '!=', 0)->get();
		?>
		@if(!$frees->isEmpty())
			<tr>
				<td colspan="3" style="font-weight: bold; font-size: 16px; border-top: 1px solid #0d0f3b; border-bottom: 1px solid #0d0f3b;">
					Free Product
				</td>
			</tr>
			@foreach($frees as $free)
				<tr>
					<td>
						<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/product/' . $free->product_id)}}" style="color: blue;">
							{{$free->product->name}}
							{!!Form::hidden('ridetail[' . $free->id . '][product]', $free->product_id)!!}
						</a>
					</td>
					<td>
						{{$free->qty}}
						{!!Form::hidden('ridetail[' . $free->id . '][qty]', $free->qty)!!}
					</td>
					<td>
						Rp 0
						{!!Form::hidden('ridetail[' . $free->id . '][price]', 0)!!}
					</td>
				</tr>
			@endforeach
		@endif
	</table>
</div>