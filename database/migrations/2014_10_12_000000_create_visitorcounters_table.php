<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVisitorcountersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visitorcounters', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('date');
            $table->integer('month');
            $table->integer('year');
            $table->integer('count');
            $table->integer('pageload');
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
        Schema::dropIfExists('visitorcounters');
    }
}
