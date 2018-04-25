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

use App\Models\Faq;
use App\Models\Shipping;


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


class CustomController extends Controller
{
	public function getPolicy(Request $request, $id, $name)
	{
		$shippings = Shipping::where('is_active', '=', 1)->orderBy('created_at', 'asc')->get();
		$data['shippings'] = $shippings;

		$shipping_content = Shipping::find($id);
		$data['shipping_content'] = $shipping_content;

		return view('front.terms_and_conditions.index', $data);
	}

	public function getFaq(Request $request, $id, $name)
	{
		$faqs = Faq::where('is_active', '=', 1)->orderBy('created_at', 'asc')->get();
		$data['faqs'] = $faqs;

		$faq_content = Faq::find($id);
		$data['faq_content'] = $faq_content;

		return view('front.faq.index', $data);
	}

	public function getHow(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		return view('front.how_to_buy.index', $data);
	}
}