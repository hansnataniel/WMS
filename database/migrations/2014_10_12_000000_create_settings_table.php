<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('settings', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('back_session_lifetime');
            $table->integer('front_session_lifetime');
            $table->integer('visitor_lifetime');
			$table->string('admin_url');

			$table->text('google_analytics')->nullable();
			$table->boolean('maintenance');
			
			$table->string('name')->nullable();
			$table->string('address')->nullable();
			$table->string('phone')->nullable();
			$table->string('fax')->nullable();
			$table->string('bbm')->nullable();
			$table->string('line')->nullable();
			$table->string('whatsapp')->nullable();

			$table->string('facebook')->nullable();
			$table->string('twitter')->nullable();
			$table->string('instagram')->nullable();
			$table->string('yahoo_messenger')->nullable();

			$table->string('contact_email')->nullable();

            $table->string('receiver_email')->nullable();
            $table->string('receiver_email_name')->nullable();
            
            $table->string('sender_email')->nullable();
            $table->string('sender_email_name')->nullable();

			$table->integer('weight_tolerance')->nullable();
			$table->integer('free_delivery')->nullable();
			$table->boolean('is_free')->nullable();
			
			$table->text('about_us')->nullable();
			$table->text('about_us_meta_desc')->nullable();

			$table->text('how_to_buy')->nullable();
			$table->text('how_to_buy_meta_desc')->nullable();

			$table->string('coor');

            $table->integer('settingupdate_id');
            $table->integer('aboutupdate_id');
            $table->integer('howupdate_id');

			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('settings');
	}

}
