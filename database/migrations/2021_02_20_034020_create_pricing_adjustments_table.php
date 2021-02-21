<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePricingAdjustmentsTable extends Migration
{
    public function up()
    {
        Schema::create('pricing_adjustments', function (Blueprint $table) {
            $table->id();

            $table->tinyInteger('membership_type_id')->unsigned();
            $table->integer('venue_id')->unsigned();
            $table->tinyInteger('age_start')->unsigned();
            $table->tinyInteger('age_end')->unsigned();
            $table->enum('adjust_method', ['fixed', 'multiplier', 'offset']);
            $table->integer('adjust_value');
            $table->bigInteger('pricing_option_id')->unsigned();

            $table->timestamps();

            $table->foreign('membership_type_id')->references('id')->on('membershiptypes');
            $table->foreign('pricing_option_id')->references('id')->on('pricingoptions');
            $table->foreign('venue_id')->references('id')->on('venues');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pricing_adjustments');
    }
}
