<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserAchievmentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('user_achievments', function(Blueprint $table)
        {
            $table->integer('id_user')->unsigned();
            $table->integer('id_achievment')->unsigned();
            $table->boolean('is_approved')->default(false);

            $table->foreign('id_user')->references('id')->on('users');
            $table->foreign('id_achievment')->references('id')->on('achievments');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('user_achievments', function(Blueprint $table)
        {
            $table->drop();
        });
	}
}
