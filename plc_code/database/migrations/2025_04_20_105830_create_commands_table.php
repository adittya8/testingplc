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
        Schema::create('commands', function (Blueprint $table) {
            $table->id();
            $table->longText('command');
            $table->foreignId('concentrator_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('rtu_id')->nullable()->constrained('remote_terminals', 'id')->nullOnDelete();
            $table->tinyInteger('command_type')->nullable();
            $table->morphs('commandable');
            $table->boolean('is_sent')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commands');
    }
};
