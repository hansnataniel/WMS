<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGudangsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('gudangs', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('code_id');
			$table->string('name');
			$table->boolean('is_active');

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
		Schema::dropIfExists('gudangs');
	}

}
