<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBreaksTable extends Migration
{

    public function up()
    {
        Schema::create('breaks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attendance_id');
            $table->time('break_start')->nullable();
            $table->time('break_end')->nullable();
            $table->time('break_time')->nullable();
            $table->timestamps();


            $table->foreign('attendance_id')->references('id')->on('attendances')->onDelete('cascade');
        });
    }


    public function down()
    {
        Schema::dropIfExists('breaks');
    }
}
