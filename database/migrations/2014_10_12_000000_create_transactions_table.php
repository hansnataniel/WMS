<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('transactions', function(Blueprint $table) {
			$table->increments('id');
			$table->string('trans_id');
			$table->integer('customer_id')->unsigned();

			// $table->string('name');
			// $table->string('address');
			// $table->string('phone');
			// $table->string('email');

			$table->date('date');

			$table->integer('total');
			$table->boolean('discounttype');
			$table->integer('discount');
			$table->integer('amount_to_pay');

			$table->text('message');			
			
			$table->string('status');

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
		Schema::dropIfExists('transactions');
	}

}
