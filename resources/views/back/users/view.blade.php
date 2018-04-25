<?php
	use Illuminate\Support\Str;

	use App\Models\User;
	use App\Models\Admin;
?>

@extends('back.template.master')

@section('title')
	User View
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
	User View
	<span>
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/dashboard')}}">Dashboard</a> / <a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/user')}}">User</a> / <span>User View</span>
	</span>
@endsection

@section('help')
	<ul class="menu-help-list-container">
		<li>
			Gunakan tombol Edit untuk mengedit User
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
						<a class="view-button-item view-button-back" href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/user')}}"></a>
					@endif
					{{$user->name}}
					@if($user->is_banned == true)
						<span class="text-red">(banned)</span>
					@endif
					<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/user/' . $user->id . '/edit')}}" class="view-button-item view-button-edit">
						Edit
					</a>
				</h1>
				
				@if (file_exists(public_path() . '/usr/img/user/' . $user->id . '_' . Str::slug($user->name, '_') . '_thumb.jpg'))
					{!!HTML::image('usr/img/user/' . $user->id . '_' . Str::slug($user->name, '_') . '_thumb.jpg?lastmod=' . Str::random(5), '', ['class'=>'view-photo'])!!}
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
										Email
									</td>
									<td class="view-info-mid">
										:
									</td>
									<td>
										{{$user->email}}
									</td>
								</tr>
								<tr>
									<td>
										Phone
									</td>
									<td class="view-info-mid">
										:
									</td>
									<td>
										{{$user->phone}}
									</td>
								</tr>
								<tr>
									<td>
										Area
									</td>
									<td class="view-info-mid">
										:
									</td>
									<td>
										{{$user->area->name}}
									</td>
								</tr>
								<tr>
									<td>
										Address
									</td>
									<td class="view-info-mid">
										:
									</td>
									<td>
										{{$user->address}}
									</td>
								</tr>
								<tr>
									<td>
										Birthdate
									</td>
									<td class="view-info-mid">
										:
									</td>
									<td>
										{{date('d F Y', strtotime($user->birthdate))}}
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
										{!!$user->is_active == 1 ? "<span class='text-green'>Active</span>" : "<span class='text-red'>Not Active</span>"!!}
									</td>
								</tr>
							</table>
						</div>
					</div>
				</div>
				<div class="view-last-edit">
					<?php
						$createuser = Admin::find($user->create_id);
						$updateuser = Admin::find($user->update_id);
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
								{{date('l, d F Y G:i:s', strtotime($user->created_at))}}
							</span>
						</div>
						@if($user->create_id != 0)
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
						@endif
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
								{{date('l, d F Y G:i:s', strtotime($user->updated_at))}}
							</span>
						</div>
						@if($user->update_id != 0)
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
						@endif
					</div>

					@if($user->banned_id != 0)
						<?php
							$banneduser = Admin::find($user->banned_id);
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
									{{date('l, d F Y G:i:s', strtotime($user->banned))}}
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
									{{$banneduser->name}}
								</span>
							</div>
						</div>

						@if($user->unbanned_id != 0)
							<?php
								$unbanneduser = Admin::find($user->unbanned_id);
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
										{{date('l, d F Y G:i:s', strtotime($user->unbanned))}}
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
										{{$unbanneduser->name}}
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