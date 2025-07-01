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
        Schema::create('control_gears', function (Blueprint $table) {
            $table->id();
            $table->string('model');
            $table->foreignId('brand_id')->constrained();
            $table->foreignId('light_source_type_id')->constrained();
            $table->foreignId('control_gear_type_id')->constrained();
            $table->tinyInteger('dimming_type');
            $table->tinyInteger('dimming_attribute');
            $table->decimal('main_road_standard_voltage')->nullable();
            $table->decimal('main_road_standard_current')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('control_gears');
    }
};
