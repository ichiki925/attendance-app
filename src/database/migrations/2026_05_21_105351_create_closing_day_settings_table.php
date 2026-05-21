<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClosingDaySettingsTable extends Migration
{
    public function up()
    {
        Schema::create('closing_day_settings', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('closing_day')->comment('締め日（1〜30=その日付、31=末日）');
            $table->string('label', 50)->comment('表示名（例：20日締め）');
            $table->boolean('is_active')->default(false)->comment('現在適用中');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('closing_day_settings');
    }
}