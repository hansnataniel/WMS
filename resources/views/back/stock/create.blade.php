<?php
	use Illuminate\Support\Str;

	use App\Models\User;
?>

@extends('back.template.master')

@section('title')
	New Stock
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
	New Stock
	<span>
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/dashboard')}}">Dashboard</a> / <a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/stock')}}">Stock</a> / <span>New Stock</span>
	</span>
@endsection

@section('help')
	<ul class="menu-help-list-container">
		<li>
			Masukkan note, jika Anda ingin menambahkan informasi lebih detail mengenai stock yang Anda buat
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
					<a class="edit-button-item edit-button-back" href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/stock')}}">
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
				{!!Form::open(['url' => URL::to(Crypt::decrypt($setting->admin_url) . '/stock'), 'files' => true])!!}
					<div class="page-group">
						<div class="page-item col-2-4">
							<div class="page-item-title">
								Detail Information
							</div>
							<div class="page-item-content edit-item-content">
								<div class="edit-form-group">
									{!!Form::label('title', 'Title', ['class'=>'edit-form-label'])!!}
									{!!Form::text('title', null, ['class'=>'edit-form-text large', 'required', 'autofocus'])!!}
									<span class="edit-form-note">
										*Required
									</span>
								</div>
								<div class="edit-form-group">
									{!!Form::label('short_description', 'Short Description', ['class'=>'edit-form-label'])!!}
									{!!Form::textarea('short_description', null, ['class'=>'edit-form-text large area', 'required'])!!}
									<span class="edit-form-note">
										*Required
									</span>
								</div>
								<div class="edit-form-group">
									{!!Form::label('image', 'Image', ['class'=>'edit-form-label'])!!}								
									{!!Form::file('image', ['class'=>'edit-form-text large'])!!}
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
						<div class="page-item col-2-4">
							<div class="page-item-title">
								Search Engine Optimation (SEO)
							</div>
							<div class="page-item-content edit-item-content">
								<div class="edit-form-group">
									{!!Form::label('meta_description', 'Meta Description', ['class'=>'edit-form-label'])!!}
									{!!Form::textarea('meta_description', $stock->meta_desc, ['class'=>'edit-form-text large area', 'required'])!!}
									<span class="edit-form-note">
										*Required
									</span>
								</div>
								<div class="edit-form-group">
									{!!Form::label('meta_keyword', 'Meta Keyword', ['class'=>'edit-form-label'])!!}
									{!!Form::textarea('meta_keyword', $stock->meta_key, ['class'=>'edit-form-text large area', 'required'])!!}
									<span class="edit-form-note">
										*Required
									</span>
								</div>
							</div>
						</div>
					</div>
					<div class="page-group">
						<div class="page-item col-1">
							<div class="page-item-title">
								Description
							</div>
							<div class="page-item-content edit-item-content">
								<div class="edit-form-group">
									{!!Form::label('description', 'Description', ['class'=>'edit-form-label'])!!}
									{!!Form::textarea('description', null, ['class'=>'edit-form-text large ckeditor'])!!}
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