<?php

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Po;
use App\Models\Podetail;
use App\Models\Ri;
use App\Models\Ridetail;
use App\Models\Productstock;
use App\Models\Returndetail;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/**
 * CUSTOM FUNCTIONS
 */

function limitChar($string, $max) 
{
	/**
	 * Untuk membuat maksimal karakter yang mau di tampilkan 
	 */
	
	$word_length = strlen($string);
    if($word_length > $max)
    {
		$hasil = substr($string, 0, $max) . '...';
    }
    else
    {
		$hasil = $string;
    }
	return $hasil;
};

function digitGroup($var) 
{
	/**
	 * Untuk merubah menjadi number format --> 10.000
	 */
	
	return number_format((float)$var, 2,",",".");
};

function removeDigitGroup($var) 
{
	/**
	 * Untuk merubah dari number format ke number normal --> 10000
	 */
	
	return str_replace(',', '', $var);
}

function rupiah($nilai) 
{
	return "Rp " . number_format((float)$nilai, 0,".",",");
}

function rupiah2($nilai) 
{
	return "Rp " . number_format((float)$nilai, 0,",",".");
}

function rupiah3($nilai) 
{
	return number_format((float)$nilai, 0,",",".");
}

function tanggal($date)
{
	return date('d F Y', strtotime($date));
}

function tanggal2($date) 
{
	return date('d/m/Y', strtotime($date));
}

function karakter($word_name, $max) 
{
	$word_lenght = strlen($word_name);
    if($word_lenght >= ($max + 1))
    {
        $lenght = $max - $word_lenght;
    }
    else
    {
        $lenght = null;
    }
	
	if($lenght == null)
	{
		$hasil = $word_name;
	}
	else
	{
		$hasil = substr($word_name, 0, $lenght) . '..';
	}
	return $hasil;

}

function weight_total($nilai) 
{
	$setting = Setting::first();
	$more = $nilai % 1000;
	$weight_total2 = ($nilai - $more) / 1000;
	if ($more <= $setting->weight_tolerance)
	{
		$more = 0;
	}
	else
	{
		$more = 1;
	}
	$weight_total3 = $weight_total2 + $more;
	if($weight_total3 == 0)
	{
		$weight_total3 = 1;
	}
	return $weight_total3;
}

