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
        Schema::create('concentrators', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('project_id')->constrained();
            $table->foreignId('road_id')->nullable()->constrained()->nullOnDelete();
            $table->string('concentrator_no');
            $table->string('sim_no')->nullable();
            $table->string('lat')->nullable();
            $table->string('lon')->nullable();
            $table->string('location')->nullable();
            $table->tinyInteger('last_status')->nullable();
            $table->timestamp('status_updated_at')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->foreignId('synced_by')->nullable()->constrained('users', 'id')->nullOnDelete();
            $table->foreignId('schedule_preset_id')->nullable()->constrained('schedule_presets', 'id')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('concentrators');
    }
};
