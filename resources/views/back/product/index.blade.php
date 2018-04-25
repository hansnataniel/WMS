<?php
	use Illuminate\Support\Str;

	use App\Models\Product;
	use App\Models\Productstock;
?>

@extends('back.template.master')

@section('title')
	Product
@endsection

@section('head_additional')
	{!!HTML::style('css/back/index.css')!!}
@endsection

@section('js_additional')
	<script type="text/javascript">
		$(document).ready(function(){
			$('.index-action-switch').click(function(e){
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

			$('.index-del-switch').click(function(){
				$('.pop-result').html($(this).parent().parent().parent().find('.index-del-content').html());

				$('.pop-container').fadeIn();
				$('.pop-container').find('.index-del-item').each(function(e){
					$(this).delay(70*e).animate({
	                    opacity: 1,
	                    top: 0
	                }, 300);
				});
			});
		});
	</script>
@endsection

@section('page_title')
	Product
	<span>
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/dashboard')}}">Dashboard</a> / <span>Product</span>
	</span>
@endsection

@section('help')
	<ul class="menu-help-list-container">
		<li>
			Gunakan tombol New untuk menambahkan data baru
		</li>
		<li>
			Gunakan tombol Image di dalam tombol Action untuk menyesuaikan gambar dari product
		</li>
		<li>
			Gunakan tombol Stock System di dalam tombol Action untuk mengedit Stock dari product
		</li>
		<li>
			Gunakan tombol Stock History di dalam tombol Action untuk melihat history stock dari product
		</li>
		<li>
			Gunakan tombol View di dalam tombol Action untuk melihat detail dari Product
		</li>
		<li>
			Gunakan tombol Edit di dalam tombol Action untuk mengedit Product
		</li>
	</ul>
@endsection

@section('search')
	{!!Form::open(['URL' => URL::current(), 'method' => 'GET'])!!}
		<div class="menu-group">
			<div class="menu-title">
				Search by
			</div>
			<div class="menu-search-group">
				{!!Form::label('src_name', 'Name', ['class'=>'menu-search-label'])!!}	
				{!!Form::text('src_name', '', ['class'=>'menu-search-text'])!!}
			</div>
			<div class="menu-search-group">
				{!!Form::label('src_no_merk', 'No Merk', ['class'=>'menu-search-label'])!!}	
				{!!Form::text('src_no_merk', '', ['class'=>'menu-search-text'])!!}
			</div>
			<div class="menu-search-group">
				{!!Form::label('src_is_active', 'Active Status', ['class'=>'menu-search-label'])!!}	
				{!!Form::select('src_is_active', [''=>'Select Active Status', '1'=>'Active', '0'=>'Not Active'], null, ['class'=>'menu-search-text select'])!!}
			</div>
		</div>

		<div class="menu-group">
			<div class="menu-title">
				Sort by
			</div>
			<div class="menu-search-group">
				{!!Form::select('order_by', ['id'=>'Input Time', 'name'=>'Name'], null, ['class'=>'menu-search-text select'])!!}
			</div>
			<div class="menu-search-group">
				<div class="menu-search-radio-group">
					{!!Form::radio('order_method', 'asc', true, ['class'=>'menu-search-radio'])!!}
					{!!HTML::image('img/admin/sort1.png', '', ['menu-class'=>'search-radio-image'])!!}
				</div>
				<div class="menu-search-radio-group">
					{!!Form::radio('order_method', 'desc', false, ['class'=>'menu-search-radio'])!!}
					{!!HTML::image('img/admin/sort2.png', '', ['class'=>'menu-search-radio-image'])!!}
				</div>
			</div>
		</div>
		<div class="menu-group">
			{!!Form::submit('Search', ['class'=>'menu-search-button'])!!}
		</div>
	{!!Form::close()!!}
@endsection

@section('content')
	<div class="page-group">
		<div class="page-item col-1">
			<div class="page-item-content">
				<div class="index-desc-container">
					<a class="index-desc-item" href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/product/create')}}">
						{!!HTML::image('img/admin/index/add_icon.png')!!}
						<span>
							Add New
						</span>
					</a>

					<span class="index-desc-count">
						{{$records_count}} record(s) found
					</span>
				</div>
				<table class="index-table">
					<tr class="index-tr-title">
						<th>
							#
						</th>
						<th>
							Name
						</th>
						<th>
							No Merk
						</th>
						<th>
							Kendaraan
						</th>
						<th>
							Stock
						</th>
						<th>
							Active Status
						</th>
						<th>
						</th>
					</tr>
					<?php
						if ($request->has('page'))
						{
							$counter = ($request->input('page')-1) * $per_page;
						}
						else
						{
							$counter = 0;
						}
					?>
					@foreach ($products as $product)
						<?php $counter++; ?>
						<tr>
							<td>
								{{$counter}}
							</td>
							<td>
								{{$product->name}}
							</td>
							<td>
								{{$product->no_merk}}
							</td>
							<td>
								<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/kendaraan/' . $product->kendaraan_id)}}" style="color: blue;">
									{{$product->kendaraan->brand}} - {{$product->kendaraan->type}}
								</a>
							</td>
							<td>
								<?php
									$productstocks = Productstock::where('product_id', '=', $product->id)->where('is_active', '=', true)->get();
									$totalstock = 0;
									foreach ($productstocks as $productstock) {
										$totalstock += $productstock->stock;
									}
								?>

								{{$totalstock}}
							</td>
							<td>
								{!!$product->is_active == true ? "<span class='text-green'>Active</span>":"<span class='text-red'>Not Active</span>"!!}
							</td>
							<td class="index-td-icon">
								<div class="index-action-switch">
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
									<ul class="index-action-child-container" style="width: 155px">
										<li>
											<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/product/more-image/' . $product->id)}}" class="separator">
												{!!HTML::image('img/admin/index/photo_icon.png')!!}
												<span>
													Image
												</span>
											</a>
										</li>
										<li>
											<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/product/substitution/' . $product->id)}}" class="separator">
												{!!HTML::image('img/admin/index/photo_icon.png')!!}
												<span>
													Substitution
												</span>
											</a>
										</li>
										<li>
											<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/product/' . $product->id)}}">
												{!!HTML::image('img/admin/index/detail_icon.png')!!}
												<span>
													View
												</span>
											</a>
										</li>
										<li>
											<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/product/' . $product->id . '/edit')}}">
												{!!HTML::image('img/admin/index/edit_icon.png')!!}
												<span>
													Edit
												</span>
											</a>
										</li>
									</ul>
								</div>
							</td>
						</tr>
					@endforeach
				</table>

				{{-- 
					Pagination
				 --}}
				{{$products->appends($criteria)->links()}}
			</div>
		</div>
	</div>
@endsection