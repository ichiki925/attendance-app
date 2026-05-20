<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOvertimeToAttendancesTable extends Migration
{
    public function up()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->unsignedInteger('work_minutes')->nullable()->after('total_time');
            $table->unsignedInteger('overtime_minutes')->nullable()->after('work_minutes');
            $table->unsignedInteger('late_night_minutes')->nullable()->after('overtime_minutes');
        });
    }

    public function down()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['work_minutes', 'overtime_minutes', 'late_night_minutes']);
        });
    }
}