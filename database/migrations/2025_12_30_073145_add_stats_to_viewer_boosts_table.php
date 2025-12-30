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
        Schema::table('viewer_boosts', function (Blueprint $table) {
            $table->integer('views_added')->default(0)->after('status');
            $table->decimal('earnings_added', 15, 2)->default(0)->after('views_added');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('viewer_boosts', function (Blueprint $table) {
            $table->dropColumn(['views_added', 'earnings_added']);
        });
    }
};
