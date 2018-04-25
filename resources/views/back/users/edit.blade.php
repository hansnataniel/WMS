<?php
	use Illuminate\Support\Str;

	use App\Models\User;
?>

@extends('back.template.master')

@section('title')
	User Edit
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

	<script type="text/javascript">
		$(function(){
			$('.edit-form-image-delete').click(function(){
		    	var value = $(this).attr('value');
		    	$('.edit-form-image-loading').fadeIn();
		    	$.ajax({
		    		type: 'GET',
		    		url: "{{URL::to(Crypt::decrypt($setting->admin_url) . '/user/delete-image')}}/"+value,
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
	User Edit
	<span>
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/dashboard')}}">Dashboard</a> / <a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/user')}}">User</a> / <span>User Edit</span>
	</span>
@endsection

@section('help')
	<ul class="menu-help-list-container">
		<li>
			Field <i>User Group</i> digunakan untuk mengatur hak akses User di Back End
		</li>
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
				{!!Form::model($user, ['url' => URL::to(Crypt::decrypt($setting->admin_url) . '/user/' . $user->id), 'method' => 'PUT', 'files' => true])!!}
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
									{!!Form::select('area', $area_options, $user->area_id, ['class'=>'edit-form-text large select'])!!}
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
									{!!Form::password('password', ['class'=>'edit-form-text large'])!!}
									<span class="edit-form-note">
										*Stay null if you don't have change the password and min 6 character
									</span>
								</div>
								<div class="edit-form-group">
									{!!Form::label('password_confirmation', 'Password Confirmation', ['class'=>'edit-form-label'])!!}
									{!!Form::password('password_confirmation', ['class'=>'edit-form-text large'])!!}
									<span class="edit-form-note">
										*Required if field new password not null and must be exactly the same with new password field
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