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
            $table->unsignedBigInteger('draft_last_editor_id')->nullable()->after('draft_content');
            $table->timestamp('draft_updated_at')->nullable()->after('draft_last_editor_id');
            
            $table->foreign('draft_last_editor_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['draft_last_editor_id']);
            $table->dropColumn(['draft_last_editor_id', 'draft_updated_at']);
        });
    }
};
