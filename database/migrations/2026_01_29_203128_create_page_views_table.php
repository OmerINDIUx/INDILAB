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
        Schema::create('page_views', function (Blueprint $table) {
            $table->id();
            $table->string('url');
            $table->string('page_title')->nullable();
            $table->string('referrer')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('ip_address', 45);
            $table->string('session_id');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('country', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->enum('device_type', ['desktop', 'mobile', 'tablet'])->default('desktop');
            $table->string('browser', 50)->nullable();
            $table->integer('time_on_page')->nullable(); // seconds
            $table->integer('scroll_depth')->nullable(); // percentage
            $table->timestamps();
            
            $table->index(['url', 'created_at']);
            $table->index('session_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_views');
    }
};
