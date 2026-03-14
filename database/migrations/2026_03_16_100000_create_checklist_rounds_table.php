<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('checklist_rounds')) {
            Schema::create('checklist_rounds', function (Blueprint $table) {
                $table->id();
                $table->unsignedSmallInteger('year');
                $table->unsignedTinyInteger('month');
                $table->string('name', 50);
                $table->timestamps();
            });
            Schema::table('checklist_rounds', function (Blueprint $table) {
                $table->unique(['year', 'month']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('checklist_rounds');
    }
};
