<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('products', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('no_merk');
			$table->integer('reference_id');
			// $table->string('no_oem');
			$table->integer('rak_id');
			$table->integer('kendaraan_id');
			$table->string('name');
			$table->string('merk');
			$table->integer('price');
			$table->string('size');
			$table->text('description')->nullable();
			$table->integer('min_stock');
			$table->integer('max_stock');
			$table->integer('stock')->nullable();
			$table->boolean('is_active');

			$table->integer('create_id');
            $table->integer('update_at');
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
		Schema::dropIfExists('products');
	}

}
