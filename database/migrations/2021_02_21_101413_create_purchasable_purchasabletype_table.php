<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasablePurchasabletypeTable extends Migration
{
    public function up()
    {
        Schema::create('purchasable_purchasabletype', function (Blueprint $table) {
            $table->id();

            $table->integer('purchasable_id')->unsigned();
            $table->bigInteger('purchasabletype_id')->unsigned();

            $table->timestamps();

            $table->unique(['purchasable_id', 'purchasabletype_id'], '_purchasabletype_unique');

            $table->foreign('purchasable_id')->references('id')->on('purchasables');
            $table->foreign('purchasabletype_id')->references('id')->on('purchasabletypes');
        });
    }

    public function down()
    {
        Schema::dropIfExists('purchasable_purchasabletype');
    }
}
