<?php
	use Illuminate\Support\Str;
	use App\Models\Productstock;

	$productstocks = Productstock::where('product_id', '=', $product->id)->where('is_active', '=', true)->get();

	$rak_options = array();
	$rak_options[''] = "Select Rak";
	foreach($productstocks as $productstock) {
		$rak_options[$productstock->rak_id] = $productstock->rak->name;
	}
?>

<tr>
	<td>
		{{$product->name}}
		{!!Form::hidden('freedetail[' . $product->id . '][product]', $product->id)!!}
	</td>
	<td>
		{!!Form::input('number', 'freedetail[' . $product->id . '][qty]', null, ['class'=>'edit-product-text', 'placeholder'=>'Qty', 'required', 'min'=>'1'])!!}

		<script>
			$(function(){
				$.ajax({
					type: "GET",
					{{-- url: "{{URL::to(Crypt::decrypt($setting->admin_url) . '/po/replace')}}", --}}
					url: "{{URL::to(Crypt::decrypt($setting->admin_url) . '/ri/replace')}}",
					success:function(msg) {
						$('.close'+{{$product->id}}).parent().parent().show();
						$('.product-parent').html(msg);
					},
					error:function(msg) {
						$('body').html(msg.responseText);
						// alert('done');
					}
				});
			});
		</script>
	</td>
	<td>
		{!! Form::select('freedetail[' . $product->id . '][rak]', $rak_options, null, ['class'=>'edit-product-text select']) !!}

		<script>
			$(function(){
				$('.select').each(function(){
					var data = $(this).attr('placeholder-data');

					$(this).select2({
						placeholder: data
					});
				});
			});
		</script>
	</td>
	<td>
		<div class="edit-product-close close{{$product->id}}" data="{{$product->id}}">
			Delete
		</div>
	</td>
</tr>