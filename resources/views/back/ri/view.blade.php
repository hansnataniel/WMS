<?php
	use Illuminate\Support\Str;

	use App\Models\Admin;
	use App\Models\Po;
	use App\Models\Podetail;
	use App\Models\Ridetail;
?>

@extends('back.template.master')

@section('title')
	Recieve Item View
@endsection

@section('head_additional')
	{!!HTML::style('css/back/detail.css')!!}
	{!!HTML::style('css/back/index.css')!!}
@endsection

@section('js_additional')
	<script type="text/javascript">
		$(document).ready(function(){
			
		});
	</script>
@endsection

@section('page_title')
	Recieve Item View
	<span>
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/dashboard')}}">Dashboard</a> / <a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/po')}}">Recieve Item</a> / <span>Recieve Item View</span>
	</span>
@endsection

@section('help')
	<ul class="menu-help-list-container">
		<li>
			Gunakan tombol Edit untuk mengedit Recieve Item
		</li>
	</ul>
@endsection

@section('content')
	<div class="page-group">
		<div class="page-item col-1">
			<div class="page-item-content">
				<h1 class="view-title">
					@if($request->session()->has('last_url'))
						<a class="view-button-item view-button-back" href="{{URL::to($request->session()->get('last_url'))}}"></a>
					@else
						<a class="view-button-item view-button-back" href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/ri')}}"></a>
					@endif
					{{$ri->no_nota}}
					<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/ri/' . $ri->id . '/edit')}}" class="view-button-item view-button-edit">
						Edit
					</a>
				</h1>

				<div class="page-group">
					<div class="page-item col-1">
						<div class="page-item-title">
							Detail Information
						</div>
						<div class="page-item-content view-item-content">
							<table class="view-detail-table">
								<tr>
									<td>
										Purchase Order
									</td>
									<td class="view-info-mid">
										:
									</td>
									<td>
										@if($ri->po_id != 0)
											<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/po/' . $ri->po_id)}}" style="color: blue;">
												{{$ri->po->no_nota}}
											</a>
										@else
											-
										@endif
									</td>
								</tr>
								<tr>
									<td>
										Supplier
									</td>
									<td class="view-info-mid">
										:
									</td>
									<td>
										@if($ri->po_id != 0)
											<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/supplier/' . $ri->po->supplier_id)}}" style="color: blue;">
												{{$ri->po->supplier->name}}
											</a>
										@else
											<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/supplier/' . $ri->supplier_id)}}" style="color: blue;">
												{{$ri->supplier->name}}
											</a>
										@endif
									</td>
								</tr>
								<tr>
									<td>
										date
									</td>
									<td class="view-info-mid">
										:
									</td>
									<td>
										{{date('d F Y', strtotime($ri->date))}}
									</td>
								</tr>
							</table>
						</div>
					</div>
				</div>
				<div class="page-group">
					<div class="page-item col-2-4">
						<div class="page-item-title">
							Product Detail
						</div>
						<div class="page-item-content view-item-content">
							<table class="index-table">
								<tr class="index-tr-title">
									<th>
										#
									</th>
									<th>
										Product
									</th>
									<th>
										Rak
									</th>
									<th>
										Order
									</th>
									<th>
										Recieve
									</th>
									<th>
										Leftover
									</th>
								</tr>
								<?php
									$counter = 0;
									$total = 0;
								?>
								@foreach ($ridetails as $ridetail)
									<?php 
										$counter++; 
										$total += $ridetail->price * $ridetail->qty;
									?>
									<tr>
										<td>
											{{$counter}}
										</td>
										<td>
											<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/product/' . $ridetail->product_id)}}" style="color: blue;">
												{{$ridetail->podetail->product->name}}
											</a>
										</td>
										<td>
											<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/rak/' . $ridetail->rak_id)}}" style="color: blue;">
												{{$ridetail->rak->name}}
											</a>
										</td>
										<td>
											{{$ridetail->podetail->qty}}
										</td>
										<td style="color: green;">
											@if($ridetail->qty == 0)
												0
											@else
												{{$ridetail->qty}}
											@endif
										</td>
										<td style="color: red;">
											{{$ridetail->podetail->qty - $ridetail->qty}}
										</td>
									</tr>
								@endforeach	
							</table>
						</div>
					</div>
					<div class="page-item col-2-4">
						<div class="page-item-title">
							Bonus Item
						</div>
						<div class="page-item-content view-item-content">
							<table class="index-table">
								<tr class="index-tr-title">
									<th>
										#
									</th>
									<th>
										Product
									</th>
									<th>
										Quantity
									</th>
									<th>
										Rak
									</th>
								</tr>
								<?php
									$counter = 0;
									$total = 0;
									$fridetails = Ridetail::where('ri_id', '=', $ri->id)->where('product_id', '!=', 0)->get();
								?>
								@foreach ($fridetails as $ridetail)
									<?php 
										$counter++; 
										$total += $ridetail->price * $ridetail->qty;
									?>
									<tr>
										<td>
											{{$counter}}
										</td>
										<td>
											<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/product/' . $ridetail->product_id)}}" style="color: blue;">
												{{$ridetail->product->name}}
											</a>
										</td>
										<td>
											{{$ridetail->qty}}
										</td>
										<td>
											<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/rak/' . $ridetail->rak_id)}}" style="color: blue;">
												{{$ridetail->rak->name}}
											</a>
										</td>
									</tr>
								@endforeach	
							</table>
						</div>
					</div>
				</div>
				<div class="view-last-edit">
					<?php
						$createuser = Admin::find($ri->create_id);
						$updateuser = Admin::find($ri->update_id);
					?>

					<div class="page-item-title" style="margin-bottom: 20px;">
						Basic Information
					</div>

					<div class="view-last-edit-group">
						<div class="view-last-edit-title">
							Create
						</div>
						<div class="view-last-edit-item">
							<span>
								Created at
							</span>
							<span>
								:
							</span>
							<span>
								{{date('l, d F Y G:i:s', strtotime($ri->created_at))}}
							</span>
						</div>
						<div class="view-last-edit-item">
							<span>
								Created by
							</span>
							<span>
								:
							</span>
							<span>
								{{$createuser->name}}
							</span>
						</div>
					</div>

					<div class="view-last-edit-group">
						<div class="view-last-edit-title">
							Update
						</div>
						<div class="view-last-edit-item">
							<span>
								Updated at
							</span>
							<span>
								:
							</span>
							<span>
								{{date('l, d F Y G:i:s', strtotime($ri->updated_at))}}
							</span>
						</div>
						<div class="view-last-edit-item">
							<span>
								Last Updated by
							</span>
							<span>
								:
							</span>
							<span>
								{{$updateuser->name}}
							</span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection