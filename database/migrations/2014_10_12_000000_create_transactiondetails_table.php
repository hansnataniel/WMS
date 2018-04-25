<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactiondetailsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('transactiondetails', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('transaction_id')->unsigned();
			$table->integer('product_id')->unsigned();
			$table->integer('rak_id')->unsigned();
			$table->integer('qty');
			$table->integer('price');
			$table->boolean('discounttype');
			$table->integer('discount');
            
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
		Schema::dropIfExists('transactiondetails');
	}

}
