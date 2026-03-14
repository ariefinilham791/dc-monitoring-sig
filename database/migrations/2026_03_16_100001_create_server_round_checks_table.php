<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('server_round_checks')) {
            Schema::create('server_round_checks', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('server_id')->index();
                $table->unsignedBigInteger('checklist_round_id')->index();
                $table->string('status', 20)->default('pending');
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();
            });
            Schema::table('server_round_checks', function (Blueprint $table) {
                $table->unique(['server_id', 'checklist_round_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('server_round_checks');
    }
};
