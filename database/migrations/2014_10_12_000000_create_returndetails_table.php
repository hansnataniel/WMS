<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReturndetailsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('returndetails', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('return_id');
			$table->integer('ridetail_id');
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
		Schema::dropIfExists('returndetails');
	}

}
