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

use App\Models\Slideshow;
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


class HomeController extends Controller
{
	/* Get the list of the resource*/
	public function index(Request $request)
	{
		// $request->session()->flush();
		$slideshows = Slideshow::where('is_active', '=', 1)->orderBy('order', 'asc')->get();
		$data['slideshows'] = $slideshows;

		$products = Product::where('is_active', '=', 1)->orderBy('id', 'desc')->take(8)->get();
		$data['products'] = $products;

		$hot_products = Product::where('is_active', '=', 1)->orderBy('views', 'desc')->take(8)->get();
		$data['hot_products'] = $hot_products;

		$sale_products = Product::where('discount', '!=', 0)->where('is_active', '=', 1)->orderByRaw("random()")->take(4)->get();
		$data['sale_products'] = $sale_products;

		$setting = Setting::first();
		$data['setting'] = $setting;

		return view('front.home.index', $data);
	}
}