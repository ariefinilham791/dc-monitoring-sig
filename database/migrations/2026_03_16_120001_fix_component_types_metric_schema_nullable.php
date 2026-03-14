<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('component_types')) {
            return;
        }

        if (Schema::hasColumn('component_types', 'metric_schema')) {
            DB::statement('ALTER TABLE component_types MODIFY metric_schema TEXT NULL DEFAULT NULL');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('component_types') && Schema::hasColumn('component_types', 'metric_schema')) {
            DB::statement('ALTER TABLE component_types MODIFY metric_schema TEXT NOT NULL');
        }
    }
};
