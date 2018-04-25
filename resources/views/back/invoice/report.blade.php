<?php
	use Illuminate\Support\Str;

	use App\Models\User;
?>

@extends('back.template.master')

@section('title')
	Invoice Debt
@endsection

@section('head_additional')
	{!!HTML::style('css/back/edit.css')!!}
	{!!HTML::style('css/jquery.datetimepicker.css')!!}
@endsection

@section('js_additional')
	{!!HTML::script('js/jquery.datetimepicker.js')!!}
	
	<script>
		$(function(){
		    $('.datetimepicker').datetimepicker({
				timepicker: false,
				format: 'Y-m-d',
				maxDate: 0
			});
		});
	</script>
@endsection

@section('page_title')
	Invoice Debt
	<span>
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/dashboard')}}">Dashboard</a> / <span>Invoice Debt</span>
	</span>
@endsection

@section('help')
	<ul class="menu-help-list-container">
		<li>
			Masukkan Supplier untuk menampilkan report Invoice Debt
		</li>
	</ul>
@endsection

@section('content')
	<div class="page-group">
		<div class="page-item col-1">
			<div class="page-item-content">
				<a class="edit-button-item edit-button-back" href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/dashboard')}}">
					Back
				</a>
				
				<div class="page-item-error-container">
					@foreach ($errors->all() as $error)
						<div class='page-item-error-item'>
							{{$error}}
						</div>
					@endforeach
				</div>
				{!!Form::open(['url' => URL::to(Crypt::decrypt($setting->admin_url) . '/invoice/report'), 'files' => true])!!}
					<div class="page-group">
						<div class="page-item col-1">
							<div class="page-item-title">
								Detail Information
							</div>
							<div class="page-item-content edit-item-content">
								<div class="edit-form-group">
									{!!Form::label('supplier', 'Supplier', ['class'=>'edit-form-label'])!!}
									{!!Form::select('supplier', $supplier_options, null, ['class'=>'edit-form-text medium select'])!!}
									<span class="edit-form-note">
										*Required
									</span>
								</div>
							</div>
						</div>
					</div>
					<div class="page-group">
						<div class="edit-button-group">
							{{Form::submit('Show Report', ['class'=>'edit-button-item'])}}
							{{Form::reset('Reset', ['class'=>'edit-button-item reset'])}}
						</div>
					</div>
				{!!Form::close()!!}
			</div>
		</div>
	</div>
@endsection