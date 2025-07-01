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
        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            $table->string('dcu_code')->nullable();
            $table->string('rtu_code')->nullable();
            $table->tinyInteger('alert_type')->nullable();
            $table->json('alert_details')->nullable();
            $table->timestamp('device_time')->nullable();
            $table->timestamps();

            $table->index(['dcu_code', 'rtu_code', 'alert_type', 'device_time'], 'alerts_composite_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alerts');
    }
};
