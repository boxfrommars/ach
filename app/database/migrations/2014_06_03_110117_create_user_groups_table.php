<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserGroupsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('user_groups', function(Blueprint $table)
        {
            $table->integer('id_user')->unsigned();
            $table->integer('id_group')->unsigned();

            $table->foreign('id_user')->references('id')->on('users');
            $table->foreign('id_group')->references('id')->on('groups');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('user_groups', function(Blueprint $table)
        {
            $table->drop();
        });
	}

}
