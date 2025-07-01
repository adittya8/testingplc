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
        Schema::create('poles', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->foreignId('road_id')->constrained();
            $table->foreignId('concentrator_id')->constrained();
            $table->string('pole_type_id')->constrained();
            $table->foreignId('project_id')->constrained();
            $table->string('lat')->nullable();
            $table->string('lon')->nullable();
            $table->string('location')->nullable();
            $table->integer('serial');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('poles');
    }
};
