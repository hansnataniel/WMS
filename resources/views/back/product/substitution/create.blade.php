<?php
	use Illuminate\Support\Str;

	use App\Models\User;
	use App\Models\Substitution;
?>

@extends('back.template.master')

@section('title')
	New Substitution
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
	New Substitution
	<span>
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/dashboard')}}">Dashboard</a> / <a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/substitution')}}">Substitution</a> / <span>New Substitution</span>
	</span>
@endsection

@section('help')
	<ul class="menu-help-list-container">
		<li>
			Substitution dijadikan sebagai pengelompok produk
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
					<a class="edit-button-item edit-button-back" href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/substitution')}}">
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
				{!!Form::open(['url' => URL::to(Crypt::decrypt($setting->admin_url) . '/product/add-substitution/' . $product->id), 'files' => true])!!}
					<div class="page-group">
						<div class="page-item col-1">
							<div class="page-item-title">
								Detail Information
							</div>
							<div class="page-item-content edit-item-content">
								<div class="edit-form-group">
									{!!Form::label('substitution_for', 'Substitution for', ['class'=>'edit-form-label'])!!}
									{!!Form::text('substitution_for', $product->name, ['class'=>'edit-form-text large', 'style'=>"border: none; padding: 0px; font-size: 18px; font-weight: bold;", "readonly"])!!}
								</div>
								<div class="edit-form-group">
									{!!Form::label('product', 'Product', ['class'=>'edit-form-label'])!!}
									{!!Form::select('product', $product_options, null, ['class'=>'edit-form-text select large'])!!}
									<span class="edit-form-note">
										*Required
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