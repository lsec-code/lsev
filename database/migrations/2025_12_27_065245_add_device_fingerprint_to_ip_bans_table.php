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
        Schema::table('ip_bans', function (Blueprint $table) {
            $table->string('device_fingerprint', 64)->nullable()->after('ipv6_address');
            $table->index('device_fingerprint');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ip_bans', function (Blueprint $table) {
            $table->dropColumn('device_fingerprint');
        });
    }
};
