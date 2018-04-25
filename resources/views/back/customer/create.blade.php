<?php
	use Illuminate\Support\Str;

	use App\Models\User;
	use App\Models\Customer;
?>

@extends('back.template.master')

@section('title')
	New Customer
@endsection

@section('head_additional')
	{!!HTML::style('css/back/edit.css')!!}
@endsection

@section('js_additional')
	<script type="text/javascript">
		$(document).ready(function(){
			
		});
	</script>
@endsection

@section('page_title')
	New Customer
	<span>
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/dashboard')}}">Dashboard</a> / <a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/customer')}}">Customer</a> / <span>New Customer</span>
	</span>
@endsection

@section('help')
	<ul class="menu-help-list-container">
		<li>
			Customer akan dijadikan sebagai data pembeli saat ada transaksi
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
					<a class="edit-button-item edit-button-back" href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/customer')}}">
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
				{!!Form::open(['url' => URL::to(Crypt::decrypt($setting->admin_url) . '/customer'), 'files' => true])!!}
					<div class="page-group">
						<div class="page-item col-1">
							<div class="page-item-title">
								Detail Information
							</div>
							<div class="page-item-content edit-item-content">
								<div class="edit-form-group">
									{!!Form::label('code', 'Code', ['class'=>'edit-form-label'])!!}
									{!!Form::text('code', null, ['class'=>'edit-form-text large', 'required', 'autofocus'])!!}
									<span class="edit-form-note">
										*Required and Unique
									</span>
								</div>
								<div class="edit-form-group">
									{!!Form::label('name', 'Name', ['class'=>'edit-form-label'])!!}
									{!!Form::text('name', null, ['class'=>'edit-form-text large', 'required'])!!}
									<span class="edit-form-note">
										*Required
									</span>
								</div>
								<div class="edit-form-group">
									{!!Form::label('email', 'Email', ['class'=>'edit-form-label'])!!}
									{!!Form::text('email', null, ['class'=>'edit-form-text large'])!!}
								</div>
								<div class="edit-form-group">
									{!!Form::label('phone', 'Phone', ['class'=>'edit-form-label'])!!}
									{!!Form::text('phone', null, ['class'=>'edit-form-text large'])!!}
								</div>
								<div class="edit-form-group">
									{!!Form::label('mobile', 'Mobile', ['class'=>'edit-form-label'])!!}
									{!!Form::text('mobile', null, ['class'=>'edit-form-text large'])!!}
								</div>
								<div class="edit-form-group">
									{!!Form::label('fax', 'Fax', ['class'=>'edit-form-label'])!!}
									{!!Form::text('fax', null, ['class'=>'edit-form-text large'])!!}
								</div>
								<div class="edit-form-group">
									{!!Form::label('address', 'Address', ['class'=>'edit-form-label'])!!}
									{!!Form::text('address', null, ['class'=>'edit-form-text large'])!!}
								</div>
								<div class="edit-form-group">
									{!!Form::label('description', 'Description', ['class'=>'edit-form-label'])!!}
									{!!Form::textarea('description', null, ['class'=>'edit-form-text large area'])!!}
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