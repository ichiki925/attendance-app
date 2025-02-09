<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStaffsTable extends Migration
{

    public function up()
    {
        Schema::create('staffs', function (Blueprint $table) {
            $table->id(); // 主キー
            $table->unsignedBigInteger('user_id'); // ユーザーID（外部キー）
            $table->string('staff_code', 50)->unique(); // スタッフコード（ユニークキー）
            $table->timestamps(); // 作成・更新日時

            // 外部キー制約
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }


    public function down()
    {
        Schema::dropIfExists('staffs');
    }
}
