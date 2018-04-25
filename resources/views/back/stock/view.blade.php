<?php
	use Illuminate\Support\Str;

	use App\Models\Admin;
?>

@extends('back.template.master')

@section('title')
	Stock View
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
	Stock View
	<span>
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/dashboard')}}">Dashboard</a> / <a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/stock')}}">Stock</a> / <span>Stock View</span>
	</span>
@endsection

@section('help')
	<ul class="menu-help-list-container">
		<li>
			Gunakan tombol Edit untuk mengedit Stock
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
						<a class="view-button-item view-button-back" href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/stock')}}"></a>
					@endif
					{{$stock->title}}
					<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/stock/' . $stock->id . '/edit')}}" class="view-button-item view-button-edit">
						Edit
					</a>
				</h1>
				
				@if (file_exists(public_path() . '/usr/img/stock/' . $stock->id . '_' . Str::slug($stock->title, '_') . '_thumb.jpg'))
					{!!HTML::image('usr/img/stock/' . $stock->id . '_' . Str::slug($stock->title, '_') . '_thumb.jpg?lastmod=' . Str::random(5), '', ['class'=>'view-photo'])!!}
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
										Date
									</td>
									<td class="view-info-mid">
										:
									</td>
									<td>
										{{$stock->last_stock}}
									</td>
								</tr>
								<tr>
									<td>
										Amount
									</td>
									<td class="view-info-mid">
										:
									</td>
									<td>
										{{$stock->amount}}
									</td>
								</tr>
								<tr>
									<td>
										Final Stock
									</td>
									<td class="view-info-mid">
										:
									</td>
									<td>
										{{$stock->final_stock}}
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
										{!!$stock->is_active != 0 ? "<span class='text-green'>Stock In</span>" : "<span class='text-red'>Stock Out</span>"!!}
									</td>
								</tr>
							</table>
						</div>
					</div>
					<div class="page-item col-2-4">
						<div class="page-item-title">
							Note
						</div>
						<div class="page-item-content view-item-content">
							{{$stock->note}}
						</div>
					</div>
				</div>
				<div class="view-last-edit">
					<?php
						$createuser = Admin::find($stock->create_id);
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
								{{date('l, d F Y G:i:s', strtotime($stock->created_at))}}
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