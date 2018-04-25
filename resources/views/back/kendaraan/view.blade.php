<?php
	use Illuminate\Support\Str;

	use App\Models\Admin;
	use App\Models\Kendaraan;
?>

@extends('back.template.master')

@section('title')
	Kendaraan View
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
	Kendaraan View
	<span>
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/dashboard')}}">Dashboard</a> / <a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/kendaraan')}}">Kendaraan</a> / <span>Kendaraan View</span>
	</span>
@endsection

@section('help')
	<ul class="menu-help-list-container">
		<li>
			Gunakan tombol Edit untuk mengedit Kendaraan
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
						<a class="view-button-item view-button-back" href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/kendaraan')}}"></a>
					@endif
					
					<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/kendaraan/' . $kendaraan->id . '/edit')}}" class="view-button-item view-button-edit">
						Edit
					</a>
				</h1>

				<?php
					if($kendaraan->code_id != null)
					{
						$generator1 = new Picqer\Barcode\BarcodeGeneratorPNG();
						echo '<img style="margin-bottom: 20px; position: relative; display: block;" src="data:image/png;base64,' . base64_encode($generator1->getBarcode("$kendaraan->code_id", $generator1::TYPE_CODE_128)) . '">';
					}
				?>
				
				@if (file_exists(public_path() . '/usr/img/kendaraan/' . $kendaraan->id . '_' . Str::slug($kendaraan->name, '_') . '_thumb.jpg'))
					{!!HTML::image('usr/img/kendaraan/' . $kendaraan->id . '_' . Str::slug($kendaraan->name, '_') . '_thumb.jpg?lastmod=' . Str::random(5), '', ['class'=>'view-photo'])!!}
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
										Brand
									</td>
									<td class="view-info-mid">
										:
									</td>
									<td>
										{{$kendaraan->brand}}
									</td>
								</tr>
								<tr>
									<td>
										Type
									</td>
									<td class="view-info-mid">
										:
									</td>
									<td>
										{{$kendaraan->type}}
									</td>
								</tr>
								<tr>
									<td>
										Th Start
									</td>
									<td class="view-info-mid">
										:
									</td>
									<td>
										{{$kendaraan->th_start}}
									</td>
								</tr>
								<tr>
									<td>
										Th End
									</td>
									<td class="view-info-mid">
										:
									</td>
									<td>
										{{$kendaraan->th_end}}
									</td>
								</tr>
								<tr>
									<td>
										Transmition
									</td>
									<td class="view-info-mid">
										:
									</td>
									<td>
										{{$kendaraan->transmition}}
									</td>
								</tr>
								<tr>
									<td>
										CC
									</td>
									<td class="view-info-mid">
										:
									</td>
									<td>
										{{$kendaraan->cc}}
									</td>
								</tr>
								<tr>
									<td>
										Code
									</td>
									<td class="view-info-mid">
										:
									</td>
									<td>
										{{$kendaraan->code}}
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
										{!!$kendaraan->is_active == 1 ? "<span class='text-green'>Active</span>" : "<span class='text-red'>Not Active</span>"!!}
									</td>
								</tr>
							</table>
						</div>
					</div>
				</div>
				<div class="view-last-edit">
					<?php
						$createuser = Admin::find($kendaraan->create_id);
						$updateuser = Admin::find($kendaraan->update_id);
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
								{{date('l, d F Y G:i:s', strtotime($kendaraan->created_at))}}
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
								{{date('l, d F Y G:i:s', strtotime($kendaraan->updated_at))}}
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