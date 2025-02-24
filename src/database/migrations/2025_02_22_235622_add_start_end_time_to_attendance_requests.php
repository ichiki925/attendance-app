<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStartEndTimeToAttendanceRequests extends Migration
{

    public function up()
    {
        Schema::table('attendance_requests', function (Blueprint $table) {
            $table->time('start_time')->nullable()->after('attendance_id');
            $table->time('end_time')->nullable()->after('start_time');
        });
    }


    public function down()
    {
        Schema::table('attendance_requests', function (Blueprint $table) {
            $table->dropColumn(['start_time', 'end_time']);
        });
    }
}
