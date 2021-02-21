<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePricingoptionsTable extends Migration
{
    public function up()
    {
        Schema::create('pricingoptions', function (Blueprint $table) {
            $table->id();

            $table->string('name')->unique();
            $table->integer('priceincents')->unsigned();
            $table->bigInteger('purchasabletype_id')->unsigned();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pricingoptions');
    }
}
