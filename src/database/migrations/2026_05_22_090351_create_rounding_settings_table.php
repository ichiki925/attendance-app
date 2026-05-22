<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoundingSettingsTable extends Migration
{
    public function up()
    {
        Schema::create('rounding_settings', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('round_minutes')->comment('丸め単位（分）');
            $table->enum('round_type', ['floor', 'ceil', 'round'])->comment('切り捨て・切り上げ・四捨五入');
            $table->boolean('is_active')->default(false)->comment('現在適用中');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rounding_settings');
    }
}