function update_inventory($productstock_id, $date, $id)
{
	$last_inventory = Inventory::where('productstock_id', '=', $productstock_id)->where('date', '=', $date)->where('id', '=', $id)->first();

	if($last_inventory == null)
	{
		$last_inventory = Inventory::where('productstock_id', '=', $productstock_id)->where(function($query1) use ($date, $id)
		{
			$query1->where('date', '<', $date);
			$query1->orWhere(function($query2) use($date, $id)
			{
				$query2->where('date', '=', $date);
				$query2->where('id', '<', $id);
			});
		})->orderBy('date', 'desc')->orderBy('id', 'desc')->first();
	}

	if ($last_inventory == null)
	{
		$qtylast = 0;
		$pricelast = 0;
	}
	else
	{
		$qtylast = $last_inventory->qty_z;
		$pricelast = $last_inventory->price_z;
	}

	$following_inventories = Inventory::where('productstock_id', '=', $productstock_id)->where(function($query1) use ($date, $id)
	{
		$query1->where('date', '>', $date);
		$query1->orWhere(function($query2) use($date, $id)
		{
			$query2->where('date', '=', $date);
			$query2->Where('id', '>', $id);
		});
	})->orderBy('date', 'asc')->orderBy('id', 'asc')->get();

	if (!$following_inventories->isEmpty())
	{
		foreach ($following_inventories as $following_inventory)
		{
			$following_inventory->qty_last = $qtylast;
			$following_inventory->price_last = $pricelast;

			$return = Inventory::where('date', '<=', $following_inventory->date)->where('id', '<', $following_inventory->id)->where('productstock_id', '=', $following_inventory->productstock_id)->orderBy('date', 'desc')->orderBy('id', 'desc')->first();

			// Recounting output
			if ($following_inventory->type != 'Ri')
			{
				if($following_inventory->type != 'Adj')
				{
					$following_inventory->price_out = $return->price_z;
				}
			}
			
			// Recounting final
			$following_inventory->qty_z = $following_inventory->qty_last + $following_inventory->qty_in - $following_inventory->qty_out;
			$qtylast = $following_inventory->qty_z;
			// if ($following_inventory->qty_z > 0)
			// {
			if ($following_inventory->type == 'Ri')
			{
				if($following_inventory->qty_z == 0)
				{
					$following_inventory->price_z = 0;
					// $following_inventory->price_z = (($following_inventory->qty_last * $following_inventory->price_last) + ($following_inventory->qty_in * $following_inventory->price_in) - ($following_inventory->qty_out * $following_inventory->price_out)) / 1;
				}
				else
				{
					$following_inventory->price_z = (($following_inventory->qty_last * $following_inventory->price_last) + ($following_inventory->qty_in * $following_inventory->price_in) - ($following_inventory->qty_out * $following_inventory->price_out)) / $following_inventory->qty_z;
				}
				// return '((' . $following_inventory->qty_last  . '*' . $following_inventory->price_last . ') + (' . $following_inventory->qty_in . '*' . $following_inventory->price_in . ') - (' . $following_inventory->qty_out . '*' . $following_inventory->price_out . ')) /' $following_inventory->qty_z;
			}
			else
			{
				if($following_inventory->qty_z == 0)
				{
					$following_inventory->price_z = 0;
				}
				else
				{
					$following_inventory->price_z = $return->price_z;
				}
			}
			// }
			// else
			// {
				// $following_inventory->price_z = 0;
			// }
			$pricelast = $following_inventory->price_z;
			$following_inventory->save();

			if ($following_inventory->type == 'R')
			{
				$returndetail = Returndetail::find($following_inventory->type_id);
				$ridetail = Ridetail::find($returndetail->ridetail_id);
				if($ridetail->ri->is_invoice == true)
				{
					$invoicedetail = Invoicedetail::where('ridetail_id', '=', $ridetail->id)->first();
					if($invoicedetail->price != $following_inventory->price_out)
					{
						$checkpricegap = Pricegap::where('returndetail_id', '=', $returndetail->id)->first();
						if($checkpricegap == null)
						{
							$pricegap = new Pricegap;
						}
						else
						{
							$pricegap = $checkpricegap;
						}
						$pricegap->price = $invoicedetail->price - $following_inventory->price_out;
						$pricegap->save();
					}
				}
				else
				{
					// dd($ridetail->podetail->id);
					// $invoicedetail = Invoicedetail::where('ridetail_id', '=', $ridetail->id)->first();
					if($ridetail->podetail->price != $following_inventory->price_out)
					{
						$checkpricegap = Pricegap::where('returndetail_id', '=', $returndetail->id)->first();
						if($checkpricegap == null)
						{
							$pricegap = new Pricegap;
						}
						else
						{
							$pricegap = $checkpricegap;
						}
						$pricegap->price = $ridetail->podetail->price - $following_inventory->price_out;
						$pricegap->save();
					}
				}
			}


			$productstock = Productstock::find($productstock_id);
			$productstock->stock = $following_inventory->qty_z;
			$productstock->save();

			$product = Product::find($productstock->product_id);
			$product->price = $following_inventory->price_z;
			$product->save();
		}
	}
}

