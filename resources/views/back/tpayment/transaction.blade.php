<?php
	use Illuminate\Support\Str;

	use App\Models\Setting;
	use App\Models\Ri;
	use App\Models\Transaction;
	use App\Models\Transactiondetail;
?>

<script>
	$(function(){
		$('.datetimepicker').datetimepicker({
			timepicker: false,
			format: 'Y-m-d',
			minDate: "{{date('Y/m/d', strtotime($transaction->date))}}",
			maxDate: 0
		});
	});
</script>

<?php
	$setting = Setting::first();
?>

<div class="edit-form-group">
	{!!Form::label('date', 'Date', ['class'=>'edit-form-label'])!!}
	{!!Form::text('date', null, ['class'=>'edit-form-text datetimepicker large', 'readonly', 'required', 'placeholder'=>'Select Transaction First'])!!}
</div>
<div class="edit-form-group nota-result">
	<div class="edit-form-label"></div>
	<div class="edit-form-text">
		<span class="transaction-nota">
			Transaction ID : 
			<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/transaction/view/' . $transaction->id)}}" style="text-decoration: none;" target="_blank">
				{{$transaction->trans_id}}
			</a>
		</span>
		<table class="index-table">
			<tr class="index-tr-title">
				<td>
					#
				</td>
				<td>
					Product
				</td>
				<td>
					Rak
				</td>
				<td>
					Qty
				</td>
				<td>
					Price
				</td>
				<td>
					Discount
				</td>
				<td>
					Sub Total
				</td>
			</tr>

			<?php
				$transactiondetails = Transactiondetail::where('transaction_id', '=', $transaction->id)->get();
				$total = 0;
				$counter = 0;
			?>
			@foreach($transactiondetails as $transactiondetail)
				<?php
					$counter++; 
					if($transactiondetail->discounttype == 0)
					{
						$total += ($transactiondetail->price * $transactiondetail->qty) - $transactiondetail->discount;
					}
					else
					{
						$total += ($transactiondetail->price * $transactiondetail->qty) - ((($transactiondetail->price * $transactiondetail->qty) * $transactiondetail->discount) / 100);
					}
				?>
				<tr>
					<td>
						{{$counter}}
					</td>
					<td>
						<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/product/view/' . $transaction->product_id)}}" style="color: blue;" target="_blank">
							{{$transactiondetail->product->name}}
						</a>
					</td>
					<td>
						<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/rak/view/' . $transaction->rak_id)}}" style="color: blue;" target="_blank">
							{{$transactiondetail->rak->name}}
						</a>
					</td>
					<td>
						{{$transactiondetail->qty}}
					</td>
					<td style="text-align: right;">
						Rp {{number_format($transactiondetail->price)}}
					</td>
					<td>
						@if($transactiondetail->discounttype == 0)
							Rp {{number_format($transactiondetail->discount)}}
						@else
							{{$transactiondetail->discount}} %
						@endif
					</td>
					<td>
						@if($transactiondetail->discounttype == 0)
							Rp {{number_format(($transactiondetail->price * $transactiondetail->qty) - $transactiondetail->discount)}}
						@else
							Rp {{number_format(($transactiondetail->price * $transactiondetail->qty) - ((($transactiondetail->price * $transactiondetail->qty) * $transactiondetail->discount) / 100))}}
						@endif
					</td>
				</tr>
				<?php
					$transactiondetailids = array();
				?>
			@endforeach
			<tr class="transaction-total">
				<td colspan="6" style="text-align: right; font-size: 18px;">
					Discount
				</td>
				<td style="font-size: 18px;">
					<?php
						if($transaction->discounttype == 0)
						{
							$total = $total - $transaction->discount;
						}
						else
						{
							$total = $total - (($total * $transaction->discount) / 100);
						}
					?>
					@if($transaction->discounttype == 0)
						Rp {{number_format($transaction->discount)}}
					@else
						{{$transaction->discount}} %
					@endif
				</td>
			</tr>
			<tr class="transaction-total">
				<td colspan="6" style="text-align: right; font-size: 18px;">
					Total
				</td>
				<td style="font-size: 18px;">
					Rp {{number_format($transaction->total)}}
				</td>
			</tr>
			<tr class="transaction-total">
				<td colspan="6" style="text-align: right; font-size: 18px;">
					Amount to Pay
				</td>
				<td style="font-size: 18px;">
					Rp {{number_format($transaction->amount_to_pay)}}
				</td>
			</tr>
		</table>
	</div>
</div>