<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAttendanceTypeIdToAttendancesTable extends Migration
{
    public function up()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->foreignId('attendance_type_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained('attendance_types')
                    ->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['attendance_type_id']);
            $table->dropColumn('attendance_type_id');
        });
    }
}