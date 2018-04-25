<?php
	use Illuminate\Support\Str;

	use App\Models\Admin;
?>

@extends('back.template.master')

@section('title')
	Admin Group Edit
@endsection

@section('head_additional')
	{!!HTML::style('css/back/edit.css')!!}
	{!!HTML::style('css/back/index.css')!!}
@endsection

@section('js_additional')
	<script type="text/javascript">
		$(function(){
			$('.checkAll').click(function(){
		    	$(this).parent().parent().find('.childAll').attr('checked', true);
		    	$(this).parent().parent().find('.childTriggered').attr('disabled', false);
		   	});

		   	$('.uncheckAll').click(function(){
		    	$(this).parent().parent().find('.childAll').attr('checked', false);
		    	$(this).parent().parent().find('.childTriggered').attr('disabled', true);
		   	});

		   	$('.childTrigger').click(function(){
		    	if (!$(this).is(':checked'))
		    	{
		     		$(this).parent().parent().find('.childTriggered').attr('checked', false).attr('disabled', true);
		    	}
		    	if ($(this).is(':checked'))
		    	{
		     		$(this).parent().parent().find('.childTriggered').attr('disabled', false);
		    	}
		   });
		});
	</script>
@endsection

@section('page_title')
	Admin Group Edit
	<span>
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/dashboard')}}">Dashboard</a> / <a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/admingroup')}}">Admin Group</a> / <span>Admin Group Edit</span>
	</span>
@endsection

@section('help')
	<ul class="menu-help-list-container">
		<li>
			Admingroup digunakan untuk mengelompokkan admin berdasarkan hak akses di back end
		</li>
	</ul>
@endsection

