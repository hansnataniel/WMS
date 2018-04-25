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

use App\Models\Category;
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


class ProductController extends Controller
{
	/* Get the list of the resource*/
	public function getCategory(Request $request, $category_id, $category_name)
	{
		if($category_id != 'sale')
		{
			$category = Category::find($category_id);
			$data['category'] = $category;
			if($category->parent_id != 0)
			{
				$parent_category = Category::find($category->parent_id);
			}
			else
			{
				$parent_category = null;

			}

			$products = Product::where('category_id', '=', $category_id)->where('is_active', '=', 1)->orderBy('id', 'desc')->paginate(20);
		}
		else
		{
			$parent_category = 'Sale';

			$products = Product::where('discount', '!=', 0)->where('is_active', '=', 1)->orderBy('id', 'desc')->paginate(20);
		}
		$data['parent_category'] = $parent_category;
		$data['products'] = $products;

		return view('front.product.index', $data);
	}

	public function getDetail(Request $request, $product_id, $product_name)
	{
		$product = Product::find($product_id);
		$data['product'] = $product;

		$productphotos = Productphoto::where('product_id', '=', $product->id)->get();
		$data['productphotos'] = $productphotos;

		$default_photo = Productphoto::where('product_id', '=', $product->id)->where('default', '=', 1)->first();
		$data['default_photo'] = $default_photo;

		$category = Category::find($product->category_id);
		$data['category'] = $category;
		if($category->parent_id != 0)
		{
			$parent_category = Category::find($category->parent_id);
		}
		else
		{
			$parent_category = null;

		}
		$data['parent_category'] = $parent_category;

		return view('front.product.detail', $data);
	}

	public function getSearch(Request $request)
	{
		$query = Product::query();

		$data['criteria'] = '';

		$name = htmlspecialchars($request->input('src_name'));
		if ($name != null)
		{
			$query->where('name', 'LIKE', '%' . $name . '%');
			$data['criteria']['src_name'] = $name;
		}
		$data['src_name'] = $name;
		$query->where('is_active', '=', 1)->orderBy('name', 'asc');

		$all_records = $query->get();
		$records_count = count($all_records);
		$data['records_count'] = $records_count;

		$per_page = 20;
		$data['per_page'] = $per_page;
		$products = $query->paginate($per_page);
		$data['products'] = $products;
			
		return view('front.product.search', $data);
	}
}