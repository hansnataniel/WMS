<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHistoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('histories', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('history_id');
			$table->integer('product_id')->unsigned();
			$table->integer('amount');
			$table->integer('last_stock');
			$table->integer('final_stock');
			$table->string('status');
			$table->string('note');
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
		Schema::dropIfExists('histories');
	}

}
