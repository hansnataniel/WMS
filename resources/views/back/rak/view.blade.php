<?php
	use Illuminate\Support\Str;

	use App\Models\Admin;
	use App\Models\Rak;
?>

@extends('back.template.master')

@section('title')
	Rak View
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
	Rak View
	<span>
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/dashboard')}}">Dashboard</a> / <a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/rak')}}">Rak</a> / <span>Rak View</span>
	</span>
@endsection

@section('help')
	<ul class="menu-help-list-container">
		<li>
			Gunakan tombol Edit untuk mengedit Rak
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
						<a class="view-button-item view-button-back" href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/rak')}}"></a>
					@endif
					{{$rak->name}}
					<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/rak/' . $rak->id . '/edit')}}" class="view-button-item view-button-edit">
						Edit
					</a>
				</h1>

				@if($rak->code_id != null)
					<div style="position: relative; display: table; text-align: center;">
						<?php
							$generator1 = new Picqer\Barcode\BarcodeGeneratorPNG();
							echo '<img style="margin-bottom: 5px; position: relative; display: block;" src="data:image/png;base64,' . base64_encode($generator1->getBarcode("$rak->code_id", $generator1::TYPE_CODE_128)) . '">';
						?>
						<span style="position: relative; display: block; margin-bottom: 20px; font-size: 14px; font-weight: bold;">
							{{$rak->code_id}}
						</span>
					</div>
				@endif
				
				@if (file_exists(public_path() . '/usr/img/rak/' . $rak->id . '_' . Str::slug($rak->name, '_') . '_thumb.jpg'))
					{!!HTML::image('usr/img/rak/' . $rak->id . '_' . Str::slug($rak->name, '_') . '_thumb.jpg?lastmod=' . Str::random(5), '', ['class'=>'view-photo'])!!}
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
										Gudang
									</td>
									<td class="view-info-mid">
										:
									</td>
									<td>
										<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/gudang/' . $rak->gudang_id)}}" style="color: blue;">
											{{$rak->gudang->name}}
										</a>
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
										{!!$rak->is_active == 1 ? "<span class='text-green'>Active</span>" : "<span class='text-red'>Not Active</span>"!!}
									</td>
								</tr>
							</table>
						</div>
					</div>
				</div>
				<div class="view-last-edit">
					<?php
						$createuser = Admin::find($rak->create_id);
						$updateuser = Admin::find($rak->update_id);
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
								{{date('l, d F Y G:i:s', strtotime($rak->created_at))}}
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
								{{date('l, d F Y G:i:s', strtotime($rak->updated_at))}}
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