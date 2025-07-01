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
        Schema::create('luminaries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('node_id')->nullable();
            $table->foreignId('lamp_type_id')->nullable();
            $table->foreignId('concentrator_id')->nullable()->constrained();
            $table->foreignId('rtu_id')->nullable()->constrained('remote_terminals', 'id');
            $table->foreignId('sub_group_id')->nullable()->constrained();
            $table->foreignId('luminary_type_id')->nullable()->constrained();
            $table->foreignId('control_gear_type_id')->nullable()->constrained();
            $table->foreignId('pole_id')->nullable()->constrained();
            $table->tinyInteger('installation_status');
            $table->integer('rated_power')->nullable();
            $table->tinyInteger('last_status')->nullable();
            $table->timestamp('status_updated_at')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('luminaries');
    }
};
