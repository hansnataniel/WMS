<?php
	use App\Models\Ridetail;
	use App\Models\Productstock;
?>

@foreach($podetails as $podetail)
	<?php
		$ridetails = Ridetail::where('podetail_id', '=', $podetail->id)->get();
		$gettotalri = 0;
		foreach ($ridetails as $ridetail) {
			$gettotalri = $gettotalri + $ridetail->qty;
		}

		$getmax = $podetail->qty - $gettotalri;

		$productstocks = Productstock::where('product_id', '=', $podetail->product_id)->where('is_active', '=', true)->get();

		$rak_options = array();
		$rak_options[''] = "Select Rak";
		foreach($productstocks as $productstock) {
			$rak_options[$productstock->rak_id] = $productstock->rak->name;
		}
	?>
	<tr>
		<td>
			{{$podetail->product->name}}
		</td>
		<td>
			{!!Form::hidden('podetail[' . $podetail->id . '][product]', 0)!!}
			{!!Form::hidden('podetail[' . $podetail->id . '][bahan]', $podetail->product_id)!!}
			
			{!!Form::hidden('podetail[' . $podetail->id . '][max]', $getmax)!!}
			{!!Form::input('number', 'podetail[' . $podetail->id . '][qty]', null, array('class'=>'edit-product-text', 'placeholder'=>'Qty', 'required', 'min'=>0, 'max'=>$getmax))!!}
		</td>
		<td>
			{!! Form::select('podetail[' . $podetail->id . '][rak]', $rak_options, null, ['class'=>'edit-product-text select']) !!}

			<script type="text/javascript">
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
			{{$podetail->qty}}
		</td>
		<td>
			{{$gettotalri}}
		</td>
		<td>
			{{$getmax}}
		</td>
	</tr>
@endforeach