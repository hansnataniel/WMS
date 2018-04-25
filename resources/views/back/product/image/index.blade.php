<?php
	use Illuminate\Support\Str;
?>

@extends('back.template.master')

@section('title')
	Image(s) of {{$product->name}}
@endsection

@section('head_additional')
	{!!HTML::style('css/back/index.css')!!}
	{!!HTML::style('css/back/indeximage.css')!!}
	{!!HTML::style('css/back/indeximagecustom.css')!!}
@endsection

@section('js_additional')
	<script type="text/javascript">
		$(document).ready(function(){
			$('.index-del-switch').click(function(e){
				e.stopPropagation();

				$('.pop-result').html($(this).find('.index-del-content').html());

				$('.pop-container').fadeIn();
				$('.pop-container').find('.index-del-item').each(function(e){
					$(this).delay(70*e).animate({
	                    opacity: 1,
	                    top: 0
	                }, 300);
				});
			});
		});
	</script>
@endsection

@section('page_title')
	Image(s) of {{$product->name}}
	<span>
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/dashboard')}}">Dashboard</a> / <a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/product')}}">Product</a> / <span>Image(s) of {{$product->name}}</span>
	</span>
@endsection

@section('help')
	<ul class="menu-help-list-container">
		<li>
			Gunakan icon "Mata" di bawah gambar masing-masing item untuk melihat detail product image
		</li>
		<li>
			Gunakan icon "Bintang" di bawah gambar masing-masing item untuk menjadikannya sebagai default
		</li>
		<li>
			Gunakan icon "Sampah" di bawah gambar masing-masing item untuk menghapus product image
		</li>
	</ul>
@endsection

@section('content')
	<div class="page-group">
		<div class="page-item col-1">
			<div class="page-item-content">
				<div class="index-desc-container">
					<a class="index-button-item index-button-back" href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/product')}}"></a>

					<a class="index-desc-item" href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/product/addphoto/' . $product->id)}}">
						{!!HTML::image('img/admin/index/add_icon.png')!!}
						<span>
							Add New
						</span>
					</a>

					<span class="index-desc-count">
						{{$records_count}} record(s) found
					</span>
				</div>

				<?php
					if ($request->has('page'))
					{
						$counter = ($request->input('page')-1) * $per_page;
					}
					else
					{
						$counter = 0;
					}

					$totalcounter = count($moreimages);
				?>

				@foreach ($moreimages as $moreimage)
					<?php 
						$counter++; 
					?>

					@if(($counter - 1) % 4 == 0)
						<div class="page-group">
					@endif
						<div class="page-item col-1-4 sld-item">
							<div class="sld-img" style="background: url('<?php echo URL::to('usr/img/product/thumbnail/' . $moreimage->gambar . '?lastmod=' . Str::random(5)); ?>')"></div>
							
							<div class="sld-icon-container">
								@if($moreimage->default == false)
									<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/product/setdefault/' . $moreimage->id)}}" class="sld-icon-item" title="Make it default">
										{!!HTML::image('img/admin/index/star_icon.png')!!}
									</a>
								@else
									<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/product/setdefault/' . $moreimage->id)}}" class="sld-icon-item" style="background: transparent !important;" title="Default">
										{!!HTML::image('img/admin/index/star_icon_active.png')!!}
									</a>
								@endif
								<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/product/detailphoto/' . $moreimage->id)}}" class="sld-icon-item" title="Detail">
									{!!HTML::image('img/admin/index/detail_icon.png')!!}
								</a>
								<div class="sld-icon-item index-del-switch delete" title="Delete">
									{!!HTML::image('img/admin/index/trash_icon.png')!!}

									{{-- 
										Content of Delete
									 --}}
									<div class="index-del-content">
										<div class="index-del-title index-del-item">
											Do you really want to delete this image?
										</div>
										{!!HTML::image('usr/img/product/thumbnail/' . $moreimage->gambar . '?lastmod=' . Str::random(5), '', ['class'=>'index-del-img index-del-item'])!!}
										{{-- <table class="index-del-table index-del-item">
											<tr>
												<td>
													Name
												</td>
												<td class="index-td-mid">
													:
												</td>
												<td>
													{{$moreimage->name}}
												</td>
											</tr>
										</table> --}}
										{!!Form::open(['url' => URL::to(Crypt::decrypt($setting->admin_url) . '/product/deletephoto/' . $moreimage->id), 'method' => 'POST', 'class'=>'form index-del-item'])!!}
											{!!Form::submit('Delete', ['class'=>'index-del-button'])!!}
										{!!Form::close()!!}
									</div>
								</div>
							</div>
							{{-- <div class="sld-content">
								<div class="sld-group">
									<span>
										Name
									</span>
									<span>
										{{$moreimage->name}}
									</span>
								</div>
								<div class="sld-group">
									<span>
										Active Status
									</span>
									<span>
										{!!$moreimage->is_active == true ? "<span class='text-green'>Active</span>":"<span class='text-red'>Not Active</span>"!!}
									</span>
								</div>
								<div class="sld-group">
									<span>
										Order
									</span>
									<span>
										<div class="sld-order-group">
											{!!Form::open(['url' => URL::to(Crypt::decrypt($setting->admin_url) . '/product-image/moveto/' . $product->id), 'class'=>'sld-form'])!!}

												{!!Form::hidden('id', $moreimage->id)!!}
												{!!Form::text('moveto', $moreimage->order, ['class'=>'sld-order-text'])!!}
												{!!Form::submit('Save', ['class'=>'sld-form-submit'])!!}

											{!!Form::close()!!}
											
											@if ($records_count > 1)
												@if ($counter == 1)
													{!!HTML::link(URL::to(Crypt::decrypt($setting->admin_url) . '/product-image/movedown/' . $moreimage->id), '', ['class'=>'sld-form-down'])!!}
												@endif
												
												@if (($counter != 1) AND ($counter != $records_count))
													{!!HTML::link(URL::to(Crypt::decrypt($setting->admin_url) . '/product-image/moveup/' . $moreimage->id), '', ['class'=>'sld-form-up'])!!} 
													{!!HTML::link(URL::to(Crypt::decrypt($setting->admin_url) . '/product-image/movedown/' . $moreimage->id), '', ['class'=>'sld-form-down'])!!}
												@endif
												
												@if ($counter == $records_count)
													{!!HTML::link(URL::to(Crypt::decrypt($setting->admin_url) . '/product-image/moveup/' . $moreimage->id), '', ['class'=>'sld-form-up'])!!}
												@endif
											@endif
										</div>
									</span>
								</div>
							</div> --}}
						</div>
					@if(($counter % 4 == 0) OR ($counter == $totalcounter))
						</div>
					@endif
				@endforeach
			</div>
		</div>
	</div>
@endsection