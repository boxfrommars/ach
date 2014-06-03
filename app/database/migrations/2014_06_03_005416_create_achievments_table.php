<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAchievmentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('achievments', function(Blueprint $table)
        {
            $table->increments('id');

            $table->integer('depth'); // глубина
            $table->integer('outlook'); // кругозор
            $table->integer('interaction'); // взаимодействие

            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image')->nullable();

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
        Schema::table('achievments', function(Blueprint $table)
        {
            $table->drop();
        });
	}

}
