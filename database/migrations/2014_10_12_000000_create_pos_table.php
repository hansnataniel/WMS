<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pos', function(Blueprint $table) {
			$table->increments('id');
			$table->string('no_nota');
			$table->integer('supplier_id');
			$table->boolean('discounttype');
			$table->integer('discount');
			$table->date('date');
			$table->text('message');
			$table->string('status');
			$table->string('ri_status');

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
		Schema::dropIfExists('pos');
	}

}
