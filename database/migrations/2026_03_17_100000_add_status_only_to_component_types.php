<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('component_types')) {
            return;
        }
        Schema::table('component_types', function (Blueprint $table) {
            if (! Schema::hasColumn('component_types', 'status_only')) {
                $table->boolean('status_only')->default(false)->after('attributes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('component_types', function (Blueprint $table) {
            if (Schema::hasColumn('component_types', 'status_only')) {
                $table->dropColumn('status_only');
            }
        });
    }
};
