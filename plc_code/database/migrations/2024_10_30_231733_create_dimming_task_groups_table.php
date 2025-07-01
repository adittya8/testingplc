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
        Schema::create('dimming_task_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dimming_task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sub_group_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('rtu_id')->nullable()->constrained('remote_terminals')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dimming_task_groups');
    }
};
