<?php
	use Illuminate\Support\Str;

	use App\Models\User;
?>

@extends('back.template.master')

@section('title')
	New User
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
	New User
	<span>
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/dashboard')}}">Dashboard</a> / <a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/user')}}">User</a> / <span>New User</span>
	</span>
@endsection

@section('help')
	<ul class="menu-help-list-container">
		<li>
			Password harus di isi minimal 6 karakter
		</li>
		<li>
			Pastikan isi dari field Password dan Password Confirmation sama
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
					<a class="edit-button-item edit-button-back" href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/user')}}">
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
				{!!Form::model($user, ['url' => URL::to(Crypt::decrypt($setting->admin_url) . '/user'), 'method' => 'POST', 'files' => true])!!}
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
									{!!Form::label('email', 'Email', ['class'=>'edit-form-label'])!!}
									{!!Form::email('email', null, ['class'=>'edit-form-text large', 'required'])!!}
									<span class="edit-form-note">
										*Required, Unique, and Email Format
									</span>
								</div>
								<div class="edit-form-group">
									{!!Form::label('phone', 'Phone', ['class'=>'edit-form-label'])!!}
									{!!Form::input('number', 'phone', null, ['class'=>'edit-form-text large'])!!}
									<span class="edit-form-note">
										*Numeric
									</span>
								</div>
								<div class="edit-form-group">
									{!!Form::label('birthdate', 'Birthdate', ['class'=>'edit-form-label'])!!}
									{!!Form::text('birthdate', null, ['class'=>'edit-form-text large datetimepicker', 'readonly'])!!}
								</div>
								<div class="edit-form-group">
									{!!Form::label('area', 'Area', ['class'=>'edit-form-label'])!!}
									{!!Form::select('area', $area_options, null, ['class'=>'edit-form-text large select'])!!}
									<span class="edit-form-note">
										*Required
									</span>
								</div>
								<div class="edit-form-group">
									{!!Form::label('address', 'Address', ['class'=>'edit-form-label'])!!}
									{!!Form::textarea('address', null, ['class'=>'edit-form-text large area'])!!}
								</div>
								<div class="edit-form-group">
									{!!Form::label('password', 'Password', ['class'=>'edit-form-label'])!!}
									{!!Form::password('password', ['class'=>'edit-form-text large', 'required'])!!}
									<span class="edit-form-note">
										*Required and min 6 Character
									</span>
								</div>
								<div class="edit-form-group">
									{!!Form::label('password_confirmation', 'Password Confirmation', ['class'=>'edit-form-label'])!!}
									{!!Form::password('password_confirmation', ['class'=>'edit-form-text large', 'required'])!!}
									<span class="edit-form-note">
										*Required and must be exactly the same with Password Field
									</span>
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