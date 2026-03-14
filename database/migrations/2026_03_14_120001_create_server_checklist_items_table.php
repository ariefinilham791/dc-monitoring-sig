<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('server_checklist_items')) {
            Schema::create('server_checklist_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('server_id')->index();
                $table->string('title', 255);
                $table->boolean('is_checked')->default(false);
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('server_checklist_items');
    }
};
