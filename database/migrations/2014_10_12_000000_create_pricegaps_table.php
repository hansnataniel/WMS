<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePricegapsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pricegaps', function(Blueprint $table) {
			$table->increments('id');
			$table->date('date');
			$table->integer('invoicedetail_id')->default(0);
			$table->integer('returndetail_id')->default(0);
			$table->integer('adjustment_id')->default(0);
			$table->integer('price');
            
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
		Schema::dropIfExists('pricegaps');
	}

}
