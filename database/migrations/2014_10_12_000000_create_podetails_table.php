<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePodetailsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('podetails', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('po_id');
			$table->integer('product_id');
			$table->integer('qty');
			$table->integer('price');
			$table->boolean('discounttype');
			$table->integer('discount');
			$table->string('status');
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
		Schema::dropIfExists('podetails');
	}

}
