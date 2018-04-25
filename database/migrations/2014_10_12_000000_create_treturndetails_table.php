<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTreturndetailsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('treturndetails', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('treturn_id');
			$table->integer('transactiondetail_id');
			$table->integer('qty');
			$table->integer('price')->default(0);
            
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
		Schema::dropIfExists('treturndetails');
	}

}
