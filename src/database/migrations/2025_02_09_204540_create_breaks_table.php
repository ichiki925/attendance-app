<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBreaksTable extends Migration
{

    public function up()
    {
        Schema::create('breaks', function (Blueprint $table) {
            $table->id(); // 主キー
            $table->unsignedBigInteger('attendance_id'); // 外部キー
            $table->time('break_start'); // 休憩開始時刻
            $table->time('break_end')->nullable(); // 休憩終了時刻（NULL許容）
            $table->time('break_time')->nullable(); // 休憩合計時間（NULL許容）
            $table->timestamps(); // 作成・更新日時

            // 外部キー制約
            $table->foreign('attendance_id')->references('id')->on('attendances')->onDelete('cascade');
        });
    }


    public function down()
    {
        Schema::dropIfExists('breaks');
    }
}
