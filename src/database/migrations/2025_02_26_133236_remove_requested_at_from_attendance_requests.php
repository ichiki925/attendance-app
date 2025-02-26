<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveRequestedAtFromAttendanceRequests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendance_requests', function (Blueprint $table) {
            $table->dropColumn('requested_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attendance_requests', function (Blueprint $table) {
            $table->timestamp('requested_at')->nullable();
        });
    }
}
