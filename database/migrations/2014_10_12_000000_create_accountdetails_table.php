<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountdetailsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('accountdetails', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('account_id');
			$table->date('date');
			$table->integer('amount');

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
		Schema::dropIfExists('accountdetails');
	}

}
