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
        Schema::create('device_data', function (Blueprint $table) {
            $table->id();
            $table->string('device_code', 50);
            $table->text('data');
            $table->string('topic', 100);
            $table->timestamps();

            $table->index(['device_code', 'topic'], 'device_data_index_composite');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_data');
    }
};
