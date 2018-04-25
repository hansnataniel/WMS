<?php
	use Illuminate\Support\Str;

	use App\Models\Transaction;
	use App\Models\Transactiondetail;
	use App\Models\Customer;
	use App\Models\Hbt;
?>

@extends('back.template.master')

@section('title')
	Accounts Recievable
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
	Accounts Recievable
	<span>
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/dashboard')}}">Dashboard</a> / <span>Accounts Recievable</span>
	</span>
@endsection

@section('help')
	<ul class="menu-help-list-container">
		<li>
			Gunakan tombol View di dalam tombol Action untuk melihat detail dari Accounts Recievable
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
							Customer
						</th>
						<th>
							Last Order
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
					?>
					@foreach ($customers as $customer)
						<?php 
							$counter++;

							$transactions = Transaction::where('customer_id', '=', $customer->id)->where('status', '=', 'Waiting for Payment')->orderBy('id', 'desc')->get();

							$total = 0;
							foreach ($transactions as $transaction) {
								$total += $transaction->amount_to_pay;
							}

							$gettrans = Transaction::where('customer_id', '=', $customer->id)->orderBy('id', 'desc')->first();
						?>
						
						<tr>
							<td>
								{{$counter}}
							</td>
							<td>
								<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/customer/' . $customer->id)}}" style="color: blue;">
									{{$customer->name}}
								</a>
							</td>
							<td>
								{{date('d F Y', strtotime($gettrans->updated_at))}}
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
											<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/accounts-recievable/' . $customer->id)}}">
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