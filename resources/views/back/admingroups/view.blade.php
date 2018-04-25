<?php
	use Illuminate\Support\Str;

	use App\Models\Admin;
?>

@extends('back.template.master')

@section('title')
	Admin Group View
@endsection

@section('head_additional')
	{!!HTML::style('css/back/detail.css')!!}
	{!!HTML::style('css/back/index.css')!!}
@endsection

@section('js_additional')
	<script type="text/javascript">
		$(document).ready(function(){
			
		});
	</script>
@endsection

@section('page_title')
	Admin Group View
	<span>
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/dashboard')}}">Dashboard</a> / <a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/admingroup')}}">Admin Group</a> / <span>Admin Group View</span>
	</span>
@endsection

@section('help')
	<ul class="menu-help-list-container">
		<li>
			Gunakan tombol Edit untuk mengedit Admin Group
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
						<a class="view-button-item view-button-back" href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/admingroup')}}"></a>
					@endif
					{{$admingroup->name}}
					<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/admingroup/' . $admingroup->id . '/edit')}}" class="view-button-item view-button-edit">
						Edit
					</a>
				</h1>
				
				@if (file_exists(public_path() . '/usr/img/admingroup/' . $admingroup->id . '_' . Str::slug($admingroup->title, '_') . '_thumb.jpg'))
					{!!HTML::image('usr/img/admingroup/' . $admingroup->id . '_' . Str::slug($admingroup->title, '_') . '_thumb.jpg?lastmod=' . Str::random(5), '', ['class'=>'view-photo'])!!}
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
										Active Status
									
									</td>
									<td class="view-info-mid">
										:
									
									</td>
									<td>
										{!!$admingroup->is_active == 1 ? "<span class='text-green'>Active</span>" : "<span class='text-red'>Not Active</span>"!!}
									
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
							<table class="index-table">
								<tr class="index-tr-title">
									<th>
										Permissions
									</th>
									<th>
										Create
									</th>
									<th>
										Read
									</th>
									<th>
										Update
									</th>
									<th>
										Delete
									</th>
								</tr>
								<tr>
									<td>
										Admin Group
									</td>
									<td>
										{!!$admingroup->admingroup_c == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
										{!!$admingroup->admingroup_r == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
										{!!$admingroup->admingroup_u == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
										{!!$admingroup->admingroup_d == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
								</tr>
								<tr>
									<td>
										Admin
									</td>
									<td>
										{!!$admingroup->admin_c == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
										{!!$admingroup->admin_r == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
										{!!$admingroup->admin_u == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
										{!!$admingroup->admin_d == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
								</tr>
								<tr>
									<td>
										Gudang
									</td>
									<td>
										{!!$admingroup->gudang_c == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
										{!!$admingroup->gudang_r == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
										{!!$admingroup->gudang_u == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
										{!!$admingroup->gudang_d == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
								</tr>
								<tr>
									<td>
										Rak
									</td>
									<td>
										{!!$admingroup->rak_c == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
										{!!$admingroup->rak_r == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
										{!!$admingroup->rak_u == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
										{!!$admingroup->rak_d == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
								</tr>
								<tr>
									<td>
										Product
									</td>
									<td>
										{!!$admingroup->product_c == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
										{!!$admingroup->product_r == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
										{!!$admingroup->product_u == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
									</td>
								</tr>
								<tr>
									<td>
										Kendaraan
									</td>
									<td>
										{!!$admingroup->kendaraan_c == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
										{!!$admingroup->kendaraan_r == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
										{!!$admingroup->kendaraan_u == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
										{!!$admingroup->kendaraan_d == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
								</tr>
								<tr>
									<td>
										Supplier
									</td>
									<td>
										{!!$admingroup->supplier_c == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
										{!!$admingroup->supplier_r == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
										{!!$admingroup->supplier_u == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
										{!!$admingroup->supplier_d == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
								</tr>
								<tr>
									<td>
										Purchase Order
									</td>
									<td>
										{!!$admingroup->po_c == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
										{!!$admingroup->po_r == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
										{!!$admingroup->po_u == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
										{!!$admingroup->po_d == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
								</tr>
								<tr>
									<td>
										Recieve Item
									</td>
									<td>
										{!!$admingroup->ri_c == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
										{!!$admingroup->ri_r == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
										{!!$admingroup->ri_u == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
										{!!$admingroup->ri_d == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
								</tr>
								<tr>
									<td>
										Uninvoiced Debt
									</td>
									<td>
									</td>
									<td>
										{!!$admingroup->hbt_r == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
									</td>
									<td>
									</td>
								</tr>
								<tr>
									<td>
										Invoice
									</td>
									<td>
										{!!$admingroup->invoice_c == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
										{!!$admingroup->invoice_r == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
										{!!$admingroup->invoice_u == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
										{!!$admingroup->invoice_d == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
								</tr>
								<tr>
									<td>
										Order Return
									</td>
									<td>
										{!!$admingroup->return_c == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
										{!!$admingroup->return_r == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
										{!!$admingroup->return_u == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
										{!!$admingroup->return_d == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
								</tr>
								<tr>
									<td>
										Order Payment
									</td>
									<td>
										{!!$admingroup->payment_c == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
										{!!$admingroup->payment_r == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
										{!!$admingroup->payment_u == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
										{!!$admingroup->payment_d == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
								</tr>
								<tr>
									<td>
										Setting
									</td>
									<td>
									</td>
									<td>
									</td>
									<td>
										{!!$admingroup->setting_u == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
									</td>
								</tr>
								<tr>
									<td>
										Product Photo
									</td>
									<td>
										{!!$admingroup->productphoto_c == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
										{!!$admingroup->productphoto_r == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
									</td>
									<td>
										{!!$admingroup->productphoto_d == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
								</tr>
								<tr>
									<td>
										Inventory
									</td>
									<td>
										{!!$admingroup->inventory_c == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
										{!!$admingroup->inventory_r == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
									</td>
									<td>
									</td>
								</tr>
								<tr>
									<td>
										Bank
									</td>
									<td>
										{!!$admingroup->bank_c == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
										{!!$admingroup->bank_r == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
										{!!$admingroup->bank_u == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
									</td>
								</tr>
								<tr>
									<td>
										Adjustment
									</td>
									<td>
										{!!$admingroup->adjustment_c == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
										{!!$admingroup->adjustment_r == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
										{!!$admingroup->adjustment_u == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
										{!!$admingroup->adjustment_d == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
								</tr>
								<tr>
									<td>
										Stock Card
									</td>
									<td>
									</td>
									<td>
										{!!$admingroup->stockcard_r == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
									</td>
									<td>
									</td>
								</tr>
								<tr>
									<td>
										Customer
									</td>
									<td>
										{!!$admingroup->customer_c == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
										{!!$admingroup->customer_r == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
										{!!$admingroup->customer_u == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
										{!!$admingroup->customer_d == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
								</tr>
								<tr>
									<td>
										Transaction
									</td>
									<td>
										{!!$admingroup->transaction_c == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
										{!!$admingroup->transaction_r == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
										{!!$admingroup->transaction_u == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
										{!!$admingroup->transaction_d == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
								</tr>
								<tr>
									<td>
										Transaction Payment
									</td>
									<td>
										{!!$admingroup->tpayment_c == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
										{!!$admingroup->tpayment_r == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
										{!!$admingroup->tpayment_u == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
										{!!$admingroup->tpayment_d == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
								</tr>
								<tr>
									<td>
										Transaction Return
									</td>
									<td>
										{!!$admingroup->treturn_c == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
										{!!$admingroup->treturn_r == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
										{!!$admingroup->treturn_u == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
										{!!$admingroup->treturn_d == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
								</tr>
								<tr>
									<td>
										Accounts Recievable
									</td>
									<td>
									</td>
									<td>
										{!!$admingroup->accountsrecievable_r == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
									</td>
									<td>
									</td>
								</tr>
								<tr>
									<td>
										Account
									</td>
									<td>
										{!!$admingroup->account_c == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
										{!!$admingroup->account_r == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
										{!!$admingroup->account_u == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
										{!!$admingroup->account_d == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
								</tr>
								<tr>
									<td>
										Account Detail
									</td>
									<td>
										{!!$admingroup->accountdetail_c == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
										{!!$admingroup->accountdetail_r == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
										{!!$admingroup->accountdetail_u == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
										{!!$admingroup->accountdetail_d == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
								</tr>
								<tr>
									<td>
										Income
									</td>
									<td>
									</td>
									<td>
										{!!$admingroup->income_r == 1 ? "<span class='text-green'>Yes</span>" : "<span class='text-red'>No</span>"!!}
									</td>
									<td>
									</td>
									<td>
									</td>
								</tr>
							</table>
						</div>
					</div>
				</div>
				<div class="view-last-edit">
					<?php
						$createadmin = Admin::find($admingroup->create_id);
						$updateadmin = Admin::find($admingroup->update_id);
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
								{{date('l, d F Y G:i:s', strtotime($admingroup->created_at))}}
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
								{{date('l, d F Y G:i:s', strtotime($admingroup->updated_at))}}
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
				</div>
			</div>
		</div>
	</div>
@endsection