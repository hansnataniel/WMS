<?php
	use Illuminate\Support\Str;

	use App\Models\Admin;
?>

@extends('back.template.master')

@section('title')
	Edit Profile
@endsection

@section('head_additional')
	{!!HTML::style('css/back/edit.css')!!}
@endsection

@section('js_additional')
	<script type="text/javascript">
		$(function(){
			$('.edit-form-image-delete').click(function(){
		    	var value = $(this).attr('value');
		    	$('.edit-form-image-loading').fadeIn();
		    	$.ajax({
		    		type: 'GET',
		    		url: "{{URL::to(Crypt::decrypt($setting->admin_url) . '/admin/delete-image')}}/"+value,
		    		success: function(msg){
		    			$('.edit-form-image-success').fadeIn().delay(1000).fadeOut(1000);
		    			$('.edit-form-image').delay(2000).animate({'width': '0px', 'opacity': '0'}, 500, 'easeInExpo').slideUp();
		    		},
		    		error: function(msg) {
		    			$('body').html(msg.responseText);
		    		}
		    	});
		    });
		});
	</script>
@endsection

@section('page_title')
	Edit Profile
	<span>
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/dashboard')}}">Dashboard</a> / <span>Edit Profile</span>
	</span>
@endsection

@section('help')
	<ul class="menu-help-list-container">
		<li>
			Kosongkan Field New Password jika Anda tidak ingin merubah password
		</li>
		<li>
			Password di isi minimal 6 karakter
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
					<a class="edit-button-item edit-button-back" href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/admin')}}">
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
				{!!Form::model($admin, ['url' => URL::to(Crypt::decrypt($setting->admin_url) . '/admin/edit-profile'), 'method' => 'POST', 'files' => true])!!}
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
									{!!Form::label('email', 'Email', ['class'=>'edit-form-label'])!!}
									{!!Form::email('email', null, ['class'=>'edit-form-text large', 'required'])!!}
									<span class="edit-form-note">
										*Required, Unique, and Email Format
									</span>
								</div>
								<div class="edit-form-group">
									{!!Form::label('new_password', 'New Password', ['class'=>'edit-form-label'])!!}
									{!!Form::password('new_password', ['class'=>'edit-form-text large'])!!}
									<span class="edit-form-note">
										*Stay null if you don't have change the password and min 6 character
									</span>
								</div>
								<div class="edit-form-group">
									{!!Form::label('new_password_confirmation', 'New Password Confirmation', ['class'=>'edit-form-label'])!!}
									{!!Form::password('new_password_confirmation', ['class'=>'edit-form-text large'])!!}
									<span class="edit-form-note">
										*Required if field new password not null and must be exactly the same with new password field
									</span>
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