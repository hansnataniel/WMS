<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKendaraansTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('kendaraans', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('brand');
			$table->string('type');
			$table->string('th_start');
			$table->string('th_end');
			$table->string('transmition');
			$table->string('cc');
			$table->string('code');
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
		Schema::dropIfExists('kendaraans');
	}

}
