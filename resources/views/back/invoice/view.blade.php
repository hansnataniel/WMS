<?php
	use Illuminate\Support\Str;

	use App\Models\Admin;
	use App\Models\Invoice;
	use App\Models\Invoicedetail;
	use App\Models\Ri;
	use App\Models\Ridetail;
?>

@extends('back.template.master')

@section('title')
	Invoice View
@endsection

@section('head_additional')
	{!!HTML::style('css/back/detail.css')!!}
	{!!HTML::style('css/back/index.css')!!}

	<style>
		.page-item-title span {
			position: relative;
			display: block;
			font-size: 14px;
			padding-top: 5px;
		}
	</style>
@endsection

@section('js_additional')
	<script type="text/javascript">
		$(document).ready(function(){
			
		});
	</script>
@endsection

@section('page_title')
	Invoice View
	<span>
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/dashboard')}}">Dashboard</a> / <a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/invoice')}}">Invoice</a> / <span>Invoice View</span>
	</span>
@endsection

@section('help')
	<ul class="menu-help-list-container">
		<li>
			Gunakan tombol Edit untuk mengedit Invoice
		</li>
	</ul>
@endsection

@section('content')
	<div class="page-group">
		<div class="page-item col-1">
			<div class="page-item-content">
				<h1 class="view-title">
					@if($request->session()->has('last_url'))
						<a class="view-button-item view-button-back" href="{{URL::to($request->session()->get('last_url'))}}"></a>
					@else
						<a class="view-button-item view-button-back" href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/invoice')}}"></a>
					@endif
					<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/supplier/' . $supplier->id)}}" style="color: blue;">
						{{$supplier->name}}
					</a>
					<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/invoice/' . $invoice->id . '/edit')}}" class="view-button-item view-button-edit">
						Edit
					</a>
				</h1>

				<?php
					if($invoice->code_id != null)
					{
						$generator1 = new Picqer\Barcode\BarcodeGeneratorPNG();
						echo '<img style="margin-bottom: 20px; position: relative; display: block;" src="data:image/png;base64,' . base64_encode($generator1->getBarcode("$invoice->code_id", $generator1::TYPE_CODE_128)) . '">';
					}
				?>
				
				@if (file_exists(public_path() . '/usr/img/invoice/' . $invoice->id . '_' . Str::slug($invoice->name, '_') . '_thumb.jpg'))
					{!!HTML::image('usr/img/invoice/' . $invoice->id . '_' . Str::slug($invoice->name, '_') . '_thumb.jpg?lastmod=' . Str::random(5), '', ['class'=>'view-photo'])!!}
				@endif
				<div class="page-group">
					<div class="page-item col-1">
						<div class="page-item-title">
							Detail Information
						</div>
						<div class="page-item-content view-item-content">
							<table class="view-detail-table">
								<tr>
									<td>
										No Nota
									</td>
									<td class="view-info-mid">
										:
									</td>
									<td>
										{{$invoice->no_nota}}
									</td>
								</tr>
								<tr>
									<td>
										Date
									</td>
									<td class="view-info-mid">
										:
									</td>
									<td>
										{{date('d F Y', strtotime($invoice->date))}}
									</td>
								</tr>
								<tr>
									<td>
										Status
									</td>
									<td class="view-info-mid">
										:
									</td>
									<td>
										{!!$invoice->status == 'Pending' ? "<span class='text-orange'>Pending</span>":"<span class='text-green'>Paid</span>"!!}
									</td>
								</tr>
								<tr>
									<td>
										Due Date
									</td>
									<td class="view-info-mid">
										:
									</td>
									<td>
										{{date('d F Y', strtotime($invoice->due_date))}}
									</td>
								</tr>
								<tr>
									<td>
										Total
									</td>
									<td class="view-info-mid">
										:
									</td>
									<td style="font-size: 18px; color: #0d0f3b;">
										<?php
											$total = 0;
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
				</div>

				<?php
					$ris = Ri::whereIn('id', $getridetailids)->get();
				?>
				@foreach($ris as $ri)
					<div class="page-group">
						<div class="page-item col-1">
							<div class="page-item-title">
								<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/ri/' . $ri->id)}}" style="color: blue;">
									Recieve Item No. {{$ri->no_nota}}
								</a>
								<span>
									Date: {{date('d F Y', strtotime($ri->date))}}
								</span>
							</div>
							<div class="page-item-content view-item-content">
								<table class="index-table">
									<tr class="index-tr-title">
										<th>
											#
										</th>
										<th>
											Product
										</th>
										<th>
											Qty
										</th>
										<th width="200" style="text-align: right;">
											Price
										</th>
										<th width="200" style="text-align: right;">
											Subtotal
										</th>
									</tr>
									<?php
										$counter = 0;
										$subtotal = 0;
										$ridetailids = array();
										$ridetails = Ridetail::where('ri_id', '=', $ri->id)->get();
										foreach ($ridetails as $ridetail) {
											$ridetailids[] = $ridetail->id;
										}
										$invoicedetails = Invoicedetail::where('invoice_id', '=', $invoice->id)->whereIn('ridetail_id', $ridetailids)->where('price', '!=', 0)->get();
										$frees = Invoicedetail::where('invoice_id', '=', $invoice->id)->whereIn('ridetail_id', $ridetailids)->where('price', '=', 0)->get();
									?>
									@foreach ($invoicedetails as $invoicedetail)
										<?php
											$counter++;
											$subtotal = $subtotal + ($invoicedetail->price * $invoicedetail->qty);
										?>

										<tr>
											<td>
												{{$counter}}
											</td>
											<td>
												<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/product/' . $invoicedetail->ridetail->podetail->product_id)}}" style="color: blue;">
													{{$invoicedetail->ridetail->podetail->product->name}}
												</a>
											</td>
											<td>
												{{$invoicedetail->qty}}
											</td>
											<td style="text-align: right;">
												Rp {{number_format($invoicedetail->price)}}
											</td>
											<td style="text-align: right;">
												Rp {{number_format($invoicedetail->price * $invoicedetail->qty)}}
											</td>
										</tr>
									@endforeach
									@if(!$frees->isEmpty())
										<tr style="border-top: 1px solid #535353;">
											<td colspan="5" style="font-weight: bold;">
												Free Product
											</td>
										</tr>
										<?php
											$counter = 0;
										?>
										@foreach($frees as $free)
											<?php
												$counter++;
											?>
											<tr style="border-top: 1px solid #535353;">
												<td>
													{{$counter}}
												</td>
												<td>
													<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/product/' . $free->ridetail->product_id)}}" style="color: blue;">
														{{$free->ridetail->product->name}}
													</a>
												</td>
												<td>
													{{$free->qty}}
												</td>
												<td style="text-align: right;">
													Rp 0
												</td>
												<td style="text-align: right;">
													Rp 0
												</td>
											</tr>
										@endforeach
									@endif
									<tr style="border-top: double #535353;">
										<td colspan="3">
										</td>
										<td style="text-align: right; font-size: 18px;">
											Total
										</td>
										<td style="text-align: right; font-size: 18px;">
											Rp {{number_format($subtotal)}}
										</td>
									</tr>
								</table>
							</div>
						</div>
					</div>
				@endforeach

				<div class="view-last-edit">
					<?php
						$createuser = Admin::find($invoice->create_id);
						$updateuser = Admin::find($invoice->update_id);
					?>

					<div class="page-item-title" style="margin-bottom: 20px;">
						Basic Information
					</div>

					<div class="view-last-edit-group">
						<div class="view-last-edit-title">
							Create
						</div>
						<div class="view-last-edit-item">
							<span>
								Created at
							</span>
							<span>
								:
							</span>
							<span>
								{{date('l, d F Y G:i:s', strtotime($invoice->created_at))}}
							</span>
						</div>
						<div class="view-last-edit-item">
							<span>
								Created by
							</span>
							<span>
								:
							</span>
							<span>
								{{$createuser->name}}
							</span>
						</div>
					</div>

					<div class="view-last-edit-group">
						<div class="view-last-edit-title">
							Update
						</div>
						<div class="view-last-edit-item">
							<span>
								Updated at
							</span>
							<span>
								:
							</span>
							<span>
								{{date('l, d F Y G:i:s', strtotime($invoice->updated_at))}}
							</span>
						</div>
						<div class="view-last-edit-item">
							<span>
								Last Updated by
							</span>
							<span>
								:
							</span>
							<span>
								{{$updateuser->name}}
							</span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection