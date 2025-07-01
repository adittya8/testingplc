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
        Schema::create('daily_running_times', function (Blueprint $table) {
            $table->id();
            $table->string('rtu_code')->nullable();
            $table->string('dcu_code')->nullable();
            $table->date('date');
            $table->float('running_time')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_running_times');
    }
};
