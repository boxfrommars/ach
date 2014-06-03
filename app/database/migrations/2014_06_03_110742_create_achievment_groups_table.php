<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAchievmentGroupsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('achievment_groups', function(Blueprint $table)
        {
            $table->integer('id_achievment')->unsigned();
            $table->integer('id_group')->unsigned();

            $table->foreign('id_achievment')->references('id')->on('achievments');
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
        Schema::table('achievment_groups', function(Blueprint $table)
        {
            $table->drop();
        });
	}

}
