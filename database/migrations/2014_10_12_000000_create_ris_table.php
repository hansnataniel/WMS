<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRisTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ris', function(Blueprint $table) {
			$table->increments('id');
			$table->string('no_nota');
			$table->integer('po_id');
			$table->integer('supplier_id');
			$table->date('date');
			$table->text('message');
			$table->boolean('is_invoice')->default(0);

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
		Schema::dropIfExists('ris');
	}

}
