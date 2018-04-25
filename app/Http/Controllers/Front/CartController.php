<?php

/*
	Use Name Space Here
*/
namespace App\Http\Controllers\Front;

/*
	Call Model Here
*/
use App\Models\Setting;
use App\Models\Que;

use App\Models\Product;
use App\Models\Productphoto;


/*
	Call Another Function you want to use
*/
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Auth;
use Validator;
use Crypt;
use URL;
use Image;
use Session;
use File;
use Cart;


class CartController extends Controller
{
	/* Get the list of the resource*/
	public function index(Request $request)
	{
		$carts = Cart::contents();
		if(count($carts) == 0)
		{
			return redirect('/');
		}
		$data['carts'] = $carts;

		$setting = Setting::first();
		$data['setting'] = $setting;
		
		return view('front.cart.index', $data);
	}

	public function getEdit(Request $request, $id, $qty)
	{
		$carts_cek = Cart::contents();
		foreach ($carts_cek as $cart) 
		{
			if($cart->id == $id)
			{
				// Count Quantity Discount
				$product = Product::find($cart->id);
				if($product->stock >= $qty)
				{
					$price_total = $cart->price * $qty;

					$cart->quantity = $qty;
					$cart->price_total = $price_total;

					/*cek stok*/
					$stok = true;
				}
				else
				{
					/*cek stok*/
					$stok = false;
				}
			}

			if($cart->quantity == 0)
			{
				$cart->remove();
			}
		}

		$carts = Cart::contents();
		$data['carts'] = $carts;

		$data['stok'] = $stok;

		return view('front.cart.cart_ajax', $data);
	}
}