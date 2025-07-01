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
        Schema::create('dimming_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('road_id')->constrained();
            $table->tinyInteger('dimming_type');
            $table->time('start_time');
            $table->time('end_time')->nullable();
            $table->tinyInteger('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dimming_schedules');
    }
};
