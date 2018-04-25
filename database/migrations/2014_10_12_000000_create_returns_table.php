<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReturnsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('returns', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('ri_id');
			$table->integer('supplier_id');
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
		Schema::dropIfExists('returns');
	}

}