function recalculating_inventory($product_id)
{
	$first_inventory = Inventory::where('product_id', '=', $product_id)->orderBy('date', 'asc')->orderBy('id', 'asc')->first();
	if($first_inventory != null)
	{	
		$first_inventory->qty_last = 0;
		$first_inventory->price_last = 0.00;
		$first_inventory->qty_z = $first_inventory->qty_in;
		$first_inventory->price_z = $first_inventory->price_in;
		$first_inventory->save();

		$qtylast = $first_inventory->qty_z;
		$pricelast = $first_inventory->price_z;

		$following_inventories = Inventory::where('product_id', '=', $product_id)->where(function($query1) use ($first_inventory)
		{
			$query1->where('date', '>', $first_inventory->date);
			$query1->orWhere(function($query2) use($first_inventory)
			{
				$query2->where('date', '=', $first_inventory->date);
				$query2->Where('id', '>', $first_inventory->id);
			});
		})->orderBy('date', 'asc')->orderBy('id', 'asc')->get();

		if (!$following_inventories->isEmpty())
		{
			foreach ($following_inventories as $following_inventory)
			{
				$following_inventory->qty_last = $qtylast;
				$following_inventory->price_last = $pricelast;

				$return = Inventory::where('date', '<=', $following_inventory->date)->where('id', '<', $following_inventory->id)->where('product_id', '=', $following_inventory->product_id)->orderBy('date', 'desc')->orderBy('id', 'desc')->first();

				// Recounting output
				if ($following_inventory->type != 'Ri')
				{
					if($following_inventory->type != 'Adj')
					{
						$following_inventory->price_out = $return->price_z;
					}
				}
				
				// Recounting final
				$following_inventory->qty_z = $following_inventory->qty_last + $following_inventory->qty_in - $following_inventory->qty_out;
				$qtylast = $following_inventory->qty_z;
				// if ($following_inventory->qty_z > 0)
				// {
				if ($following_inventory->type == 'Ri')
				{
					if($following_inventory->qty_z == 0)
					{
						$following_inventory->price_z = 0;
						// $following_inventory->price_z = (($following_inventory->qty_last * $following_inventory->price_last) + ($following_inventory->qty_in * $following_inventory->price_in) - ($following_inventory->qty_out * $following_inventory->price_out)) / 1;
					}
					else
					{
						$following_inventory->price_z = (($following_inventory->qty_last * $following_inventory->price_last) + ($following_inventory->qty_in * $following_inventory->price_in) - ($following_inventory->qty_out * $following_inventory->price_out)) / $following_inventory->qty_z;
					}
					// return '((' . $following_inventory->qty_last  . '*' . $following_inventory->price_last . ') + (' . $following_inventory->qty_in . '*' . $following_inventory->price_in . ') - (' . $following_inventory->qty_out . '*' . $following_inventory->price_out . ')) /' $following_inventory->qty_z;
				}
				else
				{
					if($following_inventory->qty_z == 0)
					{
						$following_inventory->price_z = 0;
					}
					else
					{
						$following_inventory->price_z = $return->price_z;
					}
				}
				// }
				// else
				// {
					// $following_inventory->price_z = 0;
				// }
				$pricelast = $following_inventory->price_z;
				$following_inventory->save();

				if ($following_inventory->type == 'R')
				{
					$returndetail = Returndetail::find($following_inventory->type_id);
					$ridetail = Ridetail::find($returndetail->ridetail_id);
					if($ridetail->ri->is_invoice == true)
					{
						$invoicedetail = Invoicedetail::where('ridetail_id', '=', $ridetail->id)->first();
						if($invoicedetail->price != $following_inventory->price_out)
						{
							$checkpricegap = Pricegap::where('returndetail_id', '=', $returndetail->id)->first();
							if($checkpricegap == null)
							{
								$pricegap = new Pricegap;
							}
							else
							{
								$pricegap = $checkpricegap;
							}
							$pricegap->price = $invoicedetail->price - $following_inventory->price_out;
							$pricegap->save();
						}
					}
					else
					{
						// $invoicedetail = Invoicedetail::where('ridetail_id', '=', $ridetail->id)->first();
						if($ridetail->podetail->price != $following_inventory->price_out)
						{
							$checkpricegap = Pricegap::where('returndetail_id', '=', $returndetail->id)->first();
							if($checkpricegap == null)
							{
								$pricegap = new Pricegap;
							}
							else
							{
								$pricegap = $checkpricegap;
							}
							$pricegap->price = $ridetail->podetail->price - $following_inventory->price_out;
							$pricegap->save();
						}
					}
				}


				$productstock = Productstock::find($product_id);
				$productstock->stock = $following_inventory->qty_z;
				$productstock->save();

				$product = Product::find($productstock->product_id);
				$product->price = $following_inventory->price_z;
				$product->save();
			}
		}
	}
}


