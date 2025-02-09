<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendanceRequestsTable extends Migration
{

    public function up()
    {
        Schema::create('attendance_requests', function (Blueprint $table) {
            $table->id(); // 主キー
            $table->unsignedBigInteger('attendance_id'); // 出勤データの外部キー
            $table->unsignedBigInteger('user_id'); // ユーザーの外部キー
            $table->text('reason'); // 修正理由
            $table->enum('request_status', ['pending', 'approved']); // リクエストの状態
            $table->timestamp('requested_at'); // リクエスト日時
            $table->timestamps(); // 作成・更新日時

            // 外部キー制約
            $table->foreign('attendance_id')->references('id')->on('attendances')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }


    public function down()
    {
        Schema::dropIfExists('attendance_requests');
    }
}
