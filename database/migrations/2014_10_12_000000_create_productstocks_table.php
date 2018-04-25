<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductstocksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('productstocks', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('product_id');
			$table->integer('rak_id');
			$table->integer('stock');
			$table->boolean('is_active');
            
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
		Schema::dropIfExists('productstocks');
	}

}
