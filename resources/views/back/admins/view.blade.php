<?php
	use Illuminate\Support\Str;

	use App\Models\Admin;
?>

@extends('back.template.master')

@section('title')
	Admin View
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
	Admin View
	<span>
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/dashboard')}}">Dashboard</a> / <a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/admin')}}">Admin</a> / <span>Admin View</span>
	</span>
@endsection

@section('help')
	<ul class="menu-help-list-container">
		<li>
			Gunakan tombol Edit untuk mengedit Admin
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
						<a class="view-button-item view-button-back" href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/admin')}}"></a>
					@endif
					{{$admin->name}}
					@if($admin->is_banned == true)
						<span class="text-red">(banned)</span>
					@endif
					<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/admin/' . $admin->id . '/edit')}}" class="view-button-item view-button-edit">
						Edit
					</a>
				</h1>
				
				@if (file_exists(public_path() . '/usr/img/admin/' . $admin->id . '_' . Str::slug($admin->name, '_') . '_thumb.jpg'))
					{!!HTML::image('usr/img/admin/' . $admin->id . '_' . Str::slug($admin->name, '_') . '_thumb.jpg?lastmod=' . Str::random(5), '', ['class'=>'view-photo'])!!}
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
										Admin Group
									</td>
									<td class="view-info-mid">
										:
									</td>
									<td>
										{{$admin->admingroup->name}}
									</td>
								</tr>
								<tr>
									<td>
										Email
									</td>
									<td class="view-info-mid">
										:
									</td>
									<td>
										{{$admin->email}}
									</td>
								</tr>
								<tr>
									<td>
										Admin Status
									</td>
									<td class="view-info-mid">
										:
									</td>
									<td>
										{!!$admin->is_admin == 1 ? "<span class='text-green'>Admin</span>" : "<span class='text-red'>Not Admin</span>"!!}
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
										{!!$admin->is_active == 1 ? "<span class='text-green'>Active</span>" : "<span class='text-red'>Not Active</span>"!!}
									</td>
								</tr>
							</table>
						</div>
					</div>
				</div>
				<div class="view-last-edit">
					<?php
						$createadmin = Admin::find($admin->create_id);
						$updateadmin = Admin::find($admin->update_id);
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
								{{date('l, d F Y G:i:s', strtotime($admin->created_at))}}
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
								{{$createadmin->name}}
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
								{{date('l, d F Y G:i:s', strtotime($admin->updated_at))}}
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
								{{$updateadmin->name}}
							</span>
						</div>
					</div>

					@if($admin->banned_id != 0)
						<?php
							$bannedadmin = Admin::find($admin->banned_id);
						?>
						<div class="view-last-edit-group">
							<div class="view-last-edit-title" style="color: red;">
								Banned
							</div>
							<div class="view-last-edit-item">
								<span>
									Banned at
								</span>
								<span>
									:
								</span>
								<span style="color: red;">
									{{date('l, d F Y G:i:s', strtotime($admin->banned))}}
								</span>
							</div>
							<div class="view-last-edit-item">
								<span>
									Banned by
								</span>
								<span>
									:
								</span>
								<span style="color: red;">
									{{$bannedadmin->name}}
								</span>
							</div>
						</div>

						@if($admin->unbanned_id != 0)
							<?php
								$unbannedadmin = Admin::find($admin->unbanned_id);
							?>

							<div class="view-last-edit-group">
								<div class="view-last-edit-title" style="color: green;">
									Unbanned
								</div>
								<div class="view-last-edit-item">
									<span>
										Unbanned at
									</span>
									<span>
										:
									</span>
									<span style="color: green;">
										{{date('l, d F Y G:i:s', strtotime($admin->unbanned))}}
									</span>
								</div>
								<div class="view-last-edit-item">
									<span>
										Unbanned by
									</span>
									<span>
										:
									</span>
									<span style="color: green;">
										{{$unbannedadmin->name}}
									</span>
								</div>
							</div>
						@endif
					@endif
				</div>
			</div>
		</div>
	</div>
@endsection