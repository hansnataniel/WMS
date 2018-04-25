<?php
	use Illuminate\Support\Str;

	use App\Models\Setting;
	use App\Models\Ri;
	use App\Models\Ridetail;
	use App\Models\Invoice;
	use App\Models\Invoicedetail;
?>

<script>
	$(function(){
		$('.datetimepicker').datetimepicker({
			timepicker: false,
			format: 'Y-m-d',
			minDate: "{{date('Y/m/d', strtotime($invoice->date))}}",
			maxDate: 0
		});
	});
</script>

<?php
	$setting = Setting::first();
?>

<div class="edit-form-group">
	{!!Form::label('date', 'Date', ['class'=>'edit-form-label'])!!}
	{!!Form::text('date', null, ['class'=>'edit-form-text datetimepicker large', 'readonly', 'required', 'placeholder'=>'Select Invoice First'])!!}
</div>
<div class="edit-form-group nota-result">
	<div class="edit-form-label"></div>
	<div class="edit-form-text">
		<span class="invoice-nota">
			Invoice No.Nota : 
			<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/invoice/view/' . $invoice->id)}}" style="text-decoration: none;" target="_blank">
				{{$invoice->no_nota}}
			</a>
		</span>
		<table class="invoice-table">
			<tr class="invoice-title">
				<td>
					Recieve Item No.Nota
				</td>
				<td>
					Sub Total
				</td>
			</tr>

			<?php
				$ris = Ri::whereIn('id', $getridetailids)->get();
				// $total = 0;
			?>
			@foreach($ris as $ri)
				<?php
					$ridetails = Ridetail::where('ri_id', '=', $ri->id)->get();
					foreach ($ridetails as $ridetail) {
						$ridetailids[] = $ridetail->id;
					}
					$invoicedetails = Invoicedetail::where('invoice_id', '=', $invoice->id)->whereIn('ridetail_id', $ridetailids)->where('price', '!=', 0)->get();
					$subtotal = 0;
					foreach ($invoicedetails as $invoicedetail) {
						$subtotal = $subtotal + ($invoicedetail->price * $invoicedetail->qty);
					}
					// $total = $total + $subtotal;
				?>
				<tr>
					<td>
						<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/ri/view/' . $ri->id)}}" style="text-decoration: none;" target="_blank">
							{{$ri->no_nota}}
						</a>
					</td>
					<td>
						Rp {{number_format($subtotal)}}
					</td>
				</tr>
				<?php
					$ridetailids = array();
				?>
			@endforeach
			<tr class="invoice-total">
				<td>
					Total
				</td>
				<td>
					<?php
						$total = 0;
						$getinvoicedetails = Invoicedetail::where('invoice_id', '=', $invoice->id)->get();
						foreach ($getinvoicedetails as $getinvoicedetail) {
							$total = $total + ($getinvoicedetail->qty * $getinvoicedetail->price);
						}
					?>
					Rp {{number_format($total)}}
				</td>
			</tr>
		</table>
	</div>
</div>