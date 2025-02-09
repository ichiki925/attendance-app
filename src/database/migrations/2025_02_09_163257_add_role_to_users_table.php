<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRoleToUsersTable extends Migration
{

    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('user')->after('password'); // デフォルトで「一般ユーザー」に設定
        });
    }


    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
