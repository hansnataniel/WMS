<tr class="tr{{$ridetail->id}}" datari="{{$ridetail->ri_id}}" dataproduct="{{$product->id}}" dataquantity="{{$qty}}" dataprice="{{$price}}">
	<td>
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/product/' . $product->id)}}" style="color: blue;">
			{{$product->name}}
			{!!Form::hidden('ridetail[' . $ridetail->id . '][product]', $product->id)!!}
		</a>
	</td>
	<td style="text-align: right;">
		{{$qty}}
		{!!Form::hidden('ridetail[' . $ridetail->id . '][qty]', $qty)!!}
	</td>
	<td style="text-align: right;">
		{{number_format($price)}}
		{!!Form::hidden('ridetail[' . $ridetail->id . '][price]', $price)!!}
	</td>
	<td style="text-align: right;">
		{{number_format($price * $qty)}}
		{!!Form::hidden('ridetail[' . $ridetail->id . '][subtotal]', $price * $qty, ['class'=>'subtotal'])!!}
	</td>

	<td class="index-td-icon">
		{{-- <div class="edit-product-close close{{$product->id}}" data="{{$product->id}}"> --}}
			{{-- Delete --}}
		{{-- </div> --}}
		<div class="index-action-switch index-action-switch{{$ridetail->id}}">
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
				<li data="{{$ridetail->id}}">
					<a href="#" data="{{$ridetail->id}}" class="edit">
						{!!HTML::image('img/admin/index/edit_icon.png')!!}
						<span>
							Edit
						</span>
					</a>
				</li>
				<li data="{{$ridetail->id}}">
					<a href="#" data="{{$ridetail->id}}" class="delete">
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
		$('.index-action-switch'+{{$ridetail->id}}).click(function(e){
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

		$('.index-action-switch'+{{$ridetail->id}}+' .edit').click(function(e){
			e.preventDefault();

			var dataid = $(this).attr('data');
			// var dataid = $(this).attr('data');
			var datari = $('.tr'+dataid).attr('datari');
			var dataproduct = $('.tr'+dataid).attr('dataproduct');
			var dataquantity = $('.tr'+dataid).attr('dataquantity');
			var dataprice = $('.tr'+dataid).attr('dataprice');

			$('.pop-result').html($('.loading').html());
			$('.pop-container').fadeIn();

			$.ajax({
				type: "GET",
				url: "{{URL::to(Crypt::decrypt($setting->admin_url) . '/return/add')}}/"+datari+"/"+dataid+"/"+dataquantity+"/"+dataprice,
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

		$('.index-action-switch'+{{$ridetail->id}}+' .delete').click(function(e){
			e.preventDefault();

			$(this).parent().parent().parent().find('span').text('Loading...');
			var dataids = $(this).attr('data');

			$.ajax({
				type: "GET",
				url: "{{URL::to(Crypt::decrypt($setting->admin_url) . '/return/drop')}}/"+dataids,
				success:function(msg) {
					$('.tr'+dataids).remove();

					var gettotal = 0;

					$('.subtotal').each(function(){
						gettotal += parseInt($(this).val());
					});

					$('.total').attr('totaldata', gettotal);
					$('.total').text(number_format(gettotal));
				},
				error:function(msg) {
					$('body').html(msg.responseText);
				}
			});
		});

		var gettotal = 0;

		$('.subtotal').each(function(){
			gettotal += parseInt($(this).val());
		});

		$('.total').attr('totaldata', gettotal);
		$('.total').text(number_format(gettotal));
	});
</script>