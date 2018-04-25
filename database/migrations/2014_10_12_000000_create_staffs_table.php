<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStaffsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('staffs', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('no_id')->unsigned();
			$table->string('name');
			$table->text('address')->nullable();
			$table->text('ktp')->nullable();
			$table->string('phone')->nullable();
			$table->string('password');
			$table->string('new_password')->nullable();
			$table->string('remember_token')->nullable();

			$table->boolean('is_active');

            $table->integer('create_id');
            $table->integer('update_id');
            
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
		Schema::dropIfExists('staffs');
	}

}