Route::get('creidsdb', function() {
    return view('back.template.creidsdb');
});

Route::get('creidsdbmigrate', function()
{
	echo 'Initiating DB Migrate...<br>';
	define('STDIN',fopen("php://stdin","r"));
	Artisan::call('migrate', ['--quiet' => true, '--force' => true]);
	// echo 'DB Migrate done.<br><br>';
	return "DB Migrate done.<br><br>";
});

Route::get('creidsdbfill', function()
{
	echo 'Initiating DB Seed...<br>';
	define('STDIN',fopen("php://stdin","r"));
	Artisan::call('db:seed', ['--quiet' => true, '--force' => true]);
	// echo 'DB Seed done.<br>';
	return "DB Seed done.<br>";
});

Route::get('creidsdbrollback', function()
{
	echo 'Initiating DB Rollback...<br>';
	define('STDIN',fopen("php://stdin","r"));
	Artisan::call('migrate:rollback', ['--quiet' => true, '--force' => true]);
	// echo 'DB Delete done.<br>';
	return "DB Delete done.<br>";
});

// Route::get('/', function () {
	// return "jalan";
// });

if (Schema::hasTable('settings'))
{
	$setting = Setting::first();
	if($setting != null)
	{
		Route::get('maintenance', function () {
		    return view('errors.optimize');
		});


		/*
			ROUTE FOR BACK END
		*/

			Route::group(['namespace' => 'Back', 'guard'=>'admin', 'prefix' => Crypt::decrypt($setting->admin_url)], function() use ($setting) 
			{
				
				/*
					LOGIN CONTROLLER
				*/
			    Route::get('/', 'AuthController@getLogin')->name('login');
			    Route::post('/', 'AuthController@postLogin')->name('login');
			    Route::get('logout', 'AuthController@getLogout')->name('logout');

			    /*
					FORGOT PASSWORD CONTROLLER
				*/
			    Route::get('password/remind', 'ReminderController@getRemind');
			    Route::post('password/remind', 'ReminderController@postRemind');
			    Route::get('password/reset/{token?}', 'ReminderController@getReset');
			    Route::post('password/reset/{token?}', 'ReminderController@postReset');

			    /**
				 * CROPPING ROUTE
				 */

				Route::get('cropper/{width}/{height}', function(Request $request, $width, $height){
					if ($request->ajax())
					{
						$data['w_ratio'] = $width;
						$data['h_ratio'] = $height;

						return view('back.crop.jquery', $data);
					}
				});

				Route::group(['middleware' => ['authback', 'undoneback', 'sessiontimeback', 'backlastactivity']], function(){

					/* 
						DASHBOARD 
					*/
					Route::get('dashboard', function(Request $request){
						$setting = Setting::first();
						$data['setting'] = $setting;

						$data['messageModul'] = true;
						$data['alertModul'] = true;
						$data['searchModul'] = false;
						$data['helpModul'] = true;
						$data['navModul'] = true;

						$data['request'] = $request;
						$request->session()->put('last_url', URL::full());

						return view('back.dashboard.index', $data);
					});

					/*
						ADMIN GROUP CONTROLLER
					*/
					Route::resource('admingroup', 'AdmingroupController');

					/*
						ADMIN CONTROLLER
					*/
						Route::get('admin/edit-profile', 'AdminController@getEditProfile');
						Route::post('admin/edit-profile', 'AdminController@postEditProfile');
						Route::get('admin/banned/{id}', 'AdminController@getBanned');
					Route::resource('admin', 'AdminController');

					/*
						BANK CONTROLLER
					*/
					Route::resource('bank', 'BankController');

					/*
						ADJUSTMENT CONTROLLER
					*/
					Route::resource('adjustment', 'AdjustmentController');

					/* 
						GUDANG CONTROLLER 
					*/
					Route::resource('gudang', 'GudangController');

					/* 
						RAK CONTROLLER 
					*/
					Route::resource('rak', 'RakController');

					/* 
						SUPPLIER CONTROLLER 
					*/
					Route::resource('supplier', 'SupplierController');

					/* 
						KENDARAAN CONTROLLER 
					*/
					Route::resource('kendaraan', 'KendaraanController');

					/*
						PAYMENT CONTROLLER
					*/
						Route::get('payment/pdf/{id}', 'PaymentController@getPdf');
						Route::get('payment/invoice/{id}', 'PaymentController@getInvoice');
					Route::resource('payment', 'PaymentController');

					/*
						RETURN CONTROLLER
					*/
						Route::get('return/ri/{supplier}', 'ReturnController@getRi');
						Route::get('return/product/{id}', 'ReturnController@getProduct');
						Route::get('return/replace/{id}', 'ReturnController@getReplace');
						Route::get('return/add/{id}/{productid?}/{qty?}/{price?}', 'ReturnController@getAdd');
						Route::get('return/form/{ridetailid}/{productid}/{qty}/{price}', 'ReturnController@getForm');
						// Route::get('return/addedit/{id}/{returndetail_id}', 'ReturnController@getAddedit');
						Route::get('return/drop/{id}', 'ReturnController@getDrop');

						Route::get('return/print/{id}', 'ReturnController@getPrint');
						Route::get('return/pdf/{id}', 'ReturnController@getPdf');
					Route::resource('return', 'ReturnController');

					/*
						PO CONTROLLER
					*/
						Route::get('po/send/{id}', 'PoController@getSend');
						Route::get('po/abort/{id}', 'PoController@getAbort');
						Route::get('po/print/{id}', 'PoController@getPrint');
						Route::get('po/replace', 'PoController@getReplace');
						Route::get('po/add/{productid?}/{qty?}/{price?}/{discounttype?}/{discount?}', 'PoController@getAdd');
						Route::get('po/form/{productid}/{qty}/{price}/{discounttype}/{discount}', 'PoController@getForm');
						Route::get('po/drop/{id}', 'PoController@getDrop');
					Route::resource('po', 'PoController');

					/*
						RI CONTROLLER
					*/
						Route::get('ri/find-po/{supplier_id}', 'RiController@getFindPo');
						Route::get('ri/podetail/{id}', 'RiController@getPodetail');
						Route::get('ri/po/{date}', 'RiController@getPo');
						Route::get('ri/podetail/{id}', 'RiController@getPodetail');

						Route::get('ri/send/{id}', 'RiController@getSend');
						Route::get('ri/print/{id}', 'RiController@getPrint');
						Route::get('ri/replace', 'RiController@getReplace');
						Route::get('ri/add/{id}', 'RiController@getAdd');
						Route::get('ri/drop/{id}', 'RiController@getDrop');
						Route::get('ri/pdf/{id}', 'RiController@getPdf');
					Route::resource('ri', 'RiController');

					/*
						UNINVOICE DEBT CONTROLLER
					*/
					Route::resource('uninvoiced-debt', 'HbtController');

					/*
						INVOICE REPORT CONTROLLER
					*/
						Route::get('invoice/report', 'InvoiceController@getReport');
						Route::post('invoice/report', 'InvoiceController@postReport');
						Route::get('invoice/tprint/{supplier}', 'InvoiceController@getTprint');

					/*
						INVOICE CONTROLLER
					*/
						Route::get('invoice/supplier/{id}', 'InvoiceController@getSupplier');
						Route::get('invoice/replace/{id}', 'InvoiceController@getReplace');
						Route::get('invoice/ri/{id}', 'InvoiceController@getRi');
						Route::get('invoice/drop/{id}/{supplier}', 'InvoiceController@getDrop');
						Route::get('invoice/print/{id}', 'InvoiceController@getPrint');
					Route::resource('invoice', 'InvoiceController');

					/*
						PRODUCT CONTROLLER
					*/
						Route::get('product/photocrop/{id}/{productphoto_id}', 'ProductController@getPhotocrop');
						Route::post('product/photocrop/{id}/{productphoto_id}', 'ProductController@postPhotocrop');
						Route::get('product/more-image/{id}', 'ProductController@getMoreImage');
						Route::get('product/addphoto/{id}', 'ProductController@getAddphoto');
						Route::post('product/addphoto/{id}', 'ProductController@postAddphoto');
						Route::get('product/detailphoto/{id}', 'ProductController@getDetailphoto');
						Route::get('product/photocrop2/{id}/{productphoto_id}', 'ProductController@getPhotocrop2');
						Route::post('product/photocrop2/{id}/{productphoto_id}', 'ProductController@postPhotocrop2');
						Route::get('product/setdefault/{id}', 'ProductController@getSetdefault');
						Route::post('product/deletephoto/{id}', 'ProductController@postDeletephoto');

						Route::get('product/substitution/{id}', 'ProductController@getSubstitution');
						Route::get('product/add-substitution/{id}', 'ProductController@getAddSubstitution');
						Route::post('product/add-substitution/{id}', 'ProductController@postAddSubstitution');
						Route::get('product/view-substitution/{id}', 'ProductController@getViewSubstitution');
						Route::get('product/edit-substitution/{id}', 'ProductController@getEditSubstitution');
						Route::post('product/edit-substitution/{id}', 'ProductController@postEditSubstitution');
						Route::post('product/delete-substitution/{id}', 'ProductController@postDeleteSubstitution');

						Route::get('product/rak/{id}', 'ProductController@getRak');
						Route::get('product/drop/{id}', 'ProductController@getDrop');
						Route::get('product/replace', 'ProductController@getReplace');
					
					Route::resource('product', 'ProductController');

					/*
						STOCK CARD
					*/
						Route::get('stock-card/print/{id}/{datestart}/{dateend}', 'StockcardController@getPrint');
					Route::resource('stock-card', 'StockcardController');

					/* 
						SETTING CONTROLLER 
					*/
						Route::get('setting/edit', 'SettingController@getEdit');
						Route::post('setting/edit', 'SettingController@postEdit');

					/*
						CUSTOMER CONTROLLER
					*/
						
					Route::resource('customer', 'CustomerController');


					/*
						TRANSACTION REPORT CONTROLLER
					*/
						Route::get('transaction/report', 'TransactionController@getReport');
						Route::post('transaction/report', 'TransactionController@postReport');
						Route::get('transaction/print/{datestart}/{dateend}', 'TransactionController@getPrint');

					/*
						TRANSACTION CONTROLLER
					*/
						Route::get('transaction/send/{id}', 'TransactionController@getSend');
						Route::get('transaction/abort/{id}', 'TransactionController@getAbort');
						Route::get('transaction/tprint/{id}', 'TransactionController@getTprint');
						Route::get('transaction/replace', 'TransactionController@getReplace');
						Route::get('transaction/add/{productid?}/{rakid?}/{qty?}/{price?}/{discounttype?}/{discount?}', 'TransactionController@getAdd');
						Route::get('transaction/form/{productid}/{rakid}/{qty}/{price}/{discounttype}/{discount}', 'TransactionController@getForm');
						Route::get('transaction/drop/{id}', 'TransactionController@getDrop');
					Route::resource('transaction', 'TransactionController');


					/*
						PAYMENT CONFIRMATION CONTROLLER
					*/
						Route::get('tpayment/print/{id}', 'PaymentconfirmationController@getPrint');
						Route::get('tpayment/transaction/{id}', 'PaymentconfirmationController@getTransaction');
					Route::resource('tpayment', 'PaymentconfirmationController');

					/*
						ACCOUNTS RECIEVABLE REPORT CONTROLLER
					*/
						Route::get('accounts-recievable/report', 'AccountsrecievableController@getReport');
						Route::post('accounts-recievable/report', 'AccountsrecievableController@postReport');
						Route::get('accounts-recievable/print/{customer}', 'AccountsrecievableController@getPrint');

					/*
						ACCOUNTS RECIEVABLE CONTROLLER
					*/
					Route::resource('accounts-recievable', 'AccountsrecievableController');

					/*
						TRANSACTION RETURN CONTROLLER
					*/
						Route::get('treturn/ri/{supplier}', 'TransactionreturnController@getRi');
						Route::get('treturn/product/{id}', 'TransactionreturnController@getProduct');
						Route::get('treturn/replace/{id}', 'TransactionreturnController@getReplace');
						Route::get('treturn/add/{id}/{productid?}/{qty?}/{price?}', 'TransactionreturnController@getAdd');
						Route::get('treturn/form/{ridetailid}/{productid}/{qty}/{price}', 'TransactionreturnController@getForm');
						// Route::get('treturn/addedit/{id}/{returndetail_id}', 'TransactionreturnController@getAddedit');
						Route::get('treturn/drop/{id}', 'TransactionreturnController@getDrop');

						Route::get('treturn/print/{id}', 'TransactionreturnController@getPrint');
					Route::resource('treturn', 'TransactionreturnController');

					/*
						ACCOUNTS CONTROLLER
					*/
					Route::resource('account', 'AccountController');

					/*
						OTHER EXPEND / REVENUE CONTROLLER
					*/
						Route::get('other-expense-revenue/report', 'AccountdetailController@getReport');
						Route::post('other-expense-revenue/report', 'AccountdetailController@postReport');
						Route::get('other-expense-revenue/print/{datestart}/{dateend}', 'AccountdetailController@getPrint');
					Route::resource('other-expense-revenue', 'AccountdetailController');

					/*
						INCOME STATEMENT CONTROLLER
					*/
						Route::get('income-statement/report', 'IncomeController@getReport');
						Route::post('income-statement/report', 'IncomeController@postReport');
						Route::get('income-statement/print/{datestart}/{dateend}', 'IncomeController@getPrint');
					Route::resource('income-statement', 'IncomeController');
				});
			});


		/*
			ROUTE FOR FRONT END
		*/

			Route::group(['namespace' => 'Front', 'guard'=>'web', 'middleware' => ['appisup', 'visitorcounter', 'pageload', 'visitorlastactivity']], function(){

				Config::set('app.locale', 'id');

				/*
					SMALL CART
				*/
					Route::get('small-cart/delete/{id}', 'SmallcartController@getDelete');
					Route::get('small-cart/buy/{id}/{qty}', 'SmallcartController@getBuy');
					Route::get('small-cart/empty', 'SmallcartController@getEmpty');
				Route::resource('small-cart', 'SmallcartController');

				/*
					SMALL CART
				*/
					Route::get('product/category/{id}/{name}', 'ProductController@getCategory');
					Route::get('product/detail/{id}/{name}', 'ProductController@getDetail');
					Route::get('product/search', 'ProductController@getSearch');
				// Route::resource('product', 'ProductController');

				/*
					CART
				*/

					Route::get('cart/edit/{id}/{qty}', 'CartController@getEdit');
				Route::resource('cart', 'CartController');

				/*
					LOGIN CONTROLLER
				*/
				Route::post('login', 'AuthController@postLogin');
			    Route::get('activation/{code}', 'AuthController@getActivation');
			    Route::get('logout', 'AuthController@getLogout')->name('logout');
				// Route::get('login', 'AuthController@getLogin')->name('login');
			    // Route::post('login', 'AuthController@postLogin')->name('login');

			    /*
					SIGN UP CONTROLLER
				*/

					Route::get('sign-up/ajax-city/{province_id}', 'SignupController@getAjaxCity');
				Route::resource('sign-up', 'SignupController');

				/*
					SIGN UP CONTROLLER
				*/

					Route::post('sign-in/login', 'SigninController@postLogin');
					Route::post('sign-in/signup', 'SigninController@postSignup');
					Route::post('sign-in/checkout', 'SigninController@postCheckout');
				Route::resource('sign-in', 'SigninController');

			    /*
					PAYMENT CONFIRMATION
				*/

					Route::get('payment-confirmation/pay/{code}', 'PaymentController@getPay');
				Route::resource('payment-confirmation', 'PaymentController');

				/*
					REGISTER NEWSLETTER
				*/

				Route::resource('register-newsletter', 'NewsletterController');
			    
			    /*
					ABOUT US
				*/

				Route::resource('about-us', 'AboutController');

				/*
					CONTACT US
				*/

				Route::resource('contact-us', 'ContactController');

				/*
					SHIPPING & POLICY
				*/

				Route::get('shipping-and-policies/{id}/{name}', 'CustomController@getPolicy');
				Route::get('faq/{id}/{name}', 'CustomController@getFaq');
				Route::get('how-to-buy', 'CustomController@getHow');

				/*
					NEWS
				*/

				    Route::get('news/search', 'NewsController@getSearch');
				    Route::get('news/detail/{id}/{name}', 'NewsController@getDetail');
				    Route::get('news/newsletter-unsubscribe/{id}', 'NewsController@getNewsletterUnsubscribe');
				Route::resource('news', 'NewsController');

				/*
					FORGOT PASSWORD CONTROLLER
				*/
				    Route::get('password/remind', 'ReminderController@getRemind');
				    Route::post('password/remind', 'ReminderController@postRemind');
				    Route::get('password/reset/{token?}', 'ReminderController@getReset');
				    Route::post('password/reset/{token?}', 'ReminderController@postReset');

			   	/*
					NOTIFICATION
				*/

				Route::resource('notification', 'NotificationController', ['only' => ['store']]);

				/*
					CHECKOUT
				*/

				    Route::get('checkout/step1', 'CheckoutController@getStep1');
				    Route::post('checkout/step1', 'CheckoutController@postStep1');
				    Route::get('checkout/checkemail/{product_id}', 'CheckoutController@getCheckemail');
				    Route::get('checkout/ajax-checkout/{voucher}/{layanan_id}', 'CheckoutController@getAjaxCheckout');
				    Route::get('checkout/ajax-city/{province_id}', 'CheckoutController@getAjaxCity');
				    Route::get('checkout/ajax-rate/{area_id}', 'CheckoutController@getAjaxRate');
				    Route::get('checkout/step2/{transaction_code}', 'CheckoutController@getstep2');
				    Route::get('checkout/step3/{transaction_code}', 'CheckoutController@getStep3');
				    Route::post('checkout/step3/{transaction_code}', 'CheckoutController@postStep3');
				Route::resource('checkout', 'CheckoutController');

				/*
						FORGOT PASSWORD CONTROLLER
					*/
				    Route::get('password/remind', 'ReminderController@getRemind');
				    Route::post('password/remind', 'ReminderController@postRemind');
				    Route::get('password/reset/{token?}', 'ReminderController@getReset');
				    Route::post('password/reset/{token?}', 'ReminderController@postReset');

				/*
					HOME
				*/

				Route::resource('/', 'HomeController');




				/*
					REGISTRATION CONTROLLER
				*/
				// Route::resource('register', 'RegistrationController', ['only' => ['index', 'store']]);

				Route::group(['middleware' => ['authfront', 'undonefront', 'sessiontimefront', 'frontlastactivity']], function(){

					/*
						MEMBER
					*/
					    Route::get('member/profile', 'MemberController@getProfile');
					    Route::post('member/profile', 'MemberController@postProfile');
					    Route::get('member/my-transaction', 'MemberController@getMyTransaction');
					    Route::get('member/my-transaction-detail/{code}', 'MemberController@getMyTransactionDetail');
					    Route::get('member/change-password', 'MemberController@getChangePassword');
					    Route::post('member/change-password', 'MemberController@postChangePassword');
					Route::resource('member', 'MemberController');
						
				});

				// Route::get('/{request?}', function (Request $request) {
					// $data['request'] = $request;

				    // return view('front.welcome', $data);
				// });

			});
	}
	else
	{
		return "Your Setting is empty";
	}
}
else
{
	return "The class Setting doesn't exist, Please migrate first";
}

// Auth::routes();

// Route::get('/home', 'HomeController@index');
