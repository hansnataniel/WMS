<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

use App\Models\Setting;
use App\Models\Admin;
use App\Models\Admingroup;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(SettingsTableSeeder::class);
        $this->call(AdminsTableSeeder::class);
        $this->call(AdmingroupsTableSeeder::class);
    }
}

/* 
	Setting Table Seeder
*/

class SettingsTableSeeder extends Seeder 
{
	public function run()
	{
		DB::table('settings')->truncate();

		Setting::create(
			[
				'back_session_lifetime' 			=> '60',
				'front_session_lifetime' 			=> '60',
				'visitor_lifetime' 			=> '5',
				'admin_url'					=> Crypt::encrypt('manage'),

				'google_analytics'			=> '',
				'maintenance'				=> false,

				'name'						=> 'Remax Indonesia',
				'address'					=> '',
				'phone'						=> '',
				'fax'						=> '',
				'bbm'						=> '',
				'whatsapp'					=> '',

				'facebook'					=> '',
				'twitter'					=> '',
				'instagram'					=> '',
				'yahoo_messenger'			=> '',

				'contact_email'				=> 'info@iremax.com',
				'receiver_email'			=> 'info@iremax.com',
				'receiver_email_name'			=> 'Remax Indonesia',
				'sender_email'				=> 'noreply@iremax.com',
				'sender_email_name'				=> 'Remax Indonesia',

				'weight_tolerance'			=> '',
				'free_delivery'			=> '',
				'is_free'			=> '',

				'about_us'						=> 'example About Us',
				'about_us_meta_desc'			=> 'example About Us Meta Description',

				'how_to_buy'					=> 'example How to buy',
				'how_to_buy_meta_desc'			=> 'example How to buy Meta Description',

				'coor'						=> '(-7.243337215057972, 112.73930132389069)',
				
				'settingupdate_id'			=> '1',
				'aboutupdate_id'			=> '1',
				'howupdate_id'				=> '1',
				
				'created_at'				=> date('Y-m-d H:i:s'),
				'updated_at'				=> date('Y-m-d H:i:s'),
			]
		);
	}
}


/* 
	Admin Table Seeder
*/

class AdminsTableSeeder extends Seeder 
{
	public function run()
	{
		DB::table('admins')->truncate();

		Admin::create(
			[
				'admingroup_id'			=> 1,
				'name'		 			=> 'CREIDS Cpanel',
				'email'			 		=> 'admin@creids.net',
				'password'		 		=> Hash::make('creidsadmin'),
				'remember_token' 		=> '',
				'activation_token' 		=> '',
				'is_admin'				=> true,
				'is_banned'				=> false,
				'is_active'				=> true,

				'create_id'				=> '1',
				'update_id'				=> '1',

				'banned_id'				=> '0',
				'unbanned_id'			=> '0',

				'banned'				=> date('Y-m-d H:i:s'),
				'unbanned'				=> date('Y-m-d H:i:s'),
				
				'created_at'			=> date('Y-m-d H:i:s'),
				'updated_at'			=> date('Y-m-d H:i:s'),
			]
		);
	}
}


/* 
	Admingroup Table Seeder
*/
	
class AdmingroupsTableSeeder extends Seeder 
{
	public function run()
	{
		DB::table('admingroups')->truncate();

		Admingroup::create(
			[
				'name'				=> 'Admin',

				'admingroup_c'		=> true,
				'admingroup_r'		=> true,
				'admingroup_u'		=> true,
				'admingroup_d'		=> true,

				'admin_c'			=> true,
				'admin_r'			=> true,
				'admin_u'			=> true,
				'admin_d'			=> true,

				'gudang_c'			=> true,
				'gudang_r'			=> true,
				'gudang_u'			=> true,
				'gudang_d'			=> true,

				'rak_c'			=> true,
				'rak_r'			=> true,
				'rak_u'			=> true,
				'rak_d'			=> true,

				'setting_u'			=> true,

				'kendaraan_c'			=> true,
				'kendaraan_r'			=> true,
				'kendaraan_u'			=> true,
				'kendaraan_d'			=> true,

				'supplier_c'			=> true,
				'supplier_r'			=> true,
				'supplier_u'			=> true,
				'supplier_d'			=> true,

				'po_c'			=> true,
				'po_r'			=> true,
				'po_u'			=> true,
				'po_d'			=> true,

				'ri_c'			=> true,
				'ri_r'			=> true,
				'ri_u'			=> true,
				'ri_d'			=> true,

				'hbt_r'			=> true,

				'invoice_c'			=> true,
				'invoice_r'			=> true,
				'invoice_u'			=> true,
				'invoice_d'			=> true,

				'return_c'			=> true,
				'return_r'			=> true,
				'return_u'			=> true,
				'return_d'			=> true,
				

				'product_c'			=> true,
				'product_r'			=> true,
				'product_u'			=> true,

				'productphoto_c'			=> true,
				'productphoto_r'			=> true,
				'productphoto_d'			=> true,

				'inventory_r'			=> true,
				'inventory_c'			=> true,

				'payment_c'			=> true,
				'payment_r'			=> true,
				'payment_u'			=> true,
				'payment_d'			=> true,

				'bank_c'			=> true,
				'bank_r'			=> true,
				'bank_u'			=> true,

				'adjustment_c'			=> true,
				'adjustment_r'			=> true,
				'adjustment_u'			=> true,
				'adjustment_d'			=> true,

				'stockcard_r'			=> true,

				'customer_c'			=> true,
				'customer_r'			=> true,
				'customer_u'			=> true,
				'customer_d'			=> true,

				'transaction_c'			=> true,
				'transaction_r'			=> true,
				'transaction_u'			=> true,
				'transaction_d'			=> true,

				'tpayment_c'			=> true,
				'tpayment_r'			=> true,
				'tpayment_u'			=> true,
				'tpayment_d'			=> true,

				'treturn_c'			=> true,
				'treturn_r'			=> true,
				'treturn_u'			=> true,
				'treturn_d'			=> true,

				'accountsrecievable_r'			=> true,

				'account_c'			=> true,
				'account_r'			=> true,
				'account_u'			=> true,
				'account_d'			=> true,

				'accountdetail_c'			=> true,
				'accountdetail_r'			=> true,
				'accountdetail_u'			=> true,
				'accountdetail_d'			=> true,
				
				'income_r'			=> true,

				'is_active'			=> true,

				'create_id'			=> '1',
				'update_id'			=> '1',
				
				'created_at'		=> date('Y-m-d H:i:s'),
				'updated_at'		=> date('Y-m-d H:i:s'),
			]
		);
	}
}