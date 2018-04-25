<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTreturnsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('treturns', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('transaction_id');
			$table->integer('customer_id');
			$table->date('date');
			$table->string('no_nota');
			$table->text('message');

			$table->integer('create_id');
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
		Schema::dropIfExists('treturns');
	}

}
