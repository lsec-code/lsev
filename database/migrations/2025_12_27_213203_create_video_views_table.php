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
        Schema::create('video_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('video_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('ip_address', 45); // Support IPv6
            $table->text('user_agent');
            $table->string('fingerprint', 64)->index(); // Hash of IP + UA
            $table->integer('watch_duration')->default(0); // Seconds watched
            $table->boolean('completed')->default(false); // Watched to end
            $table->timestamp('viewed_at');
            $table->timestamps();

            // Composite index for unique view checking
            $table->index(['video_id', 'fingerprint', 'viewed_at']);
            $table->index(['video_id', 'viewed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_views');
    }
};
