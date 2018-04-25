<?php
	use Illuminate\Support\Str;

	use App\Models\Admin;
?>

@extends('back.template.master')

@section('title')
	Stock Adjustment View
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
	Stock Adjustment View
	<span>
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/dashboard')}}">Dashboard</a> / <a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/adjustment')}}">Stock Adjustment</a> / <span>Stock Adjustment View</span>
	</span>
@endsection

@section('help')
	<ul class="menu-help-list-container">
		
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
						<a class="view-button-item view-button-back" href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/adjustment')}}"></a>
					@endif
					{{$adjustment->no_nota}}
				</h1>
				
				@if (file_exists(public_path() . '/usr/img/adjustment/' . $adjustment->id . '_' . Str::slug($adjustment->title, '_') . '_thumb.jpg'))
					{!!HTML::image('usr/img/adjustment/' . $adjustment->id . '_' . Str::slug($adjustment->title, '_') . '_thumb.jpg?lastmod=' . Str::random(5), '', ['class'=>'view-photo'])!!}
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
										Date
									</td>
									<td class="view-info-mid">
										:
									</td>
									<td>
										{{date('d M Y', strtotime($adjustment->date))}}
									</td>
								</tr>
								<tr>
									<td>
										Product
									</td>
									<td class="view-info-mid">
										:
									</td>
									<td>
										{{$adjustment->product->name}}
									</td>
								</tr>
								<tr>
									<td>
										Quantity
									</td>
									<td class="view-info-mid">
										:
									</td>
									<td>
										{{$adjustment->quantity}}
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
										{!!$adjustment->status == 'In' ? "<span class='text-green'>Stock In</span>":"<span class='text-red'>Stock Out</span>"!!}
									</td>
								</tr>
								<tr>
									<td>
										Note
									</td>
									<td class="view-info-mid">
										:
									</td>
									<td>
										{{$adjustment->note}}
									</td>
								</tr>
							</table>
						</div>
					</div>
				</div>
				<div class="view-last-edit">
					<?php
						$createuser = Admin::find($adjustment->create_id);
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
								{{date('l, d F Y G:i:s', strtotime($adjustment->created_at))}}
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