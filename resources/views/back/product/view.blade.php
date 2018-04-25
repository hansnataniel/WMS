<?php
	use Illuminate\Support\Str;

	use App\Models\Product;
	use App\Models\Admin;
	use App\Models\Substitution;
?>

@extends('back.template.master')

@section('title')
	Product View
@endsection

@section('head_additional')
	{!!HTML::style('css/back/detail.css')!!}
	{!!HTML::style('css/back/index.css')!!}

	<style>
		.edit-sub {
			position: absolute;
			right: 0px;
			top: 0px;
			line-height: 30px;
			font-size: 14px;
			background: #f7961f;
			color: #fff;
			padding: 0px 15px;
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
	Product View
	<span>
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/dashboard')}}">Dashboard</a> / <a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/product')}}">Product</a> / <span>Product View</span>
	</span>
@endsection

@section('help')
	<ul class="menu-help-list-container">
		<li>
			Gunakan tombol Edit untuk mengedit Product
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
						<a class="view-button-item view-button-back" href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/product')}}"></a>
					@endif
					{{$product->name}}
					<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/product/' . $product->id . '/edit')}}" class="view-button-item view-button-edit">
						Edit
					</a>
				</h1>

				@if($product->no_merk != null)
					<div style="position: relative; display: table; text-align: center;">
						<?php
							$generator1 = new Picqer\Barcode\BarcodeGeneratorPNG();
							echo '<img style="margin-bottom: 5px; position: relative; display: block;" src="data:image/png;base64,' . base64_encode($generator1->getBarcode("$product->no_merk", $generator1::TYPE_CODE_128)) . '">';
						?>
						<span style="position: relative; display: block; margin-bottom: 20px; font-size: 14px; font-weight: bold;">
							{{$product->no_merk}}
						</span>
					</div>
				@endif
				
				@if (file_exists(public_path() . '/usr/img/product/' . $product->id . '_' . Str::slug($product->name, '_') . '_thumb.jpg'))
					{!!HTML::image('usr/img/product/' . $product->id . '_' . Str::slug($product->name, '_') . '_thumb.jpg?lastmod=' . Str::random(5), '', ['class'=>'view-photo'])!!}
				@endif
				<div class="page-group">
					<div class="page-item col-2-4">
						<div class="page-item-title">
							Detail Information
						</div>
						<div class="page-item-content view-item-content">
							<table class="view-detail-table">
								<tr>
									<td>
										OEM Product
									</td>
									<td class="view-info-mid">
										:
									</td>
									<td>
										<?php
											$reference = Product::find($product->reference_id);
										?>
										@if($reference == null)
											-
										@else
											<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/product/' . $reference->id)}}" style="color: blue;">
												{{$reference->name}}
											</a>
										@endif
									</td>
								</tr>
								<tr>
									<td>
										Kendaraan
									</td>
									<td class="view-info-mid">
										:
									</td>
									<td>
										<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/kendaraan/' . $product->kendaraan_id)}}" style="color: blue;">
											{{$product->kendaraan->brand}} - {{$product->kendaraan->type}}
										</a>
									</td>
								</tr>
								<tr>
									<td>
										Min Stock
									</td>
									<td class="view-info-mid">
										:
									</td>
									<td>
										{{$product->min_stock}}
									</td>
								</tr>
								<tr>
									<td>
										Max Stock
									</td>
									<td class="view-info-mid">
										:
									</td>
									<td>
										{{$product->max_stock}}
									</td>
								</tr>
								<tr>
									<td>
										Stock
									</td>
									<td class="view-info-mid">
										:
									</td>
									<td>
										<?php
											$totalstock = 0;
											foreach ($productstocks as $productstock) {
												$totalstock += $productstock->stock;
											}
										?>

										{{$totalstock}}
									</td>
								</tr>
								<tr>
									<td>
										Active Status
									</td>
									<td class="view-info-mid">
										:
									</td>
									<td>
										{!!$product->is_active == 1 ? "<span class='text-green'>Active</span>" : "<span class='text-red'>Not Active</span>"!!}
									</td>
								</tr>
							</table>
						</div>
					</div>
					<div class="page-item col-2-4" style="padding: 0px; border: 0px;">
						<div class="page-item-child" style="padding: 20px; border: 1px solid #d2d2d2; margin-bottom: 30px;">
							<div class="page-item-title">
								Product Location
							</div>
							<div class="page-item-content view-item-content">
								<table class="index-table">
									<tr class="index-tr-title">
										<th>
											#
										</th>
										<th>
											Rak
										</th>
										<th>
											Gudang
										</th>
										<th>
											Stock
										</th>
									</tr>
									<?php
										$counter = 0;
									?>
									@foreach ($productstocks as $productstock)
										<?php 
											$counter++; 
										?>
										<tr>
											<td>
												{{$counter}}
											</td>
											<td>
												<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/rak/' . $productstock->rak->id)}}" style="color: blue;">
													{{$productstock->rak->name}}
												</a>
											</td>
											<td>
												<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/gudang/' . $productstock->rak->gudang_id)}}" style="color: blue;">
													{{$productstock->rak->gudang->name}}
												</a>
											</td>
											<td>
												{{$productstock->stock}}
											</td>
										</tr>
									@endforeach
								</table>
							</div>
						</div>
						<div class="page-item-child" style="padding: 20px; border: 1px solid #d2d2d2">
							<div class="page-item-title">
								Product Substitution

								<a class="edit-sub" href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/product/substitution/' . $product->id)}}">
									Edit Substitution
								</a>
							</div>
							<div class="page-item-content view-item-content">
								<?php
									$substitutions = Substitution::where('product_id', '=', $product->id)->get();
								?>
								<table class="index-table">
									<tr class="index-tr-title">
										<th>
											#
										</th>
										<th>
											Product
										</th>
									</tr>
									<?php
										$counter = 0;
									?>
									@foreach ($substitutions as $substitution)
										<?php 
											$getproduct = Product::find($substitution->substitution_id);
											$counter++; 
										?>
										<tr>
											<td>
												{{$counter}}
											</td>
											<td>
												<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/product/' . $getproduct->id)}}" style="color: blue;">
													{{$getproduct->name}}
												</a>
											</td>
										</tr>
									@endforeach
								</table>
							</div>
						</div>
					</div>
				</div>
				<div class="page-group">
					<div class="page-item col-1">
						<div class="page-item-title">
							Description
						</div>
						<div class="page-item-content view-item-content">
							{!!$product->description!!}
						</div>
					</div>
				</div>
				<div class="view-last-edit">
					<?php
						$createuser = Admin::find($product->create_id);
						$updateuser = Admin::find($product->update_id);
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
								{{date('l, d F Y G:i:s', strtotime($product->created_at))}}
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
								{{date('l, d F Y G:i:s', strtotime($product->update_at))}}
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