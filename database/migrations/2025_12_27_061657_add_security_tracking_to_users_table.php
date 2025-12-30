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
        Schema::table('users', function (Blueprint $table) {
            $table->integer('login_logout_count')->default(0)->after('security_answer');
            $table->timestamp('last_login_logout_reset')->nullable()->after('login_logout_count');
            $table->integer('security_verification_attempts')->default(0)->after('last_login_logout_reset');
            $table->timestamp('security_cooldown_until')->nullable()->after('security_verification_attempts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'login_logout_count',
                'last_login_logout_reset',
                'security_verification_attempts',
                'security_cooldown_until'
            ]);
        });
    }
};
