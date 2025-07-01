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
        Schema::create('dimming_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('dimming_task_category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('project_id')->constrained();
            $table->datetime('date_from');
            $table->datetime('date_to');
            $table->boolean('is_active')->default(1);
            $table->timestamp('last_command_sent_at')->nullable();
            $table->tinyInteger('type')->default(1)->comment('1 => subgroup, 2 => rtu');
            $table->timestamps();

            $table->index(['date_from', 'date_to', 'is_active'], 'dimming_tasks_composite_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dimming_tasks');
    }
};
