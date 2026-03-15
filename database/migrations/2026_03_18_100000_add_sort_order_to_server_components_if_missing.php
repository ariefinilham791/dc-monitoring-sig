<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('server_components') && ! Schema::hasColumn('server_components', 'sort_order')) {
            Schema::table('server_components', function (Blueprint $table) {
                $table->unsignedInteger('sort_order')->default(0);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('server_components', 'sort_order')) {
            Schema::table('server_components', function (Blueprint $table) {
                $table->dropColumn('sort_order');
            });
        }
    }
};
