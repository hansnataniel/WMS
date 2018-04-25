<?php
	use Illuminate\Support\Str;

	use App\Models\Admin;
?>

@extends('back.template.master')

@section('title')
	New Admin Group
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
	New Admin Group
	<span>
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/dashboard')}}">Dashboard</a> / <a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/admingroup')}}">Admin Group</a> / <span>New Admin Group</span>
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
				{!!Form::model($admingroup, ['url' => URL::to(Crypt::decrypt($setting->admin_url) . '/admingroup'), 'method' => 'POST', 'files' => true])!!}
					<div class="page-group">
						<div class="page-item col-1">
							<div class="page-item-title">
								Detail Information
							</div>
							<div class="page-item-content edit-item-content">
								<div class="edit-form-group">
									{!!Form::label('name', 'Name', ['class'=>'edit-form-label'])!!}
									{!!Form::text('name', null, ['class'=>'edit-form-text large', 'required', 'autofocus'])!!}
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
												{!!Form::checkbox('admingroup_c', true, false, ['class'=>'childAll'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('admingroup_r', true, false, ['class'=>'childAll childTrigger'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('admingroup_u', true, false, ['class'=>'childAll childTriggered'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('admingroup_d', true, false, ['class'=>'childAll childTriggered'])!!}
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
												{!!Form::checkbox('admin_c', true, false, ['class'=>'childAll'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('admin_r', true, false, ['class'=>'childAll childTrigger'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('admin_u', true, false, ['class'=>'childAll childTriggered'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('admin_d', true, false, ['class'=>'childAll childTriggered'])!!}
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
												{!!Form::checkbox('gudang_c', true, false, ['class'=>'childAll'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('gudang_r', true, false, ['class'=>'childAll childTrigger'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('gudang_u', true, false, ['class'=>'childAll childTriggered'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('gudang_d', true, false, ['class'=>'childAll childTriggered'])!!}
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
												{!!Form::checkbox('rak_c', true, false, ['class'=>'childAll'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('rak_r', true, false, ['class'=>'childAll childTrigger'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('rak_u', true, false, ['class'=>'childAll childTriggered'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('rak_d', true, false, ['class'=>'childAll childTriggered'])!!}
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
												{!!Form::checkbox('product_c', true, false, ['class'=>'childAll'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('product_r', true, false, ['class'=>'childAll childTrigger'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('product_u', true, false, ['class'=>'childAll childTriggered'])!!}
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
												{!!Form::checkbox('kendaraan_c', true, false, ['class'=>'childAll'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('kendaraan_r', true, false, ['class'=>'childAll childTrigger'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('kendaraan_u', true, false, ['class'=>'childAll childTriggered'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('kendaraan_d', true, false, ['class'=>'childAll childTriggered'])!!}
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
												{!!Form::checkbox('supplier_c', true, false, ['class'=>'childAll'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('supplier_r', true, false, ['class'=>'childAll childTrigger'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('supplier_u', true, false, ['class'=>'childAll childTriggered'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('supplier_d', true, false, ['class'=>'childAll childTriggered'])!!}
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
												{!!Form::checkbox('po_c', true, false, ['class'=>'childAll'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('po_r', true, false, ['class'=>'childAll childTrigger'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('po_u', true, false, ['class'=>'childAll childTriggered'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('po_d', true, false, ['class'=>'childAll childTriggered'])!!}
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
												{!!Form::checkbox('ri_c', true, false, ['class'=>'childAll'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('ri_r', true, false, ['class'=>'childAll childTrigger'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('ri_u', true, false, ['class'=>'childAll childTriggered'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('ri_d', true, false, ['class'=>'childAll childTriggered'])!!}
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
												{!!Form::checkbox('hbt_r', true, false, ['class'=>'childAll childTrigger'])!!}
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
												{!!Form::checkbox('invoice_c', true, false, ['class'=>'childAll'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('invoice_r', true, false, ['class'=>'childAll childTrigger'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('invoice_u', true, false, ['class'=>'childAll childTriggered'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('invoice_d', true, false, ['class'=>'childAll childTriggered'])!!}
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
												{!!Form::checkbox('return_c', true, false, ['class'=>'childAll'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('return_r', true, false, ['class'=>'childAll childTrigger'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('return_u', true, false, ['class'=>'childAll childTriggered'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('return_d', true, false, ['class'=>'childAll childTriggered'])!!}
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
												{!!Form::checkbox('payment_c', true, false, ['class'=>'childAll'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('payment_r', true, false, ['class'=>'childAll childTrigger'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('payment_u', true, false, ['class'=>'childAll childTriggered'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('payment_d', true, false, ['class'=>'childAll childTriggered'])!!}
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
												{!!Form::checkbox('setting_u', true, false, ['class'=>'childAll childTriggered'])!!}
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
												{!!Form::checkbox('productphoto_c', true, false, ['class'=>'childAll'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('productphoto_r', true, false, ['class'=>'childAll childTrigger'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
											</td>
											<td>
												{!!Form::checkbox('productphoto_d', true, false, ['class'=>'childAll childTriggered'])!!}
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
												{!!Form::checkbox('inventory_c', true, false, ['class'=>'childAll'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('inventory_r', true, false, ['class'=>'childAll childTrigger'])!!}
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
												{!!Form::checkbox('bank_c', true, false, ['class'=>'childAll'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('bank_r', true, false, ['class'=>'childAll childTrigger'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('bank_u', true, false, ['class'=>'childAll childTriggered'])!!}
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
												{!!Form::checkbox('adjustment_c', true, false, ['class'=>'childAll'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('adjustment_r', true, false, ['class'=>'childAll childTrigger'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('adjustment_u', true, false, ['class'=>'childAll childTriggered'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('adjustment_d', true, false, ['class'=>'childAll childTriggered'])!!}
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
												{!!Form::checkbox('stockcard_r', true, false, ['class'=>'childAll childTrigger'])!!}
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
												{!!Form::checkbox('customer_c', true, false, ['class'=>'childAll'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('customer_r', true, false, ['class'=>'childAll childTrigger'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('customer_u', true, false, ['class'=>'childAll childTriggered'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('customer_d', true, false, ['class'=>'childAll childTriggered'])!!}
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
												{!!Form::checkbox('transaction_c', true, false, ['class'=>'childAll'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('transaction_r', true, false, ['class'=>'childAll childTrigger'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('transaction_u', true, false, ['class'=>'childAll childTriggered'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('transaction_d', true, false, ['class'=>'childAll childTriggered'])!!}
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
												{!!Form::checkbox('tpayment_c', true, false, ['class'=>'childAll'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('tpayment_r', true, false, ['class'=>'childAll childTrigger'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('tpayment_u', true, false, ['class'=>'childAll childTriggered'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('tpayment_d', true, false, ['class'=>'childAll childTriggered'])!!}
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
												{!!Form::checkbox('treturn_c', true, false, ['class'=>'childAll'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('treturn_r', true, false, ['class'=>'childAll childTrigger'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('treturn_u', true, false, ['class'=>'childAll childTriggered'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('treturn_d', true, false, ['class'=>'childAll childTriggered'])!!}
												<div class="checkClose"></div>
											</td>
										</tr>
										<tr>
											<td>
												Accounts Recievable
											</td>
											<td>
												{!!Form::label('selectall', 'Check All', ['class'=>'question-label checkAll'])!!} / {!!Form::label('selectAll', 'Uncheck All', ['class'=>'question-label uncheckAll'])!!}
											</td>
											<td>
											</td>
											<td>
												{!!Form::checkbox('accountsrecievable_r', true, false, ['class'=>'childAll childTrigger'])!!}
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
												{!!Form::checkbox('account_c', true, false, ['class'=>'childAll'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('account_r', true, false, ['class'=>'childAll childTrigger'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('account_u', true, false, ['class'=>'childAll childTriggered'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('account_d', true, false, ['class'=>'childAll childTriggered'])!!}
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
												{!!Form::checkbox('accountdetail_c', true, false, ['class'=>'childAll'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('accountdetail_r', true, false, ['class'=>'childAll childTrigger'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('accountdetail_u', true, false, ['class'=>'childAll childTriggered'])!!}
												<div class="checkClose"></div>
											</td>
											<td>
												{!!Form::checkbox('accountdetail_d', true, false, ['class'=>'childAll childTriggered'])!!}
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
												{!!Form::checkbox('income_r', true, false, ['class'=>'childAll childTrigger'])!!}
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