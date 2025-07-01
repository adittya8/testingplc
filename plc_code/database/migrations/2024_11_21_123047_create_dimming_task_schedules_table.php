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
        Schema::create('dimming_task_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dimming_task_id')->constrained()->cascadeOnDelete();
            $table->time('time');
            $table->integer('brightness');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dimming_task_schedules');
    }
};
