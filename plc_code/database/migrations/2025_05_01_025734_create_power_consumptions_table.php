<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('power_consumptions', function (Blueprint $table) {
            $table->id();
            $table->string('dcu_code')->nullable();
            $table->string('rtu_code')->nullable();
            $table->decimal('power', 10, 2);
            $table->timestamp('device_time')->nullable();
            $table->timestamps();

            $table->index(['dcu_code', 'rtu_code', 'device_time'], 'power_consumptions_composite_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('power_consumptions');
    }
};
