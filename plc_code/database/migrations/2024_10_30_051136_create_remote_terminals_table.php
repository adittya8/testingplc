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
        Schema::create('remote_terminals', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code');
            $table->foreignId('concentrator_id')->nullable()->constrained();
            $table->foreignId('project_id')->constrained();
            $table->foreignId('pole_id')->nullable()->constrained();
            $table->foreignId('sub_group_id')->nullable()->constrained();
            $table->string('lat')->nullable();
            $table->string('lon')->nullable();
            $table->string('location')->nullable();
            $table->text('remarks')->nullable();
            $table->integer('rated_power')->nullable();
            $table->timestamp('status_updated_at')->nullable();
            $table->integer('last_command_brightness')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('remote_terminals');
    }
};
