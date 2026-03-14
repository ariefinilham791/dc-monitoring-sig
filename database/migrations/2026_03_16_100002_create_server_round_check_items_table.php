<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('server_round_check_items')) {
            Schema::create('server_round_check_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('server_round_check_id')->index();
                $table->unsignedBigInteger('server_component_id')->index();
                $table->string('result', 20)->default('pending');
                $table->decimal('used_pct', 5, 2)->nullable();
                $table->decimal('free_pct', 5, 2)->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
            Schema::table('server_round_check_items', function (Blueprint $table) {
                $table->unique(['server_round_check_id', 'server_component_id'], 'srci_round_component_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('server_round_check_items');
    }
};
