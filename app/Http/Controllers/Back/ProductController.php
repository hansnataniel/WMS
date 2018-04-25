<?php

/*
	Use Name Space Here
*/
namespace App\Http\Controllers\Back;

/*
	Call Model Here
*/
use App\Models\Setting;
use App\Models\Admingroup;
use App\Models\Substitution;
use App\Models\Que;
use App\Models\Rak;
use App\Models\Kendaraan;

use App\Models\Product;
use App\Models\Productphoto;
use App\Models\Productstock;


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
use DB;


class ProductController extends Controller
{
	/* Get the list of the resource*/
	public function index(Request $request)
	{
		$setting = Setting::first();	
		$data['setting'] = $setting;

		/*User Authentication*/
		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->product_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		/*Menu Authentication*/
		$data['navModul'] = true;
		$data['helpModul'] = true;
		$data['searchModul'] = true;
		$data['alertModul'] = true;
		$data['messageModul'] = true;
		
		
		$query = Product::query();

		$data['criteria'] = '';

		$name = htmlspecialchars($request->input('src_name'));
		if ($name != null)
		{
			$query->where('name', 'LIKE', '%' . $name . '%');
			$data['criteria']['src_name'] = $name;
		}

		$no_merk = htmlspecialchars($request->input('src_no_merk'));
		if ($no_merk != null)
		{
			$query->where('no_merk', 'LIKE', '%' . $no_merk . '%');
			$data['criteria']['src_no_merk'] = $no_merk;
		}
		
		$pricemin = htmlspecialchars($request->input('src_pricemin'));
		if ($pricemin != null)
		{
			$query->where('price', '>=', $pricemin);
			$data['criteria']['src_pricemin'] = $pricemin;
		}	

		$pricemax = htmlspecialchars($request->input('src_pricemax'));
		if ($pricemax != null)
		{
			$query->where('price', '<=', $pricemax);
			$data['criteria']['src_pricemax'] = $pricemax;
		}	

		// $rak_id = htmlspecialchars($request->input('src_rak_id'));
		// if ($rak_id != null)
		// {
		// 	$query->where('rak_id', '=', $rak_id);
		// 	$data['criteria']['src_rak_id'] = $rak_id;
		// }

		$is_active = htmlspecialchars($request->input('src_is_active'));
		if ($is_active != null)
		{
			$query->where('is_active', '=', $is_active);
			$data['criteria']['src_is_active'] = $is_active;
		}

		$order_by = htmlspecialchars($request->input('order_by'));
		$order_method = htmlspecialchars($request->input('order_method'));
		if ($order_by != null)
		{
			if (($order_by == 'is_active') or ($order_by == 'is_admin'))
			{
				$query->orderBy($order_by, $order_method)->orderBy('name', 'asc');
			}
			else
			{
			// return 'Work';
				$query->orderBy($order_by, $order_method);
			}
			$data['criteria']['order_by'] = $order_by;
			$data['criteria']['order_method'] = $order_method;
		}
		else
		{
			$query->orderBy('name', 'asc');
		}

		$all_records = $query->get();
		$records_count = count($all_records);
		$data['records_count'] = $records_count;

		$per_page = 20;
		$data['per_page'] = $per_page;
		$products = $query->paginate($per_page);
		$data['products'] = $products;

		$raks = Rak::where('is_active', '=', true)->orderBy('name')->get();
		if($raks->isEmpty())
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/rak/create')->with('error-message', "You don't have rak, please create it first");
		}
		$rak_options[''] = "Select Rak";
		foreach ($raks as $rak) {
			$rak_options[$rak->id] = $rak->name;
		}
		$data['rak_options'] = $rak_options;

		$references = Product::where('is_active', '=', true)->orderBy('name')->get();
		$reference_options[''] = "Select OEM Part";
		$reference_options[0] = "No Part";
		foreach ($references as $reference) {
			$reference_options[$reference->id] = $reference->name;
		}
		$data['reference_options'] = $reference_options;

		$request->session()->put('last_url', URL::full());

		$data['request'] = $request;

		$request->flash();

