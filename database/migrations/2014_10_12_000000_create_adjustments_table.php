<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdjustmentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('adjustments', function(Blueprint $table) {
			$table->increments('id');
			$table->string('no_nota');
			$table->integer('product_id')->unsigned();
			$table->integer('rak_id')->unsigned();
			$table->integer('quantity');
			$table->date('date');
			$table->string('status');
			$table->string('note');

			$table->integer('create_id');
            
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
		Schema::dropIfExists('adjustments');
	}

}
