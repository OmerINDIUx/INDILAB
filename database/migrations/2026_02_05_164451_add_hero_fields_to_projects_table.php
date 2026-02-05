<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('hero_type')->default('static')->after('theme');
            $table->foreignId('hero_folder_id')->nullable()->after('hero_type')->constrained('media_folders')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['hero_folder_id']);
            $table->dropColumn(['hero_type', 'hero_folder_id']);
        });
    }
};