        return view('back.product.index', $data);
	}

	/* Create a new resource*/
	public function create(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->product_c != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/product')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		/*Menu Authentication*/

		$data['navModul'] = true;
		$data['helpModul'] = true;
		$data['searchModul'] = false;
		$data['alertModul'] = true;
		$data['messageModul'] = true;
		
		
		$product = new Product;
		$data['product'] = $product;

		$raks = Rak::where('is_active', '=', true)->orderBy('name')->get();
		if($raks->isEmpty())
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/rak/create')->with('error-message', "You don't have rak, please create it first");
		}
		$rak_options[''] = "Select Rak";
		foreach ($raks as $rak) {
			$rak_options[$rak->id] = $rak->name;
		}
		$data['rak_options'] = $rak_options;

		$kendaraans = Kendaraan::where('is_active', '=', true)->orderBy('name')->get();
		if($kendaraans->isEmpty())
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/kendaraan/create')->with('error-message', "You don't have kendaraan, please create it first");
		}
		$kendaraan_options[''] = "Select Kendaraan";
		foreach ($kendaraans as $kendaraan) {
			$kendaraan_options[$kendaraan->id] = $kendaraan->brand . " - " . $kendaraan->type;
		}
		$data['kendaraan_options'] = $kendaraan_options;

		$references = Product::where('is_active', '=', true)->orderBy('name')->get();
		$reference_options[''] = "Select OEM Part";
		foreach ($references as $reference) {
			$reference_options[$reference->id] = $reference->name;
		}
		$data['reference_options'] = $reference_options;

		$data['request'] = $request;

		Session::forget('add_rak');

        return view('back.product.create', $data);
	}

	public function store(Request $request)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;
		
		$inputs = $request->all();
		$rules = array(
			'name'		=> 'required',
			'no_merk'	=> 'required|unique:products,no_merk',
			// 'rak'		=> 'required',
			'image'		=> 'required|max:500',
			'kendaraan'		=> 'required',
			'min_stock'		=> 'required|numeric|min:0',
			'max_stock'		=> 'required|numeric|min:1',
		);

		$validator = Validator::make($inputs, $rules);
		if ($validator->passes())
		{
			$raks = $request->input('raks');
			// dd(count($raks));
			if($raks == null)
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/product/create')->withInput()->withErrors("Field rak is required");
			}
			if(count($raks) > 2)
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/product/create')->withInput()->withErrors("Rak must be less than 2");
			}

			DB::transaction(function() use ($request, $raks) {
				global $product;
				global $productphoto;
			
				$product = new Product;
				$product->name = htmlspecialchars($request->input('name'));
				$product->no_merk = htmlspecialchars($request->input('no_merk'));
				// $product->rak_id = htmlspecialchars($request->input('rak'));
				$product->kendaraan_id = htmlspecialchars($request->input('kendaraan'));
				if ($request->input('reference') == null)
				{
					$product->reference_id = 0;
				}
				else
				{
					$product->reference_id = htmlspecialchars($request->input('reference'));
				}

				if ($request->input('price') == null)
	            {
	                $product->price = 0;
	            }else
	            {
					$product->price = htmlspecialchars($request->input('price'));
	            }
				
				$product->merk = htmlspecialchars($request->input('merk'));
				$product->size = htmlspecialchars($request->input('size'));
				$product->min_stock = htmlspecialchars($request->input('min_stock'));
				$product->max_stock = htmlspecialchars($request->input('max_stock'));
				$product->description = $request->input('description');
				$product->is_active = htmlspecialchars($request->input('is_active', 0));

				$product->create_id = Auth::user()->id;
				$product->update_at = date('Y-m-d H:i:s');
				$product->update_id = Auth::user()->id;

				$product->save();

				foreach ($raks as $rak) {
					$productstock = new Productstock;
					$productstock->product_id = $product->id;
					$productstock->rak_id = $rak;
					$productstock->stock = 0;
					$productstock->is_active = true;
					$productstock->save();
				}

				// $product->raks()->sync($raks);

				if ($request->hasFile('image'))
				{
					$productphoto = Productphoto::where('product_id', '=', $product->id)->orderBy('id', 'desc')->first();
	                if ($productphoto == null)
	                {
	                    $last_number = 0;
	                }
	                else 
	                {
	                    $gambar = $productphoto->gambar;
	                    $name = explode('.', $gambar);
	                    $name = $name[0];
	                    $name = explode('_', $name);
	                    $last_number = $name[1];
	                }
	                $last_number = $last_number + 1;
	                $file_name = $product->id.'_'.$last_number.'_'.Str::slug($product->name, '_').'.jpg';
	                // upload file
					$request->file('image')->move(public_path() . '/usr/img/product/', $file_name);
	                $productphoto = new Productphoto;
	                $productphoto->product_id = $product->id;
	                $productphoto->gambar = $file_name;
	                
	                $cek_default = Productphoto::where('product_id','=',$product->id)->where('default','=',1)->first();
	                if ($cek_default == null) {
	                    $productphoto->default = 1;
	                }
	                else{
	                    $productphoto->default = 0;
	                }

	                if ($request->hasFile('image'))
					{
						$productphoto->is_crop = false;
					}

					$productphoto->create_id = Auth::user()->id;

	                $productphoto->save();

				}
			});

			global $product;
			global $productphoto;

			if ($request->hasFile('image'))
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/product/photocrop/' . $product->id . '/' . $productphoto->id)->with('success-message', "product <strong>$product->name</strong> has been created");
			}

			$admingroup = Admingroup::find(Auth::user()->admingroup_id);
			if ($admingroup->product_r != true)
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
			}
			return redirect(Crypt::decrypt($setting->admin_url) . '/product')->with('success-message', "Product <strong>" . Str::words($product->name, 5) . "</strong> has been Created");
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/product/create')->withInput()->withErrors($validator);
		}
	}

	/* Show a resource*/
	public function show(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->product_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/product')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		/*Menu Authentication*/

		$data['navModul'] = true;
		$data['helpModul'] = true;
		$data['searchModul'] = false;
		$data['alertModul'] = true;
		$data['messageModul'] = true;
		
		
		$product = Product::find($id);
		if ($product != null)
		{
			$data['product'] = $product;
			$data['request'] = $request;

			$productstocks = Productstock::where('product_id', '=', $product->id)->where('is_active', '=', true)->get();
			$data['productstocks'] = $productstocks;

	        return view('back.product.view', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/product')->with('error-message', 'Can not find product with ID ' . $id);
		}
	}

	/* Edit a resource*/
	public function edit(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->product_u != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/product')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		/*Menu Authentication*/

		$data['navModul'] = true;
		$data['helpModul'] = true;
		$data['searchModul'] = false;
		$data['alertModul'] = true;
		$data['messageModul'] = true;
		
		
		$product = Product::find($id);
		
		if ($product != null)
		{
			$data['product'] = $product;
			$data['request'] = $request;

			Session::forget('add_rak');

			$productstocks = Productstock::where('product_id', '=', $product->id)->where('is_active', '=', true)->get();
			$data['productstocks'] = $productstocks;

			foreach ($productstocks as $productstock) {
				Session::push('add_rak', $productstock->rak_id);
				// $rakids[] = $productstock->rak_id;
			}
			// dd($productstocks);

			if(!$productstocks->isEmpty())
			{
				$raks = Rak::whereNotIn('id', Session::get('add_rak'))->where('is_active', '=', true)->orderBy('name')->get();
			}
			else
			{
				$raks = Rak::where('is_active', '=', true)->orderBy('name')->get();
			}
			$rak_options[''] = "Select Rak";
			foreach ($raks as $rak) {
				$rak_options[$rak->id] = $rak->name;
			}
			$data['rak_options'] = $rak_options;

			$kendaraans = Kendaraan::where('is_active', '=', true)->orderBy('name')->get();
			if($kendaraans->isEmpty())
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/kendaraan/create')->with('error-message', "You don't have kendaraan, please create it first");
			}
			$kendaraan_options[''] = "Select Kendaraan";
			foreach ($kendaraans as $kendaraan) {
				$kendaraan_options[$kendaraan->id] = $kendaraan->brand . " - " . $kendaraan->type;
			}
			$data['kendaraan_options'] = $kendaraan_options;

			$references = Product::where('is_active', '=', true)->orderBy('name')->get();
			$reference_options[''] = "Select OEM Part";
			foreach ($references as $reference) {
				$reference_options[$reference->id] = $reference->name;
			}
			$data['reference_options'] = $reference_options;

	        return view('back.product.edit', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/product')->with('error-message', 'Can not find product with ID ' . $id);
		}
	}

	public function update(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;
		
		$inputs = $request->all();
		$rules = array(
			'name'		=> 'required',
			'no_merk'	=> 'required|unique:products,no_merk,' . $id,
			// 'rak'		=> 'required',
			'kendaraan'		=> 'required',
			'min_stock'		=> 'required|numeric|min:0',
			'max_stock'		=> 'required|numeric|min:1',
		);

		$validator = Validator::make($inputs, $rules);
		if ($validator->passes())
		{
			$product = Product::find($id);
			if ($product != null)
			{
				$raks = $request->input('raks');
				// dd(count($raks));
				if($raks == null)
				{
					return redirect(Crypt::decrypt($setting->admin_url) . '/product/' . $product->id . '/edit')->withInput()->withErrors("Field rak is required");
				}
				if(count($raks) > 2)
				{
					return redirect(Crypt::decrypt($setting->admin_url) . '/product/' . $product->id . '/edit')->withInput()->withErrors("Rak must be less than 2");
				}

				DB::transaction(function() use ($request, $product, $raks) {

					$product->name = htmlspecialchars($request->input('name'));
					$product->no_merk = htmlspecialchars($request->input('no_merk'));
					// $product->rak_id = htmlspecialchars($request->input('rak'));
					$product->kendaraan_id = htmlspecialchars($request->input('kendaraan'));
					if ($request->input('reference') == null)
					{
						$product->reference_id = 0;
					}
					else
					{
						$product->reference_id = htmlspecialchars($request->input('reference'));
					}
					
					$product->merk = htmlspecialchars($request->input('merk'));
					$product->size = htmlspecialchars($request->input('size'));
					$product->min_stock = htmlspecialchars($request->input('min_stock'));
					$product->max_stock = htmlspecialchars($request->input('max_stock'));
					$product->description = $request->input('description');
					$product->is_active = htmlspecialchars($request->input('is_active', 0));

					$product->update_at = date('Y-m-d H:i:s');
					$product->update_id = Auth::user()->id;

					$product->save();

					$getproductstocks = Productstock::where('product_id', '=', $product->id)->get();
					foreach ($getproductstocks as $getproductstock) {
						$getproductstock->is_active = false;
						$getproductstock->save();
					}

					foreach ($raks as $rak) {
						$checkdata = Productstock::where('product_id', '=', $product->id)->where('rak_id', '=', $rak)->first();
						if($checkdata == null)
						{
							$productstock = new Productstock;
							$rakstock = 0;
						}
						else
						{
							$productstock = $checkdata;
							$rakstock = $checkdata->stock;
						}

						$productstock->product_id = $product->id;
						$productstock->rak_id = $rak;
						$productstock->stock = $rakstock;
						$productstock->is_active = true;
						$productstock->save();
					}
					// $product->raks()->sync($raks);
				});

				$admingroup = Admingroup::find(Auth::user()->admingroup_id);
				if ($admingroup->product_r != true)
				{
					return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
				}
				else
				{
					return redirect(Crypt::decrypt($setting->admin_url) . '/product')->with('success-message', "Product <strong>" . Str::words($product->name, 5) . "</strong> has been Updated");
				}

			}
			else
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/product')->with('error-message', 'Can not find product with ID ' . $id);
			}
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/product/' . $id . '/edit')->withInput()->withErrors($validator);
		}
	}

	/* Delete a resource*/
	public function destroy(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/

		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->product_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}
		if ($admingroup->product_d != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}
		
		$product = Product::find($id);
		if ($product != null)
		{
			/*
				Get child of product
			*/
				$refproduct = Product::where('reference_id', '=', $id)->first();
				if($refproduct != null)
				{
					return redirect(Crypt::decrypt($setting->admin_url) . '/product')->with('error-message', "You can't delete this product, because this product has child");
				}

			$product_name = Str::words($product->name, 5);
			$transaction = Transactionitem::where('product_id', '=', $id)->get();
			if (count($transaction) == 0)
			{
				$inventories = Inventory::where('product_id', '=', $id)->get();
				if (count($inventories) != 0)
				{
					foreach ($inventories as $inventory) 
					{
						$inventory->delete();
					}
				}

				$productphotos = Productphoto::where('product_id', '=', $id)->get();
				if(!$productphotos->isEmpty())
				{
					foreach ($productphotos as $productphoto) 
					{
		                File::delete(public_path() . '/usr/img/product/' . $productphoto->gambar);
		                File::delete(public_path() . '/usr/img/product/wm' . $productphoto->gambar);
		                File::delete(public_path() . '/usr/img/product/thumbnail/' . $productphoto->gambar);
		                File::delete(public_path() . '/usr/img/product/thumbnail/wm' . $productphoto->gambar);
		                File::delete(public_path() . '/usr/img/product/small/' . $productphoto->gambar);
						
						$productphoto->delete();
					}
				}  

				$product->delete();
			}  
			else
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/product')->with('error-message', 'You can not deleted this product');
			} 

			return redirect(Crypt::decrypt($setting->admin_url) . '/product')->with('success-message', "Product <strong>" . $product_name . "</strong> has been Deleted");
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/product')->with('error-message', 'Can not find product with ID ' . $id);
		}
	}

	/* Photo Crop */
	public function getPhotocrop(Request $request, $id, $productphoto_id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		$data['messageModul'] = true;
		$data['alertModul'] = true;
		$data['searchModul'] = false;
		$data['helpModul'] = true;
		$data['navModul'] = false;
		
		$product = Product::find($id);
		if ($product != null)
		{
			$checkque = Que::where('admin_id', '=', Auth::user()->id)->where('table', '=', 'product')->where('table_id', '=', $id)->first();
			if($checkque == null)
			{
				$que = new Que;
				$que->admin_id = Auth::user()->id;
				$que->table = 'product';
				$que->table_id = $id;
				$que->url = URL::full();
				$que->save();
			}

			$productphoto = Productphoto::find($productphoto_id);
			$image = 'usr/img/product/' . $productphoto->gambar;
			$data['image'] = $image;

			$w_ratio = 300;
			$h_ratio = 300;

			$getimage = public_path() . '/usr/img/product/' . $productphoto->gambar;
			list($width, $height, $type, $attr) = getimagesize($getimage);

			if($width >= $height)
			{
				$w_akhir = 980;
				$h_akhir = (980 * $height) / $width;

				$w_akhir720 = 720;
				$h_akhir720 = (720 * $height) / $width;

				$w_akhir480 = 480;
				$h_akhir480 = (480 * $height) / $width;

				$w_akhir300 = 300;
				$h_akhir300 = (300 * $height) / $width;
			}

			if($width <= $height)
			{
				$w_akhir = (600 * $width) / $height;
				$h_akhir = 600;

				$w_akhir720 = (500 * $width) / $height;
				$h_akhir720 = 500;

				$w_akhir480 = (400 * $width) / $height;
				$h_akhir480 = 400;

				$w_akhir300 = (300 * $width) / $height;
				$h_akhir300 = 300;
			}

	        $data['w_ratio'] = $w_ratio;
        	$data['h_ratio'] = $h_ratio;

        	$data['w_akhir'] = $w_akhir;
        	$data['h_akhir'] = $h_akhir;

        	$data['w_akhir720'] = $w_akhir720;
        	$data['h_akhir720'] = $h_akhir720;

        	$data['w_akhir480'] = $w_akhir480;
        	$data['h_akhir480'] = $h_akhir480;

        	$data['w_akhir300'] = $w_akhir300;
        	$data['h_akhir300'] = $h_akhir300;

            $request->session()->put('undone-back-url', URL::full());
            $request->session()->put('undone-back-message', "Please crop this image first to continue");

            $data['request'] = $request;
            
			return view('back.crop.index', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/product')->with('error-message', "Can't find Product with ID " . $id);
		}
	}

	public function postPhotocrop(Request $request, $id, $productphoto_id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;
		
		$product = Product::find($id);
		if ($product != null)
		{

			$ques = Que::where('table', '=', 'product')->where('table_id', '=', $id)->get();
			foreach ($ques as $que) {
				$que->delete();
			}
			
			if (($request->input('x1') != null) AND ($request->input('w') != 0))
			{
				$productphoto = Productphoto::find($productphoto_id);
				$productphoto->is_crop = true;
				$productphoto->save();

				$image = Image::make(public_path() . '/usr/img/product/' . $productphoto->gambar);
				$image2 = Image::make(public_path() . '/usr/img/product/' . $productphoto->gambar);
				$image3 = Image::make(public_path() . '/usr/img/product/' . $productphoto->gambar);

	            /* Crop image */
	            $product_width = $request->input('w');
	            $product_height = $request->input('h');
	            $pos_x = $request->input('x1');
	            $pos_y = $request->input('y1');
	            $image2->crop(intval($product_width), intval($product_height), intval($pos_x), intval($pos_y));
	            $image3->crop(intval($product_width), intval($product_height), intval($pos_x), intval($pos_y));

	            $getimage = public_path() . '/usr/img/product/' . $productphoto->gambar;
				list($width, $height, $type, $attr) = getimagesize($getimage);

				if($width >= $height)
				{
					if($width > 920)
					{
						$product_width = 920;
						$product_height = null;
					}
					else
					{
						$product_width = $width;
						$product_height = $height;
					}
				}

				if($width <= $height)
				{
					if($height > 600)
					{
						$product_width = null;
						$product_height = 600;
					}
					else
					{
						$product_width = $width;
						$product_height = $height;
					}
				}
	            
	            $image->resize($product_width, $product_height, function ($constraint) {
                    $constraint->aspectRatio();
                });

	            $image->save(public_path() . '/usr/img/product/' . $productphoto->gambar);


	            /* Resize image (optional) */
	            $product_width = 400;
	            $product_height = null;
	            $conserve_proportion = true;
	            $image2->resize($product_width, $product_height, function ($constraint) {
                    $constraint->aspectRatio();
                });

	            $image2->save(public_path() . '/usr/img/product/thumbnail/' . $productphoto->gambar);


                /* Resize thumbnail image (optional) */
	            $product_width = 150;
	            $product_height = null;
	            $conserve_proportion = true;
	            $image3->resize($product_width, $product_height, function ($constraint) {
                    $constraint->aspectRatio();
                });

	            $image3->save(public_path() . '/usr/img/product/small/' . $productphoto->gambar);

	            $request->session()->forget('undone-back-url');
	            $request->session()->forget('undone-back-message');

	            if($request->session()->has('last_url'))
	            {
		            return redirect($request->session()->get('last_url'))->with('success-message', "The image of Product <strong>" . Str::words($product->name, 5) . "</strong> has been Updated");
	            }
	            else
	            {
		            return redirect(Crypt::decrypt($setting->admin_url) . '/product')->with('success-message', "The image of Product <strong>" . Str::words($product->name, 5) . "</strong> has been Updated");
	            }

			}
			else
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/product/photocrop/' . $id . '/' . $productphoto_id)->with('warning-message', 'You must select the cropping area to crop this picture');
			}
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/product')->with('error-message', "Can't find Product with ID " . $id);
		}
	}

	public function getMoreImage(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		$data['navModul'] = true;
		$data['helpModul'] = true;
		$data['searchModul'] = false;
		$data['alertModul'] = true;
		$data['messageModul'] = true;

		$product = Product::find($id);
		if ($product != null)
		{
	        $moreimages = Productphoto::where('product_id','=', $id)->get();
	        $data['records_count'] = count($moreimages);
	        $data['product'] = $product;
	        $data['request'] = $request;
	        $data['moreimages'] = $moreimages;
			
			$request->session()->put('last_url', URL::full());

	        return view('back.product.image.index', $data);
	    }
		return redirect(Crypt::decrypt($setting->admin_url) . '/product')->with('error-message', 'Can not find any product with ID ' . $id);
	}

	public function getAddphoto(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		$data['navModul'] = true;
		$data['helpModul'] = false;
		$data['searchModul'] = false;
		$data['alertModul'] = true;
		$data['messageModul'] = true;

		$product = Product::find($id);
		if ($product != null)
		{
	        $productphoto = new Productphoto;
	        $data['product'] = $product;
	        $data['request'] = $request;
	        $data['productphoto'] = $productphoto;

	        return view('back.product.image.create', $data);
	    }
		return redirect(Crypt::decrypt($setting->admin_url) . '/product')->with('error-message', 'Can not find any product with ID ' . $id);
	}

	public function getSetdefault(Request $request, $id)
    {
		$setting = Setting::first();
        $query = Productphoto::find($id);
        $nodefault = Productphoto::where('product_id','=',$query->product_id)->get();
        foreach ($nodefault as $set) {
            $set->default = 0;
            $set->save();
        }
        $query->default = true;
        $query->save();
    	return redirect(Crypt::decrypt($setting->admin_url) . '/product/more-image/' . $query->product_id)->with('success-message', "Default image has been changed");
    }

    public function postDeletephoto(Request $request, $id)
    {
    	$setting = Setting::first();
        $productphoto = Productphoto::find($id);
        $product_id = $productphoto->product_id;
        if($productphoto->default != 1)
        {
	        File::delete(public_path() . '/usr/img/product/' . $productphoto->gambar);
	        File::delete(public_path() . '/usr/img/product/wm' . $productphoto->gambar);
	        File::delete(public_path() . '/usr/img/product/thumbnail/' . $productphoto->gambar);
	        File::delete(public_path() . '/usr/img/product/thumbnail/wm' . $productphoto->gambar);
	        File::delete(public_path() . '/usr/img/product/small/' . $productphoto->gambar);
	        $productphoto->delete();

        	return redirect(Crypt::decrypt($setting->admin_url) . '/product/more-image/' . $product_id)->with('message-message', "Default photo has been deleted");
        }
        else
        {
        	return redirect(Crypt::decrypt($setting->admin_url) . '/product/more-image/' . $product_id)->with('error-message', "Can't delete default image");
        }
    }

	public function postAddphoto(Request $request, $id)
    {
    	$setting = Setting::first();
		$data['setting'] = $setting;

		$inputs = $request->all();
		$rules = array(
			'image'			=> 'required|max:500',
		);

		$validator = Validator::make($inputs, $rules);
		if ($validator->passes())
		{
			$product = Product::find($id);
			$productphoto = Productphoto::where('product_id', '=', $product->id)->orderBy('id', 'desc')->first();
            if ($productphoto == null) {
                $last_number = 0;
            }
            else {
                $gambar = $productphoto->gambar;
                $name = explode('.', $gambar);
                $name = $name[0];
                $name = explode('_', $name);
                $last_number = $name[1];
            }
            $last_number = $last_number + 1;
            $file_name = $product->id.'_'.$last_number.'_'.Str::slug($product->name, '_').'.jpg';
            
            // upload file
			$request->file('image')->move(public_path() . '/usr/img/product/', $file_name);
            $productphoto = new Productphoto;
            $productphoto->product_id = $product->id;
            $productphoto->gambar = $file_name;
            
            $cek_default = Productphoto::where('product_id','=',$product->id)->where('default','=',1)->first();
            if ($cek_default == null) {
                $productphoto->default = 1;
            }
            else{
                $productphoto->default = 0;
            }

            /* Change the image file name if the field for the slug changed */
            if ($request->hasFile('image'))
			{
				$productphoto->is_crop = false;

				$ques = Que::where('table', '=', 'product')->where('table_id', '=', $id)->get();
				foreach ($ques as $que) {
					$que->delete();
				}
			}

            $productphoto->create_id = Auth::user()->id;
            
            $productphoto->save();   
            
			return redirect(Crypt::decrypt($setting->admin_url) . '/product/photocrop2/' . $id . '/' .$productphoto->id);
	    }
	    else
	    {
			return redirect(Crypt::decrypt($setting->admin_url) . '/product/addphoto/' . $id)->withInput()->withErrors($validator);
	    }
    }

	public function getDetailphoto(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*Menu Authentication*/

		$data['navModul'] = true;
		$data['helpModul'] = true;
		$data['searchModul'] = false;
		$data['alertModul'] = true;
		$data['messageModul'] = true;
		
		
		$productimage = Productphoto::find($id);
		if ($productimage != null)
		{
			$data['productimage'] = $productimage;
			$data['request'] = $request;
	        return view('back.product.image.view', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/product')->with('error-message', 'Can not find product with ID ' . $id);
		}
	}

    /* Photo Crop */
	public function getPhotocrop2(Request $request, $id, $productphoto_id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		$data['messageModul'] = true;
		$data['alertModul'] = true;
		$data['searchModul'] = false;
		$data['helpModul'] = true;
		$data['navModul'] = false;
		
		$product = Product::find($id);
		if ($product != null)
		{
			$checkque = Que::where('admin_id', '=', Auth::user()->id)->where('table', '=', 'product')->where('table_id', '=', $id)->first();
			if($checkque == null)
			{
				$que = new Que;
				$que->admin_id = Auth::user()->id;
				$que->table = 'product';
				$que->table_id = $id;
				$que->url = URL::full();
				$que->save();
			}

			$productphoto = Productphoto::find($productphoto_id);
			$image = 'usr/img/product/' . $productphoto->gambar;
			$data['image'] = $image;

			$w_ratio = 300;
			$h_ratio = 300;

			$getimage = public_path() . '/usr/img/product/' . $productphoto->gambar;
			list($width, $height, $type, $attr) = getimagesize($getimage);

			if($width >= $height)
			{
				$w_akhir = 980;
				$h_akhir = (980 * $height) / $width;

				$w_akhir720 = 720;
				$h_akhir720 = (720 * $height) / $width;

				$w_akhir480 = 480;
				$h_akhir480 = (480 * $height) / $width;

				$w_akhir300 = 300;
				$h_akhir300 = (300 * $height) / $width;
			}

			if($width <= $height)
			{
				$w_akhir = (600 * $width) / $height;
				$h_akhir = 600;

				$w_akhir720 = (500 * $width) / $height;
				$h_akhir720 = 500;

				$w_akhir480 = (400 * $width) / $height;
				$h_akhir480 = 400;

				$w_akhir300 = (300 * $width) / $height;
				$h_akhir300 = 300;
			}

	        $data['w_ratio'] = $w_ratio;
        	$data['h_ratio'] = $h_ratio;

        	$data['w_akhir'] = $w_akhir;
        	$data['h_akhir'] = $h_akhir;

        	$data['w_akhir720'] = $w_akhir720;
        	$data['h_akhir720'] = $h_akhir720;

        	$data['w_akhir480'] = $w_akhir480;
        	$data['h_akhir480'] = $h_akhir480;

        	$data['w_akhir300'] = $w_akhir300;
        	$data['h_akhir300'] = $h_akhir300;

            $request->session()->put('undone-back-url', URL::full());
            $request->session()->put('undone-back-message', "Please crop this image first to continue");

            $data['request'] = $request;
            
			return view('back.crop.index', $data);
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/product')->with('error-message', "Can't find Product with ID " . $id);
		}
	}

	public function postPhotocrop2(Request $request, $id, $productphoto_id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;
		
		$product = Product::find($id);
		if ($product != null)
		{
			$ques = Que::where('table', '=', 'product')->where('table_id', '=', $id)->get();
			foreach ($ques as $que) {
				$que->delete();
			}
			
			if (($request->input('x1') != null) AND ($request->input('w') != 0))
			{
				$productphoto = Productphoto::find($productphoto_id);
				$productphoto->is_crop = true;
				$productphoto->save();

				$image = Image::make(public_path() . '/usr/img/product/' . $productphoto->gambar);
				$image2 = Image::make(public_path() . '/usr/img/product/' . $productphoto->gambar);
				$image3 = Image::make(public_path() . '/usr/img/product/' . $productphoto->gambar);

	            /* Crop image */
	            $product_width = $request->input('w');
	            $product_height = $request->input('h');
	            $pos_x = $request->input('x1');
	            $pos_y = $request->input('y1');
	            $image2->crop(intval($product_width), intval($product_height), intval($pos_x), intval($pos_y));
	            $image3->crop(intval($product_width), intval($product_height), intval($pos_x), intval($pos_y));

	            $getimage = public_path() . '/usr/img/product/' . $productphoto->gambar;
				list($width, $height, $type, $attr) = getimagesize($getimage);

				if($width >= $height)
				{
					if($width > 920)
					{
						$product_width = 920;
						$product_height = null;
					}
					else
					{
						$product_width = $width;
						$product_height = $height;
					}
				}

				if($width <= $height)
				{
					if($height > 600)
					{
						$product_width = null;
						$product_height = 600;
					}
					else
					{
						$product_width = $width;
						$product_height = $height;
					}
				}
	            
	            $image->resize($product_width, $product_height, function ($constraint) {
                    $constraint->aspectRatio();
                });

	            $image->save(public_path() . '/usr/img/product/' . $productphoto->gambar);

	            /* Resize image (optional) */
	            $product_width = 400;
	            $product_height = null;
	            $conserve_proportion = true;
	            $image2->resize($product_width, $product_height, function ($constraint) {
                    $constraint->aspectRatio();
                });


	            $image2->save(public_path() . '/usr/img/product/thumbnail/' . $productphoto->gambar);

                /* Resize thumbnail image (optional) */
	            $product_width = 150;
	            $product_height = null;
	            $conserve_proportion = true;
	            $image3->resize($product_width, $product_height, function ($constraint) {
                    $constraint->aspectRatio();
                });

	            $image3->save(public_path() . '/usr/img/product/small/' . $productphoto->gambar);

	            $request->session()->forget('undone-back-url');
	            $request->session()->forget('undone-back-message');

	            if($request->session()->has('last_url'))
	            {
		            return redirect($request->session()->get('last_url'))->with('success-message', "The image of Product <strong>" . Str::words($product->name, 5) . "</strong> has been Updated");
	            }
	            else
	            {
		            return redirect(Crypt::decrypt($setting->admin_url) . '/product/more-image/' . $product->id)->with('success-message', "The image of Product <strong>" . Str::words($product->name, 5) . "</strong> has been Updated");
	            }

			}
			else
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/product/photocrop2/' . $id . '/' . $productphoto_id)->with('warning-message', 'You must select the cropping area to crop this picture');
			}
		}
		else
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/product')->with('error-message', "Can't find Product with ID " . $id);
		}
	}

    /*Get quatity discount*/
    public function getQuantity(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		/*User Authentication*/
		$admingroup = Admingroup::find(Auth::user()->admingroup_id);
		if ($admingroup->qtydiscount_r != true)
		{
			return redirect(Crypt::decrypt($setting->admin_url) . '/product')->with('error-message', "Sorry you don't have any privilege to access this page.");
		}

		$data['navModul'] = true;
		$data['helpModul'] = true;
		$data['searchModul'] = true;
		$data['alertModul'] = true;
		$data['messageModul'] = true;
		
		
		$data['criteria'] = '';

		$query = Qtydiscount::query()->where('product_id', '=', $id);

		$quantitymin = htmlspecialchars($request->input('quantitymin'));
		if ($quantitymin != null)
		{
			$query->where('quantity', '>=', $quantitymin);
			$data['criteria']['quantitymin'] = $quantitymin;
		}

		$quantitymax = htmlspecialchars($request->input('quantitymax'));
		if ($quantitymax != null)
		{
			$query->where('quantity', '<=', $quantitymax);
			$data['criteria']['quantitymax'] = $quantitymax;
		}

		$discountmin = htmlspecialchars($request->input('discountmin'));
		if ($discountmin != null)
		{
			$query->where('discount', '>=', $discountmin);
			$data['criteria']['discountmin'] = $discountmin;
		}

		$discountmax = htmlspecialchars($request->input('discountmax'));
		if ($discountmax != null)
		{
			$query->where('discount', '<=', $discountmax);
			$data['criteria']['discountmax'] = $discountmax;
		}

		$is_membergroup = htmlspecialchars($request->input('is_membergroup'));
		if ($is_membergroup != null)
		{
			$query->where('is_membergroup', '=', $is_membergroup);
			$data['criteria']['is_membergroup'] = $is_membergroup;
		}

		$is_active = htmlspecialchars($request->input('is_active'));
		if ($is_active != null)
		{
			$query->where('is_active', '=', $is_active);
			$data['criteria']['is_active'] = $is_active;
		}

		$order_by = htmlspecialchars($request->input('order_by'));
		$order_method = htmlspecialchars($request->input('order_method'));
		if ($order_by != null)
		{
			if (($order_by == 'is_active') or ($order_by == 'is_admin'))
			{
				$query->orderBy($order_by, $order_method)->orderBy('id', 'desc');
			}
			else
			{
			// return 'Work';
				$query->orderBy($order_by, $order_method);
			}
			$data['criteria']['order_by'] = $order_by;
			$data['criteria']['order_method'] = $order_method;
		}
		else
		{
			$query->orderBy('id', 'desc');
		}

		$all_records = $query->get();
		$records_count = count($all_records);
		$data['records_count'] = $records_count;

		$per_page = 20;
		$data['per_page'] = $per_page;
		$qtydiscounts = $query->paginate($per_page);
		$data['qtydiscounts'] = $qtydiscounts;

		$product = Product::find($id);
		$data['product'] = $product;

		$request->flash();

        return view('back.qtydiscount.index', $data);
	}

	/*
		Product Substitution
	*/

		public function getSubstitution(Request $request, $id)
		{
			$setting = Setting::first();
			$data['setting'] = $setting;

			$data['navModul'] = true;
			$data['helpModul'] = true;
			$data['searchModul'] = false;
			$data['alertModul'] = true;
			$data['messageModul'] = true;

			$product = Product::find($id);
			if ($product != null)
			{
		        $substitutions = Substitution::where('product_id','=', $id)->get();
		        $data['records_count'] = count($substitutions);
		        $data['product'] = $product;
		        $data['request'] = $request;
		        $data['substitutions'] = $substitutions;
				
				$request->session()->put('last_url', URL::full());

		        return view('back.product.substitution.index', $data);
		    }
			return redirect(Crypt::decrypt($setting->admin_url) . '/product')->with('error-message', 'Can not find any product with ID ' . $id);
		}

	/*
		Add Substitution
	*/
		public function getAddSubstitution(Request $request, $id)
		{
			$setting = Setting::first();
			$data['setting'] = $setting;

			/*User Authentication*/

			$admingroup = Admingroup::find(Auth::user()->admingroup_id);
			if ($admingroup->product_c != true)
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/product')->with('error-message', "Sorry you don't have any privilege to access this page.");
			}

			/*Menu Authentication*/

			$data['navModul'] = true;
			$data['helpModul'] = true;
			$data['searchModul'] = false;
			$data['alertModul'] = true;
			$data['messageModul'] = true;
			
			$product = Product::find($id);
			if ($product != null)
			{
		        $substitution = new Substitution;

		        $getsubstitutions = Substitution::where('product_id', '=', $product->id)->get();
		        foreach ($getsubstitutions as $getsubstitution) {
		        	$getsubstitutionids[] = $getsubstitution->substitution_id;
		        }

		        if($getsubstitutions->isEmpty())
		        {
			        $getproducts = Product::where('is_active', '=', true)->where('id', '!=', $id)->orderBy('name')->get();
		        }
		        else
		        {
			        $getproducts = Product::whereNotIn('id', $getsubstitutionids)->where('id', '!=', $id)->where('is_active', '=', true)->orderBy('name')->get();
		        }
		        	
	        	$product_options[''] = "Select Product";
		        foreach ($getproducts as $getproduct) {
		        	$product_options[$getproduct->id] = $getproduct->name;
		        }
		        $data['product_options'] = $product_options;

		        $data['product'] = $product;
		        $data['request'] = $request;
		        $data['substitution'] = $substitution;
				
		        return view('back.product.substitution.create', $data);
		    }
			return redirect(Crypt::decrypt($setting->admin_url) . '/product')->with('error-message', 'Can not find any product with ID ' . $id);
		}

	/*
		post Add Substitution
	*/
		public function postAddSubstitution(Request $request, $id)
		{
			$setting = Setting::first();
			$data['setting'] = $setting;
			
			$inputs = $request->all();
			$rules = array(
				'product'		=> 'required',
			);

			$validator = Validator::make($inputs, $rules);
			if ($validator->passes())
			{
				$substitution = new Substitution;
				$substitution->product_id = $id;
				$substitution->substitution_id = htmlspecialchars($request->input('product'));
				$substitution->create_id = Auth::user()->id;
				$substitution->update_id = Auth::user()->id;
				$substitution->save();

				return redirect(Crypt::decrypt($setting->admin_url) . '/product/substitution/' . $id)->with('success-message', "Substitution for <strong>" . Str::words($substitution->product->name, 5) . "</strong> has been Created");
			}
			else
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/product/add-substitution/' . $id)->withInput()->withErrors($validator);
			}
		}

	/*
		Show Substitution
	*/
		public function getViewSubstitution(Request $request, $id)
		{
			$setting = Setting::first();
			$data['setting'] = $setting;

			/*User Authentication*/

			$admingroup = Admingroup::find(Auth::user()->admingroup_id);
			if ($admingroup->product_r != true)
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/product')->with('error-message', "Sorry you don't have any privilege to access this page.");
			}

			/*Menu Authentication*/

			$data['navModul'] = true;
			$data['helpModul'] = true;
			$data['searchModul'] = false;
			$data['alertModul'] = true;
			$data['messageModul'] = true;
			
			
			$substitution = Substitution::find($id);
			if ($substitution != null)
			{
				$product = Product::find($substitution->product_id);
				$data['product'] = $product;
				$data['substitution'] = $substitution;
				$data['request'] = $request;
		        return view('back.product.substitution.view', $data);
			}
			else
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/product')->with('error-message', 'Can not find substitution with ID ' . $id);
			}
		}

	/*
		Edit Substitution
	*/
		public function getEditSubstitution(Request $request, $id)
		{
			$setting = Setting::first();
			$data['setting'] = $setting;

			/*User Authentication*/

			$admingroup = Admingroup::find(Auth::user()->admingroup_id);
			if ($admingroup->product_u != true)
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/product')->with('error-message', "Sorry you don't have any privilege to access this page.");
			}

			/*Menu Authentication*/

			$data['navModul'] = true;
			$data['helpModul'] = true;
			$data['searchModul'] = false;
			$data['alertModul'] = true;
			$data['messageModul'] = true;
			
	        $substitution = Substitution::find($id);
			if ($substitution != null)
			{
		        $getsubstitutions = Substitution::where('product_id', '=', $substitution->product_id)->where('id', '!=', $id)->get();
		        foreach ($getsubstitutions as $getsubstitution) {
		        	$getsubstitutionids[] = $getsubstitution->substitution_id;
		        }

		        if($getsubstitutions->isEmpty())
		        {
			        $getproducts = Product::where('is_active', '=', true)->where('id', '!=', $substitution->product_id)->orderBy('name')->get();
		        }
		        else
		        {
			        $getproducts = Product::whereNotIn('id', $getsubstitutionids)->where('id', '!=', $substitution->product_id)->where('is_active', '=', true)->orderBy('name')->get();
		        }
		        	
	        	$product_options[''] = "Select Product";
		        foreach ($getproducts as $getproduct) {
		        	$product_options[$getproduct->id] = $getproduct->name;
		        }
		        $data['product_options'] = $product_options;

		        $data['substitution'] = $substitution;
		        $data['request'] = $request;
		        $data['substitution'] = $substitution;
				
		        return view('back.product.substitution.edit', $data);
		    }
			return redirect(Crypt::decrypt($setting->admin_url) . '/product')->with('error-message', 'Can not find any product with ID ' . $id);
		}

	/*
		post Edit Substitution
	*/
		public function postEditSubstitution(Request $request, $id)
		{
			$setting = Setting::first();
			$data['setting'] = $setting;
			
			$inputs = $request->all();
			$rules = array(
				'product'		=> 'required',
			);

			$validator = Validator::make($inputs, $rules);
			if ($validator->passes())
			{
				$substitution = Substitution::find($id);
				// $substitution->product_id = $id;
				$substitution->substitution_id = htmlspecialchars($request->input('product'));
				$substitution->update_id = Auth::user()->id;
				$substitution->save();

				return redirect(Crypt::decrypt($setting->admin_url) . '/product/substitution/' . $id)->with('success-message', "Substitution for <strong>" . Str::words($substitution->product->name, 5) . "</strong> has been Updated");
			}
			else
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/product/edit-substitution/' . $id)->withInput()->withErrors($validator);
			}
		}

	/*
		Delete Substitution
	*/
		public function postDeleteSubstitution(Request $request, $id)
		{
			$setting = Setting::first();
			$data['setting'] = $setting;

			/*User Authentication*/

			$admingroup = Admingroup::find(Auth::user()->admingroup_id);
			if ($admingroup->product_r != true)
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/dashboard')->with('error-message', "Sorry you don't have any privilege to access this page.");
			}
			
			$substitution = Substitution::find($id);
			if ($substitution != null)
			{
				$substitution_name = Str::words($substitution->name, 5);
				$substitution->delete();

				return redirect(Crypt::decrypt($setting->admin_url) . '/product/substitution/' . $substitution->product_id)->with('success-message', "Substitution for <strong>" . $substitution_name . "</strong> has been Deleted");
			}
			else
			{
				return redirect(Crypt::decrypt($setting->admin_url) . '/product')->with('error-message', 'Can not find substitution with ID ' . $id);
			}
		}


	public function getRak(Request $request, $id)
	{
		$setting = Setting::first();
		$data['setting'] = $setting;

		$rak = Rak::find($id);
		$data['rak'] = $rak;

		Session::push('add_rak', $id);

		$no = count(Session::get('add_rak'));
		$data['no'] = $no;

		return view('back.product.rak', $data);
	}

	public function getDrop(Request $request, $id)
	{
		$getsessions = Session::get('add_rak');
		foreach ($getsessions as $getsession) {
			$getsessionids[] = $getsession;
		}
		// dd($getsessions);
		$removedatas = array_diff($getsessionids, array($id));
		// dd($removedata);
		Session::forget('add_rak');
		foreach($removedatas as $removedata)
		{
			Session::push('add_rak', $removedata);
		}
		// dd(Session::get('add_product'));

		if(count($removedatas) == 0)
		{
			$raks = Rak::where('is_active', '=', true)->orderBy('name')->get();
		}
		else
		{
			$raks = Rak::whereNotIn('id', $removedatas)->where('is_active', '=', true)->orderBy('name')->get();
		}
		$rak_options[] = 'Select Rak';
		foreach ($raks as $rak) {
			$rak_options[$rak->id] = $rak->name;
		}
		$data['rak_options'] = $rak_options;

		return view('back.product.replace', $data);
	}

	public function getReplace(Request $request)
	{
		$raks = Rak::whereNotIn('id', Session::get('add_rak'))->where('is_active', '=', true)->orderBy('name')->get();

		$rak_options[] = 'Select Rak';
		if(count(Session::get('add_rak')) < 2)
		{
			foreach ($raks as $rak) {
				$rak_options[$rak->id] = $rak->name;
			}
		}
		$data['rak_options'] = $rak_options;

		return view('back.product.replace', $data);
	}

	/*

	*/

	/*Stock system*/
	// public function getStock(Request $request, $id)
	// {
	// 	$setting = Setting::first();
	// 	$data['setting'] = $setting;

	// 	$data['navModul'] = true;
	// 	$data['helpModul'] = true;
	// 	$data['searchModul'] = false;
	// 	$data['alertModul'] = true;
	// 	$data['messageModul'] = true;
		

	// 	$query = Inventory::where('product_id', '=', $id)->orderBy('created_at', 'desc');
	// 	$all_records = $query->get();
	// 	$records_count = count($all_records);
	// 	$data['records_count'] = $records_count;

	// 	$per_page = 20;
	// 	$data['per_page'] = $per_page;
	// 	$stocks = $query->paginate($per_page);
	// 	$data['stocks'] = $stocks;

	// 	$product = Product::find($id);
	// 	$data['product'] = $product;

	// 	return view('back.product.stock', $data);
	// }

	// public function getChangeStock($id)
	// {
	// 	$setting = Setting::first();
	// 	$data['setting'] = $setting;

	// 	$data['navModul'] = true;
	// 	$data['helpModul'] = true;
	// 	$data['searchModul'] = false;
	// 	$data['alertModul'] = true;
	// 	$data['messageModul'] = true;
		

	// 	$stock = new Inventory;
	// 	$data['stock'] = $stock;

	// 	$product = Product::find($id);
	// 	$data['product'] = $product;

	// 	return view('back.product.change_stock', $data);
	// }

	// public function postChangeStock($id)
	// {
	// 	$setting = Setting::first();
	// 	$data['setting'] = $setting;
		
	// 	$inputs = $request->all();
	// 	$rules = array(
	// 		'amount' 		=> 'required|numeric',
	// 		'status'	 	=> 'required',
	// 	);

	// 	$validator = Validator::make($inputs, $rules);
	// 	if ($validator->passes())
	// 	{

	// 		$product = Product::find($id);
	// 		DB::transaction(function()use($id){
	// 			$product = Product::find($id);
	// 			$inventory = new Inventory;
	// 			$inventory->product_id = $product->id;
	// 			$inventory->amount = htmlspecialchars($request->input('amount'));
	// 			$inventory->last_stock = $product->stock;
	// 			if ($request->input('status') == 0) {
	// 				$inventory->final_stock = $product->stock + $request->input('amount');
	// 			}
	// 			else
	// 			{
	// 				$inventory->final_stock = $product->stock - $request->input('amount');
	// 			}
	// 			$inventory->status = htmlspecialchars($request->input('status'));
	// 			$inventory->note = htmlspecialchars($request->input('note'));
	// 			$inventory->save();

	// 			$history = new History;
	// 			$history->history_id = $inventory->id + 1000;
	// 			$history->product_id = $product->id;
	// 			$history->amount = $inventory->amount;
	// 			$history->last_stock = $product->stock;
	// 			$history->final_stock = $inventory->final_stock;
	// 			if ($request->input('status') == 0) {
	// 				$history->status = 'Stock In';
	// 			}
	// 			else
	// 			{
	// 				$history->status = 'Stock Out';
	// 			}
	// 			$history->note = $inventory->note;
	// 			$history->save();

	// 			if ($request->input('status') == 0) {
	// 				$product_qty = $product->stock;
	// 				$product->stock = $product_qty + htmlspecialchars($request->input('amount'));

	// 				if($product_qty == 0)
	// 	 			{
	// 		 			$data['product_id'] = $product->id;
	// 	 				$notifications = Notification::where('product_id', '=', $product->id)->where('is_notify', '=', 1)->get();
	// 	 				if(count($notifications) != 0)
	// 	 				{
	// 			 			$setting = Setting::first();
	// 						if($setting->email_sender != null)
	// 						{
	// 			 				$subject = $setting->name . " | Stok produk: " . $product->name . ", sudah ada.";
	// 		 					foreach ($notifications as $notification) 
	// 		 					{
	// 								Mail::queue('emails.to_member.notification', $data, function($message) use ($subject, $setting, $notification)
	// 								{
	// 									$message->from($setting->email_sender, $setting->name);
	// 									$message->to($notification->email);
	// 									$message->subject($subject);
	// 								});
									
	// 				 				$notification->is_notify = 0;
	// 				 				$notification->save();
	// 							}
	// 	 					}
	// 	 				}
	// 	 			}
	// 			} 
	// 			else 
	// 			{
	// 				$product->stock = $product->stock - htmlspecialchars($request->input('amount'));
	// 			}

	// 			$product->save();
	// 		});
				
	// 		return redirect(Crypt::decrypt($setting->admin_url) . '/product/stock/' . $id)->with('success-message', "Stock for product <strong>$product->name</strong> has been updated");
	// 	}
	// 	else
	// 	{
	// 		return redirect(Crypt::decrypt($setting->admin_url) . '/product/related/' . $id)->withInput()->withErrors($validator);
	// 	}
	// }

	// public function getDeleteStock($id)
	// {
	// 	$setting = Setting::first();
	// 	$data['setting'] = $setting;

	// 	$inventory = Inventory::find($id);
	// 	if ($inventory != null) {
	// 		$product_name = Product::find($inventory->product_id);
	// 		$inventory->delete();

	// 		return redirect(Crypt::decrypt($setting->admin_url) . '/stock')->with('success-message', "Inventory for Product <strong>$product_name->name</strong> has been deleted.");
	// 	} else {
	// 		return redirect(Crypt::decrypt($setting->admin_url) . '/stock')->with('error-message', "Can't find Inventory with ID $id.");
	// 	}
	// }

	// public function getHistory(Request $request, $id)
	// {
	// 	$setting = Setting::first();
	// 	$data['setting'] = $setting;

	// 	$data['navModul'] = true;
	// 	$data['helpModul'] = true;
	// 	$data['searchModul'] = false;
	// 	$data['alertModul'] = true;
	// 	$data['messageModul'] = true;
		

	// 	$product = Product::find($id);
	// 	$data['product'] = $product;

	// 	$data['scripts'] = array('js/jquery-ui.js');
 //        $data['styles'] = array('css/jquery-ui-back.css');

	// 	return view('back.product.report_history', $data);
	// }

	// public function getHistoryReport($id)
	// {
	// 	$setting = Setting::first();
	// 	$data['setting'] = $setting;

	// 	$data['navModul'] = false;
	// 	$data['helpModul'] = false;
	// 	$data['searchModul'] = false;
	// 	$data['alertModul'] = true;
	// 	$data['messageModul'] = true;
		

	// 	$between = $request->input('between');
 //        $data['between'] = $between;

 //        $end_date = $request->input('end');
 //        $end = date('Y-m-d', strtotime($end_date)) . ' 23:59:59';
 //        $data['end'] = $end_date;

	// 	$query = History::where('product_id', '=', $id)->whereBetween('created_at', array($between, $end))->orderBy('created_at', 'asc');
	// 	$all_records = $query->get();
	// 	$records_count = count($all_records);
	// 	$data['records_count'] = $records_count;

	// 	$histories = $query->get();
	// 	$data['histories'] = $histories;

	// 	$product = Product::find($id);
	// 	$data['product'] = $product;

	// 	return view('back.product.history', $data);
	// }
}