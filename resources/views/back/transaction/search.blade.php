<tr class="tr{{$product->id}}" dataproduct="{{$product->id}}" datarak="{{$rak->id}}" dataquantity="{{$qty}}" dataprice="{{$price}}" datadiscounttype="{{$discounttype}}" datadiscount="{{$discount}}">
	<td>
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/product/' . $product->id)}}" style="color: blue;">
			{{$product->name}}
			{!!Form::hidden('product[' . $product->id . '][product]', $product->id)!!}
		</a>
	</td>
	<td>
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/rak/' . $rak->id)}}" style="color: blue;">
			{{$rak->name}}
			{!!Form::hidden('product[' . $product->id . '][rak]', $rak->id)!!}
		</a>
	</td>
	<td style="text-align: right;">
		{{$qty}}
		{!!Form::hidden('product[' . $product->id . '][qty]', $qty)!!}
	</td>
	<td style="text-align: right;">
		{{number_format($price)}}
		{!!Form::hidden('product[' . $product->id . '][price]', $price)!!}
		{!!Form::hidden('subprice', $subprice, ['class'=>'price'])!!}
	</td>
	<td style="text-align: right;">
		@if($discounttype == '0')
			Rp {{number_format($discount)}}
			{!!Form::hidden('product[' . $product->id . '][discounttype]', '0')!!}
		@else
			{{$discount}} %
			{!!Form::hidden('product[' . $product->id . '][discounttype]', '1')!!}
		@endif
		{!!Form::hidden('product[' . $product->id . '][discount]', $discount)!!}
	</td>
	<td style="text-align: right;">
		{{number_format($subtotal)}}
		{!!Form::hidden('subtotal', $subtotal, ['class'=>'subtotal'])!!}
	</td>

	<td class="index-td-icon">
		{{-- <div class="edit-product-close close{{$product->id}}" data="{{$product->id}}"> --}}
			{{-- Delete --}}
		{{-- </div> --}}
		<div class="index-action-switch index-action-switch{{$product->id}}">
			{{-- 
				Switch of ACTION
			 --}}
			<span>
				Action
			</span>
			<div class="index-action-arrow"></div>

			{{-- 
				List of ACTION
			 --}}
			<ul class="index-action-child-container" style="width: 110px">
				<li data="{{$product->id}}">
					<a href="#" data="{{$product->id}}" class="edit">
						{!!HTML::image('img/admin/index/edit_icon.png')!!}
						<span>
							Edit
						</span>
					</a>
				</li>
				<li data="{{$product->id}}">
					<a href="#" data="{{$product->id}}" class="delete">
						{!!HTML::image('img/admin/index/trash_icon.png')!!}
						<span>
							Delete
						</span>
					</a>
				</li>
			</ul>
		</div>
	</td>
</tr>

<script type="text/javascript">
	$(function(){
		$('.index-action-switch'+{{$product->id}}).click(function(e){
			e.stopPropagation();
			
			if($(this).hasClass('active'))
			{
				indexSwitchOff();
			}
			else
			{
				indexSwitchOff();

				$(this).addClass('active');
				$(this).find($('.index-action-child-container')).fadeIn();

				$(this).find($('li')).each(function(e){
					$(this).delay(50*e).animate({
	                    opacity: 1,
	                    top: 0
	                }, 300);
				});
			}
		});

		$('.index-action-switch'+{{$product->id}}+' .edit').click(function(e){
			e.preventDefault();

			var dataid = $(this).attr('data');
			var dataproduct = $('.tr'+dataid).attr('dataproduct');
			var datarak = $('.tr'+dataid).attr('datarak');
			var dataquantity = $('.tr'+dataid).attr('dataquantity');
			var dataprice = $('.tr'+dataid).attr('dataprice');
			var datadiscounttype = $('.tr'+dataid).attr('datadiscounttype');
			var datadiscount = $('.tr'+dataid).attr('datadiscount');

			$('.pop-result').html($('.loading').html());
			$('.pop-container').fadeIn();

			$.ajax({
				type: "GET",
				url: "{{URL::to(Crypt::decrypt($setting->admin_url) . '/transaction/add')}}/"+dataproduct+"/"+datarak+"/"+dataquantity+"/"+dataprice+"/"+datadiscounttype+"/"+datadiscount,
				success:function(msg) {
					$('.pop-result').html(msg);

					$('.pop-container').find('.index-del-item').each(function(e){
						$(this).delay(70*e).animate({
		                    opacity: 1,
		                    top: 0
		                }, 300);
					});
				},
				error:function(msg) {
					$('body').html(msg.responseText);
				}
			});
		});

		$('.index-action-switch'+{{$product->id}}+' .delete').click(function(e){
			e.preventDefault();

			$(this).parent().parent().parent().find('span').text('Loading...');
			var dataids = $(this).attr('data');

			$.ajax({
				type: "GET",
				url: "{{URL::to(Crypt::decrypt($setting->admin_url) . '/transaction/drop')}}/"+dataids,
				success:function(msg) {
					$('.tr'+dataids).remove();
				},
				error:function(msg) {
					$('body').html(msg.responseText);
				}
			});
		});
	});
</script>