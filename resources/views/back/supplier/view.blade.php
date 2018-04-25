<?php
	use Illuminate\Support\Str;

	use App\Models\Admin;
	use App\Models\Supplier;
?>

@extends('back.template.master')

@section('title')
	Supplier View
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
	Supplier View
	<span>
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/dashboard')}}">Dashboard</a> / <a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/supplier')}}">Supplier</a> / <span>Supplier View</span>
	</span>
@endsection

@section('help')
	<ul class="menu-help-list-container">
		<li>
			Gunakan tombol Edit untuk mengedit Supplier
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
						<a class="view-button-item view-button-back" href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/supplier')}}"></a>
					@endif
					{{$supplier->name}}
					<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/supplier/' . $supplier->id . '/edit')}}" class="view-button-item view-button-edit">
						Edit
					</a>
				</h1>

				<?php
					if($supplier->code != null)
					{
						// $generator1 = new Picqer\Code\CodeGeneratorPNG();
						// echo '<img style="margin-bottom: 20px; position: relative; display: block;" src="data:image/png;base64,' . base64_encode($generator1->getCode("$supplier->code", $generator1::TYPE_CODE_128)) . '">';
					}
				?>
				
				@if (file_exists(public_path() . '/usr/img/supplier/' . $supplier->id . '_' . Str::slug($supplier->name, '_') . '_thumb.jpg'))
					{!!HTML::image('usr/img/supplier/' . $supplier->id . '_' . Str::slug($supplier->name, '_') . '_thumb.jpg?lastmod=' . Str::random(5), '', ['class'=>'view-photo'])!!}
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
										Code
									</td>
									<td class="view-info-mid">
										:
									</td>
									<td>
										{{$supplier->code}}
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
										{{$supplier->phone}}
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
										{{$supplier->address}}
									</td>
								</tr>
								<tr>
									<td>
										Due Date
									</td>
									<td class="view-info-mid">
										:
									</td>
									<td>
										{{$supplier->tempo}} days
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
										{!!$supplier->is_active == 1 ? "<span class='text-green'>Active</span>" : "<span class='text-red'>Not Active</span>"!!}
									</td>
								</tr>
							</table>
						</div>
					</div>
				</div>
				<div class="page-group">
					<div class="page-item col-1">
						<div class="page-item-title">
							Description
						</div>
						<div class="page-item-content view-item-content">
							{!!$supplier->ket!!}
						</div>
					</div>
				</div>
				<div class="view-last-edit">
					<?php
						$createuser = Admin::find($supplier->create_id);
						$updateuser = Admin::find($supplier->update_id);
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
								{{date('l, d F Y G:i:s', strtotime($supplier->created_at))}}
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
								{{date('l, d F Y G:i:s', strtotime($supplier->updated_at))}}
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