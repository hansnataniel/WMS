<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubstitutionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('substitutions', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('product_id');
			$table->integer('substitution_id');            
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
		Schema::dropIfExists('substitutions');
	}

}
