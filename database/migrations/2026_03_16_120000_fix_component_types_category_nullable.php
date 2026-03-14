<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('component_types') && Schema::hasColumn('component_types', 'category')) {
            DB::statement('ALTER TABLE component_types MODIFY category VARCHAR(100) NULL DEFAULT NULL');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('component_types') && Schema::hasColumn('component_types', 'category')) {
            DB::statement('ALTER TABLE component_types MODIFY category VARCHAR(100) NOT NULL DEFAULT \'\'');
        }
    }
};