@section('content')
	<div class="page-group">
		<div class="page-item col-1">
			<div class="page-item-content">
				@if($request->session()->has('last_url'))
					<a class="edit-button-item edit-button-back" href="{{URL::to($request->session()->get('last_url'))}}">
						Back
					</a>
				@else
					<a class="edit-button-item edit-button-back" href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/admingroup')}}">
						Back
					</a>
				@endif
				
				<div class="page-item-error-container">
					@foreach ($errors->all() as $error)
						<div class='page-item-error-item'>
							{{$error}}
						</div>
					@endforeach
				</div>
				{!!Form::model($admingroup, ['url' => URL::to(Crypt::decrypt($setting->admin_url) . '/admingroup/' . $admingroup->id), 'method' => 'PUT', 'files' => true])!!}
					<div class="page-group">
						<div class="page-item col-1">
							<div class="page-item-title">
								Detail Information
							</div>
							<div class="page-item-content edit-item-content">
								<div class="edit-form-group">
									{!!Form::label('name', 'Name', ['class'=>'edit-form-label'])!!}
									{!!Form::text('name', null, ['class'=>'edit-form-text large', 'required'])!!}
									<span class="edit-form-note">
										*Required
									</span>
								</div>

								<div class="edit-form-group">
									<table class="index-table" style="border-top: 1px solid #d2d2d2; border-bottom: 1px solid #d2d2d2;">
										<tr class="index-tr-title">
											<th>
												Permissions
											</th>
											<th>
												Select All
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
												{!!Form::label('selectall', 'Check All', ['class'=>'question-label checkAll'])!!} / {!!Form::label('selectAll', 'Uncheck All', ['class'=>'question-label uncheckAll'])!!}
											</td>
											<td>
												{!!Form::checkbox('admingroup_c', true, $admingroup->admingroup_c, ['class'=>'childAll'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('admingroup_r', true, $admingroup->admingroup_r, ['class'=>'childAll childTrigger'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('admingroup_u', true, $admingroup->admingroup_u, ['class'=>'childAll childTriggered'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('admingroup_d', true, $admingroup->admingroup_d, ['class'=>'childAll childTriggered'])!!}
												<div class="checkClose"></div>
											</td>
										</tr>
										<tr>
											<td>
												Admin
											</td>
											<td>
												{!!Form::label('selectall', 'Check All', ['class'=>'question-label checkAll'])!!} / {!!Form::label('selectAll', 'Uncheck All', ['class'=>'question-label uncheckAll'])!!}
											</td>
											<td>
												{!!Form::checkbox('admin_c', true, $admingroup->admin_c, ['class'=>'childAll'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('admin_r', true, $admingroup->admin_r, ['class'=>'childAll childTrigger'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('admin_u', true, $admingroup->admin_u, ['class'=>'childAll childTriggered'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('admin_d', true, $admingroup->admin_d, ['class'=>'childAll childTriggered'])!!}
												<div class="checkClose"></div>
											</td>
										</tr>
										<tr>
											<td>
												Gudang
											</td>
											<td>
												{!!Form::label('selectall', 'Check All', ['class'=>'question-label checkAll'])!!} / {!!Form::label('selectAll', 'Uncheck All', ['class'=>'question-label uncheckAll'])!!}
											</td>
											<td>
												{!!Form::checkbox('gudang_c', true, $admingroup->gudang_c, ['class'=>'childAll'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('gudang_r', true, $admingroup->gudang_r, ['class'=>'childAll childTrigger'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('gudang_u', true, $admingroup->gudang_u, ['class'=>'childAll childTriggered'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('gudang_d', true, $admingroup->gudang_d, ['class'=>'childAll childTriggered'])!!}
												<div class="checkClose"></div>
											</td>
										</tr>
										<tr>
											<td>
												Rak
											</td>
											<td>
												{!!Form::label('selectall', 'Check All', ['class'=>'question-label checkAll'])!!} / {!!Form::label('selectAll', 'Uncheck All', ['class'=>'question-label uncheckAll'])!!}
											</td>
											<td>
												{!!Form::checkbox('rak_c', true, $admingroup->rak_c, ['class'=>'childAll'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('rak_r', true, $admingroup->rak_r, ['class'=>'childAll childTrigger'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('rak_u', true, $admingroup->rak_u, ['class'=>'childAll childTriggered'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('rak_d', true, $admingroup->rak_d, ['class'=>'childAll childTriggered'])!!}
												<div class="checkClose"></div>
											</td>
										</tr>
										<tr>
											<td>
												Product
											</td>
											<td>
												{!!Form::label('selectall', 'Check All', ['class'=>'question-label checkAll'])!!} / {!!Form::label('selectAll', 'Uncheck All', ['class'=>'question-label uncheckAll'])!!}
											</td>
											<td>
												{!!Form::checkbox('product_c', true, $admingroup->product_c, ['class'=>'childAll'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('product_r', true, $admingroup->product_r, ['class'=>'childAll childTrigger'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('product_u', true, $admingroup->product_u, ['class'=>'childAll childTriggered'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
											</td>
										</tr>
										<tr>
											<td>
												Kendaraan
											</td>
											<td>
												{!!Form::label('selectall', 'Check All', ['class'=>'question-label checkAll'])!!} / {!!Form::label('selectAll', 'Uncheck All', ['class'=>'question-label uncheckAll'])!!}
											</td>
											<td>
												{!!Form::checkbox('kendaraan_c', true, $admingroup->kendaraan_c, ['class'=>'childAll'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('kendaraan_r', true, $admingroup->kendaraan_r, ['class'=>'childAll childTrigger'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('kendaraan_u', true, $admingroup->kendaraan_u, ['class'=>'childAll childTriggered'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('kendaraan_d', true, $admingroup->kendaraan_d, ['class'=>'childAll childTriggered'])!!}
												<div class="checkClose"></div>
											</td>
										</tr>
										<tr>
											<td>
												Supplier
											</td>
											<td>
												{!!Form::label('selectall', 'Check All', ['class'=>'question-label checkAll'])!!} / {!!Form::label('selectAll', 'Uncheck All', ['class'=>'question-label uncheckAll'])!!}
											</td>
											<td>
												{!!Form::checkbox('supplier_c', true, $admingroup->supplier_c, ['class'=>'childAll'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('supplier_r', true, $admingroup->supplier_r, ['class'=>'childAll childTrigger'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('supplier_u', true, $admingroup->supplier_u, ['class'=>'childAll childTriggered'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('supplier_d', true, $admingroup->supplier_d, ['class'=>'childAll childTriggered'])!!}
												<div class="checkClose"></div>
											</td>
										</tr>
										<tr>
											<td>
												Purchase Order
											</td>
											<td>
												{!!Form::label('selectall', 'Check All', ['class'=>'question-label checkAll'])!!} / {!!Form::label('selectAll', 'Uncheck All', ['class'=>'question-label uncheckAll'])!!}
											</td>
											<td>
												{!!Form::checkbox('po_c', true, $admingroup->po_c, ['class'=>'childAll'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('po_r', true, $admingroup->po_r, ['class'=>'childAll childTrigger'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('po_u', true, $admingroup->po_u, ['class'=>'childAll childTriggered'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('po_d', true, $admingroup->po_d, ['class'=>'childAll childTriggered'])!!}
												<div class="checkClose"></div>
											</td>
										</tr>
										<tr>
											<td>
												Recieve Item
											</td>
											<td>
												{!!Form::label('selectall', 'Check All', ['class'=>'question-label checkAll'])!!} / {!!Form::label('selectAll', 'Uncheck All', ['class'=>'question-label uncheckAll'])!!}
											</td>
											<td>
												{!!Form::checkbox('ri_c', true, $admingroup->ri_c, ['class'=>'childAll'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('ri_r', true, $admingroup->ri_r, ['class'=>'childAll childTrigger'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('ri_u', true, $admingroup->ri_u, ['class'=>'childAll childTriggered'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('ri_d', true, $admingroup->ri_d, ['class'=>'childAll childTriggered'])!!}
												<div class="checkClose"></div>
											</td>
										</tr>
										<tr>
											<td>
												Uninvoiced Debt
											</td>
											<td>
												{!!Form::label('selectall', 'Check All', ['class'=>'question-label checkAll'])!!} / {!!Form::label('selectAll', 'Uncheck All', ['class'=>'question-label uncheckAll'])!!}
											</td>
											<td>
											</td>
											<td>
												{!!Form::checkbox('hbt_r', true, $admingroup->hbt_r, ['class'=>'childAll childTrigger'])!!}
												<div class="checkClose"></div>
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
												{!!Form::label('selectall', 'Check All', ['class'=>'question-label checkAll'])!!} / {!!Form::label('selectAll', 'Uncheck All', ['class'=>'question-label uncheckAll'])!!}
											</td>
											<td>
												{!!Form::checkbox('invoice_c', true, $admingroup->invoice_c, ['class'=>'childAll'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('invoice_r', true, $admingroup->invoice_r, ['class'=>'childAll childTrigger'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('invoice_u', true, $admingroup->invoice_u, ['class'=>'childAll childTriggered'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('invoice_d', true, $admingroup->invoice_d, ['class'=>'childAll childTriggered'])!!}
												<div class="checkClose"></div>
											</td>
										</tr>
										<tr>
											<td>
												Order Return
											</td>
											<td>
												{!!Form::label('selectall', 'Check All', ['class'=>'question-label checkAll'])!!} / {!!Form::label('selectAll', 'Uncheck All', ['class'=>'question-label uncheckAll'])!!}
											</td>
											<td>
												{!!Form::checkbox('return_c', true, $admingroup->return_c, ['class'=>'childAll'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('return_r', true, $admingroup->return_r, ['class'=>'childAll childTrigger'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('return_u', true, $admingroup->return_u, ['class'=>'childAll childTriggered'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('return_d', true, $admingroup->return_d, ['class'=>'childAll childTriggered'])!!}
												<div class="checkClose"></div>
											</td>
										</tr>
										<tr>
											<td>
												Order Payment
											</td>
											<td>
												{!!Form::label('selectall', 'Check All', ['class'=>'question-label checkAll'])!!} / {!!Form::label('selectAll', 'Uncheck All', ['class'=>'question-label uncheckAll'])!!}
											</td>
											<td>
												{!!Form::checkbox('payment_c', true, $admingroup->payment_c, ['class'=>'childAll'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('payment_r', true, $admingroup->payment_r, ['class'=>'childAll childTrigger'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('payment_u', true, $admingroup->payment_u, ['class'=>'childAll childTriggered'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('payment_d', true, $admingroup->payment_d, ['class'=>'childAll childTriggered'])!!}
												<div class="checkClose"></div>
											</td>
										</tr>
										<tr>
											<td>
												Setting
											</td>
											<td>
												{!!Form::label('selectall', 'Check All', ['class'=>'question-label checkAll'])!!} / {!!Form::label('selectAll', 'Uncheck All', ['class'=>'question-label uncheckAll'])!!}
											</td>
											<td>
											</td>
											<td>
											</td>
											<td>
												{!!Form::checkbox('setting_u', true, $admingroup->setting_u, ['class'=>'childAll childTriggered'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
											</td>
										</tr>
										<tr>
											<td>
												Product Photo
											</td>
											<td>
												{!!Form::label('selectall', 'Check All', ['class'=>'question-label checkAll'])!!} / {!!Form::label('selectAll', 'Uncheck All', ['class'=>'question-label uncheckAll'])!!}
											</td>
											<td>
												{!!Form::checkbox('productphoto_c', true, $admingroup->productphoto_c, ['class'=>'childAll'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('productphoto_r', true, $admingroup->productphoto_r, ['class'=>'childAll childTrigger'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
											</td>
											<td>
												{!!Form::checkbox('productphoto_d', true, $admingroup->productphoto_d, ['class'=>'childAll childTriggered'])!!}
												<div class="checkClose"></div>
											</td>
										</tr>
										<tr>
											<td>
												Inventory
											</td>
											<td>
												{!!Form::label('selectall', 'Check All', ['class'=>'question-label checkAll'])!!} / {!!Form::label('selectAll', 'Uncheck All', ['class'=>'question-label uncheckAll'])!!}
											</td>
											<td>
												{!!Form::checkbox('inventory_c', true, $admingroup->inventory_c, ['class'=>'childAll'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('inventory_r', true, $admingroup->inventory_r, ['class'=>'childAll childTrigger'])!!}
												<div class="checkClose"></div>
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
												{!!Form::label('selectall', 'Check All', ['class'=>'question-label checkAll'])!!} / {!!Form::label('selectAll', 'Uncheck All', ['class'=>'question-label uncheckAll'])!!}
											</td>
											<td>
												{!!Form::checkbox('bank_c', true, $admingroup->bank_c, ['class'=>'childAll'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('bank_r', true, $admingroup->bank_r, ['class'=>'childAll childTrigger'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('bank_u', true, $admingroup->bank_u, ['class'=>'childAll childTriggered'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
											</td>
										</tr>
										<tr>
											<td>
												Adjustment
											</td>
											<td>
												{!!Form::label('selectall', 'Check All', ['class'=>'question-label checkAll'])!!} / {!!Form::label('selectAll', 'Uncheck All', ['class'=>'question-label uncheckAll'])!!}
											</td>
											<td>
												{!!Form::checkbox('adjustment_c', true, $admingroup->adjustment_c, ['class'=>'childAll'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('adjustment_r', true, $admingroup->adjustment_r, ['class'=>'childAll childTrigger'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('adjustment_u', true, $admingroup->adjustment_u, ['class'=>'childAll childTriggered'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('adjustment_d', true, $admingroup->adjustment_d, ['class'=>'childAll childTriggered'])!!}
												<div class="checkClose"></div>
											</td>
										</tr>
										<tr>
											<td>
												Stock Card
											</td>
											<td>
												{!!Form::label('selectall', 'Check All', ['class'=>'question-label checkAll'])!!} / {!!Form::label('selectAll', 'Uncheck All', ['class'=>'question-label uncheckAll'])!!}
											</td>
											<td>
											</td>
											<td>
												{!!Form::checkbox('stockcard_r', true, $admingroup->stockcard_r, ['class'=>'childAll childTrigger'])!!}
												<div class="checkClose"></div>
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
												{!!Form::label('selectall', 'Check All', ['class'=>'question-label checkAll'])!!} / {!!Form::label('selectAll', 'Uncheck All', ['class'=>'question-label uncheckAll'])!!}
											</td>
											<td>
												{!!Form::checkbox('customer_c', true, $admingroup->customer_c, ['class'=>'childAll'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('customer_r', true, $admingroup->customer_r, ['class'=>'childAll childTrigger'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('customer_u', true, $admingroup->customer_u, ['class'=>'childAll childTriggered'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('customer_d', true, $admingroup->customer_d, ['class'=>'childAll childTriggered'])!!}
												<div class="checkClose"></div>
											</td>
										</tr>
										<tr>
											<td>
												Transaction
											</td>
											<td>
												{!!Form::label('selectall', 'Check All', ['class'=>'question-label checkAll'])!!} / {!!Form::label('selectAll', 'Uncheck All', ['class'=>'question-label uncheckAll'])!!}
											</td>
											<td>
												{!!Form::checkbox('transaction_c', true, $admingroup->transaction_c, ['class'=>'childAll'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('transaction_r', true, $admingroup->transaction_r, ['class'=>'childAll childTrigger'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('transaction_u', true, $admingroup->transaction_u, ['class'=>'childAll childTriggered'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('transaction_d', true, $admingroup->transaction_d, ['class'=>'childAll childTriggered'])!!}
												<div class="checkClose"></div>
											</td>
										</tr>
										<tr>
											<td>
												Transaction Payment
											</td>
											<td>
												{!!Form::label('selectall', 'Check All', ['class'=>'question-label checkAll'])!!} / {!!Form::label('selectAll', 'Uncheck All', ['class'=>'question-label uncheckAll'])!!}
											</td>
											<td>
												{!!Form::checkbox('tpayment_c', true, $admingroup->tpayment_c, ['class'=>'childAll'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('tpayment_r', true, $admingroup->tpayment_r, ['class'=>'childAll childTrigger'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('tpayment_u', true, $admingroup->tpayment_u, ['class'=>'childAll childTriggered'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('tpayment_d', true, $admingroup->tpayment_d, ['class'=>'childAll childTriggered'])!!}
												<div class="checkClose"></div>
											</td>
										</tr>
										<tr>
											<td>
												Transaction Return
											</td>
											<td>
												{!!Form::label('selectall', 'Check All', ['class'=>'question-label checkAll'])!!} / {!!Form::label('selectAll', 'Uncheck All', ['class'=>'question-label uncheckAll'])!!}
											</td>
											<td>
												{!!Form::checkbox('treturn_c', true, $admingroup->treturn_c, ['class'=>'childAll'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('treturn_r', true, $admingroup->treturn_r, ['class'=>'childAll childTrigger'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('treturn_u', true, $admingroup->treturn_u, ['class'=>'childAll childTriggered'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('treturn_d', true, $admingroup->treturn_d, ['class'=>'childAll childTriggered'])!!}
												<div class="checkClose"></div>
											</td>
										</tr>
										<tr>
											<td>
												Other Expend / Revenue
											</td>
											<td>
												{!!Form::label('selectall', 'Check All', ['class'=>'question-label checkAll'])!!} / {!!Form::label('selectAll', 'Uncheck All', ['class'=>'question-label uncheckAll'])!!}
											</td>
											<td>
											</td>
											<td>
												{!!Form::checkbox('accountsrecievable_r', true, $admingroup->accountsrecievable_r, ['class'=>'childAll childTrigger'])!!}
												<div class="checkClose"></div>
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
												{!!Form::label('selectall', 'Check All', ['class'=>'question-label checkAll'])!!} / {!!Form::label('selectAll', 'Uncheck All', ['class'=>'question-label uncheckAll'])!!}
											</td>
											<td>
												{!!Form::checkbox('account_c', true, $admingroup->account_c, ['class'=>'childAll'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('account_r', true, $admingroup->account_r, ['class'=>'childAll childTrigger'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('account_u', true, $admingroup->account_u, ['class'=>'childAll childTriggered'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('account_d', true, $admingroup->account_d, ['class'=>'childAll childTriggered'])!!}
												<div class="checkClose"></div>
											</td>
										</tr>
										<tr>
											<td>
												Accountdetail
											</td>
											<td>
												{!!Form::label('selectall', 'Check All', ['class'=>'question-label checkAll'])!!} / {!!Form::label('selectAll', 'Uncheck All', ['class'=>'question-label uncheckAll'])!!}
											</td>
											<td>
												{!!Form::checkbox('accountdetail_c', true, $admingroup->accountdetail_c, ['class'=>'childAll'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('accountdetail_r', true, $admingroup->accountdetail_r, ['class'=>'childAll childTrigger'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('accountdetail_u', true, $admingroup->accountdetail_u, ['class'=>'childAll childTriggered'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('accountdetail_d', true, $admingroup->accountdetail_d, ['class'=>'childAll childTriggered'])!!}
												<div class="checkClose"></div>
											</td>
										</tr>
										<tr>
											<td>
												Income
											</td>
											<td>
												{!!Form::label('selectall', 'Check All', ['class'=>'question-label checkAll'])!!} / {!!Form::label('selectAll', 'Uncheck All', ['class'=>'question-label uncheckAll'])!!}
											</td>
											<td>
											</td>
											<td>
												{!!Form::checkbox('income_r', true, $admingroup->income_r, ['class'=>'childAll childTrigger'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
											</td>
											<td>
											</td>
										</tr>
									</table>
								</div>

								<div class="edit-form-group">
									{!!Form::label('is_active', 'Active Status', ['class'=>'edit-form-label'])!!}
									<div class="edit-form-radio-group">
										<div class="edit-form-radio-item">
											{!!Form::radio('is_active', 1, true, ['class'=>'edit-form-radio', 'id'=>'true'])!!} 
											{!!Form::label('true', 'Active', ['class'=>'edit-form-radio-label'])!!}
										</div>
										<div class="edit-form-radio-item">
											{!!Form::radio('is_active', 0, false, ['class'=>'edit-form-radio', 'id'=>'false'])!!} 
											{!!Form::label('false', 'Not Active', ['class'=>'edit-form-radio-label'])!!}
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="page-group">
						<div class="edit-button-group">
							{{Form::submit('Save', ['class'=>'edit-button-item'])}}
							{{Form::reset('Reset', ['class'=>'edit-button-item reset'])}}
						</div>
					</div>
				{!!Form::close()!!}
			</div>
		</div>
	</div>
@endsection