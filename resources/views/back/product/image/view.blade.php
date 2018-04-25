<?php
	use Illuminate\Support\Str;

	use App\Models\Admin;
?>

@extends('back.template.master')

@section('title')
	Image View
@endsection

@section('head_additional')
	{!!HTML::style('css/back/detail.css')!!}
@endsection

@section('js_additional')
	<script type="text/javascript">
		$(document).ready(function(){
			
		});
	</script>
@endsection

@section('page_title')
	Image View
	<span>
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/dashboard')}}">Dashboard</a> / <a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/product')}}">Product</a> / <a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/product-image/list/' . $productimage->product->id)}}">Image(s) of {{$productimage->product->name}}</a> / <span>Image View</span>
	</span>
@endsection

@section('help')
	<ul class="menu-help-list-container">
		{{-- <li> --}}
			{{-- Gunakan tombol Edit untuk mengedit Productimage --}}
		{{-- </li> --}}
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
						<a class="view-button-item view-button-back" href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/product/more-image/' . $productimage->product_id)}}"></a>
					@endif
					{{-- {{$productimage->name}} --}}
				</h1>
				
				@if (file_exists(public_path() . '/usr/img/product/thumbnail/' . $productimage->gambar))
					{!!HTML::image('usr/img/product/thumbnail/' . $productimage->gambar . '?lastmod=' . Str::random(5), '', ['class'=>'view-photo'])!!}
				@endif
				{{-- <div class="page-group">
					<div class="page-item col-1">
						<div class="page-item-title">
							Detail Information
						</div>
						<div class="page-item-content view-item-content">
							<table class="view-detail-table">
								<tr>
									<td>
										Active Status
									</td>
									<td class="view-info-mid">
										:
									</td>
									<td>
										{!!$productimage->is_active == true ? "<span class='text-green'>Active</span>" : "<span class='text-red'>Not Active</span>"!!}
									</td>
								</tr>
							</table>
						</div>
					</div>
				</div> --}}
				<div class="view-last-edit">
					<?php
						$createuser = Admin::find($productimage->create_id);
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
								{{date('l, d F Y G:i:s', strtotime($productimage->created_at))}}
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
				</div>
			</div>
		</div>
	</div>
@endsection