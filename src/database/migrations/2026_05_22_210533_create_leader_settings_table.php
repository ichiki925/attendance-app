<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeaderSettingsTable extends Migration
{
    public function up()
    {
        Schema::create('leader_settings', function (Blueprint $table) {
            $table->id();
            $table->string('secret_code');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('leader_settings');
    }
}