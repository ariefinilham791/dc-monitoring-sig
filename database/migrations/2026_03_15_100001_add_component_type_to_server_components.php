<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('server_components', function (Blueprint $table) {
            if (! Schema::hasColumn('server_components', 'component_type_id')) {
                $table->unsignedBigInteger('component_type_id')->nullable()->after('server_id')->index();
            }
            if (! Schema::hasColumn('server_components', 'label')) {
                $table->string('label', 100)->nullable()->after('component_type_id');
            }
            if (! Schema::hasColumn('server_components', 'values')) {
                $table->json('values')->nullable()->after('label')->comment('Dynamic attribute values keyed by attribute slug');
            }
        });
    }

    public function down(): void
    {
        Schema::table('server_components', function (Blueprint $table) {
            if (Schema::hasColumn('server_components', 'component_type_id')) {
                $table->dropColumn('component_type_id');
            }
            if (Schema::hasColumn('server_components', 'label')) {
                $table->dropColumn('label');
            }
            if (Schema::hasColumn('server_components', 'values')) {
                $table->dropColumn('values');
            }
        });
    }
};
