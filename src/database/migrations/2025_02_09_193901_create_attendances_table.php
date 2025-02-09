<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendancesTable extends Migration
{

    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id(); // 主キー
            $table->unsignedBigInteger('user_id'); // 外部キー
            $table->date('date'); // 日付
            $table->time('start_time'); // 開始時刻
            $table->time('end_time')->nullable(); // 終了時刻（NULL許容）
            $table->time('total_time')->nullable(); // 合計時間（NULL許容）
            $table->enum('status', ['off_duty', 'working', 'on_break', 'completed']); // ステータス
            $table->text('remarks')->nullable(); // 備考（NULL許容）
            $table->timestamps(); // 作成・更新日時

            // 外部キー制約
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }


    public function down()
    {
        Schema::dropIfExists('attendances');
    }
}
