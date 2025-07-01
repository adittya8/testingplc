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
        Schema::create('luminary_types', function (Blueprint $table) {
            $table->id();
            $table->string('model');
            $table->foreignId('brand_id')->constrained();
            $table->foreignId('light_source_type_id')->constrained();
            $table->string('rated_power')->nullable();
            $table->string('avg_life')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('luminary_types');
    }
};
