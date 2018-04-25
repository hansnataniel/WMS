<?php
	use Illuminate\Support\Str;
?>

@extends('back.template.master')

@section('title')
	Admin
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
	Admin
	<span>
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/dashboard')}}">Dashboard</a> / <span>Admin</span>
	</span>
@endsection

@section('help')
	<ul class="menu-help-list-container">
		<li>
			Gunakan tombol New untuk menambahkan data baru
		</li>
		<li>
			Gunakan tombol View di dalam tombol Action untuk melihat detail dari Admin
		</li>
		<li>
			Gunakan tombol Edit di dalam tombol Action untuk mengedit Admin
		</li>
		<li>
			Gunakan tombol Delete di dalam tombol Action untuk menghapus Admin
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
				{!!Form::label('src_email', 'Email', ['class'=>'menu-search-label'])!!}
				{!!Form::text('src_email', '', ['class'=>'menu-search-text'])!!}
			</div>
			<div class="menu-search-group">
				{!!Form::label('src_admingroup_id', 'Admin Group', ['class'=>'menu-search-label'])!!}	
				{!!Form::select('src_admingroup_id', $admingroup_options, null, ['class'=>'menu-search-text select'])!!}
			</div>
			<div class="menu-search-group">
				{!!Form::label('src_is_banned', 'Banned Status', ['class'=>'menu-search-label'])!!}	
				{!!Form::select('src_is_banned', [''=>'Select Banned Status', '1'=>'Banned', '0'=>'Not Banned'], null, ['class'=>'menu-search-text select'])!!}
			</div>
			<div class="menu-search-group">
				{!!Form::label('src_is_admin', 'Admin Status', ['class'=>'menu-search-label'])!!}	
				{!!Form::select('src_is_admin', [''=>'Select Admin Status', '1'=>'Admin', '0'=>'Not Admin'], null, ['class'=>'menu-search-text select'])!!}
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
				{!!Form::select('order_by', ['id'=>'Input Time', 'name'=>'Name', 'email'=>'Email'], null, ['class'=>'menu-search-text select'])!!}
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
					<a class="index-desc-item" href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/admin/create')}}">
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
							Email
						</th>
						<th>
							Admin Group
						</th>
						<th>
							Admin Status
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
					@foreach ($admins as $admin)
						<?php $counter++; ?>
						<tr>
							<td>
								{{$counter}}
							</td>
							<td>
								{{$admin->name}}
								@if($admin->is_banned == true)
									<span class="text-red"> (Banned)</span>
								@endif
							</td>
							<td>
								{{$admin->email}}
							</td>
							<td>
								{{$admin->admingroup->name}}
							</td>
							<td>
								{!!$admin->is_admin == true ? "<span class='text-green'>Admin</span>":"<span class='text-red'>Not Admin</span>"!!}
							</td>
							<td>
								{!!$admin->is_active == true ? "<span class='text-green'>Active</span>":"<span class='text-red'>Not Active</span>"!!}
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
									<ul class="index-action-child-container" style="width: 200px">
										@if(Auth::user()->id != $admin->id)
											<li>
												<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/admin/banned/' . $admin->id)}}" class="separator">
													@if($admin->is_banned == true)
														{!!HTML::image('img/admin/index/unbanned_icon.png')!!}
														<span>
															Unbanned
														</span>
													@else
														{!!HTML::image('img/admin/index/banned_icon.png')!!}
														<span>
															Banned
														</span>
													@endif
												</a>
											</li>
										@endif
										<li>
											<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/admin/' . $admin->id)}}">
												{!!HTML::image('img/admin/index/detail_icon.png')!!}
												<span>
													View
												</span>
											</a>
										</li>
										<li>
											<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/admin/' . $admin->id . '/edit')}}">
												{!!HTML::image('img/admin/index/edit_icon.png')!!}
												<span>
													Edit
												</span>
											</a>
										</li>
										<li>
											<div class="index-del-switch">
												{!!HTML::image('img/admin/index/trash_icon.png')!!}
												<span>
													Delete
												</span>
											</div>
										</li>
									</ul>

									{{-- 
										Content of Delete
									 --}}
									<div class="index-del-content">
										<div class="index-del-title index-del-item">
											Do you really want to delete this admin?
										</div>
										<table class="index-del-table index-del-item">
											<tr>
												<td>
													Name
												</td>
												<td class="index-td-mid">
													:
												</td>
												<td>
													{{$admin->name}}
												</td>
											</tr>
										</table>
										{!!Form::open(['url' => URL::to(Crypt::decrypt($setting->admin_url) . '/admin/' . $admin->id), 'method' => 'DELETE', 'class'=>'form index-del-item'])!!}
											{!!Form::submit('Delete', ['class'=>'index-del-button'])!!}
										{!!Form::close()!!}
									</div>
								</div>
							</td>
						</tr>
					@endforeach
				</table>

				{{-- 
					Pagination
				 --}}
				{{$admins->appends($criteria)->links()}}
			</div>
		</div>
	</div>
@endsection