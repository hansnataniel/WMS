<?php
	use Illuminate\Support\Str;
?>

@extends('back.template.master')

@section('title')
	Stock
@endsection

@section('head_additional')
	{!!HTML::style('css/back/index.css')!!}
@endsection

@section('js_additional')
	
@endsection

@section('page_title')
	Stock
	<span>
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/dashboard')}}">Dashboard</a> / <span>Stock</span>
	</span>
@endsection

@section('help')
	<ul class="menu-help-list-container">
		<li>
			Gunakan tombol New untuk menambahkan data baru
		</li>
		<li>
			Gunakan tombol View di dalam tombol Action untuk melihat detail dari Stock
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
				{!!Form::label('src_date', 'Date', ['class'=>'menu-search-label'])!!}	
				<div class="menu-search-subgroup">
					{!!Form::text('src_start', '', ['class'=>'menu-search-text', 'placeholder'=>'Start'])!!}
					{!! Form::label('date', 'to', ['class'=>'menu-search-label']) !!}
					{!!Form::text('src_end', '', ['class'=>'menu-search-text', 'placeholder'=>'End'])!!}
				</div>
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
				{!!Form::select('order_by', ['id'=>'Input Time'], null, ['class'=>'menu-search-text select'])!!}
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
					<a class="index-desc-item" href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/stock/create')}}">
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
							Date
						</th>
						<th>
							Product
						</th>
						<th>
							Quantity
						</th>
						<th>
							Status
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
					@foreach ($stocks as $stock)
						<?php $counter++; ?>
						<tr>
							<td>
								{{$counter}}
							</td>
							<td>
								{{date('l, d F Y G:i:s', strtotime($stock->date))}}
							</td>
							<td>
								{{$stock->product->name}}
							</td>
							<td>
								{{$stock->quantity}}
							</td>
							<td>
								@if($stock->status == 0)
									<span class="text-green">
										Stock In
									</span>
								@else
									<span class="text-red">
										Stock Out
									</span>
								@endif
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
									<ul class="index-action-child-container" style="width: 110px">
										<li>
											<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/stock/' . $stock->id)}}">
												{!!HTML::image('img/admin/index/detail_icon.png')!!}
												<span>
													View
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
				{{$stocks->appends($criteria)->links()}}
			</div>
		</div>
	</div>
@endsection