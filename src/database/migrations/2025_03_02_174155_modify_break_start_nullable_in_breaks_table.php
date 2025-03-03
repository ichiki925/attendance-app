<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyBreakStartNullableInBreaksTable extends Migration
{

    public function up()
    {
        Schema::table('breaks', function (Blueprint $table) {
            $table->time('break_start')->nullable()->change();
        });
    }


    public function down()
    {
        Schema::table('breaks', function (Blueprint $table) {
            $table->time('break_start')->nullable(false)->change(); // 変更を元に戻す（`NOT NULL` にする）
        });
    }
}
