<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePropertyBagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('property_bag', function (Blueprint $table) {
            $table->increments('id');
            $table->string('resource_type')->index();
            $table->integer('resource_id')->unsigned()->index();
            $table->string('key')->index();
            $table->text('value');
            $table->timestamps();

            $table->unique(['resource_type', 'resource_id', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('property_bag');
    }
}
