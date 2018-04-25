<?php
	use Illuminate\Support\Str;

	use App\Models\User;
?>

@extends('back.template.master')

@section('title')
	New Image for {{$product->name}}
@endsection

@section('head_additional')
	{!!HTML::style('css/back/edit.css')!!}
@endsection

@section('js_additional')
	{!!HTML::style('css/jquery.datetimepicker.css')!!}
	{!!HTML::script('js/jquery.datetimepicker.js')!!}
	
	<script>
		$(function(){
		    $('.datetimepicker').datetimepicker({
				timepicker: false,
				format: 'Y-m-d'
			});
		});
	</script>
@endsection

@section('page_title')
	New Image for {{$product->name}}
	<span>
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/dashboard')}}">Dashboard</a> / <a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/product')}}">Product</a> / <a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/product-image/list/' . $product->id)}}">Image(s) of {{$product->name}}</a> / <span>New Image for {{$product->name}}</span>
	</span>
@endsection

@section('help')
	<ul class="menu-help-list-container">
		<li>
			No hint for this page
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
					<a class="edit-button-item edit-button-back" href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/product/more-image/' . $product->id)}}">
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
				{!!Form::model($productphoto, ['url' => URL::to(Crypt::decrypt($setting->admin_url) . '/product/addphoto/' . $product->id), 'method' => 'POST', 'files' => true])!!}
					<div class="page-group">
						<div class="page-item col-1">
							<div class="page-item-title">
								Detail Information
							</div>
							<div class="page-item-content edit-item-content">
								{{-- <div class="edit-form-group">
									{!!Form::label('name', 'Name', ['class'=>'edit-form-label'])!!}
									{!!Form::text('name', null, ['class'=>'edit-form-text medium', 'required', 'autofocus'])!!}
									<span class="edit-form-note">
										*Required
									</span>
								</div> --}}
								<div class="edit-form-group">
									{!!Form::label('image', 'Image', ['class'=>'edit-form-label'])!!}
									{!!Form::file('image', ['class'=>'edit-form-text image'])!!}
									<span class="edit-form-note">
										*Required
									</span>
								</div>
								{{-- <div class="edit-form-group">
									{!!Form::label('is_active', 'Fields 5', ['class'=>'edit-form-label'])!!}
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
								</div> --}}
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