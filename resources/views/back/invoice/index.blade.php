<?php
	use Illuminate\Support\Str;

	use App\Models\Invoice;
	use App\Models\Invoicedetail;
?>

@extends('back.template.master')

@section('title')
	Invoice
@endsection

@section('head_additional')
	{!!HTML::style('css/back/index.css')!!}

	<style>
		.index-sub {
			position: relative;
			display: block;
			padding-left: 20px;
		}

		.index-sub:before {
			content: '';
			position: absolute;
			left: 0px;
			top: 4px;
			display: block;
			width: 5px;
			height: 5px;
			border-right: 3px solid #535353;
			border-top: 3px solid #535353;

			-webkit-transform: rotate(45deg);
			-moz-transform: rotate(45deg);
			-ms-transform: rotate(45deg);
			transform: rotate(45deg);
		}

		.index-sub-sub {
			position: relative;
			display: block;
			padding-left: 40px;
		}

		.index-sub-sub:before {
			content: '';
			position: absolute;
			left: 20px;
			top: 4px;
			display: block;
			width: 5px;
			height: 5px;
			border-right: 3px solid #535353;
			border-top: 3px solid #535353;

			-webkit-transform: rotate(45deg);
			-moz-transform: rotate(45deg);
			-ms-transform: rotate(45deg);
			transform: rotate(45deg);
		}
	</style>
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

			$('.printed').click(function(e){
				e.preventDefault();

				var datalink = $(this).attr('href');

				$(this).parent().parent().parent().parent().find('.index-action-switch > span').text('Loading...');

				$.ajax({
					type: "GET",
					url: datalink,
					success: function(msg) {
						$('.result').html(msg);

						$('.index-action-switch > span').text('Action');
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
	Invoice
	<span>
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/dashboard')}}">Dashboard</a> / <span>Invoice</span>
	</span>
@endsection

@section('help')
	<ul class="menu-help-list-container">
		<li>
			Gunakan tombol New untuk menambahkan data baru
		</li>
		<li>
			Gunakan tombol View di dalam tombol Action untuk melihat detail dari Invoice
		</li>
		<li>
			Gunakan tombol Edit di dalam tombol Action untuk mengedit Invoice
		</li>
		<li>
			Gunakan tombol Delete di dalam tombol Action untuk menghapus Invoice
		</li>
	</ul>
@endsection

@section('search')
	{!!Form::open(['URL' => URL::current(), 'method' => 'GET'])!!}
		<div class="menu-group">
			<div class="menu-title">
				Search by
			</div>
			<div class="menu-search-group">
				{!!Form::label('src_no_nota', 'No. Nota', ['class'=>'menu-search-label'])!!}	
				{!!Form::text('src_no_nota', '', ['class'=>'menu-search-text'])!!}
			</div>
			<div class="menu-search-group">
				{!!Form::label('src_date', 'Date', ['class'=>'menu-search-label'])!!}	
				{!!Form::text('src_date', '', ['class'=>'menu-search-text', 'readonly'])!!}
			</div>
			<div class="menu-search-group">
				{!!Form::label('src_supplier_id', 'Supplier', ['class'=>'menu-search-label'])!!}	
				{!!Form::select('src_supplier_id', $supplier_options, null, ['class'=>'menu-search-text select'])!!}
			</div>
			<div class="menu-search-group">
				{!!Form::label('src_status', 'Status', ['class'=>'menu-search-label'])!!}	
				{!!Form::select('src_status', [''=>'-- Status --', 'Paid'=>'Paid', 'Pending'=>'Pending'], null, ['class'=>'menu-search-text select'])!!}
			</div>
		</div>

		<div class="menu-group">
			<div class="menu-title">
				Sort by
			</div>
			<div class="menu-search-group">
				{!!Form::select('order_by', ['id'=>'Input Time'], null, ['class'=>'menu-search-text select'])!!}
			</div>
			<div class="menu-search-group">
				<div class="menu-search-radio-group">
					{!!Form::radio('order_method', 'asc', true, ['class'=>'menu-search-radio'])!!}
					{!!HTML::image('img/admin/sort1.png', '', ['menu-class'=>'search-radio-image'])!!}
				</div>
				<div class="menu-search-radio-group">
					{!!Form::radio('order_method', 'desc', false, ['class'=>'menu-search-radio'])!!}
					{!!HTML::image('img/admin/sort2.png', '', ['class'=>'menu-search-radio-image'])!!}
				</div>
			</div>
		</div>
		<div class="menu-group">
			{!!Form::submit('Search', ['class'=>'menu-search-button'])!!}
		</div>
	{!!Form::close()!!}
@endsection

@section('content')
	<div class="page-group">
		<div class="page-item col-1">
			<div class="page-item-content">
				<div class="index-desc-container">
					<a class="index-desc-item" href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/invoice/create')}}">
						{!!HTML::image('img/admin/index/add_icon.png')!!}
						<span>
							Add New
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
							No Nota
						</th>
						<th>
							Supplier
						</th>
						<th>
							Date
						</th>
						<th style="text-align: right;">
							Total
						</th>
						<th>
							Status
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
					@foreach ($invoices as $invoice)
						<?php $counter++; ?>
						<tr>
							<td>
								{{$counter}}
							</td>
							<td>
								{{$invoice->no_nota}}
							</td>
							<td>
								<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/supplier/' . $invoice->supplier_id)}}" style="color: blue;">
									{{$invoice->supplier->name}}
								</a>
							</td>
							<td>
								{{date('d F Y', strtotime($invoice->date))}}
							</td>
							<td style="text-align: right;">
								<?php
									$total = 0;
									$invoicedetails = Invoicedetail::where('invoice_id', '=', $invoice->id)->get();
									foreach ($invoicedetails as $invoicedetail) {
										$total = $total + ($invoicedetail->qty * $invoicedetail->price);
									}
								?>
								Rp {{number_format($total)}}
							</td>
							<td>
								{!!$invoice->status == 'Pending' ? "<span class='text-orange'>Pending</span>":"<span class='text-green'>Paid</span>"!!}
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
											<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/invoice/print/' . $invoice->id)}}" class="separator printed">
												{!!HTML::image('img/admin/index/print_icon.png')!!}
												<span>
													Print
												</span>
											</a>
										</li>
										<li>
											<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/invoice/' . $invoice->id)}}">
												{!!HTML::image('img/admin/index/detail_icon.png')!!}
												<span>
													View
												</span>
											</a>
										</li>
										<li>
											<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/invoice/' . $invoice->id . '/edit')}}">
												{!!HTML::image('img/admin/index/edit_icon.png')!!}
												<span>
													Edit
												</span>
											</a>
										</li>
										<li>
											<div class="index-del-switch">
												{!!HTML::image('img/admin/index/trash_icon.png')!!}
												<span>
													Delete
												</span>
											</div>
										</li>
									</ul>

									{{-- 
										Content of Delete
									 --}}
									<div class="index-del-content">
										<div class="index-del-title index-del-item">
											Do you really want to delete this invoice?
										</div>
										<table class="index-del-table index-del-item">
											<tr>
												<td>
													No Nota
												</td>
												<td class="index-td-mid">
													:
												</td>
												<td>
													{{$invoice->no_nota}}
												</td>
											</tr>
										</table>
										{!!Form::open(['url' => URL::to(Crypt::decrypt($setting->admin_url) . '/invoice/' . $invoice->id), 'method' => 'DELETE', 'class'=>'form index-del-item'])!!}
											{!!Form::submit('Delete', ['class'=>'index-del-button'])!!}
										{!!Form::close()!!}
									</div>
								</div>
							</td>
						</tr>
					@endforeach
				</table>

				{{-- 
					Pagination
				 --}}
				{{$invoices->appends($criteria)->links()}}
			</div>
		</div>
	</div>

	<div class="result" style="z-index: -1; opacity: 0; position: absolute;"></div>
@endsection