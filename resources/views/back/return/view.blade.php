<?php
	use Illuminate\Support\Str;

	use App\Models\Admin;
	use App\Models\Retur;
	use App\Models\Ridetail;
	use App\Models\Returndetail;
?>

@extends('back.template.master')

@section('title')
	Return View
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
	Return View
	<span>
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/dashboard')}}">Dashboard</a> / <a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/return')}}">Return</a> / <span>Return View</span>
	</span>
@endsection

@section('help')
	<ul class="menu-help-list-container">
		<li>
			Gunakan tombol Edit untuk mengedit Return
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
						<a class="view-button-item view-button-back" href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/return')}}"></a>
					@endif
					{{$return->no_nota}}
					<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/return/' . $return->id . '/edit')}}" class="view-button-item view-button-edit">
						Edit
					</a>
				</h1>

				<?php
					if($return->code_id != null)
					{
						$generator1 = new Picqer\Barcode\BarcodeGeneratorPNG();
						echo '<img style="margin-bottom: 20px; position: relative; display: block;" src="data:image/png;base64,' . base64_encode($generator1->getBarcode("$return->code_id", $generator1::TYPE_CODE_128)) . '">';
					}
				?>
				
				@if (file_exists(public_path() . '/usr/img/return/' . $return->id . '_' . Str::slug($return->name, '_') . '_thumb.jpg'))
					{!!HTML::image('usr/img/return/' . $return->id . '_' . Str::slug($return->name, '_') . '_thumb.jpg?lastmod=' . Str::random(5), '', ['class'=>'view-photo'])!!}
				@endif
				<div class="page-group">
					<div class="page-item col-1">
						<div class="page-item-title">
							Detail Information
						</div>
						<div class="page-item-content view-item-content">
							<table class="view-detail-table">
								<tr>
									<td>
										Receive Item
									</td>
									<td class="view-info-mid">
										:
									</td>
									<td>
										<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/ri/' . $return->ri_id)}}" style="color: blue;">
											{{$return->ri->no_nota}}
										</a>
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
										<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/supplier/' . $return->supplier_id)}}" style="color: blue;">
											{{$return->supplier->name}}
										</a>
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
										{{date('d F Y', strtotime($return->date))}}
									</td>
								</tr>
								<tr>
									<td>
										Message
									</td>
									<td class="view-info-mid">
										:
									</td>
									<td>
										{!!nl2br($return->msg)!!}
									</td>
								</tr>
							</table>
						</div>
					</div>
				</div>
				<div class="page-group">
					<div class="page-item col-1">
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
										Receive
									</th>
									<th>
										Return
									</th>
									<th style="text-align: right;">
										Return Price
									</th>
									<th style="text-align: right;">
										Sub Price
									</th>
								</tr>
								<?php
									$counter = 0;
									$total = 0;

									$getridetails = Ridetail::where('ri_id', '=', $return->ri_id)->where('product_id', '=', 0)->get();
									foreach ($getridetails as $getridetail) {
										$getridetailids[] = $getridetail->id;
									}

									$frees = Ridetail::where('ri_id', '=', $return->ri_id)->where('product_id', '!=', 0)->get();
									foreach ($frees as $free) {
										$freeids[] = $free->id;
									}
								?>

								@if(isset($getridetailids))
									<?php
										$returndetails = Returndetail::where('return_id', '=', $return->id)->whereIn('ridetail_id', $getridetailids)->get();
									?>
									@foreach ($returndetails as $returndetail)
										<?php 
											$counter++; 
											$total += $returndetail->price * $returndetail->qty;
										?>
										<tr>
											<td>
												{{$counter}}
											</td>
											<td>
												<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/product/' . $returndetail->ridetail->podetail->product_id)}}" style="color: blue;">
													{{$returndetail->ridetail->podetail->product->name}}
												</a>
											</td>
											<td style="color: green;">
												{{$returndetail->ridetail->qty}}
											</td>
											<td style="color: red;">
												{{$returndetail->qty}}
											</td>
											<td style="text-align: right;">
												Rp {!!number_format($returndetail->price)!!}
											</td>
											<td style="text-align: right;">
												Rp {!!number_format($returndetail->price * $returndetail->qty)!!}
											</td>
										</tr>
									@endforeach
								@endif

								@if(isset($freeids))
									<?php
										$freereturndetails = Returndetail::where('return_id', '=', $return->id)->whereIn('ridetail_id', $freeids)->get();
									?>
									<tr style="border-top: 1px solid #535353;">
										<td colspan="6" style="font-weight: bold;">
											Free Product
										</td>
									</tr>
									<?php
										$counter = 0;
									?>
									@foreach ($freereturndetails as $freereturndetail)
										<?php
											$counter++;
											$total += $freereturndetail->price * $freereturndetail->qty;
										?>
										<tr style="border-top: 1px solid #535353;">
											<td>
												{{$counter}}
											</td>
											<td>
												<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/product/' . $freereturndetail->ridetail->product_id)}}" style="color: blue;">
													{{$freereturndetail->ridetail->product->name}}
												</a>
											</td>
											<td style="color: green;">
												{{$freereturndetail->ridetail->qty}}
											</td>
											<td style="color: red;">
												{{$freereturndetail->qty}}
											</td>
											<td style="text-align: right;">
												Rp {{number_format($freereturndetail->price)}}
											</td>
											<td style="text-align: right;">
												Rp {!!number_format($freereturndetail->price * $freereturndetail->qty)!!}
											</td>
										</tr>
									@endforeach
								@endif

								
								<tr>
									<td colspan="5" style="font-size: 20px; text-align: right; border-top: 1px solid #535353; font-weight: bold;">
										Total
									</td>
									<td style="font-size: 20px; text-align: right; border-top: 1px solid #535353; font-weight: bold;">
										Rp {{number_format($total)}}
									</td>
								</tr>	
							</table>
						</div>
					</div>
				</div>
				<div class="view-last-edit">
					<?php
						$createuser = Admin::find($return->create_id);
						$updateuser = Admin::find($return->update_id);
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
								{{date('l, d F Y G:i:s', strtotime($return->created_at))}}
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
								{{date('l, d F Y G:i:s', strtotime($return->updated_at))}}
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