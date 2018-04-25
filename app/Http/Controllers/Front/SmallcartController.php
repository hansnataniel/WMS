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


class SmallcartController extends Controller
{
	public function index(Request $request)
	{
		$carts = Cart::contents();
		$data['carts'] = $carts;

		return view('front.template.cart_small', $data);
	}

	public function getDelete(Request $request, $id)
	{
		$cart_checks = Cart::contents();
		foreach ($cart_checks as $cart_check) 
		{
			if($cart_check->id == $id)
			{
				$cart_check->remove();
			}
		}

		$carts = Cart::contents();

		$data['carts'] = $carts;

		return view('front.template.cart_small', $data);
	}

	public function getBuy(Request $request, $id, $qty)
	{
		$carts = Cart::contents();
		if(count($carts) != 0)
		{
			$found = false;
			foreach ($carts as $cart) 
			{
				if($cart->id == $id)
				{
					$qty_total = $cart->quantity + $qty;
					$found = true;
				}
			}

			if($found == false) 
			{
				$qty_total = $qty;
			}
		}
		else
		{
			$qty_total = $qty;
		}

		$product 				= Product::find($id);
		$price_after_discount 	= $product->price - ($product->price * $product->discount / 100);

		if($product->stock >= $qty_total)
		{
			// Count Quantity Discount
			$quantity_discount = 0;

			$price_total = ($price_after_discount * $qty_total) - $quantity_discount;

			Cart::insert(array(
				'id'					=> $product->id,
				'name'					=> $product->name,
				'price'					=> $price_after_discount,
				'product_price'			=> $product->price,
				'weight'				=> $product->weight,
				'quantity'				=> $qty,
				'quantity_discount'		=> $quantity_discount,
				'price_total'			=> $price_total,
			));

			echo "This product has been succesfully added to your cart";
		}
		else
		{
			echo "The amount of desired item is exceeding the stock";
		}
	}


	/*
		Empty Cart
	*/
	public function getEmpty()
	{
		Cart::destroy();
		return 'Cart telah kosong';
	}
}