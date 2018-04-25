<?php
	use Illuminate\Support\Str;

	use App\Models\Admin;
	use App\Models\Customer;
	use App\Models\Transaction;
?>

@extends('back.template.master')

@section('title')
	Customer View
@endsection

@section('head_additional')
	{!!HTML::style('css/back/detail.css')!!}
	{!!HTML::style('css/back/index.css')!!}

	<style type="text/css">
		.view-subtitle {
			position: relative;
			display: block;
			font-size: 18px;
			color: blue;
			margin-bottom: 10px;
		}

		.view-subtitle span {
			position: relative;
			display: block;
			font-size: 12px;
			color: #f7961f;
		}

		.index-table {
			margin-bottom: 80px;
		}

		.index-table:last-child {
			margin-bottom: 0px;
		}
	</style>
@endsection

@section('js_additional')
	<script type="text/javascript">
		$(document).ready(function(){
			
		});
	</script>
@endsection

@section('page_title')
	Customer View
	<span>
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/dashboard')}}">Dashboard</a> / <a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/customer')}}">Customer</a> / <span>Customer View</span>
	</span>
@endsection

@section('help')
	<ul class="menu-help-list-container">
		<li>
			Gunakan tombol Edit untuk mengedit Customer
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
						<a class="view-button-item view-button-back" href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/customer')}}"></a>
					@endif
					{{$customer->name}}
					<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/customer/' . $customer->id . '/edit')}}" class="view-button-item view-button-edit">
						Edit
					</a>
				</h1>

				<?php
					if($customer->code != null)
					{
						// $generator1 = new Picqer\Code\CodeGeneratorPNG();
						// echo '<img style="margin-bottom: 20px; position: relative; display: block;" src="data:image/png;base64,' . base64_encode($generator1->getCode("$customer->code", $generator1::TYPE_CODE_128)) . '">';
					}
				?>
				
				@if (file_exists(public_path() . '/usr/img/customer/' . $customer->id . '_' . Str::slug($customer->name, '_') . '_thumb.jpg'))
					{!!HTML::image('usr/img/customer/' . $customer->id . '_' . Str::slug($customer->name, '_') . '_thumb.jpg?lastmod=' . Str::random(5), '', ['class'=>'view-photo'])!!}
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
										Code
									</td>
									<td class="view-info-mid">
										:
									</td>
									<td>
										{{$customer->code}}
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
										{{$customer->email}}
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
										{{$customer->phone}}
									</td>
								</tr>
								<tr>
									<td>
										Mobile
									</td>
									<td class="view-info-mid">
										:
									</td>
									<td>
										{{$customer->mobile}}
									</td>
								</tr>
								<tr>
									<td>
										Fax
									</td>
									<td class="view-info-mid">
										:
									</td>
									<td>
										{{$customer->fax}}
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
										{{$customer->address}}
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
										{!!$customer->is_active == 1 ? "<span class='text-green'>Active</span>" : "<span class='text-red'>Not Active</span>"!!}
									</td>
								</tr>
							</table>
						</div>
					</div>
					<div class="page-item col-2-4">
						<div class="page-item-title">
							Description
						</div>
						<div class="page-item-content view-item-content">
							{!!$customer->ket!!}
						</div>
					</div>
				</div>
				<div class="page-group">
					<div class="page-item col-1">
						<div class="page-item-title">
							Transaction Detail
						</div>
						<div class="page-item-content view-item-content">
							<?php
								$transactions = Transaction::where('customer_id', '=', $customer->id)->get();
							?>
							@if(!$transactions->isEmpty())
								@foreach($transactions as $transaction)
									<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/transaction/' . $transaction->id)}}" class="view-subtitle">
										{{$transaction->trans_id}}
										<span>
											{{date('l, d F Y G:i:s', strtotime($transaction->created_at))}}
										</span>
									</a>
									<table class="index-table">
										<tr class="index-tr-title">
											<th>
												#
											</th>
											<th>
												Product
											</th>
											<th>
												Qty
											</th>
											<th style="text-align: right;">
												Price
											</th>
											<th style="text-align: right;">
												Discount
											</th>
											<th style="text-align: right;">
												Sub Price
											</th>
										</tr>
										<?php
											$counter = 0;
											$total = 0;
										?>
										
									</table>
								@endforeach
							@else
								<span style="font-size: 12px; color: red; position: relative; display: block;">
									<i>This Customer don't have transaction yet</i>
								</span>
							@endif
						</div>
					</div>
				</div>
				<div class="view-last-edit">
					<?php
						$createuser = Admin::find($customer->create_id);
						$updateuser = Admin::find($customer->update_id);
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
								{{date('l, d F Y G:i:s', strtotime($customer->created_at))}}
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
								{{date('l, d F Y G:i:s', strtotime($customer->updated_at))}}
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