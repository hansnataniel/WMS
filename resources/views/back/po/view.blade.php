<?php
	use Illuminate\Support\Str;

	use App\Models\Admin;
	use App\Models\Po;
	use App\Models\Podetail;
?>

@extends('back.template.master')

@section('title')
	Purchase Order View
@endsection

@section('head_additional')
	{!!HTML::style('css/back/detail.css')!!}
	{!!HTML::style('css/back/index.css')!!}
@endsection

@section('js_additional')
	<script type="text/javascript">
		$(document).ready(function(){
			
		});
	</script>
@endsection

@section('page_title')
	Purchase Order View
	<span>
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/dashboard')}}">Dashboard</a> / <a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/po')}}">Purchase Order</a> / <span>Purchase Order View</span>
	</span>
@endsection

@section('help')
	<ul class="menu-help-list-container">
		<li>
			Gunakan tombol Edit untuk mengedit Purchase Order
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
						<a class="view-button-item view-button-back" href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/po')}}"></a>
					@endif
					{{$po->no_nota}}
					<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/po/' . $po->id . '/edit')}}" class="view-button-item view-button-edit">
						Edit
					</a>
				</h1>

				<?php
					if($po->code_id != null)
					{
						$generator1 = new Picqer\Barcode\BarcodeGeneratorPNG();
						echo '<img style="margin-bottom: 20px; position: relative; display: block;" src="data:image/png;base64,' . base64_encode($generator1->getBarcode("$po->code_id", $generator1::TYPE_CODE_128)) . '">';
					}
				?>
				
				@if (file_exists(public_path() . '/usr/img/po/' . $po->id . '_' . Str::slug($po->name, '_') . '_thumb.jpg'))
					{!!HTML::image('usr/img/po/' . $po->id . '_' . Str::slug($po->name, '_') . '_thumb.jpg?lastmod=' . Str::random(5), '', ['class'=>'view-photo'])!!}
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
										Supplier
									</td>
									<td class="view-info-mid">
										:
									</td>
									<td>
										<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/supplier/' . $po->supplier_id)}}" style="color: blue;">
											{{$po->supplier->name}}
										</a>
									</td>
								</tr>
								<tr>
									<td>
										date
									</td>
									<td class="view-info-mid">
										:
									</td>
									<td>
										{{date('d F Y', strtotime($po->date))}}
									</td>
								</tr>
								<tr>
									<td>
										Sending Status
									</td>
									<td class="view-info-mid">
										:
									</td>
									<td>
										{!!$po->status == 'Belum Dikirim' ? "<span class='text-red'>Belum Dikirim</span>":"<span class='text-green'>Terkirim</span>"!!}
									</td>
								</tr>
								<tr>
									<td>
										Recieve Item Status
									</td>
									<td class="view-info-mid">
										:
									</td>
									<td>
										@if($po->ri_status == 'Belum Diterima')
											<span class='text-red'>
										@elseif($po->ri_status == 'Diterima Sebagian')
											<span class='text-orange'>
										@elseif($po->ri_status == 'Diterima')
											<span class='text-green'>
										@endif
											{{$po->ri_status}}
										</span>
									</td>
								</tr>
							</table>
						</div>
					</div>
				</div>
				<div class="page-group">
					<div class="page-item col-1">
						<div class="page-item-title">
							Product Detail
						</div>
						<div class="page-item-content view-item-content">
							<?php
								$podetails = Podetail::where('po_id', '=', $po->id)->get();
							?>
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
									<th style="text-align: right;">
										Price
									</th>
									<th style="text-align: right;">
										Discount
									</th>
									<th style="text-align: right;">
										Sub Price
									</th>
								</tr>
								<?php
									$counter = 0;
									$total = 0;
								?>
								@foreach ($podetails as $podetail)
									<?php 
										$counter++; 
										if($podetail->discounttype == 0)
										{
											$total += ($podetail->price * $podetail->qty) - $podetail->discount;
										}
										else
										{
											$total += ($podetail->price * $podetail->qty) - ((($podetail->price * $podetail->qty) * $podetail->discount) / 100);
										}
									?>
									<tr>
										<td>
											{{$counter}}
										</td>
										<td>
											<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/product/' . $podetail->product_id)}}" style="color: blue;">
												{{$podetail->product->name}}
											</a>
										</td>
										<td>
											{{$podetail->qty}}
										</td>
										<td style="text-align: right;">
											Rp {{number_format($podetail->price)}}
										</td>
										<td style="text-align: right;">
											@if($podetail->discounttype == 0)
												Rp {{number_format($podetail->discount)}}
											@else
												{{$podetail->discount}} %
											@endif
										</td>
										<td style="text-align: right;">
											@if($podetail->discounttype == 0)
												Rp {{number_format(($podetail->price * $podetail->qty) - $podetail->discount)}}
											@else
												Rp {{number_format(($podetail->price * $podetail->qty) - ((($podetail->price * $podetail->qty) * $podetail->discount) / 100))}}
											@endif
										</td>
									</tr>
								@endforeach
								<tr>
									<td colspan="5" style="font-size: 16px; text-align: right; border-top: 1px solid #535353; font-weight: bold;">
										Discount
									</td>
									<td style="font-size: 16px; text-align: right; border-top: 1px solid #535353; font-weight: bold;">
										<?php
											if($po->discounttype == 0)
											{
												$total = $total - $po->discount;
											}
											else
											{
												$total = $total - (($total * $po->discount) / 100);
											}
										?>
										@if($po->discounttype == 0)
											Rp {{number_format($po->discount)}}
										@else
											{{$po->discount}} %
										@endif
									</td>
								</tr>
								<tr>
									<td colspan="5" style="font-size: 20px; text-align: right; border-top: 1px solid #535353; font-weight: bold;">
										Total
									</td>
									<td style="font-size: 20px; text-align: right; border-top: 1px solid #535353; font-weight: bold;">
										Rp {{number_format($total)}}
									</td>
								</tr>	
							</table>
						</div>
					</div>
				</div>
				<div class="view-last-edit">
					<?php
						$createuser = Admin::find($po->create_id);
						$updateuser = Admin::find($po->update_id);
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
								{{date('l, d F Y G:i:s', strtotime($po->created_at))}}
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
								{{date('l, d F Y G:i:s', strtotime($po->updated_at))}}
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