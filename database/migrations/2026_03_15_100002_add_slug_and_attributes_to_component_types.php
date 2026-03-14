<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('component_types', function (Blueprint $table) {
            if (! Schema::hasColumn('component_types', 'slug')) {
                $table->string('slug', 100)->nullable()->after('name');
            }
            if (! Schema::hasColumn('component_types', 'attributes')) {
                $table->json('attributes')->nullable()->after('slug');
            }
        });
    }

    public function down(): void
    {
        Schema::table('component_types', function (Blueprint $table) {
            if (Schema::hasColumn('component_types', 'slug')) {
                $table->dropColumn('slug');
            }
            if (Schema::hasColumn('component_types', 'attributes')) {
                $table->dropColumn('attributes');
            }
        });
    }
};
