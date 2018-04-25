<?php
	use Illuminate\Support\Str;

	use App\Models\Ri;
	use App\Models\Po;
	use App\Models\Supplier;
	use App\Models\Hbt;
?>

@extends('back.template.master')

@section('title')
	Uninvoiced Debt
@endsection

@section('head_additional')
	{!!HTML::style('css/back/index.css')!!}
@endsection

@section('js_additional')
	<script type="text/javascript">
		$(document).ready(function(){
			$('.index-action-switch').click(function(e){
				e.stopPropagation();
				
				if($(this).hasClass('active'))
				{
					indexSwitchOff();
				}
				else
				{
					indexSwitchOff();

					$(this).addClass('active');
					$(this).find($('.index-action-child-container')).fadeIn();

					$(this).find($('li')).each(function(e){
						$(this).delay(50*e).animate({
		                    opacity: 1,
		                    top: 0
		                }, 300);
					});
				}
			});

			$('.index-del-switch').click(function(){
				$('.pop-result').html($(this).parent().parent().parent().find('.index-del-content').html());

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
	Uninvoiced Debt
	<span>
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/dashboard')}}">Dashboard</a> / <span>Uninvoiced Debt</span>
	</span>
@endsection

@section('help')
	<ul class="menu-help-list-container">
		<li>
			Gunakan tombol View di dalam tombol Action untuk melihat detail dari Uninvoiced Debt
		</li>
	</ul>
@endsection

@section('content')
	<div class="page-group">
		<div class="page-item col-1">
			<div class="page-item-content">
				<div class="index-desc-container">
					<a class="index-desc-item index-button-back" href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/dashboard')}}">
						<span>
							Back
						</span>
					</a>

					<span class="index-desc-count">
						{{$records_count}} record(s) found
					</span>
				</div>
				<table class="index-table">
					<tr class="index-tr-title">
						<th>
							#
						</th>
						<th>
							Supplier
						</th>
						<th>
							Last Order
						</th>
						<th>
							Last Recieve Item
						</th>
						<th style="text-align: right;">
							Total
						</th>
						<th>
						</th>
					</tr>
					<?php
						if ($request->has('page'))
						{
							$counter = ($request->input('page')-1) * $per_page;
						}
						else
						{
							$counter = 0;
						}

						$hbtids = array();
						foreach ($hbts as $hbt) {
							$hbtids[] = $hbt->ri_id;
						}

						$ris = Ri::whereIn('id', $hbtids)->get();
						$riids = array();
						foreach ($ris as $ri) {
							$riids[] = $ri->po_id;
						}

						$pos = Po::whereIn('id', $riids)->where('status', '=', 'Dikirim')->get();
						$poids = array();
						foreach ($pos as $po) {
							$poids[] = $po->supplier_id;
						}

						$suppliers = Supplier::whereIn('id', $poids)->where('is_active', '=', true)->get();
					?>
					@foreach ($suppliers as $supplier)
						<?php 
							$counter++; 

							$pos = Po::where('supplier_id', '=', $supplier->id)->where('status', '=', 'Dikirim')->get();
							foreach ($pos as $po) {
								$poids[] = $po->id;
							}

							$ris = Ri::whereIn('po_id', $poids)->get();
							foreach ($ris as $ri) {
								$riids[] = $ri->id;
							}

							$hbts = Hbt::whereIn('ri_id', $riids)->where('status', '=', false)->get();
							$total = 0;
							foreach ($hbts as $hbt) {
								$total = $total + $hbt->amount;
							}

							$lasthbt = Hbt::whereIn('ri_id', $riids)->where('status', '=', false)->orderBy('id', 'desc')->first();
							$lastpo = Po::where('supplier_id', '=', $supplier->id)->where('status', '=', 'Dikirim')->orderBy('id', 'desc')->first();
						?>
						<tr>
							<td>
								{{$counter}}
							</td>
							<td>
								<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/supplier/' . $supplier->id)}}" style="color: blue;">
									{{$supplier->name}}
								</a>
							</td>
							<td>
								{{date('d F Y', strtotime($lastpo->updated_at))}}
							</td>
							<td>
								{{date('d F Y', strtotime($lasthbt->updated_at))}}
							</td>
							<td style="text-align: right;">
								Rp {{number_format($total)}}
							</td>
							<td class="index-td-icon">
								<div class="index-action-switch">
									{{-- 
										Switch of ACTION
									 --}}
									<span>
										Action
									</span>
									<div class="index-action-arrow"></div>

									{{-- 
										List of ACTION
									 --}}
									<ul class="index-action-child-container" style="width: 110px">
										<li>
											<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/uninvoiced-debt/' . $supplier->id)}}">
												{!!HTML::image('img/admin/index/detail_icon.png')!!}
												<span>
													View
												</span>
											</a>
										</li>
									</ul>
								</div>
							</td>
						</tr>
					@endforeach
				</table>
			</div>
		</div>
	</div>
@endsection