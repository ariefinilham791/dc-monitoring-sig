<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('server_components')) {
            Schema::create('server_components', function (Blueprint $table) {
                $table->id();
                $table->foreignId('server_id')->constrained('servers')->cascadeOnDelete();
                $table->string('name', 100);
                $table->string('type', 50)->nullable();
                $table->string('status', 50)->nullable();
                $table->text('notes')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('server_components');
    }
};
