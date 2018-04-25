<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('area_id')->unsigned();
			$table->string('name');
			$table->string('email');
			$table->string('phone')->nullable();
			$table->text('address')->nullable();
			$table->text('zip_code')->nullable();
			$table->date('birthdate')->nullable();
			$table->string('password');
			$table->string('new_password')->nullable();
			$table->string('remember_token')->nullable();
			
			$table->boolean('is_banned');

			$table->boolean('is_active');

            $table->integer('create_id');
            $table->integer('update_id');

            $table->integer('banned_id');
            $table->integer('unbanned_id');
            $table->datetime('banned');
            $table->datetime('unbanned');

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
		Schema::dropIfExists('users');
	}

}
