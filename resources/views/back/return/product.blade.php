<script type="text/javascript">
	$(function(){
		$('.datetimepicker').datetimepicker({
			timepicker: false,
			format: 'Y-m-d',
			minDate: "{{date('Y/m/d', strtotime($ri->date))}}",
			maxDate: 0
		});

		$('.select').each(function(){
			var data = $(this).attr('placeholder-data');

			$(this).select2({
				placeholder: data
			});
		});
	});
</script>

<div class="edit-form-group">
	{!!Form::label('date', 'Date', ['class'=>'edit-form-label'])!!}
	{!!Form::text('date', null, ['class'=>'edit-form-text datetimepicker large', 'readonly', 'required', 'placeholder'=>'Select Date'])!!}
</div>

<div class="edit-form-group product-result" style="padding-top: 40px;">
	<div class="edit-form-label"></div>
	<div class="edit-form-text product-container">
		<div class="add-new">
			Add New
		</div>
		<span class="add-new-span">
			*Please Select Receive Item First
		</span>
		<table>
			<tr class="tr-title">
				<th style="width: 300px;">
					Product
				</th>
				<th style="text-align: right;">
					Qty
				</th>
				<th style="text-align: right;">
					Price (Rp)
				</th>
				<th style="text-align: right;">
					Subtotal (Rp)
				</th>
				<th>
				</th>
			</tr>

			<tr class="global-discount" style="border-top: double #d2d2d2; border-bottom: 0px;">
				<td colspan="4" style="font-size: 18px; text-align: right;">
					Total (Rp)
				</td>
				<td colspan="2" style="font-size: 18px;" class="total" totaldata="0">
					0
				</td>
			</tr>
		</table>
	</div>
</div>