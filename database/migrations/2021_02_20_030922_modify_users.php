<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyUsers extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->date('dob')->after('password');
            $table->tinyInteger('membership_type_id')->after('dob')->unsigned();

            $table->foreign('membership_type_id')->references('id')->on('membershiptypes');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('users_membership_type_id_foreign');

            $table->dropColumn([
                'dob',
                'membership_type_id',
            ]);
        });
    }
}
