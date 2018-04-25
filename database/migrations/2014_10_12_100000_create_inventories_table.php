<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInventoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('inventories', function(Blueprint $table) {
			$table->increments('id');
			$table->date('date');
			$table->integer('productstock_id');
			$table->string('type');
			$table->integer('type_id');

			$table->double('real_price');

			$table->integer('qty_last');
			$table->double('price_last');
			$table->integer('qty_in');
			$table->double('price_in');
			$table->integer('qty_out');
			$table->double('price_out');
			$table->integer('qty_z');
			$table->double('price_z');
            
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
		Schema::dropIfExists('inventories');
	}

}
