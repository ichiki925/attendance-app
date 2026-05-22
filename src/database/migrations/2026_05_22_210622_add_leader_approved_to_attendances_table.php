<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLeaderApprovedToAttendancesTable extends Migration
{
    public function up()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->boolean('leader_approved')->default(false)->after('attendance_type_id');
            $table->timestamp('leader_approved_at')->nullable()->after('leader_approved');
        });
    }

    public function down()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn('leader_approved');
            $table->dropColumn('leader_approved_at');
        });
    }
}