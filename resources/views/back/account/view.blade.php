<?php
	use Illuminate\Support\Str;

	use App\Models\Admin;
	use App\Models\Account;
?>

@extends('back.template.master')

@section('title')
	Account View
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
	Account View
	<span>
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/dashboard')}}">Dashboard</a> / <a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/account')}}">Account</a> / <span>Account View</span>
	</span>
@endsection

@section('help')
	<ul class="menu-help-list-container">
		<li>
			Gunakan tombol Edit untuk mengedit Account
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
						<a class="view-button-item view-button-back" href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/account')}}"></a>
					@endif

					{{$account->name}}
					
					<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/account/' . $account->id . '/edit')}}" class="view-button-item view-button-edit">
						Edit
					</a>
				</h1>

				<?php
					if($account->code_id != null)
					{
						$generator1 = new Picqer\Barcode\BarcodeGeneratorPNG();
						echo '<img style="margin-bottom: 20px; position: relative; display: block;" src="data:image/png;base64,' . base64_encode($generator1->getBarcode("$account->code_id", $generator1::TYPE_CODE_128)) . '">';
					}
				?>
				
				@if (file_exists(public_path() . '/usr/img/account/' . $account->id . '_' . Str::slug($account->name, '_') . '_thumb.jpg'))
					{!!HTML::image('usr/img/account/' . $account->id . '_' . Str::slug($account->name, '_') . '_thumb.jpg?lastmod=' . Str::random(5), '', ['class'=>'view-photo'])!!}
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
										Name
									</td>
									<td class="view-info-mid">
										:
									</td>
									<td>
										{{$account->name}}
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
										@if($account->type == 'Income')
											<span class='text-green'>
										@elseif($account->type == 'Expense')
											<span class='text-orange'>
										@elseif($account->type == 'Other Income')
											<span class='text-blue'>
										@elseif($account->type == 'Other Expense')
											<span class='text-red'>
										@endif
											{{$account->type}}
										</span>
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
										{!!$account->is_active == 1 ? "<span class='text-green'>Active</span>" : "<span class='text-red'>Not Active</span>"!!}
									</td>
								</tr>
							</table>
						</div>
					</div>
				</div>
				<div class="view-last-edit">
					<?php
						$createuser = Admin::find($account->create_id);
						$updateuser = Admin::find($account->update_id);
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
								{{date('l, d F Y G:i:s', strtotime($account->created_at))}}
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
								{{date('l, d F Y G:i:s', strtotime($account->updated_at))}}
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