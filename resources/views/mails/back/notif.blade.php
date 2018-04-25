<?php
	use Illuminate\Support\Str;
	use App\Models\Setting;
	use App\Models\Productphoto;

	$setting = Setting::first();

	$productphoto = Productphoto::where('product_id', '=', $product->id)->where('default', '=', 1)->first();
?>

<html>
	<head>
		<title>{{$setting->name}}</title>
	</head>
	<body>
		<table id="wrapper" style="font-size: 14px; color: #0d0f3b; font-family: arial; width: 100%; line-height: 20px;">
			<tr>
				<td id="header-container" style="padding: 10px 20px; text-align: center;">
					{{HTML::image('img/admin/creids_logo.png', '', array('style'=>'width: 200px;'))}}
				</td>
			</tr>
			<tr>
				<td id="section-container" style="padding: 20px;">
					<p>Halo Bapak/Ibu,</p>

					<p>Terima kasih telah mengunjungi website kami.</p>

					<p>
						Melalui email ini kami menginformasikan bahwa stok untuk produk <a href="{{URL::to('product/detail/' . $product->id . '/' . Str::slug($product->name, '-'))}}" target="_blank" style="color: #000; text-decoration: none; font-weight: bold;">{{$product->name}}</a> telah tersedia.<br>
		            </p>

					<p>
		                <a href="{{URL::to('product/detail/' . $product->id . '/' . Str::slug($product->name, '-'))}}" target="_blank" style="color: #595959; text-decoration: none;">
                            {{HTML::image('usr/img/product/thumbnail/' . $productphoto->gambar, '', array('style'=>'max-width: 300px;'))}}
	                        <br>
				        </a>
		            </p>
		            <p>
		                <a href="{{URL::to('product-detail/' . $product->id . '/' . Str::slug($product->name, '-'))}}" target="_blank" style="color: #595959;">
			            	Segera klik link ini untuk mendapatkan produk ini.
			            </a>
		            </p>

					<br><br>

					Best regards, <br>
						
					{{$setting->name}}
					<br>

					<br>
				</td>
			</tr>
			<tr>
				<td class="not-reply" style="font-size: 11px; line-height: 13px;padding-left: 20px;">
					<i>
						*This email was sent from a notification-only address that cannot accept incoming emails. Please do not reply to this email.
					</i>
					<br><br>
				</td>
			</tr>
			<tr>
				<td  id="footer-container" style="padding: 10px 20px; color: #fff; background: #f7961e; text-align: center">
					<span>
						© {{date('Y')}} - {{$setting->name}}
					</span>
				</td>
			</tr>
		</table>
	</body>
</html>