<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('report_data', function (Blueprint $table) {
            $table->id();
            $table->string('rtu_code')->nullable();
            $table->string('dcu_code')->nullable();
            $table->integer('voltage')->nullable();
            $table->integer('main_light_current')->nullable();
            $table->integer('main_light_power')->nullable();
            $table->integer('temperature')->nullable();
            $table->integer('main_light_brightness')->nullable();
            $table->integer('main_light_color_temp')->nullable();
            $table->integer('running_time')->nullable();
            $table->integer('running_mode')->nullable();
            $table->integer('total_power_consumption')->nullable();
            $table->timestamp('device_time')->nullable();
            $table->text('raw_data')->nullable();
            $table->timestamps();

            $table->index(['rtu_code', 'dcu_code', 'device_time'], 'report_data_composite_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_data');
    }
};
