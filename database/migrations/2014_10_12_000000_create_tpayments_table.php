<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTpaymentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tpayments', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('no_nota');
			$table->string('date');
			$table->integer('bank_id')->unsigned();
			$table->integer('transaction_id')->unsigned();

            // $table->datetime('confirm_at');
            // $table->integer('confirm_id');

            // $table->datetime('decline_at');
            // $table->integer('decline_id');

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
		Schema::dropIfExists('tpayments');
	}

}
