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
        Schema::create('lamp_data', function (Blueprint $table) {
            $table->id();
            $table->decimal('brightness')->nullable();
            $table->decimal('voltage')->nullable()->comment('in volts');
            $table->decimal('current')->nullable()->comment('in mili amperes');
            $table->decimal('power')->nullable()->comment('in watts');
            $table->decimal('work_time')->nullable()->comment('in hours');
            $table->decimal('power_cunsumption')->nullable()->comment('in watts');
            $table->decimal('pf')->nullable();
            $table->foreignId('luminary_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lamp_data');
    }
};